<?php

namespace SistemaTCC\Application;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\TwigServiceProvider;
use SistemaTCC\Provider\DoctrineOrmServiceProvider;
use SistemaTCC\Provider\UserProvider;
use Silex\Provider\ValidatorServiceProvider;

class SistemaTCC extends Application {

	use Application\UrlGeneratorTrait;
	use Application\SecurityTrait;

	public function __construct() {

		parent::__construct();

		$app = $this;

		Request::enableHttpMethodParameterOverride();

		$app['debug'] = true;

		// Provider
		$this->register(new DoctrineOrmServiceProvider());
		$this->register(new SessionServiceProvider());
		$this->register(new SecurityServiceProvider(), ['security.firewalls' => [
			'admin' => [
				'pattern' => '^/.+',
				'form' => ['login_path' => '/', 'check_path' => '/login/'],
				'logout' => ['logout_path' => '/logout/', 'invalidate_session' => true],
				'users' => function () use ($app) {
					return new UserProvider($app['orm']->getConnection());
				}
			]
		]]);
		$app->register(new TwigServiceProvider(), ['twig.path' => __DIR__ . '/../View/']);

		// Validator
		$app->register(new ValidatorServiceProvider());

		// Controller
		$app->get('/', "\\SistemaTCC\\Controller\\IndexController::indexAction")->bind('/');
		$app->get('/creditos/', "\\SistemaTCC\\Controller\\IndexController::creditosAction");

		$app->get('/aluno/', "\\SistemaTCC\\Controller\\AlunoController::indexAction");
		$app->get('/aluno/cadastrar/', "\\SistemaTCC\\Controller\\AlunoController::cadastrarAction");
		$app->get('/aluno/editar/{id}/', "\\SistemaTCC\\Controller\\AlunoController::editarAction");
		$app->get('/aluno/excluir/', "\\SistemaTCC\\Controller\\AlunoController::excluirAction");
		$app->get('/aluno/listar/', "\\SistemaTCC\\Controller\\AlunoController::listarAction");

		$app->get('/professor/', "\\SistemaTCC\\Controller\\ProfessorController::indexAction");
		$app->get('/professor/cadastrar/', "\\SistemaTCC\\Controller\\ProfessorController::cadastrarAction");
		$app->get('/professor/editar/{id}', "\\SistemaTCC\\Controller\\ProfessorController::editarAction");
		// $app->get('/professor/excluir/', "\\SistemaTCC\\Controller\\ProfessorController::excluirAction");
		$app->get('/professor/listar/', "\\SistemaTCC\\Controller\\ProfessorController::listarAction");

		$app->get('/semestre/', "\\SistemaTCC\\Controller\\SemestreController::indexAction");
		$app->get('/semestre/cadastrar/', "\\SistemaTCC\\Controller\\SemestreController::cadastrarAction");
		$app->get('/semestre/editar/{id}/', "\\SistemaTCC\\Controller\\SemestreController::editarAction");
		$app->get('/semestre/excluir/', "\\SistemaTCC\\Controller\\SemestreController::excluirAction");
		$app->get('/semestre/listar/', "\\SistemaTCC\\Controller\\SemestreController::listarAction");

		$app->get('/etapa-semestre/', "\\SistemaTCC\\Controller\\EtapaSemestreController::indexAction");
		$app->get('/etapa-semestre/cadastrar/', "\\SistemaTCC\\Controller\\EtapaSemestreController::cadastrarAction");
		$app->get('/etapa-semestre/editar/{id}/', "\\SistemaTCC\\Controller\\EtapaSemestreController::editarAction");
		$app->get('/etapa-semestre/excluir/', "\\SistemaTCC\\Controller\\EtapaSemestreController::excluirAction");
		$app->get('/etapa-semestre/listar/', "\\SistemaTCC\\Controller\\EtapaSemestreController::listarAction");


		$app->get('/tcc/', "\\SistemaTCC\\Controller\\TccController::indexAction");
		$app->get('/tcc/cadastrar/', "\\SistemaTCC\\Controller\\TccController::cadastrarAction");
		$app->get('/tcc/editar/', "\\SistemaTCC\\Controller\\TccController::editarAction");
		$app->get('/tcc/excluir/', "\\SistemaTCC\\Controller\\TccController::excluirAction");
		$app->get('/tcc/listar/', "\\SistemaTCC\\Controller\\TccController::listarAction");

		$app->get('/campus/', "\\SistemaTCC\\Controller\\CampusController::indexAction");
		$app->get('/campus/cadastrar/', "\\SistemaTCC\\Controller\\CampusController::cadastrarAction");
		$app->get('/campus/editar/{id}/', "\\SistemaTCC\\Controller\\CampusController::editarAction");
		$app->get('/campus/listar/', "\\SistemaTCC\\Controller\\CampusController::listarAction");

		$app->get('/etapatipo/', "\\SistemaTCC\\Controller\\EtapaTipoController::indexAction");
		$app->get('/etapatipo/cadastrar/', "\\SistemaTCC\\Controller\\EtapaTipoController::cadastrarAction");
		$app->get('/etapatipo/editar/{id}/', "\\SistemaTCC\\Controller\\EtapaTipoController::editarAction");
		$app->get('/etapatipo/listar/', "\\SistemaTCC\\Controller\\EtapaTipoController::listarAction");

		$app->get('/enviaretapa/', "\\SistemaTCC\\Controller\\EnviarEtapaController::indexAction");
		$app->get('/enviaretapa/listar/', "\\SistemaTCC\\Controller\\EnviarEtapaController::listarAction");
		$app->get('/enviaretapa/enviar/{id}/', "\\SistemaTCC\\Controller\\EnviarEtapaController::enviarAction");

		$app->get('/areadeinteresse/', "\\SistemaTCC\\Controller\\AreaDeInteresseController::indexAction");
		$app->get('/areadeinteresse/cadastrar/', "\\SistemaTCC\\Controller\\AreaDeInteresseController::cadastrarAction");
		$app->get('/areadeinteresse/editar/{id}/', "\\SistemaTCC\\Controller\\AreaDeInteresseController::editarAction");
		$app->get('/areadeinteresse/listar/', "\\SistemaTCC\\Controller\\AreaDeInteresseController::listarAction");

		// REST Aluno
		$app->post('/aluno/', "\\SistemaTCC\\Controller\\AlunoController::add");
		$app->get('/aluno/{id}/', "\\SistemaTCC\\Controller\\AlunoController::find");
		$app->put('/aluno/{id}/', "\\SistemaTCC\\Controller\\AlunoController::edit");
		$app->delete('/aluno/{id}/', "\\SistemaTCC\\Controller\\AlunoController::del");

		// REST Professor
		$app->post('/professor/', "\\SistemaTCC\\Controller\\ProfessorController::add");
		$app->get('/professor/{id}/', "\\SistemaTCC\\Controller\\ProfessorController::find");
		$app->put('/professor/{id}/', "\\SistemaTCC\\Controller\\ProfessorController::edit");
		$app->delete('/professor/{id}/', "\\SistemaTCC\\Controller\\ProfessorController::del");

		// REST Enviar Etapa
		$app->post('/enviaretapa/',"\\SistemaTCC\\Controller\\EnviarEtapaController::add");
		$app->delete('/enviaretapa/{id}/', "\\SistemaTCC\\Controller\\EnviarEtapaController::del");

		// REST Etapa Status
		$app->post('/etapa-status/', "SistemaTCC\Controller\EtapaStatusController::add");
		$app->get('/etapa-status/', "SistemaTCC\Controller\EtapaStatusController::all");
		$app->get('/etapa-status/{id}/', "SistemaTCC\Controller\EtapaStatusController::find");
		$app->put('/etapa-status/{id}/', "SistemaTCC\Controller\EtapaStatusController::edit");
		$app->delete('/etapa-status/{id}/', "SistemaTCC\Controller\EtapaStatusController::del");

		// REST semestre
		$app->post('/semestre/', "\\SistemaTCC\\Controller\\SemestreController::add");
		$app->get('/semestre/{id}/', "\\SistemaTCC\\Controller\\SemestreController::find");
		$app->put('/semestre/{id}/', "\\SistemaTCC\\Controller\\SemestreController::edit");
		$app->delete('/semestre/{id}/', "\\SistemaTCC\\Controller\\SemestreController::del");

		// REST Etapa Semestre
		$app->post('/etapa-semestre/', "\\SistemaTCC\\Controller\\EtapaSemestreController::add");
		$app->get('/etapa-semestre/', "\\SistemaTCC\\Controller\\EtapaSemestreController::all");
		$app->get('/etapa-semestre/{id}/', "\\SistemaTCC\\Controller\\EtapaSemestreController::find");
		$app->put('/etapa-semestre/{id}/', "\\SistemaTCC\\Controller\\EtapaSemestreController::edit");
		$app->delete('/etapa-semestre/{id}/', "\\SistemaTCC\\Controller\\EtapaSemestreController::del");

		// REST tcc
		$app->post('/tcc/', "\\SistemaTCC\\Controller\\TccController::add");
		$app->put('/tcc/{id}/', "\\SistemaTCC\\Controller\\TccController::edit");
		$app->delete('/tcc/{id}/', "\\SistemaTCC\\Controller\\TccController::del");

		// REST Campus
		$app->post('/campus/', "\\SistemaTCC\\Controller\\CampusController::add");
		$app->put('/campus/{id}/', "\\SistemaTCC\\Controller\\CampusController::edit");
		$app->delete('/campus/{id}/', "\\SistemaTCC\\Controller\\CampusController::del");

		 // REST EtapaTipo
		$app->post('/etapatipo/', "\\SistemaTCC\\Controller\\EtapaTipoController::add");
		$app->put('/etapatipo/{id}/', "\\SistemaTCC\\Controller\\EtapaTipoController::edit");
		$app->delete('/etapatipo/{id}/', "\\SistemaTCC\\Controller\\EtapaTipoController::del");

		// REST AreaDeInteresse
	  $app->post('/areadeinteresse/', "\\SistemaTCC\\Controller\\AreaDeInteresseController::add");
	  $app->put('/areadeinteresse/{id}/', "\\SistemaTCC\\Controller\\AreaDeInteresseController::edit");
	  $app->delete('/areadeinteresse/{id}/', "\\SistemaTCC\\Controller\\AreaDeInteresseController::del");

		// Twig Extensions
    $app['twig'] = $app->extend('twig', function ($twig, $app) {
			/**
			 * Função de extensão do Twig para formatar números de telefone para exibição.
			 */
			$fone_format = new \Twig_SimpleFilter('fone_format', function($tel) {
				$formatado = '';
				if(strlen($tel)>=10){
					$formatado = '(' . substr($tel,0,2) . ') ';
				}
				$formatado .= substr($tel,2,-4) . '-' . substr($tel,-4);
				return $formatado;
			});
			$twig->addFilter($fone_format);
			return $twig;
		});
	}

}
