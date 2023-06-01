<?php
 class conf{
    var $parametros=array();
    var $SEMENTE_SEGURANCA ="xT(vVt=96  ER PeDD:<>";
    var $SEMENTE_SEGURANCA2="AsA xxxJ~u#voG (Yt0G:}4)g!";
	
	function conf(){
		$this->__construct();
	}
	
	function __construct(){
		/* Banco de dados */
		$this->parametros["tipoBD"]="mysqli";
		$this->parametros["DB_host"]="localhost";
		$this->parametros["DB_user"]="sysproject";
        $this->parametros["DB_pass"]="<SENHA>";
        $this->parametros["DB_base"]="sysproject";
		
		// Configuraçao do log de erro
        $this->parametros["log_erros_arquivo"]="~/projects/sysproject/logs/log_erros.log";
		$this->parametros["log_erros_tabela"]="";
		$this->parametros["log_erros_processo"]="arquivo"; // arquivo/print/banco (sempre minusculo)
		/* Diversos*/
        $this->parametros["nomeSite"]="Sysproject";
		$this->parametros["urlSite"]="<DOMINIO>";
		$this->parametros["emailAdmin"]="<EMAIL>"; // Respons�vel pelo site (Cliente)
		
		/* Paginaçao */
        $this->parametros["qtde_regs_listagem"]=30;
        $this->parametros["get_pagina"]="p"; // String GET que representa a p�gina na URL
    }
	
	function getParametro($nomeParametro){
		if(isset($this->parametros[$nomeParametro]))
			return $this->parametros[$nomeParametro];
		return "";
	}
 }
 ?>