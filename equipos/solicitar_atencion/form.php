<?php

  require_once("../../conectar_db.php");
  
  $equipo_id=$_GET['equipo_id'];

  function desplegar_checkbox_sql($sql, $_permiso_id, $name,
                            $inittag='', $endtag='') {
  
    GLOBAL $conn;
    GLOBAL $accesos_func;
  
    $opciones = pg_query($conn, $sql);
    
	$html='<table>';
    
    for($i=0;$i<pg_num_rows($opciones);$i++) {
    
      $opcion = pg_fetch_row($opciones);
      
      if(_ca($accesos_func, $_permiso_id, $opcion[0])) {
        $checked='CHECKED';
      } else {
        $checked='';
      }
      
      $html.='
      <tr><td>
      <input type="checkbox" 
      id="'.$name.'_'.$opcion[0].'" 
      name="'.$name.'_'.$opcion[0].'"
      value="'.$opcion[0].'" '.$checked.'>
      </td><td> 
      '.$inittag.''.htmlentities($opcion[1]).''.$endtag.'
      </td></tr>
      ';
    
    }
    
    $html.='</table>';
    
    return $html;
  
  }



  list($equipo)=cargar_registros_obj("
    SELECT * FROM equipos_medicos
    JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
    WHERE equipo_id=$equipo_id
  ", true);
  
  $tipohtml = desplegar_opciones_sql("
    SELECT eottipo_id, eottipo_desc FROM eot_tipo; 
  ");

  $estadohtml = desplegar_checkbox_sql("
    SELECT eoteseq_id, eoteseq_desc FROM eot_estado_equipo; 
  ", '', 'estado_');

  if(trim($equipo['equipo_accesorios'])!='') {

    $a=explode(',', trim($equipo['equipo_accesorios']));
  

    $ahtml='';
    
    for($i=0;$i<count($a);$i++) {
      $ahtml.='<input type="checkbox" id="a_'.$i.'" name="a_'.$i.'" 
                  value="'.trim($a[$i]).'">'.trim($a[$i]).'<br />';
    }

  } else {

    $ahtml='<i>No posee accesorios asociados.</i>';

  }
  
?>

<html>
<title>Solicitar Atenci&oacute;n para Equipo M&eacute;dico</title>

<?php cabecera_popup('../..'); ?>

<script>

ingresar_solicitud = function() {

  var myAjax=new Ajax.Request(
  'sql.php',
  {
    method:'post',
    parameters: $('solicitud').serialize(),
    onComplete: function(resp) {
    
      eot_id=(resp.responseText*1);
      
      alert("Solicitud ingresada exitosamente.".unescapeHTML());
      
      window.open("../visualizar_eot.php?eot_id="+eot_id, "_self");
    
    }
  });

}


</script>

<body class="fuente_por_defecto popup_background">

<form id='solicitud' name='solicitud' onsubmit='return false;'>

<div class="sub-content">
<img src="../../iconos/layout.png"> 
<b>Solicitud de Atenci&oacute;n a U.E.M.</b>
</div>

<div class="sub-content">
<table style="width:100%;">
<tr><td colspan=2 style="font-weight:bold;text-align:center;font-size:14px;">Datos del Equipo Electrom&eacute;dico</td></tr>
<tr>
<td style="text-align:right;">Clasificaci&oacute;n:</td>
<td><b><?php echo htmlentities($equipo['eclase_nombre']); ?></b></td>
</tr>
<tr>
<td style="text-align:right;">Marca/Modelo:</td>
<td><b><?php echo htmlentities($equipo['equipo_marca'].' '.$equipo['equipo_modelo']); ?></b></td>
</tr>
<tr>
<td style="text-align:right;">Nro. de Serie:</td>
<td><?php echo $equipo['equipo_serie']; ?></td>
</tr>
<tr>
<td style="text-align:right;">Nro. de Inventario:</td>
<td><?php echo $equipo['equipo_inventario']; ?></td>
</tr>
</table>
</div>

<input type="hidden" id="equipo_id" name="equipo_id" value="<?php echo $equipo_id; ?>">

<div class="sub-content">
<img src="../../iconos/pencil.png"> 
<b>Datos Generales</b>
</div>

<div class="sub-content2">
<table style="width:100%;">

<tr>
<td style="text-align:right;">Tipo Solicitud:</td>
<td>
<select id='eot_tipo' name='eot_tipo'>
<?php echo $tipohtml; ?>
<option value=-1>Otro</option>
</select>
</td>
</tr>

<tr>
<td style="text-align:right;">Fecha Ing. Solicitud:</td>
<td><b><?php echo date('d/m/Y'); ?></b></td>
</tr>
<tr>
<td style="text-align:right;">Hora Ing. Solicitud:</td>
<td><b><?php echo date('h:m'); ?></b></td>
</tr>

<tr>
<td style="text-align:right;" valign='top'>Estado del Equipo M&eacute;dico en General:</td>
<td>
<?php echo $estadohtml; ?>
</td>
</tr>

<tr>
<td style="text-align:right;" valign='top'>Accesorios Inclu&iacute;dos:</td>
<td>
<?php echo $ahtml; ?>
</td>
</tr>


<tr>
<td style="text-align:right;" valign="top">Observaciones:</td>
<td>
<textarea id="observaciones" name="observaciones"
style="width:300px;height:100px;"
></textarea>
</td>
</tr>
</table>
</div>

    <center>
    <div class='boton'>
		<table><tr><td>
		<img src='../../iconos/pencil_go.png'>
		</td><td>
		<a href='#' onClick='ingresar_solicitud();'>
		
    Enviar Solicitud...
    
    </a>
		</td></tr></table>
		</div>
    </center>

</form>

</body>
</html>


