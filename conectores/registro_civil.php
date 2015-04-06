<?php 

	if(!isset($script)) {
		require_once('../../config.php');
		require_once('../sigh.php');
	}
	require_once('simplehtmldom/simple_html_dom.php');

	$usuario='6740516';
	$clave='1351';
	$rut='16000469-K';
	
	$ch = curl_init();
	$action=''; $code='';
	
	function regcivil_login() {
		
		global $ch, $action, $code, $usuario, $clave, $rut;

		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)');

		curl_setopt( $ch, CURLOPT_VERBOSE, true);
		
		curl_setopt( $ch, CURLOPT_URL, 'http://monitoweb.srcei.cl/monito' );
		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
		
		$data='';

		$login1 = curl_exec ($ch);
		
		$tmp = str_get_html($login1);

		$form = $tmp->find('form[id=MonitoForm]');
		
		$action = $form[0]->attr['action'];
		$code = $form[0]->attr['onsubmit'];
		
		$code=explode("'",$code);
		$code=$code[1];
		
		$url='http://monitoweb.srcei.cl'.$action;

		$post_data='_message=&_initial=&_action='.urlencode('A:HOLA     ');

		//print("1) ACTION: $url CODE: $code URL: $post_data<br><br>");
		
		curl_setopt( $ch, CURLOPT_URL, $url );
		
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);
		
		$login2 = curl_exec ($ch);
		
		$tmp = str_get_html($login2);

		$form = $tmp->find('form[id=MonitoForm]');
		
		$action = $form[0]->attr['action'];
		$code = $form[0]->attr['onsubmit'];
		
		$code=explode("'",$code);
		$code=$code[1];
		
		$url='http://monitoweb.srcei.cl'.$action;

		$post_data='CODE='.urlencode($usuario).'&PASSWORD='.urlencode($clave).'&RUN='.urlencode($rut).'&_message=&_initial=&_action='.urlencode('1:ACEPTAR');

		//print("1) ACTION: $url CODE: $code URL: $post_data<br><br>");
		
		curl_setopt( $ch, CURLOPT_URL, $url );
		
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);

		$login3 = curl_exec ($ch);
		
		$tmp = str_get_html($login3);

		$form = $tmp->find('form[id=MonitoForm]');
		
		$action = $form[0]->attr['action'];
		$code = $form[0]->attr['onsubmit'];
		
		$code=explode("'",$code);
		$code=$code[1];
			
	}
	
	function regcivil_buscar($buscar_rut, $update=false) {

		global $ch, $action, $code, $usuario, $clave, $rut;

		$url='http://monitoweb.srcei.cl'.$action;

		$post_data='CODE='.urlencode($usuario).'&PASSWORD='.urlencode($clave).'&RUN='.urlencode($rut).'&_message=&_initial=&_action='.urlencode('B:BUS RUN  ');

		//print("3) ACTION: $url CODE: $code URL: $post_data<br><br>");
		
		curl_setopt( $ch, CURLOPT_URL, $url );
		
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);

		$login4 = curl_exec ($ch);

		$tmp = str_get_html($login4);

		$form = $tmp->find('form[id=MonitoForm]');
		
		$action = $form[0]->attr['action'];
		$code = $form[0]->attr['onsubmit'];
		
		$code=explode("'",$code);
		$code=$code[1];	
		
		$url='http://monitoweb.srcei.cl'.$action;

		$post_data='RUN='.urlencode($buscar_rut).'&_message=&_initial=&_action='.urlencode('1:ACEPTAR');

		//print("4) ACTION: $url CODE: $code URL: $post_data<br><br>");
		
		curl_setopt( $ch, CURLOPT_URL, $url );
		
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);

		$login4 = curl_exec ($ch);
		
		$tmp = str_get_html($login4);
		
		$tmp_nombre=$tmp->find('input[name=NOMBRE]');
		$nombre = $tmp_nombre[0]->attr['value'];
		$tmp_sexo=$tmp->find('input[name=SEXO]');
		$sexo = $tmp_sexo[0]->attr['value'];
		$tmp_fnac=$tmp->find('input[name=FECHA_NAC]');
		$fnac = $tmp_fnac[0]->attr['value'];
		$tmp_fdef=$tmp->find('input[name=FECHA_DEF]');
		$fdef = str_replace(' ', '', trim($tmp_fdef[0]->attr['value']));
		
		if($nombre=='' OR $fnac=='') {
		
			return false;
			
		} else {
		
			print("NOMBRE: $nombre<br>SEXO: $sexo<br>FECHA NAC: $fnac<br>FECHA DEF: $fdef<br><br>");

			$n=explode('/',$nombre);
			
			$paterno=pg_escape_string($n[0]);
			$materno=pg_escape_string($n[1]);
			$nombres=pg_escape_string(str_replace('=','',$n[2]));			

			$value=array($nombres, $paterno, $materno, $sexo, $fnac, $fdef);
		
		}
		
		flush();
		
		$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$buscar_rut'");
		
		if($pac AND $nombre!='' AND $fnac!='' AND $update) {
			
			print("ACTUALIZANDO PACIENTE... <br>");
			
			$pac_id=$pac['pac_id']*1;
			
			if($fdef=='--') $fdef='null'; else $fdef="'$fdef'";
			
			pg_query("UPDATE pacientes SET pac_appat='$paterno', pac_apmat='$materno', pac_nombres='$nombres', pac_fc_nac='$fnac', pac_fc_def=$fdef WHERE pac_id=$pac_id");
			
			if($fdef!='null') {
				
				$chq=pg_query("SELECT * FROM interconsulta WHERE inter_pac_id=$pac_id AND inter_motivo_salida=0;");
				$num=pg_num_rows($chq);
				
				if($num>0) {
				
					print("REBAJANDO $num INTERCONSULTA(S) POR FALLECIMIENTO.<br><br>");
					
					pg_query("UPDATE interconsulta SET inter_motivo_salida=9, inter_fecha_salida=$fdef WHERE inter_pac_id=$pac_id AND inter_motivo_salida=0;");
				
				}
				
			}
			
		} else {
			
			if(!$update)
				print("PACIENTE NO EXISTE EN GIS.");
			
		}
		
		$url='http://monitoweb.srcei.cl'.$action;

		$post_data='_message=&_initial=&_action='.urlencode('A:FIN     ');

		curl_setopt( $ch, CURLOPT_URL, $url );
		
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);

		$login5 = curl_exec ($ch);
		
		return $value;
		

	}

	function regcivil_logout() {

		global $ch, $action, $code;

		$url='http://monitoweb.srcei.cl'.$action;

		$post_data='_message=&_initial=&_action='.urlencode('4:SALIR   ');

		curl_setopt( $ch, CURLOPT_URL, $url );
		
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);

		$login5 = curl_exec ($ch);
	
		curl_close($ch);
	
	}
	

?>
