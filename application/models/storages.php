<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Storages extends CI_Model
{
    private $sStoragesTable = 'storages';
    private $sProductTable = 'products';
    private $sProductTypeTable = 'producttypes';
    const SECONDS_IN_A_DAY = 86400;

    public function __construct()
    {
        parent::__construct();
    }

    public function AddStorage($aStorageData)
    {
        $aInsertData = array(
            'Name' => $aStorageData['Name'],
            'Address' => $aStorageData['Address'],
            'Type' => $aStorageData['Type'],
        );

        return $this->db->insert($this->sStoragesTable,$aInsertData);
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
        if(is_array($aStorageTwoAvailability)){
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

        $aProductData = array(
            'ProductType' => $oProductType->Id,
            'ExpirationDate' => date('Y-m-d', time() + $oProductType->ExpirationTime * self::SECONDS_IN_A_DAY)
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
        $aUpdateData['Availability'] = json_encode($aAvailability);

        $this->db->where('Id',$iStorageId);
        $bResult = $this->db->update($this->sStoragesTable,$aUpdateData);
        return $bResult;
    }

    public function GetStorageById($iId)
    {
        $this->db->where('Id',$iId);
        $aResult = $this->db->get($this->sStoragesTable)->result();
        if(is_object($aResult[0])){
            return $aResult[0];
        }
    }

    public function GetAllStorages($iLimit = 10, $iOffest = 0)
    {
        $this->db->where('Active','1');
        return $this->db->get($this->sStoragesTable,$iLimit,$iOffest)->result();
    }
}