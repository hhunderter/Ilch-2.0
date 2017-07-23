<?php
$teamsMapper = $this->get('teamsMapper');
$teamsCache = [];
?>

<h1><?=$this->getTrans('historyOfUser') ?></h1>
<?php if ($this->get('joins')): ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <colgroup>
                <col class="col-lg-3" />
                <col class="col-lg-2" />
                <col class="col-lg-2" />
                <col class="col-lg-2" />
                <col class="col-lg-2" />
            </colgroup>
            <thead>
                <tr>
                    <th><?=$this->getTrans('name') ?></th>
                    <th><?=$this->getTrans('team') ?></th>
                    <th><?=$this->getTrans('dateTime') ?></th>
                    <th><?=$this->getTrans('decision') ?></th>
                    <th><?=$this->getTrans('details') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->get('joins') as $join): ?>
                    <?php if (!array_key_exists($join->getTeamId(), $teamsCache)) {
                        $teamsCache[$join->getTeamId()] = $teamsMapper->getTeamById($join->getTeamId());
                    }
                    $team = $teamsCache[$join->getTeamId()];
                    
                    $date = new Ilch\Date($join->getDateCreated()); ?>
                    <tr>
                        <td><?=$this->escape($join->getName()) ?></td>
                        <td><?=$this->escape($team->getName()) ?></td>
                        <td><?=$date->format('d.m.Y H:i', true) ?></td>
                        <td><?=($join->getDecision() == 1) ? $this->getTrans('accepted') : $this->getTrans('declined')?></td>
                        <td><a href="<?=$this->getUrl(['action' => 'show', 'id' => $join->getId()]) ?>" title="<?=$this->getTrans('show') ?>"><?=$this->getTrans('show') ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?=$this->get('pagination')->getHtml($this, ['action' => 'showuserhistory', 'userId' => $join->getUserId()]) ?>
<?php else: ?>
    <?=$this->getTrans('noApplications') ?>
<?php endif; ?>
