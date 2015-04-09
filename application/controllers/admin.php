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
        $this->Users();
    }

    //Users
    public function Users()
    {
        $this->aData['sTitle'] = 'Потребители';
        $this->aData['aUsers'] = $this->users->GetAllUsers();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/users',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

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

            if($bResult && $bEditUser){
                $bResult = true;
            }
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

    //Storages
    public function Storages()
    {
        $this->aData['sTitle'] = 'Хранилища';
        $this->aData['aStorages'] = $this->storages->GetAllStorages();

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

    //Products
    public function Products()
    {
        $this->aData['sTitle'] = 'Изделия';
        $this->aData['aProductTypes'] = $this->products->GetAllProductTypes();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/products',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function GetProductTypesByCategoryId()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['iCategoryId'])){
            $iCategoryId = (int) $_POST['iCategoryId'];
            $aResponse = array();
            $aSelectedProductTypes = array();

            $aProductTypes = $this->products->GetAllProductTypes();
            foreach($aProductTypes as $oProductType){
                if($iCategoryId == (int) $oProductType->Category){
                    $aSelectedProductTypes[] = $oProductType;
                }
            }

            if(is_array($aSelectedProductTypes) && !empty($aSelectedProductTypes)){
                $aResponse['success'] = true;
                $aResponse['aTypes'] = $aSelectedProductTypes;
            } else {
                $aResponse = array(
                    'success' => false,
                    'message' => 'Няма изделия от тази категория'
                );
            }

            echo json_encode($aResponse);
            return;
        }
    }

    public function AddProductType()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aProductData = $_POST;
            $bResult = $this->products->AddProductType($aProductData);

            echo json_encode(array('success' => $bResult));
        }
    }
}
