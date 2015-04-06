<?php

  require_once('../../conectar_db.php');
  
  $prov_id=$_POST['prov_id']*1;
  
  if($prov_id!=0) {
	  $prov_w="prov_id=$prov_id";
  } else {
	  $prov_w='true';
  }
  
  $lista_convenios = pg_query($conn,
  "
  SELECT * FROM convenio 
  JOIN proveedor USING (prov_id)
  WHERE $prov_w
  ORDER BY convenio_nombre
  "
  );
  
  print('
  <table width=100%>
  <tr class="tabla_header">
  <td><b>ID Licitaci&oacute;n</b></td>
  <td><b>Nombre del Convenio</b></td>
  <td><b>Proveedor</b></td>
  <td colspan=3><b>Acciones</b></td>
  </tr>
  <tr class="tabla_fila" id="convenio_nuevo_boton"
  onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
  onMouseOut="this.className=this.clase;">
  <td colspan=3><i>Agregar Convenio Nuevo...</i></td>
  <td colspan=3>
  <center>
  <img src="iconos/database_add.png" style="cursor: pointer;"
  alt="Agregar Convenio..."
  title="Agregar Convenio..."
  onClick="abrir_convenio(0);">
  </center>
  </td>
  </tr>
  <tr class="tabla_fila" id="convenio_nuevo" style="display: none;"
  onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
  onMouseOut="this.className=this.clase;">
  <td>
  <input type="text" id="convenio_nombre" name="convenio_nombre"
  style="width:100%;"></td>
  <td>
  <center>
  <img src="iconos/database_add.png" style="cursor: pointer;"
  alt="Agregar Convenio..."
  title="Agregar Convenio..."
  onClick="guardar_convenio();">
  </center>
  </td>
  <td>
  <center>
  <img src="iconos/database_delete.png" style="cursor: pointer;"
  alt="Cancelar..."
  title="Cancelar..."
  onClick="cancelar_convenio();">
  </center>
  </td>
  <td>
  &nbsp;
  </td>
  </tr>
  ');
  
  for($i=0;$i<pg_num_rows($lista_convenios);$i++) {
    
    $convenio = pg_fetch_assoc($lista_convenios);
    
    ($i%2==1)? $clase='tabla_fila': $clase='tabla_fila2';
    
    print('
    <tr class="'.$clase.'" style="height:30px;"
    onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
    onMouseOut="this.className=this.clase;">
    <td style="font-weight:bold;text-align:right;">
    '.htmlentities($convenio['convenio_licitacion']).'
    </td><td>
    '.htmlentities($convenio['convenio_nombre']).'
    </td><td>
    '.htmlentities($convenio['prov_glosa']).'
    </td>
    <td>
    <center>
    <img src="iconos/database_edit.png" style="cursor: pointer;"
    alt="Editar Convenio..."
    title="Editar Convenio..."
    onClick="editar_convenio('.$convenio['convenio_id'].');">
    </td>
    <td>
    <center>
    <img src="iconos/database_link.png" style="cursor: pointer;"
    alt="Editar Art&iacute;culos Relacionados..."
    title="Editar Art&iacute;culos Relacionados..."
    onClick="abrir_convenio('.$convenio['convenio_id'].');"></td>
    <td>');
    

    if($convenio['convenio_id']!=1) {
    print('
    <center>
    <img src="iconos/database_delete.png" style="cursor: pointer;"
    alt="Eliminar Convenio..."
    title="Eliminar Convenio..."onClick="eliminar_convenio('.$convenio[0].');">
    </center>');
    } else {
    print('&nbsp;');
    }
    
    print('
    </td>
    </tr>
    ');
    
  }
  
  print('</table>');
?>
