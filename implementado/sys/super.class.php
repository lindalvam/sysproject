<?php
/*
 * Classe super deve ser extendida
 */
class super{
	
    var $bd; // Objeto Banco de dados
	var $funcoes; // Objeto fun��es
	var $paginacao; // Objeto pagina��o

	/* Campos no Banco de dados */
	var $campos=array();
	var $valores=array();
	var $campo_senha=array(); // Campo de senha
	var $campo_status=array(); // Campo de status
    var $campo_dataatual=array(); // Campos que recebem o valor data atual
    var $campo_datacadastro=array(); // Campos que recebem o valor da data corrente apenas no primeiro registro
	var $campo_dinheiro=array(); // Campos que recebem o valor monetario
	var $campo_data=array(); // Campos que s�o do tipo date (sem time)
	var $campo_datatime=array(); // Campos que são do tipo datetime
	var $tabela="NOMETABELA"; // Tabela
	var $campo_id="CAMPO_ID"; // Campo prim�rio
	var $erro=""; // Erro atual
	var $erros=array(); // Listagem de erros da classe
	var $resultados=array(); // Resultados do banco de dados -- Array Associativo
	var $numRegs=0; // N�mero de linhas da �ltima query
	var $arquivos=array(); // Array de arquivos relacionados
	var $localArquivos="../gmi/";//  Diret�rio para subir imagens / arquivos
    
	function super(){
		$this->funcoes=new funcoes();
        $this->bd=new bd();
        $this->paginacao=new paginacao();
		$this->bd->setTabela($this->tabela);
		$this->bd->setCampos($this->campos);
		$this->bd->setBD($this->getParametroConf("DB_base"));
	}
	
    function __construct(){
		
    }
	
	/*
	 * Executa conex�o ao banco de dados
	 */
	function conectaBD(){
		if($this->bd->conn==""){
			$this->bd->conecta();
		}
	}
    
	function getParametroConf($nome){
		$c=new conf();		
		return $c->parametros[$nome];
	}
	
	
	// Pega o valor de uma listagem de banco de dados por campo
	function getValorResultadoPorCampo($linha=0,$campo){
		if($this->resultados!=array()){
			if(isset($this->resultados[$linha])){
				if(isset($this->resultados[$linha][$campo])){
					return $this->resultados[$linha][$campo];
				}
			}
		}
		return null; // 
	}
    
   /*
	 * Capta e trata informa��es do POST
	 */
	function trataPost($valor){
		//print_r($valor);
		for($x=0;$x<count($this->campos);$x++){
		    $valorr=isset($valor[$this->campos[$x]])?$valor[$this->campos[$x]] :"";
		
			if(in_array($this->campos[$x],$this->campo_senha)&&$valorr!=""){
				$this->seta($this->campos[$x],"'".$this->funcoes->encripta($valorr)."'");
			}else if($this->campos[$x]==$this->campo_id && $valorr!=""){
				$this->seta($this->campos[$x],((int) $valorr));
			}else if(in_array($this->campos[$x],$this->campo_senha)&&$valorr==""){
				// Campo data cadastro 
			}else if(in_array($this->campos[$x],$this->campo_datacadastro)){
				$idd=isset($valor[$this->campo_id])?$valor[$this->campo_id]:"";
				if($idd==""){ // INSERT!
					$this->seta($this->campos[$x],"NOW()");
				}else{
					// Nada - DATA CADASTRO NãO PODE SER ALTERADO
				}
				
				
			// Campo do tipo data
			}else if(in_array($this->campos[$x],$this->campo_data) && isset($valor[$this->campos[$x]])){
				//echo $this->campos[$x] ."))))))))" . $valor[$this->campos[$x]];
				if($valor[$this->campos[$x]]==""){ // Campo data = '' � NULL
					$this->seta($this->campos[$x],"NULL");
				}else {
				 $this->seta($this->campos[$x],"'".$this->funcoes->data_php2mysql($valor[$this->campos[$x]],true)."'");
				}
			// Campo tipo datetime	 
			}else if(in_array($this->campos[$x],$this->campo_datatime)&&isset($valor[$this->campos[$x]])){
				if($valor[$this->campos[$x]]==""){ // Campo data = '' � NULL
					$this->seta($this->campos[$x],"NULL");
				}else {
				 $this->seta($this->campos[$x], "'".$this->funcoes->data_php2mysql($valor[$this->campos[$x]],false)."'");
				}
			}else if(in_array($this->campos[$x],$this->campo_dataatual)){ // Data atual sempre � atualizada
				$this->seta($this->campos[$x],"NOW()");
			}else if(in_array($this->campos[$x],$this->campo_dinheiro)&&isset($valor[$this->campos[$x]])){
				$v=number_format(str_replace(",",".",str_replace(".","",$valor[$this->campos[$x]])),2,".","");
				$v*=100;
				$this->seta($this->campos[$x],"'".$v."'");
			}else{
			    $valorr=isset($valor[$this->campos[$x]])?$valor[$this->campos[$x]] :"";
				if($valorr!=""){
					$this->seta($this->campos[$x],"'".$this->funcoes->clean($valorr)."'");
					
				}
			}
		}
		
		/*if($this->campo_id=="prc_id"){
		die(print_r($this->valores));
		}*/
	}
	
	
   /*
	 * Capta e trata informa��es do JSON
	 */
	function trataJson($valor){
		trataPost($valor);
	}
	
