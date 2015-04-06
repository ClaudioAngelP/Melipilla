<?php

  require_once('../../conectar_db.php');

$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(22),')', 'ORDER BY bod_glosa');

  $servs="'".str_replace(',','\',\'',_cav2(22))."'";
 /* $bodegashtml = desplegar_opciones_sql( 
  "SELECT bod_id, bod_glosa FROM bodega WHERE bod_id IN ("._cav(22).")
  ORDER BY bod_id", NULL, '', "");*/
  
  $servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto AND centro_ruta IN (".$servs.")
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");

  
  //$bodegas2html = desplegar_opciones("bodega", "bod_id, bod_glosa",'-1','1=1', 'ORDER BY bod_glosa'); a
	$bodegas2html = desplegar_opciones("bodega", "bod_id, bod_glosa, bod_proveedores, bod_despacho",'1','bod_id IN ('._cav(65).')','ORDER BY bod_glosa');



  $fecha1=date('d/m/Y', mktime(0,0,0,date('m'),date('d')-7,date('Y')));
  $fecha2=date('d/m/Y');

?>

<script>


	xls_busqueda = function() {

		    	
    	var __ventana = window.open('abastecimiento/historial_pedido/listar_pedidos.php?xls&'+$("bodega_origen").serialize()+'&'+$("bodega_destino").serialize()+
      '&'+$("estado").serialize()+'&'+$("orden").serialize()+'&'+$("fecha1").serialize()+'&'+$("fecha2").serialize()+
      '&'+$("orientacion").serialize()+'&'+$("pds_propios").serialize(), '_self');
  }


  cargar_pedidos = function() {
  
    if($('bodega_origen').value!=-1 && $('bodega_destino').value!=-1 && $('bodega_origen').value==$('bodega_destino').value) {
      alert('Las ubicaciones de or&iacute;gen y destino no pueden ser iguales.'.unescapeHTML());
      return;
    }
    $('listado_pedidos').innerHTML='<center><table><tr><td style="width:530px;height:160px;"><center><img src="imagenes/ajax-loader2.gif"></center></td></tr></table></center>';
    var myAjax = new Ajax.Updater(
    'listado_pedidos',
    'abastecimiento/historial_pedido/listar_pedidos.php',
    {
      method: 'get',
      evalScripts:true,
      parameters: $('filtro_pedidos').serialize()
    }
    );
  
    abrir_pedido1 = function(pedido_numero)
    {
        l=(screen.availWidth/2)-250;
        t=(screen.availHeight/2)-200;
        win = window.open('visualizar.php?pedido_nro='+pedido_numero, 'ver_pedido',
                            'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                            'resizable=no, width=600, height=445');
                            win.focus();
    }
}
  
</script>

<center>
<table width=770>
<tr><td>

<div class='sub-content'>
<div class='sub-content'>
<img src='iconos/page_white_stack.png'> <b>Historial de Pedidos</b>
</div>
<div class='sub-content'>
<form id='filtro_pedidos' name='filtro_pedidos' 
onChange='cargar_pedidos();' onSubmit='return false;'>


<table width=100%>
<tr>
<td style='text-align: right;'>Or&iacute;gen:</td>
<td colspan=2><select name='bodega_origen' id='bodega_origen'>
		<option value=-1 selected>(Cualquier Origen...)</option>
		<?php echo $bodegashtml; ?>
		<?php echo $servicioshtml; ?>
	</select>
</td>
<td style='text-align: right;' colspan=2 >Fecha Inicial:</td>
<td colspan=3>
	<input type='text' name='fecha1' id='fecha1' size=10
		style='text-align: center;' value='<?php echo $fecha1?>'>
		<img src='iconos/date_magnify.png' id='fecha1_boton'>
</td>
</tr>
<tr>
<td style='text-align: right;'>Destino:</td>
<td colspan=2>
	<select name='bodega_destino' id='bodega_destino'>
		<option value=-1 selected>(Cualquier Destino...)</option>
<!--		<option value=0>Abastecimiento</option>-->
		<?php echo $bodegas2html; ?>
	</select>
</td>
<td style='text-align: right;' colspan=2>Fecha Final:</td>
<td colspan=3>
<input type='text' name='fecha2' id='fecha2' size=10
style='text-align: center;' value='<?php echo $fecha2?>'>
<img src='iconos/date_magnify.png' id='fecha2_boton'>
</td>
</tr>

<tr>
<td style='text-align: right;'>Ordenar por:</td>
<td colspan=2>
	<select id='orden' name='orden'>
		<option value=0>Fecha/Hora</option>
		<option value=1>N&uacute;mero de Pedido</option>
	</select>
	<input type='checkbox' id='orientacion' name='orientacion'> Ascendente
</td>
<td style='text-align: right;'>
    <input type='checkbox' id='pds_propios' name='pds_propios' onClick=''>
</td>
<td colspan=3>Mostrar Pedidos Propios</td>
</tr>
<tr>
    <td style='text-align: right;'>Estado:</td>
    <td colspan=2>
        <select id='estado' name='estado'>
            <option value=-1 selected>Todos</option>
            <option value=0>Enviado</option>
            <option value=1>Retornado</option>
            <option value=2>Terminado</option>
            <option value=3>Anulado</option>
            <option value=10>Sin Autorizaci&oacute;n</option>
        </select>&nbsp;
        N&deg; Pedido:&nbsp; <input type='text' name='nro_pedido' id='nro_pedido' size='6'>
    </td>
    <td style='text-align: right;'>N&deg; de Pedidos:</td>
    <td><input type='text' name='total_pds' id='total_pds' disabled size='3' style='font-size:11px;text-align: center;'></td>
    <td><center>
                    		<div class='boton'>
								<table><tr><td>
								<img src='iconos/page_excel.png'>
								</td><td>
								<a href='#' onClick='xls_busqueda();'><span id='texto_boton'>Descargar XLS (MS Excel)...</span></a>
								</td></tr></table>
								</div>
						</center>
</td>  
</tr>
</table>

</form>

</div>
<div class='sub-content2' id='listado_pedidos'
style='overflow: auto; height: 300px;'>

</div>

</div>

</td>

</tr>

</table>
</center>


<script>
  
    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });

  
  cargar_pedidos(); 

</script>


