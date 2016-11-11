<?php

namespace SistemaTCC\Controller;

use Silex\Application;

class IndexController {

    public function indexAction(Application $app) {
        return $app['twig']->render('login/index.twig');
    }

    public function creditosAction(Application $app) {

		$token = $app['security.token_storage']->getToken();

		if (null !== $token) {
			return $app->encodePassword($token->getUser(), 'admin');
		}

		return 'admin';
    }

    public function loginAction(Application $app) {

    }

}
