<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class ProfessorController {

    private function validacao($app, $dados) {
        $asserts = [
            'nome' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-ZÀ-ú]+ [a-zA-ZÀ-ú.]+?[a-zA-ZÀ-ú .]+$/i',
                    'message' => 'Informe o Nome e Sobrenome'
                ]),
                new Assert\Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => 'Seu nome precisa possuir pelo menos {{ limit }} caracteres',
                    'maxMessage' => 'Seu nome não deve possuir mais que {{ limit }} caracteres',
                ])
            ],
            'email' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Email([
                    'message' => 'Esse e-mail é inválido',
                ])
            ],
            'telefone' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
				new Assert\Regex([
					'pattern' => '/^[0-9]+$/i',
					'message' => 'Seu telefone deve possuir apenas números'
				]),
                new Assert\Length([
                    'min' => 10,
                    'max' => 12,
                    'minMessage' => 'Informe no mínimo {{ limit }} números',
                    'maxMessage' => 'Informe no máximo {{ limit }} números',
                ])
            ],
            'sexo' => [
                new Assert\NotBlank(['message' => 'Preencha esse campo']),
            ],
            'interesses' => [
                new Assert\NotBlank(['message' => 'Informe as áreas de interesse']),
                new Assert\Type([
                    'type' => 'array',
                ]),
            ],
			'senha' => [
				new Assert\Length([
					'min' => 6,
					'minMessage' => 'A senha deve conter no minímo {{ limit }} digitos'
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
            'nome'       => $request->get('nome'),
            'email'      => $request->get('email'),
            'telefone'   => str_replace(array('(',')',' ','-'),'',$request->get('telefone')),
            'sexo'       => $request->get('sexo'),
            'interesses' => $request->get('interesses'),
			'senha' 	 => $request->get('senha')
        ];

        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $pessoa = new \SistemaTCC\Model\Pessoa();
        $professor = new \SistemaTCC\Model\Professor();
		$usuario = new \SistemaTCC\Model\Usuario();

        foreach ($dados['interesses'] as $idArea) {
            $professor->addAreaDeInteresse($app['orm']->find('\SistemaTCC\Model\AreaDeInteresse', $idArea));
        }

        $pessoa->setNome($request->get('nome'))
               ->setEmail($request->get('email'))
               ->setTelefone(str_replace(array('(',')',' ','-'),'',$request->get('telefone')))
               ->setSexo($request->get('sexo'));

        $professor->setPessoa($pessoa);
		
		$usuario->setPessoa($pessoa)
				->setSenha($this->gerarSenha($app,$dados['senha']))
				->setUsuarioAcesso($app['orm']->find('\SistemaTCC\Model\UsuarioAcesso',2));
				
        try {
            $app['orm']->persist($professor);
			$app['orm']->persist($usuario);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Professor cadastrado com sucesso.'], 201);
    }

    public function find(Application $app, Request $request, $id) {
        $professor = $app['orm']->find('\SistemaTCC\Model\Professor', (int) $id);
        if (null === $professor) {
            return new Response('O professor não existe.', Response::HTTP_NOT_FOUND);
        }
        return new Response($professor->getPessoa()->getNome());
    }

    public function edit(Application $app, Request $request, $id) {

        if (null === $professor = $app['orm']->find('\SistemaTCC\Model\Professor', (int) $id))
            return new Response('O professor não existe.', Response::HTTP_NOT_FOUND);
		
        $dados = [
            'nome'       => $request->get('nome'),
            'email'      => $request->get('email'),
            'telefone'   => str_replace(array('(',')',' ','-'),'',$request->get('telefone')),
            'sexo'       => $request->get('sexo'),
            'interesses' => $request->get('interesses'),
			'senha'		 => $request->get('senha')
        ];
        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
        // Remove Areas de Interesse
        $interesses = $professor->getAreasDeInteresse();
        if (count($interesses)) {
            foreach ($interesses as $interesse) {
                $professor->removeAreaDeInteresse($interesse);
            }
        }

        // Adiciona Areas de Interesse
        if (count($dados['interesses'])) {
            foreach ($dados['interesses'] as $idArea) {
                $areaDeInteresse = $app['orm']->find('\SistemaTCC\Model\AreaDeInteresse', (int) $idArea);
                if ($areaDeInteresse) {
                    $professor->addAreaDeInteresse($areaDeInteresse);
                }
            }
        }

        $pessoa = $professor->getPessoa();
        $pessoa->setNome($request->get('nome', $pessoa->getNome()))
               ->setEmail($request->get('email', $pessoa->getEmail()))
               ->setTelefone(str_replace(array('(',')',' ','-'),'',$request->get('telefone', $pessoa->getTelefone())))
               ->setSexo($request->get('sexo', $pessoa->getSexo()));
		
		$usuario = $app['orm']->getRepository('\SistemaTCC\Model\Usuario')->findOneByPessoa($pessoa->getId());
		if($dados['senha']!=''){
			$usuario->setSenha($this->gerarSenha($app,$dados['senha']));
		}
		
        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Professor editado com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {

        if (null === $professor = $app['orm']->find('\SistemaTCC\Model\Professor', (int) $id))
            return $app->json([ 'error' => 'O professor não existe.'], 400);
		
		$usuario = $app['orm']->getRepository('\SistemaTCC\Model\Usuario')->findOneByPessoa($professor->getPessoa()->getId());
        try {
			$app['orm']->remove($usuario);
            $app['orm']->remove($professor);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Professor excluído com sucesso.']);
    }

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../professor/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {

        $areas = $app['orm']->getRepository('\SistemaTCC\Model\AreaDeInteresse')->findAll();

        $dadosParaView = [
            'titulo' => 'Cadastrar Professor',
            'areas' => $areas,
            'values' => [
                'nome'      => '',
                'email'     => '',
                'telefone'  => '',
                'sexo'      => '',
            ],
        ];
        return $app['twig']->render('professor/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Professor');
        $professor = $db->find($id);
        if (!$professor) {
            return $app->redirect('../professor/listar');
        }
        $areas = $app['orm']->getRepository('\SistemaTCC\Model\AreaDeInteresse')->findAll();
        $dadosParaView = [
            'titulo' => 'Alterando Professor: ' . $professor->getPessoa()->getNome(),
            'id' => $id,
            'areas' => $areas,
            'values' => [
                'nome'       => $professor->getPessoa()->getNome(),
                'email'      => $professor->getPessoa()->getEmail(),
                'telefone'   => $professor->getPessoa()->getTelefone(),
                'sexo'       => $professor->getPessoa()->getSexo(),
                'interesses' => $professor->getAreasDeInteresse(),
            ],
        ];
        return $app['twig']->render('professor/formulario.twig', $dadosParaView);
    }

    public function excluirAction() {
        return 'Excluir Professor';
    }

    public function listarAction(Application $app, Request $request) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Professor');
        $professores = $db->findAll();
        $dadosParaView = [
            'titulo' => 'Listar Professor',
            'professores' => $professores,
        ];
        return $app['twig']->render('professor/listar.twig', $dadosParaView);
    }
	
	private function gerarSenha(Application $app, $senha){
		$token = $app['security.token_storage']->getToken();

		if (null !== $token) {
			return $app->encodePassword($token->getUser(), $senha);
		}
		return false;
	}
}