	/* 
	 * Salvando informa��es
	 */
	function salvar(){
		$this->conectaBD();
		//die(print_r($this->valores));
		$id = isset($this->valores[$this->campo_id])? $this->valores[$this->campo_id] : "";
		if($id==""){ // Novo registro
			$this->bd->insere($this->valores);
		}else{ // Altera��o de registro
			$this->bd->altera($this->valores,$this->campo_id."=".$this->valores[$this->campo_id]);
		}
	}
	
	/*
	 * Listagem de registros do Banco de dados
	 * Ver 1.1 -> Modificado para atender � necessidade de pagina��o do sistema / Mesclado com o objeto pagina��o
	 */
	function listar($ini=0,$fim=0,$ord="",$cond="",$campos=array(),$grupo=""){
		$this->conectaBD();
		if($this->paginacao->qtdeRegsPagina>0){ // Pagina��o configurada
			$this->paginacao->calculaPaginacao($this->numRegs($cond));
			$ini=(int) $this->paginacao->primeiroRegistro;
			$ini=$ini>0?$ini-1:$ini; /* Ajuste */
			$fim=$this->paginacao->qtdeRegsPagina;
			$this->bd->DB_RESULTADO=null;
			$this->bd->instanciaBD->DB_RESULTADO=null;
		}
		$this->bd->listar($ini,$fim,$ord,$cond,$campos,$grupo);
        $this->resultados=$this->bd->DB_RESULTADO;
        $this->numRegs=$this->bd->numRegs;
		$this->captaErros();
		$this->bd->DB_RESULTADO=null;
		$this->bd->instanciaBD->DB_RESULTADO=null;
    }
    
    function numRegs($cond){
    	$this->conectaBD();
		$cond=$cond==""?"":" WHERE ".$cond;
    	$query="SELECT COUNT(*) AS SOMA FROM ".$this->tabela." ".$cond;
        $this->bd->executa_query($query,true);
		return (int) $this->bd->instanciaBD->DB_RESULTADO[0]["SOMA"]; 
    }
	
	function executaQuery($query){
    	$this->conectaBD();
        $this->bd->executa_query($query,false);
    }
	
	function getResultadoQuery($query){
    	$this->conectaBD();
        $this->bd->executa_query($query,true);
		return $this->bd->instanciaBD->DB_RESULTADO; 
    }
	
	function pegaPorId($id){
   		$this->conectaBD();
		$this->bd->listar(0,1,"",$this->campo_id."=".$id);
		$this->resultados=$this->bd->DB_resultado;
		$this->numRegs=$this->bd->DB_RESULTADOS;
   }
   
   function removePorId($id){
   		$this->conectaBD();
		$this->bd->deleta($this->campo_id." IN(".$id.")");
   }
   
   function removePorCondicao($cond){
   		$this->conectaBD();
		$this->bd->deleta($cond);
   }
   
   function proximoId(){
 	  $this->conectaBD();
	  return $this->bd->ultimo_id();
   }
   
   function mudaValorCampo($nomeCampo,$ids,$valor){
		$this->conectaBD();
		$this->bd->executa_query("UPDATE ".$this->tabela." SET ".$nomeCampo."='".$valor."' WHERE ".$this->campo_id." IN(".$ids.")");
	}
	
	function mudaValorPorCampo($nomeCampoAlterar,$valorCampoAlterar,$nomeCampoComparar,$valorCampoComparar,$semAspas=false){
		$this->conectaBD();
		$v=$semAspas?"":"'";
		$this->bd->executa_query("UPDATE ".$this->tabela." SET ".$nomeCampoAlterar."=".$v.$valorCampoAlterar.$v." WHERE ".$nomeCampoComparar." IN(".$valorCampoComparar.")");
	}
	
