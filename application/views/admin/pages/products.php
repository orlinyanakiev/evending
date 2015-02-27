<div class="page_wrapper">
    <div class="nav">
        <a class="admin" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="admin" href="<?= base_url();?>admin/storages">Складове</a>
        <a class="admin active" href="<?= base_url();?>admin/products">Стоки</a>
        <a class="admin" href="<?= base_url();?>admin/info">Справки</a>
        <a class="logout" href="<?= base_url();?>member/">Обратно</a>
    </div>
    <div class="content">
        <? if(is_array($aProductTypes) && !empty($aProductTypes)) : ?>
            <a class="add_product" href="#">Добави нов продукт.</a>
            <div class="products_list">
                List
            </div>
            <div class="add_product_form form">
                <form method="post" action="">
                    <input type="text" name="Name" placeholder="Име" />
                    <button type="submit">Създрай продукт</button>
                </form>
            </div>
        <? else : ?>
            <div class="warning">
                <p>Няма въведени типове продукти!</p>
                Въведете от <a class="add_producttype" href="#">тук</a>.
            </div>
        <? endif; ?>
        <div class="add_producttype_form form">
            <? if(is_array($aProductCategories) && !empty($aProductCategories)) : ?>
                <form method="post" action="">
                    <input type="text" name="Name" placeholder="Име" />
                    <select name="Category">
                        <?php foreach($aProductCategories as $oCategory){
                            echo '<option value="'.$oCategory->Id.'">'.$oCategory->Category.'</option>';};
                        ?>
                    </select>
                    <button type="submit">Добави тип продукт</button>
                </form>
            <? endif; ?>
        </div>
    </div>
</div>