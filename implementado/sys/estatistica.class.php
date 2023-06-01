<?php 
class estatistica extends super{

	var $tabela='estatistica';
	var $campos=array("id","nome","email","display_name","senha",
	                  "dt_nascimento","status","sexo","dt_criacao", 
					  "dt_alteracao", "prc_alteracao");
	var $campo_id='id';
	var $campo_dataatual=array("dt_alteracao");
	var $campo_datacadastro=array("dt_criacao");
	var $campo_data=array("dt_nascimento");
	var $campo_senha=array("senha");
	var $campo_status=array("status");
	var $campo_dinheiro=array();

	
}
?>