<?php

/** @var \Ilch\View $this */

/** @var \Modules\Teams\Mappers\Joins $joinsMapper */
$joinsMapper = $this->get('joinsMapper');
/** @var \Modules\Teams\Mappers\Teams $teamsMapper */
$teamsMapper = $this->get('teamsMapper');

/** @var \Modules\Teams\Models\Joins $join */
$join = $this->get('join');
$team = $teamsMapper->getTeamById($join->getTeamId());
$date = new Ilch\Date($join->getDateCreated());
$birthday = new Ilch\Date($join->getBirthday());
?>
<h1><?=$this->getTrans('application') ?></h1>
<div class="row mb-3">
    <label class="col-xl-2">
        <?=$this->getTrans('name') ?>:
    </label>
    <div class="col-xl-2">
        <?=$this->escape($join->getName()) ?>
    </div>
</div>
<div class="row mb-3">
    <label class="col-xl-2">
        <?=$this->getTrans('team') ?>:
    </label>
    <div class="col-xl-2">
        <?=(!empty($team)) ? $this->escape($team->getName()) : $this->getTrans('noTeam') ?>
    </div>
</div>
<div class="row mb-3">
    <label class="col-xl-2">
        <?=$this->getTrans('email') ?>:
    </label>
    <div class="col-xl-2">
        <?=$this->escape($join->getEMail()) ?>
    </div>
</div>
<div class="row mb-3">
    <label class="col-xl-2">
        <?=$this->getTrans('dateTime') ?>:
    </label>
    <div class="col-xl-2">
        <?=$date->format('d.m.Y H:i', true) ?>
    </div>
</div>
<div class="row mb-3">
    <label class="col-xl-2">
        <?=$this->getTrans('gender') ?>:
    </label>
    <div class="col-xl-2">
        <?php
        if ($join->getGender() == 1) {
            echo $this->getTrans('genderMale');
        } elseif ($join->getGender() == 2) {
            echo $this->getTrans('genderFemale');
        } else {
            echo $this->getTrans('genderNonBinary');
        }
        ?>
    </div>
</div>
<?php if ($join->getBirthday()) : ?>
    <div class="row mb-3">
        <label class="col-xl-2">
            <?=$this->getTrans('birthday') ?>:
        </label>
        <div class="col-xl-2">
            <?=$birthday->format('d.m.Y') ?> (<?=$joinsMapper->getAge($birthday) ?>)
        </div>
    </div>
<?php endif; ?>
<?php if ($join->getPlace()) : ?>
    <div class="row mb-3">
        <label class="col-xl-2">
            <?=$this->getTrans('place') ?>:
        </label>
        <div class="col-xl-2">
            <?=$this->escape($join->getPlace()) ?>
        </div>
    </div>
<?php endif; ?>
<div class="row mb-3">
    <label class="col-xl-2">
        <?=$this->getTrans('skill') ?>:
    </label>
    <div class="col-xl-2">
        <?php
        if ($join->getSkill() == 0) {
            echo $this->getTrans('beginner');
        } elseif ($join->getSkill() == 1) {
            echo $this->getTrans('experience');
        } elseif ($join->getSkill() == 2) {
            echo $this->getTrans('expert');
        } elseif ($join->getSkill() == 3) {
            echo $this->getTrans('pro');
        }
        ?>
    </div>
</div>
<div class="row mb-3">
    <label class="col-xl-2">
        <?=$this->getTrans('text') ?>:
    </label>
    <div class="col-xl-12 ck-content">
        <?=$this->alwaysPurify($join->getText()) ?>
    </div>
</div>
