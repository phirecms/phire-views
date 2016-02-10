<?php

namespace Phire\Views\Event;

use Phire\Views\Model;
use Pop\Application;
use Phire\Controller\AbstractController;

class View
{

    /**
     * Init the view model
     *
     * @param  AbstractController $controller
     * @param  Application        $application
     * @return void
     */
    public static function init(AbstractController $controller, Application $application)
    {
        if ($controller->hasView()) {
            $controller->view()->phire->view = new Model\View(['pagination' => $controller->config()->pagination]);
        }
    }

    /**
     * Parse view object
     *
     * @param  AbstractController $controller
     * @param  Application        $application
     * @return void
     */
    public static function parseViews(AbstractController $controller, Application $application)
    {
        if (!($controller instanceof \Phire\Content\Controller\ContentController) && ($controller->hasView())) {
            $body = $controller->response()->getBody();
            if (strpos($body, '[{view_') !== false) {
                // Parse any view placeholders
                $groupIds  = [];
                $singleIds = [];
                $views     = [];

                $viewModel = new Model\View(['pagination' => $controller->config()->pagination]);

                preg_match_all('/\[\{view.*\}\]/', $body, $views);

                if (isset($views[0]) && isset($views[0][0])) {
                    foreach ($views[0] as $view) {

                        $id = substr($view, (strpos($view, 'view_') + 5));
                        $id = str_replace('}]', '', $id);
                        if (strpos($id, '_') !== false) {
                            $idAry = explode('_', $id);
                            $singleIds[] = [
                                'view_id'  => $idAry[0],
                                'model_id' => $idAry[1]
                            ];
                        } else {
                            $groupIds[] = $id;
                        }
                    }
                }

                if (count($groupIds) > 0) {
                    foreach ($groupIds as $id) {
                        $body = str_replace(
                            '[{view_' . $id . '}]', $viewModel->renderPages($id) . PHP_EOL . $viewModel->render($id), $body
                        );
                    }
                }

                if (count($singleIds) > 0) {
                    foreach ($singleIds as $id) {
                        $body = str_replace(
                            '[{view_' . $id['view_id'] . '_' . $id['model_id'] . '}]', $viewModel->renderSingle($id['view_id'], $id['model_id']), $body
                        );
                    }
                }

                $controller->response()->setBody($body);
            }
        }
    }

}