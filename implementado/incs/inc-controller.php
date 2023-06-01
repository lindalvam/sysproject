<?php 
/* 
  *ARQUIVO ESPECIALIZADO PARA TRATAR DOS REDIRECIONAMENTOS DE CONTROLLERS, CONFORME ESCOPO 
  * TRATAR NESTE ARQUIVOS CASOS QUE são EXCEÇÕES, MAS O ESCOPO DEVE SEMPRE SER LEVADO AO SEU 
  * CONTROLLER RESPECTIVO. 
  */
  $var_escopo = "escopo"; // Nome da variável de escopo, passada via POST
  $var_acao = "acao"; // Nome da variável de ação, passada via POST
  $var_obj = null; // Objeto a manipular a classe de  escopo
  $escopo=$_POST[$var_escopo]!=""?$_POST[$var_escopo]:$_GET[$var_escopo];
  $acao=$_POST[$var_escopo]!=""?$_POST[$var_acao]:$_GET[$var_acao];
  $args=$_POST[$var_escopo]!=""?$_POST:$_GET;
  $executeController = false;
  if($escopo!=""){
    if($acao!=""){ // Somente se houver uma ação a ser passada para o escopo
	    switch($escopo){
		   case "usuarios":case "usuario":
		      $var_obj = new usuario();
			  $executeController=true;
		   break;
		   case "projetos": case "projeto":
		      $var_obj = new projeto();
			  $executeController=true;
		   break;
		   case "tarefas": case "tarefa":
		      $var_obj = new tarefa();
			  $executeController=true;
		   break;
		   case "projeto_risco": case "risco_projeto":
		      $var_obj = new projeto_risco();
			  $executeController=true;
		   break;
		   default:
		      logerro::alertaErro("Escopo desconhecido","alert",$arqAtual);
			  $executeController=false;
		   break;
		}
		/* CONTROLLER - Define o que a acao ira executar*/
		if($executeController){
			$var_obj->controller($acao,$args);
			/*echo "$acao\n";
			print_r($args);
			die(print_r($var_obj));*/
		}
	}
  }
 
?>