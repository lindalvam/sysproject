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
		
	/* 2. Fases do Projeto */
	if($retornolinhas>0){
		$projeto = $proj->resultados[0];
		$prf = new projeto_fase();
		$prf->listar(0,0,"ordem","(id=id and id_projeto='".$projeto["id"]."' )");
		$fases_projeto = $prf->resultados;
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
		/* Sort Table */
		
	function sortTable(n, idtabela) {
			  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  table = O(idtabela);
			  switching = true;
			  // Set the sorting direction to ascending:
			  dir = "asc";
			  /* Make a loop that will continue until
			  no switching has been done: */
			  while (switching) {
				// Start by saying: no switching is done:
				switching = false;
				rows = table.rows;
				/* Loop through all table rows (except the
				first, which contains table headers): */
				for (i = 1; i < (rows.length - 1); i++) {
				  // Start by saying there should be no switching:
				  shouldSwitch = false;
				  /* Get the two elements you want to compare,
				  one from current row and one from the next: */
				  x = rows[i].getElementsByTagName("TD")[n];
				  y = rows[i + 1].getElementsByTagName("TD")[n];
				  /* Check if the two rows should switch place,
				  based on the direction, asc or desc: */
				  if (dir == "asc") {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  // If so, mark as a switch and break the loop:
					  shouldSwitch = true;
					  break;
					}
				  } else if (dir == "desc") {
					if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
					  // If so, mark as a switch and break the loop:
					  shouldSwitch = true;
					  break;
					}
				  }
				}
				if (shouldSwitch) {
				  /* If a switch has been marked, make the switch
				  and mark that a switch has been done: */
				  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				  switching = true;
				  // Each time a switch is done, increase this count by 1:
				  switchcount ++;
				} else {
				  /* If no switching has been done AND the direction is "asc",
				  set the direction to "desc" and run the while loop again. */
				  if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				  }
				}
			  }
			  for(var i=1;i<7;i++){
				O(idtabela+"_brd_"+i).style.visibility="hidden";
			  }
			  O(idtabela+"_brd_"+n).style.visibility="visible";
			  O(idtabela+"_brd_"+n).style.transform=dir=="asc"?"rotateX(180deg)":"rotateX(0deg)";
			  
			}
		
		
		
		/* Checkbox control */
		
		function O(campo){
			return document.getElementById(campo);
		}
		
		function selecionaTudo(fase,ok){
		  var ids="";
		  var checks = document.getElementsByName("chk_fs_"+fase);
		  //console.log(checks);
		  try{
		   for(var i=0;i<checks.length;i++){
			  checks[i].checked=ok;
			  add(checks[i],fase);
			}    
		  }catch(err){
			alert(err.description);
			// Nenhum registro
		  }
		}

		function add(chk,fase){
		  var campo = O("regs_"+fase);
		  var ids=campo.value;
		  var mids=ids.split(",");
		  if(chk.checked){
			ids+=ids==""?"":",";
			ids+=chk.value;
		  }else{
			ids="";
			for(var i=0;i<mids.length;i++){
			   if(mids[i]!=chk.value){
				 ids+=ids==""?"":",";
				 ids+=mids[i];
			   }
			}
		  }
		  campo.value=ids;
		}
		
		/* Tarefas - ações: */
		
		function gerenciar_tarefa(fase){
			let ids = O("regs_"+fase).value;
			
			if(ids==""){
				alert("Selecione uma tarefa para gerenciar.");
			}else if(isNaN(parseInt(ids))){
				alert("Apenas uma tarefa pode ser gerenciada de cada vez.");
			}else{ 
				location.href='ger-tarefa.php?prj=<?php echo $id_projeto?>&trf='+ids;
			}			
		}
		
		function bloquear_tarefa(fase){
			let ids = O("regs_"+fase).value;
			
			if(ids==""){
				alert("Selecione pelo menos uma tarefa para bloquear.");
			/*}else if(isNaN(parseInt(reg))){
				alert("Apenas um projeto pode ser gerenciado de cada vez.");*/
			}else{ 
				document.faxs.escopo.value="tarefa";
				document.faxs.acao.value="bloquear";
				document.faxs.regs.value=ids;
				document.faxs.submit();
			}			
		}
		
		
		function excluir_tarefa(fase){
			let ids = O("regs_"+fase).value;
			
			if(ids==""){
				alert("Selecione pelo menos uma tarefa para excluir.");
			/*}else if(isNaN(parseInt(reg))){
				alert("Apenas um projeto pode ser gerenciado de cada vez.");*/
			}else{ 
				if(confirm("Deseja realmente excluir essa(s) tarefa(s)? \nNão será possível recuperar esses registros excluídos.")){
					document.faxs.escopo.value="tarefa";
					document.faxs.acao.value="excluir";
					document.faxs.regs.value=ids;
					document.faxs.submit();
				}
			}			
		}
		
		/* Projetos Ações */
		
		function bloquear_projeto(){
			if(confirm("Deseja realmente bloquear esse projeto?")){
					document.faxs.escopo.value="projeto";
					document.faxs.acao.value="muda_status";
					document.faxs.regs.value="<?php echo $id_projeto ?>";
					document.faxs.status.value=5;
					document.faxs.submit();
				}			
		}
		function encerrar_projeto(){
			if(confirm("Deseja realmente encerrar esse projeto?")){
					document.faxs.escopo.value="projeto";
					document.faxs.acao.value="muda_status";
					document.faxs.regs.value="<?php echo $id_projeto ?>";
					document.faxs.status.value=2;
					document.faxs.submit();
				}			
		}
		function cancelar_projeto(){
			if(confirm("Deseja realmente cancelar esse projeto?")){
					document.faxs.escopo.value="projeto";
					document.faxs.acao.value="muda_status";
					document.faxs.regs.value="<?php echo $id_projeto ?>";
					document.faxs.status.value=6;
					document.faxs.submit();
				}			
		}
		
		/* Prioridade das tarefas */
		
		function aumentaPrioridade(obj){
				if(global_tarefa_selecionada!=0){
					console.log("Aumentar priodade da Tarefa Id "+global_tarefa_selecionada);
					document.faxs.escopo.value="tarefa";
					document.faxs.acao.value="aumenta_prioridade";
					document.faxs.regs.value=global_tarefa_selecionada;
					document.faxs.submit();
				}
		}
		
		function diminuiPrioridade(obj){
				if(global_tarefa_selecionada!=0){
					console.log("Diminuir priodade da Tarefa Id "+global_tarefa_selecionada);
					document.faxs.escopo.value="tarefa";
					document.faxs.acao.value="diminui_prioridade";
					document.faxs.regs.value=global_tarefa_selecionada;
					document.faxs.submit();
				}
		}
		
		var global_tarefa_selecionada = 0;
		function selecionaTarefaPrioridade(n){
			global_tarefa_selecionada = n;
		}
		
		
		function lerNoticias(){
			<?php if($_GET["zs"]=="bq"){?>
				alert("Tarefa(s) bloqueadas com sucesso.");
			<?php } ?>
			<?php if($_GET["zs"]=="xx"){?>
				alert("Houve um erro na tentativa de excluir a(s) tarefa(s). Consulte o log do sistema e contate o administrador.");
			<?php } ?>
			<?php if($_GET["zs"]=="xs"){?>
				alert("Tarefa(s) excluídas com sucesso.");
			<?php } ?>
			<?php if($_GET["zs"]=="t"){?>
				alert("Tarefa incluída com sucesso.");
			<?php } ?>
			<?php if($_GET["zs"]=="s5"){?>
				alert("Projeto bloqueado.");
			<?php } ?>
			<?php if($_GET["zs"]=="s2"){?>
				alert("Projeto encerrado.");
			<?php } ?>
			<?php if($_GET["zs"]=="s6"){?>
				alert("Projeto cancelado.");
			<?php } ?>
			
		}
		
		
		</script>
		<!-- [if lt IE 9]>

			<script src="js/html5shiv.js"></script>
			
		<![endif] -->
	</head>
	<body id="home" onload="lerNoticias();">		
	<!-- Início DIV base -->
		<div>

			<!-- Início Cabeçalho -->
				<?php include "incs/inc-cabecalho.php";?>
			<!-- Fim Cabeçalho -->
			
			<!-- Inicio Main  -->
			<main id="main-page">
				<div id="grid">
					<div class="tit-page">Gerenciar Projeto <?php echo $ff->encodeToUtf8($projeto["titulo"]); ?></div>
					<div class="div-acoes">
						<div class="acao"><button class="bt-novo" onclick="location.href='ger-tarefa.php?prj=<?php echo $projeto["id"]; ?>'"><img class="ico-acao" src="img/ico-plus-circle.svg" alt="">Incluir Tarefa</button></div>
						<div class="acao"><button class="bt-risco" onclick="location.href='ger-riscos.php?prj=<?php echo $projeto["id"]; ?>'"><img class="ico-acao" src="img/ico-danger.svg" alt="">Riscos</button></div>
						<!--<div class="acao"><button class="bt-acao" onclick="location.href='ger-equipe.html'"><img class="ico-acao" src="img/ico-user-squad.svg" alt="">Equipe do Projeto</button></div>
						<div class="acao"><button class="bt-acao" onclick="location.href='ger-fases.html'"><img class="ico-acao" src="img/ico-diagram.svg" alt="">Fases do Projeto</button></div>
						<div class="acao"><button class="bt-acao" onclick="location.href='edi-projeto.php?prj=<?php echo $projeto["id"]; ?>'"><img class="ico-acao" src="img/ico-clipboard.svg" alt="">Cadastro do Projeto</button></div>-->
						<!-- <div class="div-pesquisa"><div id="pesquisa">Pesquisar&nbsp;<input type="text" class="cp-pesquisa"><button class="bt-ok">Ok</button></div></div> -->
						
						<div class="acao"><button class="bt-acao" onclick="javascript: bloquear_projeto()"><img class="ico-acao" src="img/ico-lock.svg" alt="">Bloquear Projeto</button></div>
						<div class="acao"><button class="bt-acao" onclick="javascript: encerrar_projeto()"><img class="ico-acao" src="img/ico-check-circle.svg" alt="">Encerrar Projeto</button></div>
						<div class="acao"><button class="bt-acao" onclick="javascript: cancelar_projeto()"><img class="ico-acao" src="img/ico-excluir.svg" alt="">Cancelar Projeto</button></div>
					</div>
					
					<?php /* ------ FASES -----*/ ?>
					<?php for($x=0;$x<count($fases_projeto);$x++){?>
					<div class="grid">
						<div class="div-fase">
							<div class="tit-fase"><?php echo $ff->encodeToUtf8($fases_projeto[$x]["fase"]); ?></div>
							<div class="acao"><button class="bt-acao" onclick="javascript:gerenciar_tarefa(<?php echo $x; ?>);"><img class="ico-acao" src="img/ico-enrollment.svg" alt="">Gerenciar Tarefa</button></div>
							<div class="acao"><button class="bt-acao" onclick="javascript:bloquear_tarefa(<?php echo $x; ?>);"><img class="ico-acao" src="img/ico-lock.svg" alt="">Bloquear Tarefa</button></div>
							<div class="acao"><button class="bt-acao" onclick="javascript:excluir_tarefa(<?php echo $x; ?>);"><img class="ico-acao" src="img/ico-trash.svg" alt="">Excluir Tarefa</button></div>
						</div>
						<div>
						   <?php /* ------ TAREFAS -----*/ ?>
						   <?php 
						   $tar = new tarefa();
						   $tar->comNomeResponsavel();
						   $tar->comNomeStatus();
						   $tar->listar(0,0,"ordem_prioridade","(id_projeto='".$fases_projeto[$x]["id_projeto"]."' AND id_fase='".$fases_projeto[$x]["id"]."')");
						   $tarefas = $tar->resultados;
						   ?>
							<table id="table_<?php echo $x; ?>">
							  <tr>
							  	<th><input type="checkbox" id="chk_<?php echo $x; ?>_all" name="chk_<?php echo $x; ?>_all" value="pp" onclick="javascript:selecionaTudo(<?php echo $x; ?>,this.checked);">
									<input type="hidden" name="regs_<?php echo $x; ?>" id="regs_<?php echo $x; ?>"></th>
							    <th onclick="javascript:sortTable(1,'table_<?php echo $x; ?>')">Tarefa<img class="ico-ordem" src="img/ic_arrow-down.svg"  		 id="table_<?php echo $x; ?>_brd_1" alt=""></th>
							    <th onclick="javascript:sortTable(2,'table_<?php echo $x; ?>')">Prioridade<img class="ico-ordem" src="img/ic_arrow-down.svg"	 id="table_<?php echo $x; ?>_brd_2" alt=""></th>
							    <th onclick="javascript:sortTable(3,'table_<?php echo $x; ?>')">Data Início<img class="ico-ordem" src="img/ic_arrow-down.svg" 	 id="table_<?php echo $x; ?>_brd_3" alt=""></th>
							    <th onclick="javascript:sortTable(4,'table_<?php echo $x; ?>')">Data Fim<img class="ico-ordem" src="img/ic_arrow-down.svg" 		 id="table_<?php echo $x; ?>_brd_4" alt=""></th>
							    <th onclick="javascript:sortTable(5,'table_<?php echo $x; ?>')">Responsável<img class="ico-ordem" src="img/ic_arrow-down.svg" 	 id="table_<?php echo $x; ?>_brd_5" alt=""></th>
							    <th onclick="javascript:sortTable(6,'table_<?php echo $x; ?>')">Status<img class="ico-ordem" src="img/ic_arrow-down.svg" 		 id="table_<?php echo $x; ?>_brd_6" alt=""></th>
							  </tr>
							  <?php for($k=0;$k<count($tarefas);$k++){ ?>
							  <tr>
							  	<td><input type="checkbox" name="chk_fs_<?php echo $x; ?>" value="<?php echo $tarefas[$k]["id"]; ?>" onclick="javascript:add(this,<?php echo $x; ?>);" ></td>
							    <td><?php echo $ff->encodeToUtf8($tarefas[$k]["descricao"]); ?></td>
							    <td><?php echo $tarefas[$k]["ordem_prioridade"]; ?><img class="ico-ordem" usemap="#priorMap" src="img/ico-reorder.svg" onmouseover="javascript:selecionaTarefaPrioridade(<?php echo $tarefas[$k]["id"]; ?>);"></td>
							    <td><?php echo $ff->data_mysql2php($tarefas[$k]["dt_inicio"],true); ?></td>
							   	<td><?php echo $ff->data_mysql2php($tarefas[$k]["dt_fim"],true); ?></td>
							   	<td><?php echo $ff->encodeToUtf8($tarefas[$k]["responsavel_display"]); ?></td>
							   	<td class="st-<?php echo $cor_status[$tarefas[$k]["status_tarefa"]]?>"><?php echo $ff->encodeToUtf8($tarefas[$k]["status_display"]); ?></td>
							  </tr>
							  <?php } ?>
							  
							</table>
						</div>
					</div>
					
					<?php } ?>
					
				</div>			
		
			</main>
			<!-- Fim Main  -->

			<form name="faxs" id="faxs" method="POST" enctype="urlencoded" style="margin:0px;">
					<input type="hidden" name="regs" id="regs" value="">
					<input type="hidden" name="escopo" id="escopo" value="">
			        <input type="hidden" name="acao" id="acao" value="">
			        <input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $id_projeto?>">
					<input type="hidden" name="status" id="status" value="">
				</form>
				<map name="priorMap" id="priorMap">
				  <area shape="rect" coords="1,1,59,10" alt="Aumentar Prioridade" title="Clique aqui para aumentar a priodade da tarefa" onclick="javascript:aumentaPrioridade(this);">
				  <area shape="rect" coords="1,11,59,20" alt="Diminuir Prioridade" title="Clique aqui para diminuir a priodade da tarefa" onclick="javascript:diminuiPrioridade(this);">
				  
				</map>

			<!-- Início Rodapé -->
			<?php include "incs/inc-rodape.php";?>
			<!-- Fim Rodapé -->
			
		</div>
		<!-- Fim DIV base -->

	</body>
</html>