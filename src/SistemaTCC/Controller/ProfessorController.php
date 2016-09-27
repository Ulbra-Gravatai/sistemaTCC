<?php

namespace SistemaTCC\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfessorController {

    public function add(Application $app, Request $request) {

        $pessoa = new \SistemaTCC\Model\Pessoa();
        $professor = new \SistemaTCC\Model\Professor();

        $pessoa->setNome($request->get('nome'))
               ->setEmail($request->get('email'))
               ->setTelefone($request->get('telefone'))
               ->setSexo($request->get('sexo'));

        $professor->setPessoa($pessoa);

        try {
            $app['orm']->persist($professor);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response('Professor cadastrado com sucesso.', Response::HTTP_CREATED);
    }

    public function find(Application $app, Request $request, $id) {

        if (null === $professor = $app['orm']->find('\SistemaTCC\Model\Professor', (int) $id))
            new Response('O professor não existe.', Response::HTTP_NOT_FOUND);

        return new Response($professor->getPessoa()->getNome());
    }

    public function edit(Application $app, Request $request, $id) {

        if (null === $professor = $app['orm']->find('\SistemaTCC\Model\Professor', (int) $id))
            return new Response('O professor não existe.', Response::HTTP_NOT_FOUND);

        $pessoa = $professor->getPessoa();

        $pessoa->setNome($request->get('nome', $pessoa->getNome()))
               ->setEmail($request->get('email', $pessoa->getEmail()))
               ->setTelefone($request->get('telefone', $pessoa->getTelefone()))
               ->setSexo($request->get('sexo', $pessoa->getSexo()));

        try {
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response('Professor editado com sucesso.', Response::HTTP_OK);
    }

    public function del(Application $app, Request $request, $id) {

        if (null === $professor = $app['orm']->find('\SistemaTCC\Model\Professor', (int) $id))
            return new Response('O professor não existe.', Response::HTTP_NOT_FOUND);

        try {
            $app['orm']->remove($professor);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response('Professor excluído com sucesso.', Response::HTTP_OK);
    }

    public function indexAction() {
        return 'Index Professor';
    }

    public function cadastrarAction() {
        return 'Cadastrar Professor';
    }

    public function editarAction() {
        return 'Editar Professor';
    }

    public function excluirAction() {
        return 'Excluir Professor';
    }

    public function listarAction() {
        return 'Listar Professor';
    }

}
