<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users extends CI_Model
{
    public $aUserTypes = array(
        '0' => 'Потребител',
        '1' => 'Дистрибутор',
        '2' => 'Оператор',
        '3' => 'Администратор'
    );

    const iLimit = 10;
    const iAdjacent = 6.5;

    private $sDistributorsTable = 'distributors';
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

    public function getLimit()
    {
        return self::iLimit;
    }

    public function EditUser($aUserData)
    {
        if(isset($aUserData['UserId'])){
            $iUserId = intval($aUserData['UserId']);

            $aNewUserData = array(
                'FirstName' => $aUserData['FirstName'],
                'LastName' => $aUserData['LastName'],
                'LoginName' => $aUserData['LoginName'],
                'Type' => $aUserData['Type']
            );

            $this->db->where('Id', $iUserId);
            return $this->db->update($this->sUsersTable,$aNewUserData);
        }

        return false;
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
                        'Registered' => date('Y-m-d H:i:s'),
                    );

                $bAddUser = $this->db->insert($this->sUsersTable, $aUserData);
                
                return json_encode(array('success' => $bAddUser));
            }
            
            return json_encode(array('success' => $bAddUser, 'warning' => 'username'));
        }
        
        return json_encode(array('success' => $bAddUser));
    }

    public function AddDistributor($aDistributorData)
    {
        return $this->db->insert($this->sDistributorsTable,$aDistributorData);
    }

    public function GetDistributor($iUserId)
    {
        $this->db->where('Id', $iUserId);
        return $this->db->get($this->sDistributorsTable)->first_row();
    }

    public function ListUsers($iPage = 1, $iLimit = 0)
    {
        $this->db->where('Active','1');
        $this->db->order_by("Id","asc");
        $oQuery = $this->db->get($this->sUsersTable);
        $iCount = $oQuery->num_rows();

        if($iCount > $iLimit && $iLimit != 0){
            $iLimit = self::iLimit;
            $iOffest = ($iPage - 1) * $iLimit;
            $this->db->where('Active','1');
            $this->db->order_by("Id","asc");
            $this->db->limit($iLimit, $iOffest);

            $aData['aUsers'] = $this->db->get($this->sUsersTable)->result();
            $aData['sPagination'] = $this->GetPagination($iPage, $iCount, $iLimit);
            return $aData;
        }

        return $aData = array('aUsers' => $oQuery->result(), 'sPagination' => '');
    }

    public function GetPagination($iPage, $iCount, $iLimit, $iAdjacent = self::iAdjacent)
    {
        $iLastPage = ceil($iCount/$iLimit);
        $sPagination = '';
        $sFirst = '';
        $sLast = '';
        $sPrev = '';
        $sNext = '';
        $iPrev = $iPage - 1;
        $iNext = $iPage + 1;

        if ($iLastPage > 1){
            //Prev and Next button
            if ($iPage > 1) {
                $sPrev.= "<a class=\"prev\" href=\"javascript:void(0);\" page-number=\"$iPrev\"><i class=\"fa fa-angle-left\"></i> </a>";
            }
            if ($iPage < $iLastPage) {
                $sNext.= "<a class=\"next\" href=\"javascript:void(0);\" page-number=\"$iNext\"> <i class=\"fa fa-angle-right\"></i></a>";
            }

            //All pages are shown
            if ($iLastPage <= ceil($iAdjacent + 1) * 2){
                for ($iCounter = 1; $iCounter <= $iLastPage; $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<a class=\"active\" href=\"javascript:void(0);\">$iCounter</a>";
                    } else {
                        $sPagination.="<a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a>";
                    }
                }
            } //Page at start - hiding pages at the end
            elseif ($iPage <= 1 + ceil($iAdjacent)){
                for ($iCounter = 1; $iCounter <= ceil($iAdjacent) * 2; $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<a class=\"active\" href=\"javascript:void(0);\">$iCounter</a>";
                    } else {
                        $sPagination.="<a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a>";
                    }
                }
                $sLast ="<a class=\"last\" href=\"javascript:void(0);\" page-number=\"$iLastPage\"> <i class=\"fa fa-angle-double-right\"></i></a>";
            } //Page in the middle - hiding pages at the start and at the end;
            elseif ($iPage < $iLastPage - ceil($iAdjacent)){
                $sFirst = "<a class=\"first\" href=\"javascript:void(0);\" page-number=\"1\"><i class=\"fa fa-angle-double-left\"></i> </a> ";
                for ($iCounter = $iPage - floor($iAdjacent); $iCounter <= $iPage + floor($iAdjacent); $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<a class=\"active\" href=\"javascript:void(0);\">$iCounter</a>";
                    } else {
                        $sPagination.="<a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a>";
                    }
                }
                $sLast = " <a class=\"last\" href=\"javascript:void(0);\" page-number=\"$iLastPage\"> <i class=\"fa fa-angle-double-right\"></i></a>";
            } //Page is at the end - hiding pages at the start
            else {
                $sFirst = "<a class=\"first\" href=\"javascript:void(0);\" page-number=\"1\"><i class=\"fa fa-angle-double-left\"></i> </a> ";
                for ($iCounter = $iLastPage - $iAdjacent * 2; $iCounter <= $iLastPage; $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<a class=\"active\" href=\"javascript:void(0);\">$iCounter</a>";
                    } else {
                        $sPagination.="<a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a>";
                    }
                }
            }

            $sPagination = "<div class=\"pagination_list\">" . $sFirst . $sPrev . $sPagination . $sNext . $sLast . "</div>\n";
        }

        return $sPagination;
    }

}
