<?php 

	require_once('../conectar_db.php');
	
	$bod_id=13;
	
	$f=explode("\n", utf8_decode(file_get_contents('funcionarios.csv')));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=="") continue;
	
		$r=explode('|',$f[$i]);
		
		$rut=trim($r[1].'-'.$r[2]);
		$nombre=ucwords(trim($r[3]));
		$cargo=trim($r[4].' '.$r[7]);
		$clave=substr($rut,0,4);
    $tipo=$r[4];
    
    $chk=cargar_registro("SELECT * FROM funcionario WHERE func_rut='$rut'");
    
    if($chk) continue;
    
		pg_query("INSERT INTO funcionario VALUES (default, '$rut', '$nombre', '$cargo', md5('$clave'));");
    
    if($tipo=='MEDICOS') {
      $name=explode(' ',$nombre);
      
      $pat=$name[0];
      $mat=$name[1];
      $nom=$name[2];
      
      if(isset($name[3]))
        $nom.=' '.$name[3];
      if(isset($name[4]))
        $nom.=' '.$name[4];
      
      pg_query("INSERT INTO doctores VALUES (DEFAULT, '$rut', '$pat', '$mat', '$nom');");
    }
	
	}
	
	pg_query("COMMIT;");

?>
