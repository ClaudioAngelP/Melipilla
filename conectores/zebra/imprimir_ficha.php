<?php
    function imprimir_ficha($pac_id)
    {

        $tmp_folder=dirname(__FILE__);

	chdir('/var/www/produccion/conectores/zebra/');

	require_once('../../conectar_db.php');

	// $log_id=$_GET['log_id']*1;

	$d=cargar_registro("SELECT * FROM pacientes LEFT JOIN comunas USING (ciud_id) LEFT JOIN prevision USING (prev_id) LEFT JOIN sexo USING (sex_id) WHERE pac_id=$pac_id;");
                
	foreach($d AS $key=>$val) {
                $d[$key]=mb_convert_encoding(utf8_encode($val), "Windows-1252", "UTF-8");
        }


	//foreach($d AS $key=>$val)
        //    $d[$key]=utf8_encode($d[$key]);
        
        
        

	$ip=$_SERVER['REMOTE_ADDR'];
	//print($ip);

	$f=file_get_contents('template_ficha.txt');
	$f=strtr($f, utf8_decode("áéíóúññ?ÉÉ?ÓÚÑ"), "aeiounAEIOUN");

	$f=str_replace('{$ficha}', $d['pac_ficha'], $f);
	$f=str_replace('{$rut}', formato_rut($d['pac_rut']), $f);
	$f=str_replace('{$pasaporte}', $d['pac_pasaporte'], $f);
	$f=str_replace('{$nombres}', $d['pac_nombres'], $f);
	$f=str_replace('{$apellidos}', $d['pac_appat'].' '.$d['pac_apmat'], $f);
	$f=str_replace('{$sexo}', $d['sex_desc'], $f);
	$f=str_replace('{$fecha_nac}', $d['pac_fc_nac'], $f);
	//$f=str_replace('{$domicilio}', $d['pac_direccion'].', '.$d['ciud_desc'], $f);
	//$f=str_replace('{$telefono}', $d['pac_fono'], $f);
	//$f=str_replace('{$representante}', $d['pac_padre'], $f);
	//$f=str_replace('{$prevision}', $d['prev_desc'], $f);
	$f=str_replace('{$fecha_creacion}', date('d/m/Y'), $f);


	//$f=strtr($f, utf8_decode("áéíóúñ�?É�?ÓÚÑ"), "aeiounAEIOUN");

	file_put_contents("log/$pac_id.txt", $f);
        

        if($ip=="10.5.130.186" || $ip=="10.5.130.188")
        {
            ob_start(); $var=exec("smbclient -L 10.5.130.186 -U INFORMATICA%ci1d09s", $salida); ob_end_clean();
		//print_r($salida);
        }
        if($ip=="10.5.130.187" || $ip=="10.5.130.185" || $ip=="10.5.131.107" || $ip=="10.5.130.184")
        {
            	ob_start(); 
		$var=exec("smbclient -L 10.5.130.187 -U INFORMATICA%ci1d08s", $salida); 
		
		ob_end_clean();
		//print_r($salida);
	}
        if($ip=="10.5.131.168")
        {
            ob_start(); $var=exec("smbclient -L 10.5.131.168 -U usuarios -N", $salida); ob_end_clean();
		//print_r($salida);
        }
        
        $printer='';

        for($i=0;$i<sizeof($salida);$i++)
        {
            $sal=strtolower(trim($salida[$i]));
            if(strstr($sal, 'printer') AND strstr($sal,'zebra'))
                list($printer)=explode(' ',trim($salida[$i]));
        }
	
	//print($printer);
	//die();
        if($ip=="10.5.130.186" || $ip=="10.5.130.188")
        {
            $ip_impresora="10.5.130.186";
            exec('smbclient //'.$ip_impresora.'/'.$printer.' ci1d09s -U INFORMATICA -c "print log/'.$pac_id.'.txt"');
        }
        if($ip=="10.5.130.187" || $ip=="10.5.130.185" || $ip=="10.5.131.107" || $ip=="10.5.130.184")
        {
            $ip_impresora="10.5.130.187";
            exec('smbclient //'.$ip_impresora.'/'.$printer.' ci1d08s -U INFORMATICA -c "print log/'.$pac_id.'.txt"');
        }
        if($ip=="10.5.131.168")
        {
            $ip_impresora="10.5.131.168";
            exec('smbclient //'.$ip_impresora.'/'.$printer.' -N -U usuarios -c "print log/'.$pac_id.'.txt"');
        }
	

	chdir($tmp_folder);

    }
    
    if(isset($_GET['pac_id']))
        imprimir_ficha($_GET['pac_id']*1);

?>
