<?php

  require_once('../../conectar_db.php');
  
  $bod=($_GET['bodega_origen']*1);
  
  $bodega = pg_query($conn, "SELECT * FROM bodega WHERE bod_id=".$bod);
  
  $bod_arr = pg_fetch_row($bodega);
  
?>


<select name='tipo_movimiento' id='tipo_movimiento'
onChange='corrige_campos();'>

    <option value=-1 SELECTED>(Seleccione Movimiento...)</option>
		<option value=0>Despacho a Bodegas</option>

<?php 

if($bod_arr[8]>0)	print('
      <option value=7>Despacho a Servicios</option>
      ');

?>

    <option value=8>Devoluci&oacute;n desde Servicios</option>
		<option value=1>Pr&eacute;stamo/Devoluci&oacute;n</option>
		<option value=4>Canje de Art&iacute;culos</option>
		<option value=5>Ingreso por Donaci&oacute;n</option>
		<option value=6>Ingreso por Inicio del Sistema</option>
		<option value=9>Ingreso por Producci&oacute;n Interna</option>
</select>


<?php

if($bod_arr[8]>0)print('<script>tipo_servicios_desp='.$bod_arr[8].';</script>');

?>

<script> corrige_campos(); </script>
