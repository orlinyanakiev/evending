<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>member/homepage">Начало</a>
        <a class="nav_link active" href="<?= base_url();?>member/actions">Действия</a>
        <?php if($oUser->Type > '1') : ?>
            <a class="nav_link" href="<?= base_url();?>admin/">Управление</a>
        <?php endif; ?>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
    </div>
    <div class="content">
        <div class="title">Дистрибуция</div>
        <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
            <?php if(is_array($aAdditionalStorages) && !empty($aAdditionalStorages)) : ?>
                <?php if(is_array($aProducts) && !empty($aProducts)) : ?>
                    <div class="distribution_form form">
                        <form method="post" action="">
                            <?php if(!is_object($oDistributor)) : ?>
                                <select name="Storage1">
                                    <?php foreach($aStorages as $oStorage) : ?>
                                        <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                                <input type="hidden" name="Storage1" value="<?= $oDistributor->StorageId; ?>" />
                                <div class="fake_input_field"><?= $oDistributorStorage->Name ?></div>
                            <?php endif; ?>
                            <select name="Storage2">
                                <option value="0">Към</option>
                                <?php foreach($aAdditionalStorages as $oAdditionalStorage) : ?>
                                    <option value="<?=$oAdditionalStorage->Id?>"><?=$oAdditionalStorage->Name?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="Product">
                                <option value="0">Изделие</option>
                                <?php foreach($aProducts as $oProduct):?>
                                    <option quantity="<?=$oProduct['iQuantity']?>" value="<?=$oProduct['oProduct']->Id?>"><?=$oProduct['oProduct']->Type->Name.' ('.$oProduct['oProduct']->ExpirationDate.')'?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="Quantity" placeholder="Количество" autocomplete="off" />
                            <button type="submit">Прехвърли</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="no_products">Складът е празен!</div>
                <?php endif; ?>
            <?php else: ?>
                <?php if(is_object($oDistributor)) : ?>
                    <div class="no_vendings">Нямате назначени вендинг машини!</div>
                <?php else: ?>
                    <div class="no_distributors">Няма въведени складове!</div>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="no_storages">Няма въведени складове!</div>
        <?php endif; ?>
        <div class="warning">
        </div>
        <div class="directions">
            <a href="<?= base_url();?>member/actions">Обратно</a>
        </div>
    </div>
</div>