<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class AreaDeInteresseController {

    private function validacao($app, $dados) {
        $asserts = [
            'titulo' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-ZÀ-ú]+?[a-zA-ZÀ-ú 0-9 \-]+$/i',
                    'message' => 'O titulo deve possuir apenas letras'
                ]),
                new Assert\Length([
                    'min' => 3,
                    'max' => 200,
                    'minMessage' => 'O titulo precisa possuir pelo menos {{ limit }} caracteres',
                    'maxMessage' => 'O titulo não deve possuir mais que {{ limit }} caracteres',
                ])
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

    private function tituloJaExiste($app, $titulo, $id = false) {
        $allAreaDeInteresse = $app['orm']->getRepository('\SistemaTCC\Model\AreaDeInteresse')->findAll();
        if (count($allAreaDeInteresse)) {
            foreach ($allAreaDeInteresse as $objAreaDeInteresse) {
                if ($id && (int)$id === (int)$objAreaDeInteresse->getId()) {
                    continue;
                }
                if ($titulo === $objAreaDeInteresse->getTitulo()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function add(Application $app, Request $request) {
        $dados = [
            'titulo' => $request->get('titulo'),
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        if ($this->tituloJaExiste($app, $dados['titulo'])) {
            return $app->json(['titulo' => 'Titulo já existe, informe outro'], 400);
        }

        $area = new \SistemaTCC\Model\AreaDeInteresse();
        $area->setTitulo($dados['titulo']);
        try {
            $app['orm']->persist($area);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Area de interesse cadastrada com sucesso.'], 201);
    }

    public function edit(Application $app, Request $request, $id) {
        $area = $app['orm']->find('\SistemaTCC\Model\AreaDeInteresse', (int) $id);
        if (!$area) {
            return $app->json([ 'error' => 'A area não existe.'], 400);
        }
        $dados = [
            'titulo' => $request->get('titulo')
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
        if ($this->tituloJaExiste($app, $dados['titulo'], $id)) {
            return $app->json(['titulo' => 'Titulo já existe, informe outro'], 400);
        }
        $area->setTitulo($dados['titulo']);
        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Area editado com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {
        $area = $app['orm']->find('\SistemaTCC\Model\AreaDeInteresse', (int) $id);
        if (!$area) {
            return $app->json([ 'error' => 'Area não existe.'], 400);
        }
        try {
            $app['orm']->remove($area);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Area excluído com sucesso.']);
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../areadeinteresse/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {
        $dadosParaView = [
            'titulo' => 'Cadastrar Area de interesse',
            'values' => [
            'titulo' => '',
            ],
        ];
        return $app['twig']->render('areadeinteresse/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\AreaDeInteresse');
        $area = $db->find($id);
        if (!$area) {
            return $app->redirect('../areadeinteresse/listar');
        }
        $dadosParaView = [
            'titulo' => 'Alterando Area: ' . $id,
            'id' => $id,
            'values' => [
                'titulo'      => $area->getTitulo(),
            ],
        ];
        return $app['twig']->render('areadeinteresse/formulario.twig', $dadosParaView);
    }

    public function listarAction(Application $app, Request $request) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\AreaDeInteresse');
        $area = $db->findAll();
        $dadosParaView = [
            'titulo' => 'Listar Area de interesse',
            'areadeinteresse' => $area,
        ];
        return $app['twig']->render('areadeinteresse/listar.twig', $dadosParaView);
    }

}
