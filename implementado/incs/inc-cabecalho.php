<?php
if($_SESSION["USUARIO"]!=""){
	$ac = new acesso();
	$D=$ac->pegaInstancia("USUARIO");
	$DADOS_USUARIO=$D->resultados[0];
	$is_gerente = $DADOS_USUARIO["perfil_usuario"]=="1";
	$is_analista = $DADOS_USUARIO["perfil_usuario"]=="2";
	$is_admin = $DADOS_USUARIO["perfil_usuario"]=="3";
}
?>
			<script type="text/javascript">
				function logout(){
					document.getElementById("ss").submit();
				}
			</script>
			<form name="ss" id="ss" method="POST" enctype="urlencoded" >
				<input type="hidden" name="escopo"  value="aut">
				<input type="hidden" name="acao"  value="sairDoSistema">			
			</form>

				<header class="basetop">
					<div class="topo">
						<img src="img/logo.svg" class="logomarca" alt="">
						<?php if($_SESSION["USUARIO"]!=""){?>
						<div class="userlog">Usuário atual: <?php echo $DADOS_USUARIO["nome"]?> <!-- (<?php echo $DADOS_USUARIO["perfil_display"]?>) --> </div>
						<div class="sair"><button class="bt-sair" onclick="javascript:logout();">Sair</button></div>
						<?php } ?>
					</div>
				</header>

				<nav class="navegacao esconde_480px" aria-haspopup="true">
					<h2 class="menu-icon" onclick="void(0)"><span class="esconde_480px">&equiv;</span></h2>
					<ul>
						<?php if($_SESSION["USUARIO"]!=""){?>
						<li class=""><a href="./">Dashboard</a></li>
							<?php if($is_admin){?>
						<li class=""><a href="#">Cadastro de Usuário</a></li>
						<li class=""><a href="#">Cadastro de Projeto</a></li>
							<?php } ?>
						<li class=""><a href="#">Meu Cadastro</a></li>
						<?php }?>
						<!-- <li class=""><a href="ajuda.php">Ajuda</a></li> -->
					</ul>
				</nav>

				<nav class="navdesktop esconde_mobile">
					<ul>
						<?php if($_SESSION["USUARIO"]!=""){?>
						<li class=""><a href="./" >Dashboard</a></li>
							<?php if($is_admin){?>
						<li class=""><a href="#">Cadastro de Usuário</a></li>
						<li class=""><a href="#">Cadastro de Projeto</a></li>
							<?php }?>
						<li class=""><a href="#">Meu Cadastro</a></li>
						<?php }?>
						<!-- <li class=""><a href="ajuda.php">Ajuda</a></li> -->
					</ul>
				</nav>
				