<?php
    set_time_limit(0);
    require_once("../../conectar_db.php");
    $bodega = pg_escape_string($_GET['bodega']);
    $bodega_dest = ($_GET['bodega_dest']*1);
    if(isset($_GET['fecha1'])) $fecha1 = pg_escape_string($_GET['fecha1']);
    if(isset($_GET['fecha2'])) $fecha2 = pg_escape_string($_GET['fecha2']);
    if(isset($fecha1) and isset($fecha2))
    {
        $metodo=0;    // Selección Automática
    } 
    else
    {
        $metodo=1;    // Selección Manual
    }
    if(strstr($bodega,'.'))
    {
        $stock_precalc='stock_precalculado2';
        $centro_ruta=pg_escape_string($bodega);
        $bodega='stock_centro_ruta=\''.(pg_escape_string($bodega)).'\'';
        $bod_origen = pg_query($conn,
          "SELECT centro_nombre FROM centro_costo 
          WHERE centro_ruta='$centro_ruta'");
        $bod_origen_nam = pg_fetch_result($bod_origen, 0, 0);
        if(isset($fecha1) and isset($fecha2))
        {
            $calc_gasto="0";
        }
        else
        {
            $calc_gasto='';
        }
        //$join_critico="";
      
    }
    else
    {
        $stock_precalc='stock_precalculado';
        $bod_id=($bodega*1);
        $bodega='stock_bod_id='.($bod_id);
        $bod_origen = pg_query($conn,
          "SELECT bod_glosa FROM bodega WHERE bod_id=$bod_id");
        $bod_origen_nam = pg_fetch_result($bod_origen, 0, 0);
        if(isset($fecha1) and isset($fecha2))
        {
            $calc_gasto="calcular_gasto2(art_id, $bod_id, '$fecha1', '$fecha2')";
        } 
        else
        {
            $calc_gasto='';
        }
        //$join_critico="LEFT JOIN stock_critico ON 
         //art_id=critico_art_id AND critico_bod_id=$bod_id";
    }


    $filtro = pg_escape_string($_GET['filtro_cods']);
    $limite = ($_GET['valor_lim']*1);
    $modglobal = ($_GET['modglobal']*1);
    $mods = $_GET['cant']*1;
    for($i=0;$i<($mods-1);$i++)
    {
        $modifica[$i][0]=$_GET['mod'.$i.'_sel'];
        $modifica[$i][1]=$_GET['mod'.$i.'_cant']*1;
    }
    if(trim($filtro)!='')
    {
        $filtro = str_replace('*', '%', $filtro);
        $filtro_query = "WHERE art_codigo LIKE '".$filtro."'";
    }
    else
    {
        $filtro_query = "";
    }

    $bod_destino = pg_query($conn, "SELECT bod_glosa FROM bodega WHERE bod_id=$bodega_dest");
    $bod_destino_nam = pg_fetch_result($bod_destino, 0, 0);

    if($metodo==0)
    {
        $query="
        SELECT
        *,
        (-(stock_gasto)+COALESCE(0,0))
        FROM
        (
        SELECT
        art_id,
        art_codigo,
        art_glosa,
        SUM(stock_cant) AS stock,
        $calc_gasto AS stock_gasto,
        0,
        0,
        art_clasifica_id,
        art_val_ult
        FROM
        articulo
        LEFT JOIN $stock_precalc ON stock_art_id=art_id
        $filtro_query
        GROUP BY
        art_id, art_codigo, art_glosa,
        art_clasifica_id, art_val_ult
        ) AS ss
        WHERE
        stock>0
        AND
        stock is not null
        AND
        (-(stock_gasto)+COALESCE(0,0))>stock
        ";
      
        $stocks = pg_query($conn, $query);
    }
?>
    
<script>
    
    recalcular_art_nuevo = function()
    {
        punit = $('art_punit').value;
        cantidad = $('art_sugerido_val').value;
        $('art_subtotal').innerHTML=formatoDinero(punit*cantidad);
    }
    
    recalcular = function()
    {
        filas = $('tabla_filas').getElementsByTagName('tr');
        val_total=0;
        for(i=1;i<filas.length;i++)
        {
            if(i%2==0) def_clase='tabla_fila'; else def_clase='tabla_fila2';
            filas[i].clase = def_clase;
            filas[i].className = def_clase;
            columnas = filas[i].getElementsByTagName('input');
            valor = (columnas[0].value*1)*(columnas[2].value*1);
            cols = filas[i].getElementsByTagName('td');
            cols[4].innerHTML=formatoDinero(valor);
            val_total+=valor;
            
        }
        $('total').innerHTML=formatoDinero(val_total);
    }
    
    generar_cadena_arts = function()
    {
        filas = $('tabla_filas').getElementsByTagName('tr');
        cadena='';
        for(i=1;i<filas.length;i++)
        {
            campos = filas[i].getElementsByTagName('input');
            bodega_campo = $('bodega_dest').value;
            id_fila=campos[0].name.split('_');
            id=id_fila[1];
            cant=(campos[2].value*1);
            bod=(bodega_campo);
            if(cant>0)
            {
                if(cadena=='')
                {
                    cadena=bod+'-'+id+'-'+cant;
                }
                else
                {
                    cadena=cadena+'!'+bod+'-'+id+'-'+cant;
                }
            }
        }
        return cadena;
    }
    
    generar_cadena_bods = function()
    {
        return $('bodega_dest').value;
    }
    
    generar_pedidos = function()
    {
        try
        {
            if(generar_cadena_arts()=='' || generar_cadena_bods=='')
                return false;

            cadena = 'articulos_pedidos='+generar_cadena_arts()+'&bodegas_pedidos='+generar_cadena_bods();
        }
        catch(err)
        {
            alert(err);
            return false;
        }
        return cadena;
    }
    
    devolver_cant = function (id_art)
    {
        $('cantidad_'+id_art).value=$('sugiere_'+id_art).value;
        recalcular();
    }
    
    cancelar_seleccionar_art = function()
    {
        $('art_id').value=0;
        $('art_punit').value=0;
        $('art_sugiere').value=0;
        $('art_prioridad').value=0;
        $('art_sugerido_val').value=0;
        $('prod_codigo').value='';
		$('art_nombre').innerHTML='&nbsp;';
		$('art_stock').innerHTML='&nbsp;';
		
		$('art_subtotal').innerHTML='$0.-';
        $('prod_codigo').focus();
    }
    
    buscar_codigo_prod = function()
    {
        var myAjax2 = new Ajax.Request(
			'abastecimiento/pedido_articulos/ver_articulo.php',
		{
            method: 'get',
			parameters: 'codigo='+serializar($('prod_codigo'))+'&'+$('calc_stock_pedido').serialize(),
			onComplete: function (pedido_datos)
            {
            try
            {
                datos_nuevos = eval(pedido_datos.responseText);
                if(datos_nuevos)
                {
                    $('art_id').value=datos_nuevos[0];
					$('art_punit').value=datos_nuevos[8];
					$('art_prioridad').value=datos_nuevos[9];
					$('art_sugiere').value=datos_nuevos[11];
					$('art_nombre').innerHTML=datos_nuevos[2];
					$('art_stock').innerHTML=datos_nuevos[3];
					
					$('art_sugerido_val').value=datos_nuevos[11];
                    $('art_sugerido_val').select();
                    recalcular_art_nuevo();
                }
                else
                {
                    alert('C&oacute;digo de Art&iacute;culo no encontrado.'.unescapeHTML());
                    $('art_id').value=0;
					$('art_punit').value=0;
					$('art_prioridad').value=0;
					$('art_sugiere').value=0;
					$('art_nombre').innerHTML='&nbsp;';
					$('art_stock').innerHTML='&nbsp;';
					
					$('art_pedido').innerHTML='&nbsp;';
					$('art_sugerido_val').value='0';
					$('prod_codigo').select();
                }
            }
            catch(err)
            {
                alert(err);
            }
            }
        }
        );
    }
 
    agregar_articulo = function() 
    {
        if($('art_id').value==0) return;
        pasar=false;
        fila = document.getElementById('fila_'+$('art_id').value);
        if(fila!=null)
        {
            alert("Art&iacute;culo ya est&aacute; presente en la lista de pedido.".unescapeHTML());
            return;
        }
        fila_nueva="<tr id='fila_"+($('art_id').value)+"' class='' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=this.clase;'><input type='hidden' name='valor_"+($('art_id').value)+"' id='valor_"+($('art_id').value)+"' value='"+($('art_punit').value)+"'><input type='hidden' name='sugiere_"+$($('art_id').value)+"' id='sugiere_"+($('art_id').value)+"' value='"+$('art_sugiere').value+"'><td style='text-align: right;'><B>"+$('prod_codigo').value+"</B></td><td><span id='articulo_"+($('art_id').value)+"' class='texto_tooltip' >"+$('art_nombre').innerHTML+"</span></td><td style='text-align: right;'><i>"+$('art_stock').innerHTML+"</i></td><td><center><input type='text' size=6 style='text-align: right;' id='cantidad_"+($('art_id').value)+"' name='cantidad_"+($('art_id').value)+"' value='"+$('art_sugerido_val').value+"' onKeyUp='recalcular();' onClick='this.select();'></center></td><td style='text-align: right;'> </td><td><center><img src='iconos/delete.png' style='cursor: pointer;' alt='Quitar Art&iacute;culo de la Lista...' title='Quitar Art&iacute;culo de la Lista...' onClick='eliminar_articulo("+($('art_id').value)+");'></center></td></tr>";
        new Insertion.Bottom('tabla_filas', fila_nueva);
        recalcular();
        cancelar_seleccionar_art();
        $('tabbed_content_overflow').scrollTop=$('tabbed_content_overflow').scrollHeight;

    }

   
    eliminar_articulo = function(art_id)
    {
        tabla_filas = document.getElementById('tabla_filas');
        fila = document.getElementById('fila_'+art_id);
        tabla_filas.removeChild(fila);
        recalcular();
    }
    
    ver_lista = function()
    {
        tab_up('tab_lista');
        tab_down('tab_pedido');
    }
    
    ver_pedido = function()
    {
        tab_down('tab_lista');
        tab_up('tab_pedido');
    }
    
    autocompletar_articulos = new AutoComplete(
      'prod_codigo', 
      'autocompletar_sql.php',
      function() {
        if($('prod_codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts&codigo='+encodeURIComponent($('prod_codigo').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, buscar_codigo_prod);


</script>

<table style='width:100%;' cellpadding=0 cellspacing=0> 
    <tr>
        <td style='height: 20px;'>
            <table cellpadding=0 cellspacing=0>
            <tr>
                <td>
                    <div class='tabs' id='tab_lista' style='cursor: default;' onClick='ver_lista();'>
                        <img src='iconos/application_view_list.png'>
                        Lista de Pedido
                    </div>
                </td>
                <td>
                    <div class='tabs_fade' id='tab_pedido' style='cursor: pointer;' onClick='ver_pedido();'>
                        <img src='iconos/application_form.png'>
                        Datos del Pedido
                    </div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style='height: 310px;'>
        <div class='tabbed_content' id='tabbed_content_overflow' style='height: 310px; overflow:auto;'>
            <div id='tab_lista_content'>
                <table style='width:100%;' class='tabla_informe' id='listado' name='listado' cellspacing=1>
                    <tbody id='tabla_filas' name='tabla_filas'>
                    <tr class='tabla_header' style='font-weight:bold;'>
                        <td style='width: 15%;'>C&oacute;digo Int.</td>
                        <td style='width: 40%;'>Glosa</td>
                        <td style='width: 10%;'>Stock</td>
                        
                        <td><center>Sugerido</center></td>
                       	
                        <td style='width: 10%;'><center>Subtotal</center></td>
                        <td><center>Acciones</center></td>
                    </tr>
                    <?php
                        if($metodo==0)
                        {
                            for($i=0;$i<pg_num_rows($stocks);$i++)
                            {
                                $fila = pg_fetch_row($stocks);
                                $modif=$modglobal;
                                for($u=0;$u<($mods-1);$u++)
                                {
                                    if($fila[7]==$modifica[$u][0])
                                        $modif+=$modifica[$u][1];
                                }
                                ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
                                $sugerido=(($fila[9]*1)-($fila[3]*1))+(($fila[9]*1)-($fila[3]*1))/100*$modif;
                                if($sugerido<0) $sugerido=0;
                                if($sugerido)
                                {

                                    print("
                                        <tr id='fila_".$fila[0]."' class='".$clase."'
                                        onMouseOver='this.className=\"mouse_over\";'
                                        onMouseOut='this.className=this.clase;'>
                                        <input type='hidden' name='valor_".$fila[0]."' id='valor_".$fila[0]."'
                                        value='".$fila[8]."'>
                                        <input type='hidden' name='sugiere_".$fila[0]."' id='sugiere_".$fila[0]."'
                                        value='".floor($sugerido)."'>
                                        <td style='text-align: right;'><B>".$fila[1]."</B></td>
                                        <td><span id='articulo_".$fila[0]."' class='texto_tooltip'
                                        >".htmlentities($fila[2])."</span></td>
                                        <td style='text-align: right;'><i>".$fila[3]."</i></td>
                                        
                                        <td><center>
                                        <input type='text' size=6 style='text-align: right;'
                                        id='cantidad_".$fila[0]."' name='cantidad_".$fila[0]."'
                                        value='".floor($sugerido)."'
                                        onKeyUp='recalcular();'
                                        onClick='this.select();'>
                                        </center>
                                        </td>
                                        <td style='text-align: right;'>
                                        </td>
                                        <td><center><img src='iconos/delete.png' style='cursor: pointer;'
                                        alt='Quitar Art&iacute;culo de la Lista...'
                                        title='Quitar Art&iacute;culo de la Lista...'
                                        onClick='eliminar_articulo(".$fila[0].");'></center></td>
                                        </tr>
                                    ");
                                }

                            }
                        }
                    ?>
                    </tbody>
                    <tr id='agregar_articulo' class='tabla_header'>
                        <td style='width: 100px;'>
                            <input type='hidden' id='art_id' name='art_id' value=0>
                            <input type='hidden' id='art_punit' name='art_punit' value=0>
                            <input type='hidden' id='art_sugiere' name='art_punit' value=0>
                            <input type='hidden' id='art_prioridad' name='art_prioridad' value=0>
                            <input id='prod_codigo' name='prod_codigo' type='text' style='width: 100%;'>
                        </td>
                        <td id='art_nombre' style='text-align: left;'>&nbsp;</td>
                        <td id='art_stock' style='text-align: right;'>&nbsp;</td>
                        
                        <td id='art_sugerido' style='text-align: right;'>
                            <input type='text' id='art_sugerido_val' style='text-align: right; width: 100%;'
                            onKeyUp='if(event.which==13) agregar_articulo(); recalcular_art_nuevo();'>
                        </td>
                        
                        <td id='art_subtotal' style='text-align: right;'>$0.-</td>
                        <td>
                            <img src='iconos/add.png' style='cursor: pointer;'
                            alt='Agregar Art&iacute;culo a la Lista...'
                            title='Agregar Art&iacute;culo a la Lista...'
                            onClick='agregar_articulo();'>
                            <img src='iconos/delete.png' style='cursor: pointer;'
                            onClick='cancelar_seleccionar_art();'>
                        </td>
                    </tr>
                    <tr class='tabla_header' style='font-weight:bold;'>
                        <td colspan=4 style='text-align: right;'>Total General:</td>
                        <td id='total' name='total' style='text-align: right;'></td>
                        <td>&nbsp</td>
                    </tr>
                </table>
            </div>
            <div id='tab_pedido_content' style='display: none'>
                <table width=100%>
                    <tr>
                        <td style='text-align: right;'>Fecha de Emisi&oacute;n:</td>
                        <td><b><?php echo date("d-m-Y"); ?></b></td>
                    </tr>
                    <tr>
                        <td style='text-align: right;' valign='top'>Or&iacute;gen:</td>
                        <td style='font-weight: bold;'>
                            <?php echo $bod_origen_nam; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: right;' valign='top'>Destino:</td>
                        <td style='font-weight: bold;'>
                            <?php echo $bod_destino_nam; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: right;' valign='top'>Comentarios:</td>
                        <td>
                            <textarea id='comentarios' name='comentarios'
                            cols=60 rows=2></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </td>
    </tr>
    <tr style='height: 40px;'>
        <td>
            <center>
                <table>
                    <tr>
                        <td>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/accept.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='verificar_pedido();'>Ingresar Pedido...</a>
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
                                            <img src='iconos/delete.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='limpiar_formulario();'>
                                                Limpiar Formulario...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>

<script> recalcular(); </script>