	function removeArquivo($num,$extensao){
		for($x=0;$x<count($this->arquivos);$x++){
			@unlink($this->localArquivos."/".$this->arquivos[$x]."_".$num.".".$extensao);
		}
	}

	function sobeArquivo($num){
		for($x=0;$x<count($this->arquivos);$x++){
			if($_FILES[$this->arquivos[$x]]["size"]>0){ // Arquivo para subir
				$a=explode(".",$_FILES[$this->arquivos[$x]]["name"]);
				$ext=$a[count($a)-1];
				$local=$this->localArquivos."/".$this->arquivos[$x]."_".$num.".".$ext;
				$this->funcoes->armazenaArquivo($_FILES[$this->arquivos[$x]],$local);
				//die();
			}
		}
	}

	function pegaParaSQL($campo){
		$val = $this->pega($campo);
		if($val==""){
			return "'NOTFOUND!!'";
		}else{
			return $val;
		}
	}
	
	// Getters e Setters
	function pega($campo){
		return $this->valores[$campo];
	}
	
	function seta($campo,$valor){
		$this->valores[$campo]=$valor;
	}
	
	// Configura a pagina��o
	function setaPaginacao($numero=0){
		$numero=$numero==0?$this->getParametroConf("qtde_regs_listagem"):$numero;
		$this->paginacao->setaPaginacao($numero);
	}
	
	// Configura o n�mero da p�gina atual -> De 0 a n
	function setaPagina($numero){
		$this->paginacao->setaPagina($numero);
	}
	
	// M�todo que deve ser extendido para lidar com os erros
	function captaErros($log=false){
		//$this->bd->instanciaBD->logerro->salvar();
		//$this->bd->instanciaBD->logerro->mostrar();
		$this->erros=$this->bd->instanciaBD->logerro->erro;
		if($log){
			$this->gravaLogErro();
		}
	}
	
	
	// M�todo padr�o para log de erros.
	function log(){
		$this->bd->log();
	}
	
	
	// Generaliza��o do WS para as acoes mais comuns
	function ws_action($metodo,$args){
		switch($metodo){
			default:
/*--------------------------------------------------------------
						A��es de Navega��o						
--------------------------------------------------------------*/
			/* Listagem de usu�rios */
			case "listar":
				//print_r($this->campos);
				$this->listar(false,false,$this->campo_id);
				$this->returnJSON();
			break;
			/* Primeiro registro */
			case "primeiro":
				$this->listar(0,1,$this->campo_id);
				$this->returnJSON();
			break;
			/* Busca registro */
			case "pesquisaRegistroPorId":
				$id=((int) $args["id"])>0?((int) $args["id"]):0;
				$this->listar(0,1,$this->campo_id,$this->campo_id."=".$id);
				$this->returnJSON();
			break;
			/* �ltimo registro */
			case "ultimo":
				$this->listar(0,1,$this->campo_id." DESC");
				$this->returnJSON();
			break;
			/* Pr�ximo registro */
			case "proximo":
				$id=((int) $args["id"])>0?((int) $args["id"]):0;
				$this->listar(0,1,$this->campo_id,$this->campo_id." >".$id );
				$this->returnJSON();
			break;
			/* Registro anterior */
			case "anterior":
				$id=((int) $args["id"])>0?((int) $args["id"]):0;
				if($id>0){
					$this->listar(0,1,$this->campo_id." DESC",$this->campo_id." <".((int) $args["id"]) );
				}else{
					$this->ws_action("ultimo",null);
				}
			break;
/*--------------------------------------------------------------
						CRUD									
--------------------------------------------------------------*/
			case "salvar":
				$this->trataPost($args);
				$this->salvar();
				$this->captaErros();
				//print_r($this->bd->getQueriesExecutadas());
				if($this->erros!=array()){ // Houve erros na grava��o de dados
					$d="Houve um erro na tentativa de inserir o registro. Consulte o log do sistema e contate o administrador.";
					$arr=array("Erro"=>$d);
				}else{
					$id=isset($args["id"])?$args["id"]:"";
					$acao=$id==""?"inclu�do":"alterado";
					$d="Registro $acao com sucesso.";
					$arr = array("Mensagem"=>$d);
				}
				$this->returnJSON($arr);
			break;
			case "excluir":
				$this->removePorId((int) $args[$this->campo_id]);
				$this->captaErros();
				$d="Registro exclu�do com sucesso.";
				if($this->erros!=array()){ // Houve erros na grava��o de dados
					$d="Houve um erro na tentativa de excluir o registro. Consulte o log do sistema e contate o administrador.";
					$arr = array("Mensagem"=>$d);
				}else{
					$d="Registro excluido com sucesso.";
					$arr = array("Mensagem"=>$d);
				}
				
				$this->returnJSON($arr);
			break;
			case "muda_status":
				$this->mudaValorPorCampo($this->campo_status,addslashes($args["status"]),$this->campo_id,addslashes($args["regs"]),false);
				$this->captaErros();
				$acao=$this->traduzStatus($args["status"],true);
				$d="Registro $acao com sucesso.";
				$arr = array("Mensagem"=>$d);
				if($this->erros!=array()){ // Houve erros na grava��o de dados
					$d="Houve um erro na tentativa de modificar o registro. Consulte o log do sistema e contate o administrador.";
					$arr = array("Mensagem"=>$d);
				}
				$this->returnJSON($arr);
			break;
		}
	}
	
