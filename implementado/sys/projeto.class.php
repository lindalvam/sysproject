<?php 
class projeto extends super{
    var $tabela='projeto';
	var $campos=array("id", "titulo", "descricao", "dt_inicio", "dt_fim", "created_at", "updated_at", "status_projeto");
	var $campo_id='id';          
	var $campo_dataatual=array("dt_alteracao");    
	var $campo_datacadastro=array("dt_cadastro");
	var $campo_datatime=array();
	
	var $termo_pesquisado = "";
	
	function __construct(){
		parent::super();
	}
    
    function comGerente(){
        $this->campos[]="NVL((SELECT p.nome from usuario p, projeto_equipe pe where pe.id_usuario =p.id and pe.perfil_usuario='Gerente' and pe.id_projeto=projeto.id limit 1),'-') AS responsavel_display";
        $this->bd->setCampos($this->campos);
	}
	
	function comNomeStatus(){
		$this->campos[]="(SELECT p.status from status_projeto p where p.id=projeto.status_projeto) AS status_display";
        $this->bd->setCampos($this->campos);
	}
	
	
	
	function controller($acao="listar",$args=array()){
       // die($acao);
		switch($acao){
			default:
/*--------------------------------------------------------------
						Ações de Navegação						
--------------------------------------------------------------*/
			/* Listagem de Conteúdos */
			case "listar":
				$this->listar(false,false,$this->campo_id,false,false,$this->campo_id);
				$this->procedimentoErro("Houve um erro na tentativa de listar os projetos cadastrados. Consulte o log do sistema e contate o administrador.","alert","index.php");
			break;
			
			/* Busca registro */
			case "captaRegistro":
				$id=((int) $_GET["id"])>0?((int) $_GET["id"]):0;
				$this->listar(0,1,$this->campo_id,$this->campo_id."=".$id);
				if($this->numRegs<1){
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                         <!--
                         alert("Nenhum projeto foi encontrado.");						 
                         //-->
                         </SCRIPT>');
				}
			break;
			
			/* Busca registro */
			case "pesquisar":
				$termo=addslashes($_POST["regs"]);
				$this->termo_pesquisado = $termo;
				$this->comGerente();
				$this->comNomeStatus();
				$this->listar(0,0,"dt_inicio desc","(LOWER(titulo) like '%".addslashes(strtolower($termo))."%' AND projeto.id=projeto.id)");
				
			break;
/*--------------------------------------------------------------
						CRUD									
--------------------------------------------------------------*/
			case "salvar":
				$id=$_POST[$this->campo_id]>0?$_POST[$this->campo_id]:-1;
				$this->trataPost($_POST);
				$this->salvar();
				$this->captaErros();

				if($this->erros!=array()){ // Houve erros ao salvar o registro
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
						 alert("Houve um erro na tentativa de gravar o destaque. Consulte o log do sistema.");
	                     history.go(-1);
	                     //-->
	                     </SCRIPT>'));
				}else{
						$acao=$args[$this->campo_id]==""?"incluído":"alterado";
						$d="Projeto $acao com sucesso!";
						die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						<!--
						location.href="index.php?e=a&d='.$d.'";
						//-->
						</SCRIPT>');
					}
			break;
			case "excluir":
                $ids=explode(",",$_POST["regs"]);
                //print_r($ids);die();
				for($x=0;$x<count($ids);$x++){
                   // $this->removePorId((int) $ids[$x]);
                   $this->mudaValorPorCampo("status","x",$this->campo_id,addslashes($ids[$x]),false);
				}
				$this->captaErros();
				if($this->erros!=array()){ // Houve erros na gravação de dados
                    $d="Houve um erro na tentativa de excluir o destaque. Consulte o log do sistema e contate o administrador.";
                    $this->procedimentoErro("","","");
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                     <!--
                     location.href="lista_conteudo_home.php?e=e&d='.$d.';
                     //-->
                     </SCRIPT>'));
				}else{
				    $d="Registro(s) excluído(s) com sucesso.";
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="lista_conteudo_home.php?e=a&d='.$d.'";
	                     //-->
	                     </SCRIPT>'));
				}
			break;
			case "muda_status":
				$this->mudaValorPorCampo("status_projeto",addslashes($_POST["status"]),$this->campo_id,addslashes($_POST["regs"]),false);
				$this->captaErros();
				
				//die(print_r($this));
				
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="ger-projeto.php?prj='.$_POST["regs"].'&zs=s'.$_POST["status"].'";
	                     //-->
	                     </SCRIPT>');
			break;
			
		}
	}
	
	
	function procedimentoErro($mensagem,$tipo,$redirect){
		$this->captaErros();
        if($this->erros!=array()){ // Houve erros na listagem
            $this->gravaLogErro(); // Salva log de erros
            if($mensagem!=""){
                logerro::alertaErro($mensagem,$tipo,$redirect);
            }
		}
	}
}

?>