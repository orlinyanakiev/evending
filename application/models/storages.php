<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Storages extends CI_Model
{
    private $sTable = 'storages';

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

        return $this->db->insert($this->sTable,$aInsertData);
    }

    public function GetAllStorages($iLimit = 10, $iOffest = 0)
    {
        $this->db->where('Active','1');
        return $this->db->get($this->sTable,$iLimit,$iOffest)->result();
    }
}