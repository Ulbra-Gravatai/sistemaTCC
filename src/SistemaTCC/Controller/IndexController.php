<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController {

    public function indexAction(Application $app, Request $request) {
        return $app['twig']->render('login/index.twig', [
			'erroDeLogin' => $app['security.last_error']($request)
		]);
    }

    public function creditosAction(Application $app) {
		return $app['twig']->render('index/creditos.twig');
    }

    public function gerarSenhaAction(Application $app) {

		$token = $app['security.token_storage']->getToken();

		if (null !== $token) {
			return $app->encodePassword($token->getUser(), 'admin');
		}

		return 'admin';
    }

}
