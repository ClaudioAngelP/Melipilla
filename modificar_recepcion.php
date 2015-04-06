<?php

  require_once('conectar_db.php');

  if(isset($_GET['doc_id'])) {
  
    // Modificación de Pedidos
    
  $doc_id=$_GET['doc_id'];  
    
  list($datos_pedido) = cargar_registros_obj("
  
    SELECT *, COALESCE(orden_numero, doc_orden_desc) AS _orden_numero FROM documento 
    LEFT JOIN proveedor ON doc_prov_id=prov_id
    LEFT JOIN orden_compra ON doc_orden_id=orden_id
    WHERE doc_id=$doc_id
    
  ", true);
  
  $detalle_pedido = cargar_registros_obj("

    SELECT stock_id, art_codigo, art_glosa, art_vence, 
    forma_nombre, stock_cant, stock_vence, stock_subtotal, 0 as tipo
    FROM logs
    JOIN stock ON stock_log_id=log_id
    JOIN articulo ON stock_art_id=art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE log_doc_id=$doc_id

	UNION

    SELECT serv_id AS stock_id, '' AS art_codigo, serv_glosa AS art_glosa, 
    '0' AS art_vence, serv_unidad AS forma_nombre, serv_cant AS stock_cant, 
    null as stock_vence, serv_subtotal AS stock_subtotal, 1 as tipo
    FROM logs
    JOIN servicios ON serv_log_id=log_id
    WHERE log_doc_id=$doc_id

  ", true);

  $detalle_logs = cargar_registros_obj("
  
    SELECT *, date_trunc('second', log_fecha) AS log_fecha FROM logs
    WHERE log_doc_id=$doc_id
    
  ", true);
  
  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",
  $datos_pedido['destino_bod_id'],
  'bod_id IN ('._cav(29),')',
	'ORDER BY bod_glosa'); 
	
	
  
?>

<html>
<title>Modificar Recepci&oacute;n</title>

<?php cabecera_popup('.'); ?>

<script>

  doc_id = <?php echo $doc_id; ?>;
  lista_articulos = <?php echo json_encode($detalle_pedido); ?>;
  bloquear_ingreso=false;

  redibujar_tabla = function() {
  
    var num=lista_articulos.length;
    
    html='<table style="font-size:11px;width:100%;" >';
    html+='<tr class="tabla_header">';
    html+='<td>Cod. Int.</td>';
    html+='<td style="width:50%;">Glosa</td>';
    html+='<td>Cantidad</td>';
    html+='<td>Forma</td>';
    html+='<td>Vencimiento</td>';
    html+='<td style="width:100px;">Subtotal ($ Neto)</td>';
    html+='<td>Acci&oacute;n</td>';
    html+='</tr>';
  
	total=0;
    
    for(var i=0;i<lista_articulos.length;i++) {
    
      if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2'; 
    
      var cantidad=lista_articulos[i].stock_cant+'';
      cantidad=cantidad.replace(',', '.');

      var subtotal=lista_articulos[i].stock_subtotal+'';
      subtotal=subtotal.replace(',', '.');
      
	  total+=subtotal*1;
	  
      html+='<tr class="'+clase+'" id="art_fila_'+i+'">';

      
      html+='<td style="text-align:right;font-weight:bold;">';
      
      if(lista_articulos[i].tipo*1==0) {
		html+=lista_articulos[i].art_codigo+'</td>';
		html+='<td>'+lista_articulos[i].art_glosa+'</td>';
	  } else {
		html+='<i>(n/a)</i></td>';
		html+='<td><input type="text" onKeyUp="actualizar_art('+i+');"';
		html+='id="glosa_'+i+'" name="glosa_'+i+'" ';
		html+='value="'+lista_articulos[i].art_glosa+'" style="width:100%;" /></td>';		  
	  }

      html+='<td><center><input type="text" size=10 ';
      html+='style="text-align:right;" ';
      html+='id="cant_'+i+'" ';
      html+='name="cant_'+i+'" '; 
      html+='value="';
      html+=(cantidad.replace('.', ','))+'" onFocus="this.select()" ';
      html+='onKeyUp="actualizar_art('+i+');">';
      html+='</center></td>';

      html+='<td>'+lista_articulos[i].forma_nombre+'</td>';

	  if(lista_articulos[i].art_vence=='1') {
		  html+='<td><center><input type="text" size=10 ';
		  html+='style="text-align:center;" ';
		  html+='id="vence_'+i+'" ';
		  html+='name="vence_'+i+'" '; 
		  html+='value="';
		  html+=lista_articulos[i].stock_vence+'" onFocus="this.select()" ';
		  html+='onKeyUp="validacion_fecha(this); actualizar_art('+i+');">';
		  html+='</center></td>';
	  } else {
		  html+='<td style="text-align:center;"><i>(No Perecible...)</i></td>';
	  }
      
      html+='<td><center><input type="text" size=10 ';
      html+='style="text-align:right;" ';
      html+='id="subtotal_'+i+'" ';
      html+='name="subtotal_'+i+'" '; 
      html+='value="';
      html+=(subtotal.replace('.', ','))+'" onFocus="this.select()" ';
      html+='onKeyUp="actualizar_art('+i+'); recalcular_total();">';
      html+='</center></td>';
      html+='<td><center><img src="iconos/delete.png" onClick="';
      html+='quitar_art('+i+');" ';
      html+='style="cursor:pointer;"></center></td>';

      html+='</tr>';
    
    }
    
    html+='<tr class="tabla_header"><td colspan=5 style="text-align:right;">Descuento:</td><td style="text-align:right;"><input type="text" id="_doc_descento" name="_doc_descuento" style="font-size:10px;text-align:right;width:100%;" value="'+$('doc_descuento').value+'" onKeyUp="$(\'doc_descuento\').value=this.value*1"></td><td>&nbsp;</td></tr>';
    
    total-=$('doc_descuento').value*1;
    
    html+='<tr class="tabla_header"><td colspan=5 style="text-align:right;">Neto:</td><td style="text-align:right;" id="val_neto" name="val_neto">'+formatoDinero(total)+'</td><td>&nbsp;</td></tr>';
    html+='<tr class="tabla_header"><td colspan=5 style="text-align:right;">IVA:</td><td style="text-align:right;" id="val_iva" name="val_iva">'+formatoDinero((total*$('doc_iva').value)-total)+'</td><td>&nbsp;</td></tr>';
    html+='<tr class="tabla_header"><td colspan=5 style="text-align:right;">Total:</td><td style="text-align:right;" id="val_total" name="val_total">'+formatoDinero(total*$('doc_iva').value)+'</td><td>&nbsp;</td></tr>';
    
    html+='</table>';
  
    $('lista_detalle').innerHTML=html;

    for(var i=0;i<lista_articulos.length;i++) {
		if(lista_articulos[i].art_vence=='1')
			validacion_fecha($('vence_'+i));
	}
  
  }
  
  recalcular_total = function() {
      
      subtotales=0;
      
      for(i=0;i<lista_articulos.length;i++) {
      
        fila = $('art_fila_'+i);
        cols = fila.getElementsByTagName('td');
         //console.log(lista_articulos[i][10]);
        if(lista_articulos[i][10]==0) {
          subtotal = Math.round(($('valunit_art_'+i).value*cols[3].innerHTML)/udtrans);
          $('subtotal_'+i).value=subtotal;
        } else {
          punit = ($('subtotal_'+i).value*1)/cols[3].innerHTML;
          //$('valunit_art_'+i).value=Math.round(punit*1000)/1000;
          subtotal=Math.round($('subtotal_'+i).value*1);
          $('subtotal_'+i).value=subtotal;
        }
        
        /*var difmonto  = articulos[i][15]-
                        ($('valunit_art_'+i).value/articulos[i][9]);
        var difvar    = (difmonto)*100/articulos[i][15];
        
        if((difvar>30 || difvar<-30) && !(articulos[i][14]))
          icono_precio='error';
        else 
          icono_precio='magnifier';
        
        $('art_dif_'+i).src='iconos/'+icono_precio+'.png';    
        */
        subtotales += subtotal;
        
        
      }
      
      subtotales=subtotales-$('doc_descuento').value;
      
      /*if($('iva_incl').checked) {
        
        _total=subtotales;
        _neto=Math.round(subtotales/_global_iva);
        _iva=_total-_neto;
                
      } else {
		*/	
        _neto=subtotales;
        _total=Math.round(subtotales*_global_iva);
        _iva=_total-_neto;
        
      //}
      
      $('val_total').innerHTML=formatoDinero(_total);
      $('val_iva').innerHTML=formatoDinero(_iva);
      $('val_neto').innerHTML=formatoDinero(_neto);
      
    }
  
  actualizar_art = function(i) {
    
    if(lista_articulos[i].tipo*1==1) 
		lista_articulos[i].art_glosa=$('glosa_'+i).value;
    
    lista_articulos[i].stock_cant=$('cant_'+i).value.replace(',','.');
    
	if(lista_articulos[i].art_vence=='1' && validacion_fecha($('vence_'+i)))
		lista_articulos[i].stock_vence=$('vence_'+i).value;
    
    lista_articulos[i].stock_subtotal=$('subtotal_'+i).value.replace(',','.');
  
  }

  quitar_art = function(i) {
  
    lista_articulos=lista_articulos.without(lista_articulos[i]);
  
    redibujar_tabla();
  
  }
  
  seleccionar_articulo = function(articulo) {
  
    if(typeof(lista_articulos)!='boolean') {
      for(var i=0;i<lista_articulos.length;i++) {
        if(lista_articulos[i].art_id==articulo[5]) {
          alert('El art&iacute;culo ya est&aacute; en la lista.'.unescapeHTML());
          $('cant_'+articulo[5]).focus();
          return;
        }
      }
  
      var num=lista_articulos.length;
    } else {
      lista_articulos=new Array();
      var num=0;
    }
    
    
    lista_articulos[num]=new Object();
    
    lista_articulos[num].art_id=articulo[5];
    lista_articulos[num].art_codigo=articulo[0];
    lista_articulos[num].art_glosa=articulo[2];
    lista_articulos[num].forma_nombre=articulo[3];
    lista_articulos[num].art_vence=articulo[8];
    lista_articulos[num].stock_id=0;
    lista_articulos[num].stock_cant=0;
    lista_articulos[num].stock_subtotal=0;
    lista_articulos[num].stock_vence='';
    lista_articulos[num].tipo=0;
    
    redibujar_tabla();
  
  }
  
  guardar_pedido = function() {
  
	for(var i=0;i<lista_articulos.length;i++) {
		if(lista_articulos[i].art_vence=='1') {
			if(!validacion_fecha($('vence_'+i))) {
				alert("Fecha de vencimiento no es v&aacute;lida.".unescapeHTML());
				return;
			}
		}
	}
  
    if(bloquear_ingreso) return;
  
    bloquear_ingreso=true;
    params='listado='+encodeURIComponent(lista_articulos.toJSON());
  
    var myAjax = new Ajax.Request(
    'sql_modificar_recepcion.php',
    {
      method:'post',
      parameters: params+'&'+$('pedido').serialize(),
      onComplete: function(resp) {
        
        try {
        
        resultado=resp.responseText.evalJSON(true);
        
        if(resultado) {
          
          alert( ('Recepci&oacute;n guardada exitosamente.'.unescapeHTML()) );
		  window.location.reload();
		  
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
  
  anular_pedido = function() {

    if(bloquear_ingreso) return;
  
    bloquear_ingreso=true;
    params='anular=1';
  
    var myAjax = new Ajax.Request(
    'sql_modificar_recepcion.php',
    {
      method:'post',
      parameters: params+'&'+$('pedido').serialize(),
      onComplete: function(resp) {
        
        try {
        
        resultado=resp.responseText.evalJSON(true);
        
        if(resultado) {
          alert( ('Recepci&oacute;n anulada exitosamente.'.unescapeHTML()) );
          window.open('visualizar.php?doc_id=<?php echo $doc_id; ?>', 
                        '_self');
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

</script>

<body class="fuente_por_defecto popup_background">

<form id='pedido' name='pedido' onSubmit='return false;'>

<input type='hidden' id='doc_id' name='doc_id' 
value='<?php echo $doc_id; ?>'>

<input type='hidden' id='doc_iva' name='doc_iva' 
value='<?php echo $datos_pedido['doc_iva']; ?>'>

<input type='hidden' id='doc_descuento' name='doc_descuento' 
value='<?php echo $datos_pedido['doc_descuento']; ?>'>

<div class='sub-content'>
<table>
<tr>
<td style='text-align:right;'>N&uacute;mero de Recepci&oacute;on:</td>
<td style='font-weight:bold; font-size:14px;'><?php echo $datos_pedido['doc_id'] ?></td>
</tr>

<tr>
<td style='text-align:right;'>Fecha:</td>
<td>
<select name='log_id' id='log_id' onChange=''>
<?php 
	for($i=0;$i<sizeof($detalle_logs);$i++) {
		print("<option value='".$detalle_logs[$i]['log_id']."'>".$detalle_logs[$i]['log_fecha']." [".$detalle_logs[$i]['log_folio']."]</option>");
	}
?>
</select>		
</td>
</tr>

<tr>
<td style="text-align:right;">Proveedor:</td>
<td>
<input type="hidden" id="id_proveedor" name="id_proveedor" value="<?php echo $datos_pedido['prov_id']; ?>">
<input type="text" id="rut_proveedor" name="rut_proveedor" size=20 style='font-size:10px;'
value='<?php echo $datos_pedido['prov_rut']; ?>' style='text-align:right;' DISABLED>
<input type="text" id="nombre_proveedor" name="nombre_proveedor" size=50
value="<?php echo $datos_pedido['prov_glosa']; ?>" onDblClick='liberar_proveedor();' style='font-size:10px;'>

</td>
</tr>



<tr>
<td style='text-align:right;'>Tipo de Documento:</td>
<td>
<select name='doc_tipo' id='doc_tipo' onChange=''>
<option value='0' <?php if($datos_pedido['doc_tipo']==0) echo 'SELECTED'; ?> >Gu&iacute;a de Despacho</option>
<option value='1' <?php if($datos_pedido['doc_tipo']==1) echo 'SELECTED'; ?> >Factura</option>
<option value='2' <?php if($datos_pedido['doc_tipo']==2) echo 'SELECTED'; ?> >Boleta</option>
<option value='3' <?php if($datos_pedido['doc_tipo']==3) echo 'SELECTED'; ?> >Pedido</option>
</select>		
</td>
</tr>

<tr>
<td style='text-align:right;'>N&uacute;mero:</td>
<td>
<input type='text' id='doc_num' name='doc_num' 
value='<?php echo $datos_pedido['doc_num']; ?>' /> 
</td>
</tr>

<tr>
<td style='text-align:right;'>Orden de Compra:</td>
<td>
<input type='text' id='orden_numero' name='orden_numero' 
value='<?php echo $datos_pedido['_orden_numero']; ?>' /> 
</td>
</tr>

<tr>
<td style='text-align:right;' valign='top'>Observaciones:</td>
<td>
<textarea id='doc_observacion' name='doc_observacion' rows=3 cols=60><?php echo $datos_pedido['doc_observacion']; ?></textarea> 
</td>
</tr>

<tr>
<td style='text-align:right;'>Agregar Art&iacute;culo:</td>
<td>
<input type='text' id='codigo' name='codigo'>
</td>
</tr>

</table>
</div>  

<div class='sub-content2' style='height:245px;overflow:auto;'
id='lista_detalle'>

</div>

<center>
    <div class='boton' id='guardar_pedido'>
		<table><tr><td>
		<img src='iconos/disk.png'>
		</td><td>
		<a href='#' onClick='guardar_pedido();'>
		Guardar Modificaciones...</a>
		</td></tr></table>
		</div>

    <div class='boton' id='anular_pedido'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='anular_pedido();'>
		Anular Recepci&oacute;n...</a>
		</td></tr></table>
	</div>

</center>

</form>

</body>

<script> 

  mostrar_proveedor=function(datos) {
    $('id_proveedor').value=datos[3];
    $('rut_proveedor').value=datos[1];
    $('nombre_proveedor').value=datos[2].unescapeHTML();
  }
    
  liberar_proveedor=function() {
    $('id_proveedor').value=-1;
    $('nombre_proveedor').value='';
    $('rut_proveedor').value='';
    $('nombre_proveedor').focus();
  }

  autocompletar_proveedores = new AutoComplete(
    'nombre_proveedor', 
    'autocompletar_sql.php',
    function() {
      if($('nombre_proveedor').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('nombre_proveedor').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_proveedor);

		autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      'autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts&'+$('codigo').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_articulo);

  redibujar_tabla(); 
  
</script>

</html>

<?php    
  
  }

?>
