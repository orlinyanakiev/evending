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
        $this->Homepage();
    }

    public function Homepage()
    {
        $this->aData['aExpiringProducts'] = $this->products->GetProductByExpirationDate();
        $this->aData['sTitle'] = 'Начало';

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/homepage',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function Actions()
    {
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $this->aData['sTitle'] = 'Действия';

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/actions',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function Obsolete()
    {
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $aStoragesData = $this->storages->ListStorages();
        $aProductsData = $this->products->ListProducts();

        $this->aData['sTitle'] = 'Операции/Бракуване';
        $this->aData['aProducts'] = $aProductsData['aProducts'];
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        if(is_object($this->aData['oDistributor'])){
            $aDistributorStorages = $this->GetRemainingDistributorStorages();
            $this->aData['aStorages'] = $aDistributorStorages;
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/obsolete',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function Distribution()
    {
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $aStoragesData = $this->storages->ListStorages();

        $this->aData['sTitle'] = 'Операции/Дистрибуция';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/distribution',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function GetDistributorStorages()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['iSelectedStorageId'])){
            $aStoragesData = $this->storages->ListStorages(1, 0, 2);
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
        if(is_array($_POST) && !empty($_POST)){
            $aData = $_POST;

            $bDistribute = $this->storages->Distribute($aData);

            if($bDistribute){
//                $this->events->RegisterEvent($this->aData['oUser'], \Events::DISTRIBUTE, $_POST);
            }

            echo json_encode(array('success' => $bDistribute));
            return;
        }
    }

    //Storage supply
    public function Supply()
    {
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $aStoragesData = $this->storages->ListStorages(1,0,1);
        $aProductTypesData = $this->products->ListProductTypes();

        $this->aData['sTitle'] = 'Операции/Зареждане';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['aProductTypes'] = $aProductTypesData['aProductTypes'];
        if(is_object($this->aData['oDistributor'])){
            $aDistributorStorages = $this->GetRemainingDistributorStorages();
            $this->aData['aStorages'] = $aDistributorStorages;
        }

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
                        $fValue = $oProduct->Value * $iQuantity;
                        $fPrice = $oProduct->Price * $iQuantity;
                        $aStorageAvailability[]= array('oProduct' => $oProduct, 'iQuantity' => $iQuantity, 'fValue' => $fValue, 'fPrice' => $fPrice);
                        $fCurrentValue += $fPrice;
                    }
                }
            }
            if(!empty($aStorageAvailability)){
                return array('aStorageAvailability' => $aStorageAvailability, 'fCash' => $fCash, 'fCurrentValue' => $fCurrentValue);
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
//                $this->events->RegisterEvent($this->aData['oUser'], \Events::SUPPLY, $_POST);
            }

            echo json_encode(array('success' => $bResult));
        }
    }

    //Vending machines
    public function Vending()
    {
        if($this->aData['oUser']->Type == 0){
            redirect(base_url().'member');
        }

        $aDistributorsData = $this->users->GetAllDistributors();
        $aStoragesData = $this->storages->ListStorages(1, 0, 3);

        $this->aData['sTitle'] = 'Вендинг';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['aDistributors'] = $aDistributorsData;

        if(is_object($this->aData['oDistributor'])){
            $this->aData['aStorages'] = $this->storages->GetDistributorVendingMachines(intval($this->aData['oDistributor']->Id));
        }

        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/vending',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }

    public function VendingSubmit()
    {
        if(is_array($_POST) && !empty($_POST)){
            $iDistributeProductId = intval($_POST['DistributeProduct']);
            $iObsoleteProductId = intval($_POST['ObsoleteProduct']);
            $sVendingMachineCash = $_POST['VendingMachineCash'];
            $iDistributorId = intval($_POST['Distributor']);
            $oDistributor = $this->users->GetDistributorById($iDistributorId);

            if($iDistributeProductId != 0 && $_POST['DistributeQuantity'] != ''){
                $aDistributionData = array(
                    'Storage1' => $oDistributor->StorageId,
                    'Storage2' => $_POST['VendingMachine'],
                    'Product' => $iDistributeProductId,
                    'Quantity' => $_POST['DistributeQuantity']
                );

                $bDistribute = $this->storages->Distribute($aDistributionData);

                if($bDistribute){
//                    $this->events->RegisterEvent($this->aData['oUser'], \Events::DISTRIBUTE, $aDistributionData);
                }

            }

            if($iObsoleteProductId != 0 && $_POST['ObsoleteQuantity'] != ''){
                $aData = array(
                    'Storage' => $_POST['VendingMachine'],
                    'Product' => $_POST['ObsoleteProduct'],
                    'Quantity' => $_POST['ObsoleteQuantity']
                );

                $bSale = $this->products->Obsolete($aData);

                if($bSale){
//                    $this->events->RegisterEvent($this->aData['oUser'], \Events::OBSOLETE, $aData);
                }
            }

            if($sVendingMachineCash != ''){
                $iStorageId = intval($_POST['VendingMachine']);
                $fRevenue = $sVendingMachineCash;
                $aRevenueData = array(
                    'Storage' => $iStorageId,
                    'Value' => $fRevenue
                );

                $bIncome = $this->storages->AccountIncome($iStorageId, $fRevenue);

                $oStorage = $this->storages->GetStorageById($iStorageId);

                $aUpdateData['Cash'] = $oStorage->Cash - $fRevenue;
                $bRevenueAccounting = $this->storages->EditStorage($iStorageId,$aUpdateData);

                if($bIncome && $bRevenueAccounting){
//                    $this->events->RegisterEvent($this->aData['oUser'], \Events::INCOME_ACCOUNTING, $aRevenueData);
                }

            }
            echo json_encode(array('success' => true));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    public function GetDistributorDataById($iDistributorId)
    {
        $oDistributor = $this->users->GetDistributorById($iDistributorId);

        if(is_object($oDistributor)){
            $aDistributorStorages = json_decode($oDistributor->Storages, true);
            if(is_array($aDistributorStorages) && !empty($aDistributorStorages)){
                $aDistributorAvailability = $this->GetStorageAvailability($oDistributor->StorageId);

                foreach($aDistributorStorages as $iKey => $iStorageId){
                    $aDistributorStorages[$iKey] = $this->storages->GetStorageById($iStorageId);
                }

                echo json_encode(array(
                    'success' => true,
                    'aDistributorAvailability' => $aDistributorAvailability['aStorageAvailability'],
                    'aDistributorStorages' => $aDistributorStorages
                ));
                return;
            }
        }

        echo json_encode(array('success' => false));
        return;
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
//                $this->events->RegisterEvent($this->aData['oUser'], \Events::INCOME_ACCOUNTING, $_POST);

                echo json_encode(array('success' => $bRevenueAccounting));
                return;
            }
        }

        echo json_encode(array('success' => false));
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
//                $this->events->RegisterEvent($this->aData['oUser'], \Events::SALE, $_POST);
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