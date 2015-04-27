<div class="events_content">
    <div class="title">Хронология</div>
    <div class="list">
        <?php if(is_array($aEvents) && !empty($aEvents)) : ?>
            <?php $iCounter = 0; ?>
            <?php foreach($aEvents as $oEvent) : ?>
                <div class="event_container container" style="background-color: #<?= $iCounter % 2 == 0 ? 'DDF5B7' : 'FFFF99' ?>">
                    <a href="#" class="event_preview" event-id="<?= $oEvent->Id;?>">
                        <div class="first_column column"><?=$oEvent->DateRegistered?></div>
                        <div class="first_column column"><?=$oEvent->UserId->FirstName.' '.$oEvent->UserId->LastName?></div>
                        <div class="first_column column"><?=$oEvent->Type;?></div>
                    </a>
                </div>
                <?php $iCounter++ ?>
            <?php endforeach; ?>
            <?= $sPagination ?>
        <?php else: ?>
            Няма регистрирани събития.
        <?php endif; ?>
        <div class="directions"><a href="<?= base_url();?>admin/manage"">Обратно</a></div>
    </div>
</div>