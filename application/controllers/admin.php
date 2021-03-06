<?php

require_once (APPPATH . 'core/My_AdminController.php');

class admin extends My_AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //check user type
        $this->Manage();
    }

    public function Manage()
    {
        $this->aData['sTitle'] = 'Администрация';

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/home',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function MainStorage()
    {
        $aProductsData = $this->products->ListProducts();
        $aProductCategories = $this->products->ListProductCategories();

        $this->aData['sTitle'] = 'Наличност';
        $this->aData['aProductCategories'] = $aProductCategories['aProductCategories'];
        $this->aData['aMainStorageProducts'] = $aProductsData['aProducts'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/storage',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function SupplyForm()
    {
        $aProductCategories = $this->products->ListProductCategories();

        $this->aData['sTitle'] = 'Зареждане';
        $this->aData['aProductCategories'] = $aProductCategories['aProductCategories'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/storagesupply',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function MainStorageSupply()
    {
        if(is_array($_POST) && !empty($_POST)){
            $bMainStorageSupply = $this->storages->MainStorageSupply($_POST);

            if($bMainStorageSupply){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::MAIN_STORAGE_SUPPLY, $_POST);
            }

            echo json_encode(array('success' => $bMainStorageSupply));
        }
    }

    public function Distributors()
    {
        $aDistributorsData = $this->users->ListUsers(1, 0, '1');
        $aVendingMachinesData = $this->storages->ListStorages(1, 0, 3);

        $this->aData['sTitle'] = 'Дистрибутори';
        $this->aData['aDistributors'] = $aDistributorsData['aUsers'];
        $this->aData['aVendingMachines'] = $aVendingMachinesData['aStorages'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/distributors',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function Vending()
    {
        $aVendingMachinesData = $this->storages->ListStorages(1, 0, 3);

        $this->aData['sTitle'] = 'Вендинг машини';
        $this->aData['aVendingMachines'] = $aVendingMachinesData['aStorages'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/vending',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function AddVendingMachine()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aVendingMachineData = $_POST;
            $bResult = $this->storages->AddVendingMachine($aVendingMachineData);

            if($bResult){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::ADD_STORAGE, $_POST);
            }

            echo json_encode(array('success' => $bResult));
        }
    }

    public function Categories()
    {
        $aProductCategories = $this->products->ListProductCategories();

        $this->aData['sTitle'] = 'Категории стоки';
        $this->aData['aProductCategories'] = $aProductCategories['aProductCategories'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/categories',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function AddProductCategory()
    {
        if(is_array($_POST) && !empty($_POST)){
            $aProductCategoryData = $_POST;
            $bResult = $this->products->AddProductCategory($aProductCategoryData);

            if($bResult){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::ADD_PRODUCT_CATEGORY, $_POST);
            }

            echo json_encode(array('success' => $bResult));
        }
    }

    //Users
    public function Users()
    {
        $aStoragesData = $this->storages->ListStorages(1, $this->storages->getStorageLimit());
        $aVendingMachinesData = $this->storages->ListStorages( 1 , 0 , '3');
        $aUsersData = $this->users->ListUsers(1, $this->users->getLimit());

        $this->aData['sTitle'] = 'Потребители';
        $this->aData['aUsers'] = $aUsersData['aUsers'];
        $this->aData['aVendingMachines'] = $aVendingMachinesData['aStorages'];
        $this->aData['sUsersPagination'] = $aUsersData['sPagination'];
        $this->aData['aStorages'] = $aStoragesData['aStorages'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/users',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function ChangeUserType()
    {
        $bResult = false;
        $bChangeUserType = false;
        $bIsDistributor = false;

        if(is_array($_POST) && !empty($_POST)){
            $iUserId = intval($_POST['iUserId']);
            $iType = $_POST['iType'];

            //check if distributor exists
            $oDistributor = $this->users->GetDistributorById($iUserId);
            $bIsDistributor = is_object($oDistributor);

            if($iType == 1 && !$bIsDistributor) {
                $iStorageId = $this->storages->AddDistributorStorage($iUserId);

                $aDistributorData = array(
                    'Id' => $iUserId,
                    'StorageId' => $iStorageId,
                    'Storages' => '',
                );

                $bResult = $this->users->AddDistributor($aDistributorData);
            } elseif ($iType == 1 && $bIsDistributor) {
                $aStorageData = array(
                    'IsDeleted' => '0',
                );

                $bResult = $this->storages->EditStorage($oDistributor->StorageId,$aStorageData);
            } elseif ($iType != 1 && $bIsDistributor) {
                $bResult = $this->storages->DeleteStorage($oDistributor->StorageId);
            }

            $bChangeUserType = $this->users->ChangeUserType($iUserId, $iType);
        }

        if($bIsDistributor){
            if($bResult && $bChangeUserType){
                $bResult = true;
            } else {
                $bResult = false;
            }
        } else {
            $bResult = $bChangeUserType;
        }

        if($bResult){
            $this->events->RegisterEvent($this->aData['oUser'], \Events::CHANGE_USER_TYPE, $_POST);
        }

        echo json_encode(array('success' => $bResult));
    }

//    public function EditUser()
//    {
//        $bResult = false;
//        $bEditUser = false;
//        $bIsDistributor = false;
//
//        if(is_array($_POST) && !empty($_POST)){
//            $iUserId = $_POST['UserId'];
//
//            //check if distributor exists
//            $oDistributor = $this->users->GetDistributorById($iUserId);
//            $bIsDistributor = is_object($oDistributor);
//
//            if($_POST['Type'] == 1 && !$bIsDistributor) {
//                $aStorageData = array(
//                    'Name' => $_POST['FirstName'].' '.$_POST['LastName'],
//                    'Address' => '',
//                    'Type' => '2',
//                );
//
//                $iStorageId = $this->storages->AddDistributorStorage($aStorageData);
//
//                $aDistributorData = array(
//                    'Id' => $_POST['UserId'],
//                    'StorageId' => $iStorageId,
//                    'Storages' => array_key_exists('vending_machine',$_POST) ? json_encode($_POST['vending_machine']) : '',
//                );
//
//                $bResult = $this->users->AddDistributor($aDistributorData);
//            } elseif ($_POST['Type'] == 1 && $bIsDistributor) {
//                $aStorageData = array(
//                    'Active' => '1',
//                );
//
//                $aDistributorData = array('Storages' => array_key_exists('vending_machine',$_POST) ? json_encode($_POST['vending_machine']) : '');
//
//                $this->users->EditDistributor($_POST['UserId'],$aDistributorData);
//                $bResult = $this->storages->EditStorage($oDistributor->StorageId,$aStorageData);
//            } elseif ($_POST['Type'] != 1 && $bIsDistributor) {
//                $bResult = $this->storages->DeleteStorage($oDistributor->StorageId);
//            }
//
//            $bEditUser = $this->users->EditUser($_POST);
//        }
//        if($bIsDistributor){
//            if($bResult && $bEditUser){
//                $bResult = true;
//            } else {
//                $bResult = false;
//            }
//        } else {
//            $bResult = $bEditUser;
//        }
//
//        if($bResult){
//            $this->events->RegisterEvent($this->aData['oUser'], \Events::EDIT_USER, $_POST);
//        }
//
//        echo json_encode(array('success' => $bResult));
//    }
//
//    public function DeleteUser()
//    {
//        if(is_array($_POST) && !empty($_POST) && isset($_POST['iUserId'])){
//            $iUserId = (int) $_POST['iUserId'];
//            $oUser = $this->users->GetUser($iUserId);
//
//            if($oUser->Type == 1){
//                $oDistributor = $this->users->GetDistributorById($oUser->Id);
//                $this->storages->DeleteStorage($oDistributor->StorageId);
//            }
//
//            $bResult = $this->users->deleteUserById($iUserId);
//
//            if($bResult){
//                $this->events->RegisterEvent($this->aData['oUser'], \Events::DELETE_USER, $_POST);
//            }
//
//            echo json_encode(array('success' => $bResult));
//        }
//    }
//
//    public function GetUser()
//    {
//        if(is_array($_POST) && !empty($_POST) && isset($_POST['iUserId'])){
//            $iUserId = (int) $_POST['iUserId'];
//
//            $oUser = $this->users->GetUser($iUserId);
//            if(is_object($oUser)){
//                echo json_encode(array('success' => true, 'oUser' => $oUser, 'aUserTypes' => $this->aData['aUserTypes']));
//                return;
//            }
//            echo json_encode(array('success' => false));
//            return;
//        }
//    }
//
//    public function UsersPagination()
//    {
//        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
//            $iPageId = intval($_POST['iPageId']);
//
//            $aUsersData = $this->users->ListUsers($iPageId, $this->users->getLimit());
//
//            $aUsers = $aUsersData['aUsers'];
//            $sPagination = $aUsersData['sPagination'];
//
//            echo json_encode(array('success' => true, 'oUser' => $this->aData['oUser'], 'aUsers' => $aUsers, 'sPagination' => $sPagination));
//            return;
//        }
//        echo json_encode(array('success' => false));
//        return;
//    }

//    public function EditStorage()
//    {
//        if(is_array($_POST) && !empty($_POST) && array_key_exists('Id', $_POST)){
//            $iStorageId = intval($_POST['Id']);
//            $bEditStorage = $this->storages->EditStorage($iStorageId, $_POST);
//
//            echo json_encode(array('success' => $bEditStorage));
//        }
//    }
//
//    public function DeleteStorage($iStorageId)
//    {
//        $bDeleteStorage = $this->storages->DeleteStorage($iStorageId);
//        echo json_encode(array('success' => $bDeleteStorage));
//    }
//
//    public function GetStorageById($iStorageId)
//    {
//        $oStorage = $this->storages->GetStorageById($iStorageId);
//        if(is_object($oStorage)){
//            echo json_encode(array('success' => true, 'oStorage' => $oStorage));
//            return;
//        }
//
//        echo json_encode(array('success' => false));
//    }

    public function GetDistributorVendingMachines($iUserId)
    {
        $iUserId = intval($iUserId);
        if($iUserId != 0){
            $aDistributorStorages = $this->storages->GetDistributorVendingMachines($iUserId);
            if(is_array($aDistributorStorages)){

                echo json_encode(array('success' => true, 'aDistributorStorages' => $aDistributorStorages));
                return;
            } else {
                echo json_encode(array('success' => false));
                return;
            }
        } else {
            $aVendingMachinesData = $this->storages->ListStorages( 1 , 0 , '3');
            $aVendingMachines = $aVendingMachinesData['aStorages'];

            echo json_encode(array('success' => true, 'aDistributorStorages' => $aVendingMachines));
            return;
        }
    }

    public function StoragesPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);
            $iType = 0;

            if(array_key_exists('iType',$_POST)){
                $iType = intval($_POST['iType']);
            }

            $aStoragesData = $this->storages->ListStorages($iPageId, $this->storages->getStorageLimit(), $iType);

            echo json_encode(array('success' => true, 'aStorages' => $aStoragesData['aStorages'], 'sPagination' => $aStoragesData['sPagination']));
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    //Products
    public function Products()
    {
        $aProductsData = $this->products->ListProducts(1, $this->products->getLimit());

        $this->aData['sTitle'] = 'Изделия';
        $this->aData['aProducts'] = $aProductsData['aProducts'];
        $this->aData['sProductsPagination'] = $aProductsData['sPagination'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/products',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function ProductsPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);

            $aProductsData = $this->products->ListProducts($iPageId, $this->products->getLimit());

            $aProductsData['success'] = true;

            echo json_encode($aProductsData);
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    public function GetProductById()
    {
        if(is_array($_POST) && !empty($_POST) && array_key_exists('iProductId',$_POST)){
            $iProductId = intval($_POST['iProductId']);
            $oProduct = $this->products->GetProductById($iProductId);

            echo json_encode(array('success' => true, 'oProduct' => $oProduct));
            return;
        }

        echo json_encode(array('success' => false));
    }

    public function EditProduct()
    {
        if(is_array($_POST) && !empty($_POST)){
            $iProductId = intval($_POST['Id']);
            $bEditProduct = $this->products->EditProduct($iProductId, $_POST);

            if($bEditProduct){
                $this->events->RegisterEvent($this->aData['oUser'], \Events::EDIT_PRODUCT, $_POST);
            }

            echo json_encode(array('success' => $bEditProduct));
        }
    }

    //Product types
//    public function ProductCategories()
//    {
//        $aProductTypesData = $this->products->ListProductTypes(1, $this->products->getTypesLimit());
//
//        $this->aData['sTitle'] = 'Типове изделия';
//        $this->aData['aProductTypes'] = $aProductTypesData['aProductTypes'];
//        $this->aData['sProductTypesPagination'] = $aProductTypesData['sPagination'];
//
//        $this->load->view('admin/include/header',$this->aData);
//        $this->load->view('admin/pages/producttypes',$this->aData);
//        $this->load->view('admin/include/footer',$this->aData);
//    }

//    public function GetProductCategoryById()
//    {
//        if(is_array($_POST) && !empty($_POST) && array_key_exists('iProductCategoryId',$_POST)){
//            $iPTId = intval($_POST['iProductCategoryId']);
//            $oProductCategory = $this->products->GetProductCategoryById($iPTId);
//
//            echo json_encode(array('success' => true, 'oProductCategory' => $oProductCategory));
//            return;
//        }
//
//        echo json_encode(array('success' => false));
//    }

//    public function EditProductType()
//    {
//        $bResult = $this->products->EditProductType($_POST);
//
//        if($bResult){
//            $this->events->RegisterEvent($this->aData['oUser'], \Events::EDIT_PRODUCT_TYPE, $_POST);
//        }
//
//        echo json_encode(array('success' => $bResult));
//    }

//    public function DeleteProductType()
//    {
//        $iProductTypeId = $_POST['iProductTypeId'];
//        $bResult = $this->products->DeleteProductType($iProductTypeId);
//
//        if($bResult){
//            $this->events->RegisterEvent($this->aData['oUser'], \Events::DELETE_PRODUCT_TYPE, $_POST);
//        }
//
//        echo json_encode(array('success' => $bResult));
//    }

//    public function ProductTypesPagination()
//    {
//        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
//            $iPageId = intval($_POST['iPageId']);
//
//            $aProductTypesData = $this->products->ListProductTypes($iPageId, $this->products->getTypesLimit());
//
//            $aProductTypesData['success'] = true;
//
//            echo json_encode($aProductTypesData);
//            return;
//        }
//        echo json_encode(array('success' => false));
//        return;
//    }

    //Obsolete
    public function Obsolete()
    {
        $this->aData['sTitle'] = 'Бракувани';

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/obsolete',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    //Sales
    public function Sales()
    {
        $aVendingMachinesData = $this->storages->ListStorages( 1 , 0 , '3');
        $aDistributors = $this->users->ListUsers(1,0,'1');

        $this->aData['sTitle'] = 'Продажби';
        $this->aData['aDistributors'] = $aDistributors['aUsers'];
        $this->aData['aVendingMachines'] = $aVendingMachinesData['aStorages'];
        $this->aData['aSales'] = $this->storages->GetSales();

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/sales',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function GetSales()
    {
        $iUserId = intval($_POST['iUserId']);
        $iStorageId = intval($_POST['iStorageId']);
        $sPeriod = $_POST['sPeriod'];

        $aSalesData = $this->storages->GetSales($iUserId, $iStorageId, $sPeriod);
        echo json_encode(array('success' => true, 'aSalesData' => $aSalesData));
    }

    //events
    public function Events()
    {
        $this->aData['sTitle'] = 'Хронология';
        $aEventsData = $this->events->ListEvents(1,$this->events->GetEventsLimit());

        $this->aData['aEvents'] = $aEventsData['aEvents'];
        $this->aData['sPagination'] = $aEventsData['sPagination'];

        $this->load->view('admin/include/header',$this->aData);
        $this->load->view('admin/pages/events',$this->aData);
        $this->load->view('admin/include/footer',$this->aData);
    }

    public function EventsPagination()
    {
        if(is_array($_POST) && array_key_exists('iPageId',$_POST)){
            $iPageId = intval($_POST['iPageId']);

            $aEventsData = $this->events->ListEvents($iPageId, $this->events->getEventsLimit());

            $aEventsData['success'] = true;

            echo json_encode($aEventsData);
            return;
        }
        echo json_encode(array('success' => false));
        return;
    }

    public function GetEventPreview($iEventId)
    {
        $oEvent = $this->events->GetEventById($iEventId);

        if(is_object($oEvent)){
            $aEventDescription = json_decode($oEvent->Description, true);
            $oUser = $this->users->GetUser($oEvent->UserId);
            $sEventType = $this->aData['aEventTypes'][$oEvent->Type];

            $sEventPreview = '<div class="event_container container"><div class="first_column column">'.$oEvent->DateRegistered.'</div>
                    <div class="first_column column">'.$oUser->FirstName.' '.$oUser->LastName.'</div>
                    <div class="first_column column">'.$sEventType.'</div></div>';

            switch($oEvent->Type){
                case \Events::SUPPLY :
                    $oStorage = $this->storages->GetStorageById(intval($aEventDescription['Storage']));
                    $oProductType = $this->products->GetProductTypeById(intval($aEventDescription['ProductType']));
                    $iQuantity = intval($aEventDescription['Quantity']);
                    $sExpirationDate = $aEventDescription['ExpirationDate'];
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">На: '.$oStorage->Name.' '.$oStorage->Address.'</div></div>
                    <div class="event_container container"><div class="first_column column">Изделие: '.$oProductType->Name.' ('.$sExpirationDate.')</div></div>
                    <div class="event_container container"><div class="first_column column">Количество: '.$iQuantity.'</div>
                    <div class="first_column column">Цена: '.$aEventDescription['Price'].' лв.</div>
                    <div class="first_column column">Себестойност: '.$aEventDescription['Value'].' лв.</div></div>';
                    break;
                case \Events::DISTRIBUTE :
                    $oStorageOne = $this->storages->GetStorageById(intval($aEventDescription['Storage1']));
                    $oStorageTwo = $this->storages->GetStorageById(intval($aEventDescription['Storage2']));
                    $oProduct = $this->products->GetProductById(intval($aEventDescription['Product']));
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">От: '.$oStorageOne->Name.' '.$oStorageOne->Address.'</div>
                    <div class="first_column column">Към: '.$oStorageTwo->Name.' '.$oStorageTwo->Address.'</div></div>
                    <div class="event_container container"><div class="first_column column">Изделие: '.$oProduct->Type->Name.' ('.$oProduct->ExpirationDate.')</div>
                    <div class="first_column column">Количество: '.$aEventDescription['Quantity'].'</div></div>';
                    break;
                case \Events::OBSOLETE :
                    $oStorage = $this->storages->GetStorageById(intval($aEventDescription['Storage']));
                    $oProduct = $this->products->GetProductById(intval($aEventDescription['Product']));
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">От: '.$oStorage->Name.' '.$oStorage->Address.'</div></div>
                    <div class="event_container container"><div class="first_column column">Изделие: '.$oProduct->Type->Name.' ('.$oProduct->ExpirationDate.')</div>
                    <div class="first_column column">Количество: '.$aEventDescription['Quantity'].'</div></div>';
                    break;
                case \Events::SALE :
                    $oStorage = $this->storages->GetStorageById(intval($aEventDescription['Storage']));
                    $oProduct = $this->products->GetProductById(intval($aEventDescription['Product']));
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">От: '.$oStorage->Name.' '.$oStorage->Address.'</div></div>
                    <div class="event_container container"><div class="first_column column">Изделие: '.$oProduct->Type->Name.' ('.$oProduct->ExpirationDate.')</div>
                    <div class="first_column column">Количество: '.$aEventDescription['Quantity'].'</div></div>';
                    break;
                case \Events::INCOME_ACCOUNTING :
                    $oStorage = $this->storages->GetStorageById($aEventDescription['Storage']);
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">От: '.$oStorage->Name.' '.$oStorage->Address.'</div>
                    <div class="first_column column"> '.$aEventDescription['Value'].' лв.</div></div>';
                    break;
                case \Events::ADD_STORAGE :
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">'.$aEventDescription['Name'].' '.$aEventDescription['Address'].'</div>
                    <div class="first_column column">Тип: '.$this->aData['aStorageTypes'][$aEventDescription['Type']].'</div></div>';
                    if($aEventDescription['Type'] == 3){
                        $sEventPreview .= '<div class="event_container container"><div class="first_column column">Добавена налична сума: '.$aEventDescription['Cash'].' лв.</div></div>';
                    }
                    break;
                case \Events::ADD_PRODUCT_TYPE :
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">Име: '.$aEventDescription['Name'].'</div>
                    <div class="first_column column">Категория: '.$aEventDescription['Category'].'</div></div>';
                    break;
                case \Events::EDIT_USER :
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">Име: '.$aEventDescription['FirstName'].'</div>
                    <div class="first_column column">Фамилия: '.$aEventDescription['LastName'].'</div></div>
                    <div class="event_container container"><div class="first_column column">Потребителско име: '.$aEventDescription['LoginName'].'</div>
                    <div class="first_column column">Тип: '.$this->aData['aUserTypes'][$aEventDescription['Type']].'</div></div>';
                    if($aEventDescription['Type'] == 1){
                        $aStorages = $aEventDescription['vending_machine'];
                        if(is_array($aStorages) && !empty($aStorages)){
                            $sEventPreview .= '<div class="event_container container"><div class="first_column column">Възможност за работа с:</div></div>';
                            foreach ($aStorages as $iStorageId){
                                $oStorage = $this->storages->GetStorageById($iStorageId);
                                $sEventPreview .= '<div class="event_container container"><div class="first_column column">'.$oStorage->Name.' '.$oStorage->Address.'</div></div>';
                            }
                        }
                    }
                    break;
                case \Events::DELETE_USER :
                    $oUser = $this->users->GetUser($aEventDescription['iUserId']);
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">'.$oUser->FirstName.' '.$oUser->LastName.'</div></div> ';
                    break;
                case \Events::EDIT_PRODUCT :
                    $oProduct = $this->products->GetProductById($aEventDescription['Id']);
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">'.$oProduct->Type->Name.'</div></div>
                    <div class="event_container container"><div class="first_column column">Цена: '.$aEventDescription['Price'].'</div>
                    <div class="first_column column">Себестойност: '.$aEventDescription['Value'].'</div></div>';
                    break;
                case \Events::EDIT_PRODUCT_TYPE :
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">'.$aEventDescription['Name'].'</div></div>
                    <div class="event_container container"><div class="first_column column">Категория: '.$aEventDescription['Category'].'</div></div>';
                    break;
                case \Events::DELETE_PRODUCT_TYPE :
                    $oProductType = $this->products->GetProductTypeById($aEventDescription['iProductTypeId']);
                    $sEventPreview .= '<div class="event_container container"><div class="first_column column">'.$oProductType->Name.'</div></div>';
                    break;
            }

            $sEventPreview .= '<div class="directions"><a href="'.base_url().'admin/events">Обратно</a></div>';

            echo json_encode(array('success' => true, 'sEventPreview' => $sEventPreview));
            return;
        }

        echo json_encode(array('success' => false));
        return;
    }

}
