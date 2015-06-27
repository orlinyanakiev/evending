<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users extends CI_Model
{
    public $aUserTypes = array(
        '0' => 'Посетител',
        '1' => 'Дистрибутор',
        '2' => 'Администратор'
    );

    const iLimit = 30;
    const iAdjacent = 5.5;

    private $sDistributorsTable = 'distributors';
    private $sUsersTable = 'users';

    public function getLimit()
    {
        return self::iLimit;
    }

    public function __construct()
    {
        parent::__construct();
    }
    
    public function CheckUser($sLoginName,$sPassword)
    {
        $aUserData = array(
            'LoginName' => $sLoginName,
            'Password' => sha1($sPassword),
            'IsDeleted' => '0',
        );

        $this->db->where($aUserData);
        $aUser = $this->db->get($this->sUsersTable)->row();
        if(isset($aUser->LoginName)){
            return $aUser;
        }
        
        return array();
    }

    public function GetUser($iUserId)
    {
        $iUserId = (int)$iUserId;

        $this->db->where(array(
            'Id' => $iUserId,
            'IsDeleted' => '0',
        ));
        
        $oUser = $this->db->get($this->sUsersTable)->first_row();
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

    public function ChangeUserType($iUserId, $iType)
    {
        $this->db->where('Id',$iUserId);
        return $this->db->update($this->sUsersTable,array('Type' => $iType));
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

    public function EditDistributor($iUserId,$aDistributorData)
    {
        $this->db->where('Id' , $iUserId);
        $this->db->update($this->sDistributorsTable,$aDistributorData);
    }

    public function GetDistributorById($iUserId)
    {
        $this->db->where('Id', $iUserId);
        return $this->db->get($this->sDistributorsTable)->first_row();
    }

    public function GetDistributorByStorageId($iStorageId)
    {
        return $this->db->get_where($this->sDistributorsTable,array('StorageId' => $iStorageId))->first_row();
    }

    public function ListUsers($iPage = 1, $iLimit = 0, $iType = 0)
    {
        $this->db->where('IsDeleted','0');
        if($iType != 0){
            $this->db->where('Type',$iType);
        }
        $this->db->order_by("Id","asc");
        $oQuery = $this->db->get($this->sUsersTable);
        $iCount = $oQuery->num_rows();

        if($iCount > $iLimit && $iLimit != 0){
            $iOffset = ($iPage - 1) * $iLimit;
            $this->db->where('IsDeleted','0');
            if($iType != 0){
                $this->db->where('Type',$iType);
            }
            $this->db->order_by("Id","asc");
            $this->db->limit($iLimit, $iOffset);

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

        $stuff ='
                    <li>
                      <a href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>
                    <li><a href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li>
                      <a href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>';

        if ($iLastPage > 1){
            //Prev and Next button
            if ($iPage > 1) {
                $sPrev.= "<li><a class=\"prev\" href=\"javascript:void(0);\" page-number=\"$iPrev\"><i class=\"fa fa-angle-left\"></i></a></li>";
            }
            if ($iPage < $iLastPage) {
                $sNext.= "<li><a class=\"next\" href=\"javascript:void(0);\" page-number=\"$iNext\"><i class=\"fa fa-angle-right\"></i></a></li>";
            }

            //All pages are shown
            if ($iLastPage <= ceil($iAdjacent + 1) * 2){
                for ($iCounter = 1; $iCounter <= $iLastPage; $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<li><a class=\"active\" href=\"javascript:void(0);\">$iCounter</a></li>";
                    } else {
                        $sPagination.="<li><a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a></li>";
                    }
                }
            } //Page at start - hiding pages at the end
            elseif ($iPage <= 1 + ceil($iAdjacent)){
                for ($iCounter = 1; $iCounter <= ceil($iAdjacent) * 2; $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<li><a class=\"active\" href=\"javascript:void(0);\">$iCounter</a></li>";
                    } else {
                        $sPagination.="<li><a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a></li>";
                    }
                }
                $sLast ="<li><a class=\"last\" href=\"javascript:void(0);\" page-number=\"$iLastPage\"> <i class=\"fa fa-angle-double-right\"></i></a></li>";
            } //Page in the middle - hiding pages at the start and at the end;
            elseif ($iPage < $iLastPage - ceil($iAdjacent)){
                $sFirst = "<li><a class=\"first\" href=\"javascript:void(0);\" page-number=\"1\"><i class=\"fa fa-angle-double-left\"></i></a></li>";
                for ($iCounter = $iPage - floor($iAdjacent); $iCounter <= $iPage + floor($iAdjacent); $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<li><a class=\"active\" href=\"javascript:void(0);\">$iCounter</a></li>";
                    } else {
                        $sPagination.="<li><a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a></li>";
                    }
                }
                $sLast = "<li><a class=\"last\" href=\"javascript:void(0);\" page-number=\"$iLastPage\"> <i class=\"fa fa-angle-double-right\"></i></a></li>";
            } //Page is at the end - hiding pages at the start
            else {
                $sFirst = "<li><a class=\"first\" href=\"javascript:void(0);\" page-number=\"1\"><i class=\"fa fa-angle-double-left\"></i></a></li>";
                for ($iCounter = $iLastPage - $iAdjacent * 2; $iCounter <= $iLastPage; $iCounter++){
                    if ($iPage == $iCounter){
                        $sPagination.="<li><a class=\"active\" href=\"javascript:void(0);\">$iCounter</a></li>";
                    } else {
                        $sPagination.="<li><a href=\"javascript:void(0);\" page-number=\"$iCounter\">$iCounter</a></li>";
                    }
                }
            }

            $sPagination = "<nav><ul class=\"pagination\">" . $sFirst . $sPrev . $sPagination . $sNext . $sLast . "</ul></nav>\n";
        }

        return $sPagination;
    }

//    public function EditUser($aUserData)
//    {
//        if(isset($aUserData['UserId'])){
//            $iUserId = intval($aUserData['UserId']);
//
//            $oUser = $this->GetUser($iUserId);
//
//            $aNewUserData = array(
//                'FirstName' => $aUserData['FirstName'],
//                'LastName' => $aUserData['LastName'],
//                'LoginName' => $aUserData['LoginName'],
//                'Type' => $aUserData['Type']
//            );
//
//            if(is_object($oUser) && $oUser->LoginName == $aNewUserData['LoginName']){
//                $this->db->where('Id', $iUserId);
//                return $this->db->update($this->sUsersTable,$aNewUserData);
//            } else {
//                return false;
//            }
//        }
//
//        return false;
//    }

//    public function DeleteUserById($iUserId)
//    {
//        $this->db->where('Id',$iUserId);
//        return $this->db->update($this->sUsersTable,array('IsDeleted' => '1'));
//    }

}
