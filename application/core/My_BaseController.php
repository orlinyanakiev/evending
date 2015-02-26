<?php 

if ( ! defined('BASEPATH'))
    exit('No direct script access allowed');

class My_BaseController extends CI_Controller
{
    public $aData = array();
    
    public function __construct() {
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->model('users');
        
        $this->CheckUser();
    }
    
    public function CheckUser($bRedirect = false)
    {
        $oUser = new stdClass();
        $this->aData['oUser'] = $oUser;
        
        if($this->session->userdata('iUserId')){
            $iUserId = $this->session->userdata('iUserId');
            
            if(isset($iUserId)){
                $this->aData['oUser'] = $this->users->GetUser($iUserId);
            }
            
        } elseif ($bRedirect) {
            redirect(base_url());
        }
    }
}