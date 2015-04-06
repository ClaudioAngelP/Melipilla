<?php 

        chdir(dirname(__FILE__));

	require_once('../../config.php');
	require_once('../sigh.php');
	require_once('../mercadopublico/simplehtmldom/simple_html_dom.php');


	$ch = curl_init();

	//$anio=date('Y');
	$anio=date('2013');

	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)');

	curl_setopt( $ch, CURLOPT_VERBOSE, true);

	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	
	curl_setopt( $ch, CURLOPT_URL, 'www.sii.cl/pagina/valores/dolar/dolar'.$anio.'.htm' );
		
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
		
	$data='';

	print("DESCARGANDO DATOS\n\n");

	$data = curl_exec ($ch);
	
	$tmp = str_get_html($data);

	$div_dolar = $tmp->find('div[id=contenido]');
	$tabla_dolar = $div_dolar[0]->find('table[class=tabla]');

	$dias=$tabla_dolar[0]->find('tr');
	
	for($i=0;$i<sizeof($dias)-1;$i++) {
		
		$meses=$dias[$i]->find('td');
		
		for($j=0;$j<sizeof($meses);$j++) {
			
			$fecha=$i.'/'.($j+1).'/'.$anio;
			$valor=$meses[$j]->find('text');
			$valor=trim($valor[0]);
			
			if($valor=='&nbsp;' OR $valor=='') continue;
			
			$valor=str_replace(',','.',$valor)*1;
			
			if($valor==0) continue;
			$v_fecha = cargar_registro("SELECT dolar_fecha FROM dolar_observado WHERE dolar_fecha='$fecha'");
			
			if($v_fecha==false){
				pg_query("INSERT INTO dolar_observado VALUES ('$fecha', $valor);");
			}
			
		}
		
	}

?>
