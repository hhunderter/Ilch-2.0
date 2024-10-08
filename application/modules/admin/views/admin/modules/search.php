<?php
$modulesList = url_get_contents($this->get('updateserver'));
$modulesOnUpdateServer = json_decode($modulesList);
$versionsOfModules = $this->get('versionsOfModules');
$coreVersion = $this->get('coreVersion');
$dependencies = $this->get('dependencies');
$cacheFilename = ROOT_PATH . '/cache/' . md5($this->get('updateserver')) . '.cache';
$cacheFileDate = null;
if (file_exists($cacheFilename)) {
    $cacheFileDate = new \Ilch\Date(date('Y-m-d H:i:s.', filemtime($cacheFilename)));
}

// Define the custom sort function
function custom_sort($a,$b)
{
    return strcmp($a->name, $b->name);
}

function checkOthersDependencies($module, $dependencies)
{
    $dependencyCheck = [];
    foreach ($dependencies as $dependency) {
        $key = key($module);
        if (array_key_exists($key, $dependency)) {
            $parsed = explode(',', $dependency[$key]);
            if (!version_compare($module[$key], $parsed[1], $parsed[0])) {
                $dependencyCheck[array_search(array($key => $dependency[$key]), $dependencies)] = [$key => str_replace(',','', $dependency[$key])];
            }
        }
    }

    return $dependencyCheck;
}

function checkOwnDependencies($versionsOfModules, $moduleOnUpdateServer)
{
    if (empty($moduleOnUpdateServer->depends)) {
        return true;
    }

    foreach ($moduleOnUpdateServer->depends as $key => $value) {
        if (array_key_exists($key, $versionsOfModules)) {
            $parsed = explode(',', $value);
            if (!version_compare($versionsOfModules[$key]['version'], $parsed[1], $parsed[0])) {
                return false;
            }
        }
    }

    return true;
}
?>

<link href="<?=$this->getModuleUrl('static/css/extsearch.css') ?>" rel="stylesheet">

<div class="d-flex align-items-start heading-filter-wrapper">
    <h1><?=$this->getTrans('search') ?></h1>
    <div class="input-group input-group-sm filter d-flex justify-content-end">
        <span class="input-group-text">
            <i class="fa-solid fa-filter"></i>
        </span>
        <input type="text" id="filterInput" class="form-control" placeholder="<?=$this->getTrans('search') ?>">
        <span class="input-group-text">
            <span id="filterClear" class="fa-solid fa-xmark"></span>
        </span>
    </div>
</div>
<p><a href="<?=$this->getUrl(['action' => 'refreshurl', 'from' => 'search']) ?>" class="btn btn-primary"><?=$this->getTrans('searchForUpdates') ?></a> <span class="small"><?=(!empty($cacheFileDate)) ? $this->getTrans('lastUpdateOn') . ' ' . $this->getTrans($cacheFileDate->format('l', true)) . $cacheFileDate->format(', d. ', true) . $this->getTrans($cacheFileDate->format('F', true)) . $cacheFileDate->format(' Y H:i', true) : $this->getTrans('lastUpdateOn') . ': ' . $this->getTrans('lastUpdateUnknown') ?></span></p>
<div class="checkbox">
    <label><input type="checkbox" name="setgotokey" onclick="gotokeyAll();" <?=$this->get('gotokey')? 'checked' : '' ?>/><?=$this->getTrans('gotokey') ?></label>
</div>
<?php
if (empty($modulesOnUpdateServer)) {
    echo $this->getTrans('noModulesAvailable');
    return;
}

// Sort the modules by name
usort($modulesOnUpdateServer, 'custom_sort');
?>

