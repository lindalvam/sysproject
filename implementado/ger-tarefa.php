<?php 
require_once "incs/inc_checkauth.php";

/* O perfil do usuário define quais projetos são visiveis nesta tela e quais ações podem ser feitas. */
$is_gerente = $DADOS_USUARIO["perfil_usuario"]=="1";
$is_analista = $DADOS_USUARIO["perfil_usuario"]=="2";
$is_admin = $DADOS_USUARIO["perfil_usuario"]=="3";

$redir = $_GET["redir"];
$id_projeto = intval($_GET["prj"]);
$id_tarefa = intval($_GET["trf"]);
$retornolinhas=0;
$projetoTitulo = "";
if($id_tarefa==0){ // Inclusão de tarefa
	if(!$is_analista){
		/* 1. Dados do Projeto (validando se o usuário tem acesso a ele, inicialmente) */
		$cond = $is_admin ? "id=id" : " exists (SELECT 1 FROM projeto_equipe pe WHERE pe.id_projeto = projeto.id and pe.perfil_usuario = 'Gerente' and pe.id_usuario = '". $DADOS_USUARIO["id"]."')";
		$proj = new projeto();
		$proj->listar(0,1,$proj->campo_id,"(".$proj->campo_id."=".$id_projeto ." AND $cond )");
		$retornolinhas = $proj->numRegs;
			
		/* 2. Fases do Projeto */
		if($retornolinhas>0){
			$projeto = $proj->resultados[0];
			$projetoTitulo = $projeto["titulo"];
			$prf = new projeto_fase();
			$prf->listar(0,0,"ordem","(id=id and id_projeto='".$projeto["id"]."' )");
			$fases_projeto = $prf->resultados;
			
			$pre = new projeto_equipe();
			$pre->comNome();
			$pre->listar(0,0,"responsavel_display","(id=id and id_projeto='".$projeto["id"]."' )");
			$equipe_projeto = $pre->resultados;
			
		}
	}
	if($retornolinhas<1){
		die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
			 <!--
			 history.go(-1);
			 //-->
			 </SCRIPT>');
	}
}else{ // Pesquisar a Tarefa:
  	$trf = new tarefa();
	$trf->comNomeProjeto();
	$trf->comNomeResponsavel();
	$trf->comNomeFase();
	$trf->pegaPorId($id_tarefa);
	$tarefa = $trf->bd->DB_RESULTADO[0];	
	$projetoTitulo = $tarefa["projeto_display"];
	$comentarioTarefa = $tarefa["comentario"];
	
	if($projetoTitulo != ""){
		$prf = new projeto_fase();
		$prf->listar(0,0,"ordem","(id=id and id_projeto='".$id_projeto."' )");
		$fases_projeto = $prf->resultados;
		
		$pre = new projeto_equipe();
		$pre->comNome();
		$pre->listar(0,0,"responsavel_display","(id=id and id_projeto='".$id_projeto."' )");
		$equipe_projeto = $pre->resultados;
		
	}
	
	//print_r($tarefa);
	//print_r($trf);
}


$ff = new funcoes();

$cor_status = array("5"=>"impedido","4"=>"execucao","3"=>"atraso","1"=>"pendente","2"=>"concluido","6"=>"cancelado");

