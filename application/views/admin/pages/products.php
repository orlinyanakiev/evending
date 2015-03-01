<div class="page_wrapper">
    <div class="nav">
        <a class="admin" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="admin" href="<?= base_url();?>admin/storages">Складове</a>
        <a class="admin active" href="<?= base_url();?>admin/products">Изделия</a>
        <a class="admin" href="<?= base_url();?>admin/info">Справки</a>
        <a class="logout" href="<?= base_url();?>member/">Обратно</a>
    </div>
    <div class="content">
        <a class="add_product_type" href="#">Добави нов тип изделие</a>
        <div class="list" style="display:<?= is_array($aProductTypes) && !empty($aProductTypes) ? 'block' : 'none' ;?>;">
            <?php foreach ($aProductTypes as $oProductType) : ?>
                <div class="product_type_container">
                    <?= $oProductType->Name.' '.$oProductType->Price; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="add_product_type_form form">
            <? if(is_array($aProductCategories) && !empty($aProductCategories)) : ?>
                <form method="post" action="">
                    <input type="text" name="Name" placeholder="Име" />
                    <select name="Category">
                        <?php foreach($aProductCategories as $oCategory){
                            echo '<option value="'.$oCategory->Id.'">'.$oCategory->Category.'</option>';};
                        ?>
                    </select>
                    <input type="text" name="Price" placeholder="Цена" />
                    <input type="text" name="ExpirationTime" placeholder="Време на валидност" />
                    <button type="submit">Добави тип изделие</button>
                </form>
            <? else: ?>
                <div>Няма въведени категории, свържете се с администратор!</div>
            <? endif; ?>
        </div>
        <div class="warning">

        </div>
    </div>
</div>