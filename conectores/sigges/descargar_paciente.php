<?php 

//require_once('../../config.php');
//require_once('../sigh.php');
require_once('simplehtmldom/simple_html_dom.php');
require_once('procesar_sigges.php');


// INIT CURL

// CONSTRUYE CONSULTA POR RUT DEL PACIENTE

	$ch = curl_init();

	sigges_login();


	$rutp = explode('-', utf8_decode(trim($_GET['rut'])));

	$rut = $rutp[0]*1;
	$dv = strtoupper($rutp[1]);

	$pac_rut = $rut.'-'.$dv;

	$params = codificar_url(array(
		array('dispatch', 'buscarPacienteRut'),
		array('runpaciente', $rut),
		array('dgvpaciente', $dv),
		array('uni_cod_uni', '453')
	));


curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/pacienteAction.do');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$datos = curl_exec($ch);

//print($datos);

//exit();

$data = str_get_html($datos);

// OBTIENE DATOS DEL PACIENTE DESDE EL DOM DEVUELTO POR SIGGES

pg_query("START TRANSACTION;");

$nombre = $data->find('select[name=nombrepac]');

if(sizeof($nombre)>0) {
	
	$nombre = $nombre[0]->find('option');
	$id_pac_sigges = $nombre[0]->attr['value'];
	$nombre = $nombre[0]->find('text');
	$nombre_str = $nombre[0];
	
	// DESDE EL DOM OBTIENE UNA LISTA DE CASOS GES
	// ADEMAS DEL IDENTIFICADOR PARA REGISTRO DE EVENTOS SIN CASO
	
	$casos = $data->find('input[name=chkcaso]');
	
	$pac_ok=true;
	
	//print_r($casos);

	
} else {

	pg_query("ROLLBACK;");

	print("Paciente NO ENCONTRADO.\n");
	
	$pac_ok=false;
	
}




if($pac_ok) { 

	$p['rut']=$pac_rut;
	$p['nombre']=htmlentities($nombre_str);
	$p['id_sigges']=$id_pac_sigges;
	
	$p['casos']=Array();
	
	if(sizeof($casos)>1) { 
	
		for($i=0;$i<sizeof($casos)-1;$i++) {
	
			$caso = $casos[$i]->find('text');
			
			$estado_caso = $casos[$i]->find('font');
			$estado_caso = $estado_caso[1]->find('text');
			
			$id_caso = $casos[$i]->attr['value'];
			
			// FIX: Error de SIGGES: concatena fecha al ID
			if(strstr($id_caso,','))
				list($id_caso)=explode(',',$id_caso);
			
			$p['casos'][$i]['id']=$id_caso;
			$p['casos'][$i]['nombre']=htmlentities($caso[0]);
			$p['casos'][$i]['estado']=htmlentities($estado_caso[0]);
			
			$tmp=cargar_registros_obj("SELECT * FROM patologias_sigges_traductor 
			WHERE pst_problema_salud='".pg_escape_string(trim($caso[0]))."'");
			
			$p['casos'][$i]['xproblema']=htmlentities($tmp[0]['pst_patologia_interna']);
		
		}
			
	} 

	curl_close($ch);
	
	print(json_encode($p));
	
	$data=pg_escape_string(json_encode($p));
	pg_query("INSERT INTO pacientes_sigges VALUES (DEFAULT, CURRENT_TIMESTAMP, '$pac_rut', '$data');");
	
	pg_query("COMMIT;");
	
}
	
?>
