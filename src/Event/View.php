<?php
/**
 * Phire Views Module
 *
 * @link       https://github.com/phirecms/phire-views
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Phire\Views\Event;

use Phire\Views\Model;
use Pop\Application;
use Phire\Controller\AbstractController;

/**
 * View Event class
 *
 * @category   Phire\Views
 * @package    Phire\Views
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 * @version    1.0.0
 */
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
        if (!($controller instanceof \Phire\Content\Controller\ContentController) &&
            !($controller instanceof \Phire\Templates\Controller\IndexController) && ($controller->hasView())) {
            $body = $controller->response()->getBody();

            if (strpos($body, '[{view_') !== false) {
                // Parse any view placeholders
                $groupIds  = [];
                $singleIds = [];
                $modelId   = null;
                $views     = [];

                $viewModel = new Model\View(['pagination' => $controller->config()->pagination]);

                preg_match_all('/\[\{view.*\}\]/', $body, $views);

                if (isset($views[0]) && isset($views[0][0])) {
                    foreach ($views[0] as $view) {

                        $id = substr($view, (strpos($view, 'view_') + 5));
                        $id = str_replace('}]', '', $id);
                        if (strpos($id, '_') !== false) {
                            $idAry = explode('_', $id);
                            if (!empty($idAry[1])) {
                                $modelId = $idAry[1];
                            } else if (!empty($controller->view()->id)) {
                                $modelId = $controller->view()->id;
                            }
                            $singleIds[] = [
                                'view_id'  => $idAry[0],
                                'model_id' => $modelId
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
                        if (null !== $id['model_id']) {
                            $body = str_replace(
                                '[{view_' . $id['view_id'] . '_' . $id['model_id'] . '}]', $viewModel->renderSingle($id['view_id'], $id['model_id']), $body
                            );
                            $body = str_replace(
                                '[{view_' . $id['view_id'] . '_}]', $viewModel->renderSingle($id['view_id'], $id['model_id']), $body
                            );
                        }
                    }
                }

                $controller->response()->setBody($body);
            }
        }
    }

}