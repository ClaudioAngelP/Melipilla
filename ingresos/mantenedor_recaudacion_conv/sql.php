<?php

	  require_once('../../conectar_db.php');

		$presta_codigo = strtoupper($_POST['presta_codigo']);
		$mod = $_POST['mod'];
		
		$glosa = strtoupper(pg_escape_string(trim(iconv("UTF-8", "ISO-8859-1", $_POST['prod_glosa']))));

		$modalidad = $_POST['modalidad'];
		$anio = ($_POST['anio']*1);
		$tot_mai = ($_POST['val_tot_mai']*1);
		$tot_mai2 = ($_POST['val_tot_mai_2']*1);
		$cop_a= ($_POST['cop_a']*1);
		$cop_b= ($_POST['cop_b']*1);
		$cop_c= ($_POST['cop_c']*1);
		$cop_d= ($_POST['cop_d']*1);
		$rut_med=strtoupper($_POST['doc_rut']);
		$pab = ($_POST['pab']);
		$doc_id = ($_POST['doc_id']*1);
		//if($_POST['prod_activado']=='true') $activado='true'; else $activado='false';
    $canasta = pg_escape_string( $_POST['canasta']);

	$convenio = strtoupper(pg_escape_string($_POST['convenio']));
	$porc_med = ($_POST['porc_med']*1);
	$porc_crs = ($_POST['porc_crs']*1);
		
		
    	$confirmar = pg_query($conn, "SELECT * FROM codigos_prestacion_convenio WHERE codigo='".$presta_codigo."' AND tipo='".$modalidad."' AND convenios='".$rut_med."';");
		
		if(pg_num_rows($confirmar)==0)    
		{
			
				pg_query($conn, "
				
				INSERT INTO codigos_prestacion_convenio
				VALUES (
				'$presta_codigo',
				'$glosa',
				'mai',
				$anio,			
				$tot_mai,
				$tot_mai2,
				$cop_a,
				$cop_b,
				$cop_c,
				$cop_d,
	      '$pab',
		'$canasta',
		'$rut_med',
		FALSE,
		$doc_id,
		$porc_crs,
		$porc_med
				)
			");
			pg_query($conn, "
				
				INSERT INTO codigos_prestacion_convenio
				VALUES (
				'$presta_codigo',
				'$glosa',
				'mle',
				$anio,			
				$tot_mai,
				$tot_mai2,
				$cop_a,
				$cop_b,
				$cop_c,
				$cop_d,
	      '$pab',
		'$canasta',
		'$rut_med',
		FALSE,
		$doc_id,
		$porc_crs,
		$porc_med
				)
			");
			$A='2';
		}else{
		
		
			// Edici�n de Art�culos
			
			pg_query($conn, "
			UPDATE codigos_prestacion_convenio
			SET
			glosa='$glosa',
			anio=$anio,
			tipo='mai',
			precio=$tot_mai,
			transferen=$tot_mai2,
			copago_a=$cop_a,
			copago_b=$cop_b,
			copago_c=$cop_c,
			copago_d=$cop_d,
      pab='$pab',
	canasta='$canasta',
		porc_crs=$porc_crs,
		porc_doc=$porc_med
			WHERE codigo='$presta_codigo' AND tipo ='mai'	AND convenios='$rut_med'
			");
			
			pg_query($conn, "
			UPDATE codigos_prestacion_convenio
			SET
			glosa='$glosa',
			anio=$anio,
			tipo='mle',
			precio=$tot_mai,
			transferen=$tot_mai2,
			copago_a=$cop_a,
			copago_b=$cop_b,
			copago_c=$cop_c,
			copago_d=$cop_d,
      pab='$pab',
	canasta='$canasta',
		porc_crs=$porc_crs,
		porc_doc=$porc_med
			WHERE codigo='$presta_codigo' AND tipo ='mle'	AND convenios='$rut_med'
			");
			
			$A='3';
			
		
		
			
		
	   
		
		}
		
    // Definici�n de Stocks Cr�ticos y de Pedido

//	$Array['finish']='1';
	print($A);
		


?>
