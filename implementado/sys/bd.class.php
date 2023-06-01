<?php 
/*
  * Classe de abstração de BD
  */
class bd{
    var $instanciaBD;
    var $DB_RESULTADO;
	var $conf; // Instancia de conf
	var $numRegs=0; 
	var $conn; // ID de conexão com o BD
	//var $DB_query = array(); // Array de queries executadas
    
	function bd(){
        $bd=$this->getParametroConf("tipoBD")."bd";
        $this->instanciaBD=new $bd;
		$this->instanciaBD->configLogErro($this->getParametroConf("log_erros_processo"),
										  $this->getParametroConf("log_erros_arquivo"),
										  $this->getParametroConf("log_erros_tabela"));
    }
	
	function getParametroConf($nome){
		$c=new conf();
		return $c->parametros[$nome];
	}
    
    function conecta(){
		$this->setBD($this->getParametroConf("DB_base"));
		$this->instanciaBD->conecta($this->getParametroConf("DB_host"),$this->getParametroConf("DB_user"),$this->getParametroConf("DB_pass"));
		$this->conn=$this->instanciaBD->conn;
    }
    
    function setBD($banco){
      $this->instanciaBD->DB_BASE=$banco;
    }
	
	function setTabela($tabela){
      $this->instanciaBD->DB_TABELA=$tabela;
    }
	
	function setCampos($campos){
      $this->instanciaBD->DB_CAMPOS=$campos;
    }
    
    function desconecta(){
       $this->instanciaBD->desconecta();
    }
    
    function ultimo_id(){
		return $this->instanciaBD->ultimo_id();
    }
    
	// Return -1 if error or number of rows affected
    function altera($valor,$clause){
       return $this->instanciaBD->altera($valor,$clause);
    }
    
	// Return -1 if error or number of rows affected
    function insere($valor){
        return $this->instanciaBD->insere($valor);
    }
    
	// Return -1 if error or number of rows affected
    function deleta($cond){
        return $this->instanciaBD->deleta($cond);
    }
    
    function executa_query($query,$retorno=false){
		$dados = $this->instanciaBD->executa_query($query,$retorno);
		$this->numRegs=$this->instanciaBD->numRegs;
		return $dados;
    }
	
	function listarCampos($tabela){
		$this->instanciaBD->listarCampos($tabela);
		$this->DB_RESULTADO=$this->instanciaBD->DB_RESULTADO;
        $this->numRegs=$this->instanciaBD->numRegs;
		return $this->DB_RESULTADO;
	}
	
	function listarTabelas(){
		$this->instanciaBD->listarTabelas($this->getParametroConf("DB_base"));
		$this->DB_RESULTADO=$this->instanciaBD->DB_RESULTADO;
        $this->numRegs=$this->instanciaBD->numRegs;
		return $this->DB_RESULTADO;
	}
    
    function listar($ini=0,$fim=0,$ord="",$cond="",$campos=array(),$grupo=""){
        $this->instanciaBD->listar($ini,$fim,$ord,$cond,$campos,$grupo);
        $this->DB_RESULTADO=$this->instanciaBD->DB_RESULTADO;
        $this->numRegs=$this->instanciaBD->numRegs;
    }
	
	function getForeignKeys($tabela){
		return $this->instanciaBD->getForeignKeys($tabela);
	}
	
	function captaErros(){
		return $this->instanciaBD->logerro->erro;
	}
	
	// Alias para captaErros
	function getErros(){
		return $this->captaErros();
	}
	
	// Tratamento de log configurado (vide classe conf)
	function log(){
		$this->instanciaBD->logerro->log();
	}
	
	function getQueriesExecutadas(){
		return $this->instanciaBD->DB_query;
	}
	
	function getNumRegs(){
		$this->numRegs=$this->instanciaBD->numRegs;
		return $this->numRegs;
	}
	
	
	
	function listarErros($quebraLinha){
		$arr = $this->instanciaBD->logerro;
		$retorno = "";
		$f = new funcoes();
		$quebraLinha = $quebraLinha==""?"\r\n":$quebraLinha;
		for($x=0;$x<$arr->getQuantidadeLinhas();$x++){
			$retorno .= $arr->erro["descricao"][$x] . " ";
            $retorno .= $f->somenteArquivoSemPastaLogErro($arr->erro["arq"][$x],""). " ";
			$retorno .= "(".$arr->erro["linha"][$x]. ") ";
            $retorno .= $arr->erro["data"][$x]. $quebraLinha;
		}
		return $retorno;
		
		//print_r($arr);
	}
    
}
?>