<div class="page_wrapper">
        <?php if($oUser->Type != 0) : ?>
        <div class="nav">
            <a class="nav_link" href="<?= base_url();?>member/homepage">Начало</a>
            <a class="nav_link active" href="<?= base_url();?>member/actions">Действия</a>
            <?php if($oUser->Type > '1') : ?>
                <a class="nav_link" href="<?= base_url();?>admin/">Управление</a>
            <?php endif; ?>
            <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
        </div>
        <div class="content">
            <div class="actions_navigation list">
                <div class="option"><a href="<?= base_url();?>member/supply">Зареждане</a></div>
                <div class="option"><a href="<?= base_url();?>member/distribution">Дистрибуция</a></div>
<!--                <div class="option"><a href="--><?//= base_url();?><!--member/obsolete">Бракуване</a></div>-->
                <div class="option"><a href="<?= base_url();?>member/revenue">Отчитане на приходи</a></div>
                <div class="option"><a href="<?= base_url();?>member/sales">Продажба</a></div>
            </div>
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