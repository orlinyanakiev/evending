<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Model
{
    private $sTable = 'products';
    private $sTypeTable = 'producttypes';
    private $sCategoryTable = 'productcategories';

    public function __construct()
    {
        parent::__construct();
    }

    public function GetAllProducts($iLimit = 10, $iOffest = 0)
    {
        return $this->db->get($this->sTable,$iLimit,$iOffest)->result();
    }

    //Product Types
    public function GetAllProductTypes($iLimit = 10, $iOffest = 0)
    {
        return $this->db->get($this->sTypeTable,$iLimit,$iOffest)->result();
    }

    //Product Caterogies
    public function GetAllProductCategories($iLimit = 10, $iOffest = 0)
    {
        return $this->db->get($this->sCategoryTable,$iLimit,$iOffest)->result();
    }
}