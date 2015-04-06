<?php

	require_once('../../config.php');
	require_once('../sigh.php');

	
if(isset($_GET['rut']))	{ $tipo=0;     $id = pg_escape_string($_GET['rut']); }
else if(isset($_POST['rut'])) { $tipo=0;     $id = pg_escape_string($_POST['rut']); }
else if(isset($_GET['ficha'])) { $tipo=3;     $id = pg_escape_string($_GET['ficha']); }
else if(isset($_POST['ficha'])) { $tipo=3;     $id = pg_escape_string($_POST['ficha']); }
else exit("ERROR");

	   
	  if($tipo==0) 
  	   $paciente = pg_query($conn,"
       SELECT * FROM pacientes
	LEFT JOIN comunas USING (ciud_id)
	LEFT JOIN prevision USING (prev_id)
       WHERE pac_rut='$id';
       ");
     else if($tipo==3) 
       $paciente = pg_query($conn,"
       SELECT * FROM pacientes
        LEFT JOIN comunas USING (ciud_id)
        LEFT JOIN prevision USING (prev_id)
       WHERE pac_ficha='$id';
       ");  
     else if($tipo==1) 
       $paciente = pg_query($conn,"
       SELECT * FROM pacientes
        LEFT JOIN comunas USING (ciud_id)
        LEFT JOIN prevision USING (prev_id)
       WHERE pac_pasaporte='$id';
       ");
     else
       $paciente = pg_query($conn,"
       SELECT * FROM pacientes
        LEFT JOIN comunas USING (ciud_id)
        LEFT JOIN prevision USING (prev_id)
       WHERE pac_id=$id;
       ");
     
      
     if(pg_num_rows($paciente)>0) {
     
        // Paciente encontrado en base de datos local.
     
        $pac = pg_fetch_assoc($paciente);
        
        foreach($pac AS $key => $val) {
			     $pac[$key]=htmlentities($val);
		    }
		
        print(json_encode($pac));
     
     } else if($tipo==0 or $tipo==3) {
     
      // Paciente deberá ser buscado usando conectores
      // a bases de datos secundarias.
      
       $q=$id;
       
       /*$conectores=scandir('conectores/pacientes/');

       for($i=2;$i<count($conectores);$i++) { 
          include('conectores/pacientes/'.$conectores[$i]);
          if($pac_id!=-1) break;
       }

      if($pac_id==-1) exit();

       $paciente = pg_query($conn,"
       	SELECT * FROM pacientes
       	WHERE pac_id=$pac_id;
       ");
       
       $pac = pg_fetch_row($paciente);
        
        for($i=0;$i<count($pac);$i++) {
			     $pac[$i]=htmlentities($pac[$i]);
		    }

	*/

	function formatear_rut($str) {

		$partes=explode('-',$str);

		return number_format($partes[0]*1,0,',','.').'-'.strtoupper($partes[1]);

	}

	$ch = curl_init();

	if($tipo==0) {

	curl_setopt($ch, CURLOPT_URL, "http://10.5.132.11/produccion/conectores/trakcare/login.php?buscar=".urlencode(formatear_rut($q)));
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$_id=curl_exec($ch);
	
	} else if($tipo==3) {

        curl_setopt($ch, CURLOPT_URL, "http://10.5.132.11/produccion/conectores/trakcare/login.php?buscar2=".urlencode($q));
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $_id=curl_exec($ch);

	
	}

	curl_close($ch);


	if($_id==0) { $pac_id=-1; $pac=false; } else { 

		$pac_id=$_id;

		$paciente = pg_query($conn,"
		        SELECT * FROM pacientes
			        LEFT JOIN comunas USING (ciud_id)
			        LEFT JOIN prevision USING (prev_id)
		        WHERE pac_id=$pac_id;
		       ");
       
		$pac = pg_fetch_assoc($paciente);
        
	        foreach($pac AS $key=>$val) {
                    $pac[$key]=htmlentities($val);
                }

		}

        exit(json_encode($pac));

	}
     
?>
