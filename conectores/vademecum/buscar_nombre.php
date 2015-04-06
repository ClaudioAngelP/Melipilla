<?php 

// PROXY PTO MONTT

// INIT CURL
$ch = curl_init();

// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://10.5.132.12/produccion/conectores/vademecum/buscar_nombre.php?nombre='.urlencode($_GET['nombre']));
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$data = curl_exec ($ch);

print($data);

exit();







function fixstr($str) {

        return  htmlentities(utf8_decode(html_entity_decode($str)));

}

$nombre=urlencode($_GET['nombre']);


// INIT CURL
$ch = curl_init();

// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://wslatam.vademecum.es/CL/vweb/xml/ws_drug/SearchByName?value='.$nombre);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$data = curl_exec ($ch);

//print('<pre>'.htmlentities($data).'</pre>');

$meds = new SimpleXMLElement($data);
?>
<table style='width:100%;font-size:11px;'>
<tr class='tabla_header'><td>ID VADEMECUM&copy;</td><td>C&oacute;digo ATC</td><td>Nombre</td><td>Presentaci&oacute;n</td></tr>
<?php 
foreach ($meds->drug_set->drug as $med) {
	$clase=$c++%2==1?'tabla_fila':'tabla_fila2';
   echo '<tr class="'.$clase.'" style="cursor:pointer;" onMouseOver="this.className=\'mouse_over\'" onMouseOut="this.className=\''.$clase.'\'" onClick="select_code(\''.$med->id_speciality.'|'.$med->code_atc_medicom.'\');"><td style="text-align:center;font-size:16px;font-weight:bold;color:yellowgreen;">'.$med->id_speciality.'</td><td style="text-align:center;font-size:16px;font-weight:bold;color:skyblue;">'.$med->code_atc_medicom.'</td><td>'.fixstr($med->name_speciality).'</td><td>'.fixstr($med->package).'</td></tr>';
}


?>	
