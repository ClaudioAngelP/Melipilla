<?php require_once('../../conectar_db.php'); error_reporting(E_ALL);

	$fecha=$_POST['data'];
	$tipo=$_POST['tipo'];
	
	if($tipo==1){
	
		$listado_esp=desplegar_opciones_sql("
			SELECT esp_id, esp_desc FROM especialidades
				WHERE esp_id in (SELECT DISTINCT nom_esp_id FROM nomina WHERE nom_fecha='$fecha')
			ORDER BY esp_desc	
		", NULL, '', '');

	}else if ($tipo==2){
		$listado_esp=desplegar_opciones_sql("
			SELECT esp_id, esp_desc FROM especialidades
				WHERE esp_id in (SELECT DISTINCT destino_esp_id FROM archivo_movimientos WHERE am_final AND am_estado=2)
			ORDER BY esp_desc	
		", NULL, '', '');
	}else{
			$listado_esp=desplegar_opciones_sql("
			SELECT esp_id, esp_desc FROM especialidades
				WHERE esp_id in (SELECT DISTINCT esp_id FROM ficha_espontanea WHERE fesp_estado=0)
			ORDER BY esp_desc	
		", NULL, '', '');
	}
?>
<select id='esp_id' name='esp_id' >
<option value='-1' SELECTED>(Todas las Especialidades...)</option>
<?php if($tipo==1) { ?> <option value='-2'>(Hospitalizaciones no Quir&uacute;rgicas...)</option> <?php } ?>
<?php echo $listado_esp; ?>
</select>
