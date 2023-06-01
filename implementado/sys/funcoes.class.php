<?php 
 /*
  * Classe de fun��es
  */
class funcoes{
    var $c;
 /*
  * Construtor 
  */
  function __construct(){
    
  }

  /**
   * Return the error message by the error code
   */
  function get_error_message($tipo){


	 if($tipo =="zas"){
		return "Não foi possível autenticar no sistema com os dados informados. Digite novamente seu login e senha.";
	 }
	 return "-f001: Mensagem não reconhecida! ($tipo)";
  }
  
    /*
   * Fun��o especial de envio de e-mail com formata��o HTML
   */
  function enviaEmail($conteudo,$para,$assunto,$remetente_email,$remetente_nome=""){
	$h = "MIME-Version: 1.0\r\n";
  	$h.= "Content-type: text/html; charset=iso-8859-1\r\n";
	$h.= "From:".($remetente_nome==""?$remetente_email:$remetente_nome)." <".$remetente_email.">\r\n";
	if( mail($para,$assunto,$conteudo,$h)){
		return true;
	}else{
		return false;
	}
  }
  
  /*
   * @param string valor
   * @return string
   */
  function clean($valor){
   return utf8_encode(trim(addslashes(stripslashes($valor))));
  }
  
  function encodeToUtf8($string) {
     return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
  }
  
  /*
   * @param string valor
   * @param boolean data
   * @param string local
   * @return boolean
   */
  function monta_log_txt($valor,$data,$local){
  $ok=false;
//   if((file_exists($this->LOG_ARQUIVO))&&(is_writable($this->LOG_ARQUIVO))){
  	if($h=fopen($this->LOG_ARQUIVO,"a+")){
	 $valor=$data?date("d/M/Y H:i:s")." ".$local." ".$valor:$local." ".$valor;
	  if(fwrite($h,$valor."\r\n")){
		fclose($h);
		$ok=true;
	  }
	}
//   }
   clearstatcache();
   return $ok;
  }
  
 /* 
  * Retorna uma String qualquer com $num caracteres 
  */
  function senhaAleatoria($num){
  	$lt="";
	$num_to_str=array("1"=>"a","2"=>"b","3"=>"c","4"=>"d","5"=>"e","6"=>"f","7"=>"g","8"=>"h","9"=>"i","10"=>"j","11"=>"k","12"=>"l","13"=>"m","14"=>"n","15"=>"o","16"=>"p","17"=>"q","18"=>"r","19"=>"s","20"=>"t","21"=>"u","22"=>"v","23"=>"w","24"=>"x","25"=>"y","26"=>"z","27"=>"A","28"=>"B","29"=>"C","30"=>"D","31"=>"E","32"=>"F","33"=>"G","34"=>"H","35"=>"I","36"=>"J","37"=>"K","38"=>"L","39"=>"M","40"=>"N","41"=>"O","42"=>"P","43"=>"Q","44"=>"R","45"=>"S","46"=>"T","47"=>"U","48"=>"V","49"=>"W","50"=>"X","51"=>"Y","52"=>"Z","53"=>"0","54"=>"1","55"=>"2","56"=>"3","57"=>"4","58"=>"4","59"=>"6","60"=>"7","61"=>"8","62"=>"9");
	for ($x=0;$x<$num;$x++){
		$nm=rand(1,62);
		$lt.=$num_to_str[$nm];
	}
	return $lt;
  }
  
  /*
   * Encripta��o n�o revers�vel
   * @param = string valor
   * @return string
   */
  function encripta($valor){
	  $this->c=new conf();
      return md5($this->c->SEMENTE_SEGURANCA.$valor.$this->c->SEMENTE_SEGURANCA2);
  }
  
  /*
   * Fun��o de criptografia revers�vel
   */
  function encriptaLm($valor){
  	$chars="1234567890,.+-_/:!@%&*()=qweráéíóúçôâêõãÁÉÍÓÚÇÔÂÊÕÃtyuioplkjhgfdsazxcvbnmQWERT YUIOPLKJHGFDSAZXCVBNM;<>";
	$num=rand(0,strlen($chars));
	$charsa=substr($chars,$num,strlen($chars)).substr($chars,0,$num);
	for($x=0;$x<strlen($chars);$x++){
		$charsb[substr($chars,$x,1)]=substr($charsa,$x,1);
	}
	$retorno="";
	for($x=0;$x<strlen($valor);$x++){
		$retorno.=$charsb[substr($valor,$x,1)];
	}
	return $num."-".rawurlencode($retorno);
  }

