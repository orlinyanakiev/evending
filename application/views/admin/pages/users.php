<div class="page_wrapper">
    <div class="nav">
        <a class="admin active" href="<?= base_url();?>admin/users">Потребители</a>
        <a class="admin" href="<?= base_url();?>admin/storages">Складове</a>
        <a class="admin" href="<?= base_url();?>admin/products">Изделия</a>
        <a class="admin" href="<?= base_url();?>admin/info">Справки</a>
        <a class="logout" href="<?= base_url();?>member/">Обратно</a>
    </div>
    <div class="content">
        <div class="list" style="display:<?= is_array($aUsers) && !empty($aUsers) ? 'block' : 'none' ;?>;">
            <?php foreach($aUsers as $oUserData) : ?>
                <div class="user_container"><?= $oUserData->FirstName.' '.$oUserData->LastName; ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>