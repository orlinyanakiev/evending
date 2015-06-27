<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Model
{
    private $sProductTable = 'products';
    private $sCategoryTable = 'productcategories';

    const iCategoriesLimit = 12;
    const iLimit = 12;
    const iAdjacent = 6.5;

    public function getLimit()
    {
        return self::iLimit;
    }

    public function getCategoriesLimit()
    {
        return self::iLimit;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function CheckIfProductExists($aProductData)
    {
        return $this->db->get_where($this->sProductTable,array('Category' => $aProductData['Category'], 'ExpirationDate' => $aProductData['ExpirationDate']))->first_row();
    }

    public function UpdateProductQuantity($iProductId, $iQuantity, $fValue)
    {
        $aUpdateData = array(
            'Quantity' => $iQuantity,
            'Value' => $fValue,
        );
        $this->db->where('Id',$iProductId);
        return $this->db->update($this->sProductTable,$aUpdateData);
    }

    public function AddProduct($aProductData)
    {
        return $this->db->insert($this->sProductTable, $aProductData);
    }

    public function GetProductById($iProductId){
        $oProduct = $this->db->get_where($this->sProductTable,array('Id' => $iProductId))->first_row();
        if(is_object($oProduct)){
            $oProduct->Category = $this->GetProductCategoryById($oProduct->Category);
        }

        return $oProduct;
    }

    public function EditProduct($iProductId, $aData)
    {
        $this->db->where('Id',$iProductId);
        return $this->db->update($this->sProductTable,$aData);
    }

    public function DeleteProduct($iProductId)
    {
        $this->db->where('Id',$iProductId);
        return $this->db->update($this->sProductTable,array('IsDeleted' => '1'));
    }

    public function ListProducts($iPage = 1, $iLimit = 0, $iCategory = 0)
    {
        $this->db->where('IsDeleted', '0');
        $this->db->where('Quantity >', '0');
        if($iCategory != 0){
            $this->db->where('Category', $iCategory);
        }
        $this->db->order_by("Category","asc");
        $oQuery = $this->db->get($this->sProductTable);
        $iCount = $oQuery->num_rows();

        if($iCount > 0){
            if($iCount > $iLimit && $iLimit != 0){
                $iOffset = ($iPage - 1) * $iLimit;

                $this->db->where('IsDeleted','0');
                $this->db->where('Quantity >', '0');
                if($iCategory != 0){
                    $this->db->where('Category', $iCategory);
                }
                $this->db->order_by("Category","asc");
                $this->db->limit($iLimit, $iOffset);

                $aProducts = $this->db->get($this->sProductTable)->result();
                $sPagination = $this->GetPagination($iPage, $iCount, $iLimit);

                foreach ($aProducts as $iKey => $oProduct){
                    $oProduct->Category = $this->GetProductCategoryById($oProduct->Category);
                    $aProducts[$iKey] = $oProduct;
                }

                return $aData = array(
                    'aProducts' => $aProducts,
                    'sPagination' => $sPagination,
                );
            }

            $aProducts = $oQuery->result();

            foreach ($aProducts as $iKey => $oProduct){
                $oProduct->Category = $this->GetProductCategoryById($oProduct->Category);
                $aProducts[$iKey] = $oProduct;
            }

            return $aData = array('aProducts' => $aProducts, 'sPagination' => '');
        }
    }

    public function GetProductByExpirationDate()
    {
        $this->db->where('ExpirationDate <', date('Y-m-d',(time() + 3*86400)));
        $aExpiringProducts =  $this->db->get($this->sProductTable)->result();

        if(is_array($aExpiringProducts) && !empty($aExpiringProducts)){
            foreach($aExpiringProducts as $iKey => $oExpiringProduct){
                $aExpiringProducts[$iKey]->Category = $this->GetProductCategoryById($oExpiringProduct->Category);
            }
        }

        return $aExpiringProducts;
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

    //Product Categories
    public function AddProductCategory($aProductCategoryData)
    {
        $oCategoryWithTheSameName = $this->GetProductCategoryByName($aProductCategoryData['Name']);
        $oCategoryWithTheSameBarcode = $this->GetProductCategoryByBarcode($aProductCategoryData['Barcode']);

        if(!is_object($oCategoryWithTheSameBarcode) && !is_object($oCategoryWithTheSameName)){
            $aProductCategoryInsertData = array(
                'Name' => $aProductCategoryData['Name'],
                'Barcode' => $aProductCategoryData['Barcode'],
            );

            return $this->db->insert($this->sCategoryTable,$aProductCategoryInsertData);
        }

        return false;
    }

    public function EditProductCategory($aEditProductCategoryData)
    {
        $iProductCategoryId = intval($aEditProductCategoryData['Id']);
        $aPTData = array(
            'Name' => $aEditProductCategoryData['Name'],
            'Category' => $aEditProductCategoryData['Category']
        );

        $this->db->where('Id',$iProductCategoryId);
        return $this->db->update($this->sCategoryTable,$aPTData);
    }

    public function DeleteProductCategory($iProductCategoryId)
    {
        $iProductCategoryId = intval($iProductCategoryId);
        $aDeleteProductCategory['IsDeleted'] = '1';

        $this->db->where('Id',$iProductCategoryId);
        return $this->db->update($this->sCategoryTable,$aDeleteProductCategory);
    }

    public function GetProductCategoryByName($sProductCategoryName)
    {
        $this->db->where(array('Name' => $sProductCategoryName, 'IsDeleted' => '0'));
        return $this->db->get($this->sCategoryTable)->first_row();
    }

    public function GetProductCategoryByBarcode($iBarcode)
    {
        $this->db->where(array('Barcode' => $iBarcode, 'IsDeleted' => '0'));
        return $this->db->get($this->sCategoryTable)->first_row();
    }

    public function GetProductCategoryById($iId)
    {
        return $this->db->get_where($this->sCategoryTable,array('Id' => $iId))->first_row();
    }

    public function ListProductCategories($iPage = 1, $iLimit = 0, $iCategory = 0)
    {
        $this->db->where('IsDeleted', '0');
        if($iCategory != 0){
            $this->db->where('Category', $iCategory);
        }
        $this->db->order_by("Barcode","asc");
        $oQuery = $this->db->get($this->sCategoryTable);
        $iCount = $oQuery->num_rows();

        if($iCount > 0){
            if($iCount > $iLimit && $iLimit != 0){
                $iOffset = ($iPage - 1) * $iLimit;

                $this->db->where('IsDeleted','0');
                if($iCategory != 0){
                    $this->db->where('Category', $iCategory);
                }
                $this->db->order_by("Barcode","asc");
                $this->db->limit($iLimit, $iOffset);

                $aProducts = $this->db->get($this->sCategoryTable)->result();
                $sPagination = $this->GetPagination($iPage, $iCount, $iLimit);

                return $aData = array(
                    'aProductCategories' => $aProducts,
                    'sPagination' => $sPagination,
                );
            }

            $aProducts = $oQuery->result();

            return $aData = array('aProductCategories' => $aProducts, 'sPagination' => '');
        }
    }
}
