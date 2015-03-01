<div class="page_wrapper">
    <div class="nav">
        <a class="admin" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="admin active" href="<?= base_url();?>admin/storages">Складове</a>
        <a class="admin" href="<?= base_url();?>admin/products">Изделия</a>
        <a class="admin" href="<?= base_url();?>admin/info">Справки</a>
        <a class="logout" href="<?= base_url();?>member/">Обратно</a>
    </div>
    <div class="content">
        <a class="add_storage" href="#">Добави нов склад</a>
        <div class="list" style="display:<?= is_array($aStorages) && !empty($aStorages) ? 'block' : 'none';?>;">
            <?php foreach($aStorages as $aStorage) :?>
                <div class="storage_container"><?= $aStorage->Name.' '.$aStorage->Address;?></div>
            <?php endforeach; ?>
        </div>
        <div class="add_storage_form form">
            <form method="post" action="">
                <input type="text" name="Name" placeholder="Наименование" />
                <input type="text" name="Address" placeholder="Адрес" />
                <select name="Type">
                    <option value="0">Вид склад</option>
                    <option value="1">Едно</option>
                    <option value="2">Две</option>
                </select>
                <button type="submit">Добави склад</button>
            </form>
        </div>
        <div class="warning">

        </div>
    </div>
</div>