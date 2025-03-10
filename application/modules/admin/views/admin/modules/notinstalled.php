<link href="<?=$this->getModuleUrl('static/css/extsearch.css') ?>" rel="stylesheet">
<h1><?=$this->getTrans('modulesNotInstalled') ?></h1>
<?php if (!empty($this->get('modulesNotInstalled'))): ?>
    <?php

    /** @var \Modules\Admin\Models\Module[] $modulesNotInstalled */
    $modulesNotInstalled = $this->get('modulesNotInstalled');

    $modulesList = url_get_contents($this->get('updateserver'));
    $modulesOnUpdateServer = json_decode($modulesList);
    $cacheFilename = ROOT_PATH . '/cache/' . md5($this->get('updateserver')) . '.cache';
    $cacheFileDate = new \Ilch\Date(date('Y-m-d H:i:s.', filemtime($cacheFilename)));

    function checkOwnDependencies(array $versionsOfModules, ?array $dependencies): bool
    {
        foreach ($dependencies ?? [] as $key => $value) {
            $parsed = explode(',', $value);
            if (!version_compare($versionsOfModules[$key]['version'], $parsed[1], $parsed[0])) {
                return false;
            }
        }

        return true;
    }
    ?>
    <p><a href="<?=$this->getUrl(['action' => 'refreshurl', 'from' => 'notinstalled']) ?>" class="btn btn-primary"><?=$this->getTrans('searchForUpdates') ?></a> <span class="small"><?=$this->getTrans('lastUpdateOn') ?> <?=$this->getTrans($cacheFileDate->format('l', true)) . $cacheFileDate->format(', d. ', true) . $this->getTrans($cacheFileDate->format('F', true)) . $cacheFileDate->format(' Y H:i', true) ?></span></p>
    <div id="modules" class="table-responsive">
        <table class="table table-hover table-striped">
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
                <?php foreach ($modulesNotInstalled as $module): ?>
                    <?php
                    $content = $module->getContentForLocale($this->getTranslator()->getLocale());

                    $moduleOnUpdateServerFound = null;
                    $moduleOnUpdateServer = null;
                    foreach ($modulesOnUpdateServer ?? [] as $moduleOnUpdateServer) {
                        if ($moduleOnUpdateServer->key == $module->getKey()) {
                            $moduleOnUpdateServerFound = $moduleOnUpdateServer;
                            break;
                        }
                    }

                    $extensionCheck = [];
                    $phpExtension = [];
                    if ($module->getPHPExtension()) {
                        foreach ($module->getPHPExtension() as $extension => $state) {
                            $extensionCheck[$extension] = extension_loaded($extension);
                        }

                        foreach ($extensionCheck as $key => $value) {
                            if ($value == true) {
                                $phpExtension[] = '<span class="text-success">' . $key . '</span>';
                            } else {
                                $phpExtension[] = '<span class="text-danger">' . $key . '</span>';
                            }
                        }

                        $phpExtension = implode(', ', $phpExtension);
                    }

                    if (version_compare(PHP_VERSION, $module->getPHPVersion(), '>=')) {
                        $phpVersion = '<span class="text-success">' . $module->getPHPVersion() . '</span>';
                    } else {
                        $phpVersion = '<span class="text-danger">' . $module->getPHPVersion() . '</span>';
                    }

                    if (version_compare($this->get('coreVersion'), $module->getIlchCore(), '>=')) {
                        $ilchCore = '<span class="text-success">' . $module->getIlchCore() . '</span>';
                    } else {
                        $ilchCore = '<span class="text-danger">' . $module->getIlchCore() . '</span>';
                    }
                    ?>
                    <tr id="Module_<?=$module->getKey() ?>">
                        <td>
                            <?=$content['name'] ?>
                            <br />
                            <small>
                                <?=$this->getTrans('author') ?>:
                                <?php if ($module->getLink() != ''): ?>
                                    <a href="<?=$module->getLink() ?>" title="<?=$this->escape($module->getAuthor()) ?>" target="_blank" rel="noopener">
                                        <i><?=$this->escape($module->getAuthor()) ?></i>
                                    </a>
                                <?php else: ?>
                                    <i><?=$this->escape($module->getAuthor()) ?></i>
                                <?php endif; ?>
                            </small>
                            <br /><br />
                            <?php if ($module->getPHPExtension() && in_array(false, $extensionCheck)): ?>
                                <button class="btn disabled"
                                        title="<?=$this->getTrans('phpExtensionError') ?>">
                                    <i class="fa-regular fa-floppy-disk"></i>
                                </button>
                            <?php elseif (!version_compare(PHP_VERSION, $module->getPHPVersion(), '>=')): ?>
                                <button class="btn disabled"
                                        title="<?=$this->getTrans('phpVersionError') ?>">
                                    <i class="fa-regular fa-floppy-disk"></i>
                                </button>
                            <?php elseif (!version_compare($this->get('coreVersion'), $module->getIlchCore(), '>=')): ?>
                                <button class="btn disabled"
                                        title="<?=$this->getTrans('ilchCoreError') ?>">
                                    <i class="fa-regular fa-floppy-disk"></i>
                                </button>
                            <?php elseif (version_compare('2.2.0', $module->getIlchCore(), '>')): ?>
                                <button class="btn disabled"
                                        title="<?=$this->getTrans('moduleTooOld') ?>">
                                    <i class="fa-regular fa-floppy-disk"></i>
                                </button>
                            <?php elseif (!checkOwnDependencies($this->get('versionsOfModules'), $this->get('dependencies')[$module->getKey()] ?? null)): ?>
                                <button class="btn disabled"
                                        title="<?=$this->getTrans('dependencyError') ?>">
                                    <i class="fa-regular fa-floppy-disk"></i>
                                </button>
                            <?php else: ?>
                                <form method="POST" action="<?=$this->getUrl(['action' => 'install', 'key' => $module->getKey(), 'from' => 'notinstalled']) ?>">
                                    <?=$this->getTokenField() ?>
                                    <button type="submit"
                                            class="btn btn-outline-secondary showOverlay"
                                            title="<?=$this->getTrans('installModule') ?>">
                                        <i class="fa-regular fa-floppy-disk"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if ($moduleOnUpdateServer && $module->getKey() == $moduleOnUpdateServer->key): ?>
                                <a href="<?=$this->getUrl(['action' => 'show', 'id' => $moduleOnUpdateServer->id]) ?>" title="<?=$this->getTrans('info') ?>">
                                    <span class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-info text-info"></i>
                                    </span></a>
                            <?php else: ?>
                                <span class="btn btn-outline-secondary"
                                      data-bs-toggle="modal"
                                      data-bs-target="#infoModal<?=$module->getKey() ?>"
                                      title="<?=$this->getTrans('info') ?>">
                                    <i class="fa-solid fa-info text-info"></i>
                                </span>
                            <?php endif; ?>
                            <a href="<?=$this->getUrl(['action' => 'delete', 'key' => $module->getKey()], null, true) ?>" class="btn btn-outline-secondary" title="<?=$this->getTrans('delete') ?>">
                                <i class="fa-regular fa-trash-can text-warning"></i>
                            </a>
                        </td>
                        <td><?=$module->getVersion() ?></td>
                        <td>
                            <?php if (!empty($content['description'])): ?>
                                <?=$content['description'] ?>
                            <?php endif; ?>
                            <?=($module->getOfficial()) ? '<span class="ilch-official">ilch</span>' : '' ?>
                        </td>
                    </tr>

                    <?php
                    if ($module->getLink() != '') {
                        $author = '<a href="' . $module->getLink() . '" title="' . $this->escape($module->getAuthor()) . '" target="_blank" rel="noopener">' . $this->escape($module->getAuthor()) . '</a>';
                    } else {
                        $author = $this->escape($module->getAuthor());
                    }
                    $moduleInfo = '<b>' . $this->getTrans('name') . ':</b> ' . $this->escape($content['name']) . '<br />
                            <b>' . $this->getTrans('version') . ':</b> ' . $this->escape($module->getVersion()) . '<br />
                            <b>' . $this->getTrans('author') . ':</b> ' . $author . '<br /><br />
                            <b>' . $this->getTrans('requirements') . '</b><br />
                            <b>' . $this->getTrans('ilchCoreVersion') . ':</b> ' . $ilchCore . '<br />
                            <b>' . $this->getTrans('phpVersion') . ':</b> ' . $phpVersion . '<br />';
                    if ($module->getPHPExtension()) {
                        $moduleInfo .= '<b>' . $this->getTrans('phpExtensions') . ':</b> ' . $phpExtension . '<br />';
                    }
                    if ($module->getDepends()) {
                        $moduleInfo .= '<b>' . $this->getTrans('dependencies') . ':</b><br />';

                        foreach ($module->getDepends() as $key => $value) {
                            $moduleInfo .= $key . ' '. str_replace(',','', $value) . '<br />';
                        }
                    }
                    $moduleInfo .= '<br /><b>' . $this->getTrans('desc') . ':</b><br />' . $content['description'];
                    ?>
                    <?=$this->getDialog('infoModal' . $module->getKey(), $this->getTrans('menuModules') . ' ' . $this->getTrans('info'), $moduleInfo) ?>
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
<?php else: ?>
    <?=$this->getTrans('noNotInstalledModules') ?>
<?php endif; ?>

<script>
let delayedShow;

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
});
</script>
