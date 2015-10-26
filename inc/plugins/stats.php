<?
/**
  * Classe para coletar e visualizar estatísticas de visitas e pageviews
  */
class stats(){
	/**
	  * Instala banco de dados no primeiro uso da função stats_registra(); que inicia a coleta de dados
	  */ 
	private function install(){
		mysql_query("CREATE TABLE IF NOT EXISTS contador ( id int(11) NOT NULL AUTO_INCREMENT, ip text, cont int(11) DEFAULT NULL, data date DEFAULT NULL, PRIMARY KEY (id) )") or die(mysql_error());
		return 1;
	}

	/**
	  * Coleta dados de visitas e visualizações com base no IP
	  */
	public function registra(){
		$this->install();
		$ip = $_SERVER['REMOTE_ADDR'];
		$sel = mysql_query("SELECT * FROM contador WHERE ip = '$ip' and data = '".date("Y-m-d")."'") or die(mysql_error());
		if(mysql_num_rows($sel) == 0){
			$insert = mysql_query("INSERT INTO contador (ip, cont, data) VALUES ('$ip','1','".date("Y-m-d")."')") or die(mysql_error());
		}else{
			$r = mysql_fetch_array($sel);
			$contagem = $r["cont"];
			$contagem = $contagem + 1;
			$update = mysql_query("UPDATE contador SET cont = '$contagem' WHERE ip = '$ip' and data = '".date("Y-m-d")."'") or die(mysql_error());
		}
		$sel2 = mysql_query("SELECT * FROM contador") or die(mysql_error());
		$visitas = mysql_num_rows($sel2);
		while($b = mysql_fetch_array($sel2)){
			$views = $views + $b["cont"];
		}
		$dados = "$visitas;$views";
		return $dados;
	}

	/**
	  * Formulário para visualizar os dados coletados
	  */
	function visualiza($reload){
		echo "<form name=\"form\" method=\"post\" action=\"?reload=ok\">";
		echo "Estat&iacute;sticas mensais: ";
		echo "<input name=\"anomes\" type=\"type\" value=\"".date("Y")."-mm\"><input type=\"submit\" value=\"ok\">";
		echo "</form>* aaaa: ano<br>mm: m&ecirc;s";
		if($reload == ""){
			echo "<h3>Visitas hoje</h3>";
			$sel = mysql_query("SELECT * FROM contador WHERE data = '".date("Y-m-d")."'") or die(mysql_error());
			echo mysql_num_rows($sel)." visitas e ";
			while($a = mysql_fetch_array($sel)){ $views = $views + $a["cont"]; }
			echo $views." visualiza&ccedil;&otilde;es";
			$views = 0;
			echo "<h3>Visitas ontem</h3>";
			$diahoje = date("d");
			$diaontem = $diahoje - 1;
			if($diaontem == 0){
				if(date("m") == 3){
					$diaontem = 28;
				}elseif(date("m") == 1 or date("m") == 2 or date("m") == 4 or date("m") == 6 or date("m") == 9 or date("m") == 11){
					$diaontem = 31;
				}else{
					$diaontem = 30;
				}
				$mesontem = date("m") - 1;
				if($mesontem == 0){
					$mesontem = 12;
					$anoontem = date("Y") - 1;
				}else{
					$anoontem = date("Y");
				}
			}else{
				$mesontem = date("m");
				$anoontem = date("Y");
			}
			$dataontem = $anoontem."-".$mesontem."-".$diaontem;
			$sel2 = mysql_query("SELECT * FROM contador WHERE data = '$dataontem'") or die(mysql_error());
			echo mysql_num_rows($sel2)." visitas e ";
			while($b = mysql_fetch_array($sel2)){ $views = $views + $b["cont"]; }
			echo $views." visualiza&ccedil;&otilde;es";
			$views = 0;
			echo "<h3>Visitas este m&ecirc;s</h3>";
			$sel3 = mysql_query("SELECT * FROM contador WHERE data LIKE '%".date("Y-m")."%'") or die(mysql_error());
			echo mysql_num_rows($sel3)." visitas e ";
			while($c = mysql_fetch_array($sel3)){
				$views = $views + $c["cont"];
			}
			echo $views." visualiza&ccedil;&otilde;es";
			$views = 0;
		}else{
			$anomes = $_POST["anomes"];
			echo "<h3>$anomes</h3>";
			$diacont = 0;
			while($diacont != 31){
				$diacont = $diacont + 1;
				$datastats = $anomes."-".$diacont;
				$sel3 = mysql_query("SELECT * FROM contador WHERE data = '$datastats'") or die(mysql_error());
				echo "<b>".$datastats.": </b><br>";
				$visitas_mes = $visitas_mes + mysql_num_rows($sel3);
				echo mysql_num_rows($sel3)." visitas e ";
				while($c = mysql_fetch_array($sel3)){
					$views = $views + $c["cont"];
				}
				$views_mes = $views_mes + $views;
				echo $views." visualiza&ccedil;&otilde;es<br>";
				$views = 0;
			}
			echo "<br><br><b>Visitas no m&ecirc;s: </b>$visitas_mes <br>
			<b>Visualiza&ccedil;&otilde;es no m&ecirc;s: </b>$views_mes";
		}
	}
}
