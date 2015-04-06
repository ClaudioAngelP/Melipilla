<?php

  require_once('../../conectar_db.php');
  
?>

  <table width=100%>
  
  <tr class="tabla_header" style="font-weight: bold;">
  <td>C&oacute;digo Interno</td>
  <td width='70%'>Nombre del Art&iacute;culo</td>
  <td width='10%'>Prestaci&oacute;n</td>
  <td>Acciones</td>
  </tr>  

<?php  
  
  $autf_id = ($_GET['autf_id']*1);
  
  $autorizacion = pg_query($conn, "
  SELECT * FROM autorizacion_farmacos
  WHERE autf_id=".$autf_id."
  ");
  
  $aut_data = pg_fetch_assoc($autorizacion);
  
  $detalle = pg_query($conn, "
  SELECT * 
  FROM
  autorizacion_farmacos_detalle
  JOIN articulo USING (art_id)
  WHERE autf_id=".$autf_id."
  ORDER BY art_glosa
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
    '.htmlentities($articulo['art_glosa']).'
    </td>
    <td style="text-align:center;">
    '.htmlentities($articulo['autf_codigo_presta']).'
    </td>
    <td>
    <center>
    <img src="iconos/link_break.png" style="cursor: pointer;"
    onClick="quitar_articulo('.($articulo['art_id']*1).');"
    alt="Quitar Art&iacute;culo..."
    title="Quitar Art&iacute;culo...">
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
    
  </td>
  <td>
  <center><input type='text' id='presta' name='presta' size=12 style='font-size:11px;'></center>
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

	$('autf_id').value='<?php echo htmlentities($aut_data['autf_id']); ?>';
	
	$('nombre_autorizacion').value='<?php echo htmlentities($aut_data['autf_nombre']); ?>'.unescapeHTML();
	
	$('autf_validar').checked=<?php if($aut_data['autf_validar']=='t') echo 'true'; else echo 'false'; ?>;
	
	$('pat_ges').value='<?php echo htmlentities($aut_data['autf_patologia_ges']); ?>'.unescapeHTML();


</script>
