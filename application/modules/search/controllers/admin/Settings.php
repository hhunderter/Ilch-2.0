<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Controllers\Admin;

use Modules\Search\Mappers\Search as SearchMapper;
use Ilch\Validation;

class Settings extends \Ilch\Controller\Admin
{
    public function init()
    {
        $items = [
            [
                'name' => 'menuSearch',
                'active' => false,
                'icon' => 'fa-solid fa-magnifying-glass',
                'url' => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'index'])
            ],
            [
                'name' => 'menuSettings',
                'active' => true,
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
        $searchMapper = new SearchMapper($this->getView());

        $this->getLayout()->getAdminHmenu()
            ->add($this->getTranslator()->trans('menuSearch'), ['controller' => 'index', 'action' => 'index'])
            ->add($this->getTranslator()->trans('menuSettings'), ['action' => 'index']);

        if ($this->getRequest()->isPost()) {
            $validation = Validation::create($this->getRequest()->getPost(), [
                'modules' => 'required',
            ]);

            if ($validation->isValid()) {
                $modules = implode(',', $this->getRequest()->getPost('modules'));
                $this->getConfig()->set('search_boxmodule', $modules);

                $this->redirect()
                    ->withMessage('saveSuccess')
                    ->to(['action' => 'index']);
            }
            $this->addMessage($validation->getErrorBag()->getErrorMessages(), 'danger', true);
            $this->redirect()
                ->withInput()
                ->withErrors($validation->getErrorBag())
                ->to(['action' => 'index']);
        }
        $this->getView()->set('searchMapper', $searchMapper);
        $this->getView()->set('modules', explode(",", $this->getConfig()->get('search_boxmodule') ?? 'forum'));
    }
}
