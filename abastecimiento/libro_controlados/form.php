<?php

  require_once("../../conectar_db.php");
   
  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(7),')',
	'ORDER BY bod_glosa'); 
	
  $clasificahtml = desplegar_opciones("bodega_clasificacion", 
	"clasifica_id, clasifica_nombre",'','true','ORDER BY clasifica_nombre'); 
	
	$itemshtml = desplegar_opciones("item_presupuestario", 
	"item_codigo, item_glosa",'','true','ORDER BY item_codigo'); 
	  
?>
  
  <script>
  
  realizar_busqueda = function() {
					
	  Windows.closeAll();
    
    var win = new Window("winreporte", {className: "alphacube", top:40, left:0, width: 650, height: 400, title: 'Libro de Medicamentos Controlados',
                          minWidth: 650, minHeight: 400,
                          maximizable: true, minimizable: false,
                          wiredDrag: true });
                          
    
    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
    win.setAjaxContent('mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=valorizar_stock&'+$('busqueda').serialize()
			});
    win.setDestroyOnClose();
    win.showCenter();
    win.show();
		
		}
  
  </script>
  
  <center>
  
  <table width=550>
  <tr><td>
  
  <div class='sub-content'>
  <div class='sub-content'>
  <img src='iconos/pill_delete.png'>
  <b>Libro de Medicamentos Controlados</b></div>
  <div class='sub-content'>
  <img src='iconos/zoom.png'>
  <b>Filtrar Listado</b></div>
  <div class='sub-content'>
  <form name='busqueda' id='busqueda'>
  <input type='hidden' name='item' value=0>
  <input type='hidden' name='clasifica' value=0>
  <input type='hidden' name='controlado' value=1>
  <table>
  <tr><td style='text-align: right;'>Ubicaci&oacute;n:</td><td>
  <select name='bodega' id='bodega'>
  <option value=0>(Global...)</option>
  <?php echo $bodegashtml; ?>
  </select>
  </td>
  <td rowspan=3 width=230>
  
  <div class='sub-content'>
  <div class='sub-content'><b>Valorizaci&oacute;n por:</b></div>
  <input type='radio' name='valorizar' id='valorizar' value=0> 
  Valor M&iacute;nimo<br>
  <input type='radio' name='valorizar' id='valorizar' value=1 CHECKED> 
  Valor Medio<br>
  <input type='radio' name='valorizar' id='valorizar' value=2> 
  Valor M&aacute;ximo<br>
  <input type='radio' name='valorizar' id='valorizar' value=3> 
  &Uacute;ltimo Valor<br>
  </div>
  
  
  </td>
  </tr>
  <tr><td style='text-align: right;'>Ordenar por:</td><td>
  <select name='orden' id='orden'>
  <option value=0 SELECTED>C&oacute;digo Interno</option>
  <option value=1>Glosa</option>
  <option value=2>Nombre</option>
  <option value=3>Clasificaci&oacute;n</option>
  <option value=4>Item Presupuestario</option>
  </select>
  </td></tr>
  <tr><td>&nbsp;</td><td>
  <input type='checkbox' name='ascendente' id='ascendente' CHECKED> 
  &Oacute;rden Ascendente 
  </td></tr>
  </table>
  </form>
  </div>
  <center>
  <div class='boton' id='guardar_boton'>
	<table><tr><td>
	<img src='iconos/layout.png'>
	</td><td>
	<a href='#' onClick='realizar_busqueda();'>Generar Informe...</a>
	</td></tr></table>
	</div>
  </center>
  </td></tr>
  </table>
