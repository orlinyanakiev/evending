<div class="page_wrapper">
    <?php if($oUser->Type != 0) : ?>
        <div class="nav">
            <a class="nav_link active" href="<?= base_url();?>member/homepage">Начало</a>
            <a class="nav_link" href="<?= base_url();?>member/actions">Действия</a>
            <?php if($oUser->Type > '1') : ?>
                <a class="nav_link" href="<?= base_url();?>admin/">Управление</a>
            <?php endif; ?>
            <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
        </div>
        <div class="content">
            <p><?= $oUser->FirstName.' '.$oUser->LastName.', '?>добре дошли в складовата програма на Лион!</p>
            <?php if(is_array($aExpiringProducts) && !empty($aExpiringProducts)){
                echo '<p class="request_failure">Изделия с изтичащ срок на годност:</p>';
                foreach($aExpiringProducts as $oExpiringProduct){
                    echo $oExpiringProduct->Type->Name.' '.$oExpiringProduct->ExpirationDate.'</br>';
                }
            } else {
                echo '<p class="request_success">Няма стоки с изтизащ срок на годност</p>';
            }; ?>
            <div class="warning">
            </div>
        </div>
    <?php else : ?>
        <div class="warning">
            <p class="request_failure">Обърнете се към администратор за права!</p>
            <p><a href="<?= base_url();?>member/logout">Изход</a></p>
        </div>
    <?php endif; ?>
</div>