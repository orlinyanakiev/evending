<div class="sales_content">
    <div class="title">Продажби</div>
    <div class="filter">
        <select name="User">
            <option value="0">Всички потребители</option>
            <?php if(is_array($aDistributors) && !empty($aDistributors)){
                foreach($aDistributors as $oDistributor){
                    echo "<option value='{$oDistributor->Id}'>{$oDistributor->FirstName} {$oDistributor->LastName}</option>";
                }
            };?>
        </select>
        <select name="Storage">
            <option value="0">Всички вендинг автомати</option>
            <?php if(is_array($aVendingMachines) && !empty($aVendingMachines)){
                foreach($aVendingMachines as $oVendingMachine){
                    echo "<option value='{$oVendingMachine->Id}'>{$oVendingMachine->Name} {$oVendingMachine->Address}</option>";
                }
            };?>
        </select>
        <select name="Period" style="display: none;">
            <option value="">Неограничен период</option>
        </select>
    </div>
    <div class="sales_results">
        <div class="labels">
            <p><span>Приходи: </span><span id="income"><?= isset($aSales['Income'])? $aSales['Income'] : '0.00'; ?></span> лв.</p>
            <p><span>Разходи: </span><span id="expense"><?= isset($aSales['Expense'])? $aSales['Expense'] : '0.00'; ?></span> лв.</p>
            <p><span>Печалба: </span><span id="profit"><?= isset($aSales['Profit'])? $aSales['Profit'] : '0.00'; ?></span> лв.</p>
        </div>
    </div>
</div>
<div class="directions"><a href="<?= base_url();?>admin/manage"">Обратно</a></div>
