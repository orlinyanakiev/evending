<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Events extends CI_Model
{
    const iAdjacent = 6.5;
    const iEventsLimit = 15;

    public function GetEventsLimit()
    {
        return self::iEventsLimit;
    }

    const SUPPLY = 121;
    const DISTRIBUTE = 144;
    const OBSOLETE = 169;
    const SALE = 196;
    const INCOME_ACCOUNTING = 225;
    const ADD_STORAGE = 256;
    const ADD_PRODUCT_CATEGORY = 289;
    const CHANGE_USER_TYPE = 324;
    const DELETE_USER = 361;
    const EDIT_PRODUCT = 400;
    const EDIT_PRODUCT_CATEGORY = 441;
    const DELETE_PRODUCT_CATEGORY = 484;
    const MAIN_STORAGE_SUPPLY = 529;

    public $aEventTypes = array (
        self::SUPPLY => 'Зареждане',
        self::DISTRIBUTE => 'Дистрибуция',
        self::OBSOLETE => 'Бракуване',
        self::SALE => 'Продажба',
        self::INCOME_ACCOUNTING => 'Заприходяване',
        self::ADD_STORAGE => 'Добавяне на склад',
        self::ADD_PRODUCT_CATEGORY => 'Добавяне на категория стока',
        self::CHANGE_USER_TYPE => 'Коригиране на потребител',
        self::DELETE_USER => 'Изтриване на потребител',
        self::EDIT_PRODUCT => 'Коригиране на стока',
        self::EDIT_PRODUCT_CATEGORY => 'Коригиране на категория стока',
        self::DELETE_PRODUCT_CATEGORY => 'Изтриване на категория стока',
        self::MAIN_STORAGE_SUPPLY => 'Зареждане на централен склад',
    );

    private $sEventTable = 'events';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('users');
    }

    public function RegisterEvent($oUser, $iType, $aDescription)
    {
        $aEventData = array(
            'Type' => intval($iType),
            'UserId' => $oUser->Id,
            'Description' => json_encode($aDescription),
            'DateRegistered' => date('Y-m-d H:i:s'),
        );

        return $this->db->insert($this->sEventTable,$aEventData);
    }

    public function GetEventById($iEventId)
    {
        return $this->db->get_where($this->sEventTable,array('Id' => $iEventId))->first_row();
    }

    public function ListEvents($iPage = 1, $iLimit = 0, $iUserId = 0, $iType = 0, $sStartDate = '', $sEndDate = '')
    {
        $sPagination = '';
        $iOffset = ($iPage - 1) * $iLimit;
        $iCount = $this->db->get($this->sEventTable)->num_rows();

        if($iLimit != 0){
            $sPagination = $this->GetPagination($iPage, $iCount, $iLimit);
        }

        $this->db->order_by('DateRegistered','desc');
        $this->db->limit($iLimit, $iOffset);
        $aEvents = $this->db->get($this->sEventTable)->result();

        if(is_array($aEvents) && !empty($aEvents)){
            foreach ($aEvents as $iKey => $oEvent){
                $aEvents[$iKey]->UserId = $this->users->GetUser($oEvent->UserId);
                $aEvents[$iKey]->Type = $this->aEventTypes[$oEvent->Type];
            }
        }

        return $aData = array('aEvents' => $aEvents, 'sPagination' => $sPagination);
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
