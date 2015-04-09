<?php

require_once (APPPATH . 'core/My_MemberController.php');

class Member extends My_MemberController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->Distribution();
    }

    public function Distribution()
    {
        $this->aData['sTitle'] = 'Дистрибуция';
        $this->aData['aProducts'] = $this->products->GetAllProducts();
        $this->aData['aStorages'] = $this->storages->GetAllStorages();
        if(is_object($this->aData['oDistributor'])){
            $this->aData['aStorageAvailability'] = $this->GetStorageAvailability($this->aData['oDistributor']->StorageId);
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/distribution',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function GetRemainingStorages()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['iSelectedStorageId'])){
            $aAllStorages = $this->storages->GetAllStorages();
            $aRemainingStorages = array();

            $iSelectedStorageId = (int) $_POST['iSelectedStorageId'];
            foreach($aAllStorages as $oStorage){
                if((int)$oStorage->Id != $iSelectedStorageId){
                    $aRemainingStorages[] = $oStorage;
                }
            }

            echo json_encode(array('success' => true, 'aRemainingStorages' => $aRemainingStorages));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    public function Distribute()
    {
        $bDistribute = false;
        if(is_array($_POST) && !empty($_POST)){
            $aData = $_POST;

            $bDistribute = $this->storages->Distribute($aData);
            if($bDistribute){
                echo json_encode(array('success' => $bDistribute, 'message' => 'Успешна операция!'));
                return;
            }
        }
        echo json_encode(array('success' => $bDistribute, 'message' => 'Некоректна информация!'));
        return;
    }

    //Storage supply
    public function Supply()
    {
        $this->aData['sTitle'] = 'Зареждане';
        $this->aData['aStorages'] = $this->storages->GetAllStorages();
        $this->aData['aProductTypes'] = $this->products->GetAllProductTypes();

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/supply',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function GetStorageAvailability($iStorageId)
    {
        $aStorages = $this->storages->GetAllStorages();
        $iStorageId = (int) $iStorageId;
        $aStorageAvailability = array();

        foreach ($aStorages as $oStorage){
            if((int)$oStorage->Id == $iStorageId){
                $aAvailability = json_decode($oStorage->Availability, true);
            }
        }

        if(is_array($aAvailability) && !empty($aAvailability)){
            foreach ($aAvailability as $iProductId => $iQuantity){
                $oProduct = $this->products->GetProductById((int)$iProductId);
                $oProductType = $this->products->GetProductTypeById((int)$oProduct->Type);
                $aStorageAvailability[]= array('oData' => $oProduct, 'oType' => $oProductType, 'iQuantity' => $iQuantity);
            }
        }

        return $aStorageAvailability;
    }

    public function AjaxGetStorageAvailability($iStorageId)
    {
        $aStorages = $this->storages->GetAllStorages();
        $iStorageId = (int) $iStorageId;
        $aStorageAvailability = array();

        foreach ($aStorages as $oStorage){
            if((int)$oStorage->Id == $iStorageId){
                $aAvailability = json_decode($oStorage->Availability, true);
            }
        }

        if(is_array($aAvailability) && !empty($aAvailability)){
            foreach ($aAvailability as $iProductId => $iQuantity){
                $oProduct = $this->products->GetProductById((int)$iProductId);
                $oProductType = $this->products->GetProductTypeById((int)$oProduct->Type);
                $aStorageAvailability[]= array('oData' => $oProduct, 'oType' => $oProductType, 'iQuantity' => $iQuantity);
            }
        } else {
            echo json_encode(array('success' => false, 'message' => 'Хранилището е празно!'));
            return;
        }

        if(is_array($aStorageAvailability) && !empty($aStorageAvailability)){
            echo json_encode(array('success' => true, 'aStorageAvailability' => $aStorageAvailability));
            return;
        }
        echo json_encode(array('success' => false, 'message' => 'Възникна грешка! Опитайте отново.'));
        return;
    }

    public function StorageSupply()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aStorageSupplyData = $_POST;
            $oProductType = $this->products->GetProductTypeById((int)$aStorageSupplyData['ProductType']);
            $bResult = $this->storages->StorageSupply($aStorageSupplyData, $oProductType);

            echo json_encode(array('success' => $bResult));
        }
    }

    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}