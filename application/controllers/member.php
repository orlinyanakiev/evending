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
    
    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}