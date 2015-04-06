<?php
    require_once('../../conectar_db.php');
    $doc_id=$_POST['doc_id']*1;
    $fecha=pg_escape_string($_POST['fecha']);
    $esp_id=$_POST['esp_id']*1;
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
    $cupos_id=pg_escape_string($_POST['cupos_id']);
    if($cupos_id=="")
    {
        print("
        <script>
            alert('ERROR: No se Ha seleccionado Cupos para Replicar, la transaccion fue cancelada.');
            window.close();
        </script>
        ");
        exit();
    }
    $string_cupos=str_replace("|",",",$cupos_id);
    pg_query("START TRANSACTION;");
    $dia=array();
    for($i=0;$i<7;$i++)
    {
        if(isset($_POST['dia_'.$i]))
            $dia[$i]=true;
        else
            $dia[$i]=false;
    }
    $consulta="
    SELECT DISTINCT
    date_trunc('day', cupos_fecha) AS cupos_fecha,
    cupos_horainicio, 
    cupos_horafinal, 
    cupos_id, 
    cupos_cantidad_n, 
    cupos_cantidad_c, 
    cupos_cant_i,
    cupos_cant_h,
    cupos_cant_e,
    cupos_extras,
    cupos_cant_r,
    esp_desc,
    esp_id,
    nom_motivo,
    cupos_ficha,
    cupos_adr,
    nom_tipo_contrato
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    left join nomina on cupos_atencion.nom_id=nomina.nom_id
    WHERE cupos_doc_id=$doc_id AND cupos_fecha='$fecha' and cupos_esp_id=$esp_id and cupos_id in ($string_cupos);
    ";

    $fechas = cargar_registros_obj($consulta);
    $fechas_ausencias = cargar_registros_obj("SELECT DISTINCT ausencia_fechainicio, ausencia_fechafinal FROM ausencias_medicas WHERE (doc_id=$doc_id OR doc_id=0);", true);
    $fechas_ausente=Array();
    
    for($i=0;$i<count($fechas_ausencias);$i++)
    {
        if($fechas_ausencias[$i]['ausencia_fechafinal']=='')
            $fechas_ausente[count($fechas_ausente)]=$fechas_ausencias[$i]['ausencia_fechainicio'];
        else
        {
            $finicio=explode('/',$fechas_ausencias[$i]['ausencia_fechainicio']);
            $ffinal=explode('/',$fechas_ausencias[$i]['ausencia_fechafinal']);
            $fi=mktime(0,0,0,$finicio[1],$finicio[0],$finicio[2]);
            $ff=mktime(0,0,0,$ffinal[1],$ffinal[0],$ffinal[2]);
            for(;$fi<=$ff;$fi+=86400)
            {
                $fechas_ausente[count($fechas_ausente)]=date('d/m/Y',$fi);
            }
        }
    }
    
    $fi=explode('/',$fecha1);
    $ff=explode('/',$fecha2);
    $di=mktime(0,0,0,$fi[1],$fi[0],$fi[2]);
    $df=mktime(0,0,0,$ff[1],$ff[0],$ff[2]);
    for(;$di<=$df;$di+=86400)
    {
        // Evita dias de la semana no chequeados...
        $ds=(date('w',$di)*1)-1;
        if($ds==-1)
            $ds=6;
        if(!$dia[$ds])
            continue;
        
        // Evita dias de ausencia médica global o propia del médico...
        $d=date('d/m/Y', $di);
        if(array_search($d, $fechas_ausente))
            continue;
        
        // Inserta los cupos de atención en el día seleccionado...
        for($i=0;$i<count($fechas);$i++)
        {
            $esp_id=$fechas[$i]['esp_id']*1;
            $desde=$fechas[$i]['cupos_horainicio'];
            $hasta=$fechas[$i]['cupos_horafinal'];
            $chk=cargar_registros_obj("
            SELECT * FROM cupos_atencion
            WHERE cupos_doc_id=$doc_id AND cupos_fecha='$d' AND 
            ((cupos_horainicio>='$desde' AND cupos_horafinal<='$hasta') OR
            (cupos_horainicio<='$desde' AND cupos_horafinal>='$hasta') OR
            (cupos_horainicio>'$desde' AND cupos_horainicio<'$hasta') OR
            (cupos_horafinal>'$desde' AND cupos_horafinal<'$hasta'))
            ");
            if($chk)
            {
                pg_query("ROLLBACK;");
		print("
                <script>
                    alert('ERROR: Este profesional ya tiene cupos creados en la fecha $fecha, la transaccion fue cancelada.');
                    window.close();
                </script>
		");
		exit();
            }
            if($fechas[$i]['nom_tipo_contrato']!=null and $fechas[$i]['nom_tipo_contrato']!="")
            {
                pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_centro_ruta, nom_tipo, nom_urgente, nom_fecha,nom_motivo,nom_tipo_contrato)
                VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '$tipo_atencion', 0, false, '$d','".$fechas[$i]['nom_motivo']."','".$fechas[$i]['nom_tipo_contrato']."');");
            }
            else
            {
                pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_centro_ruta, nom_tipo, nom_urgente, nom_fecha,nom_motivo)
                VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '$tipo_atencion', 0, false, '$d','".$fechas[$i]['nom_motivo']."');");
            }
            
            
            pg_query("INSERT INTO cupos_atencion VALUES (
            default,
            ".$fechas[$i]['esp_id'].",
            $doc_id,
            '$d',
            '".$fechas[$i]['cupos_horainicio']."',
            '".$fechas[$i]['cupos_horafinal']."',
            ".$fechas[$i]['cupos_cantidad_n'].",
            ".$fechas[$i]['cupos_cantidad_c'].",
            ".$fechas[$i]['cupos_cant_i'].",
            ".$fechas[$i]['cupos_cant_h'].",
            ".$fechas[$i]['cupos_cant_e'].",
            ".($fechas[$i]['cupos_extras']=='t'?'true':'false').",
            ".$fechas[$i]['cupos_cant_r'].",
            CURRVAL('nomina_nom_id_seq'),
            ".($fechas[$i]['cupos_ficha']=='t'?'true':'false').",
            ".($fechas[$i]['cupos_adr']=='t'?'true':'false')."
            );");
                
            $desde=$fechas[$i]['cupos_horainicio'];
            $hasta=$fechas[$i]['cupos_horafinal'];
            
            $cantn=$fechas[$i]['cupos_cantidad_n']*1;
            $cantc=$fechas[$i]['cupos_cantidad_c']*1;
                
            $h1=explode(':', $desde);
            $h2=explode(':', $hasta);
	  
            $hh1=($h1[0]*60)+($h1[1]*1);
            $hh2=($h2[0]*60)+($h2[1]*1);
	  
            $dif=($hh2-$hh1)/($cantn);
	  
            for($j=0;$j<($cantn);$j++)
            {
                $tipo='';
		$_hora=$hh1+($dif*$j);
                $hora=floor($_hora/60);
                $minutos=$_hora%60;
                if($minutos<10)
                    $minutos='0'.$minutos;
                
                $hr=$hora.':'.$minutos;
                pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_hora, pac_id,nomd_diag_cod) VALUES (DEFAULT, CURRVAL('nomina_nom_id_seq'), '$tipo', '$hr', 0,'');");
            }
        }
    }
    pg_query("COMMIT;");
    $fechas = cargar_registros_obj("
    SELECT DISTINCT
    date_trunc('day', cupos_fecha) AS cupos_fecha,
    cupos_horainicio, cupos_horafinal, cupos_id, 
    cupos_cantidad_n, cupos_cantidad_c, cupos_extras,
    esp_desc, cupos_cant_r
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    WHERE cupos_doc_id=$doc_id;
    ", true);
    
?>
<script>
    window.opener.fechas_ocupadas=<?php echo json_encode($fechas); ?>;
    window.opener.location.reload();
    window.close();
</script>
