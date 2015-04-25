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
            <div class="obsolete_form form">
                <form method="post" action="">
                    <select name="Storage">
                        <option value="0" selected="selected">От</option>
                        <?php foreach($aStorages as $oStorage) : ?>
                            <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="Product">
                        <option value="0" selected="selected">Изделие</option>
                    </select>
                    <input type="text" name="Quantity" placeholder="Количество" autocomplete="off" />
                    <button type="submit">Бракувай</button>
                </form>
            </div>
        <?php else: ?>
            <div class="no_storages">Няма въведени вендинг машини!</div>
        <?php endif; ?>
        <div class="warning">
        </div>
        <div class="directions">
            <a href="<?= base_url();?>member/actions">Обратно</a>
        </div>
    </div>
</div>