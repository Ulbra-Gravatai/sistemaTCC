<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class AtestadoController {

    private function validacao($app, $dados) {
        $asserts = [
            'tcc_id' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Type([
                    'type'    => 'numeric',
                    'message' => 'Informe o TCC',
                ]),
            ],
            'professor_id' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Type([
                    'type'    => 'numeric',
                    'message' => 'Informe o TCC',
                ]),
            ],
            'data' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Date(['message' => 'Preencha a data']),
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

    public function add(Application $app, Request $request) {

        $dados = [
            'tcc_id'        => $request->get('tcc_id'),
            'professor_id'  => $request->get('professor_id'),
            'data'          => $request->get('data'),
        ];

        $errors = $this->validacao($app, $dados);

        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $tcc = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->find($dados['tcc_id']);
        if (!$tcc) {
            return $app->json(['tcc_id' => 'TCC Não existe'], 400);
        }

        $professor = $app['orm']->getRepository('\SistemaTCC\Model\Professor')->find($dados['professor_id']);
        if (!$professor) {
            return $app->json(['professor_id' => 'Professor Não existe'], 400);
        }

        $atestado = new \SistemaTCC\Model\Atestado();
        $atestado
            ->setTcc($tcc)
            ->setProfessor($professor)
            ->setData(new \DateTime($dados['data']));

        try {
            $app['orm']->persist($atestado);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Atestado cadastrada com sucesso.'], 201);
    }

    public function edit(Application $app, Request $request, $id) {
        $atestado = $app['orm']->find('\SistemaTCC\Model\Atestado', (int) $id);
        if (!$atestado) {
            return $app->json(['tcc_id' => 'Atestado Não existe'], 400);
        }
        $dados = [
            'tcc_id'        => $request->get('tcc_id'),
            'professor_id'  => $request->get('professor_id'),
            'data'          => $request->get('data'),
        ];

        $errors = $this->validacao($app, $dados);

        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $tcc = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->find($dados['tcc_id']);
        if (!$tcc) {
            return $app->json(['tcc_id' => 'TCC Não existe'], 400);
        }

        $professor = $app['orm']->getRepository('\SistemaTCC\Model\Professor')->find($dados['professor_id']);
        if (!$professor) {
            return $app->json(['professor_id' => 'Professor Não existe'], 400);
        }

        $atestado
            ->setTcc($tcc)
            ->setProfessor($professor)
            ->setData(new \DateTime($dados['data']));
        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Atestado Cadastrado com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {
        $atestado = $app['orm']->find('\SistemaTCC\Model\Atestado', (int) $id);
        if (!$id) {
            return $app->json([ 'error' => 'O Atestado não existe.'], 400);
        }
        try {
			$app['orm']->remove($atestado);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Atestado excluído com sucesso.']);
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../atestado/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {

        $tccs        = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->findAll();
        $professores = $app['orm']->getRepository('\SistemaTCC\Model\Professor')->findAll();

        $dadosParaView = [
            'titulo' => 'Cadastrar Professor',
            'tccs' => $tccs,
            'professores' => $professores,
            'values' => [
                'tcc_id'            => '',
                'professor_id'      => '',
                'data'              => '',
            ],
        ];
        return $app['twig']->render('atestado/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Atestado');
        $atestado = $db->find($id);
        if (!$atestado) {
            return $app->redirect('../atestado/listar');
        }
        $tccs           = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->findAll();
        $professores    = $app['orm']->getRepository('\SistemaTCC\Model\Professor')->findAll();
        $dadosParaView = [
            'titulo' => 'Alterando Atestado : ' . $atestado->getId(),
            'id'    => $id,
            'tccs'  => $tccs,
            'professores'  => $professores,
            'values' => [
                'tcc_id'          => $atestado->getTcc()->getId(),
                'professor_id'    => $atestado->getProfessor()->getId(),
                'data'            => $atestado->getData()->format('d/m/Y'),
            ],
        ];
        return $app['twig']->render('atestado/formulario.twig', $dadosParaView);
    }

    public function listarAction(Application $app, Request $request) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Atestado');
        $atestados = $db->findAll();
        $dadosParaView = [
            'titulo'    => 'Listar Atestados',
            'atestados' => $atestados,
        ];
        return $app['twig']->render('atestado/listar.twig', $dadosParaView);
    }

}
