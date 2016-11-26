<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class tccController {

    private function validacao($app, $dados) {
        $asserts = [
            'semestre' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'Informe um valor numerico'
                ]),
            ],
			      'aluno' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Type([
                  'type' => 'numeric',
                  'message' => 'Informe um valor numerico'
                ]),
            ],
            'titulo' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-ZÀ-ú]+?[a-zA-ZÀ-ú ]+$/i',
                    'message' => 'O titulo deve possuir apenas letras'
                ]),
                new Assert\Length([
                    'min' => 3,
                    'max' => 255,
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


    public function add(Application $app, Request $request) {

        $dados = [
              'titulo'   => $request->get('titulo'),
			        'aluno'    => $request->get('aluno'),
			        'semestre' => $request->get('semestre')
        ];

        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
        $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $dados['aluno']);
        if (!$aluno) {
            return $app->json(['aluno' => 'aluno não existe'], 400);
        }
        $semestre = $app['orm']->find('\SistemaTCC\Model\Semestre', (int) $dados['semestre']);
        if (!$semestre) {
            return $app->json(['semestre' => 'O semestre não existe'], 400);
        }
        $tcc = new \SistemaTCC\Model\Tcc();

        $tcc->setTitulo($request->get('titulo'))
			     ->setAluno($aluno)
           ->setSemestre($semestre);

        try {
            $app['orm']->persist($tcc);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'tcc cadastrado com sucesso.'], 201);
    }

    public function find(Application $app, Request $request, $id) {
        $tcc = $app['orm']->find('\SistemaTCC\Model\Tcc', (int) $id);
        if (null === $tcc) {
            return new Response('O tcc não existe.', Response::HTTP_NOT_FOUND);
        }
        return new Response($tcc->tcc()->getTitulo());
    }

    public function edit(Application $app, Request $request, $id) {

        if (null === $tcc = $app['orm']->find('\SistemaTCC\Model\tcc', (int) $id))
            return new Response('O tcc não existe.', Response::HTTP_NOT_FOUND);

        $dados = [
            'titulo'     => $request->get('titulo'),
            'aluno'    	 => $request->get('aluno'),
            'semestre'   => $request->get('semestre')
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $pessoa = $tcc->getPessoa();

        $pessoa->setTitulo($request->get('titulo', $tcc->getTitulo()))
               ->setAluno($request->get('aluno', $pessoa->getAluno()))
               ->setSemestre($request->get('semestre', $pessoa->getSemestre()));

        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'tcc editado com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {

        if (null === $tcc = $app['orm']->find('\SistemaTCC\Model\tcc', (int) $id))
            return $app->json([ 'error' => 'O tcc não existe.'], 400);
        try {
            $app['orm']->remove($tcc);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'tcc excluído com sucesso.']);
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../tcc/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {
      $db = $app['orm']->getRepository('\SistemaTCC\Model\Aluno');
      $aluno = $db->findAll();

        $dadosParaView = [
            'titulo' => 'Cadastrar tcc',
            'values' => [
              'titulo'    => '',
              'aluno'     => $aluno,
              'semestre'  => '',
            ],
        ];
        return $app['twig']->render('tcc/formulario.twig', $dadosParaView);
    }


    public function editarAction(Application $app, Request $request, $id) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\tcc');
        $tcc = $db->find($id);
        if (!$tcc) {
            return $app->redirect('../tcc/listar');
        }
        $dadosParaView = [
            'titulo' => 'Alterando tcc: ' . $tcc->getTcc()->getTitulo(),
            'id' => $id,
            'values' => [
                'titulo'      => $tcc->getTitulo()->getTitulo(),
                'aluno'     => $tcc->getAluno->getAluno(),
                'semestre'  => $tcc->getSemestre()->getSemestre(),
            ],
        ];
        return $app['twig']->render('tcc/formulario.twig', $dadosParaView);
    }

    public function excluirAction() {
        return 'Excluir tcc';
    }

    public function listarAction(Application $app, Request $request) {
        // return '1223';
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Tcc');
        $tccs = $db->findAll();
        $dadosParaView = [
            'titulo' => 'Tcc Listar',
            'tccs' => $tccs,
        ];
        return $app['twig']->render('tcc/listar.twig', $dadosParaView);
    }

}
