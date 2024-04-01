<?php
$user = $this->get('user');

if ($user->getId()) {
    $fieldsetLegend = $this->getTrans('editUser');
} else {
    $fieldsetLegend = $this->getTrans('addUser');
}
?>

<h1><?=$fieldsetLegend ?></h1>
<form action="<?=$this->getUrl(['action' => 'treat']) ?>" method="POST" class="form-horizontal" id="userForm">
    <?=$this->getTokenField() ?>
    <input type="hidden"
           name="id"
           value="<?=$user->getId() ?>" />
    <div class="row mb-3 <?=$this->validation()->hasError('name') ? 'has-error' : '' ?>">
        <label for="name" class="col-xl-3 control-label">
            <?=$this->getTrans('userName') ?>
        </label>
        <div class="col-xl-9">
            <input type="text"
                   class="form-control required"
                   id="name"
                   name="name"
                   placeholder="<?=$this->getTrans('userName') ?>"
                   value="<?=($this->originalInput('name') != '') ? $this->escape($this->originalInput('name')) : $this->escape($user->getName()) ?>" />
        </div>
    </div>
    <div class="row mb-3 <?=$this->validation()->hasError('email') ? 'has-error' : '' ?>">
        <label for="email" class="col-xl-3 control-label">
            <?=$this->getTrans('userEmail') ?>
        </label>
        <div class="col-xl-9">
            <input type="text"
                   class="form-control required email"
                   id="email"
                   name="email"
                   placeholder="<?=$this->getTrans('userEmail') ?>"
                   value="<?=($this->originalInput('email') != '') ? $this->escape($this->originalInput('email')) : $this->escape($user->getEmail()) ?>" />
        </div>
    </div>
    <div class="row mb-3 <?=$this->validation()->hasError('userPassword') ? 'has-error' : '' ?>">
        <label for="password" class="col-xl-3 control-label">
            <?=$this->getTrans('userPassword') ?>
        </label>
        <div class="col-xl-9">
            <input type="password"
                   class="form-control"
                   id="password"
                   name="password"
                   placeholder="<?=$this->getTrans('userPassword') ?>"
                   value="" />
        </div>
    </div>
    <?php
    if ($user->getId()):
        $dateConfirmed = $user->getDateConfirmed();

        if ($dateConfirmed == '') {
            $dateConfirmed = $this->getTrans('notConfirmedYet');
        }

        $dateLastActivity = $user->getDateLastActivity();

        if ($dateLastActivity == '') {
            $dateLastActivity = $this->getTrans('neverLoggedIn');
        }
    ?>
        <div class="row mb-3">
            <label class="col-xl-3 control-label">
                <?=$this->getTrans('userDateCreated') ?>
            </label>
            <div class="col-xl-9">
                <p class="form-control-plaintext"><?=$this->escape($user->getDateCreated()) ?></p>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-xl-3 control-label">
                <?=$this->getTrans('userDateConfirmed') ?>
            </label>
            <div class="col-xl-9">
                <p class="form-control-plaintext"><?=$this->escape($dateConfirmed) ?></p>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-xl-3 control-label">
                <?=$this->getTrans('userDateLastActivity') ?>
            </label>
            <div class="col-xl-9">
                <p class="form-control-plaintext"><?=$this->escape($dateLastActivity) ?></p>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mb-3">
        <div class="col-xl-3 control-label">
            <?=$this->getTrans('usergalleryAllowed') ?>
        </div>
        <div class="col-xl-9">
            <div class="flipswitch">
                <input type="radio" class="flipswitch-input" id="opt-gallery-yes" name="opt_gallery" value="1" <?php if ($user->getOptGallery() == '1') {
        echo 'checked="checked"';
    } ?> />
                <label for="opt-gallery-yes" class="flipswitch-label flipswitch-label-on"><?=$this->getTrans('yes') ?></label>
                <input type="radio" class="flipswitch-input" id="opt-gallery-no" name="opt_gallery" value="0" <?php if ($user->getOptGallery() != '1') {
        echo 'checked="checked"';
    } ?> />
                <label for="opt-gallery-no" class="flipswitch-label flipswitch-label-off"><?=$this->getTrans('no') ?></label>
                <span class="flipswitch-selection"></span>
            </div>
        </div>
    </div>
    <?php if ($user->getId()) : ?>
    <div class="row mb-3">
        <div class="col-xl-3 control-label">
            <?=$this->getTrans('lockUser') ?>
        </div>
        <div class="col-xl-9">
            <div class="flipswitch">
                <input type="radio" class="flipswitch-input" id="locked-yes" name="locked" value="1" <?php if ($user->getLocked() == '1') {
        echo 'checked="checked"';
    } ?> />
                <label for="locked-yes" class="flipswitch-label flipswitch-label-on"><?=$this->getTrans('yes') ?></label>
                <input type="radio" class="flipswitch-input" id="locked-no" name="locked" value="0" <?php if ($user->getLocked() != '1') {
        echo 'checked="checked"';
    } ?> />
                <label for="locked-no" class="flipswitch-label flipswitch-label-off"><?=$this->getTrans('no') ?></label>
                <span class="flipswitch-selection"></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="row mb-3">
        <label for="assignedGroups" class="col-xl-3 control-label">
                <?=$this->getTrans('assignedGroups') ?>
        </label>
        <div class="col-xl-9">
            <select class="chosen-select form-control"
                    id="assignedGroups"
                    name="groups[]"
                    data-placeholder="<?=$this->getTrans('selectAssignedGroups') ?>"
                    multiple>
                <?php
                foreach ($this->get('groupList') as $group) {
                    if (($this->getUser()->isAdmin() && $group->getId() == 1) || $group->getId() != 1) {
                        ?>
                    <option value="<?=$group->getId() ?>"
                            <?php
                            foreach ($user->getGroups() as $assignedGroup) {
                                if ($group->getId() === $assignedGroup->getId()) {
                                    echo 'selected="selected"';
                                    break;
                                }
                            } ?>>
                        <?=$this->escape($group->getName()) ?>
                    </option>
                    <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <?php if ($user->getId()) : ?>
    <div class="row mb-3">
        <label for="assignedGroups" class="col-xl-3 control-label">
            <?=$this->getTrans('userProfile') ?>
        </label>
        <div class="col-xl-4">
            <a href="<?=$this->getUrl(['controller' => 'index', 'action' => 'treatProfile', 'user' => $user->getId()]) ?>" class="btn btn-outline-secondary"><?=$this->getTrans('editUserProfile') ?></a>
        </div>
    </div>
    <?php endif; ?>
    <?=$this->getSaveBar() ?>
</form>

<script>
$('#assignedGroups').chosen();
$('#userForm').validate();
</script>
