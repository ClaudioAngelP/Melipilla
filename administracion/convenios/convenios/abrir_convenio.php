<?php

  require_once('../../conectar_db.php');
  
?>

  <table width=100%>
  
  <tr class="tabla_header" style="font-weight: bold;">
  <td>C&oacute;digo Interno</td>
  <td width=70%>Nombre del Art&iacute;culo</td>
  <td>P. Unit.</td>
  <td>Plazo</td>
  <td>Acciones</td>
  </tr>  

<?php  
  
  $conv_id = ($_GET['convenio_id']*1);
  
  $convenio = pg_query($conn, "
  SELECT * FROM convenio
  LEFT JOIN proveedor USING (prov_id)
  LEFT JOIN funcionario USING (func_id)
  WHERE convenio_id=".$conv_id."
  ");
  
  $conv_data = pg_fetch_assoc($convenio);
  
  $detalle = pg_query($conn, "
  SELECT * 
  FROM
  convenio_detalle
  JOIN articulo USING (art_id)
  WHERE convenio_id=".$conv_id."
  ");
  
  for($i=0;$i<pg_num_rows($detalle);$i++) {
    
    $articulo = pg_fetch_assoc($detalle);
    
    ($i%2==1)? $clase='tabla_fila': $clase='tabla_fila2';
    
    print('
    <tr class="'.$clase.'"
    onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
	onMouseOut="this.className=this.clase;">
    <td style="text-align: right;">
    <b>'.htmlentities($articulo['art_codigo']).'</b></td>
    <td>
    '.htmlentities($articulo['art_nombre']).'
    </td>
    <td style="text-align:right;">$'.number_format($articulo['conveniod_punit'],0,',','.').'.-</td>
    <td style="text-align:right;">'.number_format($articulo['conveniod_plazo_entrega'],0,',','.').'</td>
    <td>
    <center>
    <img src="iconos/link_break.png" style="cursor: pointer;"
    onClick="quitar_articulo('.($articulo['art_id']*1).');"
    alt="Quitar Art&iacute;culo del Convenio..."
    title="Quitar Art&iacute;culo del Convenio...">
    </center>
    </td>
    </tr>
    ');
    
  }
  
?>

  <tr class="tabla_fila" id="agregar_articulo">
  <td>
  <center>
  
  <input type="hidden" id="art_id" name="art_id" value=0>
  
  <input type='text' id='codigo' name='codigo' size=12 style='font-size:11px;'>
     
  </center>
  </td><td id='art_nombre'>
    
  </td><td>
    <input type='text' id='conveniod_punit' name='conveniod_punit' value='' size=12 style='font-size:11px;text-align:right;' onKeyUp='if(event.which==13) insertar_articulo();' />
  </td><td>
    <input type='text' id='conveniod_plazo' name='conveniod_plazo' value='' size=12 style='font-size:11px;text-align:right;' onKeyUp='if(event.which==13) insertar_articulo();' />
  </td>
  <td>
  <center>
  <img src="iconos/link_add.png" style="cursor: pointer;"
  onClick="insertar_articulo();"
  alt="Agregar Art&iacute;culo al Convenio..."
  title="Agregar Art&iacute;culo al Convenio...">
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
	
	$('convenio_licitacion').value='<?php echo htmlentities($conv_data['convenio_licitacion']); ?>';
	
	$('nombre_convenio').value='<?php echo htmlentities($conv_data['convenio_nombre']); ?>';
	
	$('res_aprueba').value='<?php echo htmlentities($conv_data['convenio_nro_res_aprueba']); ?>';
	$('anio_aprueba').value='<?php echo htmlentities($conv_data['convenio_nro_anio_aprueba']); ?>';

	$('res_adjudica').value='<?php echo htmlentities($conv_data['convenio_nro_res_adjudica']); ?>';
	$('anio_adjudica').value='<?php echo htmlentities($conv_data['convenio_nro_anio_adjudica']); ?>';

	$('res_contrato').value='<?php echo htmlentities($conv_data['convenio_nro_res_contrato']); ?>';
	$('fecha_contrato').value='<?php echo htmlentities($conv_data['convenio_fecha_resolucion']); ?>';

	$('id_proveedor').value='<?php echo htmlentities($conv_data['prov_id']); ?>';
	$('rut_proveedor').value='<?php echo htmlentities($conv_data['prov_rut']); ?>';
	$('nombre_proveedor').value='<?php echo htmlentities($conv_data['prov_glosa']); ?>'.unescapeHTML();

	$('func_id').value='<?php echo htmlentities($conv_data['prov_id']); ?>';
	$('rut_funcionario').value='<?php echo htmlentities($conv_data['func_rut']); ?>';
	$('nombre_funcionario').value='<?php echo htmlentities($conv_data['func_nombre']); ?>'.unescapeHTML();

	$('mails').value='<?php echo htmlentities($conv_data['convenio_mails']); ?>';

	$('monto').value='<?php echo htmlentities($conv_data['convenio_monto']); ?>';
	$('plazo').value='<?php echo htmlentities($conv_data['convenio_plazo']); ?>';

	$('inicio').value='<?php echo htmlentities($conv_data['convenio_fecha_inicio']); ?>';
	$('termino').value='<?php echo htmlentities($conv_data['convenio_fecha_final']); ?>';
	
	
	$('nro_boleta').value='<?php echo htmlentities($conv_data['convenio_nro_boleta']); ?>';
	$('banco_boleta').value='<?php echo htmlentities($conv_data['convenio_banco_boleta']); ?>';
	$('fecha_boleta').value='<?php echo htmlentities($conv_data['convenio_fecha_boleta']); ?>';
	$('monto_boleta').value='<?php echo htmlentities($conv_data['convenio_monto_boleta']); ?>';

	$('multa').value='<?php echo htmlentities($conv_data['convenio_multa']); ?>'.unescapeHTML();
	$('comenta').value='<?php echo htmlentities($conv_data['convenio_comentarios']); ?>'.unescapeHTML();

	validacion_fecha($('inicio'));
	validacion_fecha($('termino'));
	validacion_fecha($('fecha_contrato'));
	validacion_fecha($('fecha_boleta'));

	
	<?php if($conv_id*1==0) { ?>
		
		$('convenio_licitacion').select();
		$('convenio_licitacion').focus();
		
	<?php } ?>

</script>
