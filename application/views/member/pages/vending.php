<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>member/homepage">Начало</a>
        <a class="nav_link active" href="<?= base_url();?>member/actions">Действия</a>
        <?php if($oUser->Type > 1) : ?>
            <a class="nav_link" href="<?= base_url();?>admin/">Управление</a>
        <?php endif; ?>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
    </div>
    <div class="content">
        <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
            <?php if(is_array($aDistributors) && !empty($aDistributors)) : ?>
                <div class="title">Вендинг</div>
                <div class="vending_machine_form form">
                    <form method="post" action="">
                        <select name="Distributor">
                            <option value="0" selected="selected">Дистрибутор</option>
                            <?php foreach($aDistributors as $oDist) : ?>
                                <option value="<?=$oDist->Id->Id?>"><?=$oDist->StorageId->Name?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="VendingMachine">
                            <option value="0" selected="selected">Вендинг машина</option>
                            <?php foreach($aStorages as $oStorage) : ?>
                                <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form_label">Зареждане</div>
                        <select name="DistributeProduct">
                            <option value="0">Изделие</option>
                        </select>
                        <input type="text" name="DistributeQuantity" placeholder="Количество" autocomplete="off" />
                        <div class="form_label">Бракуване</div>
                        <select name="ObsoleteProduct">
                            <option value="0">Изделие</option>
                        </select>
                        <input type="text" name="ObsoleteQuantity" placeholder="Количество" autocomplete="off" />
                        <div class="form_label">Отчитане на приходи</div>
                        <input type="text" name="VendingMachineCash" placeholder="Сума" autocomplete="off" />
                        <button type="submit">Изпълни</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="no_distributors">Няма активни дистрибутори!</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no_storages">Няма активни вендинг машини!</div>
        <?php endif; ?>
        <div class="warning">
        </div>
        <div class="directions">
            <a href="<?= base_url();?>member/actions">Обратно</a>
        </div>
    </div>
</div>