<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Storages extends CI_Model
{
    private $sStoragesTable = 'storages';
    private $sSalesTable = 'sales';
    private $sDistributorsTable = 'distributors';
    private $sIncomeTable = 'income';
    const iSalesLimit = 12;
    const iStorageLimit = 12;
    const iAdjacent = 6.5;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('products');
    }

    public function getStorageLimit()
    {
        return self::iStorageLimit;
    }

    public function getSalesLimit()
    {
        return self::iSalesLimit;
    }

    public function MainStorageSupply($aStorageSupplyData)
    {
        $iCategoryId = (int) $aStorageSupplyData['Category'];
        $iQuantity = (int) $aStorageSupplyData['Quantity'];
        $sExpirationDate = date('Y-m-d',strtotime(str_replace('.','-',$aStorageSupplyData['ExpirationDate']).' 00:00:00'));

        $aProductData = array(
            'Category' => $iCategoryId,
            'Quantity' => $iQuantity,
            'ExpirationDate' => $sExpirationDate,
        );

        $oProduct = $this->products->CheckIfProductExists($aProductData);

        if(is_object($oProduct)){
            $fOldValue = $oProduct->Value * $oProduct->Quantity;
            $fNewValue = $fOldValue + $aStorageSupplyData['SupplyValue'];
            $iNewQuantity = $oProduct->Quantity + $iQuantity;
            $fNewAvgValue = round($fNewValue/$iNewQuantity, 2);
            $bResult = $this->products->UpdateProductQuantity($oProduct->Id, $iNewQuantity, $fNewAvgValue);
        } else {
            $aProductData['Value'] = round($aStorageSupplyData['SupplyValue'], 2);
            $bResult = $this->products->AddProduct($aProductData);
        }

        return $bResult;
    }

    public function AddVendingMachine($aData)
    {
        $oStorageExists = $this->GetStorageByName($aData['Name']);

        if(!is_object($oStorageExists)){
            $aInsertData = array(
                'Name' => $aData['Name'],
                'Address' => $aData['Address'],
                'Type' => '3',
                'Cash' => $aData['Cash'],
            );

            return $this->db->insert($this->sStoragesTable,$aInsertData);
        }

        return false;
    }

    public function AddDistributorStorage($iUserId)
    {
        $oUser = $this->users->GetUser($iUserId);

        $aDistributorStorageData = array(
            'Name' => $oUser->FirstName.' '.$oUser->LastName,
            'Address' => '',
            'Cash' => '',
            'Type' => '2',
        );

        $this->db->insert($this->sStoragesTable,$aDistributorStorageData);
        return $this->db->insert_id();
    }

    public function GetStorageByName($sStorageName)
    {
        return $this->db->get_where($this->sStoragesTable,array('Name' => $sStorageName))->first_row();
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

        $bStorageOne = $this->EditStorageAvailability((int)$oStorageOne->Id, $aStorageOneAvailability);
        $bStorageTwo = $this->EditStorageAvailability((int)$oStorageTwo->Id, $aStorageTwoAvailability);

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
            $sExpirationDate = date('Y-m-d',strtotime(str_replace('.','-',$_POST['ExpirationDate']).' 00:00:00'));
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

    public function EditStorageAvailability($iStorageId, array $aAvailability)
    {
        foreach($aAvailability as $iProductId => $iQuantity){
            if($iQuantity == 0){
                unset($aAvailability[$iProductId]);
            }
            $oProduct = $this->products->GetProductById($iProductId);
            if($oProduct->IsDeleted == '1'){
                unset($aAvailability[$iProductId]);
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

    public function EditStorage($iStorageId, $aStorageData)
    {
        $this->db->where('Id',$iStorageId);
        return $this->db->update($this->sStoragesTable, $aStorageData);
    }

    public function DeleteStorage($iStorageId)
    {
        $aDeleteStorage['IsDeleted'] = '1';

        $this->db->where('Id', $iStorageId);
        return $this->db->update($this->sStoragesTable,$aDeleteStorage);
    }

    public function ListStorages($iPage = 1, $iLimit = 0, $iType = 0)
    {
        $this->db->where('IsDeleted','0');
        if($iType != 0){
            $this->db->where('Type', $iType);
        }
        $this->db->order_by("Id","asc");
        $oQuery = $this->db->get($this->sStoragesTable);
        $iCount = $oQuery->num_rows();

        if($iCount > $iLimit && $iLimit != 0){
            $iOffset = ($iPage - 1) * $iLimit;
            $this->db->where('IsDeleted','0');
            $this->db->order_by("Id","asc");
            $this->db->limit($iLimit, $iOffset);

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

    //Sales
    public function Sale($aData)
    {
        $iProductId = (int)$aData['Product'];
        $iQuantity = (int)$aData['Quantity'];
        $iStorageId = (int)$aData['Storage'];
        $oStorage = $this->GetStorageById($iStorageId);

        $aStorageAvailability = json_decode($oStorage->Availability, true);
        if(is_array($aStorageAvailability)){
            if($aStorageAvailability[$iProductId] >= $iQuantity){
                $aStorageAvailability[$iProductId] -= $iQuantity;
            } else {
                return false;
            }
        } else {
            return false;
        }

        $oProduct = $this->products->GetProductById($iProductId);

        $aSaleData = array(
            'StorageId' => $iStorageId,
            'ProductId' => $iProductId,
            'Quantity' => $iQuantity,
            'Price' => $oProduct->Price,
            'Value' => $oProduct->Value,
            'Date' => date('Y-m-d H:i:s'),
        );

        $bSale = $this->db->insert($this->sSalesTable,$aSaleData);

        $bStorage = $this->EditStorageAvailability($oStorage->Id, $aStorageAvailability);

        if($bSale && $bStorage){
            $fCash = $oStorage->Cash + ($iQuantity * $oProduct->Price);

            $this->db->where('Id', $oStorage->Id);
            $bAddCashToStorage = $this->db->update($this->sStoragesTable,array('Cash' => $fCash));
            if($bAddCashToStorage){
                return true;
            }

            return false;
        }

        return false;
    }

    public function AccountIncome($iStorageId, $fIncome)
    {
        return $this->db->insert($this->sIncomeTable,array('StorageId' => $iStorageId, 'Value' => $fIncome, 'DateAccounted' => date('Y-m-d H:i:s')));
    }

    public function Obsolete($aData)
    {
        $iProductId = (int)$aData['Product'];
        $iQuantity = (int)$aData['Quantity'];
        $iStorageId = (int)$aData['Storage'];
        $oStorage = $this->GetStorageById($iStorageId);

        $aStorageAvailability = json_decode($oStorage->Availability, true);
        if(is_array($aStorageAvailability)){
            if($aStorageAvailability[$iProductId] >= $iQuantity){
                $aStorageAvailability[$iProductId] -= $iQuantity;
            } else {
                return false;
            }
        } else {
            return false;
        }

        $oProduct = $this->products->GetProductById($iProductId);

        $aSaleData = array(
            'StorageId' => $iStorageId,
            'ProductId' => $iProductId,
            'Quantity' => $iQuantity,
            'Price' => $oProduct->Price,
            'Value' => $oProduct->Value,
            'Date' => date('Y-m-d H:i:s'),
        );

        $bSale = $this->db->insert($this->sSalesTable,$aSaleData);

        $bStorage = $this->EditStorageAvailability($oStorage->Id, $aStorageAvailability);

        if($bSale && $bStorage){
            return true;
        }

        return false;
    }

    public function GetSales($iUserId = 0, $iStorageId = 0, $sPeriod = '')
    {
        $aData = array(
            'Expense' => '0.00',
            'Income' => '0.00',
            'Profit' => '0.00',
        );

        if($iUserId != 0 ){
            $oDistributor = $this->db->get_where($this->sDistributorsTable,array('Id' => $iUserId))->first_row();
            $aStorages = json_decode($oDistributor->Storages, true);
            if(is_array($aStorages) && !empty($aStorages)){
                $this->db->where_in('StorageId', $aStorages);
            } else {
                return $aData;
            }
        }

        if($iStorageId != 0){
            $this->db->where('StorageId', $iStorageId);
        }

        if($sPeriod != ''){

        }

        $aSales = $this->db->get($this->sSalesTable)->result();

        if(is_array($aSales) && !empty($aSales)){
            foreach($aSales as $oSale){
                $aData['Expense'] += $oSale->Value * $oSale->Quantity;
                $aData['Income'] += $oSale->Price * $oSale->Quantity;
            }
            $aData['Profit'] += $aData['Income'] - $aData['Expense'];
        }
        return $aData;
    }

    //Vending Machines
    public function GetDistributorVendingMachines($iUserId)
    {
        $oDistributor = $this->db->get_where($this->sDistributorsTable, array('Id' => $iUserId))->first_row();

        if(isset($oDistributor->Storages)){
            $aDistributorStorages = json_decode($oDistributor->Storages, true);
            if(is_array($aDistributorStorages) && !empty($aDistributorStorages)){
                foreach($aDistributorStorages as $iKey => $iStorageId){
                    $aDistributorStorages[$iKey] = $this->GetStorageById($iStorageId);
                }
                return $aDistributorStorages;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

}
