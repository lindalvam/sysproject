<?php 
/* ------------------------------------------------------------------------------------------
								Validação de autenticação. 
	Este arquivo deve ser incluído em todas as páginas para garantir o controle de autenticação 
---------------------------------------------------------------------------------------------  */
ini_set("session.cache_expire",10000);
session_start();
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);
require_once "./sys/pacote.php";
$semAutenticar=array("login.php","fff.php");
$c=new conf();
$funcao=new funcoes();
$titulo_sistema="Administrador - ".$c->getParametro("nomeSite");
$localRdir="";
if($_POST["r"]!=""){ // Há parametros que devem ser mantidos na URL
	$GETS=explode("&",$_POST["r"]);
	for($x=0;$x<count($GETS);$x++){
		$dados=explode("=",$GETS[$x]);
		if($dados[0]=="pg"){
			$localRdir=$dados[1];
		}
	}
}
/* 
 * EXECUTA A AUTENTICA��O
 */
if($_POST["acao"]=="sairDoSistema"){ // Encerrar a sess�o
	$ace=new acesso();
	$ace->apagaInstancia("USUARIO");
	die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
          <!--
            location.href="./login.php";
           //-->
         </SCRIPT>');
}else if($_POST["usuario"]!="" && $_POST["senha"]!="" && $_POST["escopo"] == "autenticar"){
	//die(print_r($_POST));
	 $usu=new usuario();
	 $dados=array("usuario"=>$_POST["usuario"],"senha"=>$_POST["senha"]);
	 if($usu->autenticar($dados)){ // GRAVA OS DADOS NA SESS�O
	 	$_SESSION["USUARIO"]=serialize($usu);
		
		// Redireciona para a página principal
		die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
             <!--
             location.href="'.($localRdir==""?"index.php":$localRdir."?".$_POST["r"]).'";
             //-->
             </SCRIPT>');
	 }else{
		//die(print_r($usu));
		die(utf8_encode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
             <!--
			//  alert("Não foi possível autenticar no sistema com os dados informados. Digite novamente seu login e senha.");
             location.href="login.php?err=zas";
             //-->
             </SCRIPT>'));
	 }
}else{ // VERIFICA SE A SESSÃO EXPIROU
	$arqAtual = $funcao->retornaArquivoAtual();
	//die( $arqAtual);

	if(!in_array($arqAtual,$semAutenticar)){ // P�ginas sem Autentica��o
		//print_r($_SESSION);
		if($_SESSION["USUARIO"]==""){
			die(utf8_encode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
		         <!--
		         //alert("Sua sessão expirou.");
				 location.href="login.php";
		         //-->
		         </SCRIPT>'));
		}else{
			$ac = new acesso();
			$D=$ac->pegaInstancia("USUARIO");
			$DADOS_USUARIO=$D->resultados[0];
		}
	}
}
include_once "./incs/inc-controller.php";
?>