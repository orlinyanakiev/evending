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
        $aStoragesData = $this->storages->ListStorages();
        $aProductsData = $this->products->ListProducts();

        $this->aData['aExpiringProducts'] = $this->products->GetProductByExpirationDate();
        $this->aData['sTitle'] = 'Дистрибуция';
        $this->aData['aProducts'] = $aProductsData['aProducts'];
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        if(is_object($this->aData['oDistributor'])){
            $aDistributorStorages = $this->GetRemainingDistributorStorages();
            $this->aData['aStorages'] = $aDistributorStorages;
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/distribution',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function GetRemainingStorages()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['iSelectedStorageId'])){
            $aStoragesData = $this->storages->ListStorages();
            $aAllStorages = $aStoragesData['aStorages'];
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

    public function AjaxGetRemainingDistributorStorages($iTakenStorageId = 0)
    {
        $aDistributorStorages = $this->GetRemainingDistributorStorages($iTakenStorageId);
        echo json_encode(array('success' => true, 'aRemainingStorages' => $aDistributorStorages));
    }

    public function GetRemainingDistributorStorages($iTakenStorageId = 0)
    {
        $aDistributorStorages = $this->storages->GetDistributorVendingMachines($this->aData['oDistributor']->Id);
        $iDistributorStorageId = intval($this->aData['oDistributor']->StorageId);
        $aDistributorStorages[] = $this->storages->GetStorageById($iDistributorStorageId);

        foreach($aDistributorStorages as $iKey => $oStorage){
            if($oStorage->Id == $iTakenStorageId){
                unset($aDistributorStorages[$iKey]);
            }
        }

        return $aDistributorStorages;
    }

    public function Distribute()
    {
        $bDistribute = false;
        if(is_array($_POST) && !empty($_POST)){
            $aData = $_POST;

            $bDistribute = $this->storages->Distribute($aData);
            if($bDistribute){
                echo json_encode(array('success' => $bDistribute));
                return;
            }
        }
        echo json_encode(array('success' => $bDistribute));
        return;
    }

    //Storage supply
    public function Supply()
    {
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $aStoragesData = $this->storages->ListStorages();
        $aProductTypesData = $this->products->ListProductTypes();

        $this->aData['sTitle'] = 'Зареждане';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['sStoragesPagination'] = $aStoragesData['sPagination'];
        $this->aData['aProductTypes'] = $aProductTypesData['aProductTypes'];

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/supply',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function GetProductTypesByCategoryId()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['iCategoryId'])){
            $iCategoryId = (int) $_POST['iCategoryId'];
            $aResponse = array();
            $aSelectedProductTypes = array();
            $aProductTypesData = $this->products->ListProductTypes();

            $aProductTypes = $aProductTypesData['aProductTypes'];
            foreach($aProductTypes as $oProductType){
                if($iCategoryId == (int) $oProductType->Category){
                    $aSelectedProductTypes[] = $oProductType;
                }
            }

            if(is_array($aSelectedProductTypes) && !empty($aSelectedProductTypes)){
                $aResponse['success'] = true;
                $aResponse['aTypes'] = $aSelectedProductTypes;
            } else {
                $aResponse = array('success' => false);
            }

            echo json_encode($aResponse);
            return;
        }
    }

    public function AjaxGetStorageAvailability($iStorageId)
    {
        $aStorageAvailability = $this->GetStorageAvailability($iStorageId);
        if(is_array($aStorageAvailability) && !empty($aStorageAvailability)){
            echo json_encode(array('success' => true, 'aStorageAvailability' => $aStorageAvailability));
            return;
        }

        echo json_encode(array('success' => false));
    }

    public function GetStorageAvailability($iStorageId)
    {
        $aStoragesData = $this->storages->ListStorages();
        $aStorages = $aStoragesData['aStorages'];
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
                $aStorageAvailability[]= array('oProduct' => $oProduct, 'iQuantity' => $iQuantity);
            }
        }

        return $aStorageAvailability;
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

    //Sales
    public function Sales()
    {
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $aStoragesData = $this->storages->ListStorages(1, 0, 3);

        $this->aData['sTitle'] = 'Продажба';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['sStoragesPagination'] = $aStoragesData['sPagination'];

        if(is_object($this->aData['oDistributor'])){
            $this->aData['aStorages'] = $this->storages->GetDistributorVendingMachines(intval($this->aData['oDistributor']->Id));
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/sales',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function Sale()
    {
        $bSale = false;
        if(is_array($_POST) && !empty($_POST)){
            $aData = $_POST;

            $bSale = $this->storages->Sale($aData);
            if($bSale){
                echo json_encode(array('success' => $bSale));
                return;
            }
        }
        echo json_encode(array('success' => $bSale));
        return;
    }

    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}