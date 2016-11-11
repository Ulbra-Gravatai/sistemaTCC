<?php

namespace SistemaTCC\Controller;

use Silex\Application;

class IndexController {

    public function indexAction(Application $app) {
        return $app['twig']->render('login/index.twig');
    }

    public function creditosAction(Application $app) {
        return $app['twig']->render('index/creditos.twig');
    }

    public function loginAction(Application $app) {
        return 'Login';
    }

}
