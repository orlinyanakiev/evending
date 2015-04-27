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
        <div class="title">Зареждане</div>
        <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
            <?php if(is_array($aProductTypes) && !empty($aProductTypes)) : ?>
                <div class="supply_form form">
                    <form>
                        <?php if(!is_object($oDistributor)) : ?>
                            <select name="Storage">
                                <option value="0">Склад</option>
                                <?php foreach($aStorages as $oStorage) : ?>
                                    <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <input type="hidden" name="Storage" value="<?= $oDistributor->StorageId; ?>" />
                            <div class="fake_input_field"><?= $oDistributorStorage->Name ?></div>
                        <?php endif; ?>

                        <select name="Category">
                            <option value="0">Категория</option>
                            <?php foreach($aProductCategories as $iCategoryId => $sCategory){
                                echo "<option value='{$iCategoryId}'>{$sCategory}</option>";
                            }?>
                        </select>
                        <select name="ProductType">
                            <option value="0">Тип изделие</option>
                        </select>
                        <input type="text" name="Quantity" placeholder="Количество" autocomplete="off" />
                        <input id="datepicker" type="text" name="ExpirationDate" placeholder="Валидност" autocomplete="off" />
                        <input type="text" name="Price" placeholder="Цена" autocomplete="off" />
                        <?php if(!is_object($oDistributor)) : ?>
                            <input type="text" name="Value" placeholder="Себестойност" autocomplete="off" />
                        <?php endif; ?>
                        <button type="submit">Добави</button>
                    </form>
                </div>
            <?php else : ?>
                <div>
                    Няма въведени типове изделия!
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div>
                Няма въведени складове!
            </div>
        <?php endif; ?>
        <div class="warning">
        </div>
        <div class="directions">
            <a href="<?= base_url();?>member/actions">Обратно</a>
        </div>
    </div>
</div>