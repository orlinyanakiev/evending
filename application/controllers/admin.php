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

    public function Users()
    {
        $this->aData['sTitle'] = 'Потребители';
        $this->aData['aUsers'] = $this->users->GetAllUsers();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/users',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function Storages()
    {
        $this->aData['sTitle'] = 'Складове';

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/storages',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function Products()
    {
        $this->aData['sTitle'] = 'Продукти';

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/products',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function Info()
    {
        $this->aData['sTitle'] = 'Справки';

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/info',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }
}