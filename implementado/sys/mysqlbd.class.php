<?php 
class mysqlbd{
    
    var $logerro; // instância de logerro
    var $conn;
    var $DB_TABELA;
	var $DB_BASE;
    var $DB_CAMPOS;
    var $DB_RESULTADO;
    var $numRegs; 
    var $DB_query=array(); 
    
    function mysqlbd(){
        $this->logerro=new logerro();
    }
    
    function clean($valor){
       return trim(addslashes(stripslashes($valor)));
    }
    
    function _err($txt,$linha,$arq){
        $this->logerro->captaErros($txt,$linha,$arq);
    }
    
    function conecta($h,$u,$p){
        $this->conn=&mysql_connect($h,$u,$p);
        $this->_err(mysql_error(),__LINE__,__FILE__);
        mysql_select_db($this->DB_BASE,$this->conn);
        $this->_err(mysql_error(),__LINE__,__FILE__);
    }

       function desconecta(){
    	     mysql_close($this->conn);
             $this->_err(mysql_error(),__LINE__,__FILE__);
       }

	    function ultimo_id(){
			 $query="SHOW TABLE STATUS LIKE '".$this->DB_TABELA."'";
			 $this->DB_query[]=$query;
		     $result=mysql_query($query);
			 $this->_err(mysql_error(),__LINE__,__FILE__);
			 $row=mysql_fetch_array($result);
			 $this->_err(mysql_error(),__LINE__,__FILE__);
			 mysql_free_result($result);
			 $this->_err(mysql_error(),__LINE__,__FILE__);
			 return $row["Auto_increment"];
	   }
	   
	   function listarTabelas($db){
			$x=0;
			$result=mysql_list_tables($db);
			$this->_err(mysql_error(),__LINE__,__FILE__);
			while($row=mysql_fetch_array($result)){
				$this->DB_RESULTADO[]=$row;
				$x++;
			}
			$this->_err(mysql_error(),__LINE__,__FILE__);
			$this->numRegs=$x;
			@mysql_free_result($result);
			$this->_err(mysql_error(),__LINE__,__FILE__);
	   }
	   
	   function listarCampos($tabela){
			$x=0;
			$query="DESC ".$tabela;
			$result=mysql_query($query);
			$this->_err(mysql_error(),__LINE__,__FILE__);
			while($row=mysql_fetch_array($result)){
				$this->DB_RESULTADO[]=$row;
				$x++;
			}
			$this->_err(mysql_error(),__LINE__,__FILE__);
			$this->numRegs=$x;
			mysql_free_result($result);
			$this->DB_query[]=$query;
			$this->_err(mysql_error(),__LINE__,__FILE__);
	   }

       function altera($valor,$clause){
         $query="UPDATE ".$this->DB_TABELA." SET ";
         $y=0;$z=0;
         while($this->DB_CAMPOS[$y]!=""){
            $k=$z==0?"":", ";
            if($valor[$this->DB_CAMPOS[$y]]!=""){
                $query.=$k.$this->DB_CAMPOS[$y]."=".$valor[$this->DB_CAMPOS[$y]];
                $z++;
            }
            $y++;
         }
         $query.=" WHERE ".$clause;
         $this->DB_query[]=$query;
         mysql_query($query);
         $this->_err(mysql_error(),__LINE__,__FILE__);
       }

       function insere($valor){
         $query="INSERT into ".$this->DB_TABELA." (";
         $y=0;
         $x=0;
         $valor2=array();
         while($this->DB_CAMPOS[$y]!=""){
            if($valor[$this->DB_CAMPOS[$y]]!=""){
                $query.=$x==0?$this->DB_CAMPOS[$y]:",".$this->DB_CAMPOS[$y];
                $valor2[$this->DB_CAMPOS[$y]]=$valor[$this->DB_CAMPOS[$y]];
                $x++;
            }
            $y++;
         }
         
          $query.=") VALUES(".implode(",",$valor2).")";
          mysql_query($query);
          $this->DB_query[]=$query;
          $this->_err(mysql_error(),__LINE__,__FILE__);
          @mysql_free_result($this->DB_result);	
          $this->_err(mysql_error(),__LINE__,__FILE__);
       }

      function deleta($cond){
         $query=$cond==""?"DELETE from ".$this->DB_TABELA:
                 "DELETE from ".$this->DB_TABELA." where ".$cond;
         mysql_query($query);
         $this->DB_query[]=$query;
         $this->_err(mysql_error(),__LINE__,__FILE__);
      }

      function executa_query($query,$retorno=false){
        $x=0;
		$ret=array();
        $result=mysql_query($query);
        $this->_err(mysql_error(),__LINE__,__FILE__);
        while($row=@mysql_fetch_array($result)){
			if(!$retorno){
				$this->DB_RESULTADO[]=$row;
			}else{
				$ret[]=$row;
			}
            $x++;
        }
          $this->_err(mysql_error(),__LINE__,__FILE__);
		  if(!$retorno){
			$this->numRegs=$x;
		  }else{
		   return $ret;
		  }
          @mysql_free_result($result);
          $this->DB_query[]=$query;
          $this->_err(mysql_error(),__LINE__,__FILE__);
       }
       
       function listar($ini=0,$fim=0,$ord="",$cond="",$campos=array(),$grupo=""){
            $campos=$campos==array()?$this->DB_CAMPOS:$campos;
            $w=$cond==""?"":" WHERE ".$cond;
            $g=$grupo==""?"":" GROUP BY ".$grupo;
            $o=$ord==""?"":" ORDER BY ".$ord;
            $l=$fim==0?"":" LIMIT ".$ini.",".$fim;
            $query="SELECT ".implode(",",$campos)." FROM ".$this->DB_TABELA.$w.$g.$o.$l;
            $this->DB_query[]=$query;
            $this->executa_query($query);
            $this->_err(mysql_error(),__LINE__,__FILE__);
       }
	   
	 /*
		* Função de retorno das foreign keys da tabela $tabela
		* Retorna false ou um array na seguinte estrutura:
		* $array[$indice]["idTabelaOrigem"]
		* 		[$indice]["idTabelaDestino"]
		* 		[$indice]["tabelaDestino"]
		*/
   function getForeignKeys($tabela){
   		$retorno=array();
   	   	$dados=$this->executa_query("SHOW CREATE TABLE ".$tabela,true);
   	   	if(is_array($dados)){
   	   		$linhas=explode("\n",$dados[0][1]);
   	   		$nLinhas=count($linhas);
   	   		$w=0;
   	   		for($x=0;$x<$nLinhas;$x++){
   	   			if(strtoupper(substr(trim($linhas[$x]),0,10))=="CONSTRAINT"){
   	   				$ini=strpos($linhas[$x],"(")+2;
   	   				$fim=strpos($linhas[$x],"`) R");
					$retorno[$w]["idTabelaOrigem"]= substr($linhas[$x],$ini,$fim-$ini);
					$ini=strpos($linhas[$x],"REFERENCES `")+12;
   	   				$fim=strpos($linhas[$x],"` (");
   	   				$retorno[$w]["tabelaDestino"]= substr($linhas[$x],$ini,$fim-$ini);
   	   				$ini=strpos($linhas[$x],"` (`")+4;
   	   				$fim=strrpos($linhas[$x],"`)");
   	   				$retorno[$w]["idTabelaDestino"]= substr($linhas[$x],$ini,$fim-$ini);
   	   				$w++;
   	   			}
   	   		}
   	   		return $retorno;
   	   	}else{
   	   		return false;
   	   	}
   }
}
?>