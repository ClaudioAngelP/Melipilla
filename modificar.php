<?php

  require_once('conectar_db.php');

  if(isset($_GET['pedido_id'])) {
  
    // Modificación de Pedidos
    
  $pedido_id=$_GET['pedido_id'];  
    
  list($datos_pedido) = cargar_registros_obj("
    SELECT * FROM pedido WHERE pedido_id=$pedido_id
  ");
  
  $detalle_pedido = cargar_registros_obj("
    SELECT * FROM pedido_detalle 
    JOIN articulo USING (art_id)
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE pedido_id=$pedido_id
  ",true);
  
  $bodegasdesthtml = desplegar_opciones("bodega", "bod_id, bod_glosa, bod_proveedores, 
  		bod_despacho",$datos_pedido['destino_bod_id'],'bod_proveedores OR bod_despacho','ORDER BY bod_glosa'); 
	
	
  
?>

<html>
<title>Modificar Pedido</title>

<?php cabecera_popup('.'); ?>

<script>

  pedido_id = <?php echo $pedido_id; ?>;
  lista_articulos = <?php echo json_encode($detalle_pedido); ?>;
  bloquear_ingreso=false;

  redibujar_tabla = function() {
  
    var num=lista_articulos.length;
    
    html='<table style="font-size:11px;width:100%;" >';
    html+='<tr class="tabla_header">';
    html+='<td>Cod. Int.</td>';
    html+='<td>Glosa</td>';
    html+='<td>Forma</td>';
    html+='<td>Cantidad</td>';
    html+='<td>Accion</td>';
    html+='</tr>';
    
    for(var i=0;i<lista_articulos.length;i++) {
    
      if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2'; 
    
      var cantidad=lista_articulos[i].pedidod_cant+'';
      cantidad=cantidad.replace('.', ',');
    
      html+='<tr class="'+clase+'">';
      html+='<td style="text-align:right;font-weight:bold;">';
      html+=lista_articulos[i].art_codigo+'</td>';
      html+='<td>'+lista_articulos[i].art_glosa+'</td>';
      html+='<td>'+lista_articulos[i].forma_nombre+'</td>';
      html+='<td><center><input type="text" size=10 ';
      html+='style="text-align:right;" ';
      html+='id="cant_'+lista_articulos[i].art_id+'" ';
      html+='name="cant_'+lista_articulos[i].art_id+'" '; 
      html+='value="';
      html+=cantidad+'" onFocus="this.select()" ';
      html+='onKeyUp="actualizar_art('+lista_articulos[i].art_id+');">';
      html+='</center></td>';
      html+='<td><center><img src="iconos/delete.png" onClick="';
      html+='quitar_art('+lista_articulos[i].art_id+');" ';
      html+='style="cursor:pointer;"></center></td>';
      html+='</tr>';
    
    }
    
    html+='</table>';
  
    $('lista_detalle').innerHTML=html;
  
  }
  
  actualizar_art = function(art_id) {
  
    for(var i=0;i<lista_articulos.length;i++) {
      if(lista_articulos[i].art_id==art_id)
        break;
    }
    
    lista_articulos[i].pedidod_cant=$('cant_'+art_id).value.replace(',','.');
  
  }

  quitar_art = function(art_id) {
  
    for(var i=0;i<lista_articulos.length;i++) {
      if(lista_articulos[i].art_id==art_id)
        break;
    }
    
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
    lista_articulos[num].pedidod_cant=0;
    
    redibujar_tabla();
  
  }
  
  guardar_pedido = function() {
  
    if(bloquear_ingreso) return;
  
    bloquear_ingreso=true;
    params='listado='+encodeURIComponent(lista_articulos.toJSON());
  
    var myAjax = new Ajax.Request(
    'modificar_sql.php',
    {
      method:'post',
      parameters: params+'&'+$('pedido').serialize(),
      onComplete: function(resp) {
        
        try {
        
        resultado=resp.responseText.evalJSON(true);
        
        if(resultado) {
          alert('Pedido guardado exitosamente.');
          window.open('visualizar.php?id_pedido=<?php echo $pedido_id; ?>', 
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
  
  anular_pedido = function() {

    if(bloquear_ingreso) return;
  
    bloquear_ingreso=true;
    params='anular=1';
  
    var myAjax = new Ajax.Request(
    'modificar_sql.php',
    {
      method:'post',
      parameters: params+'&'+$('pedido').serialize(),
      onComplete: function(resp) {
        
        try {
        
        resultado=resp.responseText.evalJSON(true);
        
        if(resultado) {
          alert('Pedido anulado exitosamente.');
          window.open('visualizar.php?id_pedido=<?php echo $pedido_id; ?>', 
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

<input type='hidden' id='pedido_id' name='pedido_id' 
value='<?php echo $pedido_id; ?>'>

<div class='sub-content'>
<table>
<tr>
<td style='text-align:right;'>N&uacute;mero de Pedido:</td>
<td style='font-weight:bold; font-size:14px;'><?php echo $datos_pedido['pedido_nro'] ?></td>
</tr>
<tr>
<td style='text-align:right;'>Destino:</td>
<td>
<select name='bodega_destino' id='bodega_destino' onChange='' DISABLED>
<?php echo $bodegasdesthtml; ?>
</select>
		
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

<div class='sub-content2' style='height:265px;overflow:auto;'
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
		Anular Pedido...</a>
		</td></tr></table>
		</div>
</center>

</form>

</body>

<script> 

		autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      'autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts_stock&'+$('codigo').serialize()+'&'+
                      $('bodega_destino').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_articulo);


  redibujar_tabla(); 
  
</script>

</html>

<?php    
  
  }

?>
