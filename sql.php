<?php

	// Script de Querys SQL
	// Sistema de Gestión
	// Hospital San Martín de Quillota
	// ========================================================================
	// Rodrigo Carvajal J.
	// Soluciones Computacionales Viña del Mar LTDA.
	// ========================================================================

	require_once('conectar_db.php');
		
	if($_GET['accion']=='ingreso_edicion_art') {
	
		$id_art = $_GET['id_articulo'];
		$glosa = iconv("UTF-8", "ISO-8859-1", $_GET['prod_glosa']);
		$nombre = iconv("UTF-8", "ISO-8859-1", $_GET['prod_nombre']);
		$item = ($_GET['prod_item']);
		$clasifica = ($_GET['prod_clasif']*1);
		$forma = ($_GET['prod_forma']*1);
		$vence = ($_GET['prod_vence']*1);
		$_GET['prod_auge']*1==1	 	 	? $auge='true' 			: $auge='false'; 
		$_GET['prod_controlado']*1==1 	? $controlado='true' 	: $controlado='false';
			
		if($id_art!=0) {
		
			// Edición de Artículos
			
			pg_query($conn, "
			
			UPDATE articulo
			SET
			art_nombre='$nombre',
			art_glosa='$glosa',
			art_vence='$vence',
			art_clasifica_id=$clasifica,
			art_forma=$forma,
			art_auge=$auge,
			art_controlado=$controlado,
			art_item='$item'
			WHERE art_id=$id_art
			
			");
			
		
		} else {
		
			// Ingreso de Artículos nuevos
		
			$codigo = $_GET['prod_codigo'];
		
			pg_query($conn, "
			
			INSERT INTO articulo
			VALUES (
			DEFAULT,
			'$codigo',
			$vence,
			'$glosa',
			'$nombre',
			$forma,
			$auge,
			$clasifica,
			$controlado,
			'$item'
			)
			");
		
		}
		
		print('1');
	
	}
			
	if($_GET['accion']=='costo_nuevo') {
	
		$nodo = $_GET['nodo'];
		$nuevo = iconv("UTF-8", "ISO-8859-1", $_GET['nuevo']);
		
		$nuevotrans=preg_replace('@[^a-z0-9]@', '', strtolower($nuevo));
		
		$comprobar = pg_query($conn, "
		
		SELECT * FROM centro_costo WHERE centro_ruta ~ '^$nodo.$nuevotrans'
		
		");
		
		if(pg_num_rows($comprobar)>0) {
		
			printf("La rama que est&aacute; tratando de ingresar ya &eacute;xiste en el nivel
			actual.");
			
		} else {
		
			pg_query($conn, "
		
			INSERT INTO centro_costo
			VALUES (
			'$nodo'||'.'||'$nuevotrans',
			'$nuevo'
			)
			");
			
		}
	
	}
	
	if($_GET['accion']=='costo_editar') {
	
		$nodo = $_GET['nodo'];
		$nuevo = iconv("UTF-8", "ISO-8859-1", $_GET['nuevo']);
		
		$nuevaruta=preg_replace('@[^a-z0-9]@', '', strtolower($nuevo));
		$nuevonodo=preg_replace('@(\.|^)[a-z0-9]+$@','.', strtolower($nodo)).$nuevaruta;
		
		$nuevonodoereg = str_replace(".", "\.", $nuevonodo);
		
		$comprobar = pg_query($conn, "
		
		SELECT * FROM centro_costo WHERE centro_ruta ~ '^$nuevonodoereg$'
		
		");
		
		if(pg_num_rows($comprobar)==0) {
		
			$nodoereg = str_replace(".", "\.", $nodo);
		
			// Cambia el nombre de la Rama Editada, junto con su ruta...
			
			pg_query($conn, "
		
			UPDATE centro_costo
			SET 
			centro_nombre='$nuevo', 
			centro_ruta=regexp_replace(centro_ruta, '^$nodoereg','$nuevonodo')
			WHERE centro_ruta ~ '^$nodoereg$'
			
			");
			
			// Repara las rutas de las ramas interiores...
			
			pg_query($conn, "
		
			UPDATE centro_costo
			SET 
			centro_ruta=regexp_replace(centro_ruta, '^$nodoereg','$nuevonodo')
			WHERE centro_ruta ~ '^$nodoereg\.'
			
			");
			
		} else {
		
			print("La rama especificada ya existe en el nivel actual.");
		
		}
	
	}
	
	if($_GET['accion']=='costo_eliminar') {
		
		$nodo = $_GET['nodo'];
		
		$nodoereg = str_replace(".", "\.", $nodo);
		
		pg_query($conn, "
		
		DELETE FROM centro_costo
		WHERE centro_ruta ~ '^$nodoereg'
		
		");
		
	}
        
        if($_GET['accion']=='costo_winsig')
        {
            
            $costo_winsig = iconv("UTF-8", "ISO-8859-1", $_GET['campo_winsig']);
            $nodo = $_GET['nodo'];
            if($costo_winsig!="")
            {
                pg_query($conn, "UPDATE centro_costo set centro_winsig='$costo_winsig' where centro_ruta='$nodo'");
            }
            else
            {
                pg_query($conn, "UPDATE centro_costo set centro_winsig=null where centro_ruta='$nodo'");
            }
	}
        
        
	
	if($_GET['accion']=='stock_critico_guardar') {

		$articulo = ($_GET['id']*1);
		$bodega = ($_GET['bodega']*1);
		$pedido = $_GET['pedido_'.$articulo]*1;
		$critico = $_GET['critico_'.$articulo]*1;
		$gasto = $_GET['gasto_'.$articulo]*1;
		
		pg_query($conn, "DELETE FROM stock_critico WHERE critico_art_id=$articulo AND critico_bod_id=$bodega");
		
		pg_query($conn, "
		INSERT INTO stock_critico VALUES 
    ($articulo, $pedido, $critico, $bodega, $gasto)
		");
		
		print("OK");
		
	}

	
	if($_GET['accion']=='interconsulta') {
	
		$folio=$_GET['nro_folio'];
		$institucion=$_GET['institucion'];
		$especialidad=$_GET['inter_especialidad'];
		$fundamentos=iconv("UTF-8", "ISO-8859-1", $_GET['inter_funda']);
		$examenes=iconv("UTF-8", "ISO-8859-1", $_GET['inter_examen']);
		$comentarios=iconv("UTF-8", "ISO-8859-1", $_GET['inter_comenta']);
		$diagnostico=iconv("UTF-8", "ISO-8859-1", $_GET['diag_cod']);
		
		$id=($_GET['paciente_id']*1);
		
		// Comprueba el Numero de Folio...
		
		$comprobar = pg_query($conn,"
    		SELECT * FROM interconsulta WHERE
    		inter_folio=$folio AND inter_instsol=$institucion;
    	");
    
    	if(pg_num_rows($comprobar)==1) {
     		 exit('N&uacute;mero de Folio previamente ingresado al sistema.');
    	}
		
		// Ingreso de Paciente...
		
		if($id==0) {
    
		$rut=$_GET['paciente_rut'];
		$nombre=iconv("UTF-8", "ISO-8859-1", $_GET['paciente_nombre']);
		$paterno=iconv("UTF-8", "ISO-8859-1", $_GET['paciente_paterno']);
		$materno=iconv("UTF-8", "ISO-8859-1", $_GET['paciente_materno']);
		$fechanac=$_GET['paciente_fecha'];
		$sexo=$_GET['paciente_sexo'];
		$prev=$_GET['paciente_prevision'];
		$grupo=$_GET['paciente_grupo'];
		$comuna=$_GET['paciente_sangre'];
		$direccion=$_GET['paciente_dire'];
		$sangre=$_GET['paciente_comuna'];
		
    pg_query($conn,"
    INSERT INTO pacientes VALUES
		(
		DEFAULT,
		'$rut',
		'$nombre',
		'$paterno',
		'$materno',
		'$fechanac',
		$sexo,
		$prev,
		'',
		$grupo,
		$sangre,
		'$direccion',
		$comuna,
		0
		);
		");
		
		$id="CURRVAL('pacientes_pac_id_seq')";
		
		}
		
		// Ingreso de Interconsulta...
		
    pg_query($conn,"
		INSERT INTO interconsulta VALUES
		(
		$folio,
		$institucion,
		$especialidad,
		0,
		'$fundamentos',
		'$examenes',
		'$comentarios',
		$id,
		0,
		0,
		DEFAULT, DEFAULT, DEFAULT, DEFAULT,
		'$diagnostico'
		);
		");
	
		print("OK");
	
	}
	
	if($_GET['accion']=='guardar_resolucion') {
	
		$folio=($_GET['folio']*1);
		$institucion=($_GET['institucion']*1);
		$estado=$_GET['estado'];
		
    $prioridad=$_GET['prioridad'];
		$observa=iconv("UTF-8", "ISO-8859-1",$_GET['observaciones']);
		
		pg_query($conn,"
		UPDATE interconsulta SET inter_estado=$estado, inter_rev_med='$observa'
		WHERE inter_folio=$folio AND inter_instsol=$institucion; 
		");
		
		if($estado==1) {
    
    $datos = pg_query($conn,"
    SELECT 
    inter_especialidad,
    inter_pac_id, inter_diag
    FROM 
    interconsulta
    WHERE 
    inter_folio=$folio AND inter_instsol=$institucion;
    ");
    
    $data = pg_fetch_row($datos);
    
    $especialidad = $data[0];
    $paciente = $data[1];
    $diag = pg_escape_string($data[2]);
    
    pg_query($conn,"
    INSERT INTO lista_espera 
    (esp_id,pac_id,list_auge,list_fc_ingreso,list_tipo_pac,
    diag_cod,id_origen,prior_id) VALUES (
    $especialidad,$paciente,0::bit,now(),0::bit,'$diag',$institucion,$prioridad
    )
    ");
    
    }
	
	   
	
		print("OK");
	
	}
	
	if($_GET['accion']=='autorizar_pedido') {
	
	 $id_pedido=$_GET['pedido_id']*1;
	 $valor=$_GET['valor'];
	 
	 if($valor!='true') $valor='false';
	 
	 $pedido = cargar_registro('SELECT * FROM pedido WHERE pedido_id='.$id_pedido);
	 
	 if(_func_permitido(29,$pedido['origen_bod_id']) OR 
          _func_permitido_cc(29,$pedido['origen_centro_ruta'])) {
	    pg_query($conn, "
      UPDATE pedido SET pedido_autorizado=$valor WHERE pedido_id=$id_pedido
      ");
      pg_query($conn, "
      DELETE FROM pedido_autorizacion WHERE pedido_id=$id_pedido
      ");
      pg_query($conn, "
      INSERT INTO pedido_autorizacion VALUES (
        DEFAULT, $id_pedido, ".($_SESSION['sgh_usuario_id']*1).", now()
      )
      ");
      
      $auto=cargar_registro("
        
        SELECT 
        
        func_nombre, 
        date_trunc('second', pedidoa_fecha) AS fecha 
        
        FROM pedido_autorizacion
        
        LEFT JOIN funcionario ON 
                  pedido_autorizacion.func_id=funcionario.func_id
        WHERE 
                  pedidoa_id=CURRVAL('pedido_autorizacion_pedidoa_id_seq')
        
        ");
      
      print(json_encode(Array   ( true, 
                                  $valor, 
                                  htmlentities($auto['func_nombre']), 
                                  htmlentities($auto['fecha'])
                                )
                        ));
            
   } else {
      print('ERROR: Funcionario no tiene permisos para realizar esta operaci&oacute;n.');
   }
  
  }
		
?>
