<?php 
require_once "incs/inc_checkauth.php";
$descrMensagem = "";
if(isset($_GET["err"])){
	if($_GET["err"]=="zas"){
		$descrMensagem="Não foi possível autenticar no sistema com os dados informados. Digite novamente seu usuário e senha.";
	}
}
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
			function O(campo){return document.getElementById(campo);}
			function F(campo){return eval("document.ff."+campo+";");}

			function validar(){
			  var continua=false;
			  if(O("login2").value==""){
				alert("Informe o usuário.");
				//O("login2").style.border="1px solid red";
				O("login2").focus();
				O("login2").focus();
			  }else if(O("senha2").value==""){
				alert("Informe a senha.");
				//O("senha2").style.border="1px solid red";
				  O("senha2").focus();
			  }else{
				O("usuario").value=O("login2").value;
				O("senha").value=O("senha2").value;
				O("ff").submit();
			  }
			  return continua;
			}

			function init(){
			  O("login2").focus();
			  <?php if($descrMensagem != ""){?>
				alert("<?php echo $descrMensagem; ?>")
			  <?php } ?>
			  
			}


			function limpar(){
				O("login2").value="";
				O("senha2").value="";
				O("usuario").value=O("login2").value;
				O("senha").value=O("senha2").value;
				return false;
			}
			</script>
		<!-- [if lt IE 9]>

			<script src="js/html5shiv.js"></script>
			
		<![endif] -->
	</head>
	<body id="home" onload="javascript:init()">		
	<!-- Início DIV base -->
		<div>
<form name="ff" id="ff" method="POST" enctype="urlencoded" >
	<input type="hidden" name="escopo" id="escopo" value="autenticar">
	<input type="hidden" name="acao" id="acao" value="autenticar">			
	<input type="hidden" name="usuario" id="usuario" value="">			
	<input type="hidden" name="senha" id="senha" value="">			
</form>
			
			<!-- Início Cabeçalho -->
			<?php include "incs/inc-cabecalho.php";?>
			<!-- Fim Cabeçalho -->
			
			<!-- Inicio Main  -->
			<main id="main-page">
				<div id="grid">
					<div class="div-logar">
						<form class="forms-sample" onsubmit="javascript:return validar();" style="display:contents;"> 
							<div class="label-login">Usuário</div>
							<div><input type="text" class="cp-login" name="login2" id="login2" maxlength="100"></div>
							<div class="label-login">Senha</div>
							<div><input type="password" class="cp-login"  name="senha2" id="senha2"  maxlength="100"></div>
							<div class="label-login">Recuperar senha</div>
							<div class="entrar"><button type="submit" class="bt-ok" >Entrar</button></div>
						</form>
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