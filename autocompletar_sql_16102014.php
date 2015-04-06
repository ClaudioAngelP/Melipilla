<?php

  require_once('conectar_db.php');
  
  if($_GET['tipo']=='buscar_meds' or $_GET['tipo']=='buscar_meds_controlados' or $_GET['tipo']=='buscar_arts' or $_GET['tipo']=='buscar_meds_stock' or $_GET['tipo']=='buscar_meds_controlados_stock' or $_GET['tipo']=='buscar_arts_stock') {

  if($_GET['tipo']=='buscar_meds' or $_GET['tipo']=='buscar_meds_stock')
  {
      $condicion="AND (art_item='2204004' OR art_item='2204005001' OR art_item='2204004003' OR art_item='2204005' OR art_item='2204004001' OR art_item='2204003002' OR art_item='2204003001' OR art_id=6543 OR art_id=114829) AND art_control=0";
    } else if ($_GET['tipo']=='buscar_meds_controlados' or $_GET['tipo']=='buscar_meds_controlados_stock') {
      $condicion="AND (art_item='2204004' OR art_item='2204005001' OR art_item='2204004003' OR art_item='2204005' OR art_item='2204004001' OR art_item='2204003002' OR art_item='2204003001' OR art_id=10619 OR art_id=114829) AND (NOT art_control=0)";
    } else {
      $condicion='';
    }

    $cadena = pg_escape_string($_GET['codigo']);

	if($_GET['tipo']=='buscar_meds_stock' or $_GET['tipo']=='buscar_meds_controlados_stock' or $_GET['tipo']=='buscar_arts_stock')

    {
    
      if(isset($_GET['bodega_id'])) {
        
        $bod=$_GET['bodega_id'];
        $obtener_stock = true;
        
        if(strstr($bod, '.')) {
          $bodega = 'calcular_stock_trans_cc(art_id, 
                     \''.pg_escape_string($bod).'\')';
        } else {
          $bodega = 'calcular_stock_trans(art_id, '.($bod*1).')';
        }
        
      } else {
        $obtener_stock = false;
      }

    } else
		$obtener_stock=false;

		$query="

 		SELECT

			art_codigo,
			art_codigo,
			art_glosa,
			forma_nombre";

			if($obtener_stock)
				$query.=', '.$bodega;

			$query.=", art_control, art_id, art_item, item_glosa, art_vence,art_item,art_comentarios

      FROM
			articulo
			
		";
		
		if(isset($_GET['bodega_id'])) {
			$bodega=$_GET['bodega_id']*1;
			$query.="JOIN articulo_bodega ON artb_art_id=articulo.art_id AND artb_bod_id=$bodega";
		}
		
		$query.="

		  LEFT JOIN bodega_forma ON art_forma=forma_id
		  LEFT JOIN item_presupuestario ON art_item=item_codigo
    ";
    
    $query.="
		WHERE (
			(art_codigo || ' ' || art_glosa || ' ' || art_nombre) ILIKE '%$cadena%'
			) AND art_activado 
    $condicion


		ORDER BY art_codigo

		LIMIT 50

		";

		
		
		$listado = pg_query($conn, $query);

  if(pg_num_rows($listado)>0)
  for($i=0; $i<pg_num_rows($listado); $i++) {

    $array[$i]=pg_fetch_row($listado);

    for($u=0;$u<count($array[$i]);$u++) {
      $array[$i][$u]=htmlentities($array[$i][$u]);
    }


  }
  else
  $array='';

  print(json_encode($array));

  }

  if($_GET['tipo']=='medicos') {

    $cadena = pg_escape_string($_GET['nombre_medico']);
    	
    	if(isset($_GET['receta'])){  
    		if($_GET['receta']=='false')  		
   				$recet = 'AND true';
   			else
   				$recet = 'AND doc_recetas';
    	}else
    			$recet = 'AND true';


	$medicos = pg_query($conn, "
    SELECT 
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres, 
    doc_rut, 
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres || '##' || COALESCE(doc_codigo,''), 
    doc_id
    FROM doctores
    WHERE
    doc_codigo = '".trim($cadena)."' ".$recet."
    ORDER BY doc_paterno, doc_materno, doc_nombres
    LIMIT 50
    ");

    if( pg_num_rows($medicos) > 0 ) {
    
	  for($i=0;$i<pg_num_rows($medicos);$i++) {
  
      $array[$i]=pg_fetch_row($medicos);

      for($k=0;$k<count($array[$i]);$k++) {
		if($k!=2) {  
			$array[$i][$k]=htmlentities($array[$i][$k]);
		} else {
			$tmp=explode('##',$array[$i][$k]);			
			$array[$i][$k]=htmlentities($tmp[0]).'</td><td><span style="font-weight:bold;color:#0000ff;">'.$tmp[1].'</span>';
		}
      }
      
	  }

	  exit(json_encode($array));
  
    }
  

    $medicos = pg_query($conn, "
    SELECT 
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres, 
    doc_rut, 
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres || '##' || COALESCE(doc_codigo,''), 
    doc_id
    FROM doctores
    WHERE
    (doc_rut ILIKE '%".$cadena."%'
    OR
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres || ' ' || COALESCE(doc_codigo,'')
    ILIKE '%".$cadena."%'
    
    OR
    doc_paterno || ' ' || doc_nombres
    ILIKE '%".$cadena."%'

    ) ".$recet."

    ORDER BY doc_paterno, doc_materno, doc_nombres

    LIMIT 10
    ");
  
    if(pg_num_rows($medicos)>0)
    
    for($i=0; $i<pg_num_rows($medicos); $i++) {
  
      $array[$i]=pg_fetch_row($medicos);

      for($k=0;$k<count($array[$i]);$k++) {
		if($k!=2) {  
			$array[$i][$k]=htmlentities($array[$i][$k]);
		} else {
			$tmp=explode('##',$array[$i][$k]);			
			$array[$i][$k]=htmlentities($tmp[0]).'</td><td><span style="font-weight:bold;color:#0000ff;">'.$tmp[1].'</span>';
		}
      }
  
    }
    else
    $array='';

    print(json_encode($array));
  
  }

  if($_GET['tipo']=='funcionarios') {

    $cadena = pg_escape_string($_GET['nomfuncio']);

    $funcionarios = pg_query($conn, "
    
    SELECT func_nombre, func_rut, func_nombre, func_id
    FROM funcionario
    WHERE
    func_rut || ' ' || func_nombre
    ILIKE '%".$cadena."%'

    ORDER BY func_nombre
    LIMIT 10
    
    ");
  
    if(pg_num_rows($funcionarios)>0)
    
    for($i=0; $i<pg_num_rows($funcionarios); $i++) {
  
      $array[$i]=pg_fetch_row($funcionarios);
  
      for($k=0;$k<count($array[$i]);$k++) {
        $array[$i][$k]=htmlentities($array[$i][$k]);
      }
  
    }
    else
    $array='';

    print(json_encode($array));
  
  }
  
  if($_GET['tipo']=='funcionarios_npt') {

    $cadena = pg_escape_string($_GET['nomfuncionpt']);

    $funcs = pg_query($conn, "
    
    SELECT fnpt_nombre, fnpt_rut, fnpt_nombre, fnpt_id
    FROM func_npt
    WHERE
    fnpt_rut || ' ' || fnpt_nombre
    ILIKE '%".$cadena."%'

    ORDER BY fnpt_nombre
    LIMIT 50
    
    ");
  
    if(pg_num_rows($funcs)>0)
    
    for($i=0; $i<pg_num_rows($funcs); $i++) {
  
      $array[$i]=pg_fetch_row($funcs);
  
      for($k=0;$k<count($array[$i]);$k++) {
        $array[$i][$k]=htmlentities($array[$i][$k]);
      }
  
    }
    else
    $array='';

    print(json_encode($array));
  
  }


  if($_GET['tipo']=='proveedores') {
  
    $cadena = pg_escape_string($_GET['busca_proveedor']);
  
    $proveedores = pg_query($conn, "
    SELECT 
    prov_rut,
    prov_rut,
    prov_glosa,
    prov_id
    FROM proveedor
    WHERE
    prov_rut ILIKE '%".$cadena."%'
    OR
    prov_glosa ILIKE '%".$cadena."%'
    
    ORDER BY prov_rut, prov_glosa

    LIMIT 10
    ");
  
    if(pg_num_rows($proveedores)>0)
    
    for($i=0; $i<pg_num_rows($proveedores); $i++) {
  
      $array[$i]=pg_fetch_row($proveedores);
  
      for($u=0;$u<count($array[$i]);$u++) {
        $array[$i][$u]=htmlentities($array[$i][$u]);
      }
  
    }
    else
    $array='';

    print(json_encode($array));
  

  
  }

  if($_GET['tipo']=='hospitales'){

	$cadena= pg_escape_string($_GET['cadena']);

	$centros = pg_query ($conn, "SELECT instsol_id, instsol_desc FROM institucion_solicita WHERE instsol_desc ILIKE '%".$cadena."%'
					ORDER BY instsol_desc LIMIT 10
				");

	
	 if(pg_num_rows($centros)>0)  {

      for($i=0; $i<pg_num_rows($centros); $i++) {
        $array[$i]=pg_fetch_row($centros);
        $array[$i][1]=htmlentities($array[$i][1]);
      }

	}else $array='';

	print(json_encode($array));

}
  if($_GET['tipo']=='buscar_items') {
  
    $cadena = pg_escape_string($_GET['cadena']);
  
    $proveedores = pg_query($conn, "
    SELECT 
    item_codigo, item_codigo, item_glosa
    FROM item_presupuestario
    WHERE
    item_codigo ILIKE '%".$cadena."%'
    OR
    item_glosa ILIKE '%".$cadena."%'
    
    ORDER BY item_codigo, item_glosa

    LIMIT 10
    ");
  
    if(pg_num_rows($proveedores)>0)
    
    for($i=0; $i<pg_num_rows($proveedores); $i++) {
  
      $array[$i]=pg_fetch_row($proveedores);
  
      for($u=0;$u<count($array[$i]);$u++) {
        $array[$i][$u]=htmlentities($array[$i][$u]);
      }
  
    }
    else
    $array='';

    print(json_encode($array));
  

  
  }


  if($_GET['tipo']=='pacientes') {
  
    if(isset($_GET['busca_paciente'])) 
      $cadena = pg_escape_string($_GET['busca_paciente']);
    else
      $cadena = pg_escape_string($_GET['nompac']);

	$cadena=trim($cadena);
	while(strstr($cadena,'  ')) 
		$cadena=str_replace('  ', ' ', $cadena);	
	
	$cadena=str_replace(' ', ' & ', $cadena);
    
    $pacientes = pg_query($conn, "
    SELECT 
    pac_rut, 
    pac_rut, 
    pac_appat || ' ' || pac_apmat || ' ' || pac_nombres,
    pac_ficha
    ,pac_id, prev_id, prev_desc, pac_fc_nac,
    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
    date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
    date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
    '' AS edad, prev_id, ciud_id
    FROM pacientes
    LEFT JOIN prevision USING (prev_id)
    WHERE
    case when pac_ficha is null then
	to_tsvector('spanish', pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) @@ to_tsquery('".$cadena."')
    else
	to_tsvector('spanish', pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres || ' ' || pac_ficha) @@ to_tsquery('".$cadena."')
    end
    ORDER BY pac_rut, pac_appat, pac_apmat, pac_nombres
    LIMIT 10
    
    ");
  
    if(pg_num_rows($pacientes)>0)
    
    for($i=0; $i<pg_num_rows($pacientes); $i++) {
  
      $array[$i]=pg_fetch_row($pacientes);
  
      for($u=0;$u<count($array[$i]);$u++) {
        $array[$i][$u]=htmlentities($array[$i][$u]);
      }

		$array[$i][11]='';
      
      if($array[$i][8]*1>1) $array[$i][11].=$array[$i][8].' a&ntilde;os ';
		elseif($array[$i][8]*1==1) $array[$i][11].=$array[$i][8].' a&ntilde;o ';

		if($array[$i][9]*1>1) $array[$i][11].=$array[$i][9].' meses ';	
		elseif($array[$i][9]*1==1) $array[$i][11].=$array[$i][9].' mes ';

		if($array[$i][10]*1>1) $array[$i][11].=$array[$i][10].' d&iacute;as';
		elseif($array[$i][10]*1==1) $array[$i][11].=$array[$i][10].' d&iacute;a';

    }
    else
    $array='';

    print(json_encode($array));



  }
  
  
  
    if($_GET['tipo']=='pacientes_edad')
    {
        if(isset($_GET['busca_paciente']))
            $cadena = pg_escape_string($_GET['busca_paciente']);
	else
            $cadena = pg_escape_string($_GET['nompac']);

	$cadena=trim($cadena);
	
        while(strstr($cadena,'  '))
                $cadena=str_replace('  ', ' ', $cadena);	
	
	$cadena=str_replace(' ', ' & ', $cadena);
        $consulta="
        SELECT 
        pac_rut, 
        pac_rut, 
        pac_appat || ' ' || pac_apmat || ' ' || pac_nombres,
        pac_ficha, 
        ('Edad: [' ||date_part('year',age(now()::date, pac_fc_nac))|| ']') as edad,
        pac_id,
        prev_id, prev_desc, pac_fc_nac,
        date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
	'' AS edad, prev_id, ciud_id
        FROM pacientes
        LEFT JOIN prevision USING (prev_id)
        WHERE
        to_tsvector('spanish', pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres || ' ' || pac_ficha )
	@@ to_tsquery('".$cadena."')
        ORDER BY pac_rut, pac_appat, pac_apmat, pac_nombres
        LIMIT 10";
        
        $pacientes = pg_query($conn, $consulta);
        if(pg_num_rows($pacientes)>0)
            for($i=0; $i<pg_num_rows($pacientes); $i++)
            {
                $array[$i]=pg_fetch_row($pacientes);
                for($u=0;$u<count($array[$i]);$u++)
                {
                    $array[$i][$u]=htmlentities($array[$i][$u]);
                }
		$array[$i][11]='';
                if($array[$i][8]*1>1)
                    $array[$i][11].=$array[$i][8].' a&ntilde;os ';
		elseif($array[$i][8]*1==1)
                    $array[$i][11].=$array[$i][8].' a&ntilde;o ';

		if($array[$i][9]*1>1)
                    $array[$i][11].=$array[$i][9].' meses ';	
		elseif($array[$i][9]*1==1)
                    $array[$i][11].=$array[$i][9].' mes ';

		if($array[$i][10]*1>1)
                    $array[$i][11].=$array[$i][10].' d&iacute;as';
		elseif($array[$i][10]*1==1)
                    $array[$i][11].=$array[$i][10].' d&iacute;a';
            }
    else
        $array='';
    print(json_encode($array));
}  
  
    if($_GET['tipo']=='procedimiento') {

    $cadena = pg_escape_string($_GET['procedimiento']);

    $procmed = pg_query($conn, "SELECT proc_descripcion,
                                       proc_descripcion,
                                       proc_id
                                  FROM procedimiento_medico
                                 WHERE proc_descripcion ILIKE '%".$cadena."%'
                              ORDER BY proc_descripcion
                                 LIMIT 10 ");

    if(pg_num_rows($procmed)>0)  {

      for($i=0; $i<pg_num_rows($procmed); $i++) {
        $array[$i]=pg_fetch_row($procmed);
       /* for($u=0;$u<count($array[$i]);$u++) {
          $array[$i][$u]=htmlentities($array[$i][$u]);
        }*/
      }

    } else { $array=''; }

    print(json_encode($array));

  }


 //*******************************************************************************
 
  if($_GET['tipo']=='doctor') {

    $cadena = pg_escape_string($_GET['nombre_doctor']);

    $docto = pg_query($conn, "
    SELECT
    doc_id,
    doc_rut,
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres,
	doc_codigo
    FROM doctores
    WHERE
    doc_rut ILIKE '%".$cadena."%'
    OR
    doc_paterno ILIKE '%".$cadena."%'
    OR
    doc_materno ILIKE '%".$cadena."%'
    OR
    doc_nombres ILIKE '%".$cadena."%'
    OR
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres || ' ' || doc_codigo
    ILIKE '%".$cadena."%'
    ORDER BY doc_paterno, doc_materno, doc_nombres

    LIMIT 10
    ");

    if(pg_num_rows($docto)>0)

    for($i=0; $i<pg_num_rows($docto); $i++) {

      $array[$i]=pg_fetch_row($docto);
      $array[$i][2]=htmlentities($array[$i][2]);

    }
    else
    $array='';

    print(json_encode($array));

  }
  
  if($_GET['tipo']=='prestacion') {

    $cadena = pg_escape_string(utf8_decode($_GET['cod_presta']));

	

    $procmed = pg_query($conn, "
    SELECT
    codigo,
    codigo,
    glosa
    FROM codigos_prestacion
    WHERE 
    codigo ILIKE '%$cadena%' OR
    glosa ILIKE '%$cadena%'
    LIMIT 10
    ");

	

    if(pg_num_rows($procmed)>0)  {

      for($i=0; $i<pg_num_rows($procmed); $i++) {
        $array[$i]=pg_fetch_row($procmed);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }
  
  if($_GET['tipo']=='carpeta') {
  
  	 $ges=$_GET['ges'];
  	 $servicio=$_GET['centro_ruta'];

    $cadena = pg_escape_string(utf8_decode($_GET['carpeta']));

    $pat = pg_query($conn, "
    SELECT
    carp_id,
    carp_nombre,
    carp_tipo_hosp
    FROM orden_carpeta
    WHERE 
    carp_nombre ILIKE '%$cadena%' AND carp_ges=$ges AND carp_servicio='$servicio'
    ");

    if(pg_num_rows($pat)>0)  {

      for($i=0; $i<pg_num_rows($pat); $i++) {
        $array[$i]=pg_fetch_row($pat);
        $array[$i][1]=htmlentities($array[$i][1]);
        $array[$i][2]=$array[$i][2];
      }

    } else { $array=''; }

    print(json_encode($array));

  }

	if($_GET['tipo']=='carpeta2') {
  
    $cadena = pg_escape_string(utf8_decode($_GET['carpeta']));

    $carp = pg_query($conn, "
    SELECT
    carp_id,
    carp_id,
    carp_nombre
    FROM orden_carpeta
    WHERE 
    carp_nombre ILIKE '%$cadena%';
    ");

    if(pg_num_rows($carp)>0)  {

      for($i=0; $i<pg_num_rows($carp); $i++) {
        $array[$i]=pg_fetch_row($carp);
        $array[$i][2]=htmlentities($array[$i][2]);
        
      }

    } else { $array=''; }

    print(json_encode($array));

  }

    if($_GET['tipo']=='control_patologia')
    {
        $cadena = pg_escape_string(utf8_decode($_GET['pacpat_nueva']));
        $tipo = pg_escape_string(utf8_decode($_GET['bod']));
        if(!isset($_GET['opcion_patologia']))
        {
            $consulta_patologia = 1;
        }
        else
        {
            $consulta_patologia = pg_escape_string(utf8_decode($_GET['opcion_patologia']));
        }
        if($tipo==4)
            $tipo='h';
        else if($tipo==36)
            $tipo='c';
        
        if($consulta_patologia==1)
        {
            $consulta="SELECT cpat_id, cpat_nombre FROM control_patologias WHERE cpat_nombre ILIKE '%$cadena%' AND cpat_tipo='$tipo';";
        }
        else
        {
            $consulta="SELECT autf_id,autf_patologia_ges FROM autorizacion_farmacos where autf_patologia_ges ILIKE '%$cadena%' ORDER BY autf_nombre";
        }
        $pat = pg_query($conn, $consulta);
        
        if(pg_num_rows($pat)>0)
        {
            for($i=0; $i<pg_num_rows($pat); $i++)
            {
                $array[$i]=pg_fetch_row($pat);
                $array[$i][1]=htmlentities($array[$i][1]);  
            }
        }
        else
        {
            $array='';
            
        }
        print(json_encode($array));
    }



  if($_GET['tipo']=='proc_prestacion') {

    $cadena = pg_escape_string(utf8_decode($_GET['cod_presta']));
    $esp_id = pg_escape_string(utf8_decode($_GET['esp_id']));
    /*
    $procmed = pg_query($conn, "
    SELECT
    codigo,
    codigo,
    pc_desc, 
    glosa,
    pc_id
    FROM codigos_prestacion
    JOIN procedimiento_codigo 
    	ON esp_id=$esp_id AND pc_codigo=codigo
    WHERE 
    codigo ILIKE '%$cadena%' OR
    glosa ILIKE '%$cadena%' OR
    pc_desc ILIKE '%$cadena%'
    LIMIT 10
    ");
    */
    $procmed = pg_query($conn, "
    SELECT codigo,
    (case when strpos(pc_codigo, '.')>0 then pc_codigo else codigo end),
    pc_desc, glosa,pc_id FROM codigos_prestacion
    JOIN procedimiento_codigo ON esp_id=$esp_id AND split_part(pc_codigo, '.', 1)=codigo and pc_activo
    WHERE 
    codigo ILIKE '%$cadena%' OR glosa ILIKE '%$cadena%' OR pc_desc ILIKE '%$cadena%'
    order by pc_desc LIMIT 10");


    
    

    if(pg_num_rows($procmed)>0)  {

      for($i=0; $i<pg_num_rows($procmed); $i++) {
      	
        $array[$i]=pg_fetch_row($procmed);
        $array[$i][2]=htmlentities($array[$i][2]);
        
        $array[$i][3]=trim($array[$i][3]);
        
        if(sizeof($array[$i][3])>40) 
        	$array[$i][3]=substr($array[$i][3],0,37).'...';
        	
        $array[$i][3]=htmlentities($array[$i][3]);
        
      }

    } else { $array=''; }

    print(json_encode($array));

  }


  if($_GET['tipo']=='prestacion_patologia') {

    $cadena = pg_escape_string(utf8_decode($_GET['cod_presta']));
    $pat_id = ($_GET['pat_id']*1);

    $procmed = pg_query($conn, "
    SELECT
    codigo,
    codigo,
    glosa
    FROM codigos_prestacion
    WHERE 
    (codigo ILIKE '%$cadena%' OR glosa ILIKE '%$cadena%') 
    AND 
    (codigo IN (SELECT presta_codigo FROM detalle_patauge WHERE pat_id=$pat_id))
    LIMIT 10
    ");

    if(pg_num_rows($procmed)>0)  {

      for($i=0; $i<pg_num_rows($procmed); $i++) {
        $array[$i]=pg_fetch_row($procmed);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='especialidad') {

    $cadena = pg_escape_string(utf8_decode($_GET['esp_desc']));
   
    $query = "SELECT
    esp_id,
    esp_id,
    esp_desc
    FROM especialidades
    WHERE esp_desc ILIKE '%$cadena%'";
    
   if(isset($_GET['esp']))
	 		$query.=" AND esp_padre_id=-1";
	 		
	 $query.=" ORDER BY esp_desc";
	 
	 $esp=pg_query($conn, $query);	    

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='subespecialidad') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));
    $esp_padre = $_GET['esp_padre']*1;
    $w_esp_padre="";
    if($esp_padre==1)
        $w_esp_padre="esp_padre_id>0 AND ";
    
    $esp = pg_query($conn, "
    SELECT
    esp_id,
    esp_id,
    especialidades.esp_desc,
    procedimiento.esp_id AS proc_esp_id
    FROM especialidades
    LEFT JOIN procedimiento USING (esp_id)
    WHERE 
    $w_esp_padre
    especialidades.esp_desc ILIKE '%$cadena%'
    ORDER BY especialidades.esp_desc
    ");

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='subespecialidad_nominas') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

	$query='';

	if(_cax(202)) {
		$query="
		SELECT DISTINCT esp_id, esp_desc FROM nomina
		JOIN especialidades ON nom_esp_id=esp_id
  		WHERE esp_padre_id>0
  		";
	}
	
	if(_cax(202) AND _cax(300)) $query.=' UNION ';	
	
	if(_cax(300)) {
		$query.="SELECT DISTINCT esp_id, esp_desc FROM especialidades
		WHERE esp_id IN ("._cav(300).")";
	}
	
	$query="SELECT
		foo.esp_id, foo.esp_id, 
		foo.esp_desc,  
		procedimiento.esp_id AS proc_esp_id		
		FROM ($query) AS foo 
		LEFT JOIN procedimiento USING (esp_id)	
	    WHERE 
   	 foo.esp_desc ILIKE '%$cadena%'
		ORDER BY foo.esp_desc";	


    $esp = pg_query($conn, $query);

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='instituciones') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

    $esp = pg_query($conn, "
    SELECT
    inst_id,
    inst_id,
    inst_nombre
    FROM instituciones
    WHERE 
    inst_nombre ILIKE '%$cadena%'
    ORDER BY inst_nombre
    ");

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }


  if($_GET['tipo']=='garantias_patologias') {

    $cadena = pg_escape_string(utf8_decode($_GET['pat_desc']));

    $pat = pg_query($conn, "
    
    SELECT * FROM
    
    (SELECT
    ('G' || garantia_id),
    garantia_id,
    garantia_nombre AS nombre
    FROM garantias_atencion
    WHERE 
    garantia_nombre ILIKE '%$cadena%'
    
    UNION
    
    SELECT
    ('P' || pat_id),
    pat_id,
    pat_glosa AS nombre
    FROM patologias_auge
    WHERE 
    pat_glosa ILIKE '%$cadena%') AS foo
    ORDER BY nombre
    
    ");

    if(pg_num_rows($pat)>0)  {

      for($i=0; $i<pg_num_rows($pat); $i++) {
        $array[$i]=pg_fetch_row($pat);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }


  if($_GET['tipo']=='diagnostico') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

    $diag = pg_query($conn, "
    SELECT
    diag_cod,
    diag_cod,
    diag_desc
    FROM diagnosticos
    WHERE 
    diag_cod ILIKE '%$cadena%' OR
    diag_desc ILIKE '%$cadena%'
    LIMIT 50
    ");

    if(pg_num_rows($diag)>0)  {

      for($i=0; $i<pg_num_rows($diag); $i++) {
        $array[$i]=pg_fetch_row($diag);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='diagnostico_tapsa') {

    $cadena = trim(pg_escape_string(utf8_decode($_GET['cadena'])));

	 while(strstr($cadena, '  '))
		str_replace('  ', ' ', $cadena);
	
	 //$cadena=str_replace(' ',' & ',$cadena);	
	
    $diag = pg_query($conn, "

	SELECT
    	nomdiag_codigo,
    	nomdiag_codigo,
    	nomdiag_desc,
        esp_desc
    	FROM diagnosticos_tapsa
        left join especialidades on nomdiag_esp_id=esp_id
    	WHERE
    	plainto_tsquery('spanish', '$cadena') @@ nomdiag_desc_vector
    	or nomdiag_codigo ilike '%$cadena%'
        or esp_desc ilike '%$cadena%'
    	LIMIT 50
    ");

    if(pg_num_rows($diag)>0)  {

      for($i=0; $i<pg_num_rows($diag); $i++) {
        $array[$i]=pg_fetch_row($diag);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='servicios_hospitalizacion') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

    $centros = pg_query($conn, "
    SELECT
    tcama_id,
    tcama_id,
    tcama_tipo
    FROM clasifica_camas
    WHERE 
    tcama_tipo ILIKE '%$cadena%' 
    LIMIT 10
    ");

    if(pg_num_rows($centros)>0)  {

      for($i=0; $i<pg_num_rows($centros); $i++) {
        $array[$i]=pg_fetch_row($centros);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }


  if($_GET['tipo']=='centros_pabellon') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

    $centros = pg_query($conn, "
    SELECT
    centro_ruta,
    centro_ruta,
    centro_nombre
    FROM centro_costo
    WHERE 
    centro_nombre ILIKE '%$cadena%' AND
    centro_ruta ILIKE '.subdireccinmdica.%'
    LIMIT 10
    ");

    if(pg_num_rows($centros)>0)  {

      for($i=0; $i<pg_num_rows($centros); $i++) {
        $array[$i]=pg_fetch_row($centros);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

if($_GET['tipo']=='centro_costo') {



    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));



    $centros = pg_query($conn, "

    SELECT

    centro_ruta,

    centro_ruta,

    centro_nombre

    FROM centro_costo

    WHERE 

    centro_nombre ILIKE '%$cadena%' AND centro_medica

    LIMIT 10

    ");



    if(pg_num_rows($centros)>0)  {



      for($i=0; $i<pg_num_rows($centros); $i++) {

        $array[$i]=pg_fetch_row($centros);

        $array[$i][2]=htmlentities($array[$i][2]);

      }



    } else { $array=''; }



    print(json_encode($array));



  }

  if($_GET['tipo']=='personal_pabellon') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));
    $tipo = $_GET['tpers']*1;

	switch($tipo) {
		case 0: $where="pp_caracteristicas ILIKE '%CIRUGIA%'"; break;	
		case 1: $where="pp_caracteristicas ILIKE '%ANESTESIA%'"; break;	
		case 2: $where="pp_caracteristicas NOT ILIKE '%CIRUGIA%' AND pp_caracteristicas NOT ILIKE '%ANESTESIA%'"; break;	
		/*case 3: $where="pp_caracteristicas ILIKE '%PABEL.%'"; break;	
		case 4: $where="pp_caracteristicas ILIKE '%PERF.%'"; break;	
		case 5: $where="pp_caracteristicas ILIKE '%RAYOS%'"; break;	
		case 6: $where="pp_caracteristicas ILIKE '%ENFERMERA%'"; break;*/	
	}

    $personal = pg_query($conn, "
    SELECT
    pp_id,
    pp_rut,
    pp_paterno,
    pp_materno,
    pp_nombres,
    pp_desc,
    pp_caracteristicas
    FROM personal_pabellon
    WHERE 
    	pp_rut || ' ' || pp_paterno || ' ' || pp_materno || ' ' || pp_nombres 
    		ILIKE '%$cadena%'
		AND		
			$where
    LIMIT 10
    ");

    if(pg_num_rows($personal)>0)  {

      for($i=0; $i<pg_num_rows($personal); $i++) {
        $array[$i]=pg_fetch_row($personal);
        $array[$i][2]=htmlentities($array[$i][2]);
        $array[$i][3]=htmlentities($array[$i][3]);
        $array[$i][4]=htmlentities($array[$i][4]);
        $array[$i][5]='<i>'.htmlentities($array[$i][5]).'</i>';
      }

    } else { $array=''; }

    print(json_encode($array));

  }


?>
