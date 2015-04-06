<?php 

//require_once('../../config.php');
//require_once('../sigh.php');
require_once('../../conectar_db.php');
require_once('simplehtmldom/simple_html_dom.php');

set_time_limit(0);

function codificar_url($r) {

	$buffer='';

	for($i=0;$i<sizeof($r);$i++) {
		$buffer.=urlencode($r[$i][0]).'='.urlencode($r[$i][1]);
		if($i<sizeof($r)-1) $buffer.='&';	
	}

	return $buffer;
}

function fix_params($ld,$fd,$str) {
	
	$fields=explode($ld, $str);
	
	$result='';	
	
	for($i=0;$i<sizeof($fields);$i++) {
		list($name,$value)=explode($fd, $fields[$i]);
		$name=urlencode(trim($name));
		$value=urlencode(trim($value));
		$result.=$name.'='.$value;
		if($i<sizeof($fields)-1) $result.='&';
	}
	
	return $result;
	
}

// INIT CURL
$ch = curl_init();

// SET URL FOR THE POST FORM LOGIN

curl_setopt($ch, CURLOPT_USER_AGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)');


function sigges_login() {
	
GLOBAL $ch;

curl_setopt( $ch, CURLOPT_URL, 'http://www.sigges.cl/' );
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
curl_setopt( $ch, CURLOPT_FORBID_REUSE, true );
curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 20 );
curl_setopt( $ch, CURLOPT_COOKIEFILE, 'cookies/cookiefile_'.$_SESSION['sgh_usuario_id'].'_'.str_replace(' ','-',microtime(false)).'.txt');
//curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookies/cookiefile_'.$_SESSION['sgh_usuario_id'].'_'.str_replace(' ','-',microtime(false)).'.txt');
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt( $ch, CURLOPT_POST, 0);
curl_setopt( $ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$login = curl_exec ($ch);

$page = str_get_html($login);

$form = $page->find('form[name=IngresoLoginForm]');

$randomId = $page->find('input[name=randomId]');
$dispatch = $page->find('input[name=dispatch]');

$params = codificar_url(array(
	array('randomId',$randomId[0]->attr['value']),
	array('dispatch',$dispatch[0]->attr['value']),
	array('rut','10386259'),
	array('digito','0'),
	array('password','oso799')
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

// SELECCIONA PERFIL DE INGRESO A PROGRAMAS ESPECIALES...

$params = 'nombreUnidad=San+Mart%EDn+de+Quillota%2C+Hospital+&nombrePerfil=INGRESO+PROGRAMAS+ESPECIALES&idUnidad=478&idPerfil=1153';

$page = str_get_html($login2);

$form = $page->find('form[name=perfilesForm]');

$url=($form[0]->attr['action']);

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl'.$url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$login3=curl_exec($ch);

}

//die($login3);

function sigges_presta_pe() {
	
GLOBAL $ch;

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/poiiAction.do?dispatch=showPOII&DataSource=dsAT_Compartida');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');

$ingreso_presta = curl_exec($ch);

$chk=str_get_html($ingreso_presta);
$form=array();
$form=$chk->find('form[name=poiiForm]');

if(sizeof($form)==0) {
	die('Error al Cargar Formulario...<br><br>'.htmlentities($ingreso_presta));
}

$progesp=$chk->find('select[name=programaEspecialPE]');

$pesp=$progesp[0]->find('option');

// Agrega Prestación al Listado...

$ingreso_presta='';

$rut='16000469';
$dv='K';

for($i=1;$i<sizeof($pesp);$i++) {

	$val_pe=$pesp[$i]->attr['value'];
		
	$tmp=$pesp[$i]->find('text');
	$tmp_pe=$tmp[0];

	
	print("<br /><br />$val_pe >> $tmp_pe<br />");
		
	$programaEspecial=$val_pe;

	// Selecciona Programas Especiales
	$params=fix_params("\n",":",'nombreSolapa:programasEspeciales
dispatch:loadFamiliaPE
valFechaCP:
toDelete:
validoPres:0
rut:
dv:
nombre: 
direccion:
comuna:
telefono:
edadExtendida:
sexo:
casoPacienteAuge:-1
origenAuge:0
folioAuge:-1
patronPrestacionAuge:
extraSistemaAuge:false
prestadorAuge:-1
glosaPrestadorAuge:
servicioSaludAuge:-1
establecimientoAuge:-1
unidadAuge:-1
especialidadAuge:-1
fechaInicioAuge:
fechaTerminoAuge:
horaAuge:
cantidadAuge:0
servicioSolicitantePE:-1
fechaIndicacionPE:
programaEspecialPE:'.$programaEspecial.'
familiaPrestacionPE:-1
extraSistemaPE:false
prestadorPE:-1
glosaPrestadorPE:
servicioSaludPE:-1
establecimientoPE:-1
fechaPrestacionPE:
fechaPrestacionHastasPE:
cantidadPE:0
programaEspecial.observacion:
origenPP:0
folioPP:-1
extraSistemaPP:false
prestadorPP:-1
glosaPrestadorPP:
servicioSaludPP:-1
establecimientoPP:-1
unidadPP:-1
especialidadPP:-1
fechaPrestacionPP:
origenOP:0
folioOP:-1
patronPrestacionOP:
extraSistemaOP:false
prestadorOP:-1
glosaPrestadorOP:
servicioSaludOP:-1
establecimientoOP:-1
unidadOP:-1
especialidadOP:-1
fechaInicioOP:
fechaTerminoOP:
cantidadOP:0');

	curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/poiiAction.do');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	
	if(!$ingreso_presta = curl_exec($ch)) {
		print("ERROR!<br><br>");	
	}
	
	$fesp=str_get_html($ingreso_presta);
	
	$fespec=$fesp->find('select[name=familiaPrestacionPE]');

	$fesp2=$fespec[0]->find('option');

	print("Familias: (".(sizeof($fesp2)-1).") <br/>");

	flush();
	
	for($k=1;$k<sizeof($fesp2);$k++) {
		
		$val_fa=$fesp2[$k]->attr['value'];

		$tmp=$fesp2[$k]->find('text');
		$tmp_fa=$tmp[0];
		
		print(">> $val_fa >> $tmp_fa<br />");
		
		$familiaPrestacion=$val_fa;		
		
		// Seleccona Familia de Prestaciones...
		$params='nombreSolapa=programasEspeciales&dispatch=loadPrestaciones&valFechaCP=&toDelete=&validoPres=0&rut='.$rut.'&dv='.$dv.'&nombre=MAR%CDA+EUGENIA+VARAS+HERRERA&direccion=PRINCIPAL++++++++++++++++++63++++0&comuna=Quillota&telefono=Sin+Informaci%F3n&edadExtendida=47+A%F1os%2C+1+Mes%2C+11+D%EDas%2C+13+Horas.&sexo=Femenino&masDatos=Ver+m%E1s+datos+del+paciente&otroPaciente=Otro+Paciente&casoPacienteAuge=-1&origenAuge=0&patronPrestacionAuge=&buscaPrestacionAuge=Buscar+Prestaci%F3n&extraSistemaAuge=false&buscaEstablecimientoAuge=Buscar%3CBR%3EEstablecimiento&servicioSaludAuge=106&establecimientoAuge=478&unidadAuge=-1&especialidadAuge=-1&fechaInicioAuge=&horaAuge=&agregaPrestacionAuge=Agregar&borraPrestacionAuge=Borrar&imprimirPO=Imprimir&volverPO=Volver&servicioSolicitantePE=106&fechaIndicacionPE=&programaEspecialPE='.$programaEspecial.'&familiaPrestacionPE='.$familiaPrestacion.'&extraSistemaPE=false&buscaEstablecimientoPE=Buscar%3CBR%3EEstablecimiento&servicioSaludPE=106&establecimientoPE=478&fechaPrestacionPE=&programaEspecial.observacion=&agregaPrestacionPE=Agregar&borraPrestacionPE=Borrar&grabarPE=Grabar&imprimirPE=Imprimir&volverPE=Volver&origenPP=0&extraSistemaPP=false&buscaEstablecimientoPP=Buscar%3CBR%3EEstablecimiento&servicioSaludPP=106&establecimientoPP=478&unidadPP=-1&especialidadPP=-1&fechaPrestacionPP=&agregaPP=Agregar&borraPP=Borrar&grabarPP=Grabar&imprimirPP=Imprimir&volverPP=Volver&origenOP=0&patronPrestacionOP=&buscaPrestacionOP=Buscar+Prestaci%F3n&extraSistemaOP=false&buscaEstablecimientoOP=Buscar%3CBR%3EEstablecimiento&servicioSaludOP=106&establecimientoOP=478&unidadOP=-1&especialidadOP=-1&fechaInicioOP=&agregaPrestacionOP=Agregar&borraPrestacionOP=Borrar&grabarOP=Grabar&imprimirOP=Imprimir&volverOP=Volver';
	
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	
		$ingresa_presta = curl_exec($ch);
	
		$page=str_get_html($ingresa_presta);
		
		$prestas=$page->find("select[name=idPrestacionPE]");
		
		$prestas=$prestas[0]->find('option');
		
		for($kk=0;$kk<sizeof($prestas);$kk++) {
		
			$val_pres=$prestas[$kk]->attr['value'];

			$tmp=$prestas[$kk]->find('text');
			$tmp_pres=$tmp[0];
			
			preg_match("/\([0-9]+\)/",$tmp_pres, $codigo_pres);		
			$nombre_pres = trim(preg_replace("/\([0-9]+\)/","",$tmp_pres));		

			list($codigo_pres) = $codigo_pres;

			$codigo_pres=trim($codigo_pres,"()");

			print(">> >> $val_pres >> [$codigo_pres] [$nombre_pres]<br />");
			
			pg_query("INSERT INTO codigos_prestacion_pe_sigges VALUES (
				DEFAULT,
				$val_pe,
				'$tmp_pe',
				$val_fa,
				'$tmp_fa',
				$val_pres,
				'$codigo_pres',
				'$nombre_pres'			
			);");
			
		}
		
		$page->clear();			
	
	}
	
	$fesp->clear();

}

}

function sigges_logout() {

GLOBAL $ch;

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/closession');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');

curl_close($ch);

}

sigges_login();

sigges_presta_pe();

sigges_logout();

?>