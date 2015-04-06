<?php  require_once("../../conectar_db.php");
  
  $portal=cargar_registros_obj("SELECT DISTINCT orden_estado_portal FROM orden_compra WHERE orden_estado_portal!='' AND orden_estado_portal IS NOT NULL ORDER BY orden_estado_portal");
 
 //print_r($portal);
 
   $compradores = cargar_registros_obj("
		SELECT distinct func_id, UPPER(func_nombre) AS func_nombre, orden_mail FROM orden_compra
		JOIN funcionario ON orden_mail=func_email
		ORDER BY func_nombre"
  );
  
  $compradoreshtml = '<option value="-1">(Todos)...</option>';
  
  for($i=0;$i<sizeof($compradores);$i++) {
		$compradoreshtml .= '<option value="'.$compradores[$i]['orden_mail'].'">'.utf8_encode($compradores[$i]['func_nombre']).'</option>';
	  }
	  
	$compradoreshtml .= '<option value="-2">ADMINISTRACION INFORMATICA</option>';
	  
	$items = cargar_registros_obj("
	SELECT distinct item_codigo, UPPER(item_glosa) AS item_glosa FROM orden_compra
	JOIN item_presupuestario ON orden_centro_costo=item_codigo
	ORDER BY item_codigo"
  );
  
  $itemshtml = '<option value="-1">(Todos)...</option>';
  
  for($i=0;$i<sizeof($items);$i++) {
		$itemshtml .= '<option value="'.$items[$i]['item_codigo'].'">['.$items[$i]['item_codigo'].'] '.utf8_encode($items[$i]['item_glosa']).'</option>';
	  }
 
 $portalhtml="<table width=100%;><tr>";
 $f=0;
 for($i=0;$i<sizeof($portal);$i++){
	 
	 $portalhtml.="
		<td style='text-align:left;'>
			<input type='checkbox' id='chk_".$i."' name='chk_".$i."' value='".$portal[$i]['orden_estado_portal']."' onChange='listar_ordenes(1);' >&nbsp;".htmlentities($portal[$i]['orden_estado_portal'])."
		</td>";
	 
	 if($f==2){ 
		 $portalhtml.="</tr><tr>";
	 }
	 $f++;
	 if($f>2) $f=0;
 }
 
 $portalhtml.="</tr></table>";
  
  
