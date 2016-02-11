<?
// aliases para bootstrap

class bs {
	function tooltip($texto){
		$return = " data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"$texto\"";
		return $return;
	}
	
	function msg($texto,$tipo){
		if($tipo == 0){ $tipo = "info"; }
		if($tipo == 1){ $tipo = "warning"; }
		?>
			<div class="alert alert-dismissable alert-<?= $tipo ?>">
				 
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">
					×
				</button>
				<?= $texto ?>
			</div>
		<?
	}
}
