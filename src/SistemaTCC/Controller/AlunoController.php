<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class AlunoController {

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
                    'maxMessage' => 'Seu nome não deve possuir mais que {{ limit }} caracteres'
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
			'cgu' => [

				new Assert\GreaterThan([
						'value'   => 1,
						'message' => 'Seu CGU não pode ser um número negativo',
				]),
				new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Length([
                     'min' => 3,
                     'max' => 11,
                     'minMessage' => 'Seu CGU precisa possuir pelo menos {{ limit }} caracteres',
                     'maxMessage' => 'Seu CGU não deve possuir mais que {{ limit }} caracteres'
                ])
            ],
			'matricula' => [
				new Assert\GreaterThan([
						'value'   => 1,
						'message' => 'Sua Matrícula não pode ser um número negativo',
				]),
				new Assert\NotBlank(['message' => 'Preencha esse campo']),
                new Assert\Length([
                     'min' => 10,
                     'max' => 10,
                     'exactMessage' => 'Sua Matrícula deve possuir exatamente {{ limit }} caracteres'
                ])
            ],
			'senha' => [
				new Assert\Length([
					'min' => 6,
					'minMessage' => 'A senha deve conter no minímo {{ limit }} digitos'
				])
			]
        ];
        $constraint = new Assert\Collection($asserts);
        $errors     = $app['validator']->validate($dados, $constraint);
        $retorno    = [];
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
            'nome'      => $request->get('nome'),
            'email'     => $request->get('email'),
            'telefone'  => str_replace(array('(',')',' ','-'),'',$request->get('telefone')),
            'sexo'      => $request->get('sexo'),
			'cgu'		=> $request->get('cgu'),
			'matricula'	=> str_replace('-','',$request->get('matricula')),
			'senha'		=> $request->get('senha')
        ];

        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }
		
        $pessoa = new \SistemaTCC\Model\Pessoa();
        $aluno = new \SistemaTCC\Model\Aluno();
		$usuario = new \SistemaTCC\Model\Usuario();
		
        $pessoa->setNome($dados['nome'])
               ->setEmail($dados['email'])
               ->setTelefone($dados['telefone'])
               ->setSexo($dados['sexo']);

        $aluno->setMatricula($dados['matricula'])
              ->setCgu($dados['cgu'])
              ->setPessoa($pessoa);
		
		$usuario->setPessoa($pessoa)
				->setSenha($this->gerarSenha($app,$dados['senha']))
				->setUsuarioAcesso($app['orm']->find('\SistemaTCC\Model\UsuarioAcesso',3));
        try {
            $app['orm']->persist($aluno);
			$app['orm']->persist($usuario);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Aluno cadastrado com sucesso.'], 201);
    }

    public function find(Application $app, Request $request, $id) {

        if (null === $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $id))
            return new Response('O aluno não existe.', Response::HTTP_NOT_FOUND);

        return new Response($aluno->getPessoa()->getNome());
    }

    public function edit(Application $app, Request $request, $id) {

        $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $id);
        if (null === $aluno) {
            return $app->json(['error' => 'O aluno não existe.'], 400);
        }

        $pessoa = $aluno->getPessoa();
		$usuario = $app['orm']->getRepository('\SistemaTCC\Model\Usuario')->findOneByPessoa($pessoa->getId());
        $dados = [
            'nome'      => $request->get('nome', $pessoa->getNome()),
            'email'     => $request->get('email', $pessoa->getEmail()),
            'telefone'  => str_replace(array('(',')',' ','-'),'',$request->get('telefone', $pessoa->getTelefone())),
            'sexo'      => $request->get('sexo', $pessoa->getSexo()),
            'cgu'       => $request->get('cgu', $aluno->getCgu()),
            'matricula' => str_replace('-','',$request->get('matricula', $aluno->getMatricula())),
			'senha'		=> $request->get('senha')
        ];

        $errors = $this->validacao($app, $dados);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $pessoa->setNome($dados['nome'])
               ->setEmail($dados['email'])
               ->setTelefone($dados['telefone'])
               ->setSexo($dados['sexo']);

        $aluno->setMatricula($dados['matricula'])
              ->setCgu($dados['cgu']);
		
		if($dados['senha']!=''){
			$usuario->setSenha($this->gerarSenha($app,$dados['senha']));
		}

        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json($e->getMessage(), 400);
        }
        return $app->json(['success' => 'Aluno alterado com sucesso.']);
    }

    public function del(Application $app, Request $request, $id) {

        if (null === $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $id)) {
            return $app->json(['error' => 'O aluno não existe.'], 400);
        }

        try {
            $app['orm']->remove($aluno);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json($e->getMessage(), 400);
        }
        return $app->json(['success' => 'Aluno excluido com sucesso.']);
	}

    public function indexAction(Application $app, Request $request) {
        return $app->redirect('../aluno/listar');
    }

    public function cadastrarAction(Application $app, Request $request) {
        $dadosParaView = [
            'titulo' => 'Cadastrar Aluno',
            'values' => [
                'nome'      => '',
                'email'     => '',
                'telefone'  => '',
                'sexo'      => '',
				'cgu'		=> '',
				'matricula'	=> '',
            ],
        ];
        return $app['twig']->render('aluno/formulario.twig', $dadosParaView);
    }

    public function editarAction(Application $app, Request $request, $id) {
        // isso é o cara que pega os dados do banco, mas
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Aluno');
        // o metodo 'find' passando um 'id' retorna um objeto do tipo Aluno
        // $aluno === Aluno.php, usa os metodos get
        $aluno = $db->find($id);
        // se nao existir o aluno, ele retorna null, ai redireciona
        if (!$aluno) {
            return $app->redirect('../aluno/listar');
        }

		$dadosParaView = [
            'titulo' => 'Editando Aluno ' . $id,
			'id'     => $id,
			'values' => [
			'nome'		=> $aluno->getPessoa()->getNome(),
			'telefone'	=> $aluno->getPessoa()->getTelefone(),
			'email'		=> $aluno->getPessoa()->getEmail(),
			'sexo'		=> $aluno->getPessoa()->getSexo(),
			'cgu'		=> $aluno->getCgu(),
			'matricula'	=> $aluno->getMatricula()
		    ],
		];

		return $app['twig']->render('aluno/formulario.twig', $dadosParaView);
    }

    public function excluirAction() {
        return 'Excluir Aluno';
    }

		public function listarAction(Application $app) {
        $db = $app['orm']->getRepository('\SistemaTCC\Model\Aluno');
        $alunos = $db->findAll();
        $dadosParaView = [
            'titulo' => 'Aluno Listar',
            'alunos' => $alunos,
        ];
        return $app['twig']->render('aluno/listar.twig', $dadosParaView);
    }
	
	private function gerarSenha(Application $app, $senha){
		$token = $app['security.token_storage']->getToken();

		if (null !== $token) {
			return $app->encodePassword($token->getUser(), $senha);
		}
		return false;
	}
}