?>
<script>

	xls_busqueda = function() {
		
    	var __ventana = window.open('abastecimiento/orden_compra/listar_ordenes.php?xls&'+$("estado").serialize()+'&'+$("ordenes_propias").serialize()+'&'+$("rutproveedor").serialize()+'&'+$("filtrar_ordenes").serialize()+'&'+$("orden_fecha1").serialize()+'&'+$("orden_fecha2").serialize()+'&'+$("filtrar_comprador").serialize()+'&'+$("filtrar_item").serialize(), '_self');
  }
  
  agregar_orden_xml = function()
{
    l=(screen.availWidth/2)-300;
    t=(screen.availHeight/2)-75;
    win = window.open('abastecimiento/orden_compra/carga_xml.php', '',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=600, height=150');
    win.focus();
}

agregar_orden_manual = function()
{
    l=(screen.availWidth/2)-375;
    t=(screen.availHeight/2)-275;
    win = window.open('abastecimiento/orden_compra/orden_manual.php', '',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=750, height=550');
    win.focus();
}

modificar_orden = function()
{
    l=(screen.availWidth/2)-375;
    t=(screen.availHeight/2)-275;
    win = window.open('abastecimiento/ajustar_orden_compra/form1.php', '',
                    'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=yes, width=795, height=550');
    win.focus();
}





listar_ordenes = function(pag)
{		
    var params=$('estado').serialize()+'&';
    params+=$('ordenes_propias').serialize()+'&';
    params+=$('estados_p').serialize()+'&';
    params+=$('filtrar_ordenes').serialize()+'&';
    params+=$('orden_fecha1').serialize()+'&';
    params+=$('orden_fecha2').serialize()+'&';
    params+=$('filtrar_comprador').serialize()+'&';
    params+='pag='+pag+'&';
    params+=$('filtrar_item').serialize();
    
    $('ordenes').innerHTML='<br><br><img src="imagenes/ajax-loader3.gif" /><br>Cargando...';
    
    var myAjax = new Ajax.Updater('ordenes','abastecimiento/orden_compra/listar_ordenes.php',{method: 'get',parameters: params});
}

listar_pedidos = function()
{
    var params=$('estado_pedidos').serialize();
    var myAjax = new Ajax.Updater('pedidos','abastecimiento/orden_compra/listado_pedidos.php',{method: 'get',parameters: params});
}

buscar_orden = function()
{
    //var ordencompra = document.getElementById('ordentxt');\
    params=$('ordentxt').serialize()+'&'+$('orden_prov_id').serialize();
    var myAjax = new Ajax.Updater('buscar','abastecimiento/orden_compra/buscar_orden.php',
    {method:'post',parameters: params});
}

limpiar = function()
{
    html='';
    $('orden_prov_id').value='';
    $('rutproveedor').value=''
    $("buscar").innerHTML=html;

}

limpiar2 = function()
{
    html='';
    $('orden_prov_id').value='';
    $('ordentxt').value='';
    $("buscar").innerHTML=html;

}

limpiar3 = function()
{
    html='';
    $('orden_prov_id_1').value='';
    $("buscar").innerHTML=html;
}



ver_pedidos = function()
{
    tab_up('tab_pedidos');
    tab_down('tab_ordenes');
    tab_down('tab_buscar');
}



ver_ordenes = function()
{
    tab_down('tab_pedidos');
    tab_up('tab_ordenes');
    tab_down('tab_buscar');
}

buscarorden = function()
{
   tab_down('tab_pedidos');
    tab_down('tab_ordenes');
    tab_up('tab_buscar');
}

descargar_adq=function(nro)
{
    l=(screen.availWidth/2)-450;
    t=(screen.availHeight/2)-275;
    win = window.open('abastecimiento/orden_compra/ver_pedido.php?pedido_nro='+nro, '',
                    'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=900, height=650');
    win.focus();
}

marcar_pedido=function(id)
{
    valchk=$('pedido_tramite_'+id).checked;
    if(valchk) valchk='1'; else valchk='0';
    $('pedido_tramite_'+id).style.display='none';
    $('pedido_marcar_'+id).style.display='';
    checks = $('pedidos').getElementsByTagName('input');
    var x = checks.length;
    for(var i=0;i<x;i++)
    {
        if(checks[i].type=='checkbox')
        {
            checks[i].disabled=true;
        }

    }
    var myAjax = new Ajax.Request('abastecimiento/orden_compra/marcar_pedido.php',
  {
    method: 'post',
    parameters: 'pedido_id='+id+'&val='+valchk,
    onComplete: function()
    {
        listar_pedidos();
    }
  }
  );
}
abrir_solicitud = function(sol_id)
{
    alert('pegasus');
    l=(screen.availWidth/2)-250;
    t=(screen.availHeight/2)-200;
    win = window.open('visualizar.php?sol_id='+sol_id, 'ver_solicitud',
                      'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                      'resizable=no, width=500, height=415');
    win.focus();
}

//*****************************************************************

mostrar_proveedor=function(datos)
{
    $('orden_prov_id').value=datos[3];
    buscar_orden();
}

//*****************************************************************

mostrar_proveedor2=function(datos)
{
    $('orden_prov_id_1').value=datos[3];
    $('glosa_prov_1').value=datos[2];
}

//*****************************************************************

autocompletar_proveedores=new AutoComplete(
    'rutproveedor',
    'autocompletar_sql.php',
    function() {
      if($('rutproveedor').value.length<3) return false;

      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('rutproveedor').value)
      }
    }, 'autocomplete', 350, 200, 250, 1, 2, mostrar_proveedor);


//*****************************************************************

autocompletar_proveedores2=new AutoComplete(
    'rutproveedor_1',
    'autocompletar_sql.php',
    function() {
      if($('rutproveedor_1').value.length<3) return false;

      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('rutproveedor_1').value)
      }
    }, 'autocomplete', 350, 200, 250, 1, 2, mostrar_proveedor2);



//*****************************************************************

mostrar_tabla =function()
{
    $('tr_busqueda1').style.display='none';
    $('tr_busqueda2').style.display='';
    $('tr_botones').style.display='';
    $('orden_prov_id_1').value='';
}



//*****************************************************************

volver =function()
{
    $('tr_busqueda1').style.display='';
    $('tr_busqueda2').style.display='none';
    $('tr_botones').style.display='none';
    $('orden_prov_id_1').value='';
}

//*****************************************************************

generar =function()
{
    //if($('tipoinf').value==0) xls=''; else xls='&xls';
    xls='&xls';   
    params='submit&fecha1='+encodeURIComponent($('fecha1').value)
              +'&fecha2='+encodeURIComponent($('fecha2').value)
              +'&glosa_pro='+encodeURIComponent($('glosa_prov_1').value)
              +'&rut_prov='+encodeURIComponent($('rutproveedor_1').value)
               +'&id_prov='+encodeURIComponent($('orden_prov_id_1').value)+xls;
    
    top=Math.round(screen.height/2)-200;
    left=Math.round(screen.width/2)-300;

    new_win =
    window.open('abastecimiento/orden_compra/ordenesxls.php'+
    '?'+params, 'win_informe',
    'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=no, width=600, height=400, '+
    'top='+top+', left='+left);
    new_win.focus();

}



//*****************************************************************

 Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });

