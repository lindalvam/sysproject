<?php 
require_once "incs/inc_checkauth.php";

/* O perfil do usuário define quais projetos são visiveis nesta tela e quais ações podem ser feitas. */
$is_gerente = $DADOS_USUARIO["perfil_usuario"]=="1";
$is_analista = $DADOS_USUARIO["perfil_usuario"]=="2";
$is_admin = $DADOS_USUARIO["perfil_usuario"]=="3";

$termo_pesquisado = "";
if(isset($var_obj)){
	if(isset($var_obj->termo_pesquisado)){
		$termo_pesquisado = $var_obj->termo_pesquisado;
	}
}

$cond = $is_admin ? "" : " exists (SELECT 1 FROM projeto_equipe pe WHERE pe.id_projeto = projeto.id and id_usuario = '". $DADOS_USUARIO["id"]."')";

if($termo_pesquisado==""){
	$pr = new projeto();
	$pr->comGerente();
	$pr->comNomeStatus();
	
	
	$pr->listar(0,0,"dt_inicio desc",$cond);
	$projetos=$pr->resultados;
}else{
	$projetos=$var_obj->resultados;
}

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
		
		/* Pesquisa */
		function handleEnter(e){
			 var key=e.keyCode || e.which;
			  if (key==13){
				 e.preventDefault();
				 executa_pesquisa();
			  }
		}
		function executa_pesquisa(){
			let termoPesquisa = O("pesquisa_prj").value;
			if(termoPesquisa.length<3){
				alert("Digite um termo com pelo menos 3 letras para executar a pesquisa");
				O("pesquisa_prj").focus();
			}else{
				document.faxs.escopo.value="projeto";
				document.faxs.acao.value="pesquisar";
				document.faxs.regs.value=termoPesquisa;
				document.faxs.submit();				
			}			
		}
		
		/* Gerenciar Projeto */
		function gerenciar_projeto(){
			let reg = document.faxs.regs.value;
			if(reg==""){
				alert("Selecione um projeto para gerenciar.");
			}else if(isNaN(parseInt(reg))){
				alert("Apenas um projeto pode ser gerenciado de cada vez.");
			}else{// redirect:
				location.href="ger-projeto.php?prj="+reg;
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
					<div class="tit-page">Meus Projetos</div>
					<div class="div-acoes">
						<div class="acao"></div>
						<div class="acao"></div>
						<div class="acao"></div>
						<div class="div-pesquisa"><div id="pesquisa">Pesquisar&nbsp;<input type="text" id="pesquisa_prj" class="cp-pesquisa" onkeypress="javascript:handleEnter(event)" maxlength="100" value="<?php echo $termo_pesquisado; ?>"><button class="bt-ok" onclick="javascript:executa_pesquisa();">Ok</button></div></div>
					</div>
					<div class="grid">
						<div class="div-fase">
								<div class="acao"><button class="bt-acao" style="visibility:hidden"><img class="ico-acao" src="img/ico-edit.svg" alt="">Editar Projeto</button></div>
								<div class="acao"><button class="bt-acao" style="visibility:hidden"><img class="ico-acao" src="img/ico-trash.svg" alt="">Excluir Projeto</button></div>
								<div class="acao"><button class="bt-acao" <?php if($is_analista){?> style="visibility:hidden" <?php }?>onclick="javascript:gerenciar_projeto();"><img class="ico-acao" src="img/ico-enrollment.svg" alt="">Gerenciar Projeto</button></div>
								<!--<div class="acao"><button class="bt-acao"><img class="ico-acao" src="img/ico-diagram.svg" alt="" onclick="location.href='inc-fase.html'">Fases do Projeto</button></div>
								<div class="acao"><button class="bt-acao"><img class="ico-acao" src="img/ico-user-squad.svg" alt="">Equipe do Projeto</button></div>-->
						</div>
						<div><form name="ipo" id="ipo" onsubmit="javascript: return false;" style="margin:0px;">
							<table id="table">
							  <tr>
							  	<th><input type="checkbox" id="chk_all" name="chk_all" value="pp" onclick="javascript:selecionaTudo(this.checked);"></th>
							    <th onclick="javascript:sortTable(1)">Título<img class="ico-ordem" src="img/ic_arrow-down.svg"       id="tbord1" alt=""></th>
							    <th onclick="javascript:sortTable(2)">Data Início<img class="ico-ordem" src="img/ic_arrow-down.svg"  id="tbord2" alt=""></th>
							    <th onclick="javascript:sortTable(3)">Data Fim<img class="ico-ordem" src="img/ic_arrow-down.svg"     id="tbord3" alt=""></th>
							    <th onclick="javascript:sortTable(4)">Gerente<img class="ico-ordem" src="img/ic_arrow-down.svg"      id="tbord4" alt=""></th>
							    <th onclick="javascript:sortTable(5)">Status<img class="ico-ordem" src="img/ic_arrow-down.svg"       id="tbord5" alt=""></th>
							  </tr>
							 <?php for($x=0;$x<count($projetos);$x++){ ?>
							  <tr>
							  	<td><input type="checkbox" name="chk_prj" value="<?php echo $projetos[$x]["id"]; ?>" onclick="javascript:add(this);" ></td>
							    <td><?php echo $ff->encodeToUtf8($projetos[$x]["titulo"])?></td>
							    <td><?php echo $ff->data_mysql2php($projetos[$x]["dt_inicio"],true); ?></td>
							   	<td><?php echo $ff->data_mysql2php($projetos[$x]["dt_fim"],true); ?></td>
							   	<td><?php echo $projetos[$x]["responsavel_display"]?></td>
							   	<td class="st-<?php echo $cor_status[$projetos[$x]["status_projeto"]]?>"><?php echo $ff->encodeToUtf8($projetos[$x]["status_display"])?></td>
							  </tr>
							  <?php } ?>
							</table>
							</form>
						</div>
					</div>
				</div>			
		
			</main>
            
			<!-- Fim Main  -->
			
			<form name="faxs" id="faxs" method="POST" enctype="urlencoded" style="margin:0px;">
					<input type="hidden" name="regs" id="regs" value="">
					<input type="hidden" name="escopo" id="escopo" value="projeto">
			        <input type="hidden" name="acao" id="acao" value="excluir">
					<input type="hidden" name="status" id="status" value="">
				</form>

			<!-- Início Rodapé -->
			<?php include "incs/inc-rodape.php";?>
			<!-- Fim Rodapé -->

		</div>
		<!-- Fim DIV base -->

	</body>
</html>