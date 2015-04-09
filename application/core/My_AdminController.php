<?php

require_once (APPPATH . 'core/My_MemberController.php');

class My_AdminController extends My_MemberController
{
    public function __construct()
    {
        parent::__construct();

        $this->aData['aUserTypes'] = $this->users->aUserTypes;
        $this->CheckRole(true);
    }

    public function CheckRole($bRedirect = false)
    {
        if($this->aData['oUser']->Type > 1){
            return;
        } elseif ($bRedirect) {
            redirect(base_url()+ 'member/Distribution');
        }
    }
}