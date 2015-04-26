<div class="product_types_content">
    <div class="title">Типове изделия</div>
    <a class="add_product_type" href="#">Добави нов тип изделие</a>
    <div class="list" style="display:<?= is_array($aProductTypes) && !empty($aProductTypes) ? 'block' : 'none' ;?>;">
        <?php $iCounter = 0; ?>
        <?php foreach ($aProductTypes as $oProductType) : ?>
            <div class="product_type_container container" product-id="<?=$oProductType->Id;?>" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                <div class="column first_column"><?= $oProductType->Name?></div>
                <div class="manage_product_types last_column">
                    <a href="#" class="edit_pt"><i class="fa fa-pencil"></i></a>
                    <a href="#" class="delete_pt"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <?php $iCounter++; ?>
        <?php endforeach; ?>
        <?= $sProductTypesPagination ?>
    </div>
    <div class="add_product_type_form form">
        <? if(is_array($aProductCategories) && !empty($aProductCategories)) : ?>
            <form method="post" action="">
                <input type="text" name="Name" placeholder="Име" autocomplete="off" />
                <select name="Category">
                    <option value="0">Категория</option>
                    <?php foreach($aProductCategories as $iKey => $sCategory){
                        echo "<option value='{$iKey}'>{$sCategory}</option>";};
                    ?>
                </select>
                <button type="submit">Добави тип изделие</button>
            </form>
        <? endif; ?>
    </div>
    <div class="edit_product_type_form form">
        <form method="post" action="">
            <input type="hidden" name="Id" value="" />
            <input type="text" name="Name" placeholder="Име" autocomplete="off" />
            <select name="Category">
            </select>
            <button type="submit">Запази</button>
        </form>
    </div>
</div>
<div class="directions"><a href="<?= base_url();?>admin/manage"">Обратно</a></div>
