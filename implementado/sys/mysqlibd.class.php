<?php 
class mysqlibd{
    
    var $logerro; // instância de logerro
    var $conn;
    var $DB_TABELA;
	var $DB_BASE;
    var $DB_CAMPOS;
    var $DB_RESULTADO;
    var $numRegs; 
	var $mysqli; // Objeto MySqli (uso interno)
    var $DB_query=array(); 
    
    function __construct(){
        $this->logerro=new logerro();
    }
	
	// Configuração automática do LogErro de acordo com a classe de configuração
	function configLogErro($tipo,$arquivo, $tabela){
		$this->logerro->tipo = $tipo;
		$this->logerro->arquivo = $arquivo;
		$this->logerro->tabela = $tabela;
		
	}
    
    function clean($valor){
       return trim(addslashes(stripslashes($valor)));
    }
    
    function _err($txt,$linha,$arq){
        $this->logerro->captaErros($txt,$linha,$arq);
    }
    
    function conecta($h,$u,$p, $port=3306){
	 // mysqli_connect($db_host,$db_user,$db_pass,$db_database,$db_port);
		$this->mysqli= new mysqli($h,$u,$p, $this->DB_BASE, $port);// @mysqli_connect($h,$u,$p, $this->DB_BASE, $port);
		$this->mysqli->set_charset("utf8");
		if(mysqli_connect_errno($this->mysqli)){
			$this->_err(mysqli_connect_error(),__LINE__,__FILE__);
			$this->conn=false;
		}
		$this->conn=true;
			
    }

       function desconecta(){
    	     @mysqli_close($this->mysqli);
             //$this->_err(mysql_error(),__LINE__,__FILE__);
       }

	   // Pega o maximo auto_increment - Deve ser substituido adequadamente em cada classe de tabela
	    function ultimo_id(){
			 //$query="SHOW TABLE STATUS LIKE '".$this->DB_TABELA."'";
			 $query='select LAST_INSERT_ID() as Auto_increment';
			 $this->DB_query[]=$query;
		     $result=$this->mysqli->query($query);
			 //$this->_err(mysql_error(),__LINE__,__FILE__);
			 $row=$result->fetch_assoc();
			 //$this->_err(mysql_error(),__LINE__,__FILE__);
			 mysqli_free_result($result);
			 //$this->_err(mysql_error(),__LINE__,__FILE__);
			 return $row["Auto_increment"];
	   }
	   
	   function listarTabelas($db){
			$query="SHOW TABLES";
			$this->DB_query[]=$query;
		    $result=$this->mysqli->query($query);
			 //$this->_err(mysql_error(),__LINE__,__FILE__);
			$result->data_seek(0);
			$this->DB_RESULTADO=array();
			while ($row = $result->fetch_assoc()) {
				$this->DB_RESULTADO[]=$row;
			}
			
			$this->numRegs= $result->num_rows;
			@mysqli_free_result($result);
			
	   }
	   
	   function listarCampos($tabela){
			$x=0;
			$query="DESC ".$tabela;
			$this->DB_query[]=$query;
		    $result=$this->mysqli->query($query);
			 //$this->_err(mysql_error(),__LINE__,__FILE__);
			$result->data_seek(0);
			$this->DB_RESULTADO=array();
			while ($row = $result->fetch_assoc()) {
				$this->DB_RESULTADO[]=$row["Field"];
			}
			
			$this->numRegs= $result->num_rows;
			@mysqli_free_result($result);
	   }

       function altera($valor,$clause){
		$this->mysqli->set_charset("latin1");
         $query="UPDATE ".$this->DB_TABELA." SET ";
         $y=0;$z=0;
         while(isset($this->DB_CAMPOS[$y])){
            $k=$z==0?"":", ";
			if(isset($valor[$this->DB_CAMPOS[$y]])){
				if($valor[$this->DB_CAMPOS[$y]]!=""){
					
					$query.=$k.$this->DB_CAMPOS[$y]."=".$valor[$this->DB_CAMPOS[$y]];
					$z++;
				}
			}
            $y++;
         }
         $query.=" WHERE ".$clause;
         $this->DB_query[]=$query;
		 $result=$this->mysqli->query($query);
		 if(!$result){
			$this->_err($this->mysqli->error."($query)",__LINE__,__FILE__);
			return -1;
		 }
         return $this->mysqli->affected_rows;
       }

       function insere($valor){
		 $this->mysqli->set_charset("latin1");
         $query="INSERT into ".$this->DB_TABELA." (";
         $y=0;
         $x=0;
         $valor2=array();
         while(isset($this->DB_CAMPOS[$y])){
			if(isset($valor[$this->DB_CAMPOS[$y]])){
				if($valor[$this->DB_CAMPOS[$y]]!=""){
					$query.=$x==0?$this->DB_CAMPOS[$y]:",".$this->DB_CAMPOS[$y];
					$valor2[$this->DB_CAMPOS[$y]]= $valor[$this->DB_CAMPOS[$y]];
					$x++;
				}
			}
            $y++;
         }
         
          $query.=") VALUES(".implode(",",$valor2).")";
		  $this->DB_query[]=$query;
		  
		  $result=$this->mysqli->query($query);
		 if(!$result){
			$this->_err($this->mysqli->error."($query)",__LINE__,__FILE__);
			return -1;
		 }
         return $this->mysqli->affected_rows;
       }

      function deleta($cond){
		$this->mysqli->set_charset("latin1");
         $query=$cond==""?"DELETE from ".$this->DB_TABELA:
                 "DELETE from ".$this->DB_TABELA." where ".$cond;
          $this->DB_query[]=$query;
		  
		 $result=$this->mysqli->query($query);
		 if(!$result){
			$this->_err($this->mysqli->error."($query)",__LINE__,__FILE__);
			return -1;
		 }
         return $this->mysqli->affected_rows;
      }

      function executa_query($query,$retorno=false){
			$this->mysqli->set_charset("latin1");
	    	$this->DB_query[]=$query;
		    $result=$this->mysqli->query($query);
			if($this->mysqli->error!=""){
				$this->_err($this->mysqli->error."($query)",__LINE__,__FILE__);
				return null;
			}
			
			//print_r($result);			
			$this->numRegs=$result->num_rows;			
			$this->DB_RESULTADO=array();

			//print_r($result->fetch_array());
			
			if($retorno){
				//echo "COM RETORNO";
				while ($row = $result->fetch_assoc()) {
					$this->DB_RESULTADO[]=$row;
					//echo "ROW: ";print_r($row);
				}
			}
			
			
			/* free result set */
			//$result->close();
			//print_r($this->DB_RESULTADO);
			//die();
			if($retorno){
				//echo "___";
				return $this->DB_RESULTADO;
			}
			
       }
       
       function listar($ini=0,$fim=0,$ord="",$cond="",$campos=array(),$grupo=""){
			$this->mysqli->set_charset("latin1");
            $campos=$campos==array()?$this->DB_CAMPOS:$campos;
            $w=$cond==""?"":" WHERE ".$cond;
            $g=$grupo==""?"":" GROUP BY ".$grupo;
            $o=$ord==""?"":" ORDER BY ".$ord;
            $l=$fim==0?"":" LIMIT ".$ini.",".$fim;
            $query="SELECT ".implode(",",$campos)." FROM ".$this->DB_TABELA.$w.$g.$o.$l;
            $this->DB_query[]=$query;
            $this->executa_query($query,true);
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
   
   // True if not exists connection
   function semConexao(){
		return !$this->conn;
   }
}
?>