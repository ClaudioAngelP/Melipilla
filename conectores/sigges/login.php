<?php 

require_once('../../config.php');
require_once('../sigh.php');
require_once('simplehtmldom/simple_html_dom.php');
require_once('procesar_sigges.php');

function codificar_url($r) {

	$buffer='';

	for($i=0;$i<sizeof($r);$i++) {
		$buffer.=urlencode($r[$i][0]).'='.urlencode($r[$i][1]);
		if($i<sizeof($r)-1) $buffer.='&';	
	}

	return $buffer;
}

// INIT CURL
$ch = curl_init();

// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/');
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$login = curl_exec ($ch);

$page = str_get_html($login);

$form = $page->find('form[name=IngresoLoginForm]');

$randomId = $page->find('input[name=randomId]');
$dispatch = $page->find('input[name=dispatch]');

print('<pre>');

$params = codificar_url(array(
	array('randomId',$randomId[0]->attr['value']),
	array('dispatch',$dispatch[0]->attr['value']),
	array('rut','16501170'),
	array('digito','8'),
	array('password','56soto')
));

$url=($form[0]->attr['action']);

/*
echo 'URL: '.$url.'<br /><br />';
echo $params;
*/

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl'.$url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$login2 = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/jsp/menu/homepage.jsp');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');

$login3 = curl_exec($ch);

$page3 = str_get_html($login3);

$link = $page3->find('frame[name=left]');

$url=$link[0]->attr['src'];

// CONSTRUYE CONSULTA POR RUT DEL PACIENTE

$rut=$_GET['rut'];
$dv=$_GET['dv'];

$pac_rut=$rut.'-'.$dv;

$params = codificar_url(array(
	array('dispatch','buscarPacienteRut'),
	array('runpaciente',$rut),
	array('dgvpaciente',$dv),
	array('uni_cod_uni','453')
));

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/pacienteAction.do');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$datos = curl_exec($ch);

$data = str_get_html($datos);

// OBTIENE DATOS DEL PACIENTE DESDE EL DOM DEVUELTO POR SIGGES

pg_query("START TRANSACTION;");

$nombre = $data->find('select[name=nombrepac]');
$nombre = $nombre[0]->find('option');
$id_pac_sigges = $nombre[0]->attr['value'];
$nombre = $nombre[0]->find('text');
$nombre_str = $nombre[0];

// TRATAMOS DE IGUALAR EL ID DE SIGGES CON UN REGISTRO INTERNO...

$pac=cargar_registro("SELECT * FROM pacientes WHERE id_sigges=".$id_pac_sigges);

if($pac)
	// SI EL ID EXISTE SE DEVUELVE EL ID INTERNO DEL SISTEMA...
	$pac_id=$pac['pac_id'];
else {
	// SI EL PACIENTE NO EXISTE ENTONCES SE UBICA POR RUT...
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='".$rut.'-'.$dv."'");
	
	if($pac) {
		// SI EL RUT COINCIDE ENTONCES SE ACTUALIZA EL REGISTRO PARA APUNTAR
		// AL ID DE SIGGES...
		$pac_id=$pac['pac_id']*1;
		pg_query("UPDATE pacientes SET id_sigges=$id_pac_sigges WHERE pac_id=$pac_id");
	} else {
		// PACIENTE NO EXISTE... DEBE SER IMPORTADO...
		$pac_id=insertar_paciente_sigges($data);
	}
		
}

// DESDE EL DOM OBTIENE UNA LISTA DE CASOS GES
// ADEMAS DEL IDENTIFICADOR PARA REGISTRO DE EVENTOS SIN CASO

$casos = $data->find('input[name=chkcaso]');

//print_r($nombre);

print($nombre_str.' <i>ID: <b>'.$id_pac_sigges.'</b></i> <br /><br />');

flush();