<div id="modules" class="table-responsive">
    <table class="table table-hover table-striped table-list-search">
        <colgroup>
            <col class="col-xl-2" />
            <col class="col-xl-1" />
            <col />
        </colgroup>
        <thead>
            <tr>
                <th><?=$this->getTrans('name') ?></th>
                <th><?=$this->getTrans('version') ?></th>
                <th><?=$this->getTrans('desc') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modulesOnUpdateServer as $moduleOnUpdateServer):  ?>
                <?php
                if (!empty($moduleOnUpdateServer->phpExtensions)) {
                    $extensionCheck = [];
                    foreach ($moduleOnUpdateServer->phpExtensions as $extension) {
                        $extensionCheck[] = extension_loaded($extension);
                    }
                }
                ?>
                <tr id="Module_<?=$moduleOnUpdateServer->key ?>">
                    <td>
                        <a href="<?=$this->getUrl(['action' => 'show', 'id' => $moduleOnUpdateServer->id]) ?>" title="<?=$this->getTrans('info') ?>"><?=$this->escape($moduleOnUpdateServer->name) ?></a>
                        <br />
                        <small>
                            <?=$this->getTrans('author') ?>:
                            <?php if ($moduleOnUpdateServer->link != ''): ?>
                                <a href="<?=$moduleOnUpdateServer->link ?>" title="<?=$this->escape($moduleOnUpdateServer->author) ?>" target="_blank" rel="noopener"><i><?=$this->escape($moduleOnUpdateServer->author) ?></i></a>
                            <?php else: ?>
                                <i><?=$this->escape($moduleOnUpdateServer->author) ?></i>
                            <?php endif; ?>
                        </small>
                        <br /><br />
                        <?php
                        $isInstalled = in_array($moduleOnUpdateServer->key, $this->get('modules'));
                        $iconClass = ($isInstalled) ? 'fa-solid fa-arrows-rotate' : 'fa-solid fa-download';

                        if (!empty($moduleOnUpdateServer->phpExtensions) && in_array(false, $extensionCheck)): ?>
                            <button class="btn disabled"
                                    title="<?=$this->getTrans('phpExtensionError') ?>">
                                <i class="<?=$iconClass ?>"></i>
                            </button>
                        <?php elseif (version_compare(PHP_VERSION, $moduleOnUpdateServer->phpVersion, '<')): ?>
                            <button class="btn disabled"
                                    title="<?=$this->getTrans('phpVersionError') ?>">
                                <i class="<?=$iconClass ?>"></i>
                            </button>
                        <?php elseif (version_compare($coreVersion, $moduleOnUpdateServer->ilchCore, '<')): ?>
                            <button class="btn disabled"
                                    title="<?=$this->getTrans('ilchCoreError') ?>">
                                <i class="<?=$iconClass ?>"></i>
                            </button>
                        <?php elseif ($isInstalled && version_compare($versionsOfModules[$moduleOnUpdateServer->key]['version'], $moduleOnUpdateServer->version, '<') && version_compare('2.2.0', $moduleOnUpdateServer->ilchCore, '>')): ?>
                            <button class="btn disabled"
                                    title="<?=$this->getTrans('moduleTooOld') ?>">
                                <i class="<?=$iconClass ?>"></i>
                            </button>
                        <?php elseif ($isInstalled && version_compare($versionsOfModules[$moduleOnUpdateServer->key]['version'], $moduleOnUpdateServer->version, '>=')): ?>
                            <button class="btn disabled"
                                    title="<?=$this->getTrans('alreadyExists') ?>">
                                <i class="fa-solid fa-check text-success"></i>
                            </button>
                        <?php elseif ($isInstalled && !empty(checkOthersDependencies([$moduleOnUpdateServer->key => $moduleOnUpdateServer->version], $dependencies))): ?>
                            <button class="btn disabled"
                                    data-bs-toggle="modal"
                                    data-bs-target="#infoModal<?=$moduleOnUpdateServer->key ?>"
                                    title="<?=$this->getTrans('dependencyError') ?>">
                                <i class="<?=$iconClass ?>"></i>
                            </button>
                        <?php elseif (!checkOwnDependencies($versionsOfModules, $moduleOnUpdateServer)): ?>
                            <button class="btn disabled"
                                    title="<?=$this->getTrans('dependencyError') ?>">
                                <i class="<?=$iconClass ?>"></i>
                            </button>
                        <?php elseif ($isInstalled && version_compare($versionsOfModules[$moduleOnUpdateServer->key]['version'], $moduleOnUpdateServer->version, '<')): ?>
                            <form method="POST" action="<?=$this->getUrl(['action' => 'update', 'key' => $moduleOnUpdateServer->key, 'version' => $moduleOnUpdateServer->version, 'from' => 'search']) ?>">
                                <?=$this->getTokenField() ?>
                                <input type="hidden" name="gotokey" value="<?=$this->get('gotokey')? '1' : '0' ?>" />
                                <button type="submit"
                                        class="btn btn-outline-secondary showOverlay"
                                        title="<?=$this->getTrans('moduleUpdate') ?>">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?=$this->getUrl(['action' => 'search', 'key' => $moduleOnUpdateServer->key, 'version' => $moduleOnUpdateServer->version, 'from' => 'search']) ?>">
                                <?=$this->getTokenField() ?>
                                <input type="hidden" name="gotokey" value="<?=$this->get('gotokey')? '1' : '0' ?>" />
                                <button type="submit"
                                        class="btn btn-outline-secondary showOverlay"
                                        title="<?=$this->getTrans('moduleDownload') ?>">
                                    <i class="fa-solid fa-download"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                        <a href="<?=$this->getUrl(['action' => 'show', 'id' => $moduleOnUpdateServer->id]) ?>" title="<?=$this->getTrans('info') ?>">
                            <span class="btn btn-outline-secondary">
                                <i class="fa-solid fa-info text-info"></i>
                            </span>
                        </a>
                    </td>
                    <td><?=$moduleOnUpdateServer->version?></td>
                    <td>
                        <?=$moduleOnUpdateServer->desc ?>
                        <?=(!empty($moduleOnUpdateServer->official) && $moduleOnUpdateServer->official) ? '<span class="ilch-official">ilch</span>' : '' ?>
                    </td>
                </tr>
                <?php
                    $dependencyInfo = '<p>' . $this->getTrans('dependencyInfo') . '</p>';
                    foreach (checkOthersDependencies([$moduleOnUpdateServer->key => $moduleOnUpdateServer->version], $dependencies) as $key => $value) {
                        $dependencyInfo .= '<b>' . $key . ':</b> '.key($value) . $value[key($value)] . '<br />';
                    }
                ?>
                <?=$this->getDialog('infoModal' . $moduleOnUpdateServer->key, $this->getTrans('dependencies') . ' ' . $this->getTrans('info'), $dependencyInfo) ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="loadingoverlay" hidden>
    <div class="d-flex justify-content-center">
      <div class="spinner-border" style="width: 6rem; height: 6rem;" role="status">
        <span class="visually-hidden"><?=$this->getTrans('processingPleaseWait') ?></span>
      </div>
    </div>
