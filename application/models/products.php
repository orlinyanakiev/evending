<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Model
{
    private $sProductTable = 'products';
    private $sTypeTable = 'producttypes';
    private $sCategoryTable = 'productcategories';

    public function __construct()
    {
        parent::__construct();
    }

    public function GetAllProducts($iLimit = 10, $iOffest = 0)
    {
        $this->db->where('IsDeleted','0');
        return $this->db->get($this->sProductTable,$iLimit,$iOffest)->result();
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
        return $this->db->get($this->sTypeTable)->result();
    }

    public function GetAllProductTypes($iLimit = 10, $iOffest = 0)
    {
        return $this->db->get($this->sTypeTable,$iLimit,$iOffest)->result();
    }

    //Product Caterogies
    public function GetAllProductCategories()
    {
        $this->db->order_by('Id');
        return $this->db->get($this->sCategoryTable)->result();
    }
}