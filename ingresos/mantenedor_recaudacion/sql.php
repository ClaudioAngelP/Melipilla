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
		
		$pab = ($_POST['pab']);
		//if($_POST['prod_activado']=='true') $activado='true'; else $activado='false';
    $canasta = pg_escape_string( $_POST['canasta']);

	$convenio = strtoupper(pg_escape_string($_POST['convenio']));
		
    	$confirmar = pg_query($conn, "SELECT * FROM codigos_prestacion WHERE codigo='".$presta_codigo."' AND tipo='".$modalidad."';");
			
		if(pg_num_rows($confirmar)==0)    
		{
			
				pg_query($conn, "
				
				INSERT INTO codigos_prestacion
				VALUES (
				'$presta_codigo',
				'$glosa',
				'$modalidad',
				$anio,			
				$tot_mai,
				$tot_mai2,
				$cop_a,
				$cop_b,
				$cop_c,
				$cop_d,
	      '$pab',
		'$canasta',
		'$convenio',
		FALSE
				)
			");
			$A='2';
		}else{
		if($mod==$modalidad) {
		
			// Edici�n de Art�culos
			
			pg_query($conn, "
			UPDATE codigos_prestacion
			SET
			glosa='$glosa',
			anio=$anio,
			tipo='$modalidad',
			precio=$tot_mai,
			transferen=$tot_mai2,
			copago_a=$cop_a,
			copago_b=$cop_b,
			copago_c=$cop_c,
			copago_d=$cop_d,
      pab='$pab',
	canasta='$canasta',
	convenios='$convenio'
			WHERE codigo='$presta_codigo' AND tipo='$mod'		
			");
			
			$A='3';
			
		
		} else {
		
			
				die('Error al ingreso. C&oacute;digo ya existe en el sistema.');
			
		
	   
		}
		}
		
    // Definici�n de Stocks Cr�ticos y de Pedido

//	$Array['finish']='1';
	print($A);
		


?>