//*****************************************************************
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });

</script>


<center>
<table style="width:950px;">
    <tr>
        <td>
            <div class='sub-content' style='display: none;' id='cargar_ordenes_arch'>
                <div class='sub-content'>
                    <img src='iconos/page_attach.png'>
                    <b>Seleccionar Archivo XML de Chilecompras a Procesar</b>
                </div>

                <div class='sub-content2' style='width: 640px; height: 105px;'>
                    <form enctype="multipart/form-data" id='formulario' name='formulario'
                        action='abastecimiento/orden_compra/chilecompra_xcbl_parser.php'
                        method='POST' target='proceso_xml' onSubmit='$("procesando").style.display="";'>
                        <input type='file' id='archivo' name='archivo'><br><br>
                        <input type='submit' value='Procesar...'>
                    </form>

                </div>
            </div>
            <div class='sub-content' id='procesando' style='display: none;'>
                <center>
                    <img src='imagenes/ajax-loader3.gif'><br>
                    Procesando archivo...
                </center>
            </div>

            <div class='sub-content'>
                <div class='sub-content'>
                    <img src='iconos/page_gear.png'>
                    <b>&Oacute;rdenes de Compra</b>
                </div>
                <center>
                    <table>
                        <tr>
                            <td>
                                <div class='boton'>
                                    <table>
                                        <tr>
                                            <td><img src='iconos/keyboard_add.png'></td>
                                            <td>
                                            <a href='#' onClick='agregar_orden_manual();'>Cargar &oacute;rden manualmente ...</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td>
                                <div class='boton'>
                                    <table>
                                        <tr>
                                            <td><img src='iconos/folder_page_white.png'></td>
                                            <td><a href='#' onClick='agregar_orden_xml();'>Cargar &oacute;rden mercadopublico.cl</a></td>
                                            </tr>
                                    </table>
                                </div>
                            </td>
                            
                            <td>
                                <div class='boton'>
                                    <table>
                                        <tr>
                                            <td><img src='iconos/pencil.png'></td>
                                            <td><a href='#' onClick='modificar_orden();'>Modificar Orden de Compra</a></td>
                                            </tr>
                                    </table>
                                </div>
                            </td>

                        </tr>
                    </table>
                </center>
                <table width=100% cellpadding=0 cellspacing=0>
                        <tr>
                            <td>
                                <table cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td>
                                            <div class='tabs' id='tab_ordenes' style='cursor: default;' onClick='ver_ordenes();'>
                                                <img src='iconos/cog.png'>
                                                &Oacute;rdenes de Compra
                                            </div>
                                        </td>
                                        <td>
                                            <div class='tabs_fade' id='tab_pedidos' style='cursor: pointer;' onClick='ver_pedidos();'>
                                                <img src='iconos/table_refresh.png'>
                                                Pedidos
                                            </div>
                                        </td>
                                        <td>
                                            <div class='tabs_fade' id='tab_buscar' style='cursor: pointer;' onClick='buscarorden();'>
                                                   <img src='iconos/zoom.png'>
                                                   Busqueda:
                                            </div>
                                        </td>
                                        <td>
														<div class='boton'>
															<table><tr><td>
																<img src='iconos/page_excel.png'>
															</td><td>
																<a href='#' onClick='xls_busqueda();'><span id='texto_boton'>Descargar XLS (MS Excel)...</span></a>
															</td></tr></table>
														</div>                                        
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class='tabbed_content' id='tab_ordenes_content'>
                                    <table width=100%;>
                                        <tr>
                                            <td style="text-align:right;">
                                                Estado G.I.S.:
                                            </td>
                                            <td>
                                                <select id="estado" name="estado" onChange='listar_ordenes(1);'>
                                                    <option value=-1>(Todos...)</option>
                                                    <option value=0>Esperando Recepci&oacute;n</option>
                                                    <option value=1>Recepcionada Parcialmente</option>
                                                    <option value=2>Recepcion Completa</option>
                                                    <option value=3>Anulada</option>
                                                </select>
                                            </td>
                                            
                                            <td style="text-align:right;">Filtrar Nombre: </td><td><input type="text" name="filtrar_ordenes" id="filtrar_ordenes" onkeyup="if(event.which==13) listar_ordenes(1);"></td>
                                            
                                        </tr>
                                        
                                        <tr>
											<td style="text-align:right;">Fecha desde:</td><td><input type="text" onkeyup="if(event.which==13) listar_ordenes(1);" style="text-align:center;" onBlur="validacion_fecha(this)" name="orden_fecha1" id="orden_fecha1" size=6> hasta: <input type="text" onkeyup="if(event.which==13) listar_ordenes(1);" style="text-align:center;" onBlur="validacion_fecha(this)" name="orden_fecha2" id="orden_fecha2" size=6></td>
											<td style="text-align:right;">Filtrar Comprador:</td><td><SELECT onChange='listar_ordenes(1);' id="filtrar_comprador" name="filtrar_comprador" style="width:150px;"><?php echo $compradoreshtml; ?></SELECT></td>
                                        </tr>
                                        
                                        <tr>
											<td style="text-align:right;">Filtrar &Iacute;tem Presupuestario: </td><td colspan=2><SELECT style="width:350px;" onChange="listar_ordenes(1);" id="filtrar_item" name="filtrar_item"> <?php echo $itemshtml; ?> </SELECT></td>
				                           <td>
                                                <input type='checkbox' id='ordenes_propias' name='ordenes_propias' value='1' onChange='listar_ordenes(1);' CHECKED>
                                                Ver solo &oacute;rdenes propias.
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td style='text-align:right;'>
                                                Estado Portal:
                                            </td>
                                            <td colspan=3>
                                                <div id='estados_portal' name='estados_portal' class='sub-content2'>
                                                <form id='estados_p' name='estados_p'>
												<?php echo $portalhtml; ?>
												</form>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class='sub-content2' id='ordenes' name='ordenes' style='height:250px;overflow:auto;'>
                                    </div>
                                </div>
                                <div class='tabbed_content' id='tab_pedidos_content' style='display:none;'>
                                    <table>
                                        <tr>
                                            <td>
                                                Estado:
                                            </td>
                                            <td>
                                                <select id="estado_pedidos" name="estado_pedidos" onChange='listar_pedidos();'>
                                                    <option value=-1>(Todos...)</option>
                                                    <option value=0>Pendientes</option>
                                                    <option value=1>En Tr&aacute;mite</option>
                                                </select>
                                            </td>
                                            <td>
                                            <input type='button' name='updatebutton' id='updatebutton' onClick='listar_pedidos();' value='Actualizar Listado...'>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class='sub-content2' id='pedidos' name='pedidos' style='width: 640px; height: 250px; overflow:auto;'>
                                    </div>
                                </div>

                                <div class='tabbed_content' id='tab_buscar_content' style='display:none;'>
                                    <table border="1" style="font-size:11px;width:100%;">
                                        <tr id="tr_busqueda1">
                                            <td>
                                                N&deg; de Orden de Compra:
                                            </td>
                                            <td>
                                                <input type='text' name='ordentxt' id='ordentxt' style='text-align:center;' onkeyup='if(event.which==13)buscar_orden();if(event.which!=13)limpiar();' size=12>
                                                <img src='imagenes/ajax-loader1.gif' style='display:none;' id='cargando_orden'>
                                            </td>
                                            <td>
                                                
                                                    Proveedor:
                                            </td>
                                            <td>
                                                <input type='hidden' id='orden_prov_id' name='orden_prov_id' size=10>
                                                <input type='text' name='rutproveedor' id='rutproveedor' onkeyup='if(event.which!=13)limpiar2();'style='text-align:center;'  size='11'>
                                            </td>
                                            <td>
                                                <table border="0">
                                                    <tr>
                                                        <td>
                                                            <input type='checkbox' id='list_ordenes' name='list_ordenes' value='1' onChange='mostrar_tabla();'>
                                                        </td>
                                                        <td>
                                                            Excel de ordenes de Compra.
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr id="tr_busqueda2" style='display:none;'>
                                            <td>
                                                Rut:
                                            </td>
                                            <td>
                                                <input type='hidden' id='orden_prov_id_1' name='orden_prov_id_1' size=20>
                                                <input type='hidden' id='glosa_prov_1' name='glosa_prov_1' size=20>
                                                <input type='text' name='rutproveedor_1' id='rutproveedor_1' onkeyup='if(event.which!=13)limpiar3();'style='text-align:center;'  size='10'>
                                            </td>
                                            <td>
                                               Fecha inicial
                                            </td>
                                            <td>
                                                <input type='text' name='fecha1' id='fecha1' size=10
                                                style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
                                                <img src='iconos/date_magnify.png' id='fecha1_boton'>
                                            </td>
                                            <td>
                                                Fecha Termino
                                            </td>
                                            <td>
                                                <input type='text' name='fecha2' id='fecha2' size=10
                                                style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
                                                <img src='iconos/date_magnify.png' id='fecha2_boton'>
                                            </td>
                                        </tr>
                                        <tr id="tr_botones" style='display:none;'>
                                            <td colspan=6>
                                                <center>
                                                <input type="button" id="buscar_datos" name="buscar_datos" value="Generar" onclick="generar();">
                                                <input type="button" id="cancelar_busqueda" name="cancelar_busqueda" value="Cancelar" onclick="volver();">
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class='sub-content2' id='buscar' name='buscar' style='width: 640px; height: 250px; overflow:auto;'>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</center>

<script>

listar_ordenes(1);
listar_pedidos();

</script>
