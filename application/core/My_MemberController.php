<?php

require_once (APPPATH . 'core/My_BaseController.php');

class My_MemberController extends My_BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('storages');
        $this->load->model('products');

        $this->aData['aProductCategories'] = $this->products->aProductCategories;
        $this->aData['aStorageTypes'] = $this->storages->aStorageTypes;
        $this->aData['oDistributor'] = array();

        if($this->aData['oUser']->Type == 1){
            $this->aData['oDistributor'] = $this->users->GetDistributorInfo($this->aData['oUser']->Id);

            $aStoragesData = $this->storages->ListStorages();
            $aStorages = $aStoragesData['aStorages'];
            foreach ($aStorages as $oStorage){
                if($oStorage->Id == $this->aData['oDistributor']->StorageId){
                    $this->aData['oDistributorStorage'] = $oStorage;
                }
            }
        }

        $this->CheckUser(true);
    }
}