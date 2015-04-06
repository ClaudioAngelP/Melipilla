<?php 
	ini_set('memory_limit', '256M');

	require_once('../../conectar_db.php');
	//error_reporting(E_ALL);
	
    $fecha_inicio=$_POST['fecha_hosp'];
	$fecha_termino=$_POST['fecha_hosp2'];	
	$filtro_fecha = "hosp_fecha_ing>='$fecha_inicio 00:00:00' AND hosp_fecha_ing<='$fecha_termino 23:59:59'";	
	$busca=trim(pg_escape_string(utf8_decode($_POST['busqueda'])));
	
	if($busca!='') {

		$pbusca=preg_replace('/[^A-Za-z0-9 ]/','_', $busca);
		
		$pbusca=preg_replace('/\s{2,}/', ' ', $pbusca);
		
		$pbusca=str_replace(' ', '%', $pbusca);
		
		$busca_w="
	    (to_tsvector('spanish', pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres || ' ' || pac_ficha )
			@@ plainto_tsquery('".$busca."') )
		OR pac_rut='$busca' OR pac_ficha='$busca' OR
		upper(pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) ILIKE '%$pbusca%'
		";		
		
	} else {
		
		$busca_w="true";
		
	}	
	
	if(isset($_POST['xls']) AND $_POST['xls']=='1') {
	
  	    header("Content-type: application/vnd.ms-excel");
       header("Content-Disposition: filename=\"Informacion_CAMAS_GESTION.xls\";");
       print ("<h1><b>Historial Prestaciones</b></h1>");	
       print ("<br><h5><i>Gesti&oacute;n de Camas</i></h5>");	
       
       
      
		
	}
	
	if($_POST['cuentaCte']*1!=''){
		$hosp_id=$_POST['cuentaCte']*1;
		$filtro_cuenta="hosp_id=$hosp_id";
	}else{
		$filtro_cuenta="true";
	}				
	
	if($_POST['filtro']=='2') {
		
		$l=cargar_registros_obj("
			SELECT hosp_id,pac_rut,pac_nombres,pac_appat,pac_apmat,date_part('year',age( pac_fc_nac ))AS edad_paciente,pac_ficha,
			hospp_fecha_digitacion::date AS hospp_fecha_digitacion,
			func_nombre,hospp_codigo,hospp_nombre,hospp_cantidad,hospp_fecha_realizado::date AS hospp_fecha_realizado


FROM hospitalizacion_prestaciones 
left join hospitalizacion using (hosp_id)
left join pacientes on hosp_pac_id=pac_id
left join funcionario on hospp_func_id=func_id
			WHERE  ($busca_w)   AND ($filtro_cuenta) AND ($filtro_fecha)
			ORDER BY hosp_id	
		", true);
		
	}
	


?>

<input type='hidden' id='ids' name='ids' value='<?php echo $ids; ?>'>
<?php if($_POST['xls']!='1'){ ?>
<table style='width:100%;'>
<tr class='tabla_header'>	
<?php }else{
		print("<table border='1'><tr>");		
	  } ?>
<!--<td>Nro. Folio</td> de momento no interesa esta informacion-->
<td>Cta. Corriente</td>
<td>R.U.T.</td>
<td>Nombre Paciente</td>
<td>Edad</td>
<td>Ficha</td>
<td>Fecha sol.</td>
<td>Solicitante</td>
<td>Prestación</td>
<td>Cantidad</td>
<td>Realizada</td>


</tr>

<?php 


	for($x=0;$x<sizeof($e);$x++) {
		$chtml.='<option value="'.$e[$x].'">'.$e[$x].'</option>';
	}

	if($l){
		for($i=0;$i<sizeof($l);$i++) {
		
			
			
			if($l[$i]['hospp_fecha_realizado']=='') {
			$l[$i]['hospp_fecha_realizado']='<i>Pendiente</i>';
			
		} else {
			$hp[$i]['hospp_fecha_realizado']='<b>'.substr($hp[$i]['hospp_fecha_realizado'],0,18).'</b>';
			
		}						
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($_POST['xls']!='1'){
				print("<tr style='height:50px;' class='$clase'
						onMouseOver='this.className=\"mouse_over\";'
						onMouseOut='this.className=\"$clase\";'>");
			}else{
				print("<tr>");
			}

			print("
				<td style='text-align:center;'>".$l[$i]['hosp_id']."</td>				
				<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
				<td style='font-size:10px;'>".($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat']." ".$l[$i]['pac_nombres'])."</td>
				<td style='text-align:center;'>".$l[$i]['edad_paciente']."</td>
				<td style='text-aling:right;'>".$l[$i]['pac_ficha']."</td>
				<td style='text-align:center;'>".$l[$i]['hospp_fecha_digitacion']."</td>
				<td style='text-aling:right;'>".$l[$i]['func_nombre']."</td>
				<td style='text-align:left;'><b>[".$l[$i]['hospp_codigo']."] - </b> ".trim($l[$i]['hospp_nombre'])."</td>
				<td style='text-aling:center;'>".$l[$i]['hospp_cantidad']."</td>
				<td style='text-aling:left;'>".$l[$i]['hospp_fecha_realizado']."</td>");
							
			
			
			print("</tr>");
			
		
		}
	}else{
		print("<tr><td colspan=13><center>Sin Registros...</center></td></tr>");
	}
		
?>
</table>
