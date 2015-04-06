<?php
    require_once('conectar_db.php');
    if($_GET['tipo']=='buscar_pacs') {
        $buscar = pg_escape_string(trim(utf8_decode($_GET['buscar_pacs_txt'])));
        $cadena=trim($buscar);
	while(strstr($cadena,'  '))
                $cadena=str_replace('  ', ' ', $cadena);	
	
	$cadena=str_replace(' ', '%', $cadena);
	
        $pacientes = pg_query($conn,"
        SELECT 
        pac_rut, pac_appat, pac_apmat, pac_nombres, pac_fc_nac, pac_pasaporte,
        pac_id, pac_ficha
        FROM pacientes
        WHERE 
        (pac_appat || ' ' || pac_apmat || ' ' || pac_nombres )
        ILIKE '%".$cadena."%' OR pac_rut = '$cadena' OR pac_ficha = '$cadena'
        LIMIT 50;
        ");
    
        print("
        <table width='100%' style='font-size:12px;'>
        <tr class='tabla_header' style='font-weight: bold;'>
        <td>R.U.T./ID</td>
        <td>Nro. Ficha</td>
        <td>Apellido Paterno</td>
        <td>Apellido Materno</td>
        <td>Nombre</td>
        <td>Fecha de Nac.</td>
        </tr>
        ");
    
        for($i=0;$i<pg_num_rows($pacientes);$i++) {
            $paciente = pg_fetch_row($pacientes);
            ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
            // Define Código a Enviar en caso de no tener RUT
            if($paciente[0]=='' or $paciente[0]=='*') {
                if($paciente[5]=='') {
                    $id='<span style="color:green;">'.$paciente[6].'</span>';
                    $cod='I'.$paciente[6];
                } else {
                    $id='<span style="color:blue;">'.$paciente[5].'</span>';
                    $cod='P'.$paciente[5];
                }
            } else {
                $id=$paciente[0];
                $cod=$paciente[0];
            }
            print("
            <tr class='$clase' style='font-size: 12px;' onMouseOver='this.className=\"mouse_over\"' onMouseOut='this.className=\"$clase\"' onClick='sel_cod(\"".$cod."\");'>
            <td style='text-align: right;font-weight: bold;'>".$id."</td>
            <td style='text-align:center;font-weight:bold;'>".htmlentities($paciente[7])."</td>
            <td>".htmlentities($paciente[1])."</td>
            <td>".htmlentities($paciente[2])."</td>
            <td>".htmlentities($paciente[3])."</td>
            <td style='text-align: center;'>
            <i>".htmlentities($paciente[4])."</i>
            </td>
            </tr>
            ");
        }
        print("</table>");
    }

  /* EGF */
  if($_GET['tipo']=='pacientes') {

    $buscar = pg_escape_string(trim($_GET['buscar_pacs_txt']));
  
	$cadena=trim($buscar);
	while(strstr($cadena,'  ')) 
		$cadena=str_replace('  ', ' ', $cadena);	
	
	$cadena=str_replace(' ', ' & ', $cadena);
	
    $pacientes = pg_query($conn,"
    SELECT 
    pac_rut, pac_appat, pac_apmat, pac_nombres, pac_fc_nac, pac_pasaporte,
    pac_id, pac_ficha
    FROM pacientes
    WHERE 
        to_tsvector('spanish', pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres || ' ' || pac_ficha )
		@@ to_tsquery('".$cadena."')

    LIMIT 50;
    ");
    
    print("
      <table width='100%' style='font-size:12px;'>
      <tr class='tabla_header' style='font-weight: bold;'>
      <td>R.U.T./ID</td>
      <td>Nro. Ficha</td>
      <td>Apellido Paterno</td>
      <td>Apellido Materno</td>
      <td>Nombre</td>
      <td>Fecha de Nac.</td>
      </tr>
    ");
    
    for($i=0;$i<pg_num_rows($pacientes);$i++) {
    
      $paciente = pg_fetch_row($pacientes);
      ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
      
      // Define Código a Enviar en caso de no tener RUT
      
      if($paciente[0]=='') {
      
        if($paciente[5]=='') {
        
          $id='<span style="color:green;">'.$paciente[6].'</span>';
          $cod='I'.$paciente[6];
        
        } else {
        
          $id='<span style="color:blue;">'.$paciente[5].'</span>';
          $cod='P'.$paciente[5];
        
        }
      
      } else {
      
        $id=$paciente[0];
        $cod=$paciente[0];
      
      }
      
      print("
      <tr class='$clase' style='font-size: 12px;'
      onMouseOver='this.className=\"mouse_over\"'
      onMouseOut='this.className=\"$clase\"'
      onClick='sel_cod(\"".$cod."\");'
      >
      <td style='text-align: right;font-weight: bold;'>
      ".$id."
      </td>
      <td style='text-align:center;font-weight:bold;'>".htmlentities($paciente[7])."</td>
      <td>".htmlentities($paciente[1])."</td>
      <td>".htmlentities($paciente[2])."</td>
      <td>".htmlentities($paciente[3])."</td>
      <td style='text-align: center;'>
      <i>".htmlentities($paciente[4])."</i>
      </td>
      </tr>
      ");

    }

    print("
    </table>
    ");

  }
  /* EGF */

  if($_GET['tipo']=='buscar_funcs') {
  
    $buscar = pg_escape_string($_GET['buscar_funcs_txt']);
  
    $funcionarios = pg_query($conn,"
    SELECT func_rut, func_nombre, func_cargo
    FROM funcionario
    WHERE 
    func_rut ILIKE '%$buscar%' OR
    func_nombre ILIKE '%$buscar%'
    LIMIT 30;
    ");
    
    
    print("
      <table width=100%>
      <tr class='tabla_header' style='font-weight: bold;'>
      <td>RUT</td>
      <td>Nombre</td>
      <td>Cargo</td>
      </tr>
    ");
    
    for($i=0;$i<pg_num_rows($funcionarios);$i++) {
    
      $funcionario = pg_fetch_row($funcionarios);
      ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
      
      print("
      <tr class='$clase' style='font-size: 12px;'
      onMouseOver='this.className=\"mouse_over\"'
      onMouseOut='this.className=\"$clase\"'
      onClick='sel_cod(\"".$funcionario[0]."\");'
      >
      <td style='text-align: right;'>
      <i><b>".htmlentities($funcionario[0])."</b></i>
      </td>
      <td>".htmlentities($funcionario[1])."</td>
      <td>".htmlentities($funcionario[2])."</td>
      </tr>
      ");
      
    }
    
    print("
    </table>
    ");
  
  }

  if($_GET['tipo']=='buscar_docs') {
  
    $buscar = pg_escape_string($_GET['buscar_funcs_txt']);
  
    $doctores = pg_query($conn,"
    SELECT doc_rut, doc_paterno || ' ' || doc_materno || ' ' || doc_nombres
    FROM doctores
    WHERE 
    doc_rut ILIKE '%$buscar%' OR
    (doc_paterno || ' ' || doc_materno || ' ' || doc_nombres) ILIKE '%$buscar%'
    LIMIT 30;
    ");
    
    
    print("
      <table width=100%>
      <tr class='tabla_header' style='font-weight: bold;'>
      <td>RUT</td>
      <td>Nombre</td>
      </tr>
    ");
    
    for($i=0;$i<pg_num_rows($doctores);$i++) {
    
      $doctor = pg_fetch_row($doctores);
      ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
      
      print("
      <tr class='$clase' style='font-size: 12px;'
      onMouseOver='this.className=\"mouse_over\"'
      onMouseOut='this.className=\"$clase\"'
      onClick='sel_cod(\"".$doctor[0]."\");'
      >
      <td style='text-align: right;'>
      <i><b>".htmlentities($doctor[0])."</b></i>
      </td>
      <td>".htmlentities($doctor[1])."</td>
      </tr>
      ");
      
    }
    
    print("
    </table>
    ");
  
  }

  if($_GET['tipo']=='buscar_diag') {

    $buscar=pg_escape_string($_GET['buscar']);
    
    $diags = pg_query($conn,"
    SELECT
    diag_cod,
    diag_desc
    FROM diagnosticos
    WHERE
    diag_cod ILIKE '%$buscar%'
    OR
    diag_desc ILIKE '%$buscar%'
    LIMIT 50
    ");
    
    print("
    <table width=100%>
    <tr class='tabla_header' style='font-weight: bold; font-size: 12px;'>
    <td>C&oacute;digo</td>
    <td>Descripci&oacute;n</td>
    </tr>
    ");
    
    for($i=0;$i<pg_num_rows($diags);$i++) {
    
    $fila=pg_fetch_row($diags);
    ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
  
    
    print("
    <tr class='$clase' style='font-size: 12px;'
    onMouseOver='this.className=\"mouse_over\"'
    onMouseOut='this.className=\"$clase\"'
    onClick='sel_cod(\"".$fila[0]."\");'
    >
    <td><b><i>".$fila[0]."</i></b></td>
    <td><i>".htmlentities($fila[1])."</i></td>
    </tr>
    ");
    
    }

    print("</table>");

  }
  
  if($_GET['tipo']=='buscar_meds' OR $_GET['tipo']=='buscar_meds_controlados'
    OR $_GET['tipo']=='buscar_arts') 
    {
	
		if($_GET['tipo']=='buscar_meds') {
      $condicion="AND art_item='2204004001' AND art_controlado IS FALSE";
    } else if ($_GET['tipo']=='buscar_meds_controlados') {
      $condicion="AND art_item='2204004001' AND art_controlado IS TRUE";
    } else {
      $condicion='';
    }
    
    $cadena = pg_escape_string($_GET['buscar_meds_txt']);
		
		$listado = pg_query($conn, "
		
 		SELECT 
			
			art_id,
			art_codigo,
			art_glosa,
			forma_nombre
			
			FROM 
			articulo
		
		  LEFT JOIN bodega_forma ON art_forma=forma_id
		
		WHERE (
			art_codigo ILIKE '%$cadena%'
			OR 
			art_glosa ILIKE '%$cadena%'
			OR
			art_nombre ILIKE '%$cadena%'
		) 
    $condicion
			
		
		ORDER BY art_codigo
		
		");
		
		if(pg_num_rows($listado)) {
		
		print("<center>
		<table width=100% class='tabla_informe'>
    <tr class='tabla_header' style='font-size: 12px;'>
		<td class='tabla_header' width=80><b><i>C&oacute;digo</i></b></td>
		<td width=150><b><i>Glosa Producto</i></b></td>
		<td><b><i>Forma Farm.</i></b></td>
		</tr>
		");
		
		$alterna=0;
		
			for($i=0;$i<pg_num_rows($listado);$i++) {
			
			$fila=pg_fetch_row($listado);
			
			if($alterna==0) {
				$clase='tabla_fila';
				$alterna=1;
			} else {
				$clase='tabla_fila2';
				$alterna=0;
			}
			
			print("
			<tr class='".$clase."'
			onClick='sel_cod(\"".$fila[1]."\");'
			onMouseOver='this.className=\"mouse_over\"'
			onMouseOut='this.className=\"".$clase."\"'
			>");
			
			print("
			<td style='text-align: right; font-size: 12px;'>
			<i><b>".$fila[1]."</b></i></td>
			<td>
      <span class='texto_tooltip'>
      ".htmlentities($fila[2])."
      </span>
      </td>
			<td>
      ".htmlentities($fila[3])."
      </td>
			
      </tr>");
			
			}
		
			print('</table></center>');
	
		} else {
		
      print("
    		<center>
	     	(No se ha encontrado el texto especificado...)
		    </center>
      ");
      
    }
		
	}

?>


