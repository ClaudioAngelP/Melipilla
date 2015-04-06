<?php

  require_once('../../conectar_db.php');

  $bodegashtml = @desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(61).') OR bod_id IN ('._cav(62).')', 'ORDER BY bod_glosa'); 
  
  $servs="'".(@str_replace(',','\',\'',_cav2(61)))."'";
  $servs2="'".(@str_replace(',','\',\'',_cav2(62)))."'";

	$servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica AND 
          (centro_ruta IN (".$servs.") OR centro_ruta IN (".$servs2.")) 
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 

?>

<script>

  listar_solicitudes=function() {
  
    var myAjax = new Ajax.Updater(
    'lista_solicitudes',
    'abastecimiento/revision_solicitudes/listar_solicitudes.php',
    {
      method:'get',
      parameters:$('filtro').serialize()
    });
  
  }
  
  abrir_solicitud = function(sol_id) {

  l=(screen.availWidth/2)-250;
  t=(screen.availHeight/2)-200;
  
  win = window.open('visualizar.php?sol_id='+sol_id, 'ver_solicitud',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=500, height=415');
                    
  win.focus();

  }

  
  listar_solicitudes();

</script>

<center>
<form id='filtro' name='filtro' onSumbit='return false;'>

<div class='sub-content' style='width:650px;'>
<div class='sub-content'>
<img src='iconos/basket.png'>
<b>Revisi&oacute;n de Solicitudes de Compra</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='text-align:right;'>Ubicaci&oacute;n:</td>
<td>
<select id='bodega_id' name='bodega_id'>
<?php echo $bodegashtml; ?>
<?php echo $servicioshtml; ?>
</select>

</td>
</tr>
</table>
</div>

<div class='sub-content2' id='lista_solicitudes'
style='height:350px;overflow:auto;'>


</div>


</div>

</form>

</center>