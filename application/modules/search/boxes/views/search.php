<?php

/** @var \Ilch\View $this */

/** @var Modules\Search\Mappers\Search $searchMapper */
$searchMapper = $this->get('searchMapper');
?>
<form action="<?=$this->getUrl(['module' => 'search', 'controller' => 'index', 'action' => 'index']) ?>" class="form-horizontal" method="post">
    <?=$this->getTokenField(); ?>
    <?=$searchMapper->getInputHTML() ?>
    <div class="form-group">
        <div class="col-lg-12">
            <button type="submit" class="btn">
                <?=$this->getTrans('go') ?>
            </button>
        </div>
    </div>
</form>
