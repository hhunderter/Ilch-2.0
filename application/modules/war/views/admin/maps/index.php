<?php

/** @var \Ilch\View $this */

/** @var \Ilch\Pagination $pagination */
$pagination = $this->get('pagination');
?>
<h1><?=$this->getTrans('manageMaps') ?></h1>
<?php if ($this->get('maps')) : ?>
    <?=$pagination->getHtml($this, []) ?>
    <form method="POST" action="">
        <?=$this->getTokenField() ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <colgroup>
                    <col class="icon_width" />
                    <col class="icon_width" />
                    <col class="icon_width" />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th><?=$this->getCheckAllCheckbox('check_maps') ?></th>
                        <th></th>
                        <th></th>
                        <th><?=$this->getTrans('mapsName') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    /** @var \Modules\War\Models\Maps $map */
                    foreach ($this->get('maps') as $map) : ?>
                        <tr>
                            <td><?=$this->getDeleteCheckbox('check_maps', $map->getId()) ?></td>
                            <td><?=$this->getEditIcon(['action' => 'treat', 'id' => $map->getId()]) ?></td>
                            <td><?=$this->getDeleteIcon(['action' => 'del', 'id' => $map->getId()]) ?></td>
                            <td><?=$this->escape($map->getName()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?=$pagination->getHtml($this, []) ?>
        <?=$this->getListBar(['delete' => 'delete']) ?>
    </form>
<?php else : ?>
    <?=$this->getTranslator()->trans('noMaps') ?>
<?php endif; ?>
