<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Storages extends CI_Model
{
    private $sStoragesTable = 'storages';
    private $sProductTable = 'products';
    const iLimit = 12;
    const iAdjacent = 6.5;

    public $aStorageTypes = array(
        '1' => 'Склад',
        '2' => 'Дистрибутор',
        '3' => 'Вендинг машина',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function getLimit()
    {
        return self::iLimit;
    }

    public function AddStorage($aStorageData)
    {
        $aInsertData = array(
            'Name' => $aStorageData['Name'],
            'Address' => $aStorageData['Address'],
            'Type' => $aStorageData['Type'],
        );

        $this->db->insert($this->sStoragesTable,$aInsertData);
        $iStorageId = $this->db->insert_id();
        return $iStorageId;
    }

    public function GetProductByTypeAndDate($aProductData)
    {
        $this->db->where($aProductData);
        return $this->db->get($this->sProductTable)->result();
    }

    public function Distribute($aData)
    {
        $iProductId = (int)$aData['Product'];
        $iQuantity = (int)$aData['Quantity'];
        $oStorageOne = $this->GetStorageById((int)$aData['Storage1']);
        $oStorageTwo = $this->GetStorageById((int)$aData['Storage2']);

        $aStorageOneAvailability = json_decode($oStorageOne->Availability, true);
        if(is_array($aStorageOneAvailability)){
            if($aStorageOneAvailability[$iProductId] >= $iQuantity){
                $aStorageOneAvailability[$iProductId] -= $iQuantity;
            } else {
                return false;
            }
        } else {
            return false;
        }

        $aStorageTwoAvailability = json_decode($oStorageTwo->Availability, true);
        if(is_array($aStorageTwoAvailability) && array_key_exists($iProductId,$aStorageTwoAvailability)){
            $aStorageTwoAvailability[$iProductId] += $iQuantity;
        } else {
            $aStorageTwoAvailability[$iProductId] = $iQuantity;
        }

        $bStorageOne = $this->UpdateStorageAvailability((int)$oStorageOne->Id, $aStorageOneAvailability);
        $bStorageTwo = $this->UpdateStorageAvailability((int)$oStorageTwo->Id, $aStorageTwoAvailability);

        if($bStorageOne && $bStorageTwo){
            return true;
        }
    }

    public function StorageSupply($aStorageSupplyData, $oProductType)
    {
        $iStorageId = (int) $aStorageSupplyData['Storage'];
        $iQuantity = (int) $aStorageSupplyData['Quantity'];
        $sPrice = $aStorageSupplyData['Price'];
        $sValue = isset($aStorageSupplyData['Value']) ? $aStorageSupplyData['Value'] : '';

        if(isset($aStorageSupplyData['ExpirationDate'])){
            $sExpirationDate = date('d.m.Y',strtotime(str_replace('.','-',$_POST['ExpirationDate']).' 00:00:00'));
        }

        $aProductData = array(
            'Type' => $oProductType->Id,
            'ExpirationDate' => $sExpirationDate,
            'Price' => $sPrice,
            'Value' => $sValue,
        );

        $aProductExists = $this->GetProductByTypeAndDate($aProductData);

        if(is_array($aProductExists) && !empty($aProductExists)){
            $iProductId = $aProductExists[0]->Id;
            $bProductExists = true;
        } else {
            $bProductExists = $this->db->insert($this->sProductTable, $aProductData);
            $iProductId = $this->db->insert_id();
        }

        if($bProductExists){
            $oStorage = $this->GetStorageById($iStorageId);
            $aAvailability = json_decode($oStorage->Availability, true);

            if(!is_array($aAvailability)){
                $aAvailability = array( $iProductId => $iQuantity);
            } else {
                if(!array_key_exists($iProductId,$aAvailability)){
                    $aAvailability[$iProductId] = $iQuantity;
                } else {
                    $aAvailability[$iProductId] += $iQuantity;
                }
            }

            $this->db->where('Id',$iStorageId);
            return $this->db->update($this->sStoragesTable,array('Availability' => json_encode($aAvailability)));
        }

        return false;
    }

    public function UpdateStorageAvailability($iStorageId, array $aAvailability)
    {
        foreach($aAvailability as $iKey => $iQuantity){
            if($iQuantity == 0){
                unset($aAvailability[$iKey]);
            }
        }

        $aUpdateData['Availability'] = json_encode($aAvailability);

        $this->db->where('Id',$iStorageId);
        $bResult = $this->db->update($this->sStoragesTable,$aUpdateData);
        return $bResult;
    }

    public function GetStorageById($iId)
    {
        $this->db->where('Id',$iId);
        $oResult = $this->db->get($this->sStoragesTable)->first_row();

        return $oResult;
    }

    public function UpdateStorage($iStorageId, $aStorageData)
    {
        $this->db->where('Id',$iStorageId);
        return $this->db->update($this->sStoragesTable, $aStorageData);
    }

    public function DeleteStorage($iStorageId)
    {
        $aDeleteStorage['Active'] = '0';

        $this->db->where('Id', $iStorageId);
        $bResult = $this->db->update($this->sStoragesTable,$aDeleteStorage);
        return $bResult;
    }

    public function ListStorages($iPage = 1, $iLimit = 0, $iType = 0)
    {
        $this->db->where('Active','1');
        if($iType != 0){
            $this->db->where('Type', $iType);
        }
        $this->db->order_by("Id","asc");
        $oQuery = $this->db->get($this->sStoragesTable);
        $iCount = $oQuery->num_rows();

        if($iCount > $iLimit && $iLimit != 0){
            $iLimit = self::iLimit;
            $iOffest = ($iPage - 1) * $iLimit;
            $this->db->where('Active','1');
            $this->db->order_by("Id","asc");
            $this->db->limit($iLimit, $iOffest);

            $aData['aStorages'] = $this->db->get($this->sStoragesTable)->result();
            $aData['sPagination'] = $this->GetPagination($iPage, $iCount, $iLimit);
            return $aData;
        }

        return $aData = array('aStorages' => $oQuery->result(), 'sPagination' => '');
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
