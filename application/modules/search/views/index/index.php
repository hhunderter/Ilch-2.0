<?php

/** @var \Ilch\View $this */

/** @var Modules\Search\Mappers\Search $searchMapper */
$searchMapper = $this->get('searchMapper');

/** @var array $SearchModules */
$SearchModules = $searchMapper->getSearchModules();
?>
<script src="<?=$this->getModuleUrl('static/js/bootstrap-multiselect.js') ?>" charset="UTF-8"></script>
<link href="<?=$this->getModuleUrl('static/css/bootstrap-multiselect.css') ?>" rel="stylesheet">
<script src="<?=$this->getModuleUrl('static/js/dataTables.bootstrap.min.js') ?>" charset="UTF-8"></script>
<link href="<?=$this->getModuleUrl('static/css/dataTables.bootstrap.min.css') ?>" rel="stylesheet">
<script src="<?=$this->getModuleUrl('static/js/jquery.dataTables.min.js') ?>" charset="UTF-8"></script>
<h1><?=$this->getTrans('menuSearch') ?></h1>
<form method="POST" class="form-horizontal" action="<?=$this->getUrl(['action' => 'index']) ?>">
    <?=$this->getTokenField() ?>
    <?=$searchMapper->getInputHTML() ?>
    <br>
    <div id="result-div">
        <?php foreach ($searchMapper->getResults() as $key => $entryArray) : ?>
        <fieldset id="result-fieldset-<?=$key ?>">
            <legend><a href="#"><?=$this->getTrans($key) ?> <span class="badge"><?=count($entryArray) ?></span></a></legend>
            <div id="result-div-<?=$key ?>">
                <table id="result-table-<?=$key ?>" class="table table-hover table-striped">
                    <colgroup>
                        <col class="col-lg-1">
                        <col class="col-lg-2">
                        <col>
                    </colgroup>
                    <thead>
                        <tr>
                            <th><?=$this->getTrans('id') ?></th>
                            <th><?=$this->getTrans('date') ?></th>
                            <th><?=$this->getTrans('result') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        /** @var Modules\Search\Models\Search $entry */
                        foreach ($entryArray as $id => $entry) : ?>
                        <tr>
                            <td><?=($id + 1); ?></td>
                            <td><?=$this->escape($entry->getDateCreated()) ?>&nbsp;</td>
                            <td>
                            <?php if ($entry->getId()) : ?>
                                <a href="<?=$this->getUrl(array_merge(['module' => $key], $searchMapper->getSearchModules($key)->getUrl(), [$searchMapper->getSearchModules($key)->getUrlIdKey() => $entry->getId()]), $searchMapper->getSearchModules($key)->getUrlroute()) ?>" target="_blank">
                            <?php endif; ?>
                                <?=$this->escape($entry->getResult()) ?>
                            <?php if ($entry->getId()) : ?>
                                </a>
                            <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </fieldset>
        <?php endforeach; ?>
    </div>
</form>
<script>
<?php foreach ($searchMapper->getResults() as $key => $entryArray) : ?>
    new DataTable('#result-table-<?=$key ?>', {
        info: false,
        paging: false,
        search: false,
        searching: false
    });
<?php endforeach; ?>
</script>
