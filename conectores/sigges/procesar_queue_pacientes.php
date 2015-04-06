<?php 

	chdir( dirname( __FILE__ ) );

	require_once('../../config.php');
	require_once('../sigh.php');
	require_once('simplehtmldom/simple_html_dom.php');
	require_once('procesar_sigges.php');

	
	$_POST['ic']=1;
	$_POST['tipo']=0;
	$_POST['auto']=1;
	$_POST['confirma']=1;
	$_POST['login']=1;	
		
	$q=cargar_registros_obj("
		SELECT DISTINCT pacq_rut FROM pacientes_queue 
		WHERE NOT pacq_procesado 
		LIMIT 15;
	");
	
	$start=microtime(true);

	if($q) {

		$ch=curl_init();
	
		sigges_login();	
	
		echo "\n\n[".date('d/m/Y H:i:s')."] DESCARGANDO ".sizeof($q)." PACIENTES EN COLA...\n\n";
	
		for($x=0;$x<sizeof($q);$x++) {
	
			$_POST['pac']=$q[$x]['pacq_rut'];
	
			echo "[".date('d/m/Y H:i:s')."] Iniciando (".$q[$x]['pacq_rut'].") ".$q[$x]['pacq_nombre']."\n";
		
			include('descargar_datos.php');
			
			$p=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='".$q[$x]['pacq_rut']."' AND id_sigges>0");
			
			if($p) {
				$pac_id=$p[0]['pac_id'];
				$pac_nombre=pg_escape_string(trim($p[0]['pac_nombres'].' '.$p[0]['pac_appat'].' '.$p[0]['pac_apmat']));	
			} else {
				$pac_id=0;
				$pac_nombre='(PACIENTE NO ENCONTRADO)';
			}				
			
			pg_query("UPDATE pacientes_queue SET 
						pacq_procesado=true, 
						pac_id=$pac_id,
						pacq_nombre='$pac_nombre'
						WHERE pacq_rut='".$q[$x]['pacq_rut']."'");	
		
		}
		
		curl_close($ch);	 
	
	} else {
	
		echo "\n\n[".date('d/m/Y H:i:s')."] NO HAY PACIENTES EN COLA PARA DESCARGAR.\n";	
		
	}		

	$end=microtime(true);
	
	$elapsed=$end-$start;

	$cnt=cargar_registro("SELECT COUNT(*) AS cant FROM pacientes_queue WHERE NOT pacq_procesado;");

	echo "\n[".date('d/m/Y H:i:s')."] TERMINADO. [".$cnt['cant']." en cola pendientes.] ($elapsed segs. transcurridos)\n\n";
	
	exit(0);

?>