<ul class="nav nav-pills nav-stacked nav-panel">
    <li><a href="<?=base_url()?>admin/">Централен склад</a></li>
    <li><a href="<?=base_url()?>admin/distributors">Дистрибутори</a></li>
    <li><a href="<?=base_url()?>admin/vending">Вендинг машини</a></li>
    <li><a href="<?=base_url()?>admin/categories">Категории стоки</a></li>
    <li class="active"><a href="<?=base_url()?>admin/users">Потребители</a></li>
</ul>
</div>

<div class="container col-sm-10 content">
    <div class="users_list col-sm-10">
        <table class="table table-condensed table-hover">
            <thead><tr><th>Име</th><th>Фамилия</th><th>Потребителско име</th><th>Дата на регистрация</th><th>Тип</th></tr></thead>
            <tbody>
            <?php foreach($aUsers as $oUserData) : ?>
                <tr user-id="<?=$oUserData->Id?>">
                    <td><?= $oUserData->FirstName?></td>
                    <td><?=$oUserData->LastName?></td>
                    <td><?=$oUserData->LoginName?></td>
                    <td><?=$oUserData->Registered?></td>
                    <td>
                        <select name="Type">
                            <?php foreach($aUserTypes as $iKey => $sUserType) : ?>
                                <option value="<?=$iKey?>" <?= $iKey == $oUserData->Type ? 'selected="selected"' : ''?>><?=$sUserType?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="container col-sm-12" style="text-align: center;">
            <?= $sUsersPagination ?>
        </div>
    </div>

    <div class="edit_user_form form" style="display: none;">
        <form method="post" action="">
            <input type="hidden" name="UserId" value="">
            <input type="text" name="FirstName" placeholder="Първо име" autocomplete="off"/>
            <input type="text" name="LastName" placeholder="Фамилия" autocomplete="off"/>
            <input type="text" name="LoginName" placeholder="Потребителско име" autocomplete="off"/>
            <select name="Type">
            </select>
            <button type="submit">Запази</button>
            <div class="vending_machines_list">
                <?php if(is_array($aVendingMachines) && !empty($aVendingMachines)) : ?>
                    <?php foreach($aVendingMachines as $oVendingMachine){
                        echo "<div><input type='checkbox' name='vending_machine[]' value='{$oVendingMachine->Id}'>{$oVendingMachine->Name} {$oVendingMachine->Address}</div>";
                    }?>
                <?php endif; ?>
            </div>
        </form>
        <div class="directions"><a href="<?= base_url();?>admin/users"">Обратно</a></div>
    </div>
