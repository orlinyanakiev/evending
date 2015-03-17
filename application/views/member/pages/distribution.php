<div class="page_wrapper start">
    <div class="nav">
        <a class="nav_link active" href="<?= base_url();?>member/distribution">Дистрибуция</a>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
        <a class="nav_link logout" href="<?= base_url();?>admin/users">Администрация</a>
    </div>
    <div class="content">
        <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
            <?php if(is_array($aProducts) && !empty($aProducts)) : ?>
                <div class="distribution_form form">
                    <form method="post" action="">
                        <select name="Storage1">
                            <option value="0">От: </option>
                            <?php foreach($aStorages as $oStorage) : ?>
                                <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="Storage2">
                            <option value="0">Към: </option>
                        </select>
                        <select name="Product">
                            <option value="0">Изделие: </option>
                        </select>
                        <input type="text" name="Quantity" placeholder="Количество" />
                        <button type="submit">Прехвърли</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="no_products">Добавете продукт през "Администрация"</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no_storages">Добавете хранилище през "Администрация"</div>
        <?php endif; ?>
        <div class="warning">
        </div>
    </div>
</div>