  /*
   * Revers�o de criptografia lm
   */
  function decriptaLm($valor){
  	$chars="1234567890,.+-_/:!@%&*()=qweráéíóúçôâêõãÁÉÍÓÚÇÔÂÊÕÃtyuioplkjhgfdsazxcvbnmQWERT YUIOPLKJHGFDSAZXCVBNM;<>";
  	$num=substr($valor,0,strpos($valor,"-"));
	$charsa=substr($chars,$num,strlen($chars)).substr($chars,0,$num);
	for($x=0;$x<strlen($chars);$x++){
		$charsb[substr($charsa,$x,1)]=substr($chars,$x,1);
	}
	$cripto=substr($valor,strpos($valor,"-")+1,strlen($valor));
	$cripto=rawurldecode($cripto);
	$retorno="";
	for($x=0;$x<strlen($cripto);$x++){
		$retorno.=$charsb[substr($cripto,$x,1)];
	}
	return $retorno;
  }
  
  /*
   * @param = string valor
   * @param = boolean somente_data
   * @return string 
   */
  function data_mysql2php($valor,$somente_data=false){
  	$val=explode(" ",$valor);
  	$val2=explode("-",$val[0]);
    return $valor==""?"":$val2[2]."/".$val2[1]."/".$val2[0].(!$somente_data?" ".$val[1]:"");
  }
  
  function retornaArquivoAtual(){
	 
	$d=explode("/",$_SERVER["SCRIPT_NAME"]);
	return $d[count($d)-1];
  }
  
  /*
   * Transforma a string em um array de caracteres
   */
  function string2array($string){
  	$ret=array();
  	for($x=0;$x<strlen($string);$x++){
		$ret[]=substr($string,$x,1);
	}
	return $ret;
  }
  
  function retornaArquivoReferer(){
	$d=explode("/",$_SERVER["HTTP_REFERER"]);
	$pos=strpos($d[count($d)-1],"?")>0?strpos($d[count($d)-1],"?"):strlen($d[count($d)-1]);
	return substr($d[count($d)-1],0,$pos);
  }
  
  /*
   * @param= string valor
   * @param= boolean somente_data 
   */
  function data_php2mysql($valor,$somente_data=false){
  	$val=explode(" ",$valor);
  	$val2=explode("/",$val[0]);
    return $valor==""?"":$val2[2]."-".$val2[1]."-".$val2[0].(!$somente_data?" ".$val[1]:"");
  }
  
 
  /*
   * @param array valor
   * @return array 
   */
   
   function array_trim($valor){
    $arr=array();
    if(is_array($valor)){
	  $x=0;
	  foreach($valor as $key=>$texto){
	    if($texto!=""){
		  $arr[$x]=$texto;
		  $x++;
		}// End If
	  }// End Foreach
	}// End if is array
     return $arr;
   }
   
   /*
    * Fun��o para somar arrays de indices num�ricos em
	* arrays bidimensionais
	*/
	function arrayJoin($array_lista){
		$retorno=array();
		$w=0;
		for($x=0;$x<count($array_lista);$x++){
			for($y=0;$y<count($array_lista[$x]);$y++){
				$retorno[$w]=$array_lista[$x][$y];
				$w++;
			}
		}
		return $retorno;
	}

  /*
   * Apaga a imagem ou arquivo
   */
   function apaga_arquivo($val){
      @unlink($val);
   }
   
   /*
	 * String $dir no formato "pasta/pasta1/pasta2/pasta3", 
	 * cria pastas nesta hierarquia
	 */
	function criaDiretorioRecursivo($dir){
		$dirs=explode("/",$dir);
		$dir_atual="";
		for($x=0;$x<count($dirs);$x++){
			$dir_atual.=$x==0?"":"/";
			$dir_atual.=$dirs[$x];
			if(!@file_exists($dir_atual)){
				@mkdir($dir_atual,0777); //0755
				@chmod($dir_atual,0777);
			}else{
				@chmod($dir_atual,0777);
			}
		}
	}
   
   function armazenaArquivo($arq,$onde){
	  $ok=move_uploaded_file($arq["tmp_name"],$onde);
	  @chmod($onde,0777);
	  return $ok;
   }
   
	function remove_diretorio($dir) {
	  if ($handle = @opendir("$dir")) {
	   while (false !== ($item = @readdir($handle))) {
	     if ($item != "." && $item != "..") {
	       if (is_dir("$dir/$item")) {
	         remove_directory("$dir/$item");
	       } else {
	         @unlink("$dir/$item");
	  //       echo " removendo $dir/$item<br>\n";
	       }
	     }
	   }
	   @closedir($handle);
	   @rmdir($dir);
	//   echo "removendo $dir<br>\n";
	  }
	}


