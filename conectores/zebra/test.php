<?php 

	ob_start(); $var=exec("smbclient -L 10.5.130.187 -U INFORMATICA%ci1d08s", $salida); ob_end_clean();

	$printer='';

	for($i=0;$i<sizeof($salida);$i++) {
		$sal=strtolower($salida[$i]);
		if(strstr($sal, 'printer') AND strstr($sal,'zebra')) list($printer)=explode(' ',trim($salida[$i]));
	}

	print($printer);

?>
