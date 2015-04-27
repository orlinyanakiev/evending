<?php

require_once (APPPATH . 'core/My_BaseController.php');

class My_MemberController extends My_BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('storages');
        $this->load->model('products');
        $this->load->model('events');

        $this->aData['aEventTypes'] = $this->events->aEventTypes;
        $this->aData['aProductCategories'] = $this->products->aProductCategories;
        $this->aData['aStorageTypes'] = $this->storages->aStorageTypes;
        $this->aData['oDistributor'] = array();

        if($this->aData['oUser']->Type == 1){
            $this->aData['oDistributor'] = $this->users->GetDistributorById($this->aData['oUser']->Id);
            $this->aData['oDistributorStorage'] = $this->storages->GetStorageById($this->aData['oDistributor']->StorageId);
        }

        $this->CheckUser(true);
    }
}