</div>

<script>
let delayedShow;

function gotokeyAll() {
   $("[name='gotokey']").each(function() {
        if ($("[name='setgotokey']").prop('checked')) {
            $(this).prop('value',"1");
        } else {
            $(this).prop('value',"0");
        }
   });
}
// search
$(document).ready(function() {
    $(".showOverlay").on('click', function(){
        let loadingOverlay = $(".loadingoverlay");

        delayedShow = setTimeout(function(){
            loadingOverlay.removeAttr('hidden');
        }, 200);

        setTimeout(function(){
            loadingOverlay.attr('hidden', '');
        }, 30000);
    });

    clearTimeout(delayedShow);
    $(".loadingoverlay").attr('hidden', '');

    // something is entered in search form
    $('#user-search').keyup( function() {
        var that = this,
            tableBody = $('.table-list-search tbody'),
            tableRowsClass = $('.table-list-search tbody tr');

        $('.search-sf').remove();
        tableRowsClass.each( function(i, val) {

            // lower text for case insensitive
            var rowText = $(val).text().toLowerCase(),
                inputText = $(that).val().toLowerCase();

            if(inputText != '') {
                $('.search-query-sf').remove();
                tableBody.prepend('<tr class="search-query-sf"><td colspan="3"><strong><?=$this->getTrans('searchingFor') ?>: "'
                    + $(that).val()
                    + '"</strong></td></tr>');
            } else {
                $('.search-query-sf').remove();
            }

            if( rowText.indexOf( inputText ) == -1 ) {
                // hide rows
                tableRowsClass.eq(i).hide();
            } else {
                $('.search-sf').remove();
                tableRowsClass.eq(i).show();
            }
        });

        // all tr elements are hidden
        if(tableRowsClass.children(':visible').length == 0) {
            tableBody.append('<tr class="search-sf"><td class="text-muted" colspan="3"><?=$this->getTrans('noResultFound') ?></td></tr>');
        }
    });
});
</script>
