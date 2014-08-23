<?php

namespace Eva\EvaLivenews\Controllers\Admin;

use Eva\EvaLivenews\Models;

/**
* @resourceName("Livenews Category Managment")
* @resourceDescription("Livenews Category Managment")
*/
class CategoryController extends ControllerBase
{
    /**
    * @operationName("Livenews Category List")
    * @operationDescription("Livenews Category List")
    */
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int'); // GET
        $limit = $this->request->getQuery('limit', 'int');
        $order = $this->request->getQuery('order', 'int');
        $limit = $limit > 50 ? 50 : $limit;
        $limit = $limit < 10 ? 10 : $limit;

        $items = $this->modelsManager->createBuilder()
            ->from('Eva\EvaLivenews\Models\Category')
            ->orderBy('id DESC');

        $paginator = new \Eva\EvaEngine\Paginator(array(
            "builder" => $items,
            "limit"=> 500,
            "page" => $currentPage
        ));
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

    /**
    * @operationName("Create Livenews Category")
    * @operationDescription("Create Livenews Category")
    */
    public function createAction()
    {
        $form = new \Eva\EvaLivenews\Forms\CategoryForm();
        $category = new Models\Category();
        $form->setModel($category);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost()) {
            return false;
        }

        $form->bind($this->request->getPost(), $category);
        if (!$form->isValid()) {
            return $this->showInvalidMessages($form);
        }
        $category = $form->getEntity();
        try {
            $category->createCategory();
        } catch (\Exception $e) {
            return $this->showException($e, $category->getMessages());
            //return $this->response->redirect($this->getDI()->getConfig()->user->registerFailedRedirectUri);
        }
        $this->flashSession->success('SUCCESS_BLOG_CATEGORY_CREATED');

        return $this->redirectHandler('/admin/livenews/category/edit/' . $category->id);
    }

    /**
    * @operationName("Edit Livenews Category")
    * @operationDescription("Edit Livenews Category")
    */
    public function editAction()
    {
        $this->view->changeRender('admin/category/create');

        $form = new \Eva\EvaLivenews\Forms\CategoryForm();
        $category = Models\Category::findFirst($this->dispatcher->getParam('id'));
        $form->setModel($category ? $category : new Models\Category());
        $this->view->setVar('form', $form);
        $this->view->setVar('item', $category);
        if (!$this->request->isPost()) {
            return false;
        }

        $form->bind($this->request->getPost(), $category);
        if (!$form->isValid()) {
            return $this->showInvalidMessages($form);
        }
        $category = $form->getEntity();
        $category->assign($this->request->getPost());
        try {
            $category->updateCategory();
        } catch (\Exception $e) {
            return $this->showException($e, $category->getMessages());
        }
        $this->flashSession->success('SUCCESS_BLOG_CATEGORY_UPDATED');

        return $this->redirectHandler('/admin/livenews/category/edit/' . $category->id);
    }

    /**
    * @operationName("Remove Livenews Category")
    * @operationDescription("Remove Livenews Category")
    */
    public function deleteAction()
    {
        if (!$this->request->isDelete()) {
            $this->response->setStatusCode('405', 'Method Not Allowed');
            $this->response->setContentType('application/json', 'utf-8');

            return $this->response->setJsonContent(array(
                'errors' => array(
                    array(
                        'code' => 405,
                        'message' => 'ERR_POST_REQUEST_METHOD_NOT_ALLOW'
                    )
                ),
            ));
        }

        $id = $this->dispatcher->getParam('id');
        $category =  Models\Category::findFirst($id);
        try {
            $category->delete();
        } catch (\Exception $e) {
            return $this->showExceptionAsJson($e, $category->getMessages());
        }

        $this->response->setContentType('application/json', 'utf-8');

        return $this->response->setJsonContent($category);
    }
}
