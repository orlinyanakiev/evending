<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users extends CI_Model
{
    private $sTable = 'users';
    public function __construct()
    {
        parent::__construct();
    }
    
    public function CheckUser($sLoginName,$sPassword)
    {
        $aUserData = array(
            'LoginName' => $sLoginName,
            'Password' => sha1($sPassword),
            'Active' => '1',
        );

        $this->db->where($aUserData);
        $aUser = $this->db->get($this->sTable)->row();
        if(isset($aUser->LoginName)){
            return $aUser;
        }
        
        return array();
    }
    
    public function GetUser($iId)
    {
        $iId = (int)$iId;

        $this->db->where('Id', $iId);
        
        $aUser = $this->db->get($this->sTable)->row();
        if(isset($aUser->LoginName)){
            return $aUser;
        }
        
        return array();
    }
    
    public function CheckLoginName($sLoginName)
    {
        $this->db->where('LoginName',$sLoginName);
        $iResult = $this->db->get($this->sTable)->num_rows();
        
        if($iResult > 0){
            return true;
        }
        
        return false;
    }
    
    public function AddUser(array $aUserData)
    {
        $bAddUser = false;
        $aGetUser = $this->GetUser($aUserData['Id']);
        $bCheckLoginName = $this->CheckLoginName($aUserData['LoginName']);
        
        if (($aUserData['Id']) != null){
            if (!empty($aGetUser)) {
                return json_encode(array('success' => $bAddUser));
            }
        } else {
            if($bCheckLoginName !== true){
                $aUserData = array(
                        'FirstName' => $aUserData['FirstName'],
                        'LastName' => $aUserData['LastName'],
                        'LoginName' => $aUserData['LoginName'],
                        'Password' => sha1($aUserData['Password']),
                        'Company' => $aUserData['Company'],
                        'Registered' => date('Y-m-d H:i:s'),
                    );

                $bAddUser = $this->db->insert($this->sTable, $aUserData);
                
                return json_encode(array('success' => $bAddUser));
            }
            
            return json_encode(array('success' => $bAddUser, 'warning' => 'username'));
        }
        
        return json_encode(array('success' => $bAddUser));
    }

    public function GetAllUsers($iLimit = 10, $iOffest = 0)
    {
        $this->db->where('Active','1');
        return $this->db->get($this->sTable,$iLimit,$iOffest)->result();
    }
}