<?php
/**
 * @name bigboss
 * @author Original by Tiago Floriano <tiagofloriano@gmail.com> <http://paico.github.io/>
 * @author Modified by LeoCaseiro <http://www.leocaseiro.com/> 2008-11-28 11:47
 * @author Modified by nome <email ou URI> aaaa-mm-dd hh:mm  //para os próximos que modificarem
 * @version 1.0.2
 * @since  Documentação no formato PHPDocumentor e arquivo em UTF-8		Release 1.0.1	//aqui devemos colocar as releases
 * 
 * 
 * 
 * ÍNDICE
 * 
 * Resumo de funções: funções que são abreviações(resumos) de outras funções
 * Login de usuários: funções para fazer um login de usuário de forma simples e prática
 * Banco de Dados: funções para inserir, selecionar, excluir e atualizar dados
 * Arquivos: manipulação de Arquivos
 * Protocolos: email e ftp
 * Utilidades: funções úteis
 * Plugins: adicione aqui os plugins do bigboss
 * 
 */


####################
### Resumo de funções ###
####################

/**
 * Resumo da função mysql_real_escape_string
 *
 * @name str
 * @param string $nome
 * @return string
 */
function str($nome){
	$filtro = strip_tags($nome);
	$filtro = addslashes($filtro);
	$filtro = mysql_real_escape_string($filtro);
	return $filtro;
}

####################
### LOGIN DE USUÁRIO ###
####################

/**
 * função que autentica o USUÁRIO
 *
 * @name login
 * @param string $usuario
 * @param string $senha
 * @param string $destino
 */
function login($usuario, $senha, $destino){
	//verifica se o USUÁRIO e a senha constam na tabela
	echo "Verificando usu&aacute;rio ... ";
	$sel = sel("usuarios","usuario = '$usuario' and senha = '$senha'","","");
	echo "<b>ok</b><br>";
	if(mysql_num_rows($sel) == 0){//se não existir, mostra a mensagem abaixo
		echo "<i>Este usu&aacute;rio n&atilde;o existe!</i><br><br>";
		echo "<a href=\"javascript:history.go(-1);\">Voltar</a>";
		exit; //encerra a execução do arquivo, não permitindo que o USUÁRIO prossiga
	}else{
		echo "Gravando sess&atilde;o ... ";
		//guarda usuario e senha
		$_SESSION["login"] = $usuario;
		$_SESSION["senha"] = $senha;
		echo "<b>ok</b><br>";
		//guarda um valor de sessão único para gravar que o USUÁRIO está logado
		$_SESSION["idsession"] = date("H") + date("i") + date("s") + date("d") + date("m") + date("Y");
		$idsession = $_SESSION["idsession"];
		ins("sessoes","usuario, senha, sessao","'$usuario', '$senha', '$idsession'");
		echo "Redirecionando ... ";
		echo "<meta http-equiv=\"refresh\" content=\"0;URL=$destino\">";
		echo "<b>ok</b>";
		exit;
	}
}

/**
 * função para desautenticar o USUÁRIO
 *
 * @name logoff
 * @param sting $usuario
 * @param string $senha	//na minha opinião, este valor é desnecessário para esta função By LeoCaseiro
 * @param string $destino
 */
function logoff($usuario, $senha, $destino){
	$ids = $_SESSION["idsession"];
	$sel = sel("sessoes","ids = '$ids' and usuario = '$usuario and senha = '$senha'","","");
	if(total($sel) != 0){
		$r = fetch($sel);
		$del = del("sessoes",$r["id"]);
		$_SESSION["idsession"] = "";
		$_SESSION["login"] = "";
		$_SESSION["senha"] = "";
		echo "Usu&aacute;rio desautenticado...";
	}
}

/**
 * Verifica se o USUÁRIO está autenticado e o redireciona para a página de login
 *
 * @name is_autenticado	
 * @param string $destino
 */
function is_autenticado($destino) {
	if(is_on("usuarios") == false){
		e("Voc&ecirc; n&atilde;o est&aacute; logado! Redirecionando...");
		e("<meta http-equiv=\"refresh\" content=\"0;URL=$destino\">");
	}
}


/**
 * Verifica se o USUÁRIO está autenticado
 *
 * @name is_on
 * @return bool
 */
