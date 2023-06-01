<?php 
require_once "incs/inc_checkauth.php";

// Total Projetos: 
$pr = new projeto();
$qt_projetos_total = $pr->numRegs("");
$qt_projetos_atrasados = $pr->numRegs("status_projeto=3");
$qt_projetos_impedidos = $pr->numRegs("status_projeto=5");
$qt_projetos_emexecucao = $pr->numRegs("status_projeto IN(4,3)"); // Incluir Em atraso e em execução
$qt_projetos_pendentes = $pr->numRegs("status_projeto=1");
$qt_projetos_concluidos = $pr->numRegs("status_projeto=2");
$qt_projetos_cancelados = $pr->numRegs("status_projeto=6");

// Total Tarefas: 
$tr = new tarefa();
$qt_tarefas_atrasadas = $tr->numRegs("status_tarefa=3");
$qt_tarefas_impedidas = $tr->numRegs("status_tarefa=5");

// Listagem de Tarefas
$tr->comNomeProjeto(); // Display nome do projeto
$tr->comNomeResponsavel(); // Display nome do Responsavel da tarefa
$tr->comNomeFase(); // Display nome da fase
$tr->comNomeStatus(); // Display status da tarefa 

$tr->listar(0,5,"dt_fim, ordem_prioridade","status_tarefa IN(1,3,4,5) and id_responsavel=".$DADOS_USUARIO["id"]);
$tarefas_em_atraso=$tr->resultados;
$numTarefasDisplay = count($tarefas_em_atraso);

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
		<script>
			function O(campo){
				return document.getElementById(campo);				
			}
			
			function incluir_legenda(canvas, texto,cor,posicao){
				const legendaposicao = [
					{pos_x: 195, pos_y:  45, text_x: 212, text_y: 56},
					{pos_x: 195, pos_y:  65, text_x: 212, text_y: 76},
					{pos_x: 195, pos_y:  85, text_x: 212, text_y: 96},
					{pos_x: 195, pos_y: 105, text_x: 212, text_y: 116},
					{pos_x: 195, pos_y: 125, text_x: 212, text_y: 136}
				];
				const aresta_px = 13;
				posicao_escolhida = legendaposicao[posicao];
				
				canvas.fillStyle = cor;
				canvas.fillRect(posicao_escolhida.pos_x, posicao_escolhida.pos_y, aresta_px, aresta_px);
				
				canvas.fillStyle = "black";
				canvas.font = "12px sans-serif";
				
				canvas.fillText(texto, posicao_escolhida.text_x, posicao_escolhida.text_y);
			}
			
			function iniciar_grafico(){
				let canvas = O("grafico_projetos").getContext("2d");
				const results = [
					{status: "Em execução", total: <?php echo $qt_projetos_emexecucao ?>, shade: "rgb(3, 4, 94)"},
					{status: "Planejado", total: <?php echo $qt_projetos_pendentes ?>, shade: "rgb(0, 119, 192)"},
					{status: "Impedido", total:<?php echo $qt_projetos_impedidos ?>, shade: "rgb(144, 224, 239)"},
					{status: "Encerrado", total: <?php echo $qt_projetos_concluidos ?>, shade: "rgb(0, 180, 216)"},
					{status: "Cancelado", total: <?php echo $qt_projetos_cancelados ?>, shade: "rgb(210, 210, 210)"}
				];
				
				let sum = 0;
				let totalNumberOfPeople = results.reduce((sum, {total}) => sum + total, 0);
				let currentAngle = 0;
				let pos = 0;
				 for (let status of results) {
					//calculating the angle the slice (portion) will take in the chart
					let portionAngle = (status.total / totalNumberOfPeople) * 2 * Math.PI;
					//drawing an arc and a line to the center to differentiate the slice from the rest
					canvas.beginPath();
					canvas.arc(75, 75, 75, currentAngle, currentAngle + portionAngle);
					currentAngle += portionAngle;
					canvas.lineTo(75, 75);
					//filling the slices with the corresponding mood's color
					canvas.fillStyle = status.shade;
					canvas.fill();
					
					incluir_legenda(canvas,status.status,status.shade,pos); pos++;
				}

			}
			
			function iniciar(){
				<?php if($numTarefasDisplay<1){?>O("tarefas").style.display="none";<?php } ?>
				O("tbord0").style.visibility="hidden";
				O("tbord2").style.visibility="hidden";
				O("tbord3").style.visibility="hidden";				
				iniciar_grafico();
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
			  for(var i=0;i<5;i++){
				O("tbord"+i).style.visibility="hidden";
			  }
			  O("tbord"+n).style.visibility="visible";
			  O("tbord"+n).style.transform=dir=="asc"?"rotateX(180deg)":"rotateX(0deg)";
			  
			}
		</script>
		<style>
			.responsiveCanvas{width:100%;height:100%} 
		</style>
		<!-- [if lt IE 9]>

			<script src="js/html5shiv.js"></script>
			
		<![endif] -->
	</head>
	<body id="home" onload="javascript: iniciar()">		
	<!-- Início DIV base -->
		<div>

			<!-- Início Cabeçalho -->
			<?php include "incs/inc-cabecalho.php";?>
			<!-- Fim Cabeçalho -->
			
			<!-- Inicio Main  -->
			<main id="main-page">
				<div id="painel">
					<div id="atalhos" class="bloco">
						<div class="atalho" onclick="javascript:location.href='meus-projetos.php';" style="cursor:pointer">
						<div><img src="img/at-projetos.svg" alt=""></div>
						<div>Meus Projetos</div>
						</div>
						<div class="atalho" style="cursor:pointer"  onclick="javascript:location.href='minhas-tarefas.php'";>
							<div><img class="" src="img/at-tasks.svg" alt=""></div>
							<div>Minhas Tarefas</div>	
						</div>
						<div class="atalho" style="cursor:pointer">
							<div><img class="" src="img/at-reports.svg" alt=""></div>
							<div>Consultar Cronogramas</div>
						</div>
						<div class="atalho" style="cursor:pointer">
							<div><img class="" src="img/at-timeline.svg" alt=""></div>
							<div>Consultar Relatórios</div>
						</div>	
					</div>
					<div id="global" class="bloco">
						<div class="global"><h3>Visão Global</h3></div>
						<div class="global">Total de Projetos <br><img src="img/graf_total.svg" class="" alt=""><h2><?php echo $qt_projetos_total?></h2></div>
						<div class="grafico"><canvas id="grafico_projetos" class="responsiveCanvas"></canvas></div>
					</div>
					<div id="alertas" class="bloco">
						<div class="alertaRed"><h4>Projetos em atraso</h4><img class=<div class="alertaRed" style="cursor:pointer;"><h4>Projetos em atraso</h4><img class="alerta-ico" src="img/ic_alerta.svg" alt=""><br><h1><?php echo $qt_projetos_atrasados?></h1></div>
						<div class="alertaRox" style="cursor:pointer;"><h4>Projetos impedidos</h4><img class="alerta-ico" src="img/ic_alerta.svg"  alt=""><br><h1><?php echo $qt_projetos_impedidos?></h1></div>
						<div class="alertaRox" style="cursor:pointer;"><h4>Tarefas impedidas</h4><img class="alerta-ico" src="img/ic_alerta.svg" alt=""><br><h1><?php echo $qt_tarefas_impedidas?></h1></div>
						<div class="alertaRed" style="cursor:pointer;"><h4>Tarefas em atraso</h4><img class="alerta-ico" src="img/ic_alerta.svg" alt=""><br><h1><?php echo $qt_tarefas_atrasadas?></h1></div>
					</div>
					<div id="tarefas" class="bloco esconde_mobile"><h4 class="tit-table">Minhas Tarefas urgentes</h4>

						<table id="table">
						  <tr>
						    <th onclick="javascript:sortTable(0)">Tarefa<img id="tbord0"  src="img/ic_arrow-down.svg" class="" alt=""></th>
						    <th onclick="javascript:sortTable(1)">Data Início<img id="tbord1"  src="img/ic_arrow-down.svg" class="" alt=""></th>
							<th onclick="javascript:sortTable(2)">Data Fim<img id="tbord2"  src="img/ic_arrow-down.svg" class="" alt=""></th>
						    <th onclick="javascript:sortTable(3)">Projeto<img id="tbord3"  src="img/ic_arrow-down.svg" class="" alt=""></th>
						    <th onclick="javascript:sortTable(4)">Status<img id="tbord4"  src="img/ic_arrow-down.svg" class="" alt=""></th>
						  </tr>
						  <?php for($x=0; $x<count($tarefas_em_atraso);$x++){ ?>
						  <tr>
							<td><?php echo $ff->encodeToUtf8($tarefas_em_atraso[$x]["descricao"]); ?></td>
							<td><?php echo $ff->data_mysql2php($tarefas_em_atraso[$x]["dt_inicio"],true); ?></td>
							<td><?php echo $ff->data_mysql2php($tarefas_em_atraso[$x]["dt_fim"],true); ?></td>
							<td><?php echo $ff->encodeToUtf8($tarefas_em_atraso[$x]["projeto_display"]); ?></td>
							<td class="st-<?php echo $cor_status[$tarefas_em_atraso[$x]["status_tarefa"]]?>"><?php echo $ff->encodeToUtf8($tarefas_em_atraso[$x]["status_display"]); ?></td>
						  </tr>
						  <?php } ?>
						</table>
					</div>
				</div>
			</main>
			<!-- Fim Main  -->

			<!-- Início Rodapé -->
			<?php include "incs/inc-rodape.php";?>
			<!-- Fim Rodapé -->
		</div>
		<!-- Fim DIV base -->

	</body>
</html>