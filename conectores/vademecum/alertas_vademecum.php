<?php 

	require_once('../../conectar_db.php');

function fixstr($str) {

        return  htmlentities(utf8_decode(html_entity_decode($str)));

}

	$pac_id=$_GET['pac_id']*1;
	$art_id=$_GET['art_id']*1;

	$p=cargar_registro("SELECT *, (CURRENT_DATE-pac_fc_nac) AS dias_vida  FROM pacientes WHERE pac_id=$pac_id");

	$sexo=($p['sex_id']*1)+1;
	$edad=floor($p['dias_vida']/30);

	$a=cargar_registro("SELECT * FROM articulo WHERE art_id=$art_id");

	//if($a['id_vademecum']=='')
		//exit('<center><h1>Medicamento no asociado a VADEMECUM&copy;</h1></center>');

	list($idv,$atc)=explode('|', $a['id_vademecum']);

	//$rd=cargar_registros_obj("SELECT * FROM (SELECT *, receta_fecha_emision+(recetad_dias || ' days')::interval AS fecha_termino FROM receta JOIN recetas_detalle ON recetad_receta_id=receta_id JOIN articulo ON recetad_art_id=art_id WHERE receta_paciente_id=$pac_id) AS foo WHERE fecha_termino>=CURRENT_DATE");
	$rd=cargar_registros_obj("SELECT * FROM (SELECT *, receta_fecha_emision+(recetad_dias || ' days')::interval AS fecha_termino FROM receta JOIN recetas_detalle ON recetad_receta_id=receta_id JOIN articulo ON recetad_art_id=art_id WHERE receta_paciente_id=$pac_id) AS foo;");

	$_idList=$idv;

	$arts=Array();

	if($rd)
	for($i=0;$i<sizeof($rd);$i++) {

		if($rd[$i]['id_vademecum']=='') continue;
		
		if(isset($arts[$rd[$i]['art_id']])) continue;

		$arts[$rd[$i]['art_id']]=1;

		list($idv,$atc)=explode('|', $rd[$i]['id_vademecum']);

		$_idList.=','.$idv;

	}

	if($_GET['art_ids']!='') {

	$art_ids=trim($_GET['art_ids'],',');

	$rd2=cargar_registros_obj("SELECT * FROM articulo WHERE art_id IN ($art_ids)");
	for($i=0;$i<sizeof($rd2);$i++) {

                if($rd2[$i]['id_vademecum']=='') continue;

                if(isset($arts[$rd2[$i]['art_id']])) continue;

                $arts[$rd2[$i]['art_id']]=1;

                list($idv,$atc)=explode('|', $rd2[$i]['id_vademecum']);

                $_idList.=','.$idv;

        }

	}


	$_idList=trim($_idList,',');

	$nd=cargar_registros_obj("SELECT nomd_diag_cod FROM nomina_detalle WHERE pac_id=$pac_id AND nomd_diag_cod IS NOT NULL AND nomd_diag_cod NOT IN ('','X','NSP','T','H','OK');");

	

        $_cie10List='';

	if($_GET['nomd_diag_cod']!='')
		$_cie10List="'".$_GET['nomd_diag_cod']."',";

	if($nd)
        for($i=0;$i<sizeof($nd);$i++) {

                $_cie10List.="'".$nd[$i]['nomd_diag_cod']."',";

        }

	$_cie10List=trim($_cie10List,',');

	$ad=cargar_registros_obj("SELECT id_alergia, al_tipo FROM paciente_alergias WHERE pac_id=$pac_id;");

        $_clasealergiaList='';
	$_alergiaList='';

	if($ad)
        for($i=0;$i<sizeof($ad);$i++) {

		if($ad[$i]['al_tipo']==1)
	                $_clasealergiaList.=$ad[$i]['id_alergia'].",";
		else	
                        $_alergiaList.=$ad[$i]['id_alergia'].",";

        }

        $_clasealergiaList=trim($_clasealergiaList,',');	
	$_alergiaList=trim($_alergiaList,',');


$ch = curl_init();

if($_GET['inp_peso']!='') {
	$peso='&peso='.$_GET['inp_peso'];
} else {
	$peso='';
}

if($_GET['inp_renal']!='') {
        $renal='&renal='.$_GET['inp_renal'];
} else {
        $renal='&renal=false';
}

if($_GET['chk_embarazo']=='true') {
	$_GET['chk_embarazo']='';
}

if($_GET['chk_fotosensible']=='true') {
        $_GET['chk_fotosensible']='';
}











//// USAR PROXY PTO MONTT...





