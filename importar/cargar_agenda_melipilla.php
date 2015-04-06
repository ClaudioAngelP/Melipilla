<?php
    error_reporting(E_ALL);
    //require_once('../conectar_db.php');
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    //require_once('cargar_paciente_fonasa.php');
    set_time_limit(0);
    //-----------------------------------------------------------------------
    function formatear_rut($str)
    {
        $partes=explode('-',$str);
	return number_format($partes[0]*1,0,',','.').'-'.strtolower($partes[1]);
    }
    //-----------------------------------------------------------------------
    
    
    $l=explode("\n",utf8_decode(file_get_contents("Agenda_01_al_16_noviembre_2013.csv")));
    $nopacs=array();
    $noprofs=array();
    for($i=1;$i<sizeof($l);$i++)
    {
        if(trim($l[$i])=='')
            continue;
        $r=explode('|',$l[$i]);
        if(trim($r[1])=='')
        {
            continue;
        }
        $prut=strtoupper(str_replace('.','',trim($r[1])));
       	$p=cargar_registro("SELECT * FROM doctores WHERE lower(doc_nombres)=lower('$prut')");
        if(!$p)
        {
            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $prut NO EXISTE.<br>");
            $noprofs[]=$prut;
            //$pnom=trim($r[3]);
            //$ppat=trim($r[4]);
            //$pmat=trim($r[5]);
            pg_query("INSERT INTO doctores VALUES (DEFAULT, '', '', '', '$prut');");
            $ttmp=cargar_registro("SELECT CURRVAL('doctores_doc_id_seq') AS id;");
            $doc_id=$ttmp['id']*1;
            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $prut -- $noprofs CREADO.<br>");
            //continue; 
        }
        else
        {
            $doc_id=$p['doc_id']*1;
	}
	$codigo_esp=trim($r[0]);
	$e=cargar_registro("SELECT * FROM especialidades WHERE lower(esp_desc)=lower('$codigo_esp');");
	if(!$e)
        { 
            print("[L&iacute;nea ".($i+1)."] ESPECIALIDAD $codigo_esp NO EXISTE.<br>");
            pg_query("INSERT INTO especialidades VALUES (DEFAULT, '$codigo_esp');");
            $ttmp=cargar_registro("SELECT CURRVAL('especialidades_esp_id_seq') AS id;");
            $esp_id=$ttmp['id']*1;
            print("[L&iacute;nea ".($i+1)."] ESPECIALIDAD $codigo_esp CREADA.<br>"); 
            //continue; 
	}
        else
        {
            $esp_id=$e['esp_id']*1;
	}
	$func_id=7;
	$nom_fecha=strtoupper(str_replace('-','/',trim($r[2])));
	$fecha_asigna="'".strtoupper(str_replace('-','/',trim($r[4])))." ".$r[5]."'";
	$nomd_hora=strtoupper(str_replace('-','/',trim($r[3])));
	if(trim($r[11])=='')
{
	print("<br/>");
	print("<br/>");
	print("Linea Falta Ficha: ".$i );
	print("<br/>");
	print("<br/>");
	continue;
}

	
	$pac_ficha=$r[11];
	
if(trim($r[14])=='')
{
	print("<br/>");
	print("<br/>");
	print("Linea Falta Rut: ".$i );
	print("<br/>");
	print("<br/>");
	continue;
}
	$pac_rut=strtoupper(str_replace('.','',trim($r[14])));
	if(trim($r[6])!='')
        {
		$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='5422937-2' OR pac_ficha='231952' OR (CASE WHEN pac_ficha='' THEN false ELSE pac_ficha::text='231952' END)");
            if(!$pac)
            {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://10.5.130.50/produccion/conectores/trakcare/login.php?buscar=".urlencode(formatear_rut($pac_rut)));
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$pac_id=curl_exec($ch);
		curl_close($ch);
                print("[L&iacute;nea ".($i+1)."] Paciente ($pac_rut $pac_ficha) NO EXISTE.<br>");
                //$pac_id=importar_paciente($pac_rut, $pac_ficha);
                print("[L&iacute;nea ".($i+1)."] Paciente ($pac_rut $pac_ficha) CREADO.<br>"); 
		$nopacs[]=$pac_rut;
		//continue;
            }
            else
            {
		$pac_id=$pac['pac_id']*1;

            }
	}
        else
        {
            $pac_id=0;
        }
	if(trim($r[8])=='')
{
print("<br/>");
print("<br/>");
print("Linea Falto Tipo Atencion : ".$i );
print("<br/>");
print("<br/>");
continue;
}
	$motivo=$r[8];
	$nom=cargar_registro("SELECT * FROM nomina WHERE nom_fecha='$nom_fecha' AND nom_esp_id=$esp_id AND nom_doc_id=$doc_id and lower(nom_motivo)=lower('$motivo');");

	if(!$nom)
        {
            pg_query("INSERT INTO nomina VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 0, false, '$nom_fecha');");
            pg_query("INSERT INTO cupos_atencion VALUES (
                    DEFAULT,
                    $esp_id, $doc_id,
                    '$nom_fecha', '00:00:00', '00:00:00',
                    0, 0, 0, 0, 0, true, 0, CURRVAL('nomina_nom_id_seq')
                    )");
                    $nom_id="CURRVAL('nomina_nom_id_seq')";
	}
        else
        {
            $nom_id=$nom['nom_id']*1;
	}
	$tt=trim($r[7]);
	if($tt=='')
        {
            $extra='N';
	}
        else
        {
            $extra='S'; $nomd_hora='00:00:00';
        }
	/*$cod_estado=$r[23]*1;
	switch($cod_estado) {
	case 0: $diag_cod=''; break;
	case 1: $diag_cod='NSP'; break;
	case 2: $diag_cod=''; break;
        case 5: $diag_cod='OK'; break;
	default: $diag_cod=''; break;
	 }*/
        $diag_cod='';
	pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_extra, nomd_hora, pac_id, nomd_diag_cod, nomd_func_id, nomd_fecha_asigna) 
	VALUES (DEFAULT, $nom_id, 'N', '$extra', '$nomd_hora', $pac_id, '$diag_cod', $func_id, $fecha_asigna);");
	flush();
	}
    print("PROFESIONALES FALTANTES: ".sizeof(array_unique($noprofs))." PACIENTES FALTANTES: ".sizeof(array_unique($nopacs)));
    pg_query("UPDATE cupos_atencion SET 
        cupos_horainicio=(SELECT 
	min(nomd_hora) 
	FROM nomina_detalle 
	WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND NOT nomd_hora='00:00:00'),
	cupos_horafinal=(SELECT max(nomd_hora) FROM nomina_detalle 
        WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND NOT nomd_hora='00:00:00')+('30 minutes'::interval), 
        cupos_cantidad_n=(SELECT COUNT(*) FROM nomina_detalle 
        WHERE nomina_detalle.nom_id=cupos_atencion.nom_id)						
	WHERE cupos_horainicio='00:00:00'");
?>
