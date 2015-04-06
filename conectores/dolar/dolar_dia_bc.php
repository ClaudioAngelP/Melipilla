<?php
	chdir(dirname(__FILE__));
	
	require_once('../../config.php');
	require_once('../sigh.php');
	
    $url_valores = 'http://si3.bcentral.cl/indicadoresvalores/secure/indicadoresvalores.aspx';
 
    $ch = curl_init();
    // indicar la url que queremos obtener
    curl_setopt($ch, CURLOPT_URL, $url_valores);
    // obtener el contenido sin las cabeceras
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // obtener el contenido en vez de enviarlo al browser
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // usar un user agent par asimular la navegación de un browser
    curl_setopt($ch,CURLOPT_USERAGENT,
           'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'
        );
    $content = curl_exec($ch);
    // recuerden cerrar la sesion y liberar los recursos
    curl_close($ch);
 
    //usamos una expresión regular para obtener todos los valores con decimales
    //que hay en el código del sitio /[0-9]+[,][0-9]+/
    // 1 o más digitos antes de la coma - una coma - 1 o más digitos despues de la coma
    // las coincidencias se envian a $matches
    preg_match_all('/[0-9]+[,][0-9]+/',str_replace('.','',$content),$matches);
 
    //antes de establecer los indices que contienne los valores
    //debemos imprimirlos que en este caso ya los identificamos
    //$valorUF   = str_replace(',','.',$matches[0][0]);
    //$valorUTM  = str_replace(',','.',$matches[0][1]);
    $valorUSD  = str_replace(',','.',$matches[0][2]);
    //$valorEURO = str_replace(',','.',$matches[0][3]);
    $fecha=date("d/m/Y");
    
    $v_fecha = cargar_registro("SELECT dolar_fecha FROM dolar_observado WHERE dolar_fecha='$fecha'");
			
	if($v_fecha==false){
		pg_query("INSERT INTO dolar_observado VALUES ('$fecha', $valorUSD);");
		echo "Actualizado";
	}

?>

