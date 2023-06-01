<?php 
class tarefa extends super{
    var $tabela='tarefa';
	var $campos=array("id", "id_projeto", "id_fase", "id_responsavel", "descricao", "comentario", "dt_inicio", "dt_fim", "created_at", "status_tarefa", "ordem_prioridade");
	var $campo_id='id';          
	var $campo_dataatual=array();  
	var $campo_datacadastro=array("created_at");
	var $campo_datatime=array();

	var $arquivos=array();

	function __construct(){
		parent::super();
	} 
	
	function comNomeProjeto(){
		$this->campos[]="(SELECT p.titulo from projeto p where p.id=tarefa.id_projeto) AS projeto_display";
        $this->bd->setCampos($this->campos);
	}
	
	function comUltimaOrdemPrioridade(){
		$this->campos[]="(SELECT max(p.ordem) from tarefa p where p.id_projeto=tarefa.id_projeto AND p.id_fase=tarefa.id_fase) AS ultima_ordem";
        $this->bd->setCampos($this->campos);
	}
	
	function comNomeResponsavel(){
		$this->campos[]="(SELECT p.nome from usuario p where p.id=tarefa.id_responsavel) AS responsavel_display";
        $this->bd->setCampos($this->campos);
	}
	
	function comNomeFase(){
		$this->campos[]="(SELECT p.fase from projeto_fase p where p.id=tarefa.id_fase) AS fase_display";
        $this->bd->setCampos($this->campos);
	}
	
	function comNomeStatus(){
		$this->campos[]="(SELECT p.status from status_tarefa p where p.id=tarefa.status_tarefa) AS status_display";
        $this->bd->setCampos($this->campos);
	}
	
