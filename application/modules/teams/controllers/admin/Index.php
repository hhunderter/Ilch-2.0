<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Teams\Controllers\Admin;

use Modules\Teams\Mappers\Teams as TeamsMapper;
use Modules\Teams\Models\Teams as TeamsModel;
use Modules\User\Mappers\Notifications as NotificationsMapper;
use Modules\User\Mappers\User as UserMapper;
use Modules\User\Mappers\Group as UserGroupMapper;
use Ilch\Validation;
use Modules\User\Models\Notification as NotificationModel;

class Index extends \Ilch\Controller\Admin
{
    public function init()
    {
        $items = [
            [
                'name' => 'manage',
                'active' => false,
                'icon' => 'fa-solid fa-table-list',
                'url' => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'index']),
                [
                    'name' => 'add',
                    'active' => false,
                    'icon' => 'fa-solid fa-circle-plus',
                    'url' => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'treat'])
                ]
            ],
            [
                'name' => 'applications',
                'active' => false,
                'icon' => 'fa-solid fa-table-list',
                'url' => $this->getLayout()->getUrl(['controller' => 'applications', 'action' => 'index'])
            ],
            [
                'name' => 'settings',
                'active' => false,
                'icon' => 'fa-solid fa-gears',
                'url' => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'index'])
            ]
        ];

        if ($this->getRequest()->getActionName() === 'treat') {
            $items[0][0]['active'] = true;
        } else {
            $items[0]['active'] = true;
        }

        $this->getLayout()->addMenu(
            'menuTeams',
            $items
        );
    }

    public function indexAction()
    {
        $teamsMapper = new TeamsMapper();

        $this->getLayout()->getAdminHmenu()
            ->add($this->getTranslator()->trans('menuTeams'), ['action' => 'index'])
            ->add($this->getTranslator()->trans('manage'), ['action' => 'index']);

        if ($this->getRequest()->getPost('check_teams') && $this->getRequest()->getPost('action') === 'delete') {
            foreach ($this->getRequest()->getPost('check_teams') as $teamId) {
                $teamsMapper->delete($teamId);
            }
        }

        if ($this->getRequest()->getPost('saveTeams') && !empty($this->getRequest()->getPost('items'))) {
            foreach ($this->getRequest()->getPost('items') as $pos => $teamId) {
                $teamsMapper->sort($teamId, $pos);
            }

            $this->redirect()
                ->withMessage('saveSuccess')
                ->to(['action' => 'index']);
        }

        $this->getView()->set('teams', $teamsMapper->getTeams());
    }

    public function treatAction()
    {
        $teamsMapper = new TeamsMapper();
        $userMapper = new UserMapper();
        $userGroupMapper = new UserGroupMapper();

        if ($this->getRequest()->getParam('id')) {
            $this->getLayout()->getAdminHmenu()
                ->add($this->getTranslator()->trans('menuTeams'), ['action' => 'index'])
                ->add($this->getTranslator()->trans('edit'), ['action' => 'treat']);

            $model = $teamsMapper->getTeamById($this->getRequest()->getParam('id'));

            if (!$model) {
                $this->redirect(['action' => 'index']);
            }
        } else {
            $this->getLayout()->getAdminHmenu()
                ->add($this->getTranslator()->trans('menuTeams'), ['action' => 'index'])
                ->add($this->getTranslator()->trans('add'), ['action' => 'treat']);

            $model = new TeamsModel();
        }
        $this->getView()->set('team', $model);

        if ($this->getRequest()->isPost()) {
            Validation::setCustomFieldAliases([
                'name' => 'teamName',
            ]);

            $validation = Validation::create($this->getRequest()->getPost(), [
                'name' => 'required|unique:teams,name,' . $model->getId(),
                'groupId' => 'required|numeric|integer|min:1',
                'optIn' => 'required|numeric|integer|min:0|max:1',
                'notifyLeader' => 'required|numeric|integer|min:0|max:1'
            ]);

            // No need to check if the leader and coleader are identical if one of them is empty.
            if (!empty($this->getRequest()->getPost('leader')) && !empty($this->getRequest()->getPost('coLeader'))) {
                if (count(array_intersect($this->getRequest()->getPost('leader'), $this->getRequest()->getPost('coLeader')))) {
                    $validation->getErrorBag()->addError('coLeader', $this->getTranslator()->trans('leaderCoLeaderIdentic'));
                }
            }

            if ($validation->isValid()) {
                if ($this->getRequest()->getPost('image_delete') != '') {
                    $teamsMapper->delImageById($model, true);
                }

                if ($_FILES['img']['name']) {
                    $allowedFiletypes = $this->getConfig()->get('teams_filetypes');
                    $imageMaxHeight = $this->getConfig()->get('teams_height');
                    $imageMaxWidth = $this->getConfig()->get('teams_width');
                    $path = $this->getConfig()->get('teams_uploadpath');
                    $file = $_FILES['img']['name'];
                    $file_tmpe = $_FILES['img']['tmp_name'];
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $imageInfo = getimagesize($file_tmpe);

                    if (strncmp($imageInfo['mime'], 'image/', 6) === 0 && in_array($extension, explode(' ', $allowedFiletypes))) {
                        if ($model->getId()) {
                            $teamsMapper->delImageById($model, true);
                        }

                        $width = $imageInfo[0];
                        $height = $imageInfo[1];
                        do {
                            $newName = str_replace('.', '', uniqid(mt_rand(), true));
                            $image = $path . $newName . '.' . $extension;
                        } while (file_exists($image));

                        if (move_uploaded_file($file_tmpe, $image)) {
                            if ($width > $imageMaxWidth || $height > $imageMaxHeight) {
                                $upload = new \Ilch\Upload();

                                if ($upload->enoughFreeMemory($image)) {
                                    $thumb = new \Thumb\Thumbnail();
                                    $calcHeight = $height;
                                    $calcWidth = $width;

                                    // adjust height first to max height
                                    if ($calcHeight > $imageMaxHeight) {
                                        $calcWidth = $calcWidth / $calcHeight * $imageMaxHeight;
                                        $calcHeight = $imageMaxHeight;
                                    }

                                    // now adjust width to max width
                                    if ($calcWidth > $imageMaxWidth) {
                                        $calcHeight = $calcHeight / $calcWidth * $imageMaxWidth;
                                        $calcWidth = $imageMaxWidth;
                                    }

                                    $thumb -> Thumbheight = $calcHeight;
                                    $thumb -> Thumbwidth = $calcWidth;
                                    $thumb -> Thumblocation = $path;
                                    $thumb -> Createthumb($image, 'file');
                                } else {
                                    unlink($image);
                                    $this->addMessage('failedFilesize', 'warning');
                                }
                            }

                            $model->setImg($image);
                        }
                    } else {
                        $this->addMessage('failedFiletypes', 'warning');
                    }
                }

                $leader = '';
                $coLeader = '';

                if (!empty($this->getRequest()->getPost('leader'))) {
                    $leader = implode(',', $this->getRequest()->getPost('leader'));
                }

                if (!empty($this->getRequest()->getPost('coLeader'))) {
                    $coLeader = implode(',', $this->getRequest()->getPost('coLeader'));
                }

                $model->setName($this->getRequest()->getPost('name'))
                    ->setLeader($leader)
                    ->setCoLeader($coLeader)
                    ->setGroupId($this->getRequest()->getPost('groupId'))
                    ->setOptShow($this->getRequest()->getPost('optShow'))
                    ->setOptIn($this->getRequest()->getPost('optIn'))
                    ->setNotifyLeader($this->getRequest()->getPost('notifyLeader'));
                $teamsMapper->save($model);

                // Notify all users of the team.
                if ($this->getConfig()->get('teams_userNotification')) {
                    // Notify all users of the team.
                    $notificationsMapper = new NotificationsMapper();
                    $notificationModels = [];
                    $users = $userMapper->getUserListByGroupId($model->getGroupId(), 1);

                    foreach ($users as $user) {
                        $notificationModel = new NotificationModel();
                        $notificationModel->setUserId($user->getId())
                            ->setModule('teams')
                            ->setMessage($this->getTranslator()->trans('teamsAddedToTeam', $model->getName()))
                            ->setURL($this->getLayout()->getUrl(['module' => 'teams', 'controller' => 'index', 'action' => 'team', 'id' => $model->getId()], ''))
                            ->setType('teamsAddedToTeam');
                        $notificationModels[] = $notificationModel;
                    }

                    $notificationsMapper->addNotifications($notificationModels);
                }

                $this->redirect()
                    ->withMessage('saveSuccess')
                    ->to(['action' => 'index']);
            }
            $this->addMessage($validation->getErrorBag()->getErrorMessages(), 'danger', true);
            $this->redirect()
                ->withInput()
                ->withErrors($validation->getErrorBag())
                ->to(['action' => 'treat']);
        }

        $this->getView()->set('userList', $userMapper->getUserList())
            ->set('userGroupList', $userGroupMapper->getGroupList());
    }


    public function updateAction()
    {
        $teamsMapper = new TeamsMapper();
        if ($this->getRequest()->isSecure()) {
            $teamsMapper->updateShow($this->getRequest()->getParam('id'));

            $this->addMessage('saveSuccess');
        }

        $this->redirect(['action' => 'index']);
    }

    public function delAction()
    {
        if ($this->getRequest()->isSecure()) {
            $teamsMapper = new TeamsMapper();
            $teamsMapper->delete($this->getRequest()->getParam('id'));

            $this->addMessage('deleteSuccess');
        }

        $this->redirect(['action' => 'index']);
    }
}