	function download($arquivo){
		$arq=explode("/",$arquivo);
		$fname=$arq[count($arq)-1];
		$fsize=@filesize($arquivo);
		header("HTTP/1.1 200 OK");
	    header("Content-Length: $fsize");
   		header("Content-Type: application/force-download");
   		header("Content-Disposition: attachment; filename=$fname");
   		header("Content-Transfer-Encoding: binary");
   		if(@file_exists($arquivo) && $fh = @fopen($arquivo, "rb")){
	       while($buf = @fread($fh, 2000)){
    	       @print $buf;
		   }
   		   @fclose($fh);
		}else{
	       @header("HTTP/1.1 404 Not Found");
	   }
	}
	
	/*
	   * For�a o Download do arquivo $nome_arquivo.
	   * � poss�vel chamar a fun��o sem implementa��o de classe:
	   * FUNCOES::downloadArquivo("nome_arquivo");
	   */
  function downloadArquivo($nome_arquivo,$novo_nome){
  	if(is_file($nome_arquivo)){
		header('Content-type: application/force-download');
		header('Content-Transfer-Encoding: Binary');
		header('Content-length: '.filesize($nome_arquivo));
		header('Content-disposition: attachment;filename='.$novo_nome);
		readfile($nome_arquivo);
		exit();
	}
  }
	
	function cookie2session($COOKIE,$nomeSessao){
		if(!is_array($_SESSION["COOKIE"])){
			$_SESSION["COOKIE"]=array();
		}
		$_SESSION["COOKIE"][$nomeSessao]=serialize($COOKIE);
	}
	
	function pegaCookieSessao($nome){
		return unserialize($_SESSION["COOKIE"][$nome]);
	}
	
	/* Retorna o valor das parcelas calculados pela tabela Price */
	function vtp($v,$j,$p){
		return $j==0?$v/$p:($v*$j)/(1 - pow(1/(1+$j),$p));
	}
	
	
	/*
	 * Tradu��o do m�s para o nome do m�s em portugu�s
	 * $mes String
	 * return String
	 */
	function mes_pt($mes){
		$mes=strtolower($mes);
		$ret="";
		switch($mes){
			case "1":case "01": case "jan":
				$ret = "janeiro";break;
			case "2":case "02": case "feb":
				$ret = "fevereiro";break;
			case "3":case "03": case "mar":
				$ret = "mar�o";break;
			case "4":case "04": case "apr":
				$ret = "abril";break;
			case "5":case "05": case "may":
				$ret = "maio";break;
			case "6":case "06": case "jun":
				$ret = "junho";break;
			case "7":case "07": case "jul":
				$ret = "julho";break;
			case "8":case "08": case "aug":
				$ret = "agosto";break;
			case "9":case "09": case "sep":
				$ret = "setembro";break;
			case "10":case "10": case "oct":
				$ret = "outubro";break;
			case "11":case "11": case "nov":
				$ret = "novembro";break;
			case "12":case "12": case "dec":
				$ret = "dezembro";break;
		}
		 return $ret;
	}
	
	/*
	  * Retorna data no formato: xx de MES de ANO
	  */
	function dataPorExtenso($data){
	   $dts=split("/",$data);
	   return $dts[0]." de ".ucfirst($this->mes_pt($dts[1]))." de ".$dts[2];
	}
	
	/*
 * Fun��o utilizada para pegar trechos de texto, sem cortar 
 * palavras.
 * Retorna String
 */
   function pegaTrecho($string,$quant){
   	if(strlen($string)<=$quant){
		return $string;
	}else{
		$a="";
		$z=-1;
		while($a==""){
			$i=substr($string,$quant+$z,1);
			if(($i==".")||($i==" ")){ // Achei onde quebrar a string
				$a=$z;
			}else if(($quant+$z)<1){ // N�o tem onde quebrar a String.
				$a=-$quant;
			}else{ // Continua 
				$z--;
			}
		}
		return substr($string,0,$quant+$a);
	}
   }
	
	/**
	  * De uma string do tipo /var/www/a/b/c/blablabla/arquivo.php retorna somente a �ltima parte
	  * $separador = Separador de diret�rios. Se n�o informar,  usar� barra pra direita.
	  */
	function somenteArquivoSemPastaLogErro($string,$separador=""){
		if($string==""){ return "";}
		$separador = $separador == "" ? DIRECTORY_SEPARATOR :$separador;
		$lista = explode($separador, $string);
		//print_r($lista);
		if(count($lista)<1){
			return $string;
		}
		return $lista[count($lista)-1];
	}

}

if (!function_exists('getallheaders')) 
{ 
    function getallheaders() 
    { 
           $headers = []; 
       foreach ($_SERVER as $name => $value) 
       { 
           if (substr($name, 0, 5) == 'HTTP_') 
           { 
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
           } 
       } 
       return $headers; 
    } 
} 

?>