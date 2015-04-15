<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Model
{
    private $sProductTable = 'products';
    private $sTypeTable = 'producttypes';

    const iLimit = 10;
    const iAdjacent = 6.5;

    public function getLimit()
    {
        return self::iLimit;
    }

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
        '15' => 'Банички',
        '16' => 'Захарни',
        '17' => 'Млечни',
        '18' => 'Други',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function GetProductById($iProductId){
        return $this->db->get_where($this->sProductTable,array('Id' => $iProductId))->first_row();
    }

    public function ListProducts($iPage = 1, $iLimit = 0, $iType = 0)
    {
        $this->db->where('IsDeleted', '0');
        if($iType != 0){
            $this->db->where('Type', $iType);
        }
        $this->db->order_by("Id","asc");
        $oQuery = $this->db->get($this->sProductTable);
        $iCount = $oQuery->num_rows();

        if($iCount > 0){
            if($iCount > $iLimit && $iLimit != 0){
                $iLimit = self::iLimit;
                $iOffest = ($iPage - 1) * $iLimit;

                $this->db->where('Active','1');
                if($iType != 0){
                    $this->db->where('Type', $iType);
                }
                $this->db->order_by("Id","asc");
                $this->db->limit($iLimit, $iOffest);

                $aProducts = $this->db->get($this->sProductTable)->result();
                $sPagination = $this->GetPagination($iPage, $iCount, $iLimit);

                foreach ($aProducts as $iKey => $oProduct){
                    $oProduct->Type = $this->GetProductTypeById($oProduct->Type);
                    $aProducts[$iKey] = $oProduct;
                }

                return $aData = array(
                    'aProducts' => $aProducts,
                    'sPagination' => $sPagination,
                );
            }

            $aProducts = $oQuery->result();

            foreach ($aProducts as $iKey => $oProduct){
                $oProduct->Type = $this->GetProductTypeById($oProduct->Type);
                $aProducts[$iKey] = $oProduct;
            }

            return $aData = array('aProducts' => $aProducts, 'sPagination' => '');
        }
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

    //Product Types
    public function AddProductType($aProductTypeData)
    {
        $oExistingProduct = $this->GetProductTypeByName($aProductTypeData['Name']);

        if(!is_object($oExistingProduct)){
            $aProductTypeInsertData = array(
                'Name' => $aProductTypeData['Name'],
                'Category' => $aProductTypeData['Category'],
            );

            return $this->db->insert($this->sTypeTable,$aProductTypeInsertData);
        }

        return false;
    }

    public function EditProductType($aEditProductTypeData)
    {
        $iProductTypeId = intval($aEditProductTypeData['Id']);
        $aPTData = array(
            'Name' => $aEditProductTypeData['Name'],
            'Category' => $aEditProductTypeData['Category']
        );

        $this->db->where('Id',$iProductTypeId);
        return $this->db->update($this->sTypeTable,$aPTData);
    }

    public function DeleteProductType($iProductTypeId)
    {
        $iProductTypeId = intval($iProductTypeId);
        $aDeleteProductType['IsDeleted'] = '1';

        $this->db->where('Id',$iProductTypeId);
        return $this->db->update($this->sTypeTable,$aDeleteProductType);
    }

    public function GetProductTypeByName($sProductName)
    {
        $this->db->where(array('Name' => $sProductName, 'IsDeleted' => '0'));
        return $this->db->get($this->sTypeTable)->first_row();
    }

    public function GetProductTypeById($iId)
    {
        return $this->db->get_where($this->sTypeTable,array('Id' => $iId))->first_row();
    }

    public function GetAllProductTypes($iLimit = 10, $iOffest = 0)
    {
        $this->db->where('IsDeleted','0');
        return $this->db->get($this->sTypeTable,$iLimit,$iOffest)->result();
    }
}