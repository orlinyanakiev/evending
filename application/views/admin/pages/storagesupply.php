<ul class="nav nav-pills nav-stacked nav-panel">
    <li class="active"><a href="<?=base_url()?>admin">Централен склад</a>
        <ul class="nav nav-pills nav-stacked sub-nav">
            <li><a href="<?=base_url()?>admin/MainStorage">Наличност</a></li>
            <li class="active"><a href="<?=base_url()?>admin/SupplyForm">Зареждане</a></li>
        </ul>
    </li>
    <li><a href="<?=base_url()?>admin/distributors">Дистрибутори</a></li>
    <li><a href="<?=base_url()?>admin/vending">Вендинг машини</a></li>
    <li><a href="<?=base_url()?>admin/categories">Категории стоки</a></li>
    <li><a href="<?=base_url()?>admin/users">Потребители</a></li>
</ul>
</div>

<div class="container col-sm-10 content">
    <?php if(is_array($aProductCategories) && !empty($aProductCategories)) : ?>
        <div class="main_storage_supply_form">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="inputQuantity" class="col-sm-2 control-label">Категория</label>
                    <div class="col-sm-6">
                        <select name="Category" class="form-control">
                            <option value="0">Категория</option>
                            <?php foreach($aProductCategories as $oProductCategory) : ?>
                                <option value="<?=$oProductCategory->Id?>"><?=$oProductCategory->Barcode.' - '.$oProductCategory->Name?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputQuantity" class="col-sm-2 control-label">Количество</label>
                    <div class="col-sm-6">
                        <input type="text" name="Quantity" class="form-control" id="inputQuantity" placeholder="Количество">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputSupplyValue" class="col-sm-2 control-label">Обща стойност</label>
                    <div class="col-sm-6">
                        <input type="text" name="SupplyValue" class="form-control" id="inputSupplyValue" placeholder="Обща стойност">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputExpirationDate" class="col-sm-2 control-label">Срок на валидност</label>
                    <div class="col-sm-6">
                        <input type="text" name="ExpirationDate" class="form-control" id="inputExpirationDate" placeholder="Срок на валидност">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">Зареждане</button>
                    </div>
                </div>
            </form>
        </div>
    <?php else : ?>
        <div class="col-sm-4">
            <div class="alert alert-warning" role="alert">Няма въведени категории стоки!</div>
        </div>
    <?php endif; ?>

