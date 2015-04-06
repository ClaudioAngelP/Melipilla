<?php
    require_once('../../conectar_db.php');
?>
<table width=100%>
    <tr class="tabla_header" style="font-weight: bold;">
        <td>C&oacute;digo ERP</td>
        <td width=70%>Descripci&oacute;n Producto</td>
        <td>Forma Presentaci&oacute;n</td>
        <td>P. Unit.</td>
        <td>Cantidad Referencial</td>
        <td>Acciones</td>
    </tr>
    <?php  
    
    $conv_id = ($_GET['convenio_id']*1);
    $convenio = pg_query($conn, "SELECT * FROM convenio LEFT JOIN proveedor USING (prov_id) LEFT JOIN funcionario USING (func_id) WHERE convenio_id=".$conv_id."");
    $conv_data = pg_fetch_assoc($convenio);
    $detalle = pg_query($conn, "SELECT * FROM convenio_detalle JOIN articulo USING (art_id) LEFT JOIN bodega_forma ON forma_id=art_forma WHERE convenio_id=".$conv_id."");
    for($i=0;$i<pg_num_rows($detalle);$i++)
    {
        $articulo = pg_fetch_assoc($detalle);
        ($i%2==1)? $clase='tabla_fila': $clase='tabla_fila2';
        print('
        <tr class="'.$clase.'" onMouseOver="this.clase=this.className; this.className=\'mouse_over\';" onMouseOut="this.className=this.clase;">
            <td style="text-align: right;">
                <b>'.htmlentities($articulo['art_codigo']).'</b>
            </td>
            <td>
                '.htmlentities($articulo['art_glosa']).'
            </td>
            <td>'.htmlentities($articulo['forma_nombre']).'</td>
            <td style="text-align:right;">$'.number_format($articulo['conveniod_punit'],0,',','.').'.-</td>
            <td style="text-align:right;">'.number_format($articulo['conveniod_cant'],0,',','.').'</td>
            <td>
                <center>
                    <img src="iconos/link_break.png" style="cursor: pointer;" onClick="quitar_articulo('.($articulo['art_id']*1).');" alt="Quitar Art&iacute;culo del Convenio..." title="Quitar Art&iacute;culo del Convenio...">
                </center>
            </td>
        </tr>
        ');
    }
    $modificaciones = pg_query("SELECT to_char(conv_fecha_digitacion, 'DD/MM/YYYY HH24:MI:SS') as conv_fecha_digitacion, 
    convd_tipo, conv_resp_aprueba, conv_fecha_aprueba, convd_monto,convd_plazo, convd_plazo2, func_nombre
    FROM convenio_modificaciones 
    LEFT JOIN funcionario ON func_id=convd_func_adm
    WHERE convenio_id=$conv_id");
    
    $mods='<table width="100%"><tr class=\"tabla_header\"><td>Fecha</td><td>Tipo</td><td>N&deg; Aprueba</td><td>Fecha Aprueba</td><td>Plazo Entrega (Dias)</td><td>Monto</td><td>Adm. Contrato</td></tr>';
  
    for($n=0;$n<pg_num_rows($modificaciones);$n++)
    {
        $mod_det = pg_fetch_assoc($modificaciones);
        ($n%2==1)? $clase='tabla_fila': $clase='tabla_fila2';
        $mods.= '<tr class='.$clase.' onMouseOver="this.clase=this.className; this.className=\"mouse_over\";" onMouseOut="this.className=this.clase;"><td style="text-align: center;"><b>'.$mod_det['conv_fecha_digitacion'].'</b></td> <td style="text-align: center;">'.strtoupper($mod_det['convd_tipo']).'</td><td style="text-align: center;">'.$mod_det['conv_resp_aprueba'].'</td><td style="text-align: center;">'.$mod_det['conv_fecha_aprueba'].'</td><td td style="text-align: center;">'.$mod_det['convd_plazo2'].'</td><td style="text-align: center;">'.$mod_det['convd_monto'].'</td><td style="text-align: left;">'.$mod_det['func_nombre'].'</td></tr>';
    }
    $mods.='</table>';
    
    
    ?>
    <tr class="tabla_fila" id="agregar_articulo">
        <td>
            <center>
                <input type="hidden" id="art_id" name="art_id" value=0>
                <input type='text' id='codigo' name='codigo' size=12 style='font-size:11px;'>
            </center>
        </td>
        <td id='art_nombre'></td>
        <td id='art_forma'></td>
        <td>
            <input type='text' id='conveniod_punit' name='conveniod_punit' value='' size=12 style='font-size:11px;text-align:right;' onKeyUp='if(event.which==13) $("conveniod_cant").focus();' />
        </td>
        <td>
            <input type='text' id='conveniod_cant' name='conveniod_cant' value='' size=12 style='font-size:11px;text-align:right;' onKeyUp='if(event.which==13) insertar_articulo();' />
        </td>
        <td>
            <center>
                <img src="iconos/link_add.png" style="cursor: pointer;" onClick="insertar_articulo();" alt="Agregar Art&iacute;culo al Convenio..." title="Agregar Art&iacute;culo al Convenio...">
            </center>
        </td>
    </tr>
</table>
<script>
    autocompletar_medicamentos = new AutoComplete(
    'codigo', 
    'autocompletar_sql.php',
    function() {
    if($('codigo').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=buscar_arts&'+$('codigo').serialize()
    }
    }, 'autocomplete', 350, 200, 250, 1, 3, abrir_articulo);

    
    
    $('convenio_id').value='<?php echo htmlentities($conv_data['convenio_id']); ?>';
    
    $('categoria').value='<?php echo $conv_data['convenio_categoria']; ?>';
    $('tipo_lic').value='<?php echo $conv_data['convenio_tipo_licitacion']; ?>';
	
    $('convenio_licitacion').value='<?php echo htmlentities($conv_data['convenio_licitacion']); ?>';
	
    $('nombre_convenio').value='<?php echo htmlentities($conv_data['convenio_nombre']); ?>'.unescapeHTML();
	
    $('res_aprueba').value='<?php echo htmlentities($conv_data['convenio_nro_res_aprueba']); ?>';
    $('fecha_aprueba').value='<?php echo htmlentities($conv_data['convenio_fecha_aprueba']); ?>';

    $('res_adjudica').value='<?php echo htmlentities($conv_data['convenio_nro_res_adjudica']); ?>';
    $('fecha_adjudica').value='<?php echo htmlentities($conv_data['convenio_fecha_adjudica']); ?>';

    $('res_contrato').value='<?php echo htmlentities($conv_data['convenio_nro_res_contrato']); ?>';
    $('fecha_contrato').value='<?php echo htmlentities($conv_data['convenio_fecha_resolucion']); ?>';

    $('res_prorroga').value='<?php echo htmlentities($conv_data['convenio_nro_res_prorroga']); ?>';
    $('fecha_prorroga').value='<?php echo htmlentities($conv_data['convenio_fecha_prorroga']); ?>';
    $('sel_aprueba').value='<?php echo $conv_data['convenio_aprueba']; ?>';
    $('sp_sel_aprueba').innerHTML="<b><?php echo strtoupper($conv_data['convenio_aprueba']); ?></b>";
	
    $('resp_apr_prorroga').value='<?php echo htmlentities($conv_data['convenio_nrores_prorroga']); ?>';
    $('fecha_nro_prorroga').value='<?php echo htmlentities($conv_data['convenio_fecha_resprorroga']); ?>';

    $('res_aumento').value='<?php echo htmlentities($conv_data['convenio_nro_res_aumento']); ?>';
    $('fecha_aumento').value='<?php echo htmlentities($conv_data['convenio_fecha_aumento']); ?>';
    $('monto_aumento').value='<?php echo htmlentities($conv_data['convenio_monto_aumento']); ?>';

    $('id_proveedor').value='<?php echo htmlentities($conv_data['prov_id']); ?>';
    $('rut_proveedor').value='<?php echo htmlentities($conv_data['prov_rut']); ?>';
    $('nombre_proveedor').value='<?php echo htmlentities($conv_data['prov_glosa']); ?>'.unescapeHTML();

    $('func_id').value='<?php echo htmlentities($conv_data['func_id']); ?>';
    $('rut_funcionario').value='<?php echo htmlentities($conv_data['func_rut']); ?>';
    $('nombre_funcionario').value='<?php echo htmlentities($conv_data['func_nombre']); ?>'.unescapeHTML();

    $('mails').value='<?php echo htmlentities($conv_data['convenio_mails']); ?>';

    $('monto').value='<?php echo htmlentities($conv_data['convenio_monto']); ?>';
    $('plazo').value='<?php echo htmlentities($conv_data['convenio_plazo']); ?>';
	
    $('plazo2').value='<?php echo htmlentities($conv_data['convenio_plazo2']); ?>';

    $('tipo_garantia').value='<?php echo $conv_data['convenio_tipo_licitacion']; ?>';
    $('inicio').value='<?php echo $conv_data['convenio_fecha_inicio']; ?>';
    $('termino').value='<?php echo htmlentities($conv_data['convenio_fecha_final']); ?>';
    
    $('tipo_garantia').value='<?php echo $conv_data['convenio_tipo_garantia']; ?>';
    $('nro_boleta').value='<?php echo htmlentities($conv_data['convenio_nro_boleta']); ?>';
    $('banco_boleta').value='<?php echo htmlentities($conv_data['convenio_banco_boleta']); ?>';
    $('fecha_boleta').value='<?php echo htmlentities($conv_data['convenio_fecha_boleta']); ?>';
    $('monto_boleta').value='<?php echo htmlentities($conv_data['convenio_monto_boleta']); ?>';

    <?php 
		$cadena_sin_saltos=ereg_replace('[[:space:]]+',' ',$conv_data['convenio_multa']);   
    ?>

    $('multa').value= '<?php echo $cadena_sin_saltos; ?>';
    
    <?php 
		$comentarios_sin_saltos=ereg_replace('[[:space:]]+',' ',$conv_data['convenio_comentarios']);   
    ?>
    
    $('comenta').value='<?php echo $comentarios_sin_saltos; ?>';
    
    $('t_aumento').value='<?php echo htmlentities($conv_data['convenio_aumento_aprueba']); ?>';
    $('b_aumento').value='<?php echo htmlentities($conv_data['convenio_aumento_fecha']); ?>';
	
    $('historico').innerHTML='<?php echo $mods; ?>';
    
    
    
    validacion_fecha($('inicio'));
    
    validacion_fecha($('termino'));
    validacion_fecha($('fecha_contrato'));
    validacion_fecha($('fecha_boleta'));
    validacion_fecha($('fecha_aprueba'));
    validacion_fecha($('fecha_adjudica'));
    validacion_fecha($('b_aumento'));
    validacion_fecha2($('fecha_prorroga'));
    validacion_fecha2($('fecha_aumento'));
    
    muestra_campos();
	
    <?php
        if($conv_id*1==0)
        {
    ?>
            
            $('convenio_licitacion').select();
            $('convenio_licitacion').focus();
            //$('tr_adjuntos').style.display='none';
            //$('bt_adjunto').style.display='none';
            
    <?php
        }
        else
        {
    ?>
            /*
            $('tr_adjuntos').style.display='';
            $('bt_adjunto').style.display='';
            */
    <?php
        }
    ?>
</script>
