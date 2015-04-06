<?php 

//require_once('../../config.php');
//require_once('../sigh.php');
require_once('../../conectar_db.php');
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
	array('rut','16000469'),
	array('digito','K'),
	array('password','123soluc')
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

$ic=isset($_POST['ic']);

if($_POST['tipo']*1==0 OR isset($_POST['auto'])) {

	$rutp = explode('-', utf8_decode(trim($_POST['pac'])));

	$rut = $rutp[0]*1;
	$dv = strtoupper($rutp[1]);

	$pac_rut = $rut.'-'.$dv;

	$params = codificar_url(array(
		array('dispatch', 'buscarPacienteRut'),
		array('runpaciente', $rut),
		array('dgvpaciente', $dv),
		array('uni_cod_uni', '453')
	));

}

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/pacienteAction.do');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$datos = curl_exec($ch);

$data = str_get_html($datos);

// OBTIENE DATOS DEL PACIENTE DESDE EL DOM DEVUELTO POR SIGGES

pg_query("START TRANSACTION;");

$nombre = $data->find('select[name=nombrepac]');

if(sizeof($nombre)>0) {
	
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
	
	pg_query("COMMIT;");	
	
	// DESDE EL DOM OBTIENE UNA LISTA DE CASOS GES
	// ADEMAS DEL IDENTIFICADOR PARA REGISTRO DE EVENTOS SIN CASO
	
	$casos = $data->find('input[name=chkcaso]');

	
} else {

	pg_query("ROLLBACK;");

	exit("<br><br><br><br><b>Paciente NO ENCONTRADO.</b>");
	
}

