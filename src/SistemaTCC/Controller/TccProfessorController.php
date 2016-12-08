<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class tccProfessorController {

    private function validacao($app, $dados) {
        $asserts = [
            'professor' => [
                new Assert\NotBlank(['message' => 'Selecione um professor']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'O Professor selecionado não é válido'
                ]),
            ],
			'tcc' => [
                new Assert\NotBlank(['message' => 'Selecione um TCC']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'O TCC selecionado não é válido'
                ]),
            ],
			'tipo' => [
                new Assert\NotBlank(['message' => 'Selecione um Tipo']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'O tipo selecionado não é válido'
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


    public function add(Application $app, Request $request) {

        $dados = [
			'professor'   => $request->get('professor'),
			'tcc'    => $request->get('tcc'),
			'tipo' => $request->get('tipo')
        ];

        $errors = $this->validacao($app, $dados);

		$professor = $app['orm']->find('\SistemaTCC\Model\Professor', (int) $dados['professor']);
        if (!array_key_exists('professor',$errors) && !$professor) {
            $errors['professor'] = 'Professor não existe';
        }
        $tcc = $app['orm']->find('\SistemaTCC\Model\Tcc', (int) $dados['tcc']);
        if (!array_key_exists('tcc',$errors) && !$tcc) {
            $errors['tcc'] = 'O TCC não existe';
        }

		if (!array_key_exists('professor',$errors) && $this->jaContemProfessor($app,$professor,$tcc)) {
            $errors['professor'] = 'Este professor já foi adicionado a banca.';
        }
		
		if (!array_key_exists('professor',$errors) && $this->contemOrientador($app,$tcc, (int) $dados['tipo'])) {
            $errors['professor'] = 'Já existe um Orientador cadastrado.';
        }
		
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $tccProfessor = new \SistemaTCC\Model\TccProfessor();

        $tccProfessor->setTcc($tcc)
			->setProfessor($professor)
			->setTipo($request->get('tipo'));

        try {
            $app['orm']->persist($tccProfessor);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json([
			'success' => 'Professor banca cadastrado com sucesso.',
			'banca' => $tccProfessor->toJson(),
			'professor' => ['pessoa'=>['nome' => $professor->getPessoa()->getNome()]],
			'tipo' => ['orientador' => \SistemaTCC\Model\TccProfessor::ORIENTADOR,'banca' => \SistemaTCC\Model\TccProfessor::BANCA]
		], 201);
    }

    public function edit(Application $app, Request $request, $id) {

        if (null === $tccProfessor = $app['orm']->find('\SistemaTCC\Model\TccProfessor', (int) $id))
            return new Response('O Professor banca não existe.', Response::HTTP_NOT_FOUND);

        $dados = [
			'tipo' => $request->get('tipo')
        ];
        $errors = $this->validacao($app, $dados);

        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $tccProfessor->setTipo($request->get('tipo'));

        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
	return $app->json(['success' => 'Professor banca editado com sucesso.','tipo' => ['orientador' => \SistemaTCC\Model\TccProfessor::ORIENTADOR,'banca' => \SistemaTCC\Model\TccProfessor::BANCA]]);
    }

    public function del(Application $app, Request $request, $id) {

        if (null === $tccProfessor = $app['orm']->find('\SistemaTCC\Model\TccProfessor', (int) $id))
            return $app->json([ 'error' => 'O Professor banca não existe.'], 400);
        try {
            $app['orm']->remove($tccProfessor);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json([$e->getMessage()], 400);
        }
        return $app->json(['success' => 'Professor banca excluído com sucesso.']);
    }
	
	private function jaContemProfessor(Application $app, $professor, $tcc){
		if(!$tcc || !$professor){
			return false;
		}
		$tccProfessor = $app['orm']->getRepository('\SistemaTCC\Model\TccProfessor')->findBy(['tcc' => $tcc,'professor' => $professor]);
		return count($tccProfessor) > 0;
	}
	
	private function contemOrientador(Application $app, $tcc, $tipo){
		if(!$tcc || $tipo != \SistemaTCC\Model\TccProfessor::ORIENTADOR){
			return false;
		}
		$tccProfessor = $app['orm']->getRepository('\SistemaTCC\Model\TccProfessor')->findBy(['tcc' => $tcc,'tipo' => \SistemaTCC\Model\TccProfessor::ORIENTADOR]);
		return count($tccProfessor) > 0;
	}
}

