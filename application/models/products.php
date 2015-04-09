<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Model
{
    private $sProductTable = 'products';
    private $sTypeTable = 'producttypes';

    public $aProductCategories = array(
        '1' => 'Салати',
        '2' => 'Супи',
        '3' => 'Постна серия',
        '4' => 'Ястия с пилешко',
        '5' => 'Ястия с телешко',
        '6' => 'Ястия със свинско',
        '7' => 'Ястия с кайма',
        '8' => 'Пикантни ястия',
        '9' => 'Меню на шефа',
        '10' => 'Италианска серия',
        '11' => 'Печено с гарнитура',
        '12' => 'Серия XXL',
        '13' => 'Сандвичи',
        '14' => 'Тортили',
        '15' => 'Банички'
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function GetProductById($iProductId){
        $aResult = $this->db->get_where($this->sProductTable,array('Id' => $iProductId))->result();
        return $aResult[0];
    }

    public function GetAllProducts($iLimit = 100, $iOffest = 0)
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
            'ProductionPrice' => $aProductTypeData['ProductionPrice'],
        );

        return $this->db->insert($this->sTypeTable,$aProductTypeInsertData);
    }

    public function GetProductTypeById($iId)
    {
        $this->db->where('Id',$iId);
        $aResult = $this->db->get($this->sTypeTable)->result();
        return $aResult[0];
    }

    public function GetAllProductTypes($iLimit = 100, $iOffest = 0)
    {
        return $this->db->get($this->sTypeTable,$iLimit,$iOffest)->result();
    }
}