<?php

namespace SistemaTCC\Controller;

use DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class EnviarEtapaController {

	private function validacao($app, $dados) {
		$asserts = [
			'arquivo' => [
				new Assert\File([
					'maxSize' => '30Mi',
					'maxSizeMessage' => 'O arquivo é muito grande ({{ size }} {{ suffix }}). O tamanho máximo permitido é {{ limit }} {{ suffix }}.',
					//'mimeTypes' => ['application/pdf','application/x-pdf','application/msword'],
					//'mimeTypesMessage' => 'Somente os formatos doc e pdf são aceitos.',
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
		$pessoa = $app['orm']->getRepository('\SistemaTCC\Model\Pessoa')->findOneByEmail($app['user']->getUsername());
		$aluno = $app['orm']->getRepository('\SistemaTCC\Model\Aluno')->findOneByPessoa($pessoa);
		if(!$aluno){
			return $app['twig']->render('alerta.twig',['tipo'=>'danger','mensagem'=>'Somente alunos podem acessar está área.']);
		}
		
		$file = $request->files->get('arquivo');
		
		$dados = [
			'arquivo' => $file
		];
		
		$errors = $this->validacao($app, $dados);
		
		$tipo = $file->getClientOriginalExtension(); 
		$extensoesPermitidas = ['pdf','doc','docx'];
		if(!array_key_exists('arquivo',$errors) && !in_array($tipo,$extensoesPermitidas)){
			$errors['arquivo'] ='Somente os formatos de arquivo pdf, doc e docx são aceitos.';
		}
		
		if (count($errors) > 0) {
			return $app->json($errors, 400);
		}
		
		$caminho = 'files/idsemestre/idtcc/' . $request->get('etapa') . '/';
		$nome = md5(uniqid()) . '.' . $tipo;
		$file->move(__DIR__ . '/../../../' . $caminho, $nome);
		
		$etapaEntregaArquivo = new \SistemaTCC\Model\EtapaEntregaArquivo();
		$etapa = $app['orm']->find('\SistemaTCC\Model\Etapa', $request->get('etapa'));
		
		$etapaEntrega = $app['orm']->getRepository('\SistemaTCC\Model\EtapaEntrega')->findOneBy(['etapa'=>$etapa,'aluno'=>$aluno]);
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
		$pessoa = $app['orm']->getRepository('\SistemaTCC\Model\Pessoa')->findOneByEmail($app['user']->getUsername());
		$aluno = $app['orm']->getRepository('\SistemaTCC\Model\Aluno')->findOneByPessoa($pessoa);
		if(!$aluno){
			return $app['twig']->render('alerta.twig',['tipo'=>'danger','mensagem'=>'Somente alunos podem acessar está área.']);
		}
		//Busca o semestre atual
		$semestre  = $app['orm']->createQuery('SELECT s FROM SistemaTCC\Model\Semestre s WHERE CURRENT_DATE() BETWEEN s.dataInicio AND s.dataFim')->getOneOrNullResult();
		if(!$semestre){
			return $app['twig']->render('alerta.twig',['tipo'=>'danger','mensagem'=>'O semestre atual não foi cadastrado, contacte o administrado.']);
		}
		$tcc = $app['orm']->getRepository('\SistemaTCC\Model\Tcc')->findOneBy(['aluno'=>$aluno,'semestre'=>$semestre]);
		if(!$tcc){
			return $app['twig']->render('alerta.twig',['tipo'=>'danger','mensagem'=>'Você não tem um TCC cadastrado no semestre atual.']);
		}
		$db = $app['orm']->getRepository('\SistemaTCC\Model\Etapa');
		$etapas = $db->findBy(array('semestre' => $semestre,'tcc' => $tcc->getDisciplina()));
		
		$etapas_status = array();
		$etapas_nota = array();
		foreach($etapas as $etapa){
			$etapa_entrega = $app['orm']->getRepository('\SistemaTCC\Model\EtapaEntrega')->findOneBy(['etapa'=>$etapa,'aluno'=>$aluno]);
			if($etapa_entrega!=''){
				$etapas_status[$etapa->getId()] = $etapa_entrega->getEtapaStatus();
				$etapa_nota = $app['orm']->getRepository('\SistemaTCC\Model\EtapaNota')->findOneByEtapaEntrega($etapa_entrega->getId());
				if($etapa_nota != ''){
					$etapas_nota[$etapa->getId()] = $etapa_nota;
				}
			}
		}
		
		$dadosParaView = [
			'titulo' => 'Listar Etapas',
			'etapas' => $etapas,
			'etapas_status' => $etapas_status,
			'etapas_nota' => $etapas_nota,
			'data_atual' => (new DateTime())
		];
		return $app['twig']->render('enviaretapa/listar.twig', $dadosParaView);
	}

	public function enviarAction(Application $app, Request $request, $id) {
		$pessoa = $app['orm']->getRepository('\SistemaTCC\Model\Pessoa')->findOneByEmail($app['user']->getUsername());
		$aluno = $app['orm']->getRepository('\SistemaTCC\Model\Aluno')->findOneByPessoa($pessoa);
		if(!$aluno){
			return $app['twig']->render('alerta.twig',['tipo'=>'danger','mensagem'=>'Somente alunos podem acessar está área.']);
		}
		
		$etapa = $app['orm']->getRepository('\SistemaTCC\Model\Etapa')->find($id);
		
		if (!$etapa) {
			return $app->redirect('../enviaretapa/listar');
		}
		
		$etapa_entrega = $app['orm']->getRepository('\SistemaTCC\Model\EtapaEntrega')->findOneBy(['etapa'=>$etapa,'aluno'=>$aluno]);
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
