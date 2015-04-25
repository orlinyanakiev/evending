<div class="users_content">
    <div class="list" style="<?= is_array($aUsers) && !empty($aUsers) ? '' : 'display:none;' ;?>">
        <?php $iCounter = 0; ?>
        <?php foreach($aUsers as $oUserData) : ?>
            <div class="user_container container" user-id="<?=$oUserData->Id?>" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                <div class="column first_column"><?= $oUserData->FirstName?></div>
                <div class="column"><?=$oUserData->LastName?></div>
                <div class="manage_users last_column">
                    <a href="#" class="edit_user"><i class="fa fa-pencil"></i></a>
                    <?php if($oUserData->Id != $oUser->Id) : ?>
                        <a href="#" class="delete_user"><i class="fa fa-times"></i></a>
                    <?php else: ?>
                        <i style="color:grey" class="fa fa-times"></i>
                    <?php endif; ?>
                </div>
            </div>
            <?php $iCounter++; ?>
        <?php endforeach; ?>
        <?= $sUsersPagination ?>
    </div>
    <div class="edit_user_form form">
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
    </div>
</div>
<div class="directions"><a href="<?= base_url();?>admin/manage"">Обратно</a></div>
