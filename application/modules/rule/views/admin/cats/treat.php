<?php

/** @var \Ilch\View $this */

/** @var Modules\Rule\Models\Rule $cat */
$cat = $this->get('cat');

/** @var Modules\Rule\Models\Rule[]|null $rulesparents */
$rulesparents = $this->get('rulesparents');

/** @var Modules\User\Models\Group[]|null $userGroupList */
$userGroupList = $this->get('userGroupList');
?>
<h1><?=($cat) ? $this->getTrans('edit') : $this->getTrans('add') ?></h1>
<form method="POST">
    <?=$this->getTokenField(); ?>
    <div class="row mb-3<?=$this->validation()->hasError('paragraph') ? ' has-error' : '' ?>">
        <label for="paragraph" class="col-xl-2 col-form-label">
            <?=$this->getTrans('art') ?>
        </label>
        <div class="col-xl-1">
            <input type="text"
                   class="form-control"
                   id="paragraph"
                   name="paragraph"
                   value="<?=$this->escape($this->originalInput('paragraph', $cat->getParagraph())) ?>"
                   required />
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('name') ? ' has-error' : '' ?>">
        <label for="name" class="col-xl-2 col-form-label">
            <?=$this->getTrans('name') ?>:
        </label>
        <div class="col-xl-3">
            <input type="text"
                   class="form-control"
                   id="name"
                   name="name"
                   value="<?=$this->escape($this->originalInput('name', $cat->getTitle())) ?>"
                   required />
        </div>
    </div>
    <div class="row mb-3">
        <label for="assignedGroupsRead" class="col-xl-2 col-form-label">
            <?=$this->getTrans('see') ?>
            <a href="#" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-title="<?=$this->getTrans('seetext') ?>"><i class="fa-solid fa-circle-info"></i></a>
        </label>
        <div class="col-xl-4">
            <select class="choices-select form-control" id="assignedGroupsRead" name="groups[]" data-placeholder="<?=$this->getTrans('selectAssignedGroups') ?>" multiple>
                <option value="all"<?=(in_array('all', $this->originalInput('groups', $this->get('groups')))) ? ' selected' : '' ?>><?=$this->getTrans('all') ?></option>
            <?php foreach ($userGroupList as $groupList) : ?>
                <?php if ($groupList->getId() != 1) : ?>
                    <option value="<?=$groupList->getId() ?>"<?=(in_array($groupList->getId(), $this->originalInput('groups', $this->get('groups')))) ? ' selected' : '' ?>><?=$this->escape($groupList->getName()) ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?=($cat->getId()) ? $this->getSaveBar('updateButton') : $this->getSaveBar('addButton') ?>
</form>

<script>
    $(document).ready(function() {
        new Choices('#assignedGroupsRead', {
            ...choicesOptions,
            searchEnabled: true
        })
    });
</script>
