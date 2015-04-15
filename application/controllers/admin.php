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
        $this->AdminOptions();
    }

    public function AdminOptions()
    {
        $aStoragesData = $this->storages->ListStorages(1, $this->storages->getLimit());
        $aUsersData = $this->users->ListUsers(1, $this->users->getLimit());
        $aProductsData = $this->products->ListProducts(1, $this->products->getLimit());

        $this->aData['sTitle'] = 'Администрация';
        $this->aData['aUsers'] = $aUsersData['aUsers'];
        $this->aData['sUsersPagination'] = $aUsersData['sPagination'];
        $this->aData['aStorages'] = $aStoragesData['aStorages'];
        $this->aData['sStoragesPagination'] = $aStoragesData['sPagination'];
        $this->aData['aProducts'] = $aProductsData['aProducts'];
        $this->aData['sProductsPagination'] = $aProductsData['sPagination'];
        $this->aData['aProductTypes'] = $this->products->GetAllProductTypes();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/main_page',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    //Users
    public function EditUser()
    {
        $bResult = false;
        $bEditUser = false;

        if(is_array($_POST) && !empty($_POST)){
            $iUserId = $_POST['UserId'];

            //check if distributor exists
            $oDistributor = $this->users->GetDistributor($iUserId);
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
                );

                $bResult = $this->users->AddDistributor($aDistributorData);
            } elseif ($_POST['Type'] == 1 && $bIsDistributor) {
                $aStorageData['Active'] = '1';

                $bResult = $this->storages->UpdateStorage($oDistributor->StorageId,$aStorageData);
            } elseif ($_POST['Type'] != 1 && $bIsDistributor) {
                $bResult = $this->storages->DeleteStorage($oDistributor->StorageId);
            }

            $bEditUser = $this->users->EditUser($_POST);
        }

        if($bResult && $bEditUser){
            $bResult = true;
        } else {
            $bResult = false;
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
            echo json_encode(array('success' => false, 'message' => 'Възникна грешка! Опитайте отново.'));
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

    public function StoragesPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);

            $aStoragesData = $this->storages->ListStorages($iPageId, $this->storages->getLimit());

            $aStorages = $aStoragesData['aStorages'];
            $sPagination = $aStoragesData['sPagination'];

            echo json_encode(array('success' => true, 'aStorages' => $aStorages, 'sPagination' => $sPagination));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    //Products
    public function ProductsPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);

            $aProductsData = $this->products->ListProducts($iPageId, $this->products->getLimit());

            $aProducts = $aProductsData['aProducts'];
            $sPagination = $aProductsData['sPagination'];

            echo json_encode(array('success' => true, 'aProducts' => $aProducts, 'sPagination' => $sPagination));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    //Product types
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
}
