<?php 
	require_once('../config.php');
	require_once('../conectores/sigh.php');
	$script=1;
	require('../conectores/registro_civil/registrocivil.php');

	$rt=explode("\n",file_get_contents('rut_registro_civil.csv'));
	
	for($i=0;$i<sizeof($rt);$i++) {
		
		if(trim($rt[$i])=='') continue;

		$r=explode(';',$rt[$i]);

		ob_start();
		@regcivil_login();
		$dat=@regcivil_buscar($r[0]);
		@regcivil_logout();
		ob_end_clean();
		
		if(!$dat) {  
			
			file_put_contents('rut_registro_civil_ok.csv', 
			trim($rt[$i]).";ERROR\n",
			FILE_APPEND);
				
		} else {
			
			file_put_contents('rut_registro_civil_ok.csv', 
			trim($rt[$i]).';'.$dat[0].';'.$dat[1].';'.$dat[2].';'.$dat[3].';'.$dat[4].';'.$dat[5]."\n",
			FILE_APPEND);
			
		}
		
		sleep(1);
	
	}
	
?>
