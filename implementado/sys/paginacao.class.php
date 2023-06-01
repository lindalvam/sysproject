<?php
class paginacao{

	var $paginaAtual=0; // Página atual
	var $numPaginas=0; // Número de páginas
	var $maxPaginas=0; // Máximo de paginas para paginar
	var $qtdeRegsPagina=0; // Qtde de registros por página
	var $primeiroRegistro=0; // Número do primeiro registro
	var $ultimoRegistro=0; // Número do último registro
	var $qtdeRegsTotal=0; // Número de registros totais
	var $ehPrimeiraPagina=true; // É a primeira página da paginação
	var $ehUltimaPagina=true; // É a última página da paginação
	
	var $urlPrimeiraPagina="";
	var $urlUltimaPagina="";
	var $urlProximaPagina="";
	var $urlPaginaAnterior="";
	
	/* Construtor */
	function paginacao(){}
	
	/* Calcula os parametros da Paginação */
	function calculaPaginacao($numMaxRegs){
		$this->qtdeRegsTotal=$numMaxRegs;
		if($numMaxRegs>$this->qtdeRegsPagina){ // Existe + de 1 página de resultados
			$this->numPaginas=ceil($numMaxRegs/$this->qtdeRegsPagina);
			$this->maxPaginas=$this->numPaginas-1;
			$this->primeiroRegistro=$this->paginaAtual>0?($this->paginaAtual*$this->qtdeRegsPagina)+1:0;
			$this->ehPrimeiraPagina=$this->paginaAtual<1;
			if($numMaxRegs<($this->primeiroRegistro+$this->qtdeRegsPagina)){ // O último registro é inferior à quantidade total de registros
				$this->ultimoRegistro=$numMaxRegs;
				$this->ehUltimaPagina=true;
			}else{
				$this->ultimoRegistro=($this->paginaAtual+1)*$this->qtdeRegsPagina;
				$this->ehUltimaPagina=false;
			}
		}else{
			$this->numPaginas=1;
			$this->maxPaginas=0;
			$this->ehPrimeiraPagina=true;
			$this->ehUltimaPagina=true;
			$this->primeiroRegistro=0;
			$this->ultimoRegistro=$numMaxRegs;
		}
		$this->paginaAtual+=1;
		$this->calculaUrlPaginas();
	}
	
	/* Configura a quantidade de registros por página da paginação e capta a página atual */
	function setaPaginacao($num){
		$this->qtdeRegsPagina=(int) $num;
		$c=new conf();
		$this->paginaAtual=0;
		$parPagina=$c->parametros["get_pagina"];
		if($_SERVER["QUERY_STRING"]!=""){ // Há parametros que devem ser mantidos na URL
			$GETS=explode("&",$_SERVER["QUERY_STRING"]);
			for($x=0;$x<count($GETS);$x++){
				$dados=explode("=",$GETS[$x]);
				if($dados[0]==$parPagina){
					$this->paginaAtual=(int) $dados[1];
					break;
				}
			}
		}
	}
	
	/* Configura o número da página atual  */
	function setaPagina($num){
		$this->paginaAtual=(int) $num;
	}
	
	/* Calcula a URL das páginas da paginação */
	function calculaUrlPaginas(){
		$urlBase = $_SERVER["PHP_SELF"];
		$c=new conf();
		$parPagina=$c->parametros["get_pagina"];
		$strAdd="?";
		if($_SERVER["QUERY_STRING"]!=""){ // Há parametros que devem ser mantidos na URL
			$GETS=explode("&",$_SERVER["QUERY_STRING"]);
			$str="";
			for($x=0;$x<count($GETS);$x++){
				$dados=explode("=",$GETS[$x]);
				if($dados[0]!=$parPagina){
					$str.=$str==""?"":"&";
					$str.=$GETS[$x];
				}
			}
			$urlBase.="?".$str;
			$strAdd="&";
		}
		$this->urlPrimeiraPagina=$urlBase;
		$this->urlUltimaPagina=$urlBase.$strAdd.$parPagina."=".$this->maxPaginas;
		$this->urlProximaPagina=$urlBase.$strAdd.$parPagina."=".$this->paginaAtual;
		$this->urlPaginaAnterior=$urlBase.$strAdd.$parPagina."=".($this->paginaAtual-2);
	}

}
?>