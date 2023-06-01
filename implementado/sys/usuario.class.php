<?php 
class usuario extends super{

	var $tabela='usuario';
	var $campos=array("id","nome","email","senha","perfil_usuario","usuario",
	                  "status_usuario","created_at", "updated_at");
	var $campo_id='id';
	var $campo_dataatual=array("updated_at");
	var $campo_datacadastro=array("created_at");
	var $campo_datatime=array();
	var $campo_senha=array("senha");
	var $campo_status=array("status_usuario");
	var $campo_dinheiro=array();

	
	function usuario(){
		$this->__construct();
	}
	
	function __construct(){
		parent::super();
	}
	
	 function comPerfilStatus(){
        $this->campos[]="(SELECT s.status from status_usuario s where s.id=usuario.status_usuario) AS status_display";
		$this->campos[]="(SELECT p.perfil from perfil_usuario p where p.id=usuario.perfil_usuario) AS perfil_display";
        $this->bd->setCampos($this->campos);
	}
	
	
	// Especializacao das acoes de WS para usuario
	function ws_action($metodo,$args){
		//echo $metodo;
		//print_r($args);
		switch($metodo){
				

/*--------------------------------------------------------------
						A��es especializadas									
--------------------------------------------------------------*/
			case "salvar":
				//echo "---------";
				if(!isset($args["email"]) ){
					$d = "EMAIL nao informado";
					$arr = array("Erro"=>$d);
					$this->returnJSON($arr);
					die();
				}
				$args["id"]=!isset($args["id"])?"":$args["id"];
			    if($this->existeLogin($args["email"],$args["id"])){
				    $d = "J� existe cadastro com o email ".$args["email"];
					$arr = array("Erro"=>$d);
					$this->returnJSON($arr);
					die();
				}
				$this->trataPost($args);
				$this->salvar();
				$this->captaErros();
				if($this->erros!=array()){ // Houve erros na grava��o de dados
					$d="Houve um erro na tentativa de inserir o registro. Consulte o log do sistema e contate o administrador.";
					$arr = array("Erro"=>$d,"more"=>$this->erros);
				}else{
					$acao=$args["id"]==""?"incluido":"alterado";
					$d="Registro $acao com sucesso.";
					$arr = array("Mensagem"=>$d);
				}
				$this->returnJSON($arr);
				die();
			break;
			
			case "excluir":
				/* AO INVES DE EXCLUIR O USUARIO, COLOCAMOS UM STATUS INVISIVEL, PARA MANTER HIST�RICO */
				$this->mudaValorPorCampo($this->campo_status,"x",$this->campo_id,$args["regs"],false);
				$this->captaErros();
				$d="Registro exclu�do com sucesso.";
				if($this->erros!=array()){ // Houve erros na grava��o de dados
					$d="Houve um erro na tentativa de excluir o registro. Consulte o log do sistema e contate o administrador.";
					$arr = array("Erro"=>$d);
				}else{
					$d="Registro excluido com sucesso.";
					$arr = array("Mensagem"=>$d);
				}
				$this->returnJSON($arr);
			break;
			
			case "salvarSenha":
				$this->mudaValorPorCampo($this->campo_senha,$this->funcoes->encripta($args["novasenha"]),$this->campo_id,$args[$this->campo_id],false);
				$this->captaErros();
				if($this->erros!=array()){ // Houve erros na grava��o de dados
					$d="Houve um erro na tentativa de modificar o registro. Consulte o log do sistema e contate o administrador.";
					$arr = array("Erro"=>$d);
				}
				$arr = array("Mensagem"=>"Senha alterada com sucesso!");
				$this->returnJSON($arr);
			break;
			
			case "autenticar":
				if(autenticar($args)){
					$arr = array("Erro"=>"Login ou senha incorretos");
					$this->returnJSON($arr);
				}
				$arr = array("Mensagem"=>"OK");
				$this->returnJSON($arr);
			break;
		}
		
		parent::ws_action($metodo,$args);
			
	}
	
	
	/* 
	 * Autenticacao / Retorna TRUE ou False
	 */
	
