<?php 

	require_once('../../conectar_db.php');
	
	$c=cargar_registros_obj("
		SELECT *, to_char(nom_fecha, 'D') AS dow  FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		JOIN pacientes USING (pac_id)
		LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
		WHERE nomd_diag_cod = 'X'
		ORDER BY nom_fecha, nomd_hora
	", true);

?>

<script>

 buscar_citacion=function(nomd_id) {

      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win =
      window.open('prestaciones/ingreso_nominas/buscar_citacion.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();

        }

 gestiones_citacion=function(nomd_id) {

      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win =
      window.open('prestaciones/ingreso_nominas/gestionar_citacion.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();

        }

</script>

<center>

<div class='sub-content' style='width:950px;'>
<div class='sub-content'>
<img src='iconos/arrow_refresh.png' />
<b>Listado Cupos a Reasignar</b>
</div>

<div class='sub-content2' style='height:350px;overflow:auto;' id='lista_citaciones' >

<table style='width:100%;font-size:10px;'>

<tr class='tabla_header'> 
		<td>&nbsp;</td>
		<td>Fecha</td>
		<td>Especialidad</td>
		<td>Profesional</td>
		<td>R.U.N.</td>
		<td>Ficha</td>
		<td>Paciente</td>
		<td>Motivo</td>
		<td>Gestionar</td>
</tr>

<?php 

	if($c)
	for($i=0;$i<sizeof($c);$i++) {
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'
		>
		<td style='text-align:right;font-size:18px;'><i>".($i+1)."</td>
		<td style='text-align:center;font-size:14px;'>".substr($c[$i]['nom_fecha'],0,10)."<br />".substr($c[$i]['nomd_hora'],0,5)."</td>
		");
		
		print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
		
		print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
		
		print("<td style='text-align:right;'>".$c[$i]['pac_rut']."</td>");
		print("<td style='text-align:center;'>".$c[$i]['pac_ficha']."</td>");
		print("<td style='text-align:left;'>".$c[$i]['pac_nombres']." ".$c[$i]['pac_appat']." ".$c[$i]['pac_apmat']."</td>");

		print("<td style='text-align:left;'>".$c[$i]['cancela_desc']."</td>");	

		print("<td><center>

		        <img src='iconos/date_magnify.png'  style='cursor:pointer;'
                alt='Citaciones Similares' title='Citaciones Similares' onClick='buscar_citacion(".$c[$i]['nomd_id'].");' />

                <img src='iconos/phone.png'  style='cursor:pointer;'
                alt='Gestiones Citaci&oacute;n' title='Gestiones Citaci&oacute;n' onClick='gestiones_citacion(".$c[$i]['nomd_id'].");' />

		</center>
		</td>");
		
		
		print("</tr>");
	
	}

?>

</table>

</div>

</div>

</center>
