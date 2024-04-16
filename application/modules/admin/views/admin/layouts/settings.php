<?php

/** @var \Ilch\View $this */
?>
<h1><?=$this->getTrans('settings') ?></h1>
<form class="form-horizontal" method="POST">
    <?=$this->getTokenField() ?>
    <div class="form-group <?=$this->validation()->hasError('favicon') ? 'has-error' : '' ?>">
        <label for="selectedImage_1" class="col-lg-2 control-label">
            <?=$this->getTrans('favicon') ?>:
        </label>
        <div class="col-lg-4">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       id="selectedImage_1"
                       name="favicon"
                       placeholder="<?=$this->getTrans('choose') ?>"
                       value="<?=$this->escape($this->originalInput('favicon', $this->get('favicon'))) ?>"
                       readonly />
                <span class="input-group-addon">
                    <a href="javascript:eraseValue('selectedImage_1')">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </span>
                <span class="input-group-addon">
                    <a href="javascript:media_1()" id="media">
                        <i class="fa-regular fa-image"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group <?=$this->validation()->hasError('appleIcon') ? 'has-error' : '' ?>">
        <label for="selectedImage_2" class="col-lg-2 control-label">
            <?=$this->getTrans('appleIcon') ?>:
        </label>
        <div class="col-lg-4">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       id="selectedImage_2"
                       name="appleIcon"
                       placeholder="<?=$this->getTrans('choose') ?>"
                       value="<?=$this->escape($this->originalInput('appleIcon', $this->get('appleIcon'))) ?>"
                       readonly />
                <span class="input-group-addon">
                    <a href="javascript:eraseValue('selectedImage_2')">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </span>
                <span class="input-group-addon">
                    <a href="javascript:media_2()" id="media">
                        <i class="fa-regular fa-image"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group <?=$this->validation()->hasError('pageTitle') ? 'has-error' : '' ?>">
        <label for="pageTitle" class="col-lg-2 control-label">
            <?=$this->getTrans('pageTitle') ?>:
        </label>
        <div class="col-lg-4">
            <input type="text"
                   class="form-control"
                   id="pageTitle"
                   name="pageTitle"
                   value="<?=$this->escape($this->originalInput('pageTitle', $this->get('pageTitle'))) ?>" />
        </div>
    </div>
    <div class="form-group <?=$this->validation()->hasError('pageTitleOrder') ? 'has-error' : '' ?>">
        <label for="pageTitleOrder" class="col-lg-2 control-label">
            <?=$this->getTrans('pageTitleOrder') ?>:<br>
            <h6><?=$this->getTrans('pageTitleOrderInfo') ?></h6>
        </label>
        <div class="col-lg-4">
            <input type="text"
                   class="form-control"
                   id="pageTitleOrder"
                   name="pageTitleOrder"
                   value="<?=$this->escape($this->originalInput('pageTitleOrder', $this->get('pageTitleOrder'))) ?>" />
        </div>
    </div>
    <div class="form-group <?=$this->validation()->hasError('pageTitleModuledataSeparator') ? 'has-error' : '' ?>">
        <label for="pageTitleModuledataSeparator" class="col-lg-2 control-label">
            <?=$this->getTrans('pageTitleModuledataSeparator') ?>:
        </label>
        <div class="col-lg-4">
            <input type="text"
                   class="form-control"
                   id="pageTitleModuledataSeparator"
                   name="pageTitleModuledataSeparator"
                   value="<?=$this->escape($this->originalInput('pageTitleModuledataSeparator', $this->get('pageTitleModuledataSeparator'))) ?>" />
        </div>
    </div>
    <div class="form-group <?=$this->validation()->hasError('pageTitleModuledataOrder') ? 'has-error' : '' ?>">
        <label for="pageTitleModuledataOrder" class="col-lg-2 control-label">
            <?=$this->getTrans('pageTitleModuledataOrder') ?>:
        </label>
        <div class="col-lg-4">
            <div class="flipswitch">
                <input type="radio" class="flipswitch-input" id="pageTitleModuledataOrder-yes" name="pageTitleModuledataOrder" value="1" <?=($this->originalInput('pageTitleModuledataOrder', $this->get('pageTitleModuledataOrder'))) ? 'checked="checked"' : '' ?> />
                <label for="pageTitleModuledataOrder-yes" class="flipswitch-label flipswitch-label-on"><?=$this->getTrans('DESC') ?></label>
                <input type="radio" class="flipswitch-input" id="pageTitleModuledataOrder-no" name="pageTitleModuledataOrder" value="0"  <?=(!$this->originalInput('pageTitleModuledataOrder', $this->get('pageTitleModuledataOrder'))) ? 'checked="checked"' : '' ?> />
                <label for="pageTitleModuledataOrder-no" class="flipswitch-label flipswitch-label-off"><?=$this->getTrans('ASC') ?></label>
                <span class="flipswitch-selection"></span>
            </div>
        </div>
    </div>
    <div class="form-group <?=$this->validation()->hasError('keywords') ? 'has-error' : '' ?>">
        <label for="keywords" class="col-lg-2 control-label">
            <?=$this->getTrans('seoKeywords') ?>:
        </label>
        <div class="col-lg-4">
            <textarea class="form-control"
                      id="keywords"
                      name="keywords"><?=$this->escape($this->originalInput('keywords', $this->get('keywords'))) ?></textarea>
        </div>
    </div>
    <div class="form-group <?=$this->validation()->hasError('description') ? 'has-error' : '' ?>">
        <label for="description" class="col-lg-2 control-label">
            <?=$this->getTrans('seoDescription') ?>:
        </label>
        <div class="col-lg-4">
            <textarea class="form-control"
                      id="description"
                      name="description"><?=$this->escape($this->originalInput('description', $this->get('description'))) ?></textarea>
        </div>
    </div>
    <?=$this->getSaveBar() ?>
</form>

<?=$this->getDialog('mediaModal', $this->getTrans('media'), '<iframe style="border:0;"></iframe>') ?>
<script>
    <?=$this->getMedia()
            ->addMediaButton($this->getUrl('admin/media/iframe/index/type/single/input/_1/'))
            ->addInputId('_1')
            ->addUploadController($this->getUrl('admin/media/index/upload'))
    ?>
    <?=$this->getMedia()
            ->addMediaButton($this->getUrl('admin/media/iframe/index/type/single/input/_2/'))
            ->addInputId('_2')
            ->addUploadController($this->getUrl('admin/media/index/upload'))
    ?>

    function eraseValue(id) {
        document.getElementById(id).value = "";
    }
    $('#keywords').tokenfield();
    $('#keywords').on('tokenfield:createtoken', function (event) {
        let existingTokens = $(this).tokenfield('getTokens');
        $.each(existingTokens, function(index, token) {
            if (token.value === event.attrs.value)
                event.preventDefault();
        });
    });
</script>
