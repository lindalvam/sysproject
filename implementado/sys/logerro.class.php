<?php
/*
  * Classe de manipulação de erros
  */  
class logerro{

    var $erro=array();
	var $erroLong=array();
	var $quantidadeLinhas=0;
	var $tipo; // Tipo de log: Print, Arquivo ou tabela.
	var $arquivo; // Nome do arquivo no caso de opção de Log Erro for Arquivo.
	var $tabela; // Nome da Tabela, no caso de opção de log erro for Tabela.
    
	
	
	// Configuração automática do LogErro de acordo com a classe de configuração
	function configLogErro($tipo,$arquivo, $tabela){
		$this->tipo = $tipo;
		$this->arquivo = $arquivo;
		$this->tabela = $tabela;
		
	}
	
	// Alias para captaErros (a função tem nome não-intuitivo)
	function logTexto($texto,$linha="",$arq=""){
        $this->captaErros($texto,$linha,$arq);
    }
    
    function captaErros($texto,$linha="",$arq=""){
        if(trim($texto)!=""){
			$f=new funcoes();
			$arqq = $f->somenteArquivoSemPastaLogErro($arq);
            $this->erro["descricao"][]=$texto;
            $this->erro["linha"][]=$linha;
            $this->erro["arq"][]=$arq;
            $this->erro["data"][]=date("d/m/Y H:i:s");
			$this->erroLong[] = "[".date("d/m/Y H:i:s")."] $arqq ($linha): $texto";
			$this->quantidadeLinhas++;
        }
    }
	
	function log(){
		//die(print_r($this));
		if($this->tipo=="print"||$this->tipo=="display"||$this->tipo=="echo"){
			$this->mostrar();
		}else if($this->tipo=="arquivo"||$this->tipo=="file"){
			$this->salvar();
		}else if($this->tipo=="tabela"||$this->tipo=="bd"||$this->tipo=="banco"){
			$this->gravarTabela();
		}else{
			// Nothing
		}
	}
    
    function mostrar(){
		if($this->erroLong!=array()){
			print_r($this->erroLong);
		}
    }
	
	function gravarTabela(){
         // print_r($this->erro);
		 // TODO: implementar
    }
	
	function getQuantidadeLinhas(){
		return $this->quantidadeLinhas;
	}
	
	
	function salvar($arq=""){
		$ok=false;
		$arq=$arq==""?$this->arquivo:$arq;
	  	if($h=fopen($arq,"a+")){
		 $valor="";
		  for($x=0;$x<count($this->erro["descricao"]);$x++){
			 $valor.="\r\n".$this->erro["data"][$x]." [".$this->erro["arq"][$x]."] "." [L:".$this->erro["linha"][$x]."] -> ".$this->erro["descricao"][$x];
		  }
		  if(fwrite($h,$valor)){
			fclose($h);
			$ok=true;
		  }
		}
	   clearstatcache();
	   return $ok;
	}
	
	/*
	 * Mensagem de erro do sistema
	 */
	function alertaErro($mensagem,$tipo,$redirect=""){
		if($tipo=="alert"){
			$acao=$redirect==""?"":'location.href="'.$redirect.'"';
			die('<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                 <!--
                 alert("'.$mensagem.'");
				 '.$acao.'
                 //-->
                 </SCRIPT>');
		}
	}
}
?>