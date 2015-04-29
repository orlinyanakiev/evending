<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>member/homepage">Начало</a>
        <a class="nav_link active" href="<?= base_url();?>member/actions">Действия</a>
        <a class="nav_link" href="<?= base_url();?>admin/">Управление</a>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
    </div>
    <div class="content">
        <div class="title">Дистрибуция</div>
        <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
            <div class="distribution_form form">
                <form method="post" action="">
                    <select name="Storage1">
                        <option value="0" selected="selected">От</option>
                        <?php foreach($aStorages as $oStorage) : ?>
                            <?php if($oStorage->Type == 1) : ?>
                                <option type="<?=$oStorage->Type?>" value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <select name="Storage2">
                        <option value="0" selected="selected">Към</option>
                    </select>
                    <select name="Product">
                        <option value="0" selected="selected">Изделие</option>
                    </select>
                    <input type="text" name="Quantity" placeholder="Количество" autocomplete="off" />
                    <button type="submit">Прехвърли</button>
                </form>
            </div>
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