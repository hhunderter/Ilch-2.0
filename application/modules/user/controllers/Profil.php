<?php
/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\User\Controllers;

use Ilch\Comments;
use Modules\User\Config\Config as UserConfig;
use Modules\User\Mappers\User as UserMapper;
use Modules\User\Mappers\Gallery as GalleryMapper;
use Modules\User\Mappers\ProfileFields as ProfileFieldsMapper;
use Modules\User\Mappers\ProfileFieldsContent as ProfileFieldsContentMapper;
use Modules\User\Mappers\ProfileFieldsTranslation as ProfileFieldsTranslationMapper;
use Modules\User\Mappers\Friends as FriendsMapper;
use Modules\Comment\Mappers\Comment as CommentMapper;

class Profil extends \Ilch\Controller\Frontend
{
    public function indexAction()
    {
        $userMapper = new UserMapper();
        $galleryMapper = new GalleryMapper();
        $profileFieldsMapper = new ProfileFieldsMapper();
        $profileFieldsContentMapper = new ProfileFieldsContentMapper();
        $profileFieldsTranslationMapper = new ProfileFieldsTranslationMapper();
        $friendsMapper = new FriendsMapper();

        $profil = ($this->getRequest()->getParam('user') && is_numeric($this->getRequest()->getParam('user'))) ? $userMapper->getUserById($this->getRequest()->getParam('user')) : null;

        if ($profil) {
            $profileIconFields = $profileFieldsMapper->getProfileFields(['type' => 2]);
            $profileFields = $profileFieldsMapper->getProfileFields(['type !=' => 2]);
            $profileFieldsContent = $profileFieldsContentMapper->getProfileFieldContentByUserId($this->getRequest()->getParam('user'));
            $profileFieldsTranslation = $profileFieldsTranslationMapper->getProfileFieldTranslationByLocale($this->getTranslator()->getLocale());
            $commentsOnProfiles = $this->getConfig()->get('user_commentsOnProfiles');

            $this->getLayout()->getHmenu()
                    ->add($this->getTranslator()->trans('menuUserList'), ['controller' => 'index'])
                    ->add($profil->getName(), ['action' => 'index', 'user' => $this->getRequest()->getParam('user')]);

            if ($this->getUser() && $profil->getOptComments() && $profil->getAdminComments() && $commentsOnProfiles) {
                if ($this->getRequest()->getPost('saveComment')) {
                    if (!$this->getRequest()->getPost('comment_text')) {
                        $this->redirect()
                            ->withMessage('emptyCommentText', 'danger')
                            ->to(['action' => 'index', 'user' => $this->getRequest()->getParam('user')]);
                    }

                    $comments = new Comments();
                    $key = sprintf(UserConfig::COMMENT_KEY_TPL, $this->getRequest()->getParam('user'));

                    if ($this->getRequest()->getPost('fkId')) {
                        $key .= '/id_c/' . $this->getRequest()->getPost('fkId');
                    }

                    $comments->saveComment($key, $this->getRequest()->getPost('comment_text'), $this->getUser()->getId());
                    $this->redirect(['action' => 'index', 'user' => $this->getRequest()->getParam('user')]);
                }

                if ($this->getRequest()->getParam('commentId') && ($this->getRequest()->getParam('key') === 'up' || $this->getRequest()->getParam('key') === 'down')) {
                    $commentId = $this->getRequest()->getParam('commentId');
                    $comments = new Comments();

                    $comments->saveVote($commentId, $this->getUser()->getId(), ($this->getRequest()->getParam('key') === 'up'));
                    $this->redirect(['action' => 'index', 'user' => $this->getRequest()->getParam('user') . '#comment_' . $commentId]);
                }
            }

            $this->getView()->set('userMapper', $userMapper);
            $this->getView()->set('profil', $profil);
            $this->getView()->set('profileIconFields', $profileIconFields);
            $this->getView()->set('profileFields', $profileFields);
            $this->getView()->set('profileFieldsContent', $profileFieldsContent);
            $this->getView()->set('profileFieldsTranslation', $profileFieldsTranslation);
            $this->getView()->set('commentsOnProfiles', $commentsOnProfiles);
            $this->getView()->set('galleryAllowed', $this->getConfig()->get('usergallery_allowed'));
            $this->getView()->set('gallery', $galleryMapper->getCountGalleryByUser($this->getRequest()->getParam('user')));
            $this->getView()->set('isFriend', $friendsMapper->hasFriend($this->getUser()->getId(), $profil->getid()));
        } else {
            $this->redirect(['module' => 'error', 'controller' => 'index', 'action' => 'index', 'error' => 'User', 'errorText' => 'notFound']);
        }
    }

    public function deleteCommentAction()
    {
        $commentMapper = new CommentMapper();

        // Check if the request is secure, the user tries to delete a comment on his profile or is an administrator.
        if ($this->getRequest()->isSecure() && (($this->getRequest()->getParam('user') == $this->getUser()->getId()) || $this->getUser()->isAdmin())) {
            $commentMapper->delete($this->getRequest()->getParam('id'));

            $this->redirect()
                ->withMessage('deleteSuccess')
                ->to(['action' => 'index', 'user' => $this->getRequest()->getParam('user')]);
        }

        $this->redirect()
            ->to(['action' => 'index', 'user' => $this->getRequest()->getParam('user')]);
    }
}
