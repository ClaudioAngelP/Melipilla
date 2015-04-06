<?php 

	require_once('../../conectar_db.php');
	
	$id_boletin=$_POST['str']*1;
	$tipo=0;
	$r=cargar_registro("SELECT * FROM boletines JOIN pacientes USING (pac_id) where bolnum =$id_boletin");
	$g=cargar_registros_obj("select* from boletin_detalle join codigos_prestacion on codigo=bdet_codigo and tipo='mle' where bolnum=$id_boletin",true);
	$p=cargar_registros_obj("select * from codigos_procedimientos");
	$t=cargar_registro("SELECT * FROM boletin_equipo where bolnum =$id_boletin");
	
if($t)
{
	//boletin ya ingresado
	$tipo=1;
}


	$arr[0]=$r;
	$arr[1]=$g;
	$arr[2]=$p;
	$arr[3]=$tipo;
	
	print(json_encode($arr));
?>