// SET URL FOR THE POST FORM LOGIN
curl_setopt($ch, CURLOPT_URL, 'http://wslatam.vademecum.es/CL/vweb/xml/ws_patient/alertas?edad='.$edad.'&sexo='.$sexo.'&_idList='.($_idList).'&_idList_contra_cie='.($_cie10List).'&_idList_clasealergia='.($_clasealergiaList).'&_idList_alergia='.($_alergiaList).'&lactancia='.$_GET['chk_lactancia'].'&embarazo='.$_GET['chk_embarazo'].'&fotosensibilidad='.$_GET['chk_fotosensible'].''.$peso.''.$renal);

curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$data = curl_exec ($ch);

//print('http://wslatam.vademecum.es/CL/vweb/xml/ws_patient/alertas?edad='.$edad.'&sexo='.$sexo.'&_idList='.($_idList).'&_idList_contra_cie='.($_cie10List).'&_idList_clasealergia='.($_clasealergiaList));
//print('<pre>'.($data).'</pre>');



$alertas = new SimpleXMLElement($data);

$al=Array();
$al_lvl=Array();

$trad=Array();
$trad['insuficiencia_renal']='Insuficiencia Renal';
$trad['edad']='Edad';
$trad['peso']='Peso';
$trad['alergias']='Alergias';
$trad['lactancia']='Lactancia';
$trad['embarazo']='Embarazo';
$trad['fotosensibilidad']='Fotosensibilidad';

$trad['patologias']='Patolog&iacute;as';
$trad['administrativas']='Alertas Administrativas';

curl_setopt($ch, CURLOPT_URL, 'http://wslatam.vademecum.es/CL/vweb/xml/ws_drug/Analyse_vmp?_idList='.($_idList).'&molecules=true');
//curl_setopt($ch, CURLOPT_URL, 'http://wslatam.vademecum.es/CL/vweb/xml/ws_drug/Analyse_vmp?_idList=5925,11980,5052,52409&molecules=true');

curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$data = curl_exec ($ch);

//print($data);

$ix = new SimpleXMLElement($data);


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<title>Alertas - VADEMECUM&copy;</title>

<?php cabecera_popup('../..'); ?>


<script>

function show_alertas() {

	var a=$('alertas').value*1;

	if(a==0) {
		$$('.alerta_yellowgreen').each(function(e) {e.show();});
		$$('.alerta_orange').each(function(e) {e.show();});
		$$('.alerta_yellow').each(function(e) {e.show();});
		$$('.alerta_red').each(function(e) {e.show();});


	} else if(a==1) {

		$$('.alerta_yellowgreen').each(function(e) {e.hide();});
                $$('.alerta_orange').each(function(e) {e.hide();});
                $$('.alerta_yellow').each(function(e) {e.show();});
                $$('.alerta_red').each(function(e) {e.show();});


	} else if(a==2) {

		$$('.alerta_yellowgreen').each(function(e) {e.hide();});
                $$('.alerta_orange').each(function(e) {e.show();});
                $$('.alerta_yellow').each(function(e) {e.show();});
                $$('.alerta_red').each(function(e) {e.show();});

	} else if(a==3) {

                $$('.alerta_yellowgreen').each(function(e) {e.hide();});
                $$('.alerta_orange').each(function(e) {e.hide();});
                $$('.alerta_yellow').each(function(e) {e.hide();});
                $$('.alerta_red').each(function(e) {e.show();});

        }



}


</script>

<body class='fuente_por_defecto popup_background'>

<center>
<div class='sub-content'>
<center><h2>Nivel de Alertas <select id='alertas' name='alertas' onChange='show_alertas();' style='font-size:18px'>
<option value='0'>Todas</option>
<option value='3'>Muy Graves</option>
<option value='1'>Solo Graves</option>
<option value='2'>Medias y Graves</option>
</select></h2></center>
</div>

<div class='sub-content'  style='text-align:center;text-decoration:underline;'>
Interacciones Medicamentosas
</div>

<span style='font-size:10px;'>
<?php 

$colores[10]='yellowgreen';
$colores[20]='orange';
$colores[30]='yellow';
$colores[40]='red';

$ixs=Array();
$ixs_lvl=Array();

foreach ($ix->interaction_set->interaction AS $inter) {

	$num=sizeof($ixs);
	$ixs[$num]=$inter;
	$ixs_lvl[$num]=$inter->id_gravitation*1;

}

array_multisort($ixs_lvl, SORT_DESC, $ixs);

