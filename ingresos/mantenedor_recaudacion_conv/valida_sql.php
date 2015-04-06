<?php

	  require_once('../../conectar_db.php');

		$presta_codigo = $_POST['presta_codigo'];
		$mod = $_POST['mod'];
		$modalidad = $_POST['modalidad'];
		
		//if($_POST['prod_activado']=='true') $activado='true'; else $activado='false';
		if($mod==$modalidad){//update
		print('2');
		}else{
			//insert validar que no exista
			$confirmar = pg_query($conn, "SELECT * FROM codigos_prestacion WHERE codigo='".$presta_codigo."' AND modalidad='".$modalidad."';");
			if(pg_num_rows($confirmar)==0){
				//no existe se puede insertar
				print('1');
			}else{
				//existe, no se puede insertar ni updatear
				print('0');
			}
		}
   
			
			
		
      
      
      
		  
      
    

		


?>
