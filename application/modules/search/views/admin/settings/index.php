<?php

/** @var \Ilch\View $this */

/** @var Modules\Search\Mappers\Search $searchMapper */
$searchMapper = $this->get('searchMapper');

/** @var Modules\Search\Models\Modules[] $SearchModules */
$SearchModules = $searchMapper->getSearchModules();

/** @var array $modules */
$modules = $this->get('modules');
?>
<h1><?=$this->getTrans('settings') ?></h1>
<form class="form-horizontal" method="POST" action="<?=$this->getUrl(['action' => $this->getRequest()->getActionName()]) ?>">
    <?=$this->getTokenField() ?>
    <h2><?=$this->getTrans('boxSettings') ?></h2>
    <div class="form-group <?=$this->validation()->hasError('modules') ? 'has-error' : '' ?>">
        <label for="assignedModules" class="col-lg-2 control-label">
                <?=$this->getTrans('activemodules') ?>:
        </label>
        <div class="col-lg-9">
            <select class="chosen-select form-control"
                    id="assignedModules"
                    name="modules[]"
                    data-placeholder="<?=$this->getTrans('selectmodules') ?>"
                    multiple>
                <?php if ($SearchModules != '') : ?>
                    <?php foreach ($SearchModules as $modulkey => $options) : ?>
                        <?php if ($options->getHasModul()) : ?>
                        <option value="<?=$modulkey ?>"<?=(in_array($modulkey, $modules)) ? ' selected' : '' ?>>
                            <?=$this->getTrans($modulkey) ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <?=$this->getSaveBar() ?>
</form>
<script>
    $('#assignedModules').chosen();
</script>
