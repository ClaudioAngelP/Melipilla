<?php

	require_once('../../conectar_db.php');
        set_time_limit(0);
        ini_set("memory_limit","1500M");

        $bod_id=$_POST['bod_id']*1;
        $fecha1=pg_escape_string($_POST['fecha1']);
        $fecha2=pg_escape_string($_POST['fecha2']);

	$centro=pg_escape_string($_POST['centro_ruta']);
        

	$f1=explode('/',$fecha1);
	$dia_i=mktime(0,0,0,$f1[1],$f1[0],$f1[2]);
	$f2=explode('/',$fecha2);
        $dia_f=mktime(0,0,0,$f2[1],$f2[0],$f2[2]);
	$dif_dias=($dia_f-$dia_i)/86400;
	$array_dias=Array();
	$array_pacientes=Array();
        
        if($centro!="")
            $w_centro=" and receta_centro_ruta='$centro'";
        else
            $w_centro="";
        

        $cen=cargar_registro("SELECT * FROM centro_costo WHERE centro_ruta='$centro'");
        
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


        if(isset($_POST['xls']))
        {

                //$b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id");

                header("Content-type: application/vnd.ms-excel");
                if($$w_centro!="")
                    header("Content-Disposition: filename=\"Farmacos_Servicio_".$cen['centro_ruta'].".xls\";");
                else
                    header("Content-Disposition: filename=\"Farmacos_Servicio_Todos_los_centros.xls\";");

		print("
                <table>
                        <tr>
                                <td colspan=4><b>Perfil Farmacol&oacute;gico por Servicio</b></td>
                        </tr>
                        <tr>
                                <td colspan=2>Servicio:</td>");
                if($$w_centro!="")
                    print("<td>".htmlentities($cen['centro_nombre'])."</td>");
                else
                    print("<td>Todos los Centros</td>");
                print("
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

	$d=cargar_registros_obj("SELECT *,
	date_part('year',age('$fecha1'::date, pac_fc_nac)) as edad_anios,
        date_part('month',age('$fecha1'::date, pac_fc_nac)) as edad_meses,
        date_part('day',age('$fecha1'::date, pac_fc_nac)) as edad_dias
	FROM (
        SELECT *, receta_fecha_emision::date AS recetad_fecha_inicio, 
        (receta_fecha_emision+(recetad_dias || ' days')::interval)::date AS recetad_fecha_termino 
        FROM receta 
        JOIN recetas_detalle ON recetad_receta_id=receta_id 
        JOIN articulo ON recetad_art_id=art_id 
        LEFT JOIN bodega_forma ON art_forma=forma_id 
        JOIN pacientes ON receta_paciente_id=pac_id 
        WHERE receta_bod_id=$bod_id $w_centro
        ORDER BY art_glosa, pac_appat, pac_apmat, pac_nombres
        ) AS foo 
        WHERE recetad_fecha_inicio BETWEEN '$fecha1' AND '$fecha2' OR recetad_fecha_termino BETWEEN '$fecha1' AND '$fecha2';", true);

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
<?php

	$art_codigo=''; $pac_id=0; $c=0;

	for($i=0;$i<sizeof($d);$i++) {
		if($art_codigo!=$d[$i]['art_glosa']) {
		print("<tr class='tabla_header'><td style='font-size:20px;' colspan=2>".$d[$i]['art_codigo']."</td><td style='font-size:20px;'>".$d[$i]['art_glosa']."</td><td style='font-size:20px;'>".$d[$i]['forma_nombre']."</td><td style='font-size:20px;' colspan=".($dif_dias+1).">$fecha1 - $fecha2</td></tr>");
?>
<tr class='tabla_header'>
<td>RUN</td>
<td>Ficha</td>
<td>Nombre Completo</td>
<td>Edad al <?php echo substr($fecha1,0,5); ?></td>
<?php
        for($dia=0;$dia<=$dif_dias;$dia++) {
                $dia_str=date('d/m', mktime(0,0,0,$f1[1],$f1[0]+$dia,$f1[2]));
                print("<td>$dia_str</td>");
        }
?>
</tr>

<?php
		$art_codigo=$d[$i]['art_glosa'];
		$pac_id=0;
		}

		if($pac_id!=$d[$i]['pac_id']) {
			$clase=($c++%2)==0?'tabla_fila':'tabla_fila2';
			print("<tr class='$clase'><td>".$d[$i]['pac_rut']."</td><td>".$d[$i]['pac_ficha']."</td>
			<td>".$d[$i]['pac_appat']." ".$d[$i]['pac_apmat']." ".$d[$i]['pac_nombres']."</td>
			<td>".$d[$i]['edad_anios']."a ".$d[$i]['edad_meses']."m ".$d[$i]['edad_dias']."d</td>
			");
			$pac_id=$d[$i]['pac_id']*1;

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
