<div class="storages_content">
    <a class="add_storage" href="#">Добави склад</a>
    <div class="list" <?= is_array($aStorages) && !empty($aStorages) ? '' : 'style="display:none;"';?> >
        <?php $iCounter = 0; ?>
        <?php foreach($aStorages as $oStorage) :?>
            <div class="storage_container container" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                <div class="column first_column"><a href="#" class="storage_availability" storage-id="<?=$oStorage->Id?>"><?= $oStorage->Name?></a></div>
                <div class="manage_storage last_column">
                    <a href="#" class="edit_pt"><i class="fa fa-pencil"></i></a>
                    <a href="#" class="delete_pt"><i class="fa fa-times"></i></a>
                </div>
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
                <option value="0">Вид склад</option>
                <?php foreach($aStorageTypes as $iKey => $sStorageType){
                    if($sStorageType != 'Дистрибутор'){
                        echo "<option value='{$iKey}'>{$sStorageType}</option>";
                    }
                };?>
            </select>
            <button type="submit">Добави склад</button>
        </form>
    </div>
</div>
<div class="directions"><a href="<?= base_url();?>admin/manage"">Обратно</a></div>
