<?php 
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    $l=explode("\n",utf8_decode(file_get_contents("agenda_libre_melipilla.csv")));
    //$nopacs=array();
    $noprofs=array();
    for($i=2;$i<sizeof($l);$i++)
    {
        if(trim($l[$i])=='')
            continue;
        
        $r=explode('|',$l[$i]);
        
        if(trim($l[$i])=='')
            continue;
        
        $r=explode('|',$l[$i]);
        
        $prut=strtoupper(str_replace('.','',trim($r[1].'-'.$r[2])));
        
        if($prut=='-')
            $prut='';
        
        $pnom=strtoupper(str_replace('.','',trim($r[3])));
	
        $p=cargar_registro("SELECT * FROM doctores WHERE doc_nombres='$pnom' OR (doc_rut='$prut' AND NOT doc_rut='')");
		
	if(!$p)
        {
            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $pnom NO EXISTE.<br>"); $noprofs[]=$prut;
            //$pnom=trim($r[3]);
            //$ppat=trim($r[4]);
            //$pmat=trim($r[5]);
	
            pg_query("INSERT INTO doctores VALUES (DEFAULT, '$prut', '', '', '$pnom');");
			
            $ttmp=cargar_registro("SELECT CURRVAL('doctores_doc_id_seq') AS id;");
			
            $doc_id=$ttmp['id']*1;

            print("[L&iacute;nea ".($i+1)."] PROFESIONAL $prut -- $pnom $ppat $pmat CREADO.<br>");
			
            //continue; 
			
	}
        else
        {
            $doc_id=$p['doc_id']*1;
		
	}
		
	$fechas_ausencias = cargar_registros_obj("
	SELECT DISTINCT
	ausencia_fechainicio, ausencia_fechafinal
	FROM ausencias_medicas
	WHERE (doc_id=$doc_id OR doc_id=0);
	", true);



        $codigo_esp=trim($r[0]);
		
	$e=cargar_registro("SELECT * FROM especialidades WHERE esp_desc='$codigo_esp';");
		
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

		

        //	$frut=strtoupper(str_replace('.','',trim($r[8])));
	$frut='22222222-2';
		
	$f=cargar_registro("SELECT * FROM funcionario WHERE func_rut='$frut'");
		
	if(!$f) { print("[L&iacute;nea ".($i+1)."] FUNCIONARIO $frut NO EXISTE.<br>"); continue; }
		
        $func_id=$f['func_id']*1;		
		
		
	if(trim($r[5])=='' OR trim($r[6])=='')
            continue;
		
	$nom_fecha1=strtoupper(str_replace('-','/',trim($r[5])));
	$nom_fecha2=strtoupper(str_replace('-','/',trim($r[6])));
		
		
	$dias=Array($r[7],$r[9],$r[11],$r[13],$r[15],$r[17]);
	$cupos=Array($r[8]*1,$r[10]*1,$r[12]*1,$r[14]*1,$r[16]*1,$r[18]*1);
		
	$tipo=trim($r[4]);
		
	switch($tipo)
        {
            case 'Consulta Nueva': $ttipo='N'; break;
            case 'Consulta Repetida': $ttipo='C'; break;
            default: $ttipo='P'; break;
        }
		
		
	//$cod_estado=$r[16]*1;
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
                continue;
    
            // Evita dias de ausencia médica global o propia del médico...
    
            $d=date('d/m/Y', $di);
            if($fechas_ausente AND array_search($d, $fechas_ausente))
                    continue;
    
            // Inserta los cupos de atención en el día seleccionado...
    
            pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_motivo, nom_tipo, nom_urgente, nom_fecha) VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '$tipo', 0, false, '$d');");



            $tmp=explode('-',$dias[$ds]);

            $desde=trim($tmp[0]);
            $hasta=trim($tmp[1]);
		
		

            pg_query("INSERT INTO cupos_atencion VALUES (
            default,
            $esp_id,
            $doc_id,
            '$d',
            '".$desde."',
            '".$hasta."',
            ".$cupos[$ds].",
            0,
            0,
            0,
            0,
            true,
            0,
            CURRVAL('nomina_nom_id_seq')
            );");
                
      
            $cantn=$cupos[$ds];
            $cantc=0;
                
            $h1=explode(':', $desde);
            $h2=explode(':', $hasta);
	  
            $hh1=($h1[0]*60)+($h1[1]*1);
            $hh2=($h2[0]*60)+($h2[1]*1);
	  
            $dif=($hh2-$hh1)/($cantn+$cantc);
	  
            for($j=0;$j<($cantn+$cantc);$j++)
            {
                $_hora=$hh1+($dif*$j);
		$hora=floor($_hora/60);
		$minutos=$_hora%60;
		if($minutos<10)
                    $minutos='0'.$minutos;
                
                $hr=$hora.':'.$minutos;
		  
		pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_hora, pac_id, nomd_diag_cod) 
		VALUES (DEFAULT, CURRVAL('nomina_nom_id_seq'), '$ttipo', '$hr', 0, '');");
		  
            }
        }
        //pg_query("COMMIT;");
	flush();
    }
    print("PROFESIONALES FALTANTES: ".sizeof(array_unique($noprofs))." .");
	
    /*pg_query("UPDATE cupos_atencion SET 
    cupos_horainicio=(SELECT 
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