	function autenticar($dados){
		$this->trataPost($dados);
		$this->comPerfilStatus();
		
		$this->listar(0,1,false,"usuario=".$this->pegaParaSQL("usuario")." 
								 AND status_usuario=(SELECT ss.id from status_usuario ss WHERE ss.status='Ativo')
								 AND senha=".$this->pega("senha"));
								 
		$continua=false;
		if($this->numRegs>0){ // Registro encontrado!
			$continua=true;
		}
		return $continua;
	}
	
	
	
	function existeLogin($login,$id=0){
		$vrf=new usuario();
		$vrf->listar(0,1,false,"login='".addslashes($login)."' AND status!='x'");
		if($vrf->numRegs>0){
			if($vrf->resultados[0]["id"]!=$id){
				return true;
			}
		}
		return false;
	}
	
	function existeSenha($id,$senha){
		$vrf=new usuario();
		$vrf->listar(0,1,false,"id='$id' AND senha='".$this->funcoes->encripta($senha)."'");
		if($vrf->numRegs>0){
			return true;
		}
		return false;
	}

	function controller($metodo,$args){
		switch($metodo){
			case "ajaxVerificaLogin":
				$id=intval($args["id"])==$args["id"]?$args["id"]:"0";
				if($this->existeLogin($args["login"],$args["id"]) ){
						die("1");
				}
				die("0");
			break;

			case "ajaxVerificaSenha":
				//die(print_r($args));
				$id=intval($args["id"])==$args["id"]?$args["id"]:"0";
				if($this->existeSenha($id,$args["senha"]) ){
						die("1");
				}
				die("0");
			break;

			case  "salvarSenha":
			 	//die(print_r($args));
				$this->mudaValorPorCampo($this->campo_senha[0],$this->funcoes->encripta($args["novasenha"]),$this->campo_id,$args[$this->campo_id],false);
				$this->captaErros();
				$ac="a";
				$d="Senha alterada com sucesso!";
				if($this->erros!=array()){ // Houve erros na grava��o de dados
					$d="Houve um erro na tentativa de modificar o registro. Consulte o log do sistema e contate o administrador.";
					$this->gravaLogErro();
					$ac="e";
				}
				die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						<!--
						location.href="nova_senha.php?e='.$ac.'&d='.$d.'";
						//-->
						</SCRIPT>');
			break;

				case "captaRegistro":
					
					$id=((int) $args["id"])>0?((int) $args["id"]):0;
					$this->listar(0,1,$this->campo_id,$this->campo_id."=".$id);
					
					
				break;

				case "excluir":
					/* AO INVES DE EXCLUIR O USUARIO, COLOCAMOS UM STATUS INVISIVEL, PARA MANTER HIST�RICO */
					$this->mudaValorPorCampo($this->campo_status[0],"x",$this->campo_id,$args["regs"],false);
					$this->captaErros();
					$d="Registro exclu�do com sucesso.";
					$ac="a";
					if($this->erros!=array()){ // Houve erros na grava��o de dados
						$d="Houve um erro na tentativa de excluir o registro. Consulte o log do sistema e contate o administrador.";
						$ac="e";
						$this->gravaLogErro();
					}
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						<!--
						location.href="lista_usuario.php?e='.$ac.'&d='.$d.'";
						//-->
						</SCRIPT>');
				
				break;
				
				case "muda_status":
					//die(print_r($args));
					$this->mudaValorCampo("status",$args["regs"],$args["status"]);
					$d="Registro alterado com sucesso";
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						<!--
						location.href="lista_usuario.php?e=a&d='.$d.'";
						//-->
						</SCRIPT>');
					//$this->listar(0,1,$this->campo_id,$this->campo_id."=".$id);
				break;

				case "salvar":
					$this->trataPost($args);
					$this->salvar();
					$this->captaErros();
					//die(print_r($this->erros));
					if($this->erros!=array()){ // Houve erros na grava��o de dados
						$d="Houve um erro na tentativa de inserir o registro. Consulte o log do sistema e contate o administrador.";
						$arr = array("Erro"=>$d,"more"=>$this->erros);
						$this->gravaLogErro();
						die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						<!--
						location.href="lista_usuario.php?e=e&d='.$d.'";
						//-->
						</SCRIPT>');
					}else{
						$acao=$args["id"]==""?"incluído":"alterado";
						$d="Usuário $acao com sucesso!";
						die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						<!--
						location.href="lista_usuario.php?e=a&d='.$d.'";
						//-->
						</SCRIPT>');
					}
					
				break;
		}

	}
	function traduzPerfil($perfil){
	  
			$st=array("a"=>"Administrador",
					  "e"=>"Editor",
			          "-"=>"desconhecido");
		return $st[$perfil];
	}
	
}

?>