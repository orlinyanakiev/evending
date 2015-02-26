<?php

require_once (APPPATH . 'core/My_MemberController.php');

class My_AdminController extends My_MemberController
{
    public function __construct()
        {
            parent::__construct();
        }
}