<div class="page_wrapper">
    <div class="nav">
        <a class="nav_link" href="<?= base_url();?>member/distribution">Дистрибуция</a>
        <a class="nav_link active" href="<?= base_url();?>member/supply">Зареждане</a>
        <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
        <?php if($oUser->Type > 1) : ?>
            <a class="nav_link logout" href="<?= base_url();?>admin/users">Администрация</a>
        <?php endif; ?>
    </div>
    <div class="content">
        <?php if(is_array($aStorages) && !empty($aStorages)) : ?>
            <?php if(is_array($aProductTypes) && !empty($aProductTypes)) : ?>
                <div class="supply_form form">
                    <form>
                        <select name="Storage">
                            <option value="0">Хранилище</option>
                            <?php foreach($aStorages as $oStorage) : ?>
                                <option value="<?=$oStorage->Id?>"><?=$oStorage->Name?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="Category">
                            <option value="0">Категория</option>
                            <?php foreach($aProductCategories as $iCategoryId => $sCategory){
                                echo "<option value='{$iCategoryId}'>{$sCategory}</option>";
                            }?>
                        </select>
                        <select name="ProductType">
                            <option value="0">Тип изделие</option>
                        </select>
                        <input type="text" name="Quantity" placeholder="Количество">
                        <input type="text" name="ExpirationDate" placeholder="Валидност">
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
                Няма въведени хранилища!
            </div>
        <?php endif; ?>
        <div class="warning">
        </div>
    </div>
</div>