<?php 


// PROXY PTO MONTT

// INIT CURL
$ch = curl_init();

// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://10.5.132.12/produccion/conectores/vademecum/buscar_alergias.php?nombre='.urlencode($_GET['nombre']));
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
curl_setopt($ch, CURLOPT_URL, 'http://wslatam.vademecum.es/CL/vweb/xml/ws_allergy/SearchAllergy?value='.$nombre);
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
<tr class='tabla_header'><td>ID VADEMECUM&copy;</td><td>Tipo Alergia</td></tr>
<?php 
foreach ($meds->allergy_set->allergy as $al) {
	$clase=$c++%2==1?'tabla_fila':'tabla_fila2';
   echo '<tr class="'.$clase.'" style="cursor:pointer;" onMouseOver="this.className=\'mouse_over\'" onMouseOut="this.className=\''.$clase.'\'" onDblClick="select_code(\''.$al->id_alergia.'|'.fixstr($al->alergia).'|1\');"><td style="text-align:center;font-size:16px;font-weight:bold;color:red;">'.$al->id_alergia.'</td><td style="font-size:16px;">'.fixstr($al->alergia).'</td></tr>';
}

// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://wslatam.vademecum.es/CL/vweb/xml/ws_substance/SearchByName?value='.$nombre);
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
<?php
foreach ($meds->substance_set->substance as $al) {
        $clase=$c++%2==1?'tabla_fila':'tabla_fila2';
   echo '<tr class="'.$clase.'" style="cursor:pointer;" onMouseOver="this.className=\'mouse_over\'" onMouseOut="this.className=\''.$clase.'\'" onClick="select_code(\''.$al->id_molecule.'|'.fixstr($al->name_molecule).'|2\');"><td style="text-align:center;font-size:16px;font-weight:bold;color:orange;">'.$al->id_molecule.'</td><td style="font-size:16px;">'.fixstr($al->name_molecule).'</td></tr>';
}


?>	
