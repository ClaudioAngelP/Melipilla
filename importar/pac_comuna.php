<?php 

	error_reporting(E_ALL);

  require_once('../conectar_db.php');
  
  $fi=explode("\n", utf8_decode(file_get_contents('comunas_pac.csv')));
  
 // pg_query("START TRANSACTION;");
 
 	$str=('N|CENTRO|CIUDAD|RUT|COMUNA|DIRECCION|SECTOR')."\r\n";
  
 for($i=1;$i<sizeof($fi);$i++) {
      
	$r=explode('|',$fi[$i]);
     
    $n=$r[0];
    $centro=$r[1];
	$ciudad=($r[2]);
	$rut=$r[3];
	     		
	$comuna=cargar_registro("select ciud_desc,pac_direccion,sector_nombre from pacientes
								left join comunas using (ciud_id)
								where pac_rut='$rut'");
	 $ciud=$comuna['ciud_desc'];
	 $direcc=str_replace(',',' ',$comuna['pac_direccion']);
	 $sector=$comuna['sector_nombre'];
	
	$str.=($n.'|'.$centro.'|'.$ciudad.'|'.$rut.'|'.$ciud.'|'.$direcc.'|'.$sector)."\r\n";
	  		
  	flush();
 // pg_query("COMMIT");

}
	$fname='pac_comunas.csv';
	file_put_contents("$fname",$str);

?>
