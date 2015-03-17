<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="nav_link" href="<?= base_url();?>admin/storages">Хранилища</a>
        <a class="nav_link active" href="<?= base_url();?>admin/products">Изделия</a>
        <a class="nav_link" href="<?= base_url();?>admin/supply">Зареждане</a>
        <a class="logout" href="<?= base_url();?>member/">Обратно</a>
    </div>
    <div class="content">
        <a class="add_product_type" href="#">Добави нов тип изделие</a>
        <div class="list" style="display:<?= is_array($aProductTypes) && !empty($aProductTypes) ? 'block' : 'none' ;?>;">
            <div class="container">
                <div class="column first_column">Наименование</div>
                <div class="column last_column">Цена в лева</div>
            </div>
            <?php $iCounter = 0; ?>
            <?php foreach ($aProductTypes as $oProductType) : ?>
                <?php $iCounter++; ?>
                <div class="product_type_container container" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                    <div class="column first_column"><?= $oProductType->Name?></div>
                    <div class="column last_column"><?=$oProductType->Price?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="add_product_type_form form">
            <? if(is_array($aProductCategories) && !empty($aProductCategories)) : ?>
                <form method="post" action="">
                    <input type="text" name="Name" placeholder="Име" />
                    <select name="Category">
                        <option value="0">Категория</option>
                        <?php foreach($aProductCategories as $iKey => $sCategory){
                            echo "<option value='{$iKey}'>{$sCategory}</option>";};
                        ?>
                    </select>
                    <input type="text" name="Price" placeholder="Цена" />
                    <input type="text" name="ExpirationTime" placeholder="Време на валидност" />
                    <button type="submit">Добави тип изделие</button>
                </form>
            <? endif; ?>
        </div>
        <div class="warning">

        </div>
    </div>
</div>