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
    
    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}