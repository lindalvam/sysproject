<?php 
class projeto_equipe extends super{
    var $tabela='projeto_equipe';
	var $campos=array("id","id_projeto","id_usuario", "perfil_usuario","created_at");
	var $campo_id='id';          
	var $campo_dataatual=array();  
	var $campo_datacadastro=array("created_at");
	var $campo_datatime=array();

	var $arquivos=array();

	function __construct(){
		parent::super();
	}  
	
	function comNome(){
		$this->campos[]="(SELECT p.nome from usuario p where p.id=projeto_equipe.id_usuario) AS responsavel_display";
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
				$this->procedimentoErro("Houve um erro na tentativa de listar as equipes do projeto cadastrados. Consulte o log do sistema e contate o administrador.","alert","pop_area.php");
			break;
			
			/* Busca registro */
			case "captaRegistro":
				$id=((int) $_GET["id"])>0?((int) $_GET["id"]):0;
				$this->listar(0,1,$this->campo_id,$this->campo_id."=".$id);
				if($this->numRegs<1){
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                         <!--
                         alert("Nenhuma equipe de projeto foi encontrado.");						 
                         //-->
                         </SCRIPT>');
				}
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
						 alert("Houve um erro na tentativa de gravar a equipe do projeto. Consulte o log do sistema.");
	                     history.go(-1);
	                     //-->
	                     </SCRIPT>'));
				}else{
						$acao=$args[$this->campo_id]==""?"incluída":"alterada";
						$d="Equipe do projeto $acao com sucesso!";
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
                    $d="Houve um erro na tentativa de excluir a equipe do projeto. Consulte o log do sistema e contate o administrador.";
                    $this->procedimentoErro("","","");
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                     <!--
                     location.href="index.php?e=e&d='.$d.';
                     //-->
                     </SCRIPT>'));
				}else{
				    $d="Registro(s) excluído(s) com sucesso.";
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="index.php?e=a&d='.$d.'";
	                     //-->
	                     </SCRIPT>'));
				}
			break;
			case "muda_status":
				$this->mudaValorPorCampo("status",addslashes($_POST["status"]),$this->campo_id,addslashes($_POST["regs"]),false);
				$this->captaErros();
				$acao=$this->traduzStatus($_POST["status"],true);
				$d="Equipe do projeto $acao com sucesso.";
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="index.php?e=a&d='.$d.'";
	                     //-->
	                     </SCRIPT>');
			break;
			/*--------------------------------------------------------------
						AJAX									
			--------------------------------------------------------------*/
            case "ajaxSalvarConteudo":
                // IMPLEMENTAR!
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