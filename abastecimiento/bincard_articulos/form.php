<?php

  require_once("../../conectar_db.php");
  
  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(4),')', 'ORDER BY bod_glosa'); 
  
  $servs="'".str_replace(',','\',\'',_cav2(4))."'";

	$servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica AND centro_ruta IN (".$servs.")
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 


?>	
    <script>
          
  consulta_enviada=false;
  
  buscar_codigo_prod = function() {
  
  codigo_art = document.getElementById('codigo');
  
  $('prod_id').value='';
  $('cargandoimg').style.display='';
  
  var myAjax2 = new Ajax.Updater(
      'lista_productos',
			'abastecimiento/bincard_articulos/buscar_arts.php',
			{
				method: 'get',
				parameters: 'codigo='+serializar(codigo_art),
				evalScripts: true,
				onComplete: function (pedido_datos) {
				
				  $('codigo').style.background=''; // Pone Normal, Existe
					$('cargandoimg').style.display='none';
          $('producto_detalle').style.display='';
					
          $('fecha1').focus();
          	
				}	
			}
			
			);
			
  }
  
  seleccionar_art = function(art_id, art_codigo) {
  
    $('codigo').value=art_codigo;
    $('prod_id').value=art_id;
  
  }
  
  verifica_tabla = function()
  {
    if($('prod_id').value=='')
    {
        alert('C&oacute;digo de Producto ingresado es inv&aacute;lido.'.unescapeHTML());
        return;
    }
    
   // if(!isDate($('fecha1').value))
   // {
   //     alert('Fecha inicial inv&aacute;lida.'.unescapeHTML());
   //     $('fecha1').focus();
   //     return;
   // }  
    
   // if(!isDate($('fecha2').value))
  //  {
  //      alert('Fecha final es inv&aacute;lida.'.unescapeHTML());
  //      $('fecha2').focus();
  //      return;
  //  }
    params=$('bincard').serialize();
    top=Math.round(screen.height/2)-225;
    left=Math.round(screen.width/2)-350;
    new_win = 
    window.open('abastecimiento/bincard_articulos/bincard.php?'+params,
    'win_talonarios', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
    'top='+top+', left='+left);
      
    new_win.focus();
    
    return;
 
	}
	
	verifica_tabla_mult = function() {
  
    if(consulta_enviada) return;
  
    if($('art_ids')==null) {
      alert('No hay art&iacute;culos seleccionados.'.unescapeHTML());
      return;
    }
    
    if(!isDate($('fecha1').value)) {
      alert('Fecha inicial inv&aacute;lida.'.unescapeHTML());
      $('fecha1').focus();
      return;
    }  
    if(!isDate($('fecha2').value)) {
      alert('Fecha final es inv&aacute;lida.'.unescapeHTML());
      $('fecha2').focus();
      return;
    }
    
    consulta_enviada=true;
    
    var myAjax = new Ajax.Request(
    'abastecimiento/bincard_articulos/bincard.php',
    {
      method: 'get',
      parameters: $('bincard').serialize()+'&strip&prod_ids='+encodeURIComponent($('art_ids').value),
      onComplete: function(informe) {
      
        imprimirHTML(informe.responseText);
        
        consulta_enviada=false;
      
      }
    });
    
  }
  
  xls_busqueda = function() {

    var __ventana = window.open('abastecimiento/bincard_articulos/bincard.php?xls&'+$("bincard").serialize(), '_self');
    
  
  }
		
	</script>
  
  
  <center>
    <table>
        <tr>
            <td width=650>
                <div class='sub-content'>
                    <div class='sub-content'>
                        <img src='iconos/page_refresh.png'>
                        <b>Bincard de Art&iacute;culos Producto</b>
                    </div>
                    <div class='sub-content'>
                        <img src='iconos/zoom.png'>
                        <b>Selecci&oacute;n de Producto</b>
                    </div>
                    <form name='bincard' id='bincard'>
                        <table>
                            <tr>
                                <td valign='top'>
                                    <input type='hidden' id='prod_id' name='prod_id' value=''>
                                    <table>
                                        <tr>
                                            <td style='text-align: right;'>Enfocar Ubicaci&oacute;n:</td>
                                            <td>
                                                <select name='bodega' id='bodega'>
                                                    <?php echo $bodegashtml; echo $servicioshtml; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;'>C&oacute;digo Int.:</td>
                                            <td>
                                                <input type='text' name='codigo' id='codigo'  size=17>
                                                <img id='cargandoimg' src='imagenes/ajax-loader1.gif' style='display: none;'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;'>Fecha Inicio:</td>
                                            <td>
                                                <input type='text' name='fecha1' id='fecha1' size=10
                                                style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
                                                <img src='iconos/date_magnify.png' id='fecha1_boton'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;'>Fecha Final:</td>
                                            <td>
                                                <input type='text' name='fecha2' id='fecha2' size=10
                                                style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
                                                <img src='iconos/date_magnify.png' id='fecha2_boton'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;' valign='top'>
                                                Visualizar Campos:
                                            </td>
                                            <td>
                                                <input type='checkbox' id='tipomov' name='tipomov' CHECKED>
                                                Tipo de Movimiento<br>
                                                <input type='checkbox' id='saldo' name='saldo'>
                                                Saldo en cada Movimiento<br>
                                                <input type='checkbox' id='origendestino' name='origendestino' CHECKED>
                                                Or&iacute;gen/Destino<br>
                                                <input type='checkbox' id='valor' name='valor'>
                                                Precio Unitario<br><hr>
                                                <input type='checkbox' id='datospaciente' name='datospaciente'>
                                                Datos del Paciente<br>
                                                <input type='checkbox' id='datosadquiriente' name='datosadquiriente'>
                                                Datos del Adquiriente<br>
                                                <input type='checkbox' id='datosmedico' name='datosmedico'>
                                                Datos del M&eacute;dico<br>
                                                <input type='checkbox' id='datosreceta' name='datosreceta'>
                                                N&uacute;mero y Tipo de Receta<br>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <div class='sub-content' id='producto_detalle'style='width:300px;'>
                                        <div class='sub-content'>
                                            <img src='iconos/script.png'>
                                            <b>Listado de Art&iacute;culos</b>
                                        </div>
                                        <div class='sub-content2' id='lista_productos' style='height: 250px; overflow:auto;'>
                                            (No ha seleccionado art&iacute;culos...)
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <center>
                            <table>
                                <tr>
                                    <td>
                                        <div class='boton'>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <img src='iconos/script.png'>
                                                    </td>
                                                    <td>
                                                        <a href='#' onClick='verifica_tabla();'> Generar Bincard...</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td>
                                        <div class='boton'>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <img src='iconos/printer.png'>
                                                    </td>
                                                    <td>
                                                        <a href='#' onClick='verifica_tabla_mult();'> Generar Bincards...</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td>
                                        <div class='boton'>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <img src='iconos/page_excel.png'>
                                                    </td>
                                                    <td>
                                                        <a href='#' onClick='xls_busqueda();'> Descargar XLS (MS Excel) ...</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </form>
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
	
	   abrir_articulo = function(d) {
      	
      	$('codigo').value=d[0];
      	buscar_codigo_prod();
      	
      }
      
      autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      'autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts&'+$('codigo').serialize()+'&bodega_id='+($('bodega').value*1)
        }
      }, 'autocomplete', 350, 200, 250, 1, 3, abrir_articulo);
  
  </script>
