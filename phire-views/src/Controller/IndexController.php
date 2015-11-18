<?php

namespace Phire\Views\Controller;

use Phire\Views\Model;
use Phire\Views\Form;
use Phire\Views\Table;
use Phire\Controller\AbstractController;
use Pop\Paginator\Paginator;

class IndexController extends AbstractController
{

    /**
     * Index action method
     *
     * @return void
     */
    public function index()
    {
        $view = new Model\View();

        if ($view->hasPages($this->config->pagination)) {
            $limit = $this->config->pagination;
            $pages = new Paginator($view->getCount(), $limit);
            $pages->useInput(true);
        } else {
            $limit = null;
            $pages = null;
        }

        $this->prepareView('views/index.phtml');
        $this->view->title = 'Views';
        $this->view->pages = $pages;
        $this->view->views = $view->getAll(
            $limit, $this->request->getQuery('page'), $this->request->getQuery('sort')
        );

        $this->send();
    }

    /**
     * Add action method
     *
     * @return void
     */
    public function add()
    {
        $this->prepareView('views/add.phtml');
        $this->view->title = 'Views : Add';

        $fields = $this->application->config()['forms']['Phire\Views\Form\View'];

        $models = $this->application->module('phire-fields')->config()['models'];
        foreach ($models as $model => $type) {
            $fields[4]['model_1']['value'][$model] = $model;
        }

        $flds = \Phire\Fields\Table\Fields::findAll();

        foreach ($flds->rows() as $f) {
            $fields[2]['group_fields']['value'][$f->id] = $f->name;
            $fields[3]['single_fields']['value'][$f->id] = $f->name;
        }

        $this->view->form = new Form\View($fields);

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                 ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
                 ->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();
                $view = new Model\View();
                $view->save($this->view->form->getFields());
                $this->view->id = $view->id;
                $this->sess->setRequestValue('saved', true);
                $this->redirect(BASE_PATH . APP_URI . '/views/edit/' . $view->id);
            }
        }

        $this->send();
    }

    /**
     * Edit action method
     *
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {
        $view = new Model\View();
        $view->getById($id);

        $fields = $this->application->config()['forms']['Phire\Views\Form\View'];

        $this->prepareView('views/edit.phtml');
        $this->view->title     = 'Views';
        $this->view->view_name = $view->name;

        $fields[1]['name']['attributes']['onkeyup'] = 'phire.changeTitle(this.value);';

        $models = $this->application->module('phire-fields')->config()['models'];
        foreach ($models as $model => $type) {
            $fields[4]['model_1']['value'][$model] = $model;
        }

        $flds = \Phire\Fields\Table\Fields::findAll();

        foreach ($flds->rows() as $f) {
            $fields[2]['group_fields']['value'][$f->id] = $f->name;
            $fields[3]['single_fields']['value'][$f->id] = $f->name;
        }

        $this->view->form = new Form\View($fields);
        $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
             ->setFieldValues($view->toArray());

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                 ->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();
                $view = new Model\View();
                $view->update($this->view->form->getFields());
                $this->view->id = $view->id;
                $this->sess->setRequestValue('saved', true);
                $this->redirect(BASE_PATH . APP_URI . '/views/edit/' . $view->id);
            }
        }

        $this->send();
    }

    /**
     * JSON action method
     *
     * @param  int $id
     * @return void
     */
    public function json($id)
    {
        $json = [];
        $view = Table\Views::findById($id);

        if (isset($view->id)) {
            $json['models'] = (null != $view->models) ? unserialize($view->models) : [];
        }

        $this->response->setBody(json_encode($json, JSON_PRETTY_PRINT));
        $this->send(200, ['Content-Type' => 'application/json']);
    }

    /**
     * Remove action method
     *
     * @return void
     */
    public function remove()
    {
        if ($this->request->isPost()) {
            $view = new Model\View();
            $view->remove($this->request->getPost());
        }
        $this->sess->setRequestValue('removed', true);
        $this->redirect(BASE_PATH . APP_URI . '/views');
    }

    /**
     * Prepare view
     *
     * @param  string $template
     * @return void
     */
    protected function prepareView($template)
    {
        $this->viewPath = __DIR__ . '/../../view';
        parent::prepareView($template);
    }

}
