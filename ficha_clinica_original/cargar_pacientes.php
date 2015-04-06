<?php 

function cargar_paciente($paciente_tipo_id, $paciente_rut) {

	 GLOBAL $conn;

	  if(isset($paciente_tipo_id)) {
       $tipo = $paciente_tipo_id*1;
     } else {
       $tipo = 0;    
     }
     
     if($tipo!=2)
     	$id = pg_escape_string($paciente_rut);
     else
     	$id = pg_escape_string($paciente_rut)*1;     
	   
	   if($tipo==0) 
  	   $paciente = pg_query($conn,"
       SELECT * FROM pacientes
       WHERE pac_rut='$id';
       ");
     else if($tipo==1) 
       $paciente = pg_query($conn,"
       SELECT * FROM pacientes
       WHERE pac_pasaporte='$id';
       ");
     else if($tipo==3) 
       $paciente = pg_query($conn,"
       SELECT * FROM pacientes
       WHERE pac_ficha='$id';
       ");     
     else
       $paciente = pg_query($conn,"
       SELECT * FROM pacientes
       WHERE pac_id=$id AND (pac_rut IS NULL OR pac_rut='');
       ");
     

     $pac_id=-1;
      
     if(pg_num_rows($paciente)>0) {
     
        // Paciente encontrado en base de datos local.
     
        $pac = pg_fetch_row($paciente);

		  if($pac[21]=='t') {
		  	 $q=$id;
		    include( dirname(__FILE__) . '/../conectores/hsmq_sco_informix_actualizar_pacientes.php');
		    if($pac_id!=-1) {
		    	$paciente=pg_query("SELECT * FROM pacientes WHERE pac_id=".$pac[0]);
		    	$pac=pg_fetch_row($paciente);
		    } else {
		    	pg_query("UPDATE pacientes SET pac_actualizar=false WHERE pac_id=".$pac[0]);
		    }
		  }
        
        for($i=0;$i<count($pac);$i++) {
			     $pac[$i]=htmlentities($pac[$i]);
		    }
		
        return $pac;
     
     } else if($tipo==0 OR $tipo==3) {
     
      // Paciente deberá ser buscado usando conectores
      // a bases de datos secundarias.
      
       $q=$id;
       
       $conectores=scandir( dirname(__FILE__) . '/../conectores/pacientes/' );

       for($i=2;$i<count($conectores);$i++) {
          include( dirname( __FILE__ ) . '/../conectores/pacientes/'.$conectores[$i]);
          if($pac_id!=-1) break;
       }

      if($pac_id==-1) return -1;

       $paciente = pg_query($conn,"
       	SELECT * FROM pacientes
       	WHERE pac_id=$pac_id;
       ");
       
       $pac = pg_fetch_row($paciente);
        
        for($i=0;$i<count($pac);$i++) {
			     $pac[$i]=htmlentities($pac[$i]);
		  }
		
        return $pac;
     
     }


}

?>