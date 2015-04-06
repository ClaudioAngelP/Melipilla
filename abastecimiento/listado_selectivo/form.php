<?php
    require_once("../../conectar_db.php");
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(4),')', 'ORDER BY bod_glosa');
    $itemshtml = desplegar_opciones("item_presupuestario", "item_codigo, item_glosa",'','true','ORDER BY item_codigo');  
?>	
<script>
    verifica_tabla = function()
    {
        params=$('listado').serialize();
        top=Math.round(screen.height/2)-225;
        left=Math.round(screen.width/2)-350;
        new_win = 
        window.open('abastecimiento/listado_selectivo/listado.php?'+params,
        'win_talonarios', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
        'top='+top+', left='+left);
        new_win.focus();
        return;
    }
	
    verifica_tabla_mult = function()
    {
        var myAjax=new Ajax.Updater('lista',
        {
            method: 'get',
            parameters: $('listado').serialize(),
            onComplete: function(informe)
            {
                imprimirHTML(informe.responseText);
            }
        });
    }
  
    ver_campos = function()
    {
        if($('accion').value==0)
        {
            //campos para generar reporte
            $('tr_correlativo').style.display='none';
            $('tr_bodega').style.display='';
            $('tr_item_pres').style.display='';
            $('tr_limit').style.display='';
            $('tr_activado').style.display='none';
            $('generar').style.display='';
            $('comparar').style.display='none';
            $('correlativo').value='';
            $('limit').value='';
            $('listado_informe').innerHTML='';
        }
        else if($('accion').value==1)
        {
            //campos para procesar reporte
            $('tr_correlativo').style.display='';
            $('tr_bodega').style.display='none';
            $('tr_item_pres').style.display='none';
            $('tr_limit').style.display='none';
            $('tr_activado').style.display='none';
            $('generar').style.display='none';
            $('comparar').style.display='';
            $('correlativo').value='';
            $('limit').value='';
            $('listado_informe').innerHTML='';
        }
        else if($('accion').value==2)
        {
            $('tr_correlativo').style.display='none';
            $('tr_bodega').style.display='';
            $('tr_item_pres').style.display='';
            $('tr_limit').style.display='none';
            $('tr_activado').style.display='none';
            $('generar').style.display='';
            $('comparar').style.display='none';
            $('correlativo').value='';
            $('limit').value='';
            $('listado_informe').innerHTML='';
        }
    }
  
    imprimir_reporte=function()
    {
        var myAjax=new Ajax.Updater('listado_informe','abastecimiento/listado_selectivo/imprimir_listado.php',
        {
            method: 'post',
            parameters: $('listado').serialize(),
            onComplete: function(informe)
            {
                imprimirHTML(informe.responseText);
            }
	});
    }
	
    generar_reporte=function()
    {
        if($('accion').value==0)
        {
            if(!IsNumeric($('limit').value))
            {
                alert(('Debe ingresar una cantidad v&aacute;lida'.unescapeHTML()));
                return;
            }
        }
        else if($('accion').value==2)
        {
            $('limit').value='';
        }
        
        if(!confirm('¡Advertencia! Este listado no podr&aacute; ser eliminado posteriormente, &iquest;Desea continuar?.'.unescapeHTML())) return;
	var myAjax=new Ajax.Updater('listado_informe','abastecimiento/listado_selectivo/listado.php',
        {
            method: 'post',
            parameters: $('listado').serialize(),
            onComplete: function(r)
            {
            }
	});
    }
	
    buscar_reporte=function()
    {
        if(!IsNumeric($('correlativo').value)){
            alert(('Correlativo ingresado inv&aacute;lido.'.unescapeHTML()));
            return;
        }
        var myAjax=new Ajax.Updater('listado_informe','abastecimiento/listado_selectivo/listado.php',
        {
            method: 'post',
            parameters: $('listado').serialize(),
                onComplete: function(r) {
                }
        });
    }
    
    comprueba_comparacion=function()
    {
        var myAjax = new Ajax.Request('abastecimiento/listado_selectivo/comprueba_comparacion.php',
        {
            method:'post',
            parameters: $('listado').serialize(),
            onComplete: function(resp)
            {
                try
                {
                    resultado=resp.responseText.evalJSON(true);
                    if(resultado)
                    {
                        alert( ('El listado ya fu&eacute; comparado.'.unescapeHTML()) );
                    }
                    else
                    {
                        comparar_reporte();
                    }
		}
                catch(err)
                {
                    alert("ERROR: " + resp.responseText);
		}
	  }
        });
    }
    
    comparar_reporte=function()
    {
        var myAjax=new Ajax.Updater('listado_informe','abastecimiento/listado_selectivo/listado.php',
        {
            method: 'post',
            parameters: 'comparar=true&'+$('listado').serialize(),
            onComplete: function(r)
            {
            }
        });
    }
    
    xls_busqueda = function()
    {
        var __ventana = window.open('abastecimiento/listado_selectivo/listado.php?xls&'+$('listado').serialize(), '_self');
    }
    
    function IsNumeric(expression)
    {
        return (String(expression).search(/^\d+$/) != -1);
    }
    
    agregar_comentario = function(lsd_id,comentario){

		var myAjax = new Ajax.Request(
			'abastecimiento/listado_selectivo/guarda_observacion.php',
			{
				method:'post',
				parameters: 'lsd_id='+lsd_id+'&comentario='+comentario,
				onComplete: function(resp) {
				try {
			
					resultado=resp.responseText.evalJSON(true);
				
					if(resultado) {
						alert( ('Observaci&oacute;n Guardada.'.unescapeHTML()) );
						$('comentario_'+lsd_id).disabled=true;
					}
				}catch(err){
					alert("ERROR: " + resp.responseText);
				}
			}
		  });
	
	}