?><!doctype html>
<html lang="pt-BR">
	<head>
		<meta charset="utf-8">
		<title>SYSProject - Sistema de Gerenciamento de Projetos</title>
		<meta name="description" content="Sistema de Gerenciamento de Projeto">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/normalize.css">
		<link rel="stylesheet" href="css/estilo.css">
		<script src="js/jquery.js"></script>
		<script src="js/script.js"></script>
		<script type="text/javascript">
		
		function O(campo){
			return document.getElementById(campo);
		}
		
		/* Ações do formulário */
		function cancelar_acao(){
			history.go(-1);
		}
		
		function validar_tarefa(){
			let fase = O("fase_projeto").value;
			let descricao = O("descricao_tarefa").value;
			let responsavel = O("responsavel_tarefa").value;
			let dt_inicio = O("dt_inicio_tarefa").value;
			let dt_fim = O("dt_fim_tarefa").value;
			let status = O("status_da_tarefa").value;
			let comentario = O("comentario_tarefa").value;
			
			if(fase==""){
				alert("É obrigatório selecionar uma fase para esta tarefa");
				 O("fase_projeto").focus();
			}else if(descricao==""){
				alert("É obrigatório preencher uma descrição para esta tarefa");
				 O("descricao_tarefa").focus();
			}else if(responsavel==""){
				alert("É obrigatório selecionar um responsável para esta tarefa");
				 O("responsavel_tarefa").focus();
			}else if(dt_inicio==""){
				alert("É obrigatório informar uma data de início para esta tarefa");
				 O("dt_inicio").focus();
			}else if(dt_fim==""){
				alert("É obrigatório informar uma data final para esta tarefa");
				 O("dt_fim").focus();
			 <?php if($id_tarefa!=0){ ?>
			}else if(status==""){
				alert("É obrigatório selecionar um status para esta tarefa");
				 O("status_da_tarefa").focus();
			<?php }?>
			}else if(comentario==""&&document.faxs.id.value!=""){
				alert("É obrigatório preencher um comentário para esta tarefa");
				 O("comentario_tarefa").focus();
			}else{
				// Submeter ingestao
				document.faxs.id_fase.value=fase;
				document.faxs.id_responsavel.value=responsavel;
				document.faxs.descricao.value=descricao;
				document.faxs.comentario.value=comentario;
				document.faxs.dt_inicio.value=dt_inicio;
				document.faxs.dt_fim.value=dt_fim;
				document.faxs.status_tarefa.value=<?php echo $id_tarefa!=0?"status":"1"; ?>;
				document.faxs.submit();				
			}
			
		}
		
		function inicia(){
			<?php if($id_tarefa>0) {?>
				O("fase_projeto").value="<?php echo $tarefa["id_fase"]; ?>";
				O("responsavel_tarefa").value="<?php echo $tarefa["id_responsavel"]; ?>";
				O("descricao_tarefa").value="<?php echo $ff->encodeToUtf8($tarefa["descricao"]); ?>";
				O("dt_inicio_tarefa").value="<?php echo $tarefa["dt_inicio"]; ?>";
				O("dt_fim_tarefa").value="<?php echo $tarefa["dt_fim"]; ?>";
				O("status_da_tarefa").value="<?php echo $tarefa["status_tarefa"]; ?>";
			<?php } ?>
			
		}
		</script>
		<!-- [if lt IE 9]>

			<script src="js/html5shiv.js"></script>
			
		<![endif] -->
	</head>
	<body id="home" onload="javascript:inicia()">		
	<!-- Início DIV base -->
		<div>

			<!-- Início Cabeçalho -->
				<?php include "incs/inc-cabecalho.php";?>
			<!-- Fim Cabeçalho -->
			
			<!-- Inicio Main  -->
			<main id="main-page">
				<div id="grid">
					<div class="tit-page"><?php echo $id_tarefa==0?"Incluir":"Gerenciar"; ?> Tarefa</div>
						<div class="div-form2">
							<div class="form">
								<div class="label">Projeto</div>
								<div><input type="text" class="campo2" disabled value="<?php echo str_replace('"',"'",$ff->encodeToUtf8($projetoTitulo))?>"></div>
							</div>
							<div class="form">
								<div class="label">Fase</div>
								<div class="selecao"><select name="fase_projeto" id="fase_projeto" <?php echo $is_analista?"disabled":""; ?>>
									<option value="">Selecione</option>
									<?php for($x=0;$x<count($fases_projeto);$x++){ ?>
									<option value="<?php echo $fases_projeto[$x]["id"]?>"><?php echo $ff->encodeToUtf8($fases_projeto[$x]["fase"]); ?></option>
									<?php } ?>
								</select></div>
							</div>
							<div class="form">
								<div class="label">Descrição</div>
								<div><input type="text" class="campo2" <?php echo $is_analista?"disabled":""; ?> name="descricao_tarefa" id="descricao_tarefa"  value="<?php echo str_replace('"',"'",utf8_decode($descricao))?>" maxlength="100"></div>
							</div>
							<div class="form">
								<div class="label">Responsável</div>
								<div class="selecao"><select name="responsavel_tarefa" id="responsavel_tarefa" <?php echo $is_analista?"disabled":""; ?>>
									<option value="">Selecione</option>
									<?php for($x=0;$x<count($equipe_projeto);$x++){ ?>
									<option value="<?php echo $equipe_projeto[$x]["id_usuario"]?>"><?php echo utf8_decode($equipe_projeto[$x]["responsavel_display"]); ?></option>
									<?php } ?>
								</select></div>
							</div>
							<div class="form">
								<div class="label">Data Início</div>
								<div><input type="date" min="<?=date('Y-m-d')?>" class="campo2" name="dt_inicio_tarefa" id="dt_inicio_tarefa" maxlength="10"></div>
							</div>
							<div class="form">
								<div class="label">Data Fim</div>
								<div><input type="date" min="<?=date('Y-m-d')?>"  class="campo2" name="dt_fim_tarefa" id="dt_fim_tarefa" maxlength="10" ></div>
							</div>
							<div class="form" <?php if($id_tarefa==0){ ?>style="display:none" <?php }?>>
								<div class="label">Anotação</div>
								<div><textarea class="textarea"  name="comentario_tarefa" id="comentario_tarefa" ><?php echo $comentarioTarefa; ?></textarea></div>
							</div>
							<div class="form" <?php if($id_tarefa==0){ ?>style="display:none" <?php }?>>
								<div class="label">Status</div>
								<div class="selecao"><select name="status_da_tarefa" id="status_da_tarefa">
									<option value="">Selecione</option>
									<option value="1">Pendente</option>
									<option value="2">Concluído</option>
									<option value="3">Atrasado</option>
									<option value="4">Em execução</option>
									<option value="5">Impedido</option>
									<option value="6">Cancelado</option>
									
								</select></div>
							</div>
						</div>
						<div class="div-form3">
							<div class="botoes">
								<div>
								<button class="bt-cancelar" onclick="javascript:cancelar_acao()">Cancelar</button></div>
								<div>
								<button class="bt-ok" onclick="javascript:validar_tarefa();">Confirmar</button></div>
							</div>
						</div>
					</div>
				</div>
		
			</main>
			<!-- Fim Main  -->
			
			<form name="faxs" id="faxs" method="POST" enctype="urlencoded" style="margin:0px;">
					<input type="hidden" name="regs" id="regs" value="">
					<input type="hidden" name="redir" id="redir" value="<?php echo $redir=="mt"?"minhas-tarefas.php?a=a":"" ?>">
					<input type="hidden" name="escopo" id="escopo" value="tarefa">
			        <input type="hidden" name="acao" id="acao" value="salvar_tarefa">
			        <input type="hidden" name="id_fase" id="id_fase" value="">
					<input type="hidden" name="id" id="id" value="<?php echo $id_tarefa==0?"":$id_tarefa;?>">
			        <input type="hidden" name="id_responsavel" id="id_responsavel" value="">
			        <input type="hidden" name="descricao" id="descricao" value="">
			        <input type="hidden" name="comentario" id="comentario" value="">
			        <input type="hidden" name="dt_inicio" id="dt_inicio" value="">
			        <input type="hidden" name="dt_fim" id="dt_fim" value="">
			        <input type="hidden" name="status_tarefa" id="status_tarefa" value="">
					<input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $id_projeto;?>">
				</form>

			<!-- Início Rodapé -->
			<?php include "incs/inc-rodape.php";?>
			<!-- Fim Rodapé -->

		</div>
		<!-- Fim DIV base -->

	</body>
</html>