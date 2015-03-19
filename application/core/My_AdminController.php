<?php

require_once (APPPATH . 'core/My_MemberController.php');

class My_AdminController extends My_MemberController
{
    private $aUserTypes = array(
        '0' => 'Потребител',
        '1' => 'Дистрибутор',
        '2' => 'Оператор',
        '3' => 'Администратор'
    );

    public function __construct()
    {
        parent::__construct();

        $this->aData['aUserTypes'] = $this->aUserTypes;
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