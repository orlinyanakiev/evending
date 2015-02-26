<?php

require_once (APPPATH . 'core/My_MemberController.php');

class Member extends My_MemberController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->CheckUser(true);
    }
    
    public function index()
    {
        $this->aData['sTitle'] = 'Потребителска страница';
        
        $this->load->view('member/include/header',$this->aData);
        $this->load->view('member/pages/start',$this->aData);
        $this->load->view('member/include/footer',$this->aData);
    }
    
    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}