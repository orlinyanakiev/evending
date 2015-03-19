<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users extends CI_Model
{
    private $sUsersTable = 'users';
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
        $aUser = $this->db->get($this->sUsersTable)->row();
        if(isset($aUser->LoginName)){
            return $aUser;
        }
        
        return array();
    }

    public function DeleteUserById($iUserId)
    {
        $this->db->where('Id',$iUserId);
        return $this->db->update($this->sUsersTable,array('Active' => '0'));
    }
    
    public function GetUser($iUserId)
    {
        $iUserId = (int)$iUserId;

        $this->db->where(array(
            'Id' => $iUserId,
            'Active' => '1',
        ));
        
        $oUser = $this->db->get($this->sUsersTable)->row();
        if(isset($oUser->LoginName)){
            return $oUser;
        }
        
        return array();
    }
    
    public function CheckLoginName($sLoginName)
    {
        $this->db->where('LoginName',$sLoginName);
        $iResult = $this->db->get($this->sUsersTable)->num_rows();
        
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

                $bAddUser = $this->db->insert($this->sUsersTable, $aUserData);
                
                return json_encode(array('success' => $bAddUser));
            }
            
            return json_encode(array('success' => $bAddUser, 'warning' => 'username'));
        }
        
        return json_encode(array('success' => $bAddUser));
    }

    public function GetAllUsers($iCurrentUserId = 0, $iLimit = 100, $iOffest = 0)
    {
//        $iCurrentUserId = (int) $iCurrentUserId;
        $this->db->where('Active','1');
//        $this->db->where('Id !=', $iCurrentUserId);
        return $this->db->get($this->sUsersTable,$iLimit,$iOffest)->result();
    }
}