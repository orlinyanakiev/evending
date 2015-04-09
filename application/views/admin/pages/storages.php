<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="nav_link active" href="<?= base_url();?>admin/storages">Хранилища</a>
        <a class="nav_link" href="<?= base_url();?>admin/products">Изделия</a>
        <a class="logout" href="<?= base_url();?>member/">Обратно</a>
    </div>
    <div class="content">
        <a class="add_storage" href="#">Добави хранилище</a>
        <div class="list" style="display:<?= is_array($aStorages) && !empty($aStorages) ? 'block' : 'none';?>;">
            <div class="container">
                <div class="column first_column">Наименование</div>
                <div class="column last_column">Адрес</div>
            </div>
            <?php $iCounter = 0; ?>
            <?php foreach($aStorages as $oStorage) :?>
                <?php $iCounter++; ?>
                <div class="storage_container container" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                    <div class="column first_column"><a href="#" class="storage_availability" storage-id="<?=$oStorage->Id?>"><?= $oStorage->Name?></a></div>
                    <div class="column last_column"><?=$oStorage->Address?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="add_storage_form form">
            <form method="post" action="">
                <input type="text" name="Name" placeholder="Наименование" />
                <input type="text" name="Address" placeholder="Адрес" />
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
        <div class="warning">

        </div>
    </div>
</div>