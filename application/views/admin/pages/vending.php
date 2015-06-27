<ul class="nav nav-pills nav-stacked nav-panel">
    <li><a href="<?=base_url()?>admin/">Централен склад</a></li>
    <li><a href="<?=base_url()?>admin/distributors">Дистрибутори</a></li>
    <li class="active"><a href="<?=base_url()?>admin/vending">Вендинг машини</a>
        <ul class="nav nav-pills nav-stacked sub-nav">
            <?php foreach($aVendingMachines as $oVendingMachine) : ?>
                <li>
                    <a class="vending_machine" storage-id="<?=$oVendingMachine->Id?>" href="#"><?=$oVendingMachine->Name;?><?= isset($oVendingMachine->Address) && $oVendingMachine->Address != '' ? ' ('.$oVendingMachine->Address.')' : '' ;?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li><a href="<?=base_url()?>admin/categories">Категории стоки</a></li>
    <li><a href="<?=base_url()?>admin/users">Потребители</a></li>
</ul>
</div>

<div class="container col-sm-10 content">
    <?php if(is_array($aVendingMachines) && !empty($aVendingMachines)) : ?>

    <?php else: ?>
        <div class="alert alert-warning" role="alert">Не са намерени вендинг машини!</div>
    <?php endif; ?>

    <div class="col-sm-12">
        <a class="btn add_vending_machine" href="#" role="button"><i class="glyphicon glyphicon-plus-sign"></i> Добави вендинг машина</a>
    </div>

    <div class="add_vending_machine_form col-sm-8" style="display: none;">
        <div style=""><a class="btn back" href="#" role="button">Скрий <i class="glyphicon glyphicon-arrow-up" style="position:relative; top: 2px;"></i></a></div>

        <form class="form-horizontal">
            <div class="form-group">
                <label for="inputName" class="col-sm-2 control-label">Име</label>
                <div class="col-sm-6">
                    <input type="text" name="Name" class="form-control" id="inputName" placeholder="Име">
                </div>
            </div>
            <div class="form-group">
                <label for="inputAddress" class="col-sm-2 control-label">Адрес</label>
                <div class="col-sm-6">
                    <input type="text" name="Address" class="form-control" id="inputAddress" placeholder="Адрес">
                </div>
            </div>
            <div class="form-group">
                <label for="inputCash" class="col-sm-2 control-label">Начална сума</label>
                <div class="col-sm-6">
                    <input type="text" name="Cash" class="form-control" id="inputCash" placeholder="Начална сума">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Добави</button>
                </div>
            </div>
        </form>
    </div>

    <div class="vending_machine_template" style="display: none;">
        template
    </div>
