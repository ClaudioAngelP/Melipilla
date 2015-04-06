<?php
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    $l=explode("\n",file_get_contents("agenda/Mayo_2014_2.csv"));
    pg_query("START TRANSACTION;");
    $noprofs=array();
    for($i=1;$i<sizeof($l);$i++)
    {
        print("<br>");
        print("Linea ".$i ." - ".trim($r[0]));
        print("<br>");
        
        if(trim($l[$i])=='')
        {
            continue;
        }
        
        $r=explode(';',$l[$i]);
        $prut=strtoupper(str_replace('.','',trim($r[1].'-'.$r[2])));
        
        if($prut=='-')
            $prut='';
        
        $pnom=strtoupper(str_replace('.','',trim($r[3])));
        $encontrado=false;
        if($prut!='')
        {
            //print("<br>");
            //print("SELECT * FROM doctores WHERE doc_rut='$prut'");
            //print("<br>");
            $p=cargar_registros_obj("SELECT * FROM doctores WHERE upper(doc_rut)=upper('".$prut."')");
            if(!$p)
            {
                print("<br>");
                print("Doctor No Encontrado con rut: ".$prut." - Nombre: ".$pnom."");
                //print("SELECT * FROM doctores WHERE upper(doc_nombre_trackare)=upper('$pnom')");
                print("<br>");
                $p=cargar_registros_obj("SELECT * FROM doctores WHERE upper(doc_nombre_trackare)=upper('$pnom')");
                if(!$p)
                {
                    print("<br>");
                    //print("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$pnom') OR (doc_rut='$prut' AND NOT doc_rut='')");
                    print("Doctor No Encontrado con Nombre Trackare: ".$pnom." - Rut: ".$prut."");
                    print("<br>");
                    $p=cargar_registros_obj("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$pnom') OR (doc_rut='$prut' AND NOT doc_rut='')");
                    if($p)
                    {
                        $doc_id=$p[0]['doc_id']*1;
                        pg_query("update doctores set doc_nombre_trackare=upper($pnom') where doc_id=$doc_id;");
                        if(count($p)>1)
                        {
                            print("<br>");
                            print("Se Encontraron ".count($p)." registros para el nombre ". $pnom);
                            print("<br>");
                            die();
                        }
			$encontrado=true;
                    }
                    else
                    {
                        print("<br>");
                        //print("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$pnom') OR (doc_rut='$prut' AND NOT doc_rut='')");
                        print("Doctor No Encontrado con Nombre: ".$pnom." - Rut: ".$prut."");
                        print("<br>");
                    }
		}
		else
		{
                    $doc_id=$p[0]['doc_id']*1;
                    if(count($p)>1)
                    {
                        print("<br>");
			print("Se Encontraron ".count($p)." registros para el nombre de medico trackare ". $pnom);
			print("<br>");
                        die();
                    }
                    $encontrado=true;
		}
            }
            else
            {
                $doc_id=$p[0]['doc_id']*1;
                pg_query("update doctores set doc_nombre_trackare=upper('$pnom') where doc_id=$doc_id;");
                if(count($p)>1)
		{
                    print("<br>");
                    print("Se Encontraron ".count($p)." registros para el rut ". $prut);
                    print("<br>");
                    die();
		}
		$encontrado=true;
            }
        }
	else
	{
            //print("<br>");
            //print("SELECT * FROM doctores WHERE upper(doc_nombre_trackare)=upper('$pnom')");
            //print("<br>");
            $p=cargar_registros_obj("SELECT * FROM doctores WHERE upper(doc_nombre_trackare)=upper('$pnom')");
            if(!$p)
            {
                print("<br>");
		//print("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$pnom') OR (doc_rut='$prut' AND NOT doc_rut='')");
                print("Doctor No Encontrado con Nombre Trackare: ".$pnom." - Rut: ".$prut."");
		print("<br>");
		$p=cargar_registros_obj("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$pnom') OR (doc_rut='$prut' AND NOT doc_rut='')");
		if($p)
		{
                    $doc_id=$p[0]['doc_id']*1;
                    pg_query("update doctores set doc_nombre_trackare=upper('$pnom') where doc_id=$doc_id;");
                    if(count($p)>1)
                    {
                        print("<br>");
			print("Se Encontraron ".count($p)." registros para el nombre ". $pnom);
			print("<br>");
                        die();
                    }
                    $encontrado=true;
		}
                else
                {
                    print("<br>");
                    //print("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$pnom') OR (doc_rut='$prut' AND NOT doc_rut='')");
                    print("Doctor No Encontrado con Nombre: ".$pnom." - Rut: ".$prut."");
                    print("<br>");
                    die();
                }
            }
            else
            {
                $doc_id=$p[0]['doc_id']*1;
                if(count($p)>1)
                {
                    print("<br>");
                    print("Se Encontraron ".count($p)." registros para el nombre de medico trackare ". $pnom);
                    print("<br>");
                    die();
		}
		$encontrado=true;
            }
	}
	if(!$encontrado)
	{
            print("<br>");
            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $pnom NO EXISTE.<br>"); $noprofs[]=$prut;
            print("<br>");
            //print("<br>");
            //print("INSERT INTO doctores (doc_id,doc_rut,doc_paterno,doc_materno,doc_nombres,doc_nombre_trackare) VALUES (DEFAULT, '".$prut."', '', '', upper('".$pnom."'),upper('".$pnom."'));");
            //print("<br>");
            pg_query("INSERT INTO doctores (doc_id,doc_rut,doc_paterno,doc_materno,doc_nombres,doc_nombre_trackare) VALUES (DEFAULT, upper('".$prut."'), '', '', upper('".$pnom."'),upper('".$pnom."'));");
            $ttmp=cargar_registros_obj("SELECT CURRVAL('doctores_doc_id_seq') AS id;");
            $doc_id=$ttmp[0]['id']*1;
            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $prut -- $pnom -- CREADO.<br>");
            print("<br>");
    	}
        /*
        print("<br>");
        print("SELECT DISTINCT ausencia_fechainicio, ausencia_fechafinal FROM ausencias_medicas WHERE (doc_id=".$doc_id." OR doc_id=0);");
        print("<br>");
        */
        
        $fechas_ausencias = cargar_registros_obj("SELECT DISTINCT ausencia_fechainicio, ausencia_fechafinal FROM ausencias_medicas WHERE (doc_id=".$doc_id." OR doc_id=0);", true);
	
        $codigo_esp=trim($r[0]);
	$e=cargar_registro("SELECT * FROM especialidades WHERE upper(esp_desc)=upper('".$codigo_esp."');");
	if(!$e)
	{ 
            print("<br>");
            print("[L&iacute;nea ".($i+1)."] ESPECIALIDAD ".$codigo_esp." NO EXISTE.<br>"); 
            print("<br>");
            pg_query("INSERT INTO especialidades VALUES (DEFAULT, upper('".$codigo_esp."'));");
            $ttmp=cargar_registro("SELECT CURRVAL('especialidades_esp_id_seq') AS id;");
            $esp_id=$ttmp['id']*1;
            print("[L&iacute;nea ".($i+1)."] ESPECIALIDAD ".$codigo_esp." CREADA.<br>"); 
            print("<br>");
	}
	else
	{
            $esp_id=$e['esp_id']*1;
	}
	//$frut=strtoupper(str_replace('.','',trim($r[8])));
	$frut='22222222-2';
	$f=cargar_registro("SELECT * FROM funcionario WHERE func_rut='".$frut."'");
	if(!$f)
	{
            print("<br>");
            print("[L&iacute;nea ".($i+1)."] FUNCIONARIO ".$frut." NO EXISTE.<br>");
            print("<br>");
            continue;
	}
	$func_id=$f['func_id']*1;		
	
        if(trim($r[5])=='' OR trim($r[6])=='')
        {
            continue;
        }
	
        $nom_fecha1=strtoupper(str_replace('-','/',trim($r[5])));
	$nom_fecha2=strtoupper(str_replace('-','/',trim($r[6])));
        
	$dias=Array($r[7],$r[10],$r[13],$r[16],$r[19],$r[22]);
	$cupos=Array($r[8]*1,$r[11]*1,$r[14]*1,$r[17]*1,$r[20]*1,$r[23]*1);
        $sobre_cupos=Array($r[9]*1,$r[12]*1,$r[15]*1,$r[18]*1,$r[21]*1,$r[24]*1);
        //print_r($cupos);
        //print("<br>");
        //print_r($sobre_cupos);
        //die();
	$tipo=trim($r[4]);
        $tipo_contrato=trim($r[26]);
	switch($tipo)
	{
            case 'Consulta Nueva':
                $ttipo='N';
		break;
            
            case 'Consulta Repetida':
                $ttipo='C';
                break;
            
            default:
                $ttipo='P';
		break;
	}
	
	$diag_cod='';
        
        $fi=explode('/',$nom_fecha1);
        $ff=explode('/',$nom_fecha2);
        $di=mktime(0,0,0,$fi[1],$fi[0],$fi[2]);
        $df=mktime(0,0,0,$ff[1],$ff[0],$ff[2]);
        for(;$di<=$df;$di+=86400)
        {
            // Evita dias de la semana no chequeados...
           
            $ds=(date('w',$di)*1)-1;
            
            
            
            
            if($ds==-1)
                $ds=6;
            
          
            
            if(trim($dias[$ds])=='')
            {
                continue;
            }
            
            
            // Evita dias de ausencia médica global o propia del médico...
            $d=date('d/m/Y', $di);
            if($fechas_ausencias AND array_search($d, $fechas_ausencias))
            {
                print("<br>");
                print("Encontro Ausencia medica: Doctor: ".$pnom." para la fecha: ".$d);
                print("<br>");
		continue;
            }
            
            // Inserta los cupos de atención en el díaa seleccionado...
            //pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_motivo, nom_tipo, nom_urgente, nom_fecha) VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '$tipo', 0, false, '$d');");
            /*
            print("<br>");
            print("SELECT * FROM nomina WHERE nom_fecha='".$d."' AND nom_esp_id=".$esp_id." AND nom_doc_id=".$doc_id." and upper(nom_motivo)=upper('".$tipo."');");
            print("<br>");
            */
            
            $nom=cargar_registro("SELECT * FROM nomina WHERE nom_fecha='".$d."' AND nom_esp_id=".$esp_id." AND nom_doc_id=".$doc_id." and upper(nom_motivo)=upper('".$tipo."') and upper(nom_tipo_contrato)=upper('".$tipo_contrato."');");
            if(!$nom)
            {
                print("<br>");
		print("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_motivo, nom_tipo, nom_urgente, nom_fecha,nom_tipo_contrato) VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, initcap(lower('$tipo')), 0, false, '$d','$tipo_contrato');");
		print("<br>");
                pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_motivo, nom_tipo, nom_urgente, nom_fecha,nom_tipo_contrato) VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, initcap(lower('$tipo')), 0, false, '$d','$tipo_contrato');");
		$nom_id="CURRVAL('nomina_nom_id_seq')";
            }
            else
            {
                //print("<br>");
		//print("Econtro Nomina");
		//print("<br>");
		$nom_id=$nom['nom_id']*1;
            }
            
            $tmp=explode(' ',$dias[$ds]);
            $desde=trim($tmp[0]);
            $hasta=trim($tmp[1]);
            print("<br>");
            print("INSERT INTO cupos_atencion VALUES (default, ".$esp_id.", ".$doc_id.", '".$d."', '".$desde."', '".$hasta."', ".$cupos[$ds].", 0, 0, 0, 0, true, 0, ".$nom_id.", true, true );");
            print("<br>");
            print("<br>");
            print("<br>");
            //print("INSERT INTO cupos_atencion VALUES (default, ".$esp_id.", ".$doc_id.", '".$d."', '".$desde."', '".$hasta."', ".$cupos[$ds].", 0, 0, 0, 0, true, 0, ".$nom_id.", true, true);");
            
            print("<br>");
            print("<br>");
            print("<br>");
            pg_query("INSERT INTO cupos_atencion VALUES (default, ".$esp_id.", ".$doc_id.", '".$d."', '".$desde."', '".$hasta."', ".$cupos[$ds].", $sobre_cupos[$ds], 0, 0, 0, true, 0, ".$nom_id.", true, true);");
            
            
            $cantn=$cupos[$ds];
            //$cantc=$sobre_cupos[$ds];
            //if($cantc=="" || $cantc=="0")
            //{
                $cantc=0;
            //}
            
            $h1=explode(':', $desde);
            $h2=explode(':', $hasta);
            $hh1=($h1[0]*60)+($h1[1]*1);
            $hh2=($h2[0]*60)+($h2[1]*1);
            print("<br>");
            print("<br>");
            print($hh1);
            print("<br>");
            print("<br>");
            print($hh2);
            print("<br>");
            print("<br>");
            print("--".$cantn."--");
            print("<br>");
            print("<br>");
            print("--".$cantc."--");
            print("<br>");
            print("<br>");
            $dif=($hh2-$hh1)/($cantn);
            print("<br>");
            print("Diferencia entre horas: ".$dif);
            print("<br>");
            
            for($j=0;$j<($cantn);$j++)
            {
                $_hora=$hh1+($dif*$j);
		$hora=floor($_hora/60);
		$minutos=$_hora%60;
		
                if($minutos<10)
                    $minutos='0'.$minutos;
                
		$hr=$hora.':'.$minutos;
                
                //if($j<$cantn)
                //{
                    $nomd_extra="N";
                //}
                //else
                //{
                //    $nomd_extra="S";
                //}
                
                print("<br>");
                print("--------");
                print("<br>");
		print("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo,nomd_extra, nomd_hora, pac_id, nomd_diag_cod) VALUES (DEFAULT, $nom_id, '$ttipo','$nomd_extra', '$hr', 0, '');");
		print("<br>");
                print("--------");
                print("<br>");
                pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo,nomd_extra, nomd_hora, pac_id, nomd_diag_cod) VALUES (DEFAULT, $nom_id, '$ttipo','$nomd_extra', '$hr', 0, '');");
            }
            
        }
	flush();
    }
    
    pg_query("COMMIT;");
    print("PROFESIONALES FALTANTES: ".sizeof(array_unique($noprofs))." .");
	
    /*pg_query("UPDATE cupos_atencion SET cupos_horainicio=(SELECT 
    min(nomd_hora) 
    FROM nomina_detalle 
    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id),
    cupos_horafinal=(SELECT 
    max(nomd_hora) 
    FROM nomina_detalle 
    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id)+('30 minutes'::interval),
    cupos_cantidad_n=(SELECT 
    COUNT(*)
    FROM nomina_detalle 
    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id)						
    WHERE cupos_horainicio='00:00:00'");*/
?>
