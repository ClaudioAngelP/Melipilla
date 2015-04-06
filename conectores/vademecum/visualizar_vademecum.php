<?php 



// PROXY PTO MONTT

// INIT CURL
$ch = curl_init();

// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://10.5.132.12/produccion/conectores/vademecum/visualizar_vademecum.php?id_vademecum='.urlencode($_GET['id_vademecum']));
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$data = curl_exec ($ch);

print($data);

exit();






require_once('../../conectar_db.php');

function fixstr($str) {

	return  htmlentities(utf8_decode(html_entity_decode($str)));

}

$id_vademecum=($_GET['id_vademecum']);

list($id, $atc) = explode('|', $id_vademecum);

// INIT CURL
$ch = curl_init();

// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://wslatam.vademecum.es/CL/vweb/xml/ws_miniplus/miniplus_show?atc='.urlencode($atc));
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$data = curl_exec ($ch);

//print('<pre>'.htmlentities($data).'</pre>');

$med = new SimpleXMLElement($data);

?>

<html>
<head>
<title>VADEMECUM&copy;</title>
<meta charset="ISO-8859-1">
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1">
</head>


<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='text-align:center;font-size:26px;text-decoration:underline;'><?php echo fixstr($med->PA_ATC_set->PA_ATC->atcdesc); ?></td></tr>
<tr><td style='text-align:center;font-size:20px;'><?php echo fixstr($med->PA_ATC_set->PA_ATC->atcdesc4); ?></td></tr>
<tr><td style='text-align:center;font-size:16px;'><?php echo fixstr($med->PA_ATC_set->PA_ATC->atcdesc3); ?></td></tr>
<tr><td style='text-align:center;font-size:16px;'><?php echo fixstr($med->PA_ATC_set->PA_ATC->atcdesc2); ?></td></tr>
<tr><td style='text-align:center;font-size:16px;'><?php echo fixstr($med->PA_ATC_set->PA_ATC->atcdesc1); ?></td></tr>
</table>
</div>

<div class='sub-content2' style='font-size:12px;text-align:justify;padding:15px;margin:5px;'>
<h2><u>Acci&oacute;n</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->accion); ?><br/><br/>
<h2><u>Indicaciones</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->indicaciones); ?><br/><br/>
<h2><u>Posolog&iacute;a</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->posologia); ?><br/><br/>
<h2><u>Interacciones</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->interacciones); ?><br/><br/>
<h2><u>Precauciones</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->precauciones); ?><br/><br/>
<h2><u>Embarazo</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->embarazo); ?><br/><br/>
<h2><u>Lactancia</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->lactanciai); ?><br/><br/>
<h2><u>Reacciones Adversas</u></h2><?php echo fixstr($med->PA_ATC_set->PA_ATC->reaccionesadversas); ?><br/><br/>

<h2><u>Presentaciones</u></h2>

<table style='width:100%;font-size:11px;'>
<tr class='tabla_header'>
<td>Nombre</td>
<td>Laboratorio</td>
<td>Composici&oacute;n</td>
<td>Formato</td>
<td colspan=2>Dosis</td>
</tr>

<?php

foreach ($med->ESPECIALIDADE_set->ESPECIALIDADE as $m) {

	$clase=($c++%2==0)?'tabla_fila':'tabla_fila2';

	print("<tr class='$clase'>
	<td>".fixstr($m->nomesp)."</td>
	<td>".fixstr($m->nomlab)."</td>
	<td>".fixstr($m->ESPECIALIDADE_COMPOSICION_set->ESPECIALIDADE_COMPOSICION->titmolmin)."</td>
	<td>".fixstr($m->ESPECIALIDADE_COMPOSICION_set->ESPECIALIDADE_COMPOSICION->add_dos)."</td>
	<td style='text-align:right;font-weight:bold;font-size:16px;'>".fixstr($m->ESPECIALIDADE_COMPOSICION_set->ESPECIALIDADE_COMPOSICION->cant_dos)."</td>
	<td style='text-align:left;font-weight:bold;font-size:16px;'>".fixstr($m->ESPECIALIDADE_COMPOSICION_set->ESPECIALIDADE_COMPOSICION->uni_dos)."</td>
	</tr>
	");

}

?>

</table>

</div>

</body>
</html>