</script>
<center>
    <table width=50% >
        <tr>
            <td>
                <div class='sub-content'>
                    <div class='sub-content'>
                        <img src='iconos/page_refresh.png'>
                        <b>Listado selectivo de Art&iacute;culos</b>
                    </div>
                    <form name='listado' id='listado'>
                        <table>
                            <tr>
                                <td valign='top'>
                                    <table>
                                        <tr>
                                            <td style='text-align: right;' class='form_titulo'>Acci&oacute;n:</td>
                                            <td class='form_campo'>
                                                <select name='accion' id='accion' onChange='ver_campos();'>
                                                    <option value=0>Generar Listado Selectivo</option>
                                                    <option value=2>Generar Listado General</option>
                                                    <option value=1>Comparar Listado Selectivo</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr id='tr_correlativo' name='tr_correlativo'>
                                            <td style='text-align: right;' class='form_titulo'>Correlativo:</td>
                                            <td class='form_campo'>
                                                <input type='text' id='correlativo' name='correlativo' size=6>
                                            	<input type='button' value='Buscar Reporte...' onClick='buscar_reporte();'>
                                        	</td>
                                        </tr>
                                        <tr id='tr_bodega' name='tr_bodega'>
                                            <td style='text-align: right;' class='form_titulo'>Enfocar Ubicaci&oacute;n:</td>
                                            <td class='form_campo'>
                                                <select name='bodega' id='bodega'>
                                                    <?php echo $bodegashtml; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr id='tr_item_pres' name='tr_item_pres'>
                                            <td style='text-align: right;' class='form_titulo'>Item Presupuestario:</td>
                                            <td class='form_campo'>
                                               <select name='item_pres' id='item_pres'>
                                                <option value=0>(Todos...)</option>
                                                <?php echo $itemshtml; ?>
                                                </select> 
                                            </td>
                                        </tr>
                                        <tr id='tr_limit' name='tr_limit'>
                                            <td style='text-align: right; ' valign='center	' class='form_titulo'>
                                                Cantidad de Art&iacute;culos:
                                            </td>
                                            <td class='form_campo'>
                                            		<input type='text' id='limit' name='limit' onKeyUp='if(event.which==13)generar_reporte();' size=3/>
                                            </td>
                                        </tr>
                                        <tr id='tr_activado' name='tr_activado'>
                                            <td style='text-align: right;' valign='center' class='form_titulo'>
                                            <input id='activados' name='activados' type='checkbox' value='1'></td>
                                            <td>Art&iacute;culos Activados</td>
                                        </tr>
                                        <tr>
                                            
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <div class='sub-content2' id='listado_informe' name='listado_informe' style='height:300px;overflow:auto;'></div>
                        <center>
                            <table>
                                <tr>
                                    <td  id='generar'>
                                        <div class='boton'>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <img src='iconos/printer.png'>
                                                    </td>
                                                    <td>
                                                        <a href='#' onClick='generar_reporte();'> Generar Listado...</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                     <td  id='comparar'>
                                        <div class='boton'>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <img src='iconos/printer.png'>
                                                    </td>
                                                    <td>
                                                        <!--<a href='#' onClick='comparar_reporte();'> Comparar Listado...</a>-->
                                                        <a href='#' onClick='comprueba_comparacion();'> Comparar Listado...</a>
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
                                                        <img src='iconos/script.png'>
                                                    </td>
                                                    <td>
                                                        <a href='#' onClick='imprimir_reporte();'> Imprimir Listado...</a>
                                                    </td>
						</tr>
                                            </table>
					</div>
                                    </td>
                                    <!--<td>
                                        <div class='boton'>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <img src='iconos/page_excel.png'>
                                                    </td>
                                                    <td>
                                                        <a href='#'  onClick='xls_busqueda();'> Descargar XLS (MS Excel) ...</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>-->
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
    ver_campos();
</script>