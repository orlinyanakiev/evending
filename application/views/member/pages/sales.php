<div class="page_wrapper start">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>member/distribution">Дистрибуция</a>
        <a class="nav_link" href="<?= base_url();?>member/supply">Зареждане</a>
        <a class="nav_link active" href="<?= base_url();?>member/sales">Продажба</a>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
        <?php if($oUser->Type > 1) : ?>
            <a class="nav_link logout" href="<?= base_url();?>admin/">Администрация</a>
        <?php endif; ?>
    </div>
    <div class="content">
        <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
            <div class="sales_form form">
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
                    <button type="submit">Продажба</button>
                </form>
            </div>
        <?php else: ?>
            <div class="no_storages">Няма въведени вендинг машини!</div>
        <?php endif; ?>
        <div class="warning">
        </div>
    </div>
</div>