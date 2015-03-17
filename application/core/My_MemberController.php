<?php

require_once (APPPATH . 'core/My_BaseController.php');

class My_MemberController extends My_BaseController
{
    private $aProductCategories = array(
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
    );

    private $aStorageTypes = array(
        '1' => 'Склад',
        '2' => 'Автомобил',
        '3' => 'Вендинг машина',
    );

    public function __construct()
        {
            parent::__construct();

            $this->aData['aProductCategories'] = $this->aProductCategories;
            $this->aData['aStorageTypes'] = $this->aStorageTypes;

            $this->load->model('storages');
            $this->load->model('products');
            $this->CheckUser(true);
        }
}