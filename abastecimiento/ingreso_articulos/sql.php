<?php

	  require_once('../../conectar_db.php');

		$id_art = $_GET['id_articulo'];
		$glosa = strtoupper(pg_escape_string(trim(iconv("UTF-8", "ISO-8859-1", $_GET['prod_glosa']))));
		$nombre = strtoupper(pg_escape_string(trim(iconv("UTF-8", "ISO-8859-1", $_GET['prod_nombre']))));
		$item = ($_GET['prod_item']);
		$clasifica = ($_GET['prod_clasif']*1);
		$forma = ($_GET['prod_forma']*1);
		$vence = ($_GET['prod_vence']*1);
		$_GET['prod_auge']*1==1	 	 	  ? $auge='true' 			  : $auge='false'; 
		$control = ($_GET['prod_control']*1);
		$prioridad = ($_GET['prod_prioridad']*1);
		if($_GET['prod_activado']=='true') $activado='true'; else $activado='false';
		$codigo = strtoupper(pg_escape_string(trim($_GET['prod_codigo'])));
		$resp='';
    	
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
			art_item='$item',
			art_prioridad_id=$prioridad,
			art_activado=$activado,
			art_control=$control
			WHERE art_id=$id_art
			
			");
			
			$id_articulo=$id_art;
			$resp=$id_articulo."|".$codigo;
		
		} else {
		
			// Ingreso de Artículos nuevos
		
			$codigo = strtoupper(pg_escape_string(trim($_GET['prod_codigo'])));
			
			$confirmar = pg_query($conn, "SELECT * FROM articulo WHERE art_codigo='".$codigo."'");
			
			if(pg_num_rows($confirmar)>0) die('Error al ingreso. C&oacute;digo ya existe en el sistema.');
		
		  if($control>0)  $repo='true';
		  else            $repo='false';
		
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
			$repo,
			'$item',
			0,0,0,0,
			$prioridad,
			$activado,
			$control
			)
			");
			
			$currval = pg_query($conn, "SELECT CURRVAL('articulo_art_id_seq')");
			$currv = pg_fetch_result($currval, 0, 0);
			$id_articulo = $currv;
			$resp=$id_articulo."|".$codigo;
		
		}
		
    // Definición de Stocks Críticos y de Pedido

	$bodegas = pg_query($conn, "
    SELECT bod_id FROM bodega
    ");
    
    for($i=0;$i<pg_num_rows($bodegas);$i++) {
      
      $bod = pg_fetch_row($bodegas);
      $bod_id=$bod[0];
      
      if(isset($_GET['critico_'.$bod[0]])) $critico=$_GET['critico_'.$bod[0]]*1;
      else                                 $critico=0;
      
      if(isset($_GET['pedido_'.$bod[0]]))  $pedido=$_GET['pedido_'.$bod[0]]*1;
      else                                 $pedido=0;
      
      if(isset($_GET['gasto_'.$bod[0]]))  $gasto=$_GET['gasto_'.$bod[0]]*1;
      else                                $gasto=0;
      
      $chk1=(pg_num_rows(pg_query("SELECT * FROM articulo_bodega WHERE artb_art_id=$id_articulo AND artb_bod_id=$bod_id;"))>=1);
      $chk2=(isset($_GET['ver_'.$bod_id]) AND $_GET['ver_'.$bod_id]=='true');
      
      if($chk1 AND !$chk2) {
		  pg_query("DELETE FROM articulo_bodega WHERE artb_art_id=$id_articulo AND artb_bod_id=$bod_id");
	  } elseif ($chk2 AND !$chk1) {
		  pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT, $id_articulo, $bod_id);");
	  }
      
      
      if($critico>0 and $pedido>0 and $pedido>$critico) {
      
        $comprobar = pg_query($conn, 
              "SELECT * FROM stock_critico WHERE critico_art_id=$id_articulo AND critico_bod_id=".$bod[0]);
        
        if(pg_num_rows($comprobar)>0) {
          pg_query($conn, "
            UPDATE stock_critico 
            SET 
            critico_pedido=$pedido,
            critico_critico=$critico,
            critico_gasto=$gasto
            WHERE
            critico_art_id=$id_articulo AND critico_bod_id=".$bod[0]
            );
        } else {
          pg_query($conn, "
            INSERT INTO stock_critico
            VALUES
            ($id_articulo, $pedido, $critico, ".$bod[0].", $gasto)
          ");
        }
      
      } 
      
      if($critico==0 and $pedido==0 and $gasto==0) {
        pg_query($conn, "DELETE FROM stock_critico WHERE critico_art_id=$id_articulo AND critico_bod_id=".$bod[0]);
      }
      
    }
		
	print($resp);


?>
