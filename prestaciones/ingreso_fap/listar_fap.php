<?php
  require_once('../../conectar_db.php');
  function img($t) {    if($t=='t') return '<center><img src="iconos/tick.png"                                   width=8 height=8></center>';    else return '<center><img src="iconos/cross.png"                                   width=8 height=8></center>';  }
  $fecha = pg_escape_string($_POST['fecha1']);  $tipo = ($_POST['tipo']*1);
  $filtro= pg_escape_string(utf8_decode(trim($_POST['filtro'])));
  $selpab= pg_escape_string(utf8_decode(trim($_POST['selpab'])));  
  switch($tipo) {

		case 1: $tipo_w='(fap_tipo_atencion=1)'; break;
		case 2: $tipo_w='(fap_tipo_atencion=2)'; break;
		case 3: $tipo_w='(fap_tipo_atencion=3 OR fap_tipo_atencion=4)'; break;
		case 5: $tipo_w='(fap_tipo_atencion=5)'; break;
  	
  }
  
	if($filtro=='') {
		$filtro_w='true';	
	} else {
		$filtro_w="((pac_appat || ' ' || pac_apmat || ' ' || 
					   pac_nombres || ' ' || pac_rut || ' ' || pac_ficha) ILIKE '%$filtro%')";
	}				  
	if($selpab=='') {		$pab_w='true';		} elseif($selpab!='10') {		$pab_w="fap_numpabellon=".$selpab;	} else {		$pab_w="(fap_numpabellon=10 OR fap_pab_hora7 IS NOT NULL)";	}		
  
  if($tipo!=5) {
  	
  $lista = cargar_registros_obj("
	  	  SELECT 
	  			fap.*, 
			pacientes.pac_appat, 
			pacientes.pac_apmat, 
			pacientes.pac_nombres, 
			pacientes.pac_id, 
			pacientes.pac_rut, 
			pacientes.pac_ficha
			
	  FROM fap	  JOIN pacientes ON fap_pac_id=pac_id	  WHERE fap_fecha::date='$fecha' AND $tipo_w AND $filtro_w	  ORDER BY fap_ftipo, fap_fnumero
	    ", true);
  
  } else {

  $lista = cargar_registros_obj("
	  	  SELECT 
	  			fap_pabellon.*, 
			pacientes.pac_appat, 
			pacientes.pac_apmat, 
			pacientes.pac_nombres, 
			pacientes.pac_id, 
			pacientes.pac_rut, 
			pacientes.pac_ficha,
			fappab_pabellones.*, 
			funcionario.*,
			date_trunc('second',fap_fecha)::time AS fap_hora
			
	  FROM fap_pabellon	  JOIN pacientes USING (pac_id)	  LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id		
	  LEFT JOIN funcionario ON funcionario.func_id=fap_pabellon.func_id		
	  WHERE fap_fecha::date='$fecha' AND $filtro_w AND $pab_w	  ORDER BY fap_fnumero DESC
	    ", true);
  	
  }
?>
<table style='width:100%;' class='lista_small'>
<tr class='tabla_header'><td>N&uacute;mero</td>
<?php if($tipo==5) { ?> <td>Hora</td> <td>Pabell&oacute;n</td> <?php } ?>
<td>Nro. Ficha</td>

<?php if($tipo!=5) { ?>
<td>Paterno</td><td>Materno</td><td>Nombres</td>

<?php } else { ?>
<td>Nombre</td>
<?php } ?>

<?php if($tipo==5) { ?> <td>Funcionario</td> <?php } ?><td>Hoja Intervenci&oacute;n</td><td>Protocolo Qx</td><td>Escalas</td><td>Pausa de Seguridad</td><td>Hoja Cargo</td><td>Adjuntos</td><td>Terminado</td><?php if(_cax(210)){ ?>
<td>Eliminar</td><?php } ?>
<?php if($tipo==5) { ?> <td>Imprimir</td> <?php } ?>
</tr>
<?php 
  if($lista)  for($i=0;$i<count($lista);$i++) {
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';    	if($lista[$i]['fap_suspension']!='') $color='color:red;'; else $color='color:black;';	
    print("    <tr class='$clase'    onMouseOver='this.className=\"mouse_over\";'    onMouseOut='this.className=\"".$clase."\";'>    <td style='text-align:center;font-weight:bold;'>".$lista[$i]['fap_fnumero']."</td>
    ");
    
	 if($tipo==5) {
	    print("	    	<td style='text-align:center;$color'>".$lista[$i]['fap_hora']."</td>
	    ");
	    print("	    	<td style='text-align:center;font-size:9px;$color'>".$lista[$i]['fapp_desc']."</td>
	    ");
	 }    
    
    print("    <td style='text-align:center;$color'>".$lista[$i]['pac_ficha']."</td>
    ");
    
    if($tipo!=5)
	    print("	    <td>".(($lista[$i]['pac_appat']))."</td>	    <td>".(($lista[$i]['pac_apmat']))."</td>	    <td>".(($lista[$i]['pac_nombres']))."</td>
	    ");
    else 
	    print("	    <td style='$color'>".trim((($lista[$i]['pac_appat']))."	    ".(($lista[$i]['pac_apmat']))."	    ".(($lista[$i]['pac_nombres'])))."</td>
	    ");    
    
	if($tipo==5) {
    print("    	<td style='text-align:left;font-size:8px;style='$color''>".$lista[$i]['func_nombre']."</td>
    ");		
	}    
    
    if(_cax(208)) {
    print("<td>    <center>    <img src='iconos/pencil.png' style='cursor:pointer;'    onClick='abrir_fap(".$lista[$i]['fap_id'].", $i);'>    </center>    </td>	    <td><center>        <img src='iconos/layout.png' style='cursor:pointer;'    onClick='protocolo_fap(".$lista[$i]['fap_id'].", $i);'>    </center></td>    <td><center>        <img src='iconos/chart_line.png' style='cursor:pointer;'    onClick='escalas_fap(".$lista[$i]['fap_id'].", $i);'>    </center></td>    <td><center>        <img src='iconos/table_edit.png' style='cursor:pointer;'    onClick='checklist_fap(".$lista[$i]['fap_id'].", $i);'>    </center></td>	    <td><center>        <img src='iconos/script_edit.png' style='cursor:pointer;'    onClick='cargos_fap(".$lista[$i]['fap_id'].", $i);'>    </center></td>		<td><center>        <img src='iconos/photos.png' style='cursor:pointer;'    onClick='adjuntos_fap(".$lista[$i]['fap_id'].", $i);'>    </center></td>		");		if($lista[$i]['fap_terminado']=='t')    {        print("<td><center>        <img src='iconos/tick.png' style=''    onClick=''>        </center></td>");    }    else    {        print("<td><center>&nbsp;</center></td>");	    }		if(_cax(210)){	print("<td>    <center>    <img src='iconos/delete.png' style='cursor:pointer;'    onClick='quitar_fap(".$lista[$i]['fap_id'].");'>    </center>    </td>    </td>");	}	
    } elseif($lista[$i]['func_id']==$_SESSION['sgh_usuario_id'] AND $lista[$i]['func_id2']==0) {
    print("<td>    <center>    <img src='iconos/pencil.png' style='cursor:pointer;'    onClick='reabrir_fap(".$lista[$i]['fap_id'].");'>    </center>    </td>");    	
    } elseif($tipo<5) { 
    print("<td>    <center>    <img src='iconos/pencil.png' style='cursor:pointer;'    onClick='abrir_fap(".$lista[$i]['fap_id'].", $i);'>    </center>    </td>");		if($lista[$i]['fap_terminado']=='t')    {        print("<td><center>        <img src='iconos/tick.png' style=''    onClick=''>        </center></td>");    }    else    {        print("<td><center>&nbsp;</center></td>");	    }		print("<td>    <center>    <img src='iconos/delete.png' style='cursor:pointer;'    onClick='quitar_fap(".$lista[$i]['fap_id'].");'>    </center>    </td>    </td>");	
    } else {
    	print("<td><center>&nbsp;</center></td>");	
		}		    
    if($tipo==5) {
    print("
    <td>    <center>    <img src='iconos/printer.png' style='cursor:pointer;'    onClick='comprueba_pausa(".$lista[$i]['fap_id'].",0,1,$i);'>    </center>    </td>    
    ");	
    }
    
	 print("
    </tr>    ");
  }
?>
</table>


<script>
	
	datos_fap=<?php echo json_encode($lista); ?>;
	
	if(datos_fap)
   	$('ver_refs').innerHTML='Total de Registros: <b>'+datos_fap.length+'</b>';
   else
   	$('ver_refs').innerHTML='<i>No hay registros.</i>';

</script>