<?php

	require_once('../../conectar_db.php');

        $bod_id=$_POST['bod_id']*1;
        $fecha1=pg_escape_string($_POST['fecha1']);
        $fecha2=pg_escape_string($_POST['fecha2']);

	$pac_id=($_POST['pac_id']*1);

	$f1=explode('/',$fecha1);
	$dia_i=mktime(0,0,0,$f1[1],$f1[0],$f1[2]);
	$f2=explode('/',$fecha2);
        $dia_f=mktime(0,0,0,$f2[1],$f2[0],$f2[2]);
	$dif_dias=($dia_f-$dia_i)/86400;
	$array_dias=Array();
	$array_pacientes=Array();

        $pac=cargar_registro("SELECT *,
        date_part('year',age('$fecha1'::date, pac_fc_nac)) as edad_anios,
        date_part('month',age('$fecha1'::date, pac_fc_nac)) as edad_meses,
        date_part('day',age('$fecha1'::date, pac_fc_nac)) as edad_dias
	FROM pacientes WHERE pac_id=$pac_id;");
        
	$xls=isset($_POST['xls']);

        function number_format2($num, $dig, $c, $p) {
                GLOBAL $xls;
                if(!$xls)
                        return number_format($num, $dig, $c, $p);
                else
                        return number_format($num, $dig, '.', '');
        }

        function number_format3($num, $dig, $c, $p) {
                GLOBAL $xls;
                if(!$xls)
                        return '$'.number_format($num, $dig, $c, $p).'.-';
                else
                        return number_format($num, $dig, '.', '');
        }


        if(isset($_POST['xls'])) {

                //$b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id");

                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: filename=\"Farmacos_Paciente_".$cen['centro_ruta'].".xls\";");

		print("
                <table>
                        <tr>
                                <td colspan=4><b>Perfil Farmacol&oacute;gico por Paciente</b></td>
                        </tr>
                        <tr>
                                <td colspan=2>Paciente:</td>
                                <td>".htmlentities($pac['pac_appat'].' '.$pac['pac_apmat'].' '.$pac['pac_nombres'])."</td>
                        </tr>
                        <tr>
                                <td colspan=2>Fecha Inicial:</td>
                                <td>".$fecha1."</td>
                        </tr>
                        <tr>
                                <td colspan=2>Fecha Final:</td>
                                <td>".$fecha2."</td>
                        </tr>
                </table>
                ");	

	}	

	$d=cargar_registros_obj("SELECT *, receta_paciente_id AS pac_id
	FROM (SELECT *, receta_fecha_emision::date AS recetad_fecha_inicio, (receta_fecha_emision+(recetad_dias || ' days')::interval)::date AS recetad_fecha_termino FROM receta JOIN recetas_detalle ON recetad_receta_id=receta_id JOIN centro_costo ON receta_centro_ruta=centro_ruta JOIN articulo ON recetad_art_id=art_id LEFT JOIN bodega_forma ON art_forma=forma_id WHERE receta_paciente_id=$pac_id ORDER BY art_glosa) AS foo WHERE recetad_fecha_inicio BETWEEN '$fecha1' AND '$fecha2' OR recetad_fecha_termino BETWEEN '$fecha1' AND '$fecha2';", true);

	if(!$d) { exit("<center><h2>No hay datos en el rango seleccionado.</h2></center>"); }

	$array_articulos=Array();
	$array_pacientes=Array();

	for($i=0;$i<sizeof($d);$i++) {

		if(!isset($array_articulos[$d[$i]['art_codigo']])) {
			$array_articulos[$d[$i]['art_codigo']]=Array();
			$array_articulos[$d[$i]['art_codigo']][0]=$d[$i]['art_glosa'];
		}

		if(!isset($array_pacientes[$d[$i]['art_codigo']][$d[$i]['pac_id']])) {
                        $array_pacientes[$d[$i]['art_codigo']][$d[$i]['pac_id']]=Array();
                        $array_pacientes[$d[$i]['art_codigo']][$d[$i]['pac_id']]['datos']=$d[$i];
			$array_pacientes[$d[$i]['art_codigo']][$d[$i]['pac_id']]['fechas']=Array();
			for($dia=0;$dia<=$dif_dias;$dia++) {
                        	$dia_str=date('d/m/Y', mktime(0,0,0,$f1[1],$f1[0]+$dia,$f1[2]));
	                        $array_pacientes[$d[$i]['art_codigo']][$d[$i]['pac_id']]['fechas'][$dia_str]=0;
                	}
                }

		$fi=explode('/',$d[$i]['recetad_fecha_inicio']);
                $fini=mktime(0,0,0,$fi[1],$fi[0],$fi[2]);
                $ff=explode('/',$d[$i]['recetad_fecha_termino']);
                $ffin=mktime(0,0,0,$ff[1],$ff[0],$ff[2]);

		for($dia=0;$dia<=$dif_dias;$dia++) {
			$ts=mktime(0,0,0,$f1[1],$f1[0]+$dia,$f1[2]);
                        $dia_str=date('d/m/Y', mktime(0,0,0,$f1[1],$f1[0]+$dia,$f1[2]));
			if($ts>=$fini AND $ts<=$ffin)
	                        $array_pacientes[$d[$i]['art_codigo']][$d[$i]['pac_id']]['fechas'][$dia_str]+=$d[$i]['recetad_cant']*24/$d[$i]['recetad_horas'];
                }

		

	}

?>

<table style='width:100%;'>

<tr class='tabla_header'>
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Unidad</td>
<td>Servicio/Unidad Cl&iacute;nica</td>
<?php
        for($dia=0;$dia<=$dif_dias;$dia++) {
                $dia_str=date('d/m', mktime(0,0,0,$f1[1],$f1[0]+$dia,$f1[2]));
                $array_dias[]=$dia_str;
                print("<td>$dia_str</td>");
        }
?>
</tr>

<?php

	$art_codigo=''; $pac_id=0; $c=0;

	for($i=0;$i<sizeof($d);$i++) {

		if($art_codigo!=$d[$i]['art_glosa']) {
			$clase=($c++%2)==0?'tabla_fila':'tabla_fila2';
			print("<tr class='$clase'><td style='text-align:right;font-weight:bold;'>".$d[$i]['art_codigo']."</td><td>".$d[$i]['art_glosa']."</td>
			<td style='text-align:center;'>".$d[$i]['forma_nombre']."</td>
			<td style='text-align:center;'>".$d[$i]['centro_nombre']."</td>
			");
			$art_codigo=$d[$i]['art_glosa'];

			$fechas=$array_pacientes[$d[$i]['art_codigo']][$d[$i]['pac_id']]['fechas'];

			foreach($fechas AS $key=>$val) {
				if($val!=0)
					print("<td style='text-align:right;'>".number_format2($val,2,',','.')."</td>");
				else
					print("<td style='text-align:right;color:lightgray;'>".number_format2($val,2,',','.')."</td>");
			}
		}

		print("</tr>");
	}


?>

</table>
