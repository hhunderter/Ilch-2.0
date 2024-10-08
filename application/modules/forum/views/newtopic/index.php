<?php

/** @var \Ilch\View $this */

/** @var \Modules\Forum\Models\ForumItem $forum */
$forum = $this->get('forum');
/** @var \Modules\Forum\Models\ForumItem $cat */
$cat = $this->get('cat');
/** @var \Modules\Forum\Models\Prefix[] $prefixes */
$prefixes = $this->get('prefixes');

$adminAccess = null;
if ($this->getUser()) {
    $adminAccess = $this->getUser()->isAdmin();
}
?>

<link href="<?=$this->getModuleUrl('static/css/forum.css') ?>" rel="stylesheet">

<?php if ($adminAccess || $forum->getCreateAccess()) : ?>
    <div id="forum">
        <h1>
            <a href="<?=$this->getUrl(['controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('forum') ?></a>
            <i class="fa-solid fa-chevron-right"></i> <a href="<?=$this->getUrl(['controller' => 'showcat', 'action' => 'index', 'id' => $cat->getId()]) ?>"><?=$cat->getTitle() ?></a>
            <i class="fa-solid fa-chevron-right"></i> <a href="<?=$this->getUrl(['controller' => 'showtopics', 'action' => 'index', 'forumid' => $forum->getId()]) ?>"><?=$forum->getTitle() ?></a>
            <i class="fa-solid fa-chevron-right"></i> <?=$this->getTrans('newTopicTitle') ?>
        </h1>
        <div class="row">
            <div class="col-xl-12">
                <div class="new-post-head ilch-head">
                    <?=$this->getTrans('createNewTopic') ?>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="new-topic ilch-bg ilch-border">
                    <form method="POST">
                        <?=$this->getTokenField() ?>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row mb-3<?=$this->validation()->hasError('topicTitle') ? ' has-error' : '' ?>">
                                    <label for="topicTitle" class="col-lg-2 col-form-label">
                                         <?=$this->getTrans('topicTitle') ?>
                                    </label>
                                    <?php if ($forum->getPrefixes() != '') : ?>
                                        <?php $prefixIds = explode(',', $forum->getPrefixes()); ?>
                                        <?php array_unshift($prefixIds, ''); ?>
                                        <div class="col-xl-2 prefix">
                                            <select class="form-select" id="topicPrefix" name="topicPrefix">
                                                <?php foreach ($prefixIds as $prefixId) : ?>
                                                    <?php $selected = ''; ?>
                                                    <?php if ($prefixId == $this->originalInput('topicPrefix')) : ?>
                                                        <?php $selected = 'selected="selected"'; ?>
                                                    <?php endif; ?>
                                                    <?php if ($prefixId) : ?>
                                                        <option <?=$selected ?> value="<?=$prefixId ?>"><?=$this->escape($prefixes[$prefixId]->getPrefix()) ?></option>
                                                    <?php else : ?>
                                                        <option <?=$selected ?> value="0"></option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-xl-5">
                                        <input type="text"
                                               class="form-control"
                                               id="topicTitle"
                                               name="topicTitle"
                                               value="<?=$this->originalInput('topicTitle') ?>" />
                                    </div>
                                </div>
                                <div class="row mb-3<?=$this->validation()->hasError('text') ? ' has-error' : '' ?>">
                                    <label class="col-xl-2 col-form-label" for="ck_1">
                                        <?=$this->getTrans('text') ?>
                                    </label>
                                    <div class="col-xl-10">
                                    <textarea class="form-control ckeditor"
                                              id="ck_1"
                                              name="text"
                                              toolbar="ilch_html_frontend"><?=$this->originalInput('text') ?></textarea>
                                    </div>
                                </div>
                                <?php if ($this->getUser()->isAdmin()) : ?>
                                    <div class="row mb-3">
                                        <div class="col-xl-2 col-form-label">
                                            <?=$this->getTrans('forumOptions') ?>
                                        </div>
                                        <div class="col-xl-10">
                                            <input type="checkbox"
                                                   id="fix"
                                                   name="fix"
                                                   value="1"
                                                   <?=($this->originalInput('fix')) ? 'checked' : '' ?> />
                                            <label for="fix">
                                                <?=$this->getTrans('forumTypeFixed') ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="row mb-3">
                                    <div class="offset-xl-2 col-xl-8">
                                        <input type="submit"
                                               class="btn btn-sm btn-primary"
                                               name="saveNewTopic"
                                               value="<?=$this->getTrans('add') ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?=$this->getDialog('mediaModal', $this->getTrans('media'), '<iframe frameborder="0"></iframe>') ?>
<?php else : ?>
    <?php
    header('location: ' . $this->getUrl(['controller' => 'index', 'action' => 'index', 'access' => 'noaccess']));
    exit;
    ?>
<?php endif; ?>
