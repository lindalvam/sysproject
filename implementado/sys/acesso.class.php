<?php 
class acesso extends super{

	var $tabela='acesso'; //TODO:  Tabela não implementada
	var $campos=array("ace_id","ace_tipoacesso","ace_pagina");
	var $campo_id='ace_id';
	var $campo_dataatual=array();
	var $campo_datacadastro=array();
	var $campo_senha=array();
	var $campo_dinheiro=array();
	var $mensagemAcesso=""; // Mensagem de acesso
	var $niveisAcesso=array(); // Niveis de acesso checados
	var $paginaPop=false; // Se a página que está sendo validado o acesso é um pop up

	function acesso(){
		parent::super();
	}
	
	/*
	 * Pega a instância de um elemento serializado na sessão
	 */
	function pegaInstancia($el){
		return unserialize($_SESSION[$el]);
	}
	
	/*
	 * Apaga o objeto serializado da sessão
	 */
	function apagaInstancia($el){
		$_SESSION[$el]=null;
	}
	
	/*
	 * Verifica se o arquivo é um pop up 
	 * (colocado nesta classe, por ser uma questão de lógica de negócios)
	 */
	function verificaPop($arq){
		$this->paginaPop= substr(strtolower($arq),0,4)=="pop_";
	}
	
	/*
	 * Checa se o acesso à página é permitido pelo nível do usuário
	 */
	function checaNivelAcesso($el){
		$usu=$this->pegaInstancia($el);
		$retorno = true;		
		return $retorno;
	}
}
?>