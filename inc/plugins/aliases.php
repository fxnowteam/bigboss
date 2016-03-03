<?
/*
* ALIAS PARA O ALERT DO JS
*/
function alert($retorno){
	e("<script type=\"text/javascript\"> alert('$retorno'); </script>");
}

/*
* ALIAS PARA O DIV CLEAR BOTH DO CSS
*/
function cboth(){
	e("<div style=\"clear: both;\"></div>");
}

/*
* FORMS
*/
function form(){
	e("<form name=\"form_".date("YmdHis")."\" method=\"post\" action=\"\">");
}
function fform(){
	e("</form>");
}
function label($txt){
	e("<label>$txt</label>");
}
function input($idname,$value=false){
	e("<input type=\"text\" id=\"$idname\" name=\"$idname\" value=\"$value\">");
}
function ihidden($idname,$value){
	e("<input type=\"hidden\" id=\"$idname\" name=\"$idname\" value=\"$value\">");
}
function textarea($idname,$value=false){
	e("<textarea id=\"$idname\" name=\"$idname\">$value</textarea>");
}
function submit($value){
	e("<input type=\"submit\" id=\"enviar\" name=\"enviar\" value=\"$value\">");
}
function button($idname,$value,$onclick,$style=false){
	e("<input type=\"button\" id=\"$idname\" name=\"$idname\" value=\"$value\" onclick=\"$onclick\"");
	if($style == true){
		e(" style=\"$style\"");
	}
	e(">");
}
function includeJS($path){
	e("<script type=\"text/javascript\" src=\"$path\"></script>");
}
function includeCSS($path){
	e("<link rel=\"stylesheet\" type=\"text/css\" href=\"$path\">");
}
function br(){
	e("<br>");
}

//http://www.php.net/manual/en/function.get-browser.php#101314
function using_ie(){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = False;
	if(preg_match('/MSIE/i',$u_agent)){
		$ub = True;
	}
	return $ub;
} 

//no cache
function nocache(){
	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Expires: {$gmtDate} GMT");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
}

//seta o timezone padrão
function timezone($tz="America/Sao_Paulo"){
	date_default_timezone_set($tz);
}

//error reporting
function error($var){
	if($var == 0){ $var2 = "Off"; }else{ $var2 = "On"; }
	error_reporting($var);
	ini_set('display_errors','$var2');
}

/**
 * Atalho para a função mysql_escape_string
 * 
 * @name escape
 * @author php-br, canal brasileiro de PHP no IRC <irc.freenode.net>
 * @param string $string		//troquei de $n para $string, assim fica mais semantico by LeoCaseiro
 * @return string
 */
function escape($string){
	return mysql_escape_string($string);
}

/**
 * Atalho do mysql_num_rows
 *
 * @name total
 * @param string $query
 * @return int $num
 */
function total($query){
	$num = mysql_num_rows($query);
	return $num;
}

/**
 * Atalho para função mysql_fetch_array
 * 
 * @name fetch
 * @param string $query
 * @return array
 */
function fetch($query){
	$fetch = mysql_fetch_array($query);
	return $fetch;
}

/**
 * Atalho da função echo
 *
 * @name e
 * @param string $texto
 * @return string
 */
function e($texto){
	echo $texto;
}

/**
 * Imprime um parágrafo centralizado
 *
 * @name p
 * @param string $texto
 * @param bool $red	caso for true, exibe texto em vermelho
 */
function p($texto, $red = false){ //(deixei por default o normal, assim...basta digitar p("escrevi aqui"); //Sem precisar do $red
	if(!$red){ //mensagem normal
		e("<p align=\"center\">$texto</p>");
	}elseif($red){ //mensagem de erro
		e("<p align=\"center\" style=\"color: red\">$texto</p>");
	}else{
		echo "<p>$texto</p>";
	}
}
