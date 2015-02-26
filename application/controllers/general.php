<?php

require_once (APPPATH . 'core/My_BaseController.php');

class General extends My_BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function IsLogged()
    {
        $iUserId = $this->session->userdata('iUserId');
        if($iUserId > 0){
            redirect(base_url().'member/');
        }
    }
    
    public function index()
    {
        $this->IsLogged();
        $this->Login();
    }

    public function Login()
    {
        $this->aData['sTitle'] = 'Вход';

        $this->load->view('public/include/header',$this->aData);
        $this->load->view('public/pages/login',$this->aData);
        $this->load->view('public/include/footer',$this->aData);
    }
    
    public function Register()
    {
        $this->aData['sTitle'] = 'Регистрация';
        
        $this->load->view('public/include/header', $this->aData);
        $this->load->view('public/pages/register', $this->aData);
        $this->load->view('public/include/footer', $this->aData);
    }
    
    public function Authentication()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aPageData = $_POST;
            $oUser = $this->users->CheckUser($aPageData['LoginName'], $aPageData['Password']);
            
            if(is_object($oUser) && isset($oUser->Id)){
                
                $this->session->set_userdata('iUserId', $oUser->Id);

                echo json_encode(array('success' => true));
                return;
            }
            
            echo json_encode(array('success' => false));
            return;
        }
    }

    public function AddUser()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aUserData = $_POST;
            
            echo $this->users->AddUser($aUserData);
            return;
        }
    }
}