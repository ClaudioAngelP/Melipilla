<?php
    require_once("../../conectar_db.php");
    $ficha_inicial=pg_escape_string($_POST['ficha']);
    $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
    $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
?>
<script type="text/javascript" >
    tipo_destino = function()
    {
        if($('select_tipo_destino').value=="-1")
        {
            
            $('destino_masivo').disabled=true;
            //$('pac_rut').style.display='block';
            //$('txt_paciente').style.display='none';
            
        }
        else
        {
            
            $('destino_masivo').disabled=false;
            if($('select_tipo_destino').value=="1")
            {
            
            }
            else
            {
                
            }
            
            //$('pac_rut').style.display='none';
            //$('txt_paciente').style.display='block';
        }
        //limpiar_paciente();
    }
    
</script>
<html>
    <div class="sub-content">
        <input type="hidden" id="ficha_inicial"  name="ficha_inicial" value="<?php echo $ficha_inicial;?>"/>
        <table style="font-size:12px;width: 100%;">
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Tipo Destino:</td>
                <td class='tabla_fila'>
                    <select name='select_tipo_destino' id='select_tipo_destino' TABINDEX="1" onchange="tipo_destino();">
                        <option value="-1">(Seleccionar Tipo Destino...)</option>
                        <option value="1">Especialidades</option>
                        <option value="2">Servicios</option>
                        <?php
                            echo $bodegashtml2; 
                            echo $servicioshtml2
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Destino de Envio:</td>
                <td class='tabla_fila'>
                    <select name='destino_masivo' id='destino_masivo' TABINDEX="2" disabled>
                        <option value="-1">(Seleccionar Destino Envio...)</option>
                        <?php
                            //echo $bodegashtml2; 
                            //echo $servicioshtml2
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Motivo Solicitud:</td>
		<td class='tabla_fila'>
                    <select id='amp_id' name='amp_id' style='font-size:12px;'>
                        <option value=''>(Especifique el motivo de su solicitud...)</option>
                        <?php 
                            $amp=cargar_registros_obj("SELECT * FROM archivo_motivos_prestamo ORDER BY amp_id;", true);
                            for($i=0;$i<sizeof($amp);$i++) {
				print("<option value='".$amp[$i]['amp_id']."'>".$amp[$i]['amp_nombre']."</option>");
                            }
                        ?>
                    </select>
		</td>
            </tr>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>
                    <img src='abastecimiento/hoja_cargo/barras.png' />
                </td>
                <td class='tabla_fila'>
                    <input type='text' id='barras_list' name='barras_list' size=25 style='font-size:16px;text-align:center;' onKeyUp='if(event.which==13) pistolear_ficha_list();' onFocus='this.style.border="3px dashed red";this.select();' onBlur='this.style.border="";this.style.background="";' />
                </td>
            </tr>
        </table>
    </div>
    <div class='sub-content2' id='list_masivo' name='list_masivo' style='height:300px;overflow:auto;'>
        
    </div>
    <center>
        <table>
            <tr>
                <td>
                    <div class='boton' style="min-width: 100px;">
                        <center>
                            <table>
                                <tr>
                                    <td>
                                        <img src='iconos/page_white_swoosh.png'>
                                    </td>
                                    <td>
                                        <a href='#' onClick='enviar_masivo();'>Enviar</a>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </div>
                </td>
                <td>
                    <div class='boton' style="min-width: 100px;">
                        <table>
                            <tr>
                                <td>
                                    <img src='iconos/cancel.png'>
                                </td>
                                <td>
                                    <a href='#' onClick='limpiar_list();'>Limpiar Lista</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </center>
</html>