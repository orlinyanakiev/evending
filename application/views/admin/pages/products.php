<div class="products_content">
    <div class="title">Произведени изделия</div>
    <div class="list" style="<?= is_array($aProducts) && !empty($aProducts) ? '' : 'display:none;' ?>;">
        <?php $iCounter = 0; ?>
        <?php foreach ($aProducts as $oProduct) : ?>
            <div class="product_container container" product-id="<?=$oProduct->Id;?>" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                <div class="column first_column"><a class="show_product_details" href="#"><?= $oProduct->Type->Name?></a></div>
                <div class="column last_column"><?= $oProduct->ExpirationDate?></div>
            </div>
            <?php $iCounter++; ?>
        <?php endforeach; ?>
        <?= $sProductsPagination ?>
    </div>
    <div class="edit_product_form form">
        <form method="post" action="">
            <input type="hidden" name="Id" value="" />
            <div class="fake_input_field" name="Name"></div>
            <input type="text" name="Price" placeholder="Стойност" value="" autocomplete="off" />
            <input type="text" name="Value" placeholder="Себестойност" value="" autocomplete="off" />
            <button type="submit">Запази</button>
        </form>
    </div>
</div>
<div class="directions"><a href="<?= base_url();?>admin/manage"">Обратно</a></div>
