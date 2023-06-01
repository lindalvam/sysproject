<?php 
class acesso extends super{

	var $tabela='acesso'; //TODO:  Tabela n�o implementada
	var $campos=array("ace_id","ace_tipoacesso","ace_pagina");
	var $campo_id='ace_id';
	var $campo_dataatual=array();
	var $campo_datacadastro=array();
	var $campo_senha=array();
	var $campo_dinheiro=array();
	var $mensagemAcesso=""; // Mensagem de acesso
	var $niveisAcesso=array(); // Niveis de acesso checados
	var $paginaPop=false; // Se a p�gina que est� sendo validado o acesso � um pop up

	function acesso(){
		parent::super();
	}
	
	/*
	 * Pega a inst�ncia de um elemento serializado na sess�o
	 */
	function pegaInstancia($el){
		return unserialize($_SESSION[$el]);
	}
	
	/*
	 * Apaga o objeto serializado da sess�o
	 */
	function apagaInstancia($el){
		$_SESSION[$el]=null;
	}
	
	/*
	 * Verifica se o arquivo � um pop up 
	 * (colocado nesta classe, por ser uma quest�o de l�gica de neg�cios)
	 */
	function verificaPop($arq){
		$this->paginaPop= substr(strtolower($arq),0,4)=="pop_";
	}
	
	/*
	 * Checa se o acesso � p�gina � permitido pelo n�vel do usu�rio
	 */
	function checaNivelAcesso($el){
		$usu=$this->pegaInstancia($el);
		$retorno = true;		
		return $retorno;
	}
}
?>