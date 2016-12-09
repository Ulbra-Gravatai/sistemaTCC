<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class TccDefesaController {

    private function validacao($app, $dados) {
        $asserts = [
            'tcc_id' => [
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
            'hora' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
				new Assert\Time(['message' => 'Hora inválida']),
            ],
            'local' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Length([
                    'min' => 3,
                    'max' => 50,
                    'minMessage' => 'Informe pelo menos {{ limit }} caracteres',
                    'maxMessage' => 'Informe no máximo {{ limit }} caracteres',
                ])
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

    public function add(Application $app, Request $request) {

        $dados = [
            'tcc_id'  => $request->get('tcc_id'),
            'data'    => $request->get('data'),
            'hora'    => $request->get('hora'),
            'local'   => $request->get('local')
        ];

        $errors = $this->validacao($app, $dados);

        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $tcc = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->find($dados['tcc_id']);
        if (!$tcc) {
            return $app->json(['tcc_id' => 'TCC Não existe'], 400);
        }

        $defesa = new \SistemaTCC\Model\TccDefesa();
        $defesa->setTcc($tcc)
               ->setDataHora(new \DateTime($dados['data']. ' ' . $dados['hora']))
               ->setLocal($dados['local']);

        try {
            $app['orm']->persist($defesa);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Defesa cadastrada com sucesso.'], 201);
    }

    public function edit(Application $app, Request $request, $id) {
        $defesa = $app['orm']->find('\SistemaTCC\Model\TccDefesa', (int) $id);
        if (!$defesa) {
            return $app->json(['tcc_id' => 'Defesa Não existe'], 400);
        }
        $dados = [
            'tcc_id'  =>$request->get('tcc_id'),
            'data'    => $request->get('data'),
            'hora'    => $request->get('hora'),
            'local'   => $request->get('local')
        ];

        $errors = $this->validacao($app, $dados);

        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $tcc = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->find($dados['tcc_id']);
        if (!$tcc) {
            return $app->json(['tcc_id' => 'TCC Não existe'], 400);
        }

        $defesa->setTcc($tcc)
               ->setDataHora(new \DateTime($dados['data']. ' ' . $dados['hora']))
               ->setLocal($dados['local']);
        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Defesa Agendada com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {
        $defesa = $app['orm']->find('\SistemaTCC\Model\TccDefesa', (int) $id);
        if (!$id) {
            return $app->json([ 'error' => 'O Defesa não existe.'], 400);
        }
        try {
			$app['orm']->remove($defesa);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Defesa excluída com sucesso.']);
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../defesa/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {

        $tccs = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->findAll();

        $dadosParaView = [
            'titulo' => 'Cadastrar Professor',
            'tccs' => $tccs,
            'values' => [
                'tcc_id'    => '',
                'data'      => '',
                'hora'      => '',
                'local'     => '',
            ],
        ];
        return $app['twig']->render('defesa/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\TccDefesa');
        $defesa = $db->find($id);
        if (!$defesa) {
            return $app->redirect('../defesa/listar');
        }
        $tccs = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->findAll();
        $dadosParaView = [
            'titulo' => 'Alterando Agendamento de TCC : ' . $defesa->getTcc()->getTitulo(),
            'id' => $id,
            'tccs' => $tccs,
            'values' => [
                'tcc_id'    => $defesa->getTcc()->getId(),
                'data'      => $defesa->getDataHora()->format('d/m/Y'),
                'hora'      => $defesa->getDataHora()->format('h:i:s'),
                'local'     => $defesa->getLocal(),
            ],
        ];
        return $app['twig']->render('defesa/formulario.twig', $dadosParaView);
    }

    public function listarAction(Application $app, Request $request) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\TccDefesa');
        $defesas = $db->findAll();
        $dadosParaView = [
            'titulo' => 'Listar Defesas Agendadas',
            'defesas' => $defesas,
        ];
        return $app['twig']->render('defesa/listar.twig', $dadosParaView);
    }

}
