<?php $entrie = $this->get('groups'); ?>
<h1><?=(!$entrie->getId()) ? $this->getTrans('manageNewGroup') : $this->getTrans('treatGroup') ?></h1>
<form id="article_form" method="POST" action="">
    <?=$this->getTokenField() ?>
    <div class="row mb-3<?=$this->validation()->hasError('groupName') ? ' has-error' : '' ?>">
        <label for="groupNameInput" class="col-xl-2 col-form-label">
            <?=$this->getTrans('groupName') ?>:
        </label>
        <div class="col-xl-4">
            <input type="text"
                   class="form-control"
                   id="groupNameInput"
                   name="groupName"
                   value="<?=$this->escape($this->originalInput('groupName', ($entrie->getId()?$entrie->getGroupName():''))) ?>" />
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('groupTag') ? ' has-error' : '' ?>">
        <label for="groupTagInput" class="col-xl-2 col-form-label">
            <?=$this->getTrans('groupTag') ?>:
        </label>
        <div class="col-xl-4">
            <input type="text"
                   class="form-control"
                   id="groupTagInput"
                   name="groupTag"
                   value="<?=$this->escape($this->originalInput('groupTag', ($entrie->getId()?$entrie->getGroupTag():''))) ?>" />
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('groupImage') ? ' has-error' : '' ?>">
        <label for="selectedImage_1" class="col-xl-2 col-form-label">
            <?=$this->getTrans('groupImage') ?>:
        </label>
        <div class="col-xl-4">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       id="selectedImage_1"
                       name="groupImage"
                       placeholder="<?=$this->getTrans('groupImage') ?>"
                       value="<?=$this->escape($this->originalInput('groupImage', ($entrie->getId()?$entrie->getGroupImage():''))) ?>" />
                <span class="input-group-text">
                    <a id="media" href="javascript:media_1()"><i class="fa-regular fa-image"></i></a>
                </span>
            </div>
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('groupDesc') ? ' has-error' : '' ?>">
        <label for="groupDesc" class="col-xl-2 col-form-label">
            <?=$this->getTrans('groupDesc') ?>:
        </label>
        <div class="col-xl-4">
            <div class="input-group">
                <textarea class="form-control"
                          name="groupDesc"
                          id="groupDesc"
                          cols="50"
                          rows="5"
                          placeholder="<?=$this->escape($this->originalInput('groupDesc', ($entrie->getId()?$entrie->getGroupDesc():''))) ?>"></textarea>
            </div>
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('userGroup') ? ' has-error' : '' ?>">
        <label for="warGroup" class="col-xl-2 col-form-label">
            <?=$this->getTrans('assignedMember') ?>
        </label>
        <div class="col-xl-4">
            <select class="form-select" id="warGroup" name="userGroup">
                <optgroup label="<?=$this->getTrans('groupsName') ?>">
                    <?php foreach ($this->get('userGroupList') as $groupList): ?>
                        <?php if ($groupList->getId() != '3'): ?>
                            <option value="<?=$groupList->getId() ?>" <?=($this->originalInput('userGroup', ($entrie->getId()?$entrie->getGroupMember():0))) == $groupList->getId() ? 'selected=""' : '' ?>><?=$this->escape($groupList->getName()) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
    </div>
    <?=($entrie->getId()) ? $this->getSaveBar('updateButton') : $this->getSaveBar('addButton') ?>
</form>

<?=$this->getDialog('mediaModal', $this->getTrans('media'), '<iframe style="border:0;"></iframe>') ?>
<script>
    // Example for multiple input filds
    <?=$this->getMedia()
        ->addMediaButton($this->getUrl('admin/media/iframe/index/type/single/input/_1/'))
        ->addInputId('_1')
        ->addUploadController($this->getUrl('admin/media/index/upload'))
    ?>

    $('#assignedMember').chosen();
</script>