function is_on(){
	$login = str($_SESSION["login"]);
	$senha = str($_SESSION["senha"]);
	$sel = sel("usuarios","usuario = '$login' and senha = '$senha'","","");
	if(total($sel) == 0){
		return false;
	}else{
		$ids = $_SESSION["idsession"];
		$sel = sel("sessoes","ids = '$ids' and usuario = '$usuario and senha = '$senha'","","");
		if(total($sel) == 0){
			return false;
		}else{
			return true;
		}
	}
}

######################
### BANCO DE DADOS ###
######################

/**
 * função para conectar no Bando de Dados
 * 
 * @name con
 * @param string $usuario
 * @param string $senha
 * @return bool
 */
function con($usuario, $senha){
	if(!mysql_connect("localhost",$usuario,$senha)){
		echo "Erro ao conectar no db.";
		return false;
	} else {
		return true;
	}
}


/**
 * Selecionando um db
 *
 * @name db
 * @param string $db
 * @return bool
 */
function db($db){
	if(!mysql_select_db($db)){
		echo "Erro ao selecionar db.";
		return false;
	} else {
		return true;
	}
}


/**
 * função para exucutar consulta(select) no banco
 *
 * @name sel
 * @param string $tabela
 * @param string $condicoes
 * @param string $ordem
 * @param int $limite
 * @return true|die		if die, print mysql_error
 */
function sel($tabela, $condicoes, $ordem = false, $limite = false){
	$query = "SELECT * FROM $tabela";
	if($condicoes != "") {
		$query .= " WHERE $condicoes";
	}
	if($ordem != ""){
		$query .= " ORDER BY $ordem";
	}
	if($limite != ""){
		$query .= " LIMIT $limite";
	}
	$sql = mysql_query($query) or die(mysql_error());
	return $sql;
}


/**
 * Retorna o valor de um determinado registro(id) na tabela
 *
 * @name campo
 * @param string $tabela
 * @param string $campo
 * @param int $id
 * @return string
 */
function campo($tabela, $campo, $id){
	$sel = mysql_query("SELECT * FROM $tabela WHERE id = '$id'") or die(mysql_error());
	$r = mysql_fetch_array($sel);
	return $r[$campo];
}


/**
 * Insere os campos com os respectivos valores
 *
 * @name ins
 * @param string $tabela
 * @param string $campos	seguidos de vírgula
 * @param string $valores	seguidos de vírgula
 * @return true|die		if die, print mysql_error
 */
function ins($tabela, $campos, $valores){
	$query = "INSERT INTO $tabela ($campos) VALUES ($valores)";
	$sql = mysql_query($query) or die(mysql_error());
	return $sql;
}


/**
 * Efetua uma alteração (update) em uma tabela com seus respectivos valores
 *
 * @name upd
 * @param string $tabela
 * @param string $dados
 * @param int $id
 * @return true|die		if die, print mysql_error
 */
function upd($tabela, $dados, $id){
	$query = "UPDATE $tabela SET $dados WHERE id = '$id'";
	$sql = mysql_query($query) or die(mysql_error());
	return $sql;
}


/**
 * Deletar
 *
 * @name del
 * @param string $tabela
 * @param int $id
 * @return true|die		if die, print mysql_error
 */
function del($tabela, $id){
	$query = "DELETE FROM $tabela WHERE id = '$id'";
	$sql = mysql_query($query) or die(mysql_error());
	return $sql;
}


/**
 * Criar tabela
 *
 * @name createT
 * @param string $nometabela
 * @param string $campos
 * @return true|die		if die, print mysql_error
 */
function createT($nometabela,$campos){
	$sql = mysql_query("CREATE TABLE IF NOT EXISTS $nometabela ( id int(11), $campos, primary key(id) )") or die(mysql_error());
	return $sql;
}

##############
### ARQUIVOS ###
##############

# - Nome: Upload de arquivos
function up($arquivo,$destino,$extensoes){//para permitir todas extensões de arquivos, deixe a $extensoes em branco, se não, informe todas as extensões permitidas

}

# - Nome: Gera miniatura de imagem gif, png ou jpg
/**
 * Gera miniatura de imagem gif, png ou jpg
 *
 * @param string $imagem
 * @param int $h	altura
 * @param int $w	largura
 * @return string endereço da miniatura gerada da imagem ($imagem)
 * @example im("imagens/minhaimagem.png",150,200);
 */
