<?php

  require_once('../../conectar_db.php');
  
  
  $lista_autorizaciones = pg_query($conn,
  "
  SELECT * FROM autorizacion_farmacos
  ORDER BY autf_nombre
  "
  );
  
  print('
  <table width=100%>
  <tr class="tabla_header">
  <td><b>Nombre Autorizaci&oacute;n</b></td>
  <td colspan=3><b>Acciones</b></td>
  </tr>
  <tr class="tabla_fila" id="autorizacion_nuevo_boton"
  onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
  onMouseOut="this.className=this.clase;">
  <td colspan=3><i>Agregar Autorizaci&oacute;n de F&aacute;rmacos Nueva...</i></td>
  <td colspan=3>
  <center>
  <img src="iconos/database_add.png" style="cursor: pointer;"
  alt="Agregar Autorizaci&oacute;n..."
  title="Agregar Autorizaci&oacute;n..."
  onClick="abrir_autorizacion(0);">
  </center>
  </td>
  </tr>
  <tr class="tabla_fila" id="autorizacion_nuevo" style="display: none;"
  onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
  onMouseOut="this.className=this.clase;">
  <td>
  <input type="text" id="autorizacion_nombre" name="convenio_nombre"
  style="width:100%;"></td>
  <td>
  <center>
  <img src="iconos/database_add.png" style="cursor: pointer;"
  alt="Agregar Autorizaci&oacute;n..."
  title="Agregar Autorizaci&iacute;n..."
  onClick="guardar_autorizacion();">
  </center>
  </td>
  <td>
  <center>
  <img src="iconos/database_delete.png" style="cursor: pointer;"
  alt="Cancelar..."
  title="Cancelar..."
  onClick="cancelar_autorizacion();">
  </center>
  </td>
  <td>
  &nbsp;
  </td>
  </tr>
  ');
  
  for($i=0;$i<pg_num_rows($lista_autorizaciones);$i++) {
    
    $autorizacion = pg_fetch_assoc($lista_autorizaciones);
    
    ($i%2==1)? $clase='tabla_fila': $clase='tabla_fila2';
    
    print('
    <tr class="'.$clase.'" style="height:30px;"
    onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
    onMouseOut="this.className=this.clase;">
    <td>
    '.htmlentities($autorizacion['autf_nombre']).'
    </td>
    <td>
    <center>
    <img src="iconos/database_edit.png" style="cursor: pointer;"
    alt="Editar Autorizaci&oacute;n..."
    title="Editar Autorizaci&oacute;n..."
    onClick="editar_autorizacion('.$autorizacion['autf_id'].');">
    </td>
    <td>
    <center>
    <img src="iconos/database_link.png" style="cursor: pointer;"
    alt="Editar Art&iacute;culos Relacionados..."
    title="Editar Art&iacute;culos Relacionados..."
    onClick="abrir_autorizacion('.$autorizacion['autf_id'].');"></td>
    <td>
    <center>
    <img src="iconos/database_delete.png" style="cursor: pointer;"
    alt="Eliminar Convenio..."
    title="Eliminar Convenio..." onClick="eliminar_autorizacion('.$autorizacion['autf_id'].');">
    </center>
    </td>
    </tr>
    ');
    
  }
  
  print('</table>');
?>
