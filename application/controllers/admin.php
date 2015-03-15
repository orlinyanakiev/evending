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

    //Storages
    public function Storages()
    {
        $this->aData['sTitle'] = 'Складове';
        $this->aData['aStorages'] = $this->storages->GetAllStorages();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/storages',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function AddStorage()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aStorageData = $_POST;
            $bResult = $this->storages->AddStorage($aStorageData);

            echo json_encode(array('success' => $bResult));
        }
    }

    public function GetStorageAvailability()
    {
        if(is_array($_POST) && !empty($_POST) && isset($_POST['StorageId'])){
            $iStorageId = $_POST['StorageId'];
            $aStorageAvailability = $this->storages->GetStorageAvailability($iStorageId);

            if(is_array($aStorageAvailability) && !empty($aStorageAvailability)){
                echo json_encode(array('success' => true, 'aStorageAvailability' => $aStorageAvailability));
                return;
            }
            echo json_encode(array('success' => false));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    //Storage supply
    public function Supply()
    {
        $this->aData['sTitle'] = 'Зареждане';
        $this->aData['aStorages'] = $this->storages->GetAllStorages();
        $this->aData['aProductTypes'] = $this->products->GetAllProductTypes();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/supply',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function StorageSupply()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aStorageSupplyData = $_POST;
            $oProductType = $this->products->GetProductTypeById((int)$aStorageSupplyData['ProductType']);
            $bResult = $this->storages->StorageSupply($aStorageSupplyData, $oProductType[0]);

            echo json_encode(array('success' => $bResult));
        }
    }

    //Products
    public function Products()
    {
        $this->aData['sTitle'] = 'Изделия';
        $this->aData['aProductTypes'] = $this->products->GetAllProductTypes();
        $this->aData['aProductCategories'] = $this->products->GetAllProductCategories();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/products',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
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