function im($imagem, $h, $w){
	//verifica na $imagem o que é pasta, o que é arquivo e o que é extensão
	$explode = explode("/",$imagem);
	$pasta = $explode[0]."/"; // $pasta = pasta_de_imagens/
	$arquivo = $explode[1]; // $arquivo = arquivo.jpg ou .gif ou .png
	$explode = explode(".",$arquivo);
	$extensao = $explode[1];
	$nomearquivo = $explode[0];
	//script original: http://sniptools.com/tutorials/generating-jpggifpng-thumbnails-in-php-using-imagegif-imagejpeg-imagepng
	//adaptado por Tiago Floriano Webdesigner em 27 de outubro de 2007
	$thumbWidth = $w; // Intended dimension of thumb
	$thumbHeight = $h;
	if(!file_exists($pasta.$nomearquivo."_".$w.$h.".".$extensao)){
		if($extensao == "jpg" or $extensao == "JPG" or $extensao == "jpeg" or $extensao == "JPEG"){
			// Beyond this point is simply code.
			$sourceImage = imagecreatefromjpeg($imagem);
			$sourceWidth = imagesx($sourceImage);
			$sourceHeight = imagesy($sourceImage);

			$targetImage = imagecreatetruecolor($thumbWidth,$thumbHeight);
			imagecopyresampled($targetImage,$sourceImage,0,0,0,0,$thumbWidth,$thumbHeight,imagesx($sourceImage),imagesy($sourceImage));
			imagejpeg($targetImage,$pasta.$nomearquivo."_".$w.$h.".".$extensao);
			$thumbName = $pasta.$nomearquivo."_".$w.$h.".".$extensao;
		}
		if($extensao == "gif" or $extensao == "GIF"){
			// Beyond this point is simply code.
			$sourceImage = imagecreatefromgif($imagem);
			$sourceWidth = imagesx($sourceImage);
			$sourceHeight = imagesy($sourceImage);

			$targetImage = imagecreatetruecolor($thumbWidth,$thumbHeight);
			imagecopyresampled($targetImage,$sourceImage,0,0,0,0,$thumbWidth,$thumbHeight,imagesx($sourceImage),imagesy($sourceImage));
			imagegif($targetImage, $pasta.$nomearquivo."_".$w.$h.".".$extensao);
			$thumbName = $pasta.$nomearquivo."_".$w.$h.".".$extensao;
		}
		if($extensao == "png" or $extensao == "PNG"){
			// Beyond this point is simply code.
			$sourceImage = imagecreatefrompng($imagem);
			$sourceWidth = imagesx($sourceImage);
			$sourceHeight = imagesy($sourceImage);

			$targetImage = imagecreatetruecolor($thumbWidth,$thumbHeight);
			imagecopyresampled($targetImage,$sourceImage,0,0,0,0,$thumbWidth,$thumbHeight,imagesx($sourceImage),imagesy($sourceImage));
			imagepng($targetImage, $pasta.$nomearquivo."_".$w.$h.".".$extensao);
			$thumbName = $pasta.$nomearquivo."_".$w.$h.".".$extensao;
		}
	}else{
		$thumbName = $pasta.$nomearquivo."_".$w.$h.".".$extensao;
	}
	return $thumbName;
}

/**
 * Exibe arquivos de uma pasta
 *
 * @param string $diretorio
 */
function pasta($diretorio){

}

/**
 * Edita arquivo
 *
 * @param string $arquivo
 */
function arq($arquivo){

}

################
### PROTOCOLOS ###
################

/**
 * função para Enviar E-mail
 *
 * @param string $destino
 * @param string $assunto
 * @param string $mensagem
 * @param string $remetente
 * @param string $tipo	(html|texto)	
 * @param string $protocolo	(mail|smtp)
 */
function sendmail($destino, $assunto, $mensagem, $remetente, $tipo, $protocolo){//o $tipo é se o e-mail é html ou somente texto, o $protocolo é se é usando a função mail ou smtp
	if($protocolo == "mail"){ //criado por LeoCaseiro e adaptado por Tiago Floriano
		if($tipo == "html"){
			/* Para enviar email HTML, você precisa definir o header Content-type. */
			$headers  = 'MIME-Version: 1.0\n';
			$headers .= 'Content-type: text/html; charset=iso-8859-1\n';
		}
		/* headers adicionais */
		$headers .= "From: $deNome <$deEmail>\r\n";
		$headers .= "To: $paraNome <$paraEmail>\r\n";
		/* Enviar o email */
		if(mail($para, $assunto, $mensagem, $headers)) {
			return true;
		}else{
			return false;
		}
	}else{
		//smtp
	}
}



