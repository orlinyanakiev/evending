<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>member/homepage">Начало</a>
        <a class="nav_link active" href="<?= base_url();?>member/actions">Действия</a>
        <?php if($oUser->Type > 1) : ?>
            <a class="nav_link" href="<?= base_url();?>admin/">Управление</a>
        <?php endif; ?>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
    </div>
    <div class="content">
        <div class="title">Вендинг</div>
            <div class="vending_form form">
                <form>
                    <?php if(!is_object($oDistributor)) : ?>
                        <select name="Storage">
                            <?php foreach($aStorages as $oStorage) : ?>
                                <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="VendingMachines">
                            <option value="0">Към</option>
                        </select>
                    <?php else : ?>
                        <?php if(is_array($aVendingMachines) && !empty($aVendingMachines)) : ?>
                            <?php if(is_array($aDistributorAvailability) && !empty($aDistributorAvailability)) : ?>
                                <input type="hidden" name="Storage" value="<?= $oDistributor->StorageId; ?>" />
                                <div class="fake_input_field"><?= $oDistributorStorage->Name ?></div>
                                <select name="VendingMachine">
                                    <option value="0" selected="selected">Вендинг машина</option>
                                    <?php foreach($aVendingMachines as $oVendingMachine) : ?>
                                        <option value="<?=$oVendingMachine->Id?>"><?=$oVendingMachine->Name.' '.$oVendingMachine->Address?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="vending_title">Зареждане</div>
                                <select name="DistributeProduct">
                                    <option quantity="" value="0">Изделие</option>
                                    <?php foreach($aDistributorAvailability as $aAvailability) : ?>
                                        <option quantity="<?=$aAvailability['iQuantity']?>" value="<?=$aAvailability['oProduct']->Id?>"><?=$aAvailability['oProduct']->Type->Name?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="DistributeQuantity" placeholder="Количество" autocomplete="off" />
                                <div class="vending_title">Бракуване</div>
                                <select name="ObsoleteProduct">
                                    <option quantity="" value="0">Изделие</option>
                                </select>
                                <input type="text" name="ObsoleteQuantity" placeholder="Количество" autocomplete="off" />
                                <div class="vending_title">Отчитане на приход</div>
                                <input type="text" name="IncomeValue" placeholder="Сума" autocomplete="off"/>
                                <button type="submit">Изпълни</button>
                            <?php else: ?>
                                <div>
                                    Нямате налични изделия!
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div>
                                Нямате назначени вендинг машини!
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </form>
            </div>
        <div class="warning">
        </div>
        <div class="directions">
            <a href="<?= base_url();?>member/actions">Обратно</a>
        </div>
    </div>
</div>