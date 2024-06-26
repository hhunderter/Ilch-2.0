<h1><?=$this->getTrans('menuPartnerAdd') ?></h1>
<form id="partnerForm" name="partnerForm" method="POST">
    <?=$this->getTokenField() ?>
    <div class="row mb-3 d-none">
        <label class="col-xl-2 col-form-label">
            <?=$this->getTrans('bot') ?>*
        </label>
        <div class="col-xl-8">
            <input type="text"
                   class="form-control"
                   name="bot"
                   placeholder="Bot" />
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('name') ? ' has-error' : '' ?>">
        <label for="name" class="col-xl-2 col-form-label">
            <?=$this->getTrans('name') ?>:
        </label>
        <div class="col-xl-8">
            <input type="text"
                   class="form-control"
                   id="name"
                   name="name"
                   placeholder="Name"
                   value="<?=($this->originalInput('name') != '' ? $this->escape($this->originalInput('name')) : '') ?>" />
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('link') ? ' has-error' : '' ?>">
        <label for="link" class="col-xl-2 col-form-label">
            <?=$this->getTrans('link') ?>:
        </label>
        <div class="col-xl-8">
            <input type="text"
                   class="form-control"
                   id="link"
                   name="link"
                   placeholder="https://"
                   value="<?=($this->originalInput('link') != '' ? $this->escape($this->originalInput('link')) : '') ?>" />
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('banner') ? ' has-error' : '' ?>">
        <label for="banner" class="col-xl-2 col-form-label">
            <?=$this->getTrans('banner') ?>:
        </label>
        <div class="col-xl-8">
            <input type="text"
                   class="form-control"
                   id="banner"
                   name="banner"
                   placeholder="https://"
                   value="<?=($this->originalInput('banner') != '' ? $this->escape($this->originalInput('banner')) : '') ?>" />
        </div>
    </div>
    <?php if ($this->get('captchaNeeded') && $this->get('defaultcaptcha')) : ?>
        <?=$this->get('defaultcaptcha')->getCaptcha($this) ?>
    <?php endif; ?>
    <div class="row mb-3">
        <div class="offset-xl-2 col-xl-8">
            <?php
                if ($this->get('captchaNeeded')) {
                    if ($this->get('googlecaptcha')) {
                        echo $this->get('googlecaptcha')->setForm('partnerForm')->getCaptcha($this, 'addButton', 'Partner');
                    } else {
                        echo $this->getSaveBar('addButton', 'Partner');
                    }
                } else {
                    echo $this->getSaveBar('addButton', 'Partner');
                }
            ?>
        </div>
    </div>
</form>