for($i=0;$i<sizeof($casos);$i++) {

	$caso = $casos[$i]->find('text');
	$id = $casos[$i]->attr['value'];
	
	// OBTIENE DESCRIPCIÓN Y ID INTERNO DE CADA CASO	
	
	print("<h2>CASO ".($i+1).": ".$caso[0].' (<b>'.$id.'</b>)</h2><br /><br />');
	
	flush();

	// SELECCIONAR CADA CASO REGISTRADO (INCLUYE EVENTOS SIN CASO)
	// ENVIA ID DEL CASO JUNTO CON LOS DATOS DEL PACIENTE PARA
	// OBTENER CARTOLA EXPRESS
	
	$params = codificar_url(array(
		array('dispatch','selectCaso'),
		array('idpaciente',$id_pac), 			// DATOS DEL PACIENTE
		array('nombrepac',$id_pac),			// DATOS DEL PACIENTE
		array('runpaciente',$rut),
		array('dgvpaciente',$dv),
		array('chkcaso',$id),					// ID DE CASO
		array('uni_cod_uni','453')
	));
	
	curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/pacienteAction.do');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	
	$datos = curl_exec($ch);
	
	// DESCARGA CARTOLA EXPRESS DEL CASO
	
	curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/actions/paciente/cartolaAction.do?dispatch=load&DataSource=dsAT_Dedicada');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, '');
	
	$datos = curl_exec($ch);
	
	// GENERA DOM CON HTML DEL CASO PARA GENERAR LINKS
	// A LOS DOCUMENTOS NECESARIOS	
	
	$domc = str_get_html($datos);
	
	$docs = $domc->find('input[class=htmlbutton]');
	
	// ITERACIÓN SOBRE LOS LINKS ENCONTRADOS PARA DESCARGAR
	// LOS DOCUMENTOS APUNTADOS POR CADA UNO DEPENDIENDO DEL TIPO
	// DE DOCUMENTO...	
	
	// print("DOCUMENTOS GES (".sizeof($docs)."):<br><br>");	
	
	for($d=0;$d<sizeof($docs);$d++) {

		// PROCESA CADA LINK PARA OBTENER DATOS PRINCIPALES Y
		// GENERAR LINK DE DESCARGA...

		$link=str_replace(')','',trim($docs[$d]->attr['onclick']));
		$link=str_replace(';','',$link);

		$link=explode('(',$link);
		
		$funcion=$link[0];

		$link=explode(',',$link[1]);
		
		$doc_tipo=trim($link[0])*1;
		$doc_id=trim($link[1]);
		$caso_id=trim($link[2]);
		
		switch($doc_tipo) {
			case 1:	$tipo='INTERCONSULTA'; 			break;
			case 2:	$tipo='I.P.D.'; 					break;
			case 3:	$tipo='ORDEN DE ATENCIÓN'; 	break;
			case 4:	$tipo='PRESTACIÓN'; 				break;
			case 14:	$tipo='PRESTACIÓN NO VALORADA???'; 				break;
			case 6:	$tipo='CITACIÓN'; 				break;
			default: $tipo='DESCONOCIDO'; 			break;
		}			

		// SI EL LINK ES RECONOCIDO COMO ENLACE A DOCUMENTO
		// DESCARGA EL CONTENIDO DEL ENLACE Y PASA EL CONTENIDO
		// POR LA FUNCIÓN DE PROCESAMIENTO....

		if($doc_id!='' AND $caso_id!='')	{	

			print('<b>['.$tipo.']</b> ('.$doc_tipo.' '.$funcion.') id: '.$doc_id.' caso: '.$caso_id.'<br /><br />');

			if($doc_tipo!=1 AND $doc_tipo!=3) continue;

			if(chequear_doc_sigges($doc_tipo, $doc_id)) {
				print('PREVIAMENTE DESCARGADO.<br><br>');	
				continue;
			}	

			$url_doc='http://www.sigges.cl/actions/paciente/cartolaAction.do?';
			$url_doc.="dispatch=enviar&numDocu=".$doc_tipo."&idDocu=".$doc_id."&idCaso=".$caso_id."&nuevoFormulario=0";

			curl_setopt($ch, CURLOPT_URL, $url_doc);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '');
			
			$html_doc = curl_exec($ch);
			
			procesar_documento($doc_tipo, $doc_id, $caso_id, $html_doc);
			
			flush();
		
		}
		
	}
	
}

pg_query("COMMIT;");

print('</pre>');


?>
