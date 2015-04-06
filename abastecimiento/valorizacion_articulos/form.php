<?php

  require_once("../../conectar_db.php");
   
  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(6),')',
	'ORDER BY bod_glosa'); 
	
	$servs="'".str_replace(',','\',\'',_cav2(6))."'";

	$servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica AND centro_ruta IN (".$servs.")
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 

	
  $clasificahtml = desplegar_opciones("bodega_clasificacion", 
	"clasifica_id, clasifica_nombre",'','true','ORDER BY clasifica_nombre'); 
	
	$itemshtml = desplegar_opciones("item_presupuestario", "item_codigo, item_glosa",'','true','ORDER BY item_codigo'); 

	  
?>
  
  <script>
  
  realizar_busqueda = function() {
					
	    params=$('busqueda').serialize();
    
      top=Math.round(screen.height/2)-225;
      left=Math.round(screen.width/2)-350;
      
      new_win = 
      window.open('abastecimiento/valorizacion_articulos/'+
      'valorizacion_stock.php?'+params, 'win_reporte', 
      'toolbar=no, location=no, directories=no, status=no, resizable=yes,'+
      'menubar=no, scrollbars=yes, resizable=no, width=700, height=450, '+
      'top='+top+', left='+left);
      
      new_win.focus();
		
	}
		
	xls_busqueda = function() {

    var __ventana = window.open('abastecimiento/valorizacion_articulos/valorizacion_stock.php?xls&'+$("busqueda").serialize(), '_self');
    
  
  }
  
  </script>
  
  <center>
  
  <table width=650>
  <tr><td>
  
  <div class='sub-content'>
  <div class='sub-content'>
  <img src='iconos/money.png'>
  <b>Valorizaci&oacute;n de Stock</b></div>
  <div class='sub-content'>
  <img src='iconos/zoom.png'>
  <b>Filtrar Listado</b></div>
  <div class='sub-content'>
  <form name='busqueda' id='busqueda' onSubmit='return false;'>
  <table>
  <tr><td style='text-align: right;'>Ubicaci&oacute;n:</td><td>
  <select name='bodega' id='bodega'>
  <?php echo $bodegashtml; echo $servicioshtml; ?>
  </select>
  </td></tr>
  <tr>
  <td style='text-align: right;'>Filtrar C&oacute;digos:</td>
  <td>
  <input type='text' id='buscar' name='buscar'>
  </td>
  </tr>
  <tr><td style='text-align: right;'>Item Presupuestario:</td><td>
  <select name='item' id='item' style='width:250px;'>
  <option value=0 selected="">(Todos...)</option>
  <?php echo $itemshtml; ?>
  </select>
  </td></tr>
  <tr><td style='text-align: right;'>Clasificaci&oacute;n:</td><td>
  <select name='clasifica' id='clasifica'>
  <option value=0>(Todas...)</option>
  <?php echo $clasificahtml; ?>
  </select>
  </td></tr>
  <tr><td style='text-align: right;'>Fecha de Referencia:</td><td>
  <input type='text' id='fecha_ref' name='fecha_ref' size=10
  style='text-align: center;' 
  value='<?php echo date('d/m/Y'); ?>'> 
  <img src='iconos/date_magnify.png' id='fecha_ref_boton'>
  </td></tr>
  <tr><td style='text-align: right;'>Ordenar por:</td><td>
  <select name='orden' id='orden'>
  <option value=0 SELECTED>C&oacute;digo Interno</option>
  <option value=1>Glosa</option>
  <option value=2>Nombre</option>
  <option value=3>Clasificaci&oacute;n</option>
  <option value=4>Item Presupuestario</option>
  <option value=5>Prioridad</option>
  </select>
  </td></tr>
  <tr><td style='text-align: right;'>Orden:</td><td>
  <input type='checkbox' name='ascendente' id='ascendente' CHECKED> 
  &Oacute;rden Ascendente 
  </td></tr>
  <tr>
  <td valign='top' style='text-align: right;'>
  Valorizaci&oacute;n:
  </td>
  <td valign='top'>
  <input type='radio' name='valorizar' id='valorizar' value=0> 
  Valor M&iacute;nimo<br>
  <input type='radio' name='valorizar' id='valorizar' value=1> 
  Valor Medio<br>
  <input type='radio' name='valorizar' id='valorizar' value=2> 
  Valor M&aacute;ximo<br>
  <input type='radio' name='valorizar' id='valorizar' value=3> 
  &Uacute;ltimo Valor<br>
  <input type='radio' name='valorizar' id='valorizar' value=10 CHECKED> 
  No Valorizar<br>
  </td>
  <td valign='top' style='text-align: right;'>Visualizar:</td>
  <td valign='top'>
  <input type='checkbox' id='mostrar_clasif' name='mostrar_clasif'>
  Clasificaci&oacute;n<br>
  <input type='checkbox' id='mostrar_forma' name='mostrar_forma'>
  Forma Farmac&eacute;utica<br>
  <input type='checkbox' id='mostrar_item' name='mostrar_item'>
  Item Presupuestario<br>
  <input type='checkbox' id='mostrar_prioridad' name='mostrar_prioridad'>
  Prioridad<br>
  <hr>
  <input type='checkbox' id='mostrar_lotes' name='mostrar_lotes'>
  Lotes por Fecha de Venc.<br>
  <hr>
  <input type='checkbox' id='mostrar_lotes_cero' name='mostrar_lotes_cero'>
  Articulos sin Saldo<br>
  <input type='checkbox' id='mostrar_solo_lotes_cero' name='mostrar_solo_lotes_cero'>
  <b>Solamente</b> Articulos sin Saldo<br>
  
  </td>
  </tr>
  </table>
  </form>
  </div>
  <center>
  
  <table>
  <tr><td>
  
  <div class='boton' id='guardar_boton'>
	<table><tr><td>
	<img src='iconos/layout.png'>
	</td><td>
	<a href='#' onClick='realizar_busqueda();'> Generar Informe...</a>
	</td></tr></table>
	</div>
  </center>
  
  </td><td>
  
  <div class='boton' id='guardar_boton'>
	<table><tr><td>
	<img src='iconos/page_white_excel.png'>
	</td><td>
	<a href='#' onClick='xls_busqueda();'> Descargar XLS (MS Excel) ...</a>
	</td></tr></table>
	</div>
	
	</td></tr></table>
  
  </td></tr>
  </table>
  
    <script>
  
    Calendar.setup({
        inputField     :    'fecha_ref',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_ref_boton'
    });

    </script>
