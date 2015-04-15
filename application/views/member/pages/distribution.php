<div class="page_wrapper start">
    <?php if($oUser->Type != 0) : ?>
        <div class="nav">
            <a class="nav_link active" href="<?= base_url();?>member/distribution">Дистрибуция</a>
            <a class="nav_link" href="<?= base_url();?>member/supply">Зареждане</a>
            <a class="nav_link" href="<?= base_url();?>member/sales">Продажба</a>
            <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
            <?php if($oUser->Type > 1) : ?>
                <a class="nav_link logout" href="<?= base_url();?>admin/">Администрация</a>
            <?php endif; ?>
        </div>
        <div class="content">
            <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
                <div class="distribution_form form">
                    <form method="post" action="">
                        <?php if(!is_object($oDistributor)) : ?>
                            <select name="Storage1">
                                <option value="0" selected="selected">От</option>
                                <?php foreach($aStorages as $oStorage) : ?>
                                    <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <input type="hidden" name="Storage1" value="<?= $oDistributor->StorageId; ?>" />
                            <div class="fake_input_field">
                                От: <?= $oDistributorStorage->Name ?>
                            </div>
                        <?php endif; ?>
                        <select name="Storage2">
                            <option value="0" selected="selected">Към</option>
                            <?php if(is_object($oDistributor)) : ?>
                                <?php foreach ($aStorages as $oStorage) : ?>
                                    <?php if($oStorage->Id != $oDistributor->StorageId) : ?>
                                        <option value="<?= $oStorage->Id ?>"><?= $oStorage->Name ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif;?>
                        </select>
                        <select name="Product">
                            <option value="0" selected="selected">Изделие</option>
                            <?php foreach ($aStorageAvailability as $aStorageProduct): ?>
                                <option value="<?= $aStorageProduct['oData']->Id;?>" product-quantity="<?= $aStorageProduct['iQuantity']?>"><?= $aStorageProduct['oType']->Name.' ('.$aStorageProduct['oData']->ExpirationDate.')'?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="Quantity" placeholder="Количество" />
                        <button type="submit">Прехвърли</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="no_storages">Няма въведени хранилища!</div>
            <?php endif; ?>
            <div class="warning">
            </div>
        </div>
    <?php else : ?>
        <div class="warning">
            <p class="request_failure">Обърнете се към администратор за права!</p>
            <p><a href="<?= base_url();?>member/logout">Изход</a></p>
        </div>
    <?php endif; ?>
</div>