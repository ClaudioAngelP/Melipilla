<?php

  require_once('../../conectar_db.php');
  
  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(23),')', 'ORDER BY bod_glosa');
  
  $fecha1=date('d/m/Y', mktime(0,0,0,date('m'),date('d')-7,date('Y')));
  $fecha2=date('d/m/Y');


?>


<script>

  cargar_recepciones = function() {
  
    var myAjax = new Ajax.Updater(
    'recepciones',
    'abastecimiento/historial_recepcion/listar_recepcion.php',
    {
      method: 'get',
      parameters: $('filtro_pedidos').serialize()
    }
    );
  
  }
  
  abrir_recepcion = function(id_doc) {
  
    visualizador_documentos('Visualizar Recepci&oacute;n', 'doc_id='+encodeURIComponent(id_doc));
  
  }
  
  mostrar_proveedor=function(datos) {
    $('prov_id').value=datos[3];
    $('nombre_prov_id').value=datos[2].unescapeHTML();
    cargar_recepciones();
  }
  
  liberar_proveedor=function() {
    $('prov_id').value=0;
    $('nombre_prov_id').value='';
    $('_prov_id').value='';
    $('_prov_id').focus();
    cargar_recepciones();
  }

  abrir_movimiento= function(id_lg) {
  
    visualizador_documentos('Visualizar Movimiento', 'log_id='+encodeURIComponent(id_log));
  
  }
  
  autocompletar_proveedores = new AutoComplete(
    '_prov_id', 
    'autocompletar_sql.php',
    function() {
      if($('_prov_id').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('_prov_id').value)
      }
    }, 'autocomplete', 350, 200, 250, 1, 2, mostrar_proveedor);


</script>

<center>
<table width=650>
<tr><td>

<div class="sub-content">

<div class="sub-content">
<img src='iconos/layout.png'> <b>Historial de Movimientos</b>
</div>

<form id='filtro_pedidos' onChange='cargar_recepciones();' 
onSubmit='return false;'>

<div class="sub-content">

<table width=100%>

<tr>
<td style='text-align: right;'>Tipo de Movimiento:</td>
<td>
<select name='tipo_mov' id='tipo_mov'>
<option value=1 SELECTED>Recepci&oacute;n</option>
<option value=5>Ingreso por Donaci&oacute;n</option>
<option value=6>Pr&eacute;stamo/Devoluci&oacute;n</option>
<option value=7>Baja por Vencimiento</option>
<option value=8>Baja de Art&iacute;culos</option>
<option value=16>Devoluci&oacute;n desde Servicios</option>
<option value=20>Inicio de Control por Sistema</option>

</select>
</td>
</tr>


<tr>
<td style='text-align: right;'>Ubicaci&oacute;n:</td>
<td>
<select name='bodega_origen' id='bodega_origen'>
<option value='-1'>(Fuera de Bodegas...)</option>
<?php echo $bodegashtml; ?>
</select>
</td>
<td style='text-align: right;'>Fecha Inicial:</td>
<td>
<input type='text' name='fecha1' id='fecha1' size=10
style='text-align: center;' value='<?php echo $fecha1?>'>
<img src='iconos/date_magnify.png' id='fecha1_boton'>

</td>
</tr>
<tr>
<td style='text-align: right;'>Documento:</td>
<td>
<select id='documento' name='documento'>
<option value='-1' SELECTED>(Cualquier Documento...)</option>
<option value='0'>Gu&iacute;a de Despacho</option>
<option value='1'>Factura</option>
<option value='2'>Boleta</option>
<option value='3'>Pedido</option>
</select>

</td>
<td style='text-align: right;'>Fecha Final:</td>
<td>
<input type='text' name='fecha2' id='fecha2' size=10
style='text-align: center;' value='<?php echo $fecha2?>'>
<img src='iconos/date_magnify.png' id='fecha2_boton'>

</td>
</tr>
<tr>
<td style='text-align: right;'>Proveedor:</td>
<td colspan=3>
<input type='hidden' id='prov_id' name='prov_id' value=0>
<input type='text' id='_prov_id' name='_prov_id' onDblClick='liberar_proveedor();' size=15>
<input type='text' id='nombre_prov_id' name='nombre_prov_id' size=45 DISABLED>

</td>
</tr>
<tr>
<td style='text-align: right;'>N&uacute;mero de Documento:</td>
<td>
<input type='text' id='nro' name='nro' size=10>

</td>

<td style='text-align: right;'>Correlativo Recepci&oacute;n:</td>
<td>
<input type='text' id='nro_corr' name='nro_corr' size=10>

</td>
</tr>
</table>

</div>

</form>

<div class="sub-content2" id='recepciones' style="height:270px; overflow:auto;">

</div>

</div>

</td></tr>
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


    cargar_recepciones(); 

</script>
