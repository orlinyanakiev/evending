<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link active" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="nav_link" href="<?= base_url();?>admin/storages">Складове</a>
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
                <div class="user_container container" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                    <div class="column first_column"><?= $oUserData->FirstName?></div>
                    <div class="column"><?=$oUserData->LastName?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>