<?php

die();

require_once('../conectar_db.php');


if($_GET['accion']=='listar_parientes' or 
$_GET['accion']=='listar_parientes_estatico') {

    $pac_id = $_GET['paciente']*1;

    $sql=
    "SELECT 
    Relaciones.pac_id_relacionado AS id, 
    pacientes.pac_rut as run, 
	  pacientes.pac_nombres AS Nombres, 
    pacientes.pac_appat AS Paterno, 
    pacientes.pac_apmat AS Materno, 
    tipo_relaciones.rel_desc1 AS Relacion, 
    tipo_relaciones.rel_cod,
    0 AS tipo,
    0 AS real_pac_id
    FROM pacientes 
    RIGHT JOIN 
    (Relaciones INNER JOIN tipo_relaciones 
    ON Relaciones.rel_cod = tipo_relaciones.rel_cod) 
    ON pacientes.pac_id = Relaciones.pac_id_relacionado 
    WHERE Relaciones.pac_id=".$pac_id." 
    
    UNION 
    
    SELECT 
    Relaciones.pac_id_relacionado AS id, 
    pacientes.pac_rut as run, 
    pacientes.pac_nombres, 
    pacientes.pac_appat AS Paterno, 
    pacientes.pac_apmat AS Materno, 
    tipo_relaciones.rel_desc2 AS Relacion, 
    tipo_relaciones.rel_cod ,
    1 AS tipo,
    relaciones.pac_id AS real_pac_id
    FROM pacientes 
    RIGHT JOIN 
    (Relaciones INNER JOIN tipo_relaciones ON Relaciones.rel_cod = 
	  tipo_relaciones.rel_cod) 
    ON pacientes.pac_id = Relaciones.pac_id  
    WHERE Relaciones.pac_id_relacionado=".$pac_id;
    
    
	  $personal = pg_query($conn,$sql);
	   
    if($_GET['accion']=='listar_parientes') {
    
    $relacioneshtml = desplegar_opciones("tipo_relaciones", 
	   "rel_cod, rel_desc1",'','true', 'ORDER BY rel_cod');

    
    print("
	  <table width=100%>
		  <tr class='tabla_header' style='font-weight: bold;'>
			<td>Rut</td>
			<td>Nombre</td>
			<td>Relaci&oacute;n</td>
			<td>Acciones</td>
			</tr>
      <tr class='tabla_header' id='pariente_nuevo_2'>
			<td colspan=3 style='text-align: center;'>
      <b>Agregar Enlace de Parentezco Nuevo</b>
      </td>
			<td><center><img src='iconos/link_add.png'
      onClick='agregar_pariente();' style='cursor: pointer;'
      alt='Agregar Enlace de Parentezco...' 
      title='Agregar Enlace de Parentezco...'
      ></center></td>
			</tr>
			<tr class='tabla_header' id='pariente_nuevo'
      style='display: none;'>
      <td>
      <input style='width: 100%; text-align: right; font-size: 10px;'
      id='pariente_rut' name='pariente_rut' 
      onBlur='ver_pariente();'
      onKeyUp='
      if(event.which==13) {
        ver_pariente();
      }
      '>
      </td>
      <td style='text-align: left;'>
      <input type='hidden' id='pariente_id' name='pariente_id' value=''>
      <span id='pariente_nombre'>&nbsp;</span></td>
      <td>
      <select style='width: 100%; font-size: 10px;'
      id='pariente_relacion' name='pariente_relacion'>
      ".$relacioneshtml."</select>
      </td>
      <td><img src='iconos/add.png'
      onClick='guardar_pariente();'
      alt='Guardar Enlace de Parentezco...' style='cursor: pointer;'
      title='Guardar Enlace de Parentezco...'>
      
      <img src='iconos/delete.png' style='cursor: pointer;'
      onClick='cancelar_agregar_pariente();'
      alt='Cancelar Enlace de Parentezco...'
      title='Cancelar Enlace de Parentezco...'></td>
      </tr>
    ");
    } else 
	  print("
	  <table width=100%>
		  <tr class='tabla_header' valign='top' style='font-weight: bold;'>
			<td>Rut</td>
			<td>Nombre</td>
			<td>Relaci&oacute;n</td>
			<td>Ver</td>
			</tr>");
		
    $subcla="";
	   
     for($i=0;$i<pg_num_rows($personal);$i++) {
		    $datos=pg_fetch_row($personal);
		    if(($i%2)==0) {
				  $clase='tabla_fila';
		    } else {
				  $clase='tabla_fila2';
		    }
		    if($datos[7]) $tooltip = $datos[8]; else $tooltip=$datos[0];
        
        print("
		    <tr class='$clase'		
		    onMouseOver='this.className=\"mouse_over\"'
		    onMouseOut='this.className=\"$clase\"'>
        <td style='text-align: right;'>
        ".$datos[1]."
        </td>
        <td style='text-align: left;'>
        <span id='pac_tooltip_".$tooltip."'>
        ".htmlentities($datos[2])." 
        ".htmlentities($datos[3])." 
        ".htmlentities($datos[4])."
        </span>
        </td>
		    <td style='text-align:left;'>
        ".htmlentities($datos[5])."
        </td>
		    ");
		    
		    if($_GET['accion']=='listar_parientes')
        print("  
        <td><center>
        <img src='iconos/link_break.png' style='cursor: pointer;'
        onClick='quitar_pariente(".$pac_id.", ".$datos[6].", ".$tooltip.")'
        alt='Quitar Enlace...'
        title='Quitar Enlace...'>
        </center>
        </td>
        </tr>");
        else 
        print ("
        <td><center>
        <img src='iconos/link_go.png' style='cursor: pointer;'
        onClick='abrir_pariente(\"".$datos[1]."\");'
        alt='Abrir Ficha del Pariente...'
        title='Abrir Ficha del Pariente...'>
        </center>
        </td>
        </tr>");
        
	   }
	
    print("</table>");
	
	}

if($_GET['accion']=='agregar_pariente') {
	
  $pac_id			         = $_GET['paciente']*1;
	$rel_cod			       = $_GET['relacion']*1;
	$pac_id_relacionado	 = $_GET['pariente']*1;
	
  $sql = "
  INSERT INTO
  relaciones 
  (pac_id, rel_cod, pac_id_relacionado) 
  VALUES 
  (
  ".$pac_id.",
  ".$rel_cod.",
  ".$pac_id_relacionado."
  );";	
  
  $ejecuta = pg_query($conn,$sql);

}

if($_GET['accion']=='eliminar_pariente') {

  $pac_id		          =   $_GET['paciente']*1;
	$rel_cod	          =   $_GET['relacion']*1;
	$pac_id_relacionado	=   $_GET['pariente']*1;
	
  $sql = "
  DELETE FROM 
  relaciones 
  WHERE pac_id=".$pac_id." 
  AND rel_cod=".$rel_cod." 
  AND pac_id_relacionado=".$pac_id_relacionado;	
  
	$ejecuta = pg_query($conn,$sql);
	
  $sql = "
  DELETE FROM 
  relaciones 
  WHERE pac_id=".$pac_id_relacionado." 
  AND rel_cod=".$rel_cod." 
  AND pac_id_relacionado=".$pac_id;
  
  	
	$ejecuta = pg_query($conn,$sql);
		
}

if($_GET['accion']=='ver_pariente') {

  $pariente_rut = $_GET['pariente'];

  $campos = pg_query("
  SELECT pac_id, pac_nombres || ' ' || pac_appat || ' ' || pac_apmat 
  FROM pacientes WHERE pac_rut='".$pariente_rut."' LIMIT 1");

  $datos = pg_fetch_row($campos);
		
		for($i=0;$i<count($datos);$i++) {
			$datos[$i]=htmlentities($datos[$i]);
		}
		
		if (count($datos) > 1) {
		
			print(json_encode($datos));
	
		}

}


?>
