<?php 
require_once "incs/inc_checkauth.php";


// Listagem de Tarefas
$tr = new tarefa();
$tr->comNomeProjeto(); // Display nome do projeto
$tr->comNomeResponsavel(); // Display nome do Responsavel da tarefa
$tr->comNomeFase(); // Display nome da fase
$tr->comNomeStatus(); // Display status da tarefa 

$tr->listar(0,0,"dt_fim, ordem_prioridade","id_responsavel=".$DADOS_USUARIO["id"]);
$tarefas=$tr->resultados;

$ff = new funcoes();
$cor_status = array("5"=>"impedido","4"=>"execucao","3"=>"atraso","1"=>"pendente");
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
			
			function sortTable(n) {
			  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  table = O("table");
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
			  for(var i=1;i<6;i++){
				O("tbord"+i).style.visibility="hidden";
			  }
			  O("tbord"+n).style.visibility="visible";
			  O("tbord"+n).style.transform=dir=="asc"?"rotateX(180deg)":"rotateX(0deg)";
			  
			}
			
			
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
			
				/* Checkbox control */
					
		function selecionaTudo(ok){
		  var ids="";
		  try{
		   if(document.ipo.chk_prj.length==undefined){
			   document.ipo.chk_prj.checked=ok;
			   add(document.ipo.chk_prj);
		   }
		   for(var i=0;i<document.ipo.chk_prj.length;i++){
			  document.ipo.chk_prj[i].checked=ok;
			  add(document.ipo.chk_prj[i]);
			}    
		  }catch(err){
			alert(err.description);
			// Nenhum registro
		  }
		}

		function add(chk){
		  var ids=document.faxs.regs.value;
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
		  document.faxs.regs.value=ids;
		}
		
		/* Gerenciar Tarefa */
		function gerenciar_tarefa(){
			let reg = document.faxs.regs.value;
			if(reg==""){
				alert("Selecione uma tarefa para gerenciar.");
			}else if(isNaN(reg)){
				alert("Apenas uma tarefa pode ser gerenciado de cada vez.");
			}else{// redirect:
				console.log(reg);
				let prj = O("prj_id_"+reg).value; // Id Projeto
				location.href="ger-tarefa.php?redir=mt&prj="+prj+"&trf="+reg;
			}
		}
		
		function bloquear_tarefa(fase){
			let ids =document.faxs.regs.value;
			
			if(ids==""){
				alert("Selecione pelo menos uma tarefa para bloquear.");
			}else{ 
				document.faxs.escopo.value="tarefa";
				document.faxs.acao.value="bloquear";
				document.faxs.regs.value=ids;
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
					<div class="tit-page">Minhas Tarefas</div>
					<!--<div class="div-acoes">
						<div class="div-pesquisa"><div id="pesquisa">Pesquisar&nbsp;<input type="text" class="cp-pesquisa"><button class="bt-ok">Ok</button></div></div>
					</div>-->
					<div class="grid">
						<div class="div-fase">
							<div class="acao"><button class="bt-acao" onclick="javascript:gerenciar_tarefa();"><img class="ico-acao" src="img/ico-enrollment.svg" alt="">Gerenciar Tarefa</button></div>
							<div class="acao"><button class="bt-acao" onclick="javascript:bloquear_tarefa();"><img class="ico-acao" src="img/ico-lock.svg" alt="">Bloquear Tarefa</button></div>
						</div>
						<div><form name="ipo" id="ipo" onsubmit="javascript: return false;" style="margin:0px;">
							<table id="table">
							  <tr>
							  	<th><input type="checkbox" id="chk_all" name="chk_all" value="pp" onclick="javascript:selecionaTudo(this.checked);"></th>
							    <th onclick="javascript:sortTable(1)">Tarefa<img class="ico-ordem" src="img/ic_arrow-down.svg" 		id="tbord1" alt=""></th>
							    <th onclick="javascript:sortTable(2)">Data Início<img class="ico-ordem" src="img/ic_arrow-down.svg" id="tbord2" alt=""></th>
							    <th onclick="javascript:sortTable(3)">Data Fim<img class="ico-ordem" src="img/ic_arrow-down.svg" 	id="tbord3" alt=""></th>
							    <th onclick="javascript:sortTable(4)">Projeto<img class="ico-ordem" src="img/ic_arrow-down.svg" 	id="tbord4" alt=""></th>
							    <th onclick="javascript:sortTable(5)">Status<img class="ico-ordem" src="img/ic_arrow-down.svg" 		id="tbord5" alt=""></th>
							  </tr>
							   <?php for($x=0; $x<count($tarefas);$x++){ ?>
							  <tr>
							  	<td><input type="checkbox" name="chk_prj" value="<?php echo $tarefas[$x]["id"]; ?>" onclick="javascript:add(this);" ><input type="hidden" id="prj_id_<?php echo $tarefas[$x]["id"]; ?>" value="<?php echo $tarefas[$x]["id_projeto"]; ?>"></td>
								
								<td><?php echo $ff->encodeToUtf8($tarefas[$x]["descricao"]); ?></td>
								<td><?php echo $ff->data_mysql2php($tarefas[$x]["dt_inicio"],true); ?></td>
								<td><?php echo $ff->data_mysql2php($tarefas[$x]["dt_fim"],true); ?></td>
								<td><?php echo $ff->encodeToUtf8($tarefas[$x]["projeto_display"]); ?></td>
								<td class="st-<?php echo $cor_status[$tarefas[$x]["status_tarefa"]]?>"><?php echo $ff->encodeToUtf8($tarefas[$x]["status_display"]); ?></td>
								
							  </tr>
							   <?php } ?>
							</table></form>
						</div>
					</div>
				</div>			
		
			</main>
			<!-- Fim Main  -->

				<form name="faxs" id="faxs" method="POST" enctype="urlencoded" style="margin:0px;">
					<input type="hidden" name="regs" id="regs" value="">
					<input type="hidden" name="escopo" id="escopo" value="">
			        <input type="hidden" name="acao" id="acao" value="">
					<input type="hidden" name="status" id="status" value="">
				</form>
				
			<!-- Início Rodapé -->
			<?php include "incs/inc-rodape.php";?>
			<!-- Fim Rodapé -->
			

		</div>
		<!-- Fim DIV base -->

	</body>
</html>