/**
 * Criar conta de e-mail no cPanel
 *
 * @param string $email
 * @param string $senha
 */
function criamail($email, $senha){

}

/**
 * Deleta e-mail do cPanel
 *
 * @param string $email
 * @param string $senha
 */
function delmail($email,$senha){

}

##########################
### UTILIDADES E INUTILIDADES ###
##########################

/**
 * Retira caracteres especiais de uma string
 *
 * @param string $string
 * @return string
 */
function remCE($string){
	//retira acentos
	$string = str_replace("ã","a",$string);
	$string = str_replace("á","a",$string);
	$string = str_replace("à","a",$string);
	$string = str_replace("ä","a",$string);
	$string = str_replace("â","a",$string);

	$string = str_replace("ẽ","e",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("è","e",$string);
	$string = str_replace("ë","e",$string);
	$string = str_replace("ê","e",$string);

	$string = str_replace("ĩ","i",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ì","i",$string);
	$string = str_replace("ï","i",$string);
	$string = str_replace("î","i",$string);

	$string = str_replace("õ","o",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ò","o",$string);
	$string = str_replace("ö","o",$string);
	$string = str_replace("ô","o",$string);

	$string = str_replace("ũ","u",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("ù","u",$string);
	$string = str_replace("ü","u",$string);
	$string = str_replace("û","u",$string);

	$string = str_replace("ç","c",$string);

	//retira outras porcarias
	$string = str_replace("\"","",$string);
	$string = str_replace("'","",$string);
	#$string = str_replace("�","",$string);
	$string = str_replace("`","",$string);
	$string = str_replace("!","",$string);
	$string = str_replace("#","",$string);
	$string = str_replace("$","",$string);
	$string = str_replace("%","",$string);
	#$string = str_replace("�","",$string);
	$string = str_replace("&","",$string);
	$string = str_replace("*","",$string);
	$string = str_replace("(","",$string);
	$string = str_replace(")","",$string);
	$string = str_replace("_","",$string);
	$string = str_replace("-","",$string);
	$string = str_replace("+","",$string);
	$string = str_replace("=","",$string);
	#$string = str_replace("�","",$string);
	$string = str_replace("}","",$string);
	$string = str_replace("]","",$string);
	#$string = str_replace("�","",$string);
	#$string = str_replace("�","",$string);
	#$string = str_replace("�","",$string);
	#$string = str_replace("�","",$string);
	#$string = str_replace("�","",$string);
	#$string = str_replace("�","",$string);
	$string = str_replace("{","",$string);
	$string = str_replace("[","",$string);
	#$string = str_replace("�","",$string);
	$string = str_replace("^","",$string);
	$string = str_replace("~","",$string);
	$string = str_replace("?","",$string);
	$string = str_replace("/","",$string);
	#$string = str_replace("�","",$string);
	$string = str_replace("<","",$string);
	$string = str_replace(",","",$string);
	$string = str_replace(">","",$string);
	$string = str_replace(".","",$string);
	$string = str_replace(":","",$string);
	$string = str_replace(";","",$string);
	$string = str_replace("|","",$string);
	$string = str_replace("\\","",$string);
	$string = str_replace(" ","",$string);

	//retira espa�os
	$string = trim($string);

	return $string;
}


/**
 * Exibe mês em Português do Brasil
 *
 * @param int $mes
 * @return string	(use ucfirst para deixar com a 1a. letra maiúscula)
 */
function mes($mes){
	if($mes == 1){
		$mes = "janeiro";
	}
	if($mes == 2){
		$mes = "fevereiro";
	}
	if($mes == 3){
		$mes = "mar&ccedil;o";
	}
	if($mes == 4){
		$mes = "abril";
	}
	if($mes == 5){
		$mes = "maio";
	}
	if($mes == 6){
		$mes = "junho";
	}
	if($mes == 7){
		$mes = "julho";
	}
	if($mes == 8){
		$mes = "agosto";
	}
	if($mes == 9){
		$mes = "setembro";
	}
	if($mes == 10){
		$mes = "outubro";
	}
	if($mes == 11){
		$mes = "novembro";
	}
	if($mes == 12){
		$mes = "dezembro";
	}
	return $mes;
}


/**
 * Transforma data formato aaaa-mm-dd para dd/mm/aaaa ou dd de mm de aaaa
 *
 * @param string $data 	(aaaa-mm-dd)
 * @param bool $tipo	(if true, exibe dd/mm/aaaa, else print dd de mm de aaaa) //valor default
 * @return string	(dd/mm/aaaa)
 */
function data($data, $tipo = 0){
	if($tipo == 0){
		$data = explode("-",$data);
		$data = $data[2]."/".$data[1]."/".$data[0];
	}elseif($tipo == 1){
		$data = explode("-",$data);
		$data = $data[2]." de ".mes($data[1])." de ".$data[0];
	}else{
		$data = explode("/",$data);
		$data = $data[2]."-".$data[1]."-".$data[0];
        }
	return $data;
}


/**
 * Atalho para URL (Ex: ?pagina ao invés de page=pagina)
 *
 * @param string $uri
 * @param string $pasta
 * @return string
 * @example $path = url($_SERVER['REQUEST_URI'],"meusite/"); include($path);
 */
function url($uri, $pasta) { //Podemos amadurecer mais ainda esta função
	if($uri == "/$pasta"){ $uri = "/?home"; }
	$uri = explode("?",$uri);
	$uri = explode("&",$uri[1]);
	$uri = $uri[0];
	$arquivo = "pages/".$uri.".php";//pages é o nome da pasta onde ficarão os arquivos internos que guardam o conteúdo, que serão inseridos no arquivo principal, no lugar do conteúdo. Mude para o nome da pasta onde você guarda os arquivos.
	if(is_file($arquivo)){
		$path = $arquivo;
	}else{
		$path = "pages/erro.php";
	}
	return $path;
}

/**
 * Verifica se uma string está vazia
 *
 * @param string $var
 * @param int $num
 * @return bool
 */
function is_clear($var, $num){ //pega variavel e o número de campos que há nela
	$str = explode(" ",$var); //registra array
	$cont = 0;
	while($cont <= $num){ //enquanto a $cont for diferente de $num, ele faz a verificação
		if($str[$cont] != ""){ //se $str não for vazia, soma 1 na $result
			$result = $result + 1;
		}
		$cont = $cont + 1;
	}
	if($result == $num){ //se todas as $str tiverem valor, o resultado deste if deve ser true
		return true;
	}else{ //se houver algum campo vazio, dará false
		return false;
	}
}

/**
 * função que gera lista de um select de estado(UF) do Brasil
 *
 * @param string $tipo	(full)
 * @param string $selecione (campo marcado)
 */
function gerauf($tipo, $selecione){ //Acho que poderia melhorar essa função usando o selected="selected" e também trocando a ordem dos campos
	if($tipo == "full"){
		echo "<select name=\"uf\">";
		echo "<option>$selecione</option>";
	}
	echo "<option value=\"AC\">AC</option>
	<option value=\"AL\">AL</option>
	<option value=\"AP\">AP</option>
	<option value=\"AM\">AM</option>
	<option value=\"BA\">BA</option>
	<option value=\"CE\">CE</option>
	<option value=\"DF\">DF</option>
	<option value=\"ES\">ES</option>
	<option value=\"GO\">GO</option>
	<option value=\"MA\">MA</option>
	<option value=\"MT\">MT</option>
	<option value=\"MS\">MS</option>
	<option value=\"MG\">MG</option>
	<option value=\"PA\">PA</option>
	<option value=\"PB\">PB</option>
	<option value=\"PR\">PR</option>
	<option value=\"PE\">PE</option>
	<option value=\"PI\">PI</option>
	<option value=\"RJ\">RJ</option>
	<option value=\"RN\">RN</option>
	<option value=\"RS\">RS</option>
	<option value=\"RO\">RO</option>
	<option value=\"RR\">RR</option>
	<option value=\"SC\">SC</option>
	<option value=\"SP\">SP</option>
	<option value=\"SE\">SE</option>
	<option value=\"TO\">TO</option>";
	if($tipo == "full"){
		echo "</select>";
	}
}


##################
### PLUGINS
/* para adicionar novas extensões, adicione as seguintes linhas abaixo:
if(file_exists("plugins/nomedoarquivo.php")){
	include("plugins/nomedoarquivo.php");
}
*/
if(file_exists("plugins/aliases.php")){
	include("plugins/aliases.php");
}
