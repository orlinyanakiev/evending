<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="nav_link" href="<?= base_url();?>admin/storages">Складове</a>
        <a class="nav_link" href="<?= base_url();?>admin/products">Изделия</a>
        <a class="nav_link active" href="<?= base_url();?>admin/supply">Зареждане</a>
        <a class="logout" href="<?= base_url();?>member/">Обратно</a>
    </div>
    <div class="content">
        <div class="supply_form form">
            <form>
                <select name="Storage">
                    <option value="0">Склад</option>
                    <?php foreach($aStorages as $oStorage) : ?>
                        <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                    <?php endforeach; ?>
                </select>
                <select name="ProductType">
                    <option value="0">Тип Изделие</option>
                    <?php foreach($aProductTypes as $oProductType) : ?>
                        <option value="<?=$oProductType->Id?>"><?=$oProductType->Name?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="Quantity" placeholder="Количество">
                <button type="submit">Добави</button>
            </form>
        </div>
        <div class="warning">

        </div>
    </div>
</div>