<?
/**
 * Mostra mensagem de informação usando jqueryui.com. Baixe a biblioteca em http://jqueryui.com para usar esta função.
 * @name info
 * @since v. r2
 * @param string $txt
 * @example info("Cadastro efetuado com sucesso!");
 * @return string
 */
function info($txt,$width=false){
    if($width == true){ $width = "width: ".$width."px"; }
    e("<div class=\"ui-widget\" style=\"margin-top: 3px;$width\">");
    e("<div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 5px;\">");
    e("<span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: 0.3em;\"></span>");
    e($txt);
    e("</div>");
    e("</div>");
}

/**
 * Mostra mensagem de erro usando jqueryui.com. Baixe a biblioteca em http://jqueryui.com para usar esta função.
 * @name error
 * @since v. r2
 * @param string $txt
 * @example error("Você não preencheu o campo e-mail!");
 * @return string
 */
function error($txt,$width=false){
    if($width == true){ $width = "width: ".$width."px"; }
    e("<div class=\"ui-widget\" style=\"margin-top: 3px;$width\">");
    e("<div class=\"ui-state-error ui-corner-all\" style=\"padding: 5px;\">");
    e("<span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: 0.3em;\"></span>");
    e($txt);
    e("</div>");
    e("</div>");
}
