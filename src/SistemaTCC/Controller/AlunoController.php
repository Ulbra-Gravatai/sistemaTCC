<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AlunoController {

    public function add(Application $app, Request $request) {

        $pessoa = new \SistemaTCC\Model\Pessoa();
        $aluno = new \SistemaTCC\Model\Aluno();

        $pessoa->setNome($request->get('nome'))
               ->setEmail($request->get('email'))
               ->setTelefone($request->get('telefone'))
               ->setSexo($request->get('sexo'));

        $aluno->setMatricula($request->get('matricula'))
              ->setCgu($request->get('cgu'))
              ->setPessoa($pessoa);

        try {
            $app['orm']->persist($aluno);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response('Aluno cadastrado com sucesso.', Response::HTTP_CREATED);
    }

    public function find(Application $app, Request $request, $id) {

        if (null === $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $id))
            return new Response('O aluno não existe.', Response::HTTP_NOT_FOUND);

        return new Response($aluno->getPessoa()->getNome());
    }

    public function edit(Application $app, Request $request, $id) {

        if (null === $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $id))
            return new Response('O aluno não existe.', Response::HTTP_NOT_FOUND);

        $pessoa = $aluno->getPessoa();

        $pessoa->setNome($request->get('nome', $pessoa->getNome()))
               ->setEmail($request->get('email', $pessoa->getEmail()))
               ->setTelefone($request->get('telefone', $pessoa->getTelefone()))
               ->setSexo($request->get('sexo', $pessoa->getSexo()));

        $aluno->setMatricula($request->get('matricula', $aluno->getMatricula()))
              ->setCgu($request->get('cgu', $aluno->getCgu()));

        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response('Aluno editado com sucesso.', Response::HTTP_OK);
    }

    public function del(Application $app, Request $request, $id) {

        if (null === $aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', (int) $id))
            return new Response('O aluno não existe.', Response::HTTP_NOT_FOUND);

        try {
            $app['orm']->remove($aluno);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response('Aluno excluído com sucesso.', Response::HTTP_OK);
    }

    public function indexAction() {
        return 'Index Aluno';
    }

    public function cadastrarAction() {
        return 'Cadastrar Aluno';
    }

    public function editarAction() {
        return 'Editar Aluno';
    }

    public function excluirAction() {
        return 'Excluir Aluno';
    }

    public function listarAction() {
        return 'Listar Aluno';
    }

}
