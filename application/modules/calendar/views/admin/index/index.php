<?php
$periodDays = [
    '0' => '',
    '1' => $this->getTranslator()->trans('Monday'),
    '2' => $this->getTranslator()->trans('Tuesday'),
    '3' => $this->getTranslator()->trans('Wednesday'),
    '4' => $this->getTranslator()->trans('Thursday'),
    '5' => $this->getTranslator()->trans('Friday'),
    '6' => $this->getTranslator()->trans('Saturday'),
    '7' => $this->getTranslator()->trans('Sunday')
];
?>

<h1><?=$this->getTrans('manage') ?></h1>
<?php if ($this->get('calendar') != ''): ?>
    <form class="form-horizontal" method="POST" action="">
        <?=$this->getTokenField() ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <colgroup>
                    <col class="icon_width">
                    <col class="icon_width">
                    <col class="icon_width">
                    <col class="col-lg-2">
                    <col class="col-lg-2">
                    <col class="col-lg-2">
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th><?=$this->getCheckAllCheckbox('check_entries') ?></th>
                        <th></th>
                        <th></th>
                        <th><?=$this->getTrans('start') ?></th>
                        <th><?=$this->getTrans('end') ?></th>
                        <th><?=$this->getTrans('periodEntry') ?></th>
                        <th><?=$this->getTrans('title') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->get('calendar') as $calendar): ?>
                        <tr>
                            <td><?=$this->getDeleteCheckbox('check_entries', $calendar->getId()) ?></td>
                            <td><?=$this->getEditIcon(['action' => 'treat', 'id' => $calendar->getId()]) ?></td>
                            <td><?=$this->getDeleteIcon(['action' => 'del', 'id' => $calendar->getId()]) ?></td>
                            <td><?=(new \Ilch\Date($calendar->getStart()))->format(($calendar->getPeriodDay()) ? 'H:i' : 'd.m.Y H:i') ?></td>
                            <td><?=(new \Ilch\Date($calendar->getEnd()))->format(($calendar->getPeriodDay()) ? 'H:i' : 'd.m.Y H:i') ?></td>
                            <td><?=$periodDays[$calendar->getPeriodDay()] ?></td>
                            <td><?=$this->escape($calendar->getTitle()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?=$this->getListBar(['delete' => 'delete']) ?>
    </form>
<?php else: ?>
    <?=$this->getTrans('noCalendar') ?>
<?php endif; ?>
