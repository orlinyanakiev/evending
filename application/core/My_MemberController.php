<?php

require_once (APPPATH . 'core/My_BaseController.php');

class My_MemberController extends My_BaseController
{
    public function __construct()
        {
            parent::__construct();

            $this->load->model('storages');
            $this->load->model('products');
            $this->load->model('producttypes');
            $this->CheckUser(true);
        }
}