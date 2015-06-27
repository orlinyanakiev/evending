
<ul class="nav nav-pills nav-stacked nav-panel">
    <li><a href="<?=base_url()?>admin/">Централен склад</a></li>
    <li class="active"><a href="<?=base_url()?>admin/distributors">Дистрибутори</a>
        <ul class="nav nav-pills nav-stacked sub-nav">
            <?php foreach($aDistributors as $oDistributor) : ?>
                <li>
                    <a storage-type="distributor" storage-id="<?=$oDistributor->Id?>" href="#"><?=$oDistributor->FirstName.' '.$oDistributor->LastName;?></a>
                </li>
            <?php endforeach ?>
        </ul>
    </li>
    <li><a href="<?=base_url()?>admin/vending">Вендинг машини</a></li>
    <li><a href="<?=base_url()?>admin/categories">Категории стоки</a></li>
    <li><a href="<?=base_url()?>admin/users">Потребители</a></li>
</ul>
</div>

<div class="container col-sm-10 distributors_content">
    <?php if(is_array($aDistributors) && !empty($aDistributors)) : ?>
        <?php if(is_array($aVendingMachines) && !empty($aVendingMachines)) : $aDistributorsId = array(); ?>
            <form>
                <table class="table table-condensed table-hover">
                    <thead><tr><th></th>
                        <?php foreach($aDistributors as $oDistributor) : ?>
                            <th><?=$oDistributor->FirstName.' '.$oDistributor->LastName?></th>
                        <?php
                        $aDistributorsId[] = $oDistributor->Id;
                        endforeach; ?>
                    </tr></thead><tbody>
                        <?php foreach($aVendingMachines as $oVendingMachine) : ?>
                            <tr>
                                <td><?=$oVendingMachine->Name.' '.$oVendingMachine->Address?></td>
                            <?php foreach($aDistributorsId as $iDistributorsId) : ?>
                                <td>
                                    <input type="checkbox" name="vending_machines[]" value="<?=$iDistributorsId.' '.$oVendingMachine->Id?>">
                                </td>
                            <?php endforeach; ?>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <div class="container col-sm-7 col-sm-offset-5">
                    <button class="btn btn-default" type="submit">Запази</button>
                </div>
            </form>
        <?php else : ?>
            <div class="alert alert-warning" role="alert">Не са намерени вендинг машини!</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">Не са намерени дистрибутори!</div>
    <?php endif; ?>