for($i=0;$i<sizeof($ixs);$i++) {

	$inter=$ixs[$i];

	$color=$colores[$inter->id_gravitation*1];


	print("<table class='alerta_$color' cellpadding=1 cellspacing=0 style='border:6px solid $color;margin:2px;width:95%;font-size:12px;'><tr><td rowspan=3 style='text-align:center;background-color:$color;width:40px;' class='tabla_header'><img src='../../iconos/error.png' style='width:32px;height:32px;' /></td><td class='tabla_header' style='background-color:$color;text-align:left;font-size:12px;'>Interacci&oacute;n entre<br> <b>".fixstr($inter->name_speciality_1)."</b> y <br/><b>".fixstr($inter->name_speciality_2)."</b></td></tr><tr class='tabla_fila'><td><u>Mecanismo:</u> ".fixstr($inter->gravity_set->gravity->mechanism_interaction)."</td></tr><tr class='tabla_fila2'><td><u>Consejos:</u> <i>".fixstr($inter->gravity_set->gravity->council_prescriber)."</i></td></tr></table>");

}

?>
</span>

<?php 

foreach ($alertas->patient_set AS $pset) {

	if(!isset($pset->patient)) continue;	

	$tipo=$trad[trim($pset->attributes()->id)];

	/*print("<h3><u>".$trad[trim($pset->attributes()->id)]."</u></h3>");

	print("<table style='width:90%;font-size:12px;'>
		<tr class='tabla_header'><td>Nombre</td><td>Detalle</td></tr>
	");*/

	$colores=Array('yellowgreen','orange','yellow','red');

	foreach($pset->patient AS $p) {

		$level=$p->level*1;
		$color=$colores[$level*1];
	
		$med=fixstr($p->name_speciality);
		$def=fixstr($p->level_definition);
		$desc=fixstr($p->classe_alergy1).fixstr($p->RECETA).' '.fixstr($p->classe_alergy1).' '.fixstr($p->specific_name)." ".fixstr($p->explication);	

		/*print("<tr class='$clase' style='background-color:$color'>
			<td>".fixstr($p->name_speciality)."</td>
			<td><b>[".fixstr($p->level_definition).']</b> '.fixstr($p->classe_alergy1).fixstr($p->RECETA).' '.fixstr($p->classe_alergy1).' '.fixstr($p->specific_name)." ".fixstr($p->explication)."</td></tr>");*/

		$num=sizeof($al);
		$al[$num]=Array($tipo, $level, $color, $med, $def, $desc);
		$al_lvl[$num]=$level*1;


	}

}

foreach ($alertas->object->patient_set AS $pset) {

        if(!isset($pset->patient)) continue;

        $tipo=$trad[trim($pset->attributes()->id)];

        /*print("<h3><u>".$trad[trim($pset->attributes()->id)]."</u></h3>");

        print("<table style='width:90%;font-size:12px;'>
                <tr class='tabla_header'><td>Nombre</td><td>Detalle</td></tr>
        ");*/

        $colores=Array('yellowgreen','orange','yellow','red');

        foreach($pset->patient AS $p) {

                $level=$p->level*1;
                $color=$colores[$level*1];

                $med=fixstr($p->name_speciality);
                $def=fixstr($p->level_definition);
                $desc=fixstr($p->classe_alergy1).fixstr($p->RECETA).' '.fixstr($p->classe_alergy1).' '.fixstr($p->specific_name)." ".fixstr($p->explication);

                /*print("<tr class='$clase' style='background-color:$color'>
                        <td>".fixstr($p->name_speciality)."</td>
                        <td><b>[".fixstr($p->level_definition).']</b> '.fixstr($p->classe_alergy1).fixstr($p->RECETA).' '.fixstr($p->classe_alergy1).' '.fixstr($p->specific_name)." ".fixstr($p->explication)."</td></tr>");*/

                $num=sizeof($al);
                $al[$num]=Array($tipo, $level, $color, $med, $def, $desc);
                $al_lvl[$num]=$level*1;


        }

}

	array_multisort($al_lvl, SORT_DESC, $al);

?>

<div class='sub-content' style='text-align:center;text-decoration:underline;'>
Otras Alertas
</div>

<?php


 print("<table style='width:95%;font-size:12px;'>
                <tr class='tabla_header'><td>Tipo</td><td>Nombre</td><td>Detalle</td></tr>");

        for($i=0;$i<sizeof($al);$i++) {

                $clase=($c%2)==0?'tabla_fila':'tabla_fila2';

		if($al[$i]==$al[$i-1]) continue;
		$c++;	

		if($al[$i][4]!='') $corchetes="<b>[".$al[$i][4].']</b>'; else $corchetes='';

                print("<tr class='alerta_".$al[$i][2]."' style='background-color:".$al[$i][2]."'>
                        <td>".$al[$i][0]."</td><td>".$al[$i][3]."</td>
                        <td>$corchetes ".$al[$i][5]."</td></tr>");

        }

        print("</table>");

?>

</center>

</body>
</html>
	
