<ul class="nav nav-pills nav-stacked nav-panel">
    <li class="active"><a href="<?php base_url()?>admin/">Централен склад</a>
        <ul class="nav nav-pills nav-stacked sub-nav">
            <li class="active"><a href="<?=base_url()?>admin/MainStorage">Наличност</a></li>
            <li><a href="<?=base_url()?>admin/SupplyForm">Зареждане</a></li>
        </ul>
    </li>
    <li><a href="<?=base_url()?>admin/distributors">Дистрибутори</a></li>
    <li><a href="<?=base_url()?>admin/vending">Вендинг машини</a></li>
    <li><a href="<?=base_url()?>admin/categories">Категории стоки</a></li>
    <li><a href="<?=base_url()?>admin/users">Потребители</a></li>
</ul>
</div>

<div class="container col-sm-10 content">
    <?php if(is_array($aMainStorageProducts) && !empty($aMainStorageProducts)) : ?>
        <table class="table table-condensed table-hover">
            <?php
            $iQuantityOfAllProducts = 0;
            $fValueOfAllProducts = 0;
            ?>
            <thead><tr><th>Баркод</th><th>Име</th><th>Стойност</th><th>Дата на изтичане</th><th>Количество</th></tr></thead>
            <tbody>
            <?php foreach($aMainStorageProducts as $oMainStorageProduct) :
                $iQuantityOfAllProducts += $oMainStorageProduct->Quantity;
                $fValueOfAllProducts += $oMainStorageProduct->Quantity * $oMainStorageProduct->Value?>
                <tr><td><?=$oMainStorageProduct->Category->Barcode?></td>
                    <td><?=$oMainStorageProduct->Category->Name?></td>
                    <td><?=round($oMainStorageProduct->Value, 2)?> лв.</td>
                    <td><?=$oMainStorageProduct->ExpirationDate?></td>
                    <td><?=$oMainStorageProduct->Quantity?></td>
                </tr>
            <?php endforeach; ?>
            <tr><td></td><td></td><td><?=round($fValueOfAllProducts, 2)?> лв.</td><td></td><td><?=$iQuantityOfAllProducts?></td></tr>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert-warning alert col-sm-4" role="alert">Централният склад е празен!</div>
    <?php endif; ?>
