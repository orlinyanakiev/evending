<ul class="nav nav-pills nav-stacked nav-panel">
    <li><a href="<?=base_url()?>admin/">Централен склад</a></li>
    <li><a href="<?=base_url()?>admin/distributors">Дистрибутори</a></li>
    <li><a href="<?=base_url()?>admin/vending">Вендинг машини</a></li>
    <li class="active"><a href="<?=base_url()?>admin/categories">Категории стоки</a></li>
    <li><a href="<?=base_url()?>admin/users">Потребители</a></li>
</ul>
</div>

<div class="container col-sm-10 content">
    <div class="product_categories_list col-sm-6">
        <?php if(is_array($aProductCategories) && !empty($aProductCategories)) : ?>
            <table class="table table-condensed table-hover">
                <thead><tr><th>Баркод</th><th>Име</th></tr></thead>
                <tbody>
                <?php foreach($aProductCategories as $oProductCategory){
                    echo "<tr><td>{$oProductCategory->Barcode}</td><td>{$oProductCategory->Name}</td></tr>";
                } ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="col-sm-8">
                <div class="alert alert-warning" role="alert">Няма въведени категории стоки!</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-sm-12">
        <a class="btn add_product_category" href="#" role="button"><i class="glyphicon glyphicon-plus-sign"></i> Добави категория</a>
    </div>

    <div class="add_product_category_form col-sm-8" style="display: none;">
        <div style=""><a class="btn back" href="#" role="button">Скрий <i class="glyphicon glyphicon-arrow-up" style="position:relative; top: 2px;"></i></a></div>

        <form class="form-horizontal">
            <div class="form-group">
                <label for="inputBarcode" class="col-sm-2 control-label">Баркод</label>
                <div class="col-sm-6">
                    <input type="text" name="Barcode" class="form-control" id="inputBarcode" placeholder="Баркод">
                </div>
            </div>
            <div class="form-group">
                <label for="inputName" class="col-sm-2 control-label">Име</label>
                <div class="col-sm-6">
                    <input type="text" name="Name" class="form-control" id="inputName" placeholder="Име">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Добави</button>
                </div>
            </div>
        </form>
    </div>
