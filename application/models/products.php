<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Model
{
    private $sProductTable = 'products';
    private $sTypeTable = 'producttypes';

    public function __construct()
    {
        parent::__construct();
    }

    public function GetProductById($iProductId){
        $aResult = $this->db->get_where($this->sProductTable,array('Id' => $iProductId))->result();
        return $aResult[0];
    }

    public function GetAllProducts($iLimit = 10, $iOffest = 0)
    {
        $this->db->where('IsDeleted','0');
        $aProducts = $this->db->get($this->sProductTable,$iLimit,$iOffest)->result();
        return $aProducts;
    }

    //Product Types
    public function AddProductType($aProductTypeData)
    {
        $aProductTypeInsertData = array(
            'Name' => $aProductTypeData['Name'],
            'Category' => $aProductTypeData['Category'],
            'Price' => $aProductTypeData['Price'],
            'ExpirationTime' => $aProductTypeData['ExpirationTime'],
        );

        return $this->db->insert($this->sTypeTable,$aProductTypeInsertData);
    }

    public function GetProductTypeById($iId)
    {
        $this->db->where('Id',$iId);
        $aResult = $this->db->get($this->sTypeTable)->result();
        return $aResult[0];
    }

    public function GetAllProductTypes($iLimit = 10, $iOffest = 0)
    {
        return $this->db->get($this->sTypeTable,$iLimit,$iOffest)->result();
    }
}