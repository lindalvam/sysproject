<?php
/**
  * Classe especial para lidar com serviços REST (via JSON)
  * As APIS devem ser implementadas nas instâncias requisitadas.
  * Classe conf deve ser configurada para as requisições
 */  
class webservice {
	var $conf;
	var $logerro; // 
	var $objRequest; // Objeto REST enviado no Request

	// Construtor
	function __construct($logErro=""){
		$this->conf = new conf();
		$this->logerro = new logerro();
		if($logErro==""){
			$this->logerro->configLogErro($this->valorConf("log_erros_processo"),
									  $this->valorConf("log_erros_arquivo"),
									  $this->valorConf("log_erros_tabela"));
		}else{
			$this->logerro = $logErro;
		}
	}
	
	function webservice($logErro=""){
		__construct($logErro);
	}
	
	// Inicia a verificação do Request
	function start($sendHTTP=false){
	    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			//$this->logerro->logTexto("Servico nao iniciado. Post nao enviado.",__LINE__,__FILE__);
			return false;
		}
	
		if($this->valorConf("WS_auth")=="basic"){
			if(!$this->auth_basic()){
				$this->logerro->logTexto("Servico nao iniciado. Autenticacao nao confirmada.",__LINE__,__FILE__);
				if($sendHTTP){
					header("HTTP/1.0 401 Unauthorized");
					if($this->getValorRequest("wsm_prc_alteracao")=="APP_REST_TESTE"){ // Teste ou debug
						$this->logerro->mostrar();
					}
					die("Servico nao iniciado. Autenticacao nao confirmada.");
				}
				return false;
			}
		}
		if($this->valorConf("WS_protocolo")=="" ){
			$this->logerro->logTexto("Servico nao iniciado. Protocolo nao configurado.",__LINE__,__FILE__);
			return false;
		}
		if($this->valorConf("WS_instancia_var")=="" ){
			$this->logerro->logTexto("Servico nao iniciado. Instancia nao configurada.",__LINE__,__FILE__);
			return false;
		}
		if($this->valorConf("WS_metodo_var")=="" ){
			$this->logerro->logTexto("Servico nao iniciado. Metodo de instancia nao configurado.",__LINE__,__FILE__);
			return false;
		}
		if($this->valorConf("WS_prefixo_args_var")=="" ){
			$this->logerro->logTexto("Servico nao iniciado. Argumentos de instancia nao configurado.",__LINE__,__FILE__);
			return false;
		}
		// Validando protocolo
		if(!$this->protocoloCorreto()){
			$this->logerro->logTexto("Servico nao iniciado. Protocolo nao reconhecido.",__LINE__,__FILE__);
			return false;
		}
		
		// Validando instancia
		$class = $this->getValorRequest($this->valorConf("WS_instancia_var"));
		if( $class != ""){
			
			if(!class_exists($class)){
				$this->logerro->logTexto("Servico nao iniciado. Nenhuma classe instanciada para o webservice.",__LINE__,__FILE__);
				return false;
			}
		}else{
			$this->logerro->logTexto("Servico nao iniciado. Nenhuma instancia foi informada pelo request.",__LINE__,__FILE__);
			return false;
		}
		
		// Validando metodo
		$metodo = $this->getValorRequest($this->valorConf("WS_metodo_var"));
		if( $metodo == ""){
			$this->logerro->logTexto("Servico nao iniciado. Nenhum metodo foi informado pelo request.",__LINE__,__FILE__);
			return false;
		}
		
		// Tratando os argumentos
		$args = $this->trataArgsRequest($this->valorConf("WS_prefixo_args_var"));
		$inst= new $class;
		$inst->ws_action($metodo,$args);
		
		
	}
	
	// Verifica se as requisições de autenticação básica foram cumpridas.
	function auth_basic(){
		if($this->valorConf("WS_auth_basic_user_var")!="" && 
		   $this->valorConf("WS_auth_basic_pass_var")!="" && 
		   $this->valorConf("WS_auth_basic_user_val")!="" && 
		   $this->valorConf("WS_auth_basic_pass_val")!=""){
		    // echo $this->valorConf("WS_auth_basic_user_var");
			 // echo $this->getValorHeader($this->valorConf("WS_auth_basic_user_var"));
			 //echo "\n----<br>\n";
			 //echo $this->valorConf("WS_auth_basic_user_val");			 
			 if($this->getValorHeader($this->valorConf("WS_auth_basic_user_var"))==$this->valorConf("WS_auth_basic_user_val")){
				if($this->getValorHeader($this->valorConf("WS_auth_basic_pass_var"))==$this->valorConf("WS_auth_basic_pass_val")){
					return true;
				}else{
					$this->logerro->logTexto("Autenticacao nao permitida. Erro 0U.",__LINE__,__FILE__);
					return false;
				}
			 }else{
				$this->logerro->logTexto("Autenticacao nao permitida. Erro 0K.",__LINE__,__FILE__);
				return false;
			}
		}else{
			$this->logerro->logTexto("Autenticacao nao configurada. ",__LINE__,__FILE__);
		}
		return false;
	}
	
	// Apenas JSON configurado
	function protocoloCorreto(){
		if($this->valorConf("WS_protocolo")=="JSON"){
			if($this->getValorHeader("Content-Type")=="application/json"){
				return true;
			}
		}
		
		return false;
	}
	
	function valorConf($var){
		if(null!==$this->conf->getParametro($var)){
			return $this->conf->getParametro($var);
		}
		return "";
	}
	
	function getValorHeader($var){
		$header = getallheaders();
		//print_r($header);
		if(isset($header[$var])){
			return $header[$var];
		}else if(isset($header[strtolower($var)])){
			return $header[strtolower($var)];
		}
		return "";
	}
	
	
	function trataArgsRequest($pre){
		$arr = (array) $this->objRequest;
		//print_r($arr);
		$retorno = array();
		foreach($arr as $key=>$value){
			if(strpos($key,$pre)===0){
				$chave = str_replace($pre,"",$key);
				$retorno[$chave]=utf8_decode($value);
			}
		}
		//print_r($retorno);
		return $retorno;
	}
	
	function getValorRequest($valor){
		if($this->objRequest!=null){
			return $this->objRequest->{$valor};
		}
		if($this->valorConf("WS_protocolo")=="JSON"){
			return $this->getValorRequestJson($valor);
		}
		$this->logerro->logTexto("Nao ha funcao definida para captura de valores no protocolo requisitado. ",__LINE__,__FILE__);
		return "";
	}
	
	
	function getValorRequestJson($valor){
		$json =  file_get_contents('php://input');
		//var_dump($json);
		//var_dump($valor);
		$json = utf8_encode($json); 
		$obj = json_decode($json);
		if (json_last_error() === JSON_ERROR_NONE) { 
			$this->objRequest = $obj;
			return $this->objRequest->{$valor};
		}else{
			$this->logerro->logTexto("Erro de Parse no JSON do Request: ".json_last_error(),__LINE__,__FILE__);
			return "";
		}
    }
	
}
?>