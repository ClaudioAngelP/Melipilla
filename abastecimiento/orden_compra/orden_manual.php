<?php
    require_once('../../conectar_db.php');
?>

<html>
    <title>Ingreso Manual de &Oacute;rdenes de Compra</title>
    <?php cabecera_popup('../..');  ?>
    <script>

  articulos=new Array();
  
  bloquear_ingreso=false;

  buscar_pedidos=function() {
  
    l=screen.availWidth/2-225;
    t=screen.availHeight/2-175;
  
    var win = window.open('listar_pedidos.php','lista_pedidos',
                                'scrollbar=no, toolbar=no, width=550, '+
                                'height=350,left='+l+',top='+t);
                                
    win.focus();
                                
  }
  
  usar_pedido = function(pedido_datos, pedido_detalle) {
  
    articulos=new Array();
  
    $('pedido_id').value=pedido_datos[0].pedido_id;
    $('pedido_numero').value=pedido_datos[0].pedido_nro;
    
    for(i=0;i < pedido_detalle.length;i++) {
      if(pedido_detalle[i].pedidod_estado=='f')
        agregar_art_auto(pedido_detalle[i]);
        
    }
  
    redibujar_tabla();
    
    recalcular_valores();

  }
  
  agregar_art_auto = function(articulo) {
    
    var num=articulos.length;
    articulos[num]=new Object();
    articulos[num].id=articulo.art_id;
    articulos[num].codigo=articulo.art_codigo;
    articulos[num].glosa=articulo.art_glosa;
    articulos[num].item_glosa=articulo.item_glosa;
    articulos[num].item_codigo=articulo.item_codigo;
    articulos[num].cantidad=articulo.pedidod_cant;
    articulos[num].valor=articulo.art_val_ult;
    
  }
  
  redibujar_tabla=function() {
  
    var html='<table style="width:100%;font-size:11px;">';
    
    html+='<tr class="tabla_header" style="font-weight:bold;">';
    html+='<td>C&oacute;digo Int.</td>';
    html+='<td>Glosa</td>';
    html+='<td>Item Presupuestario</td>';
    html+='<td style="width:80px;">Cantidad</td>';
    html+='<td style="width:80px;">P. Unit ($)</td>';
    html+='<td>Subtotal ($)</td>';
    html+='<td>Acciones</td>';
    html+='</tr>';
    
    for(i=0;i < articulos.length;i++) {
      (i%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
    
      if(articulos[i].id==0) cod='<i>(n/a)</i>';
      else cod=articulos[i].codigo;
      
      html+='<tr class="'+clase+'">';
      html+='<td style="text-align:right;">'+cod+'</td>';
      html+='<td>'+articulos[i].glosa+'</td>';
      html+='<td>'+articulos[i].item_codigo+' - '+articulos[i].item_glosa+'</td>';
      html+='<td style="text-align:right;">';
      html+='<input type="text" id="cant_'+i+'" name="cant_'+i+'" ';
      html+='style="text-align:right;" size=10 ';
      html+='onKeyUp="recalcular_valores();" ';
      
      html+='value="'+number_format(articulos[i].cantidad,1,',','.')+'" ';
      html+='onFocus="this.select();" /></td>';
      html+='<td style="text-align:right;">';
      html+='<input type="text" id="punit_'+i+'" name="punit_'+i+'" ';
      html+='style="text-align:right;" size=10 ';
      html+='onKeyUp="recalcular_valores();" ';
      html+='value="'+number_format(articulos[i].valor,1,',','.')+'" ';
      html+='onFocus="this.select();" /></td>';
      html+='<td style="text-align:right;" id="subt_'+i+'">';
      html+='$'+number_format((articulos[i].cantidad*articulos[i].valor),0)+'.-</td>';
      html+='<td><center><img src="../../iconos/delete.png" ';
      html+='onClick="quitar('+i+');" style="cursor:pointer;"></center></td>';
      html+='</tr>';
    }
    
    html+='<tr class="tabla_header" style="font-weight:bold;">';
    html+='<td colspan=5 style="text-align:right;">Neto:</td>';
    html+='<td id="orden_neto" style="text-align:right;">$0.-</td>';
    html+='<td rowspan=3>&nbsp;</td></tr>';
    html+='<tr class="tabla_header" style="font-weight:bold;">';
    html+='<td colspan=5 style="text-align:right;">I.V.A.:</td>';
    html+='<td id="orden_iva" style="text-align:right;">$0.-</td></tr>';
    html+='<tr class="tabla_header" style="font-weight:bold;">';
    html+='<td colspan=5 style="text-align:right;">Total:</td>';
    html+='<td id="orden_total" style="text-align:right;">$0.-</td></tr>';
    html+='</table>';
    
    $('orden_detalle').innerHTML=html;
  
  }
  
  recalcular_valores=function() {
    
    var total=0;
    
    for(i=0;i<articulos.length;i++) {

      var cant=$('cant_'+i).value.replace('.','');
      cant=cant.replace(',','.');
      
      var cant2=$('punit_'+i).value.replace('.','');
      cant2=cant2.replace(',','.');
      
      articulos[i].cantidad=(cant*1);
      articulos[i].valor=(cant2*1);

      subtmp=(articulos[i].cantidad*articulos[i].valor);
      $('subt_'+i).innerHTML='$'+number_format(subtmp, 1, ',', '.')+'.-';
      total+=subtmp;

    }
    
    
    if($('ivaincl').checked) {
      neto=total/_global_iva;
      iva=total-neto;
    } else {
      neto=total;
      total=neto*_global_iva;
      iva=total-neto;
    }
    
    $('orden_neto').innerHTML='$'+number_format(neto, 1, ',', '.')+'.-';
    $('orden_iva').innerHTML='$'+number_format(iva, 1, ',', '.')+'.-';
    $('orden_total').innerHTML='$'+number_format(total, 1, ',', '.')+'.-';
    
    
  }

  mostrar_proveedor=function(datos) {
    $('orden_prov_id').value=datos[3];
    $('orden_prov_nombre').value=datos[2].unescapeHTML();
  }
  
  liberar_proveedor=function() {
    $('orden_prov_id').value=0;
    $('orden_prov_nombre').value='';
    $('orden_prov_rut').value='';
    $('orden_prov_rut').focus();
  }
  
  limpiar_orden=function() {
    $('orden_numero').value='';
    liberar_proveedor();
    $('pedido_numero').value='';
    articulos=new Array();
    redibujar_tabla();
    recalcular_valores();
    $('orden_numero').focus();
  }
  
  guardar_orden=function() {
  
    if(bloquear_ingreso) return;
  
    /*if(trim($('orden_numero').value)=='') {
      alert('Debe ingresar un n&uacute;mero de &oacute;rden de compra.'.unescapeHTML());
      return;
    }*/
    
    if($('orden_prov_id').value==0) {
      alert('Debe ingresar un proveedor para la &oacute;rden de compra.'.unescapeHTML());
      return;
    }

    if($('pedido_id').value==0 && !$('nopedido').checked) {
      alert('Debe asociar un pedido a la &oacute;rden de compra.'.unescapeHTML());
      return;
    }
    
    if(articulos.length==0) {
      alert('Debe asociar art&iacute;culos a la &oacute;rden de compra.'.unescapeHTML());
      return;
    }
    
    var detalle_arts=encodeURIComponent(articulos.toJSON());
    
    bloquear_ingreso=true;
    
    params=$('cabecera').serialize()+'&'+$('ivaincl').serialize()+'&det='+detalle_arts;
    
    var myAjax=new Ajax.Request(
    'sql.php',
    {
      method: 'post',
      parameters: params,
      onComplete: function(resp) {
      
        try {
        
        var datos=resp.responseText.evalJSON(true);
      
        if(datos[0]) {
        
          alert('&Oacute;rden de Compra ingresada exitosamente.'.unescapeHTML());
          window.opener.listar_ordenes();
          window.close();
        
        } else {
        
          alert('ERROR:\n\n'+resp.responseText);
        
        }
        
        } catch(err) {
        
          alert('ERROR:\n\n'+resp.responseText);
        
        }
        
        bloquear_ingreso=false;
        
      }
    }
    );

  
  }
  
  orden_pedido = function() {
  
    var x=$('nopedido').checked;
    
    if(x) {
      $('pedido_numero').value='';
      $('pedido_numero').disabled=true;
      $('buscar_pedido').style.display='none';
      $('botones_agregar').style.display='';
      $('div_agregar_articulos').style.display='';
      $('div_agregar_servicios').style.display='none';
      $('orden_detalle').style.height='190px';
      articulos=new Array();
    } else {
      $('pedido_numero').value='';
      $('pedido_numero').disabled=false;
      $('buscar_pedido').style.display='';
      $('botones_agregar').style.display='none';
      $('div_agregar_articulos').style.display='none';
      $('div_agregar_servicios').style.display='none';
      $('orden_detalle').style.height='235px';
      articulos=new Array();
    }
    
    redibujar_tabla();
  
  }
  
    seleccionar_articulo = function (art) {
    
      $('art_nombre').innerHTML=art[2];
      $('art_id').value=art[5];
      $('art_codigo').value=art[0];
      $('item_codigo').value=art[6];
      $('item_glosa').value=art[7];
  
      $('btn_agregar').focus();
      
		}
		
		seleccionar_item = function (item) {
		
		  $('item_codigo').value=item[0];
      $('item_glosa').value=item[2];
      
      $('btn_agregar2').focus();
      
    }

    agregar_art=function()
    {
        if ($('codigo').value!='')
        {
            // Si el articulo ya fué ingresado se retira.
            for(var i=0;i<articulos.length;i++)
            {
                if(articulos[i].id==$('art_id').value) return;
            }
            num = articulos.length;
            articulos[num] = new Object();
            articulos[num].id=$('art_id').value;
            articulos[num].codigo=$('art_codigo').value;
            articulos[num].glosa=$('art_nombre').innerHTML;
            articulos[num].item_codigo=$('item_codigo').value;
            articulos[num].item_glosa=$('item_glosa').value;
            articulos[num].cantidad=0;
            articulos[num].valor=0;
            redibujar_tabla();
            $('codigo').value='';
            $('codigo').select();
            $('codigo').focus();
        }
        else
        {
            alert('Debe seleccionar un artículo para continuar');
            $('codigo').value='';
            $('codigo').select();
            $('codigo').focus();

        }
    }
    

    agregar_serv=function ()
    {
        if(($('glosa_serv').value!='')&&($('item_serv').value!='')&&($('item_glosa').value!=''))
        {
            num = articulos.length;
            articulos[num] = new Object();
            articulos[num].id=0;
            articulos[num].codigo=0;
            articulos[num].glosa=$('glosa_serv').value;
            articulos[num].item_codigo=$('item_codigo').value;
            articulos[num].item_glosa=$('item_glosa').value;
            articulos[num].cantidad=0;
            articulos[num].valor=0;
            redibujar_tabla();
            $('glosa_serv').value='';
            $('item_glosa').value='';
            $('item_serv').value='';
            $('item_codigo').value='';
            $('glosa_serv').select();
            $('glosa_serv').focus();
        }
           else
        {
            alert('Debe seleccionar descripción del servicio o un ítem presupuestario valido para continuar');
            $('glosa_serv').value='';
            $('item_serv').value='';
            $('item_glosa').value='';
            $('item_codigo').value='';
            $('glosa_serv').select();
            $('glosa_serv').focus();
        }
    }
    
    quitar=function(num) {
    
      articulos=articulos.without(articulos[num]);
      
      redibujar_tabla();
    
    }
    
    agrega_arts=function() {
    
      $('div_agregar_articulos').style.display='';
      $('div_agregar_servicios').style.display='none';
      $('codigo').value='';
      $('art_nombre').value='';
      $('codigo').focus();
    
    }
    
    agrega_servs=function() {

      $('div_agregar_articulos').style.display='none';
      $('div_agregar_servicios').style.display='';
      $('glosa_serv').value='';
      $('item_serv').value='';
      $('glosa_serv').focus();
    
    }

</script>

<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<b>Datos Generales</b>
</div>

<div class='sub-content'>

<form id='cabecera' name='cabecera' onSubmit='return false;'>

<table style='width:100%;'>
<tr>
<td style="text-align:right;">Nro. de &Oacute;rden:</td>
<td><input type='text' id='orden_numero' name='orden_numero' size=25></td>
</tr>

<tr>
<td style="text-align:right;">Proveedor:</td>
<td>
<input type='hidden' id='orden_prov_id' name='orden_prov_id' size=10>
<input type='text' id='orden_prov_rut' name='orden_prov_rut'
 onDblClick='liberar_proveedor();'  size=15>
<input type='text' id='orden_prov_nombre' name='orden_prov_nombre' 
size=45 DISABLED>
</td>
</tr>

<tr>
<td style="text-align:right;">Nro. de Pedido:</td>
<td>
<input type='hidden' id='pedido_id' name='pedido_id' value=0>
<input type='text' style="text-align:right;" 
id='pedido_numero' name='pedido_numero' size=10>
<img src='../../iconos/zoom_in.png' onClick='buscar_pedidos();' 
id='buscar_pedido'
style='cursor:pointer;'>
<input type='checkbox' id='nopedido' name='nopedido'
onClick='orden_pedido();'> Sin Pedido Asociado.

</td>
</tr>

<tr>
<td style="text-align:right;" valign='top'>Observaciones:</td>
<td><textarea id='orden_observacion' name='orden_observacion' 
cols=50 rows=2></textarea>
</td>
</tr>


</table>

</form>

</div>


<div class='sub-content'>
<table style='width:100%;'><tr><td>
<b>Detalle ( 
<input type='checkbox' id='ivaincl' name='ivaincl' checked
onClick='recalcular_valores();' value=1>
Valores con I.V.A. Inclu&iacute;do )</b>
</td><td style='text-align:right;'>
<span id='botones_agregar' style='display:none;'>
<span onClick='agrega_arts();' 
style='cursor:pointer;color:#6666FF;'>Agregar Art&iacute;culo</span> - 
<span onClick='agrega_servs();' 
style='cursor:pointer;color:#6666FF;'>Agregar Servicio</span>
</span>
</td></tr></table>
</div>
<div class='sub-content' id='div_agregar_articulos' style='display:none;'>
    <table style='width:100%;'>
        <tr>
            <td style='text-align:right;'>
                C&oacute;digo Int.:
            </td>
            <td>
                <input type='hidden' id='art_id' name='art_id'>
                <input type='hidden' id='art_codigo' name='art_codigo'>
                <input type='hidden' id='item_codigo' name='item_codigo'>
                <input type='hidden' id='item_glosa' name='item_glosa'>
                <input type='text' id='codigo' name='codigo'>
            </td>
            <td style='width:45%;' id='art_nombre'>
            </td>
            <td>
                <input type='button' value='Agregar...' id='btn_agregar'
                onKeyUp='agregar_art();' onMouseUp='agregar_art();'>
            </td>
        </tr>
    </table>
</div>
<div class='sub-content' id='div_agregar_servicios' style='display:none;'>
    <table style='width:100%;'>
        <tr>
            <td style='text-align:right;'>
                Descripci&oacute;n:
            </td>
            <td>
                <input type='text' id='glosa_serv' name='glosa_serv' size=45>
            </td>
            <td>
                <input type='text' id='item_serv' name='item_serv' size=25>
            </td>
            <td>
                <input type='button' value='Agregar...' id='btn_agregar2'
                onKeyUp='agregar_serv();' onMouseUp='agregar_serv();'>
            </td>
        </tr>
    </table>
</div>

<div class='sub-content2' id='orden_detalle'
style="height:235px;overflow:auto;">


</div>

<center>
<table>
<tr>
<td>
<div class='boton'>
<table><tr><td><img src='../../iconos/keyboard_add.png'></td>
<td><a href='#' onClick='guardar_orden();'>Guardar &Oacute;rden de Compra...</a></td></tr></table>
</div>

</td>
<td>
<div class='boton'>
<table><tr><td><img src='../../iconos/folder_page_white.png'></td>
<td><a href='#' onClick='limpiar_orden();'>Limpiar &Oacute;rden de Compra...</a></td></tr></table>
</div>

</td>
</tr>
</table>
</center>

</body>

<script>


  autocompletar_proveedores=new AutoComplete(
    'orden_prov_rut', 
    '../../autocompletar_sql.php',
    function() {
      if($('orden_prov_rut').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('orden_prov_rut').value)
      }
    }, 'autocomplete', 350, 200, 250, 1, 2, mostrar_proveedor);
    
    autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      '../../autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts&'+$('codigo').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_articulo);

    autocompletar_items = new AutoComplete(
      'item_serv', 
      '../../autocompletar_sql.php',
      function() {
        if($('item_serv').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_items&cadena='+encodeURIComponent($('item_serv').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_item);

    
    redibujar_tabla();


</script>


</html>




