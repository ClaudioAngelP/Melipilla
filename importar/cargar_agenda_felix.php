<?php
    error_reporting(E_ALL);
    //require_once('../conectar_db.php');
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    require_once('../conectores/fonasa/cargar_paciente_fonasa.php');
    set_time_limit(0);
    //--------------------------------------------------------------------------
    function formatear_rut($str)
    {
        $partes=explode('-',$str);
	return number_format($partes[0]*1,0,',','.').'-'.strtolower($partes[1]);
    }
    //--------------------------------------------------------------------------
    function limpiar_caracteres_especiales($s)
    {
        $s = ereg_replace("[áàâãªä@]","a",$s);
        $s = ereg_replace("[ÁÀÂÃÄ]","A",$s);
        $s = ereg_replace("[éèêë]","e",$s);
        $s = ereg_replace("[ÉÈÊË]","E",$s);
        $s = ereg_replace("[íìîï]","i",$s);
        $s = ereg_replace("[ÍÌÎÏ]","I",$s);
        $s = ereg_replace("[óòôõºö]","o",$s);
        $s = ereg_replace("[ÓÒÔÕÖ]","O",$s);
        $s = ereg_replace("[úùûü]","u",$s);
        $s = ereg_replace("[ÚÙÛÜ]","U",$s);
        $s = str_replace("[¿?\]","_",$s);
        $s = str_replace(" ","-",$s);
        $s = str_replace("ñ","n",$s);
        $s = str_replace("Ñ","N",$s);
        //para ampliar los caracteres a reemplazar agregar lineas de este tipo:
        //$s = str_replace("caracter-que-queremos-cambiar","caracter-por-el-cual-lo-vamos-a-cambiar",$s);
        return $s;
    }
    
    
    
    
    
    $l=explode("\n",file_get_contents("agenda/agendados_1.csv"));
    $nopacs=array();
    $noprofs=array();
    pg_query("START TRANSACTION;");
    for($i=1;$i<sizeof($l);$i++)
    {
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        if(trim($l[$i])=='')
            continue;
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $r=explode(';',$l[$i]);
        $estado=strtolower(trim($r[9]));
        if($estado!=strtolower('Agendado') && $estado!=strtolower('Alta de Especialidad') && $estado!=strtolower('Atendido'))
        {
            print("<br>");
            print("Estado ".$estado." Distinto al esperado");
            print("<br>");
            continue;
	}
        
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        print('<br>');
        print('Linea '.$i.' Especialidad: '.trim($r[0]));
	print('<br>');
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        if(trim($r[1])=='')
        {
            /*
            if(strtoupper($codigo_esp)==strtoupper("Oftalmologia Hfb"))
            {
                $prut="Toma de Muestra Diabéticos";
            }
            elseif (strtoupper($codigo_esp)==strtoupper("Toma de Muestras HFB"))
            {
                $prut="Toma de Muestra Diabéticos";
            }
            else
            {
                $prut="SIN ASIGNAR MEDICO";
            }
            */
            $prut="SIN MEDICO/RECURSO ASIGNADO";
        }
        else
        {
            $prut=strtoupper(str_replace('.','',trim($r[1])));
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $encontrado=false;
        print("<br>");
        print("SELECT * FROM doctores WHERE upper(doc_nombre_trackare)=upper('$prut')");
        print("<br>");
        $p=cargar_registro("SELECT * FROM doctores WHERE upper(doc_nombre_trackare)=upper('$prut')");
        if($p)
        {
            $doc_id=$p['doc_id']*1;
            $encontrado=true;
        }
        else
        {
            print("<br>");
            print("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$prut')");
            print("<br>");
            $p=cargar_registro("SELECT * FROM doctores WHERE upper(doc_nombres)=upper('$prut')");
            if($p)
            {
                $doc_id=$p['doc_id']*1;
                $encontrado=true;
            }
     	}
        if(!$encontrado)
        {
            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $prut NO EXISTE.<br>");
            $noprofs[]=$prut;
            //$pnom=trim($r[3]);
            //$ppat=trim($r[4]);
            //$pmat=trim($r[5]);
            print("<br>");
            print("<br>");
            print("INSERT INTO doctores (doc_id,doc_rut,doc_paterno,doc_materno,doc_nombres,doc_nombre_trackare) VALUES (DEFAULT, '', '', '', upper('$prut'),upper('$prut'));");
            print("<br>");
            print("<br>");
            pg_query("INSERT INTO doctores (doc_id,doc_rut,doc_paterno,doc_materno,doc_nombres,doc_nombre_trackare) VALUES (DEFAULT, '', '', '', upper('$prut'),upper('$prut'));");
            $ttmp=cargar_registro("SELECT CURRVAL('doctores_doc_id_seq') AS id;");
            $doc_id=$ttmp['id']*1;
            print("<br>");
            print("<br>");
            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $prut -- ".print_r($noprofs)." CREADO.<br>");
            print("<br>");
            print("<br>");
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $codigo_esp=trim($r[0]);
        print("<br>");
        print("SELECT * FROM especialidades WHERE upper(esp_desc)=upper('$codigo_esp');");
        print("<br>");
	$e=cargar_registro("SELECT * FROM especialidades WHERE upper(esp_desc)=upper('$codigo_esp');");
        if(!$e)
        {
            print("<br>");
            print("<br>");
            print("[L&iacute;nea ".($i+1)."] ESPECIALIDAD $codigo_esp NO EXISTE.<br>");
            print("<br>");
            print("<br>");
            pg_query("INSERT INTO especialidades VALUES (DEFAULT, upper('$codigo_esp'));");
            $ttmp=cargar_registro("SELECT CURRVAL('especialidades_esp_id_seq') AS id;");
            $esp_id=$ttmp['id']*1;
            print("<br>");
            print("<br>");
            print("[L&iacute;nea ".($i+1)."] ESPECIALIDAD $codigo_esp CREADA.<br>"); 
            print("<br>");
            print("<br>");
        }
        else
        {
            $esp_id=$e['esp_id']*1;
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $func_id=7;
        $nom_fecha=strtoupper(str_replace('-','/',trim($r[2])));
        $fecha_asigna="'".strtoupper(str_replace('-','/',trim($r[4])))." ".$r[5]."'";
        $nomd_hora=strtoupper(str_replace('-','/',trim($r[3])));
        $funcionario_nombre=strtoupper(trim($r[6]));
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
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
        $direccion= str_replace("'"," ",$r[16]);
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
        if(trim($pac_rut)!='')
        {
            print("<br/>");
            print("SELECT * FROM pacientes WHERE pac_rut='$pac_rut' OR pac_ficha='$pac_ficha' OR (CASE WHEN pac_ficha='' THEN false ELSE pac_ficha::text='$pac_ficha' END)");
            print("<br/>");
            $nombres=trim($r[13]);
            $nombres=strtoupper(str_replace(',','',trim($nombres)));
            $nombres=strtoupper(str_replace('.','',trim($nombres)));
            $name=explode(' ',$nombres);
            $paterno=$name[0];
            $materno=$name[1];
            $nombres=$name[2];
            
            if(isset($name[3]))
                $nombres.=' '.$name[3];
            if(isset($name[4]))
                $nombres.=' '.$name[4];
            
            $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$pac_rut' OR pac_ficha='$pac_ficha' OR (CASE WHEN pac_ficha='' THEN false ELSE pac_ficha::text='$pac_ficha' END)");
            if(!$pac)
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://10.5.130.50/produccion/conectores/trakcare/login.php?buscar=".urlencode(formatear_rut($pac_rut)));
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $pac_id=curl_exec($ch);
                curl_close($ch);
                print("<br>");
                print("<br>");
                print("[L&iacute;nea ".($i+1)."] Paciente (".$pac_rut." ".$pac_ficha.") NO EXISTE.<br>");
                print("<br>");
                print("<br>");
                //$pac_id=importar_paciente($pac_rut, $pac_ficha);
                //$resp=pac_fonasa_return($pac_rut,$pac_ficha);
                //pac_fonasa($pac_rut,$pac_ficha);
                $fc_nac=trim($r[15]);
                $fc_nac=strtoupper(str_replace('-','/',trim($fc_nac)));
                $sexo=trim($r[21]);
                if(trim($sexo)=="M")
                    $sexo=0;
                else
                    $sexo=1;
                $direccion=$direccion;
                $comuna=trim($r[17]);
                $ciud_id = cargar_registro("SELECT ciud_id FROM comunas WHERE ciud_desc='".$comuna."'", true);
                if($ciud_id)
                {
                    $ciud_id = $ciud_id['ciud_id'];
                }
                else
                {
                    $ciud_id =-1;
                }
                $comuna=$ciud_id;
                $telefono=trim($r[12]);
                pac_fonasa_2($pac_rut,$pac_ficha,$nombres,$paterno,$materno,$fc_nac,$sexo,$direccion,$comuna,$telefono);
                $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$pac_rut' OR pac_ficha='$pac_ficha' OR (CASE WHEN pac_ficha='' THEN false ELSE pac_ficha::text='$pac_ficha' END)");
                $pac_id=$pac['pac_id']*1;
                //pg_query("update pacientes set pac_ficha='$pac_ficha',pac_direccion='$direccion' where pac_id=$pac_id;");
                print("<br>");
                print("<br>");
                print("[L&iacute;nea ".($i+1)."] Paciente (".$pac_rut." ".$pac_ficha.") CREADO.<br>"); 
                print("<br>");
                print("<br>");
                $nopacs[]=$pac_rut;
                //continue;
            }
            else
            {
                $pac_id=$pac['pac_id']*1;
                pg_query("update pacientes set pac_nombres='$nombres',pac_appat='$paterno',pac_apmat='$materno', pac_ficha='$pac_ficha',pac_direccion='$direccion' where pac_id=$pac_id;");
            }
        }
        else
        {
            print("<br/>");
            print("<br/>");
            print("Falto Usurio : ".$i );
            print("<br/>");
            print("<br/>");
            continue;
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        if(trim($r[8])=='')
        {
            print("<br/>");
            print("<br/>");
            print("Linea Falto Tipo Atencion : ".$i );
            print("<br/>");
            print("<br/>");
            continue;
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $motivo=$r[8];
        print("<br>");
      	print("SELECT * FROM nomina WHERE nom_fecha='$nom_fecha' AND nom_esp_id=$esp_id AND nom_doc_id=$doc_id and lower(nom_motivo)=lower('$motivo');");
        print("<br>");
        $nom=cargar_registro("SELECT * FROM nomina WHERE nom_fecha='$nom_fecha' AND nom_esp_id=$esp_id AND nom_doc_id=$doc_id and lower(nom_motivo)=lower('$motivo');");
        if(!$nom)
        {
            pg_query("INSERT INTO nomina VALUES (DEFAULT, 'RE_AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 0, false, '$nom_fecha',null,null,null,null,null,initcap(lower('$motivo')));");
            pg_query("INSERT INTO cupos_atencion VALUES (DEFAULT, $esp_id,$doc_id, '$nom_fecha', '00:00:00', '00:00:00', 0, 0, 0, 0, 0, true, 0, CURRVAL('nomina_nom_id_seq'),true,true);");
            $nom_id="CURRVAL('nomina_nom_id_seq')";
        }
        else
        {
            $nom_id=$nom['nom_id']*1;
	}
        $presta_glosa=trim($r[10]);
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
	$tt=trim($r[7]);
        if($tt=='')
	{
            $extra='N';
	}
        else
        {
            $extra='S';
	}
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        switch($motivo)
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
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $diag_cod='';
        $nom_detalle=cargar_registro("select * from nomina_detalle where nom_id=$nom_id and nomd_hora='$nomd_hora' and pac_id=$pac_id");
        if($nom_detalle)
        {
            $nomd_id=$nom_detalle['nomd_id']*1;
            print("<br>");
            print("Linea ".$i."");			
            print("<br>");
            print("Paciente encontrado en la nomina con la misma Hora|Linea - ".$i."|Nomina_id - ".$nom_id."|Nomina_detalle_id - ".$nomd_id."|Nom_fecha - ".$nom_fecha."|Nomd_hora - ".$nomd_hora."|Pac_id - ".$pac_id."|Motivo - ".$motivo."|Espeacilidad - ".$esp_id);
            print("<br>");
            print("<br>");
        }
	else
	{
            $nom_detalle=cargar_registro("select * from nomina_detalle where nom_id=$nom_id and nomd_hora='$nomd_hora' and pac_id=0");
            if(!$nom_detalle)
            {
                $nom_detalle=cargar_registro("select * from nomina_detalle where nom_id=$nom_id and pac_id=0 ORDER BY date_part( 'epoch', nomd_hora-'09:00') ASC LIMIT 1");		
            }
            if(!$nom_detalle)
            {
                $extra='S';
		/*
                print("<br>");
                print("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_extra, nomd_hora, pac_id, nomd_diag_cod, nomd_func_id, nomd_fecha_asigna) VALUES (DEFAULT, ".$nom_id.", 'N', '$extra', '$nomd_hora', ".$pac_id.", '$diag_cod', ".$func_id.", $fecha_asigna);");
                print("<br>");
                */
                pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_extra, nomd_hora, pac_id, nomd_diag_cod, nomd_func_id, nomd_fecha_asigna,nomd_presta_glosa,nomd_motivo_sobrecupo) VALUES (DEFAULT, ".$nom_id.", '$ttipo', '$extra', '$nomd_hora', ".$pac_id.", '$diag_cod', ".$func_id.", $fecha_asigna,'$presta_glosa','$tt');");
            }
            else
            {
                $nomd_id=$nom_detalle['nomd_id']*1;
                /*
                print("<br>");
                print("update nomina_detalle set pac_id=".$pac_id.", nomd_tipo='N', nomd_extra='$extra', nomd_diag_cod='$diag_cod', nomd_func_id=".$func_id.",nomd_fecha_asigna=$fecha_asigna, nomd_presta_glosa='$presta_glosa', nomd_motivo_sobrecupo='$tt' where nomd_id=".$nomd_id."");
                print("<br>");
                */
                pg_query("update nomina_detalle set pac_id=".$pac_id.", nomd_tipo='$ttipo', nomd_extra='$extra', nomd_diag_cod='$diag_cod',nomd_hora='$nomd_hora', nomd_func_id=".$func_id.",nomd_fecha_asigna=$fecha_asigna, nomd_presta_glosa='$presta_glosa', nomd_motivo_sobrecupo='$tt' where nomd_id=".$nomd_id."");
            }
        }
        flush();
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
    }
    print("PROFESIONALES FALTANTES: ".sizeof(array_unique($noprofs))." PACIENTES FALTANTES: ".sizeof(array_unique($nopacs)));
    pg_query("UPDATE cupos_atencion 
    SET cupos_horainicio=(
        SELECT 
        min(nomd_hora) 
	FROM nomina_detalle 
	WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND NOT nomd_hora='00:00:00'
    ),
    cupos_horafinal=(
        SELECT max(nomd_hora) FROM nomina_detalle 
        WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND NOT nomd_hora='00:00:00'
        )+('30 minutes'::interval)
    , 
    cupos_cantidad_n=(
        SELECT COUNT(*) FROM nomina_detalle 
        WHERE nomina_detalle.nom_id=cupos_atencion.nom_id
    )
    WHERE cupos_horainicio='00:00:00'");
    pg_query("COMMIT;");
?>