<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>member/distribution">Дистрибуция</a>
        <a class="nav_link" href="<?= base_url();?>member/supply">Зареждане</a>
        <a class="nav_link" href="<?= base_url();?>member/sales">Продажба</a>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
        <?php if($oUser->Type > 1) : ?>
            <a class="nav_link active logout" href="<?= base_url();?>admin/">Администрация</a>
        <?php endif; ?>
    </div>
    <div class="content">
        <div class="admin_options">
            <div class="option"><a class="section" section="users_content" href="#">Потребители</a></div>
            <div class="option"><a class="section" section="storages_content" href="#">Хранилища</a></div>
            <div class="option"><a class="section" section="product_types_content" href="#">Типове изделия</a></div>
            <div class="option"><a class="section" section="products_content" href="#">Произведени изделия</a></div>
            <div class="option"><a class="section" section="sales_content" href="#">Продажби</a></div>
            <div class="option"><a class="section" section="obsolete_content" href="#">Бракувани</a></div>
            <div class="option"><a class="section" section="garbage_content" href="#">Хронология</a></div>
        </div>

        <!--users-->
        <div class="users_content">
            <div class="list" style="<?= is_array($aUsers) && !empty($aUsers) ? '' : 'display:none;' ;?>">
                <?php $iCounter = 0; ?>
                <?php foreach($aUsers as $oUserData) : ?>
                    <div class="user_container container" user-id="<?=$oUserData->Id?>" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                        <div class="column first_column"><?= $oUserData->FirstName?></div>
                        <div class="column"><?=$oUserData->LastName?></div>
                        <div class="manage_users last_column">
                            <a href="#" class="edit_user"><i class="fa fa-pencil"></i></a>
                            <?php if($oUserData->Id != $oUser->Id) : ?>
                                <a href="#" class="delete_user"><i class="fa fa-times"></i></a>
                            <?php else: ?>
                                <i style="color:grey" class="fa fa-times"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $iCounter++; ?>
                <?php endforeach; ?>
                <?= $sUsersPagination ?>
            </div>
            <div class="edit_user_form form">
                <form method="post" action="">
                    <input type="hidden" name="UserId" value="">
                    <input type="text" name="FirstName" placeholder="Първо име" autocomplete="off"/>
                    <input type="text" name="LastName" placeholder="Фамилия" autocomplete="off"/>
                    <input type="text" name="LoginName" placeholder="Потребителско име" autocomplete="off"/>
                    <select name="Type">
                    </select>
                    <button type="submit">Запази</button>
                    <div class="vending_machines_list">
                        <?php if(is_array($aVendingMachines) && !empty($aVendingMachines)) : ?>
                            <?php foreach($aVendingMachines as $oVendingMachine){
                                echo "<div><input type='checkbox' name='vending_machine[]' value='{$oVendingMachine->Id}'>{$oVendingMachine->Name} {$oVendingMachine->Address}</div>";
                            }?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!--storage-->
        <div class="storages_content">
            <a class="add_storage" href="#">Добави хранилище</a>
            <div class="list" <?= is_array($aStorages) && !empty($aStorages) ? '' : 'style="display:none;"';?> >
                <?php $iCounter = 0; ?>
                <?php foreach($aStorages as $oStorage) :?>
                    <div class="storage_container container" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                        <div class="column first_column"><a href="#" class="storage_availability" storage-id="<?=$oStorage->Id?>"><?= $oStorage->Name?></a></div>
