<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",utf8_decode(file_get_contents("normaliza_patologias.csv")));
	
	for($i=0;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
		
		$r=explode("|", $f[$i]);
		
		list($num)=explode('.', $r[0]); 
		$num*=1;
		
		if($num>9)
			$num_w="[$num]";
		else
			$num_w="[0$num]";
		
		pg_query("
			UPDATE patologias_auge SET 
			pst_patologia_interna='".$r[1]."'
			WHERE pat_glosa ILIKE '$num_w%'
		");
	
	}

?>
