<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class EtapaTipoController {

    private function validacao($app, $dados) {
        $asserts = [
            'nome' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-ZÀ-ú0-9]+?[a-zA-ZÀ-ú 0-9]+$/i',
                    'message' => 'O nome deve possuir apenas letras'
                ]),
                new Assert\Length([
                    'min' => 3,
                    'max' => 25,
                    'minMessage' => 'O nome precisa possuir pelo menos {{ limit }} caracteres',
                    'maxMessage' => 'O nome não deve possuir mais que {{ limit }} caracteres',
                ])
            ],
			'banca' =>[
				new Assert\Type([
                  'type' => 'numeric',
                  'message' => 'Informe um valor númerico'
                ]),
			],
			'orientador' => [
				new Assert\Type([
                  'type' => 'numeric',
                  'message' => 'Informe um valor númerico'
                ]),
			],
			'coordenador' => [
				new Assert\Type([
                  'type' => 'numeric',
                  'message' => 'Informe um valor númerico'
                ]),
			],
			'entrega_arquivo' => [
				new Assert\Type([
                  'type' => 'numeric',
                  'message' => 'Informe um valor númerico'
                ]),
			]
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
        $allEtapaTipo = $app['orm']->getRepository('\SistemaTCC\Model\EtapaTipo')->findAll();
        if (count($allEtapaTipo)) {
            foreach ($allEtapaTipo as $objEtapaTipo) {
                if ($id && (int)$id === (int)$objEtapaTipo->getId()) {
                    continue;
                }
                if ($nome === $objEtapaTipo->getNome()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function add(Application $app, Request $request) {
        $dados = [
            'nome' 			=> $request->get('nome'),
			'banca'			=> $request->get('banca'),
			'orientador'	=> $request->get('orientador'),
			'coordenador' 	=> $request->get('coordenador'),
			'entrega_arquivo' => $request->get('entrega_arquivo')
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        if ($this->nomeJaExiste($app, $dados['nome'])) {
            return $app->json(['nome' => 'Nome já existe, informe outro'], 400);
        }

        $etapatipo = new \SistemaTCC\Model\EtapaTipo();
        $etapatipo->setNome($dados['nome'])
				->setAvaliadoBanca($dados['banca'])
				->setAvaliadoOrientador($dados['orientador'])
				->setAvaliadoCoordenador($dados['coordenador'])
				->setEntregaArquivo($dados['entrega_arquivo']);
        try {
            $app['orm']->persist($etapatipo);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Tipo cadastrado com sucesso.'], 201);
    }

    public function edit(Application $app, Request $request, $id) {
        $etapatipo = $app['orm']->find('\SistemaTCC\Model\EtapaTipo', (int) $id);
        if (!$etapatipo) {
            return $app->json([ 'error' => 'A etapa não existe.'], 400);
        }
        $dados = [
            'nome' => $request->get('nome'),
			'banca'			=> $request->get('banca'),
			'orientador'	=> $request->get('orientador'),
			'coordenador' 	=> $request->get('coordenador'),
			'entrega_arquivo' => $request->get('entrega_arquivo')
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
        if ($this->nomeJaExiste($app, $dados['nome'], $id)) {
            return $app->json(['nome' => 'Nome já existe, informe outro'], 400);
        }
        $etapatipo->setNome($dados['nome'])
				->setAvaliadoBanca($dados['banca'])
				->setAvaliadoOrientador($dados['orientador'])
				->setAvaliadoCoordenador($dados['coordenador'])
				->setEntregaArquivo($dados['entrega_arquivo']);
        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Tipo editado com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {
        $etapatipo = $app['orm']->find('\SistemaTCC\Model\EtapaTipo', (int) $id);
        if (!$etapatipo) {
            return $app->json([ 'error' => 'Tipo não existe.'], 400);
        }
        try {
            $app['orm']->remove($etapatipo);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Tipo excluído com sucesso.']);
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../etapatipo/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {
        $dadosParaView = [
            'titulo' => 'Cadastrar Tipo de Etapa',
            'values' => [
				'nome' => '',
				'banca'			=> '',
				'orientador'	=> '',
				'coordenador' 	=> '',
				'entrega_arquivo' => ''
            ],
        ];
        return $app['twig']->render('etapatipo/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\EtapaTipo');
        $etapatipo = $db->find($id);
        if (!$etapatipo) {
            return $app->redirect('../etapatipo/listar');
        }
        $dadosParaView = [
            'titulo' => 'Alterando Tipo de Etapa: ' . $id,
            'id' => $id,
            'values' => [
                'nome'      	=> $etapatipo->getNome(),
				'banca'			=> $etapatipo->getAvaliadoBanca(),
				'orientador'	=> $etapatipo->getAvaliadoOrientador(),
				'coordenador' 	=> $etapatipo->getAvaliadoCoordenador(),
				'entrega_arquivo' => $etapatipo->getEntregaArquivo()
            ],
        ];
        return $app['twig']->render('etapatipo/formulario.twig', $dadosParaView);
    }

    public function listarAction(Application $app, Request $request) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\EtapaTipo');
        $etapatipo = $db->findAll();
        $dadosParaView = [
            'titulo' => 'Listar Tipo de Etapa',
            'etapatipo' => $etapatipo,
        ];
        return $app['twig']->render('etapatipo/listar.twig', $dadosParaView);
    }

}
