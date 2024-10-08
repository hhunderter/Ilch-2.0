<?php

/** @var \Ilch\View $this */
?>
<h1><?=$this->getTrans('currencies') ?></h1>
<form method="POST" action="">
    <?=$this->getTokenField() ?>
    <table class="table table-hover table-striped">
        <colgroup>
            <col class="icon_width" />
            <col class="icon_width" />
            <col class="icon_width" />
            <col />
        </colgroup>
        <thead>
            <tr>
                <th><?=$this->getCheckAllCheckbox('check_currencies') ?></th>
                <th></th>
                <th></th>
                <th><?=$this->getTrans('currency') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($this->get('currencies')) > 0) : ?>
                <?php
                /** @var \Modules\Events\Models\Currency $currency */
                foreach ($this->get('currencies') as $currency) : ?>
                    <tr>
                        <td><?=$this->getDeleteCheckbox('check_currencies', $currency->getId()) ?></td>
                        <td><?=$this->getEditIcon(['action' => 'treat', 'id' => $currency->getId()]) ?></td>
                        <td> <?=$this->getDeleteIcon(['action' => 'delete', 'id' => $currency->getId()]) ?></td>
                        <td><?=$this->escape($currency->getName()) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?=$this->getTrans('noCurrenciesExist') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?=$this->getListBar(['delete' => 'delete']) ?>
</form>