	// Converte resultado (listagem de banco de dados) em JSON
	function resultadosParaJSON(){
		//print_r($this->resultados);
		//print_r($this->erros);
		$resultados = array();
		foreach($this->resultados as $key=>$var){
			$resultados[]=array_map('htmlentities',$var);
		}
		if($resultados!=array()){
			$resultados=array("result"=>$resultados,"qtde"=>count($resultados));
		}else{
			$resultados=array("result"=>"","qtde"=>0);
		}
		//$resultados = array_map('htmlentities',$this->resultados);
		//echo json_encode($resultados);
		//echo json_last_error();
		if ($this->getErrorJSON()!=""){
			$this->erros[] = $this->getErrorJSON();
			return false;
		}
		return json_encode($resultados,JSON_FORCE_OBJECT);
	}
	
	function returnJSON($obj=null){
		header('Content-Type: application/json');
		//print_r($obj);
		// print_r($this->bd->getQueriesExecutadas());
		if($obj==null){
			die($this->resultadosParaJSON());
		}else{
			$retorno = json_encode($obj,JSON_FORCE_OBJECT);
			if($this->getErrorJSON()!=""){
				$obj=array_map('utf8_encode',$obj);
			}
			$retorno = json_encode($obj,JSON_FORCE_OBJECT);
			//print_r($obj2);
			if ($this->getErrorJSON()!=""){
				$this->erros[] = $this->getErrorJSON();
				print_r($this->erros);
				die();
			}
			die($retorno);
		}
	}
	
	function traduzStatus($status,$gerundio=false){
	    if(!$gerundio){
		    $st=array("a"=>"Ativo",
					  "x"=>"Exclu�do",
			          "b"=>"Bloqueado");
		}else{
			$st=array("a"=>"Ativado",
					  "x"=>"Exclu�do",
			          "b"=>"Bloqueado");
		}
		return $st[$status];
	}
	
	
	function procedimentoErro($mensagem,$tipo,$redirect){
		$this->captaErros();
		if($this->erros!=array()){ // Houve erros na listagem
			logerro::alertaErro($mensagem,$tipo,$redirect);
		}
	}

	/**
	 * Grava o log dos erros ocorridos de acordo com a configura��o na classe conf
	 */
	function gravaLogErro(){
		$logerro = new logerro();
		$logerro->configLogErro($this->getParametroConf("log_erros_processo"),
							$this->getParametroConf("log_erros_arquivo"),
							$this->getParametroConf("log_erros_tabela")
							);
		//die(print_r($this->erros));
		for($x=0; $x<count($this->erros["descricao"]) ; $x++){
			$logerro->logTexto($this->erros["descricao"][$x], $this->erros["linha"][$x], $this->erros["arq"][$x]);
		}
		//die(print_r($logerro));
		$logerro->log();
	}
	
	
	function getErrorJSON(){
	
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					return "";// ' - No errors';
				break;
				case JSON_ERROR_DEPTH:
					return  'Maximum stack depth exceeded';
				break;
				case JSON_ERROR_STATE_MISMATCH:
					return 'Underflow or the modes mismatch';
				break;
				case JSON_ERROR_CTRL_CHAR:
					return 'Unexpected control character found';
				break;
				case JSON_ERROR_SYNTAX:
					return 'Syntax error, malformed JSON';
				break;
				case JSON_ERROR_UTF8:
					return 'Malformed UTF-8 characters, possibly incorrectly encoded';
				break;
				default:
					return 'Unknown error';
				break;
			}
		
		return "";
	}


	
	
}
?>