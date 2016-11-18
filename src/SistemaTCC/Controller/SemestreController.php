<?php

namespace SistemaTCC\Controller;

use DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SistemaTCC\Model\Campus;
use SistemaTCC\Model\Semestre;
use Symfony\Component\Validator\Constraints as Assert;

class SemestreController {

    private function validacao($app, $dados) {
        $asserts = [
            'nome' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                // new Assert\Regex([
                //     'pattern' => '/^[0-9]{4}\/[0-2]{2}+$/i',
                //     'message' => 'Nome do semestre deve possuir esse formato Ex.: 2016/02'
                // ]),
                new Assert\Length([
                    'min' => 6,
                    'max' => 50,
                    'minMessage' => 'Seu nome precisa possuir pelo menos {{ limit }} caracteres',
                    'maxMessage' => 'Seu nome não deve possuir mais que {{ limit }} caracteres',
                ])
            ],
            'dataInicio' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Date(['message' => 'Preencha a data']),
            ],
            'dataFim' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Date(['message' => 'Preencha a data']),
            ],
            // 'tipo' => [
            //     new Assert\NotBlank(['message' => 'Preencha esse campo']),
            //     new Assert\Regex([
            //         'pattern' => '/^[1|2]$/',
            //         'message' => 'Não é um tipo válido'
            //     ]),
            // ],
            'campus' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Type([
                        'type'    => 'numeric',
                        'message' => 'O valor {{ value }} não é um {{ type }} válido.',
                    ]),
            ],
        ];
        $constraint = new Assert\Collection($asserts);
        $errors = $app['validator']->validate($dados, $constraint);
        $retorno = [];
        if (count($errors)) {
            foreach ($errors as $error) {
                $key = preg_replace("/[\[\]]/", '', $error->getPropertyPath());
                $retorno[$key] = $error->getMessage();
            }
        }
        return $retorno;
    }

    private function nomeJaExiste($app, $nome, $id = false) {
        $semestres = $app['orm']->getRepository('\SistemaTCC\Model\Semestre')->findAll();
        if (count($semestres)) {
            foreach ($semestres as $semestre) {
                if ($id && (int)$id === (int)$semestre->getId()) {
                    continue;
                }
                if ($nome === $semestre->getNome()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function add(Application $app, Request $request) {
        $dados = [
            'nome'       => $request->get('nome'),
            'dataInicio' => $request->get('dataInicio'),
            'dataFim'    => $request->get('dataFim'),
            // 'tipo'       => $request->get('tipo'),
            'campus'     => $request->get('campus')
        ];

        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        if ($this->nomeJaExiste($app, $dados['nome'])) {
            return $app->json(['nome' => 'Nome já existe, informe outro'], 400);
        }

        $semestre = new Semestre();
        $campus = $app['orm']->find('\\SistemaTCC\\Model\\Campus', $request->get('campus'));

        if (!$campus) {
            return $app->json(['campus' => 'Não existe campus cadastrado'], 400);
        }
        date_default_timezone_set('UTC');
        $semestre->setNome($request->get('nome'));
        $semestre->setDataInicio(new DateTime($request->get('dataInicio')));
        $semestre->setDataFim(new DateTime($request->get('dataFim')));
        // $semestre->setTipo($request->get('tipo'));
        $semestre->setCampus($campus);


        try {
            $app['orm']->persist($semestre);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json(['semestre' => $e->getMessage()], 400);
        }

        return $app->json(['semestre' => 'Semestre criado com sucesso', 'id' => $semestre->getId()]);
    }

    public function edit(Application $app, Request $request, $id) {

        $semestre = $app['orm']->find('\SistemaTCC\Model\Semestre', (int) $id);
        if (!$semestre) {
          return $app->json(['semestre' => 'Não existe semestre cadastrado'], 400);
        }

        $dados = [
            'nome'       => $request->get('nome'),
            'dataInicio' => $request->get('dataInicio'),
            'dataFim'    => $request->get('dataFim'),
            // 'tipo'       => $request->get('tipo'),
            'campus'     => $request->get('campus')
        ];

        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        if ($this->nomeJaExiste($app, $dados['nome'], $id)) {
            return $app->json(['nome' => 'Nome já existe, informe outro'], 400);
        }

        $campus = $app['orm']->find('\SistemaTCC\Model\Campus', $request->get('campus'));
        if (!$campus) {
            return $app->json(['campus' => 'Não existe campus cadastrado'], 400);
        }

        $semestre->setNome($request->get('nome'));
        $semestre->setDataInicio(new DateTime($request->get('dataInicio')));
        $semestre->setDataFim(new DateTime($request->get('dataFim')));
        // $semestre->setTipo($request->get('tipo'));
        $semestre->setCampus($campus);

        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json(['semestre' => $e->getMessage()], 400);
        }
        return $app->json(['semestre' => 'Semestre alterado com sucesso']);
    }

    public function del(Application $app, Request $request, $id) {
		$dbSem = $app['orm']->getRepository('\\SistemaTCC\\Model\\Semestre');
        $semestre = $dbSem->find($id);

		$dbEtapas = $app['orm']->getRepository('\\SistemaTCC\\Model\\Etapa');
		$etapas = $dbEtapas->findBy(['semestre' => $id]);

        if (!$semestre) {
          return $app->json(['semestre' => 'Não existe semestre cadastrado'], 400);
        }

        try {
		  if(count($etapas) > 0) {
		    foreach ($etapas as $etapa) {
          	  $app['orm']->remove($etapa);
      		 }
    		 $app['orm']->flush();
		  }

          $app['orm']->remove($semestre);
          $app['orm']->flush();
        }
        catch (\Exception $e) {
          return $app->json(['semestre' => $e->getMessage()], 400);
        }
        return $app->json(['semestre' => 'Semestre excluido com sucesso']);
    }

    public function find(Application $app, Request $request, $id) {

        if (null === $semestre = $app['orm']->find('\SistemaTCC\Model\semestre', (int) $id))
            return new Response('O semestre não existe.', Response::HTTP_NOT_FOUND);

        return new Response($semestre->getNome());
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../semestre/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Campus');
        $campus = $db->findAll();

        $dadosParaView = [
             'titulo' => 'Cadastrar Semestre',
             'values' => [
                 'campus'   => $campus,
                 'nome'      => '',
                 'datainicio'  => '',
                 'datafim'  => '',
                 'etapa_tcc1'  => [],
                 'etapa_tcc2'  => [],
             ],
        ];
        return $app['twig']->render('semestre/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        $dbSem = $app['orm']->getRepository('\SistemaTCC\Model\Semestre');
        $semestre = $dbSem->find($id);
        if (!$semestre) {
            return $app->redirect('../semestre/listar');
        }

        $dbCampus = $app['orm']->getRepository('\SistemaTCC\Model\Campus');
        $campus = $dbCampus->findAll();

        $dbTipo = $app['orm']->getRepository('\SistemaTCC\Model\EtapaTipo');
        $tipos = $dbTipo->findAll();

        $dbEtapaTcc1 = $app['orm']->getRepository('\SistemaTCC\Model\Etapa')->findBy(['semestre' => $id, 'tcc' => 1]);
        $dbEtapaTcc2 = $app['orm']->getRepository('\SistemaTCC\Model\Etapa')->findBy(['semestre' => $id, 'tcc' => 2]);

        $dadosParaView = [
            'titulo' => 'Editar Semestre: ' .$semestre->getNome(),
            'id'     => $id,
            'values' => [
                'campus'        => $campus,
                'campusid'      => $semestre->getCampus()->getId(),
                'nome'          => $semestre->getNome(),
                'datainicio'    => $semestre->getDataInicio()->format('d/m/Y'),
                'datafim'       => $semestre->getDataFim()->format('d/m/Y'),
                'tipos'         => $tipos,
                'etapa_tcc1'    => $dbEtapaTcc1,
                'etapa_tcc2'    => $dbEtapaTcc2
            ]
        ];
        return $app['twig']->render('semestre/formulario.twig', $dadosParaView);
    }

    public function excluirAction() {
        return 'Excluir Semestre';
    }

    public function listarAction(Application $app) {

        $semestres = $app['orm']->getRepository('\SistemaTCC\Model\Semestre')->findAll();

        $dadosParaView = [
            'titulo' => 'Semestre Listar',
            'semestres' => $semestres
        ];

        return $app['twig']->render('semestre/listar.twig', $dadosParaView);
    }

}
