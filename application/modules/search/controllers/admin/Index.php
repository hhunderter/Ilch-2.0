<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Controllers\Admin;

use Modules\Search\Mappers\Search as SearchMapper;
use Ilch\Validation;

class Index extends \Ilch\Controller\Admin
{
    public function init()
    {
        $items = [
            [
                'name' => 'menuSearch',
                'active' => true,
                'icon' => 'fa-solid fa-magnifying-glass',
                'url' => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'index'])
            ],
            [
                'name' => 'menuSettings',
                'active' => false,
                'icon' => 'fa-solid fa-gears',
                'url'  => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'index'])
            ]
        ];

        $this->getLayout()->addMenu(
            'menuSearch',
            $items
        );
    }

    public function indexAction()
    {
        $searchMapper = new SearchMapper($this->getView(), $this->getUser(), true);
        if ($this->getRequest()->getParam('uid')) {
            $searchMapper->backResult($this->getRequest()->getParam('uid'));
        }

        $this->getLayout()->getAdminHmenu()
            ->add($this->getTranslator()->trans('menuSearch'), ['controller' => 'index', 'action' => 'index']);

        if ($this->getRequest()->isPost()) {
            Validation::setCustomFieldAliases([
                'search_text' => 'search',
            ]);

            $validation = Validation::create($this->getRequest()->getPost(), [
                'search_text' => 'required',
                'search_options' => 'required',
            ]);

            if ($validation->isValid()) {
                $searchMapper->checkDays($this->getRequest()->getPost('search_days'));
                $searchMapper->makeSearch($this->getRequest()->getPost('search_text'), $this->getRequest()->getPost('search_options'));

                $resultModel = $searchMapper->saveResult();
                $this->redirect(array_merge(['action' => 'index'], $resultModel ? ['uid' => $resultModel->getUid()] : []));
            }
            $this->addMessage($validation->getErrorBag()->getErrorMessages(), 'danger', true);
            $this->redirect()
                ->withInput()
                ->withErrors($validation->getErrorBag())
                ->to(['action' => 'index']);
        }

        $this->getView()->set('searchMapper', $searchMapper);
    }
}
