<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Search\Controllers;

use Modules\Search\Mappers\Search as SearchMapper;
use Ilch\Validation;

class Index extends \Ilch\Controller\Frontend
{
    public function indexAction()
    {
        $searchMapper = new SearchMapper($this->getView(), $this->getUser());
        if ($this->getRequest()->getParam('uid')) {
            $searchMapper->backResult($this->getRequest()->getParam('uid'));
        }

        $this->getLayout()->getHmenu()
            ->add($this->getTranslator()->trans('menuSearch'), ['action' => 'index']);

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
