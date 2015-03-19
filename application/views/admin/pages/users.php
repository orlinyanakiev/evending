<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link active" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="nav_link" href="<?= base_url();?>admin/storages">Хранилища</a>
        <a class="nav_link" href="<?= base_url();?>admin/products">Изделия</a>
        <a class="nav_link" href="<?= base_url();?>admin/supply">Зареждане</a>
        <a class="logout" href="<?= base_url();?>member/distribution">Обратно</a>
    </div>
    <div class="content">
        <div class="list" style="display:<?= is_array($aUsers) && !empty($aUsers) ? 'block' : 'none' ;?>;">
            <div>
                <div class="column first_column">Име</div>
                <div class="column">Фамилия</div>
            </div>
            <?php $iCounter = 0; ?>
            <?php foreach($aUsers as $oUserData) : ?>
                <?php $iCounter++ ;?>
                <div class="user_container container" user-id="<?=$oUserData->Id?>" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                    <div class="column first_column"><?= $oUserData->FirstName?></div>
                    <div class="column"><?=$oUserData->LastName?></div>
                    <?php if($oUser->Type > 2) : ?>
                        <div class="manage_users last_column">
                            <a href="#" class="edit_user"><i class="fa fa-pencil"></i></a>
                            <?php if($oUserData->Id != $oUser->Id) : ?>
                            <a href="#" class="delete_user"><i class="fa fa-times"></i></a>
                            <?php else: ?>
                            <i style="color:grey" class="fa fa-times"></i>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="warning">
        </div>
    </div>
</div>