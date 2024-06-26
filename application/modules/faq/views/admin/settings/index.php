<h1><?=$this->getTrans('menuSettings') ?></h1>
<form method="POST">
    <?=$this->getTokenField() ?>
    <div class="row mb-3<?=$this->validation()->hasError('sortCategoriesAlphabetically') ? ' has-error' : '' ?>">
        <div class="col-xl-2 col-form-label">
            <?=$this->getTrans('sortCategoriesAlphabetically') ?>
        </div>
        <div class="col-xl-4">
            <div class="flipswitch">
                <input type="radio" class="flipswitch-input" id="sortCategoriesAlphabetically-on" name="sortCategoriesAlphabetically" value="1" <?=($this->originalInput('sortCategoriesAlphabetically', $this->get('sortCategoriesAlphabetically')) == '1') ? 'checked="checked"' : '' ?> />
                <label for="sortCategoriesAlphabetically-on" class="flipswitch-label flipswitch-label-on"><?=$this->getTrans('on') ?></label>
                <input type="radio" class="flipswitch-input" id="sortCategoriesAlphabetically-off" name="sortCategoriesAlphabetically" value="0" <?=($this->originalInput('sortCategoriesAlphabetically', $this->get('sortCategoriesAlphabetically')) != '1') ? 'checked="checked"' : '' ?> />
                <label for="sortCategoriesAlphabetically-off" class="flipswitch-label flipswitch-label-off"><?=$this->getTrans('off') ?></label>
                <span class="flipswitch-selection"></span>
            </div>
        </div>
    </div>
    <div class="row mb-3<?=$this->validation()->hasError('sortQuestionsAlphabetically') ? ' has-error' : '' ?>">
        <div class="col-xl-2 col-form-label">
            <?=$this->getTrans('sortQuestionsAlphabetically') ?>
        </div>
        <div class="col-xl-4">
            <div class="flipswitch">
                <input type="radio" class="flipswitch-input" id="sortQuestionsAlphabetically-on" name="sortQuestionsAlphabetically" value="1" <?=($this->originalInput('sortQuestionsAlphabetically', $this->get('sortQuestionsAlphabetically')) == '1') ? 'checked="checked"' : '' ?> />
                <label for="sortQuestionsAlphabetically-on" class="flipswitch-label flipswitch-label-on"><?=$this->getTrans('on') ?></label>
                <input type="radio" class="flipswitch-input" id="sortQuestionsAlphabetically-off" name="sortQuestionsAlphabetically" value="0" <?=($this->originalInput('sortQuestionsAlphabetically', $this->get('sortQuestionsAlphabetically')) != '1') ? 'checked="checked"' : '' ?> />
                <label for="sortQuestionsAlphabetically-off" class="flipswitch-label flipswitch-label-off"><?=$this->getTrans('off') ?></label>
                <span class="flipswitch-selection"></span>
            </div>
        </div>
    </div>

    <?=$this->getSaveBar() ?>
</form>
