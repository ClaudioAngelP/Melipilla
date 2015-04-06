<?php

	function imprimir_despacho($log_id) {

	$tmp_folder=dirname(__FILE__);

	chdir('/var/www/produccion/conectores/zebra/');

	require_once('../../conectar_db.php');

	// $log_id=$_GET['log_id']*1;

	$l=cargar_registros_obj("SELECT receta_id, art_id, art_codigo, art_glosa, forma_nombre, SUM(-stock_cant) AS total, pac_ficha, (pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS paciente, log_fecha::date AS fecha, recetad_cant, recetad_horas, recetad_dias FROM logs JOIN stock ON stock_log_id=log_id JOIN recetas_detalle ON log_recetad_id=recetad_id AND stock_art_id=recetad_art_id JOIN receta ON recetad_receta_id=receta_id JOIN pacientes ON receta_paciente_id=pac_id JOIN articulo ON stock_art_id=art_id LEFT JOIN bodega_forma ON art_forma=forma_id WHERE log_id=$log_id GROUP BY receta_id, art_id, art_codigo, art_glosa, forma_nombre, pac_ficha, pac_nombres, pac_appat, pac_apmat, log_fecha::date, recetad_cant, recetad_horas, recetad_dias;");

	$ip=$_SERVER['REMOTE_ADDR'];

	for($iii=0;$iii<sizeof($l);$iii++) {

	$d=$l[$iii];

	$f=file_get_contents('template.txt');

	$nombre1=substr($d['art_glosa'],0,22);
	if(strlen($d['art_glosa'])>22) $nombre2=substr($d['art_glosa'],22,22); else $nombre2='';
	if(strlen($d['art_glosa'])>44) $nombre3=substr($d['art_glosa'],44,22); else $nombre3='';

	if(strlen($d['paciente'])>32) $paciente=substr($d['paciente'],0,32); else $paciente=$d['paciente'];

	$d['dosis']=number_format($d['recetad_cant']).' '.$d['forma_nombre'].' c/ '.$d['recetad_horas'].' hrs. por '.$d['recetad_dias'].' dias.';

	$dosis1=substr($d['dosis'],0,32);
	if(strlen($d['dosis'])>32) $dosis2=substr($d['dosis'],32,32);

	$f=str_replace('{$codigo}', $d['receta_id'], $f);
	$f=str_replace('{$fecha}', $d['fecha'], $f);
	$f=str_replace('{$total}', substr(number_format($d['total']).' '.$d['forma_nombre'],0,16), $f);
	$f=str_replace('{$nombre1}', $nombre1, $f);
	$f=str_replace('{$nombre2}', $nombre2, $f);
	$f=str_replace('{$nombre3}', $nombre3, $f);
	$f=str_replace('{$receta_id}', $d['receta_id'], $f);
	$f=str_replace('{$ficha}', $d['pac_ficha'], $f);
	$f=str_replace('{$paciente}', $paciente, $f);
	$f=str_replace('{$dosis1}', $dosis1, $f);
	$f=str_replace('{$dosis2}', $dosis2, $f);
	$art_id=$d['art_id']*1;

	$f=strtr($f, utf8_decode("áéíóúñÁÉÍÓÚÑ"), "aeiounAEIOUN");

	file_put_contents("log/$log_id.$art_id.txt", $f);

	exec('smbclient //'.$ip.'/ZebraTLP -N -c "print log/'.$log_id.'.'.$art_id.'.txt"');

	}

	chdir($tmp_folder);

	}

?>
