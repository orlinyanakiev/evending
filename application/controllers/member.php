<?php

require_once (APPPATH . 'core/My_MemberController.php');

class Member extends My_MemberController
{
    public function __construct()
    {
        parent::__construct();
    }

    private function CheckUserPermissions()
    {
//        $iPermissions = $this->aData['oUser']->Type;
//        if($iPermissions < 1){
//            redirect(base_url().'member/');
//        } elseif ($iPermissions > 1){
//            redirect(base_url().'admin/');
//        }
    }
    
    public function index()
    {
        $this->Homepage();
    }

    public function Homepage()
    {
        $this->CheckUserPermissions();
        $aStoragesData = $this->storages->ListStorages();
        $aStorages = $aStoragesData['aStorages'];
        $aAvailableProducts = array();
        foreach ($aStorages as $oStorage){
            $aStorageAvailability = json_decode($oStorage->Availability,true);
            if(is_array($aStorageAvailability) && !empty($aStorageAvailability)){
                foreach ($aStorageAvailability as $iProductId => $iQuantity){
                    if(!in_array($iProductId,$aAvailableProducts)){
                        $aAvailableProducts[] = $iProductId;
                    }
                }
            }
        }
        $aExpiringProducts = $this->products->GetProductByExpirationDate();

        foreach ($aExpiringProducts as $iKey => $oExpiringProduct){
            if(!in_array($oExpiringProduct->Id,$aAvailableProducts)){
                $this->products->DeleteProduct($oExpiringProduct->Id);
                unset($aExpiringProducts[$iKey]);
            }
        }

        $this->aData['aExpiringProducts'] = $aExpiringProducts;
        $this->aData['sTitle'] = 'Начало';

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/homepage',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function Actions()
    {
        $this->CheckUserPermissions();
        $this->aData['sTitle'] = 'Действия';

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/actions',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function Obsolete()
    {
        $this->CheckUserPermissions();
        $aStoragesData = $this->storages->ListStorages(1, 0, 3);

        $this->aData['sTitle'] = 'Операции/Бракуване';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        if(is_object($this->aData['oDistributor'])){
            $this->aData['aStorages'] = $this->storages->GetDistributorVendingMachines(intval($this->aData['oDistributor']->Id));
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/obsolete',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function ObsoleteProduct()
    {
        $bObsolete = false;
        if(is_array($_POST) && !empty($_POST)){
            $aData = $_POST;

            $bObsolete = $this->storages->Obsolete($aData);

            if($bObsolete){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::OBSOLETE, $_POST);
            }
        }
        echo json_encode(array('success' => $bObsolete));
        return;
    }

    public function Distribution()
    {
        $this->CheckUserPermissions();
        $aAdditionalStorages = $this->storages->ListStorages(1, 0, 2);
        $aStoragesData = $this->storages->ListStorages(1, 0, 1);
        $aStorageAvailability = $this->GetStorageAvailability($aStoragesData['aStorages'][0]->Id);

        $this->aData['sTitle'] = 'Операции/Дистрибуция';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['aProducts'] = $aStorageAvailability['aStorageAvailability'];
        $this->aData['aAdditionalStorages'] = $aAdditionalStorages['aStorages'];
        if(is_object($this->aData['oDistributor'])){
            $aStorageAvailability = $this->GetStorageAvailability($this->aData['oDistributor']->StorageId);
            $aStorages = json_decode($this->aData['oDistributor']->Storages, true);
            if(is_array($aStorages) && !empty($aStorages))
            foreach($aStorages as $iKey => $iStorageId){
                $aStorages[$iKey] = $this->storages->GetStorageById($iStorageId);
            }

            $this->aData['aAdditionalStorages'] = $aStorages;
            $this->aData['aStorages'] = array($this->storages->GetStorageById($this->aData['oDistributor']->StorageId));
            $this->aData['aProducts'] = $aStorageAvailability['aStorageAvailability'];
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/distribution',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function GetRemainingStorages()
    {
        $this->CheckUserPermissions();
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

    public function AjaxGetDistributorStorages($iDistributorAsStorageId = 0)
    {
        $aDistributorStorages = $this->GetDistributorStorages($iDistributorAsStorageId);
        echo json_encode(array('success' => true, 'aDistributorStorages' => $aDistributorStorages));
    }

    public function GetDistributorStorages($iDistributorAsStorageId = 0)
    {
        $oDistributor = $this->users->GetDistributorByStorageId($iDistributorAsStorageId);
        $aDistributorStorages = json_decode($oDistributor->Storages,true);

        if(is_array($aDistributorStorages) && !empty($aDistributorStorages)){
            foreach($aDistributorStorages as $iKey => $iStorageId){
                $aDistributorStorages[$iKey] = $this->storages->GetStorageById($iStorageId);
            }
        } else {
            $aDistributorStorages = array();
        }

        return $aDistributorStorages;
    }

    public function Distribute()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aData = $_POST;

            $bDistribute = $this->storages->Distribute($aData);

            if($bDistribute){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::DISTRIBUTE, $_POST);
            }

            echo json_encode(array('success' => $bDistribute));
            return;
        }
    }

    //Storage supply
    public function Supply()
    {
        $this->CheckUserPermissions();
        $aStoragesData = $this->storages->ListStorages(1, 0, 1);
        $aProductTypesData = $this->products->ListProductTypes();

        $this->aData['sTitle'] = 'Операции/Зареждане';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
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
            echo json_encode(array('success' => true, 'aStorageAvailability' => $aStorageAvailability['aStorageAvailability'], 'fCash' => $aStorageAvailability['fCash'], 'fCurrentValue' => $aStorageAvailability['fCurrentValue']));
            return;
        }

        echo json_encode(array('success' => false));
    }

    public function GetStorageAvailability($iStorageId)
    {
        $iStorageId = intval($iStorageId);
        $aStorageAvailability = array();

        $oStorage = $this->storages->GetStorageById($iStorageId);

        if(is_object($oStorage)){
            $aAvailability = json_decode($oStorage->Availability, true);
            $fCash = $fCurrentValue = $oStorage->Cash;

            if(is_array($aAvailability) && !empty($aAvailability)){
                foreach ($aAvailability as $iProductId => $iQuantity){
                    $oProduct = $this->products->GetProductById((int)$iProductId);
                    if($oProduct->IsDeleted == '1'){
                        unset($aAvailability[$iProductId]);
                    } else {
                        $fValue = '0.00';
                        $fPrice = '0.00';
                        $fValue += $oProduct->Value * $iQuantity;
                        $fPrice += $oProduct->Price * $iQuantity;
                        $aStorageAvailability[]= array('oProduct' => $oProduct, 'iQuantity' => $iQuantity, 'fValue' => $fValue, 'fPrice' => $fPrice);
                        $fCurrentValue += $fPrice;
                    }
                }
            }
            if(!empty($aStorageAvailability)){
                return array('aStorageAvailability' => $aStorageAvailability, 'fCash' => floatval($fCash), 'fCurrentValue' => floatval($fCurrentValue));
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

            if($bResult){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::SUPPLY, $_POST);
            }

            echo json_encode(array('success' => $bResult));
        }
    }

    //Income
    public function Revenue()
    {
        $this->CheckUserPermissions();
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $aStoragesData = $this->storages->ListStorages(1, 0, 3);

        $this->aData['sTitle'] = 'Приходи';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['sStoragesPagination'] = $aStoragesData['sPagination'];

        if(is_object($this->aData['oDistributor'])){
            $this->aData['aStorages'] = $this->storages->GetDistributorVendingMachines(intval($this->aData['oDistributor']->Id));
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/revenue',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function RevenueAccounting()
    {
        if(is_array($_POST) && !empty($_POST)){
            $iStorageId = intval($_POST['Storage']);
            $fRevenue = $_POST['Value'];

            $bIncome = $this->storages->AccountIncome($iStorageId, $fRevenue);

            $oStorage = $this->storages->GetStorageById($iStorageId);

            $aUpdateData['Cash'] = $oStorage->Cash - $fRevenue;
            $bRevenueAccounting = $this->storages->EditStorage($iStorageId,$aUpdateData);

            if($bIncome && $bRevenueAccounting){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::INCOME_ACCOUNTING, $_POST);

                echo json_encode(array('success' => $bRevenueAccounting));
                return;
            }
        }

        echo json_encode(array('success' => false));
    }

    //Sales
    public function Sales()
    {
        $this->CheckUserPermissions();

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
                $this->events->RegisterEvent($this->aData['oUser'], \Events::SALE, $_POST);
            }
        }
        echo json_encode(array('success' => $bSale));
        return;
    }

    public function Vending()
    {
        $this->CheckUserPermissions();

        $aDistributorsAsStoragesData = $this->storages->ListStorages(1, 0, 2);

        $this->aData['sTitle'] = 'Продажба';
        $this->aData['aDistributorsAsStorages'] = $aDistributorsAsStoragesData['aStorages'];

        if(is_object($this->aData['oDistributor'])) {
            $aDistributorAvailabilityData = $this->GetStorageAvailability($this->aData['oDistributor']->StorageId);
            $this->aData['aVendingMachines'] = $this->storages->GetDistributorVendingMachines(intval($this->aData['oDistributor']->Id));
            $this->aData['aDistributorAvailability'] = $aDistributorAvailabilityData['aStorageAvailability'];
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/vending',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}