<?php 
require_once "incs/inc_checkauth.php";

/* O perfil do usuário define quais projetos são visiveis nesta tela e quais ações podem ser feitas. */
$is_gerente = $DADOS_USUARIO["perfil_usuario"]=="1";
$is_analista = $DADOS_USUARIO["perfil_usuario"]=="2";
$is_admin = $DADOS_USUARIO["perfil_usuario"]=="3";

$id_projeto = intval($_GET["prj"]);
$retornolinhas=0;

if(!$is_analista){
	/* 1. Dados do Projeto (validando se o usuário tem acesso a ele, inicialmente) */
	$cond = $is_admin ? "id=id" : " exists (SELECT 1 FROM projeto_equipe pe WHERE pe.id_projeto = projeto.id and pe.perfil_usuario = 'Gerente' and pe.id_usuario = '". $DADOS_USUARIO["id"]."')";
	$proj = new projeto();
	$proj->listar(0,1,$proj->campo_id,"(".$proj->campo_id."=".$id_projeto ." AND $cond )");
	$retornolinhas = $proj->numRegs;
		
	/* 2. Risco do Projeto */
	if($retornolinhas>0){
		$projeto = $proj->resultados[0];
		$prr = new projeto_risco();
		$prr->listar(0,0,"id","(id=id and id_projeto='".$projeto["id"]."' )");
		$risco_projeto = $proj->numRegs>0? $prr->resultados[0]["risco"]:"";
		$id_risco = $proj->numRegs>0? $prr->resultados[0]["id"]:"";
	}
}
if($retornolinhas<1){
	die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
		 <!--
		 history.go(-1);
		 //-->
		 </SCRIPT>');
}

$termo_pesquisado = "";
if(isset($var_obj)){
	if(isset($var_obj->termo_pesquisado)){
		$termo_pesquisado = $var_obj->termo_pesquisado;
	}
}

$cond = $is_admin ? "" : " exists (SELECT 1 FROM projeto_equipe pe WHERE pe.id_projeto = projeto.id and id_usuario = '". $DADOS_USUARIO["id"]."')";

$ff = new funcoes();

$cor_status = array("5"=>"impedido","4"=>"execucao","3"=>"atraso","1"=>"pendente","2"=>"concluido","6"=>"cancelado");

?>
<!doctype html>
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
		function salvarRisco(){
			let descricao = O("risco2").value;
			if(descricao == ""){
				alert("Preencha a descrição do Risco do projeto");
				O("risco").focus();
			}else{
				document.faxs.risco.value = descricao;
				document.faxs.submit();
			}
		}
		</script>
		<!-- [if lt IE 9]>

			<script src="js/html5shiv.js"></script>
			
		<![endif] -->
	</head>
	<body id="home">		
	<!-- Início DIV base -->
		<div>

			<!-- Início Cabeçalho -->
				<?php include "incs/inc-cabecalho.php";?>
			<!-- Fim Cabeçalho -->
			
			<!-- Inicio Main  -->
			<main id="main-page">
				<div id="grid">
					<div class="tit-page">Riscos do Projeto</div>
						<div class="div-form2">
							<div class="form">
								<div class="label">Título</div>
								<div><input type="text" disabled class="campo2" value="<?php echo str_replace('"',"'",utf8_decode($projeto["titulo"]))?>"></div>
							</div>
							<div class="form">
								<div class="label">Descrição</div>
								<div><textarea class="textarea" name="risco" id="risco2"><?php echo utf8_decode($risco_projeto); ?></textarea></div>
							</div>
						</div>
						<div class="div-form3">
							<div class="botoes">
								<div>
								<button class="bt-cancelar" onclick="location.href='ger-projeto.php?prj=<?php echo $_GET["prj"]; ?>'">Cancelar</button></div>
								<div>
								<button class="bt-ok" onclick="javascript:salvarRisco();">Confirmar</button></div>
							</div>
						</div>
					</div>

				</div>
		
			</main>
			<!-- Fim Main  -->

			<form name="faxs" id="faxs" method="POST" enctype="urlencoded" style="margin:0px;">
				<input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $projeto["id"]?>">
				<input type="hidden" name="escopo" id="escopo" value="projeto_risco">
				<input type="hidden" name="acao" id="acao" value="salvar_risco">
				<input type="hidden" name="risco" id="risco" value="">
				<input type="hidden" name="id" id="id" value="<?php echo $id_risco; ?>">
			</form>

			<!-- Início Rodapé -->
			<?php include "incs/inc-rodape.php";?>
			<!-- Fim Rodapé -->

		</div>
		<!-- Fim DIV base -->

	</body>
</html>