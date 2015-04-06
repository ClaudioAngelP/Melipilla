<?php

  require_once("../../conectar_db.php");
  
  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(5),')', 'ORDER BY bod_glosa'); 

  $itemshtml = desplegar_opciones_sql(
  "SELECT DISTINCT item_codigo, item_glosa 
  FROM item_presupuestario
  JOIN articulo ON art_item=item_codigo ORDER BY item_glosa"
  ); 

  $convenioshtml = desplegar_opciones_sql(
  "SELECT DISTINCT convenio_id, convenio_nombre 
  FROM convenio ORDER BY convenio_nombre"
  ); 
		
?>

	
	<script>
	
	seltab=0;
	
	info_art_id='';
		
	articulos=new Array();

	cargar_listado = function() {
	
		//if(($('buscar').value.length>0 || $('item').value!=-1) && seltab==0) {
		
		$('imagen_carga').style.display='';
	
		var myAjax = new Ajax.Updater(
			'resultado', 
			'abastecimiento/stock_articulos/listado_criticos.php', 
			{
				method: 'get', 
				parameters: $('bodega').serialize()+'&'+$('buscar').serialize()+'&'+$('item').serialize(),
				evalScripts: true,
		    onComplete: function() {
				$('imagen_carga').style.display='none';
			}		
			}
			
			);
			
			//}
	
	}
	
	
	comprobar_item = function(articulo) {
	
	if(($('pedido_'+articulo).value*1)>0 && ($('critico_'+articulo).value*1)>0 && ($('pedido_'+articulo).value*1)>=($('critico_'+articulo).value*1)) {
	
				$('aceptar_'+articulo).show();
				$('error_'+articulo).src='iconos/tick.png';
				$('error_'+articulo).hide();
				
				return;
				
		} else {

				$('aceptar_'+articulo).hide();
				$('error_'+articulo).src='iconos/error.png';
				$('error_'+articulo).show();
				
				return;
				
		}
	
	}

	guardar_articulo = function (articulo) {

			$('aceptar_'+articulo).hide();
			$('error_'+articulo).src='imagenes/ajax-loader1.gif';
			$('error_'+articulo).show();
	
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=stock_critico_guardar&id='+articulo+'&'+$('bodega').serialize()+'&'+$('pedido_'+articulo).serialize()+'&'+$('critico_'+articulo).serialize()+'&'+$('gasto_'+articulo).serialize(),
				onComplete: function (pedido_datos) {
		
					if(pedido_datos.responseText=='OK') {
					
						$('pedido_'+articulo).style.background='inherit';
						$('critico_'+articulo).style.background='inherit';
						$('error_'+articulo).src='iconos/tick.png';
						$('error_'+articulo).style.display='';
						$('aceptar_'+articulo).style.display='none';
						
					} else {
					
						alert('ERROR:'+chr(13)+pedido_datos.responseText);
						
					}
				}
			}
			
			);
	
	} 
	

	info_articulo = function(art_id) {
    
      var val=$('gasto_calc').value;
      
      // Evita que lo pida dos veces seguidas al cambiar de campo...
      if(art_id+''+val==info_art_id) return;
      
      info_art_id=art_id+''+val;
      
      var t=new Date().getTime();
    
      var html='<center><span id="gasto_loader"><br><br><img src="imagenes/ajax-loader3.gif"></span><img style="display:none;" onLoad="this.style.display=\'\';$(\'gasto_loader\').style.display=\'none\';" src="abastecimiento/stock_articulos/calcular_gasto.php?art_id='+art_id+'"></center>';
      
      $('info_articulo').innerHTML=html;
    
    }


	</script>

	<center>

	<table style='width:900px;'><tr><td>

	<div class='sub-content'>
	<div class='sub-content'><img src='iconos/chart_line.png' /><b>Stock Cr&iacute;tico/Puntos de Pedido</b></div>

	<div class='sub-content'>
	<table>
	<tr><td style='text-align: right;'>Bodega/Ubicaci&oacute;n:</td>
	<td colspan=3>
	<select id='bodega' name='bodega' onChange='cargar_listado();'>
  <?php echo $bodegashtml; ?>
  </select>
	<img src='imagenes/ajax-loader1.gif' id='imagen_carga' style='display: none;'>
  </td></tr>
  <tr><td style='text-align: right;'>Filtro:</td>
	<td colspan=3>
  <input type='text' id='buscar' name='buscar' onChange='cargar_listado();' size=75>
  </td></tr>
	<tr>
  <td style='text-align: right;'>
  Rubro/Item:
  </td>
  <td colspan=3>
  <select id='item' name='item' onChange='cargar_listado();'>
  <option value=-1 SELECTED>(Cualquier Rubro...)</option>
  <?php echo $itemshtml; ?>
  </select>
  </td>
  </tr>
  </table>
	</div>
	
  <table style='width:100%;' cellpadding=0 cellspacing=0>
  <tr><td>
  
  <div class='tabbed_content' style='height:310px; overflow: auto;'>

  <div id='tab_modifica_content'>

  <div id='resultado'
	style='height:235px; overflow: auto;'>
	<center>(No se ha efectuado una b&uacute;squeda...)</center>
	</div>
	
  
  <table style='width:100%;' cellpadding=0 cellspacing=0>
	<tr><td style='background-color:white;'>
	<div id='info_articulo' style='height:60px;overflow:hidden;'>

	</div>
	</td><td style='width:100px;'>
	
  <center><u><b>Calcular Gasto</b></u><br /><br />
	<select id='gasto_calc'>
  <option value='0' SELECTED>Mensual</option>
	<option value='1'>Semanal</option>
	</select>
	</center>
	
	</td></tr>
	</table>
	
	</div>
	
	
	
  </center>
	
	</div>

  </div>
	
	</td></tr></table>
	
	</div>
	
</td></tr></table>
	
