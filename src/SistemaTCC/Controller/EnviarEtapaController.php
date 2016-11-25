<?php

namespace SistemaTCC\Controller;

use DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class EnviarEtapaController {

	private function validacao($app, $dados) {
		$asserts = [
			'arquivo' => [
				new Assert\File([
					'mimeTypes' => ['application/pdf','application/x-pdf','application/msword'],
					'mimeTypesMessage' => 'Somente os formatos doc e pdf são aceitos.',
					'disallowEmptyMessage' => 'Selecione um arquivo',
					'uploadErrorMessage' => 'Não foi possível realizar o upload dos arquivos, tente novamente mais tarde.']
				),
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
		$file = $request->files->get('arquivo');
		if(!$file){
			return $app->json(['Nenhum arquivo recebido.'], 400);
		}
		
		$dados = [
			'arquivo' => $file
		];
		
		//$errors = $this->validacao($app, $dados);
		//if (count($errors) > 0) {
		//	return $app->json($errors, 400);
		//}
		$caminho = 'files/idsemestre/idtcc/' . $request->get('etapa') . '/';
		$tipo = $file->getClientOriginalExtension(); 
		$nome = md5(uniqid()) . '.' . $tipo;
		$file->move(__DIR__ . '/../../../' . $caminho, $nome);
		
		$etapaEntregaArquivo = new \SistemaTCC\Model\EtapaEntregaArquivo();
		$etapa = $app['orm']->find('\SistemaTCC\Model\Etapa', $request->get('etapa'));
		$aluno = $app['orm']->find('\SistemaTCC\Model\Aluno', 1);//$request->getSession()->get('alunoId')); //Verificar como será armazenado as informações do usuário na sessão
		
		$etapaEntrega = $app['orm']->getRepository('\SistemaTCC\Model\EtapaEntrega')->findOneByEtapa($request->get('etapa'));
		if(!$etapaEntrega){
			$etapaEntrega = new \SistemaTCC\Model\EtapaEntrega();
			$etapaStatus = $app['orm']->find('\SistemaTCC\Model\EtapaStatus', 3);
			$etapaEntrega->setData(new DateTime())
					->setAluno($aluno)
					->setEtapa($etapa)
					->setEtapaStatus($etapaStatus);
		}
		$etapaEntregaArquivo->setNome($nome)
				->setTipo($tipo)
				->setCaminho($caminho)
				->setEtapaEntrega($etapaEntrega);

		try {
			$app['orm']->persist($etapaEntregaArquivo);
			$app['orm']->flush();
		} catch (\Exception $e) {
			return $app->json([$e->getMessage()], 400);
		}
		return $app->json(['success' => 'Arquivo enviado com sucesso.','arquivo' => $etapaEntregaArquivo->toJson()], 201);//['nome'=>$etapaEntregaArquivo->getNome()]
	}
	
	public function del(Application $app, Request $request, $id) {

        if (null === $arquivo= $app['orm']->find('\SistemaTCC\Model\EtapaEntregaArquivo', (int) $id)) {
            return $app->json(['error' => 'O arquivo não existe.'], 400);
        }
		
		if(!unlink(__DIR__ . '/../../../' . $arquivo->getCaminho() . $arquivo->getNome())){
			return $app->json(['error' => 'O arquivo não existe.'], 400);
		}
        try {
            $app['orm']->remove($arquivo);
            $app['orm']->flush();
        }
        catch (\Exception $e) {
            return $app->json($e->getMessage(), 400);
        }
        return $app->json(['success' => 'Arquivo excluido com sucesso.']);
	}

	public function indexAction(Application $app, Request $request) {
		return $app->redirect('../enviaretapa/listar');
	}

	public function listarAction(Application $app, Request $request) {
		$semestre = 1; //$request->getSession()->get('semestreId'); //Id Semestre das etapas a serem listadas (Verificar como será armazenado as informações de sessão)
		$tcc = 1;
		$db = $app['orm']->getRepository('\SistemaTCC\Model\Etapa');
		$etapas = $db->findBy(array('semestre' => $semestre,'tcc' => $tcc));
		
		$etapas_status = array();
		$etapas_nota = array();
		foreach($etapas as $etapa){
			$etapa_entrega = $app['orm']->getRepository('\SistemaTCC\Model\EtapaEntrega')->findOneByEtapa($etapa->getId());
			if($etapa_entrega!=''){
				$etapas_status[$etapa->getId()] = $etapa_entrega->getEtapaStatus();
				$etapa_nota = $app['orm']->getRepository('\SistemaTCC\Model\EtapaNota')->findOneByEtapaEntrega($etapa_entrega->getId());
				if($etapa_nota != ''){
					$etapas_nota[$etapa->getId()] = $etapa_nota;
				}
			}
		}
		
		$dadosParaView = [
			'titulo' => 'Etapas',
			'etapas' => $etapas,
			'etapas_status' => $etapas_status,
			'etapas_nota' => $etapas_nota,
			'data_atual' => (new DateTime())
		];
		return $app['twig']->render('enviaretapa/listar.twig', $dadosParaView);
	}

	public function enviarAction(Application $app, Request $request, $id) {
		$etapa = $app['orm']->getRepository('\SistemaTCC\Model\Etapa')->find($id);
		
		if (!$etapa) {
			return $app->redirect('../enviaretapa/listar');
		}
		
		$etapa_entrega = $app['orm']->getRepository('\SistemaTCC\Model\EtapaEntrega')->findOneByEtapa($etapa->getId());
		$arquivos = array();
		if($etapa_entrega){
			$arquivos = $app['orm']->getRepository('\SistemaTCC\Model\EtapaEntregaArquivo')->findByEtapaEntrega($etapa_entrega->getId());
		}
		$dadosParaView = [
			'titulo' => 'Enviar Etapa:',
			'subtitulo' => $etapa->getNome(),
			'etapa' => $etapa,
			'data_atual' => new DateTime(),
			'arquivos' => $arquivos
		];
		return $app['twig']->render('enviaretapa/formulario.twig', $dadosParaView);
	}

}
