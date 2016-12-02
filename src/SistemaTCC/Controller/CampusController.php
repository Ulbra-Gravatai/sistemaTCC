<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class CampusController {

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
        $allCampus = $app['orm']->getRepository('\SistemaTCC\Model\Campus')->findAll();
        if (count($allCampus)) {
            foreach ($allCampus as $objCampus) {
                if ($id && (int)$id === (int)$objCampus->getId()) {
                    continue;
                }
                if ($nome === $objCampus->getNome()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function add(Application $app, Request $request) {
        $dados = [
            'nome' => $request->get('nome'),
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        if ($this->nomeJaExiste($app, $dados['nome'])) {
            return $app->json(['nome' => 'Nome já existe, informe outro'], 400);
        }

        $campus = new \SistemaTCC\Model\Campus();
        $campus->setNome($dados['nome']);
        try {
            $app['orm']->persist($campus);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Campus cadastrado com sucesso.'], 201);
    }

    public function edit(Application $app, Request $request, $id) {
        $campus = $app['orm']->find('\SistemaTCC\Model\Campus', (int) $id);
        if (!$campus) {
            return $app->json([ 'error' => 'O Campus não existe.'], 400);
        }
        $dados = [
            'nome' => $request->get('nome')
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
        if ($this->nomeJaExiste($app, $dados['nome'], $id)) {
            return $app->json(['nome' => 'Nome já existe, informe outro'], 400);
        }
        $campus->setNome($dados['nome']);
        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Campus editado com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {
        $campus = $app['orm']->find('\SistemaTCC\Model\Campus', (int) $id);
        if (!$campus) {
            return $app->json([ 'error' => 'O Campus não existe.'], 400);
        }
        try {
            $app['orm']->remove($campus);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Campus excluído com sucesso.']);
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../campus/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {
        $dadosParaView = [
            'titulo' => 'Cadastrar Campus',
            'values' => [
            'nome' => '',
            ],
        ];
        return $app['twig']->render('campus/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Campus');
        $campus = $db->find($id);
        if (!$campus) {
            return $app->redirect('../campus/listar');
        }
        $dadosParaView = [
            'titulo' => 'Alterando Campus: ' . $id,
            'id' => $id,
            'values' => [
                'nome'      => $campus->getNome(),
            ],
        ];
        return $app['twig']->render('campus/formulario.twig', $dadosParaView);
    }

    public function listarAction(Application $app, Request $request) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Campus');
        $campus = $db->findAll();
        $dadosParaView = [
            'titulo' => 'Listar Campus',
            'campus' => $campus,
        ];
        return $app['twig']->render('campus/listar.twig', $dadosParaView);
    }

}
