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
                new Assert\NotBlank(['message' => 'Selecione um semestre']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'O semestre selecionado não é válido'
                ]),
            ],
            'aluno' => [
                new Assert\NotBlank(['message' => 'Defina o aluno deste TCC']),
                new Assert\Type([
                  'type' => 'numeric',
                  'message' => 'Informe o nome de um aluno válido'
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
            'disciplina' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'A disciplina selecionada não é válida'
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


    public function add(Application $app, Request $request) {


        $dados = [
            'titulo'   => $request->get('titulo'),
            'aluno'    => $request->get('aluno'),
            'semestre' => $request->get('semestre'),
            'disciplina' => $request->get('disciplina')
        ];

        $errors = $this->validacao($app, $dados);
        
        $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $dados['aluno']);
        if (!array_key_exists('aluno',$errors) && !$aluno) {
            $errors['aluno'] = 'aluno não existe';
        }
        $semestre = $app['orm']->find('\SistemaTCC\Model\Semestre', (int) $dados['semestre']);
        if (!array_key_exists('semestre',$errors) && !$semestre) {
            $errors['semestre'] = 'O semestre não existe';
        }
        
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
        
        $tcc = new \SistemaTCC\Model\Tcc();

        $tcc->setTitulo($request->get('titulo'))
            ->setAluno($aluno)
            ->setSemestre($semestre)
            ->setDisciplina($request->get('disciplina'));

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
            'aluno'      => $request->get('aluno'),
            'semestre'   => $request->get('semestre'),
            'disciplina' => $request->get('disciplina')
        ];
        $errors = $this->validacao($app, $dados);
        
        $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $dados['aluno']);
        if (!array_key_exists('aluno',$errors) && !$aluno) {
            $errors['aluno'] = 'aluno não existe';
        }

        $semestre = $app['orm']->find('\SistemaTCC\Model\Semestre', (int) $dados['semestre']);
        if (!array_key_exists('semestre',$errors) && !$semestre) {
            $errors['semestre'] = 'O semestre não existe';
        }
        
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
        
        $tcc->setTitulo($request->get('titulo', $tcc->getTitulo()))
               ->setAluno($aluno)
               ->setSemestre($semestre)
               ->setDisciplina($request->get('disciplina'));

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

        $alunos = [];
        foreach ($aluno as $a => $al) {
          array_push($alunos, ['id'=> $al->getId(), 'nome' => $al->getMatricula().' - '.$al->getPessoa()->getNome()]);
        }

        $semestres = $app['orm']->getRepository('\SistemaTCC\Model\Semestre')->findAll();
        $dadosParaView = [
            'titulo' => 'Cadastrar tcc',
            'listaAlunos' => json_encode($alunos),
            'listarSemestres' => $semestres,
            'values' => [
                'titulo'     => '',
                'aluno'      => '',
                'semestre'   => '',
                'disciplina' => ''
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
        
        $alunos = [];
        $aluno = $app['orm']->getRepository('\SistemaTCC\Model\Aluno')->findAll();
        foreach ($aluno as $a => $al) {
          array_push($alunos, $al->getId().' - '.$al->getPessoa()->getNome());
        }
        $semestres = $app['orm']->getRepository('\SistemaTCC\Model\Semestre')->findAll();
        $dadosParaView = [
            'titulo' => 'Alterando tcc: ' . $tcc->getTitulo(),
            'id' => $id,
            'listaAlunos' => json_encode($alunos),
            'listarSemestres' => $semestres,
            'values' => [
                'titulo'      => $tcc->getTitulo(),
                'aluno'     => $tcc->getAluno()->getId() . ' - ' . $tcc->getAluno()->getPessoa()->getNome(),
                'semestre'  => $tcc->getSemestre()->getId(),
                'disciplina' => $tcc->getDisciplina()
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
            'titulo' => 'Listar Tcc',
            'tccs' => $tccs,
        ];
        return $app['twig']->render('tcc/listar.twig', $dadosParaView);
    }

}

    