<?php

	require_once('../../conectar_db.php');

	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$func_id=$_POST['func_id']*1;
	$ver=$_POST['ver']*1;

	if($func_id!=0) {
		$func_w="func_id=$func_id";
	} else {
		$func_w='true';
	}
	
	$meses[1]='Enero';
	$meses[2]='Febrero';
	$meses[3]='Marzo';
	$meses[4]='Abril';
	$meses[5]='Mayo';
	$meses[6]='Junio';
	$meses[7]='Julio';
	$meses[8]='Agosto';
	$meses[9]='Septiembre';
	$meses[10]='Octubre';
	$meses[11]='Noviembre';
	$meses[12]='Diciembre';
	if($ver==0)
	$l=cargar_registros_obj("SELECT * FROM (SELECT func_id,fecha::date, upper(func_nombre) AS func_nombre, ip, COALESCE((la_glosa||' / '||la_glosa2),ruta)as glosa, COUNT(*) AS cnt FROM logs_acceso LEFT JOIN logs_acceso_rutas on la_ruta=ruta JOIN funcionario USING (func_id) WHERE ruta!='/produccion/chat_status.php' AND (CASE  WHEN ruta='/produccion/chat_mensajes.php'  THEN (CASE WHEN http_post !='' THEN TRUE ELSE FALSE END) ELSE TRUE END) AND ruta!='/produccion/autocompletar_sql.php' AND (fecha::date BETWEEN '$fecha1' AND '$fecha2') AND $func_w GROUP BY fecha::date, func_nombre, ip, la_glosa,la_glosa2,ruta,func_id) AS foo ORDER BY cnt DESC;;");
	if($ver==1)
	$l=cargar_registros_obj("SELECT func_id,fecha, upper(func_nombre) AS func_nombre, ip,  (la_glosa||' / '||la_glosa2)as glosa, la_id FROM logs_acceso left JOIN logs_acceso_rutas on la_ruta=ruta JOIN funcionario USING (func_id) WHERE ruta!='/produccion/chat_status.php' AND (CASE  WHEN ruta='/produccion/chat_mensajes.php'  THEN (CASE WHEN http_post !='' THEN TRUE ELSE FALSE END) ELSE TRUE END) AND ruta!='/produccion/autocompletar_sql.php' AND (fecha::date BETWEEN '$fecha1' AND '$fecha2') AND $func_w ORDER BY fecha ASC;");
	if($ver==2)
	$l=cargar_registros_obj("SELECT * FROM (SELECT  func_id,(la_glosa||' / '||la_glosa2)as glosa, COUNT(*) AS cnt FROM logs_acceso left JOIN logs_acceso_rutas on la_ruta=ruta JOIN funcionario USING (func_id) WHERE ruta!='/produccion/chat_status.php' AND (CASE  WHEN ruta='/produccion/chat_mensajes.php'  THEN (CASE WHEN http_post !='' THEN TRUE ELSE FALSE END) ELSE TRUE END) AND ruta!='/produccion/autocompletar_sql.php' AND  (fecha::date BETWEEN '$fecha1' AND '$fecha2') AND $func_w GROUP BY la_glosa,la_glosa2,func_id) AS foo ORDER BY cnt DESC;;");
	
	if($ver==3)
	$l=cargar_registros_obj("
SELECT * FROM (SELECT  date_part('month',fecha) as fecha, upper(func_nombre) AS func_nombre,  
 COUNT(*) AS cnt,func_id 
FROM logs_acceso 
LEFT JOIN logs_acceso_rutas on la_ruta=ruta 
JOIN funcionario USING (func_id) 
WHERE ruta!='/produccion/chat_status.php' 
AND (CASE  WHEN ruta='/produccion/chat_mensajes.php'  THEN (CASE WHEN http_post !='' THEN TRUE ELSE FALSE END) ELSE TRUE END) 
AND ruta!='/produccion/autocompletar_sql.php' AND ( date_part('month',fecha)=date_part('month','$fecha1'::date)) 
AND TRUE GROUP BY   date_part('month',fecha), func_nombre,func_id) AS foo ORDER BY cnt DESC ");
	
	if($ver==4)
	$l=cargar_registros_obj("
SELECT * FROM (SELECT  date_part('month',fecha) as fecha,  
COALESCE((la_glosa||' / '||la_glosa2),ruta)as glosa, COUNT(*) AS cnt 
FROM logs_acceso 
LEFT JOIN logs_acceso_rutas on la_ruta=ruta 
JOIN funcionario USING (func_id) 
WHERE ruta!='/produccion/chat_status.php' 
AND (CASE  WHEN ruta='/produccion/chat_mensajes.php'  THEN (CASE WHEN http_post !='' THEN TRUE ELSE FALSE END) ELSE TRUE END) 
AND ruta!='/produccion/autocompletar_sql.php' AND ( date_part('month',fecha)=date_part('month','$fecha1'::date))  
AND TRUE GROUP BY   date_part('month',fecha), la_glosa,la_glosa2,ruta) AS foo ORDER BY cnt DESC ");
?>

<table style='width:100%;font-size:11px;'>

<tr class='tabla_header'>
<?php if($ver==0 OR $ver==1) { ?><td>Fecha</td>
<td>Funcionario</td>
<td>IP</td>
<td>Ruta</td><?php } 

if($ver==3) { ?>
<td>Fecha</td>
<td>Funcionario</td>
<td># Accesos</td>
<td>Grafico</td>
<?php }


if($ver==2) { ?>
<td># Accesos</td>

<?php }

if($ver==4) { ?>
<td>Fecha</td>
<td>Ruta</td>
<td># Accesos</td>
<?php }
?>

<?php if($ver==0 OR $ver==2) { ?><td># Accesos</td><td>Grafico</td><?php } ?>
</tr>

<?php

for($i=0;$i<sizeof($l);$i++) {
$clase=$i%2==0?'tabla_fila':'tabla_fila2';

print("<tr class='$clase'>");

if($ver==0 OR $ver==1)
print("
<td style='text-align:center;'>".substr($l[$i]['fecha'],0,19)."</td>
<td style='text-align:left;font-size:9px;'>".htmlentities($l[$i]['func_nombre'])."</td>
<td style='text-align:center;'>".($l[$i]['ip'])."</td>
<td style='text-align:left;font-size:9px;'>".htmlentities($l[$i]['glosa'])."</td>"
);
if($ver==2)
print("

<td style='text-align:left;font-size:9px;'>".htmlentities($l[$i]['glosa'])."</td>"
);


if($ver==3)
print("
<td style='text-align:center;'>".$meses[($l[$i]['fecha'])]."</td>
<td style='text-align:left;font-size:9px;'>".htmlentities($l[$i]['func_nombre'])."</td>
<td style='text-align:right;'>".number_format($l[$i]['cnt'],0,',','.')."</td>
<td style='text-align:center;font-size:9px;'><img src='iconos/magnifier.png' onClick='detalle(".$l[$i]['func_id'].");' style='cursor:pointer;' />
</td>
");

if($ver==4)
print("
<td style='text-align:center;'>".$meses[($l[$i]['fecha'])]."</td>
<td style='text-align:left;font-size:9px;'>".htmlentities($l[$i]['glosa'])."</td>
<td style='text-align:right;'>".number_format($l[$i]['cnt'],0,',','.')."</td>
");


if($ver==0 OR $ver==2)
print("
<td style='text-align:right;'>".number_format($l[$i]['cnt'],0,',','.')."</td>
<td style='text-align:center;font-size:9px;'><img src='iconos/magnifier.png' onClick='detalle(".$l[$i]['func_id'].");' style='cursor:pointer;' />
</td>
");

print("</tr>");


}

?>


</table>	