<!--                        <div class="manage_storage last_column">-->
<!--                            <a href="#" class="edit_pt"><i class="fa fa-pencil"></i></a>-->
<!--                            <a href="#" class="delete_pt"><i class="fa fa-times"></i></a>-->
<!--                        </div>-->
                        <div class="column last_column"><?=$oStorage->Address?></div>
                    </div>
                    <?php $iCounter++; ?>
                <?php endforeach; ?>
                <?= $sStoragesPagination ?>
            </div>
            <div class="add_storage_form form">
                <form method="post" action="">
                    <input type="text" name="Name" placeholder="Наименование" autocomplete="off" />
                    <input type="text" name="Address" placeholder="Адрес" autocomplete="off" />
                    <select name="Type">
                        <option value="0">Вид хранилище</option>
                        <?php foreach($aStorageTypes as $iKey => $sStorageType){
                            if($sStorageType != 'Дистрибутор'){
                                echo "<option value='{$iKey}'>{$sStorageType}</option>";
                            }
                        };?>
                    </select>
                    <button type="submit">Добави хранилище</button>
                </form>
            </div>
        </div>

        <!--product types-->
        <div class="product_types_content">
            <a class="add_product_type" href="#">Добави нов тип изделие</a>
            <div class="list" style="display:<?= is_array($aProductTypes) && !empty($aProductTypes) ? 'block' : 'none' ;?>;">
                <?php $iCounter = 0; ?>
                <?php foreach ($aProductTypes as $oProductType) : ?>
                    <div class="product_type_container container" product-id="<?=$oProductType->Id;?>" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                        <div class="column first_column"><?= $oProductType->Name?></div>
                        <div class="manage_product_types last_column">
                            <a href="#" class="edit_pt"><i class="fa fa-pencil"></i></a>
                            <a href="#" class="delete_pt"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                    <?php $iCounter++; ?>
                <?php endforeach; ?>
                <?= $sProductTypesPagination ?>
            </div>
            <div class="add_product_type_form form">
                <? if(is_array($aProductCategories) && !empty($aProductCategories)) : ?>
                    <form method="post" action="">
                        <input type="text" name="Name" placeholder="Име" autocomplete="off" />
                        <select name="Category">
                            <option value="0">Категория</option>
                            <?php foreach($aProductCategories as $iKey => $sCategory){
                                echo "<option value='{$iKey}'>{$sCategory}</option>";};
                            ?>
                        </select>
                        <button type="submit">Добави тип изделие</button>
                    </form>
                <? endif; ?>
            </div>
            <div class="edit_product_type_form form">
                <form method="post" action="">
                    <input type="hidden" name="Id" value="" />
                    <input type="text" name="Name" placeholder="Име" autocomplete="off" />
                    <select name="Category">
                    </select>
                    <button type="submit">Запази</button>
                </form>
            </div>
        </div>

        <!--products-->
        <div class="products_content">
            <div class="list" style="<?= is_array($aProducts) && !empty($aProducts) ? '' : 'display:none;' ?>;">
                <?php $iCounter = 0; ?>
                <?php foreach ($aProducts as $oProduct) : ?>
                    <div class="product_container container" product-id="<?=$oProduct->Id;?>" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                        <div class="column first_column"><a class="show_product_details" href="#"><?= $oProduct->Type->Name?></a></div>
                        <div class="column last_column"><?= $oProduct->ExpirationDate?></div>
                    </div>
                    <?php $iCounter++; ?>
                <?php endforeach; ?>
                <?= $sProductsPagination ?>
            </div>
            <div class="edit_product_form form">
                <form method="post" action="">
                    <input type="hidden" name="Id" value="" />
                    <div class="fake_input_field" name="Name"></div>
                    <div class="fake_input_field" name="Price"></div>
                    <input type="text" name="Value" placeholder="Себестойност" value="" autocomplete="off" />
                    <button type="submit">Запази</button>
                </form>
            </div>
        </div>

        <div class="sales_content">
            <div class="filter">
                <select name="User">
                    <option value="0">Всички потребители</option>
                    <?php if(is_array($aDistributors) && !empty($aDistributors)){
                        foreach($aDistributors as $oDistributor){
                            echo "<option value='{$oDistributor->Id}'>{$oDistributor->FirstName} {$oDistributor->LastName}</option>";
                        }
                    };?>
                </select>
                <select name="Storage">
                    <option value="0">Всички вендинг автомати</option>
                    <?php if(is_array($aVendingMachines) && !empty($aVendingMachines)){
                        foreach($aVendingMachines as $oVendingMachine){
                            echo "<option value='{$oVendingMachine->Id}'>{$oVendingMachine->Name} {$oVendingMachine->Address}</option>";
                        }
                    };?>
                </select>
                <select name="Period" style="display: none;">
                    <option value="">Неограничен период</option>
                </select>
            </div>
            <div class="sales_results">
                <div class="labels">
                    <p><span>Приходи: </span><span id="income"><?= isset($aSales['Income'])? $aSales['Income'] : '0.00'; ?></span> лв.</p>
                    <p><span>Разходи: </span><span id="expense"><?= isset($aSales['Expense'])? $aSales['Expense'] : '0.00'; ?></span> лв.</p>
                    <p><span>Печалба: </span><span id="profit"><?= isset($aSales['Profit'])? $aSales['Profit'] : '0.00'; ?></span> лв.</p>
                </div>
            </div>
        </div>

        <div class="warning">
        </div>
    </div>
</div>