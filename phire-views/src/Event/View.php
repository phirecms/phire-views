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
            $controller->view()->phire->view = new Model\View();
        }
    }

}