print("<br /><br />
<input type='hidden' id='pac_id' name='pac_id' value='$pac_id'>
<font style='font-size:16px;'>
R.U.T. <b>$pac_rut</b> | <i>ID SIGGES: <b>($id_pac_sigges)</b></i> <br /></font>
<font style='font-size:18px;'>".htmlentities($nombre_str)."</font>
<br /><br />");

flush();

if(!isset($_POST['confirma'])) {

	if(sizeof($casos)>0) { 
	
		print('<center><table style="width:500px;"><tr class="tabla_header"><td>CASO #</td><td>DESCRIPCI&Oacute;N</td></tr>');
	
		for($i=0;$i<sizeof($casos);$i++) {
	
			$caso = $casos[$i]->find('text');
			$id_caso = $casos[$i]->attr['value'];
			
			// FIX: Error de SIGGES: concatena fecha al ID
			if(strstr($id_caso,','))
				list($id_caso)=explode(',',$id_caso);
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';			
			
			print('<tr class="'.$clase.'">
			<td style="text-align:center;">'.($i+1).'</td>
			<td>'.htmlentities($caso[0]).'</td></tr>');
		
		}
		
		print('</table></center>');
	
	} else {
	
		print('<center><b>SIN CASOS ASOCIADOS.</b></center>');	
		
	}

	print('<br /><br /><center>
	<input type="button" value="Descargar Datos del Paciente..." 
	onClick="descargar_sigges(1);" />
	</center>');

	curl_close($ch);

	exit(0);
	
}	

ob_start();

print('<center>
<table style="width:500px;font-size:8px;">
<tr class="tabla_header"><td>CASO #</td><td>DESCRIPCI&Oacute;N</td></tr>');

for($i=0;$i<sizeof($casos);$i++) {

	$caso = $casos[$i]->find('text');
	$id_caso = $casos[$i]->attr['value'];

	// FIX: Error de SIGGES: Concatena fecha al ID
	if(strstr($id_caso,','))
		list($id_caso)=explode(',',$id_caso);
	
	// OBTIENE DESCRIPCI嚘術 Y ID INTERNO DE CADA CASO	

	$clase=($i%2==0)?'tabla_fila':'tabla_fila2';			
			
	print('<tr class="'.$clase.'">
	<td style="text-align:center;">'.($i+1).'</td>
	<td>'.htmlentities($caso[0]).'</td></tr>');
	
	flush();
	
	// CREAMOS CASO SIGGES EN SISTEMA LOCAL...

/*
CREATE TABLE casos_auge
(
  ca_id bigserial NOT NULL,
  ca_pac_id bigint,
  ca_fecha_ingreso timestamp without time zone,
  ca_fecha_inicio timestamp without time zone,
  ca_fecha_cierre timestamp without time zone,
  ca_patologia text,
  ca_pat_id bigint,
  ca_patrama_id bigint,
  ca_estado smallint DEFAULT (-1),
  ca_etapa smallint DEFAULT 0,
  id_sigges bigint DEFAULT (-1),
  CONSTRAINT casos_auge_ca_id_key PRIMARY KEY (ca_id)
)
WITH (OIDS=FALSE);
*/	

	if($id_caso!=0) {
		$chk=cargar_registro("SELECT * FROM casos_auge WHERE id_sigges=$id_caso");

		if(!$chk)
		pg_query("INSERT INTO casos_auge VALUES (
			DEFAULT, $pac_id, now(), null, null,
			'".pg_escape_string($caso[0])."', 0, 0, -1, 0, $id_caso	
		);");
	}

	// SELECCIONAR CADA CASO REGISTRADO (INCLUYE EVENTOS SIN CASO)
	// ENVIA ID DEL CASO JUNTO CON LOS DATOS DEL PACIENTE PARA
	// OBTENER CARTOLA EXPRESS
	
	$params = codificar_url(array(
		array('dispatch','selectCaso'),
		array('idpaciente',$id_pac), 			// DATOS DEL PACIENTE
		array('nombrepac',$id_pac),			// DATOS DEL PACIENTE
		array('runpaciente',$rut),
		array('dgvpaciente',$dv),
		array('chkcaso',$id_caso),					// ID DE CASO
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
	
	// ITERACI嚘術 SOBRE LOS LINKS ENCONTRADOS PARA DESCARGAR
	// LOS DOCUMENTOS APUNTADOS POR CADA UNO DEPENDIENDO DEL TIPO
	// DE DOCUMENTO...	
	
	// print("DOCUMENTOS GES (".sizeof($docs)."):<br><br>");
	
	// Cuenta la cantidad de documentos asociados al caso...	
	
	$num_docs_n=0; $num_docs_d=0;	
	
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
			case 3:	$tipo='ORDEN DE ATENCI嚘術'; 	break;
			case 4:	$tipo='PRESTACI嚘術'; 				break;
			case 14:	$tipo='PRESTACI嚘術 NO VALORADA???'; 				break;
			case 6:	$tipo='CITACI嚘術'; 				break;
			default: $tipo='DESCONOCIDO'; 			break;
		}			

		// SI EL LINK ES RECONOCIDO COMO ENLACE A DOCUMENTO
		// DESCARGA EL CONTENIDO DEL ENLACE Y PASA EL CONTENIDO
		// POR LA FUNCI嚘術 DE PROCESAMIENTO....

		//print('tipo: '.$doc_tipo.'<br>');

		if($doc_id!='' AND $caso_id!='')	{	

			// print('<b>['.$tipo.']</b> ('.$doc_tipo.' '.$funcion.') id: '.$doc_id.' caso: '.$caso_id.'<br /><br />');

			if(!$ic AND $doc_tipo!=1 AND $doc_tipo!=2 AND $doc_tipo!=3 AND $doc_tipo!=4) {
				// Descarga todo...
				continue;
			} elseif($ic AND $doc_tipo!=1 AND $doc_tipo!=3) {
				// Descarga solo interconsultas y Ordenes de Atenci鏮...
				continue;
			}
			
			if(chequear_doc_sigges($doc_tipo, $doc_id, $id_caso)) {
				//print('PREVIAMENTE DESCARGADO.<br><br>');
				$num_docs_d++;	
				continue;
			}	

			$num_docs_n++; // Cuenta como documentos asociados al caso...

			$url_doc='http://www.sigges.cl/actions/paciente/cartolaAction.do?';
			$url_doc.="dispatch=enviar&numDocu=".$doc_tipo."&idDocu=".$doc_id."&idCaso=".$caso_id."&nuevoFormulario=0";

			curl_setopt($ch, CURLOPT_URL, $url_doc);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '');
			
			$html_doc = curl_exec($ch);

			pg_query("START TRANSACTION;");
			
			procesar_documento($doc_tipo, $doc_id, $caso_id, $html_doc);

			pg_query("COMMIT;");
			
			flush();
		
		}
		
	}
	
	print('<tr><td>&nbsp;</td><td>('.$num_docs_n.') documentos descargados.</td></tr>');
	
}

print('</table>');

$stats=ob_get_clean();

if(isset($_POST['auto'])) {
	exit('DESCARGA OK');	
}

$ic=cargar_registros_obj("SELECT * FROM interconsulta
				LEFT JOIN instituciones ON inter_inst_id1=inst_id
				LEFT JOIN especialidades ON inter_especialidad=esp_id
				LEFT JOIN prioridad ON inter_prioridad=prior_id 
				WHERE inter_pac_id=$pac_id 
				ORDER BY inter_ingreso DESC");

$oa=cargar_registros_obj("SELECT *, oa_fecha::date AS oa_fecha FROM orden_atencion
				LEFT JOIN instituciones ON oa_inst_id=inst_id 
				LEFT JOIN especialidades ON oa_especialidad=esp_id
				LEFT JOIN prioridad ON oa_prioridad=prior_id 
				WHERE oa_pac_id=$pac_id AND
				(NOT oa_motivo=-1)
				ORDER BY orden_atencion.oa_fecha DESC");

$script='';
$ic_local=0;
$oa_local=0;

for($i=0;$i<sizeof($ic);$i++) {
	if($ic[$i]['inter_inst_id2']*1==$sgh_inst_id) {
		$ic_local++;
	} 	
}

for($i=0;$i<sizeof($oa);$i++) {
	if($oa[$i]['oa_inst_id2']*1==$sgh_inst_id) {
		$oa_local++;
	} 	
}

print("<center>
<form id='lista_ics' name='lista_ics' onSubmit='return false;'>
<table style='width:850px;'>
<tr class='tabla_header'>
<td colspan=9 style='font-weight:bold;'><u>Interconsultas Descargadas (".($ic_local)."/".($ic?sizeof($ic):'0').")</u></td>
</tr>
<tr class='tabla_header'>
<td>&nbsp;</td>
<td>Nro. Folio</td>
<td>Fecha</td>
<td>Instituci&oacute;n Solicitante</td>
<td style='width:150px;'>Especialidad</td>
<td>G.E.S.</td>
<td>Prioridad</td>
<td>Ver</td>
</tr>");

$prioridadhtml = desplegar_opciones("prioridad", "prior_id, prior_desc",'prior_id=0','true','ORDER BY prior_id'); 
if($ic)				
for($i=0;$i<sizeof($ic);$i++) {
	
	$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
	
	if($ic[$i]['inter_estado']==-1) {	
	
	if($ic[$i]['inter_inst_id2']*1!=$sgh_inst_id) {
		$color='color:#FF0000;';
		print("<input type='hidden' 
		id='inter_local_".$ic[$i]['inter_id']."' 
		name='inter_local_".$ic[$i]['inter_id']."' 
		value='0' />");	
	} else {
		$color='';
		print("<input type='hidden' 
		id='inter_local_".$ic[$i]['inter_id']."' 
		name='inter_local_".$ic[$i]['inter_id']."' 
		value='1' />");	
	}		
	
	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<input type='checkbox' 
	id='inter_".$ic[$i]['inter_id']."' name='inter_".$ic[$i]['inter_id']."'>
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$ic[$i]['inter_folio']."</td>
	<td style='text-align:center;'>".$ic[$i]['inter_ingreso']."</td>
	<td style='font-size:11px;'><i>".htmlentities($ic[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($ic[$i]['esp_desc'])."</td>
	<td><center>
	".(($ic[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center><select 
	id  ='prioridad_ic_".$ic[$i]['inter_id']."' 
	name='prioridad_ic_".$ic[$i]['inter_id']."' 
	onClick='
		if(this.value!=0) $(\"inter_".$ic[$i]['inter_id']."\").checked=true;	
	'>$prioridadhtml
	</select></center></td>
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_ficha(".$ic[$i]['inter_id'].");' />
	</center></td></tr>");
	
	} else {

	$color='';

	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<img src='iconos/tick.png' />	
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$ic[$i]['inter_folio']."</td>
	<td style='text-align:center;'>".$ic[$i]['inter_ingreso']."</td>
	<td style='font-size:11px;'><i>".htmlentities($ic[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($ic[$i]['esp_desc'])."</td>
	<td><center>
	".(($ic[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center>".htmlentities($ic[$i]['prior_desc'])."</center></td>
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_ficha(".$ic[$i]['inter_id'].");' />
	</center></td></tr>");

		
	}
			
}

print('</table>');

print("
<table style='width:850px;'>
<tr class='tabla_header'>
<td colspan=9 style='font-weight:bold;'><u>Ordenes de Atenci&oacute;n Descargadas (".($oa_local)."/".($oa?sizeof($oa):'0').")</u></td>
</tr>
<tr class='tabla_header'>
<td>&nbsp;</td>
<td>Nro. Folio</td>
<td>Fecha</td>
<td>Instituci&oacute;n Solicitante</td>
<td style='width:150px;'>Especialidad</td>
<td>G.E.S.</td>
<td>Prioridad</td>
<td>Ver</td>
</tr>");

$prioridadhtml = desplegar_opciones("prioridad", "prior_id, prior_desc",'prior_id=0','true','ORDER BY prior_id'); 

if($oa)				
for($i=0;$i<sizeof($oa);$i++) {
	
	$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
	if($oa[$i]['oa_estado']==-1) {
	
	if($oa[$i]['oa_inst_id2']*1!=$sgh_inst_id) {
		$color='color:#FF0000;';
		print("<input type='hidden' 
		id='oa_local_".$oa[$i]['oa_id']."' 
		name='oa_local_".$oa[$i]['oa_id']."' 
		value='0' />");	
	} else {
		$color='';
		print("<input type='hidden' 
		id='oa_local_".$ic[$i]['oa_id']."' 
		name='oa_local_".$ic[$i]['oa_id']."' 
		value='1' />");	
	}		
	
	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<input type='checkbox' 
	id='oa_".$oa[$i]['oa_id']."' name='oa_".$oa[$i]['oa_id']."'>
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$oa[$i]['oa_folio']."</td>
	<td style='text-align:center;'>".$oa[$i]['oa_fecha']."</td>
	<td style='font-size:11px;'><i>".htmlentities($oa[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($oa[$i]['esp_desc'])."</td>
	<td><center>
	".(($oa[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center><select 
	id  ='prioridad_oa_".$oa[$i]['oa_id']."' 
	name='prioridad_oa_".$oa[$i]['oa_id']."' 
	onClick='
		if(this.value!=0) $(\"oa_".$oa[$i]['oa_id']."\").checked=true;	
	'>$prioridadhtml
	</select></center></td>	
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_oa(".$oa[$i]['oa_id'].");' />
	</center></td></tr>");
	
	} else {

	$color='';

	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<img src='iconos/tick.png' />	
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$oa[$i]['oa_folio']."</td>
	<td style='text-align:center;'>".$oa[$i]['oa_fecha']."</td>
	<td style='font-size:11px;'><i>".htmlentities($oa[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($oa[$i]['esp_desc'])."</td>
	<td><center>
	".(($oa[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center>".htmlentities($oa[$i]['prior_desc'])."</center></td>	
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_oa(".$oa[$i]['oa_id'].");' />
	</center></td></tr>");
		
	}	

}

print('</table>');



if($ic OR $oa)
print('<center>
<input type="button" value="-- Confirmar Ingreso de Documentos... --" 
onClick="validar_ic();" /></form>
<br><br></center>
');
else 
print('<center>
<h2>No hay documentos disponibles.</h2>
<br><br></center>');

print($stats);

curl_close($ch);

?>