	function getUltimaOrdemPrioridade($idProjeto,$idFase){
		$idp = (int) $idProjeto;
		$idf = (int) $idFase;
		$query= "SELECT MAX(ordem_prioridade) as ultima_ordem FROM tarefa WHERE (id_projeto = $idp AND id_fase = $idf )";
		$res = $this->getResultadoQuery($query);
		return $res[0]["ultima_ordem"];
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
				$this->procedimentoErro("Houve um erro na tentativa de listar as tarefas cadastradas. Consulte o log do sistema e contate o administrador.","alert","pop_area.php");
			break;
			
			/* Busca registro */
			case "captaRegistro":
				$id=((int) $_GET["id"])>0?((int) $_GET["id"]):0;
				$this->listar(0,1,$this->campo_id,$this->campo_id."=".$id);
				if($this->numRegs<1){
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                         <!--
                         alert("Nenhuma tarefa foi encontrada.");
                         //-->
                         </SCRIPT>');
				}
			break;
/*--------------------------------------------------------------
						CRUD									
--------------------------------------------------------------*/
			case "salvar": 
			case "salvar_tarefa":
				$id=$_POST[$this->campo_id]>0?$_POST[$this->campo_id]:-1;
				$prj=$_POST["id_projeto"];
				$fs=$_POST["id_fase"];
				$redir=$_POST["redir"];
				$redir=$redir==""?'ger-projeto.php?prj='.$prj:$redir;
				$this->trataPost($_POST);
				if($id<1){ // Ultima Ordem de prioridade
					$ordemPrioridade = (int) $this->getUltimaOrdemPrioridade($prj,$fs);
					$ordemPrioridade++;
					$this->seta("ordem_prioridade",$ordemPrioridade);
				}
				
				$this->salvar();
				$this->captaErros();
				//print_r($_POST);
				//die(print_r($this));

				if($this->erros!=array()){ // Houve erros ao salvar o registro
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
						 alert("Houve um erro na tentativa de gravar a tarefa. Consulte o log do sistema.");
	                     location.href="'.$redir.'";
	                     //-->
	                     </SCRIPT>'));
				}else{
						$acao=$args[$this->campo_id]==""?"incluída":"alterado";
						$d="Tarefa $acao com sucesso!";
						die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						<!--
						location.href="'.$redir.'&zs=t";
						//-->
						</SCRIPT>');
					}
			break;
			case "excluir":
                $ids=explode(",",$_POST["regs"]);
				$id = $_POST["id_projeto"];
                //print_r($ids);die();
				for($x=0;$x<count($ids);$x++){
                    $this->removePorId((int) $ids[$x]);
                   // $this->mudaValorPorCampo("status","x",$this->campo_id,addslashes($ids[$x]),false);
				}
				$this->captaErros();
				if($this->erros!=array()){ // Houve erros na gravação de dados
                    $d="Houve um erro na tentativa de excluir a(s) tarefa(s). Consulte o log do sistema e contate o administrador.";
                    $this->procedimentoErro("","","");
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                     <!--
                     location.href="ger-projeto.php?prj='.$id.'&zs=xx";
                     //-->
                     </SCRIPT>'));
				}else{
				    $d="Registro(s) excluído(s) com sucesso.";
					die(utf8_decode('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="ger-projeto.php?prj='.$id.'&zs=xs";
	                     //-->
	                     </SCRIPT>'));
				}
			break;
			case "bloquear":
				$this->mudaValorPorCampo("status_tarefa","5",$this->campo_id,addslashes($_POST["regs"]),false);
				$this->captaErros();
				$id = $_POST["id_projeto"];
				$d="Tarefa bloqueada com sucesso.";
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="ger-projeto.php?prj='.$id.'&zs=bq";
	                     //-->
	                     </SCRIPT>');
			break;
			case "aumenta_ordem": case "aumenta_prioridade":
				$tt = new tarefa();
				$tt->pegaPorId($_POST["regs"]);
				$tarefa=$tt->bd->DB_RESULTADO[0]; // Acesso à ordem anterior
				if($tarefa["ordem_prioridade"]>0){ // Menor ordem possível é 0
					$this->mudaValorPorCampo("ordem_prioridade","ordem_prioridade-1",$this->campo_id,$tarefa["id"],true);
					//print_r($this);
					
					$tk = new tarefa();
					$tk->executaQuery("UPDATE tarefa SET ordem_prioridade=ordem_prioridade+1 
										 WHERE ordem_prioridade= ".($tarefa["ordem_prioridade"]-1)." AND id_projeto=".$tarefa["id_projeto"]." 
										 AND id !=".$tarefa["id"]." AND id_fase=".$tarefa["id_fase"]);
										 
					//die(print_r($tk));
				}
				
				$id = $tarefa["id_projeto"];
				
				$d="Tarefa ordenada com sucesso.";
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="ger-projeto.php?prj='.$id.';";
	                     //-->
	                     </SCRIPT>');
			break;
			case "diminui_ordem": case "diminui_prioridade":
				$tt = new tarefa();
				$tt->pegaPorId($_POST["regs"]);
				$tarefa=$tt->bd->DB_RESULTADO[0]; // Acesso à ordem anterior
				$this->mudaValorPorCampo("ordem_prioridade","ordem_prioridade+1",$this->campo_id,$tarefa["id"],true);
					
				$tk = new tarefa();
				$tk->executaQuery("UPDATE tarefa SET ordem_prioridade=ordem_prioridade-1 
									 WHERE ordem_prioridade= ".($tarefa["ordem_prioridade"]+1)." AND id_projeto=".$tarefa["id_projeto"]." 
									 AND id !=".$tarefa["id"]." AND id_fase=".$tarefa["id_fase"]);
				
				
				$id = $tarefa["id_projeto"];
				
				$d="Tarefa ordenada com sucesso.";
					die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	                     <!--
	                     location.href="ger-projeto.php?prj='.$id.';";
	                     //-->
	                     </SCRIPT>');
			break;
			case "muda_status":
				$this->mudaValorPorCampo("status",addslashes($_POST["status"]),$this->campo_id,addslashes($_POST["regs"]),false);
				$this->captaErros();
				$acao=$this->traduzStatus($_POST["status"],true);
				$d="Tarefa $acao com sucesso.";
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