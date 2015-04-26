<?php

require_once (APPPATH . 'core/My_AdminController.php');

class admin extends My_AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //check user type
        $this->Manage();
    }

    public function Manage()
    {
        $this->aData['sTitle'] = 'Администрация';

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/manage',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    //Users
    public function Users()
    {
        $aStoragesData = $this->storages->ListStorages(1, $this->storages->getStorageLimit());
        $aVendingMachinesData = $this->storages->ListStorages( 1 , 0 , '3');
        $aUsersData = $this->users->ListUsers(1, $this->users->getLimit());

        $this->aData['sTitle'] = 'Потребители';
        $this->aData['aUsers'] = $aUsersData['aUsers'];
        $this->aData['aVendingMachines'] = $aVendingMachinesData['aStorages'];
        $this->aData['sUsersPagination'] = $aUsersData['sPagination'];
        $this->aData['aStorages'] = $aStoragesData['aStorages'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/users',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function EditUser()
    {
        $bResult = false;
        $bEditUser = false;
        $bIsDistributor = false;

        if(is_array($_POST) && !empty($_POST)){
            $iUserId = $_POST['UserId'];

            //check if distributor exists
            $oDistributor = $this->users->GetDistributorInfo($iUserId);
            $bIsDistributor = is_object($oDistributor);

            if($_POST['Type'] == 1 && !$bIsDistributor) {
                $aStorageData = array(
                    'Name' => $_POST['FirstName'].' '.$_POST['LastName'],
                    'Address' => '',
                    'Type' => '2',
                );

                $iStorageId = $this->storages->AddStorage($aStorageData);

                $aDistributorData = array(
                    'Id' => $_POST['UserId'],
                    'StorageId' => $iStorageId,
                    'Storages' => array_key_exists('vending_machine',$_POST) ? json_encode($_POST['vending_machine']) : '',
                );

                $bResult = $this->users->AddDistributor($aDistributorData);
            } elseif ($_POST['Type'] == 1 && $bIsDistributor) {
                $aStorageData = array(
                    'Active' => '1',
                );

                $aDistributorData = array('Storages' => array_key_exists('vending_machine',$_POST) ? json_encode($_POST['vending_machine']) : '');

                $this->users->EditDistributor($_POST['UserId'],$aDistributorData);
                $bResult = $this->storages->EditStorage($oDistributor->StorageId,$aStorageData);
            } elseif ($_POST['Type'] != 1 && $bIsDistributor) {
                $bResult = $this->storages->DeleteStorage($oDistributor->StorageId);
            }

            $bEditUser = $this->users->EditUser($_POST);
        }
        if($bIsDistributor){
            if($bResult && $bEditUser){
                $bResult = true;
            } else {
                $bResult = false;
            }
        } else {
            $bResult = $bEditUser;
        }

        echo json_encode(array('success' => $bResult));
    }

    public function DeleteUser()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['iUserId'])){
            $iUserId = (int) $_POST['iUserId'];

            $bResult = $this->users->deleteUserById($iUserId);
            echo json_encode(array('success' => $bResult));
        }
    }

    public function GetUser()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['iUserId'])){
            $iUserId = (int) $_POST['iUserId'];

            $oUser = $this->users->GetUser($iUserId);
            if(is_object($oUser)){
                echo json_encode(array('success' => true, 'oUser' => $oUser, 'aUserTypes' => $this->aData['aUserTypes']));
                return;
            }
            echo json_encode(array('success' => false));
            return;
        }
    }

    public function UsersPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);

            $aUsersData = $this->users->ListUsers($iPageId, $this->users->getLimit());

            $aUsers = $aUsersData['aUsers'];
            $sPagination = $aUsersData['sPagination'];

            echo json_encode(array('success' => true, 'oUser' => $this->aData['oUser'], 'aUsers' => $aUsers, 'sPagination' => $sPagination));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    //Storages
    public function Storages()
    {
        $aStoragesData = $this->storages->ListStorages(1, $this->storages->getStorageLimit());

        $this->aData['sTitle'] = 'Складове';
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['sStoragesPagination'] = $aStoragesData['sPagination'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/storages',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function AddStorage()
    {
        $bResult = false;
        if(is_array($_POST) && !empty($_POST)){
            $aStorageData = $_POST;
            $iStorageId = $this->storages->AddStorage($aStorageData);

            if(is_int($iStorageId)){
                $bResult = true;
            }
            echo json_encode(array('success' => $bResult));
        }
    }

    public function EditStorage()
    {
        if(is_array($_POST) && !empty($_POST) && array_key_exists('Id', $_POST)){
            $iStorageId = intval($_POST['Id']);
            $bEditStorage = $this->storages->EditStorage($iStorageId, $_POST);

            echo json_encode(array('success' => $bEditStorage));
        }
    }

    public function DeleteStorage($iStorageId)
    {
        $bDeleteStorage = $this->storages->DeleteStorage($iStorageId);
        echo json_encode(array('success' => $bDeleteStorage));
    }

    public function GetStorageById($iStorageId)
    {
        $oStorage = $this->storages->GetStorageById($iStorageId);
        if(is_object($oStorage)){
            echo json_encode(array('success' => true, 'oStorage' => $oStorage));
            return;
        }

        echo json_encode(array('success' => false));
    }

    public function GetDistributorVendingMachines($iUserId)
    {
        $iUserId = intval($iUserId);
        if($iUserId != 0){
            $aDistributorStorages = $this->storages->GetDistributorVendingMachines($iUserId);
            if(is_array($aDistributorStorages)){

                echo json_encode(array('success' => true, 'aDistributorStorages' => $aDistributorStorages));
                return;
            } else {
                echo json_encode(array('success' => false));
                return;
            }
        } else {
            $aVendingMachinesData = $this->storages->ListStorages( 1 , 0 , '3');
            $aVendingMachines = $aVendingMachinesData['aStorages'];

            echo json_encode(array('success' => true, 'aDistributorStorages' => $aVendingMachines));
            return;
        }
    }

    public function StoragesPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);
            $iType = 0;

            if(array_key_exists('iType',$_POST)){
                $iType = intval($_POST['iType']);
            }

            $aStoragesData = $this->storages->ListStorages($iPageId, $this->storages->getStorageLimit(), $iType);

            echo json_encode(array('success' => true, 'aStorages' => $aStoragesData['aStorages'], 'sPagination' => $aStoragesData['sPagination']));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    //Products
    public function Products()
    {
        $aProductsData = $this->products->ListProducts(1, $this->products->getLimit());

        $this->aData['sTitle'] = 'Изделия';
        $this->aData['aProducts'] = $aProductsData['aProducts'];
        $this->aData['sProductsPagination'] = $aProductsData['sPagination'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/products',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function ProductsPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);

            $aProductsData = $this->products->ListProducts($iPageId, $this->products->getLimit());

            $aProductsData['success'] = true;

            echo json_encode($aProductsData);
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    public function GetProductById()
    {
        if(is_array($_POST) && !empty($_POST) && array_key_exists('iProductId',$_POST)){
            $iProductId = intval($_POST['iProductId']);
            $oProduct = $this->products->GetProductById($iProductId);

            echo json_encode(array('success' => true, 'oProduct' => $oProduct));
            return;
        }

        echo json_encode(array('success' => false));
    }

    public function EditProduct()
    {
        if(is_array($_POST) && !empty($_POST)){
            $iProductId = intval($_POST['Id']);
            $bEditProduct = $this->products->EditProduct($iProductId, $_POST);

            echo json_encode(array('success' => $bEditProduct));
        }
    }

    //Product types
    public function ProductTypes()
    {
        $aProductTypesData = $this->products->ListProductTypes(1, $this->products->getLimit());

        $this->aData['sTitle'] = 'Типове изделия';
        $this->aData['aProductTypes'] = $aProductTypesData['aProductTypes'];
        $this->aData['sProductTypesPagination'] = $aProductTypesData['sPagination'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/producttypes',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function GetProductTypeById()
    {
        if(is_array($_POST) && !empty($_POST) && array_key_exists('iProductTypeId',$_POST)){
            $iPTId = intval($_POST['iProductTypeId']);
            $oProductType = $this->products->GetProductTypeById($iPTId);
            $aCategories = $this->products->aProductCategories;

            echo json_encode(array('success' => true, 'oProductType' => $oProductType, 'aCategories' => $aCategories));
            return;
        }

        echo json_encode(array('success' => false));
    }

    public function AddProductType()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aProductData = $_POST;
            $bResult = $this->products->AddProductType($aProductData);

            echo json_encode(array('success' => $bResult));
        }
    }

    public function EditProductType()
    {
        $bResult = $this->products->EditProductType($_POST);
        echo json_encode(array('success' => $bResult));
    }

    public function DeleteProductType()
    {
        $iProductTypeId = $_POST['iProductTypeId'];

        $bResult = $this->products->DeleteProductType($iProductTypeId);
        echo json_encode(array('success' => $bResult));
    }

    public function ProductTypesPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);

            $aProductTypesData = $this->products->ListProductTypes($iPageId, $this->products->getTypesLimit());

            $aProductTypesData['success'] = true;

            echo json_encode($aProductTypesData);
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    //Obsolete
    public function Obsolete()
    {
        $this->aData['sTitle'] = 'Бракувани';

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/obsolete',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    //Sales
    public function Sales()
    {
        $aVendingMachinesData = $this->storages->ListStorages( 1 , 0 , '3');
        $aDistributors = $this->users->ListUsers(1,0,'1');

        $this->aData['sTitle'] = 'Продажби';
        $this->aData['aDistributors'] = $aDistributors['aUsers'];
        $this->aData['aVendingMachines'] = $aVendingMachinesData['aStorages'];
        $this->aData['aSales'] = $this->storages->GetSales();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/sales',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function GetSales()
    {
        $iUserId = intval($_POST['iUserId']);
        $iStorageId = intval($_POST['iStorageId']);
        $sPeriod = $_POST['sPeriod'];

        $aSalesData = $this->storages->GetSales($iUserId, $iStorageId, $sPeriod);
        echo json_encode(array('success' => true, 'aSalesData' => $aSalesData));
    }
}
