<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",utf8_decode(file_get_contents("clasifica_ges.csv")));

	if(isset($_GET['confirma'])) {
		
		//pg_query('truncate table monitoreo_ges;');
		//pg_query('truncate table monitoreo_ges_registro;');
		//pg_query('truncate table lista_dinamica_instancia;');
		//pg_query('truncate table lista_dinamica_caso;');
		
	}
  
  $mon=Array();
  
  $nuevas=0;

  $q=cargar_registros_obj("SELECT mon_id, mon_rut, mon_nombre, mon_fecha_inicio, mon_fecha_limite, mon_fecha_inicio::date, mon_fecha_limite::date FROM monitoreo_ges;");
  
  for($i=0;$i<sizeof($q);$i++) {
    $mon[$q[$i]['mon_id']*1]=$q[$i];
  }
	  
	for($i=1;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
	
		$r=explode('|', $f[$i]);
		
		for($j=0;$j<sizeof($r);$j++) {
			$r[$j]=pg_escape_string(trim($r[$j]));
		}
		
		$fing=$r[1];
		
		$rut=$r[3].'-'.$r[4];
		$nombre=$r[5];
		
		$fini=$r[7];
		$ffin=$r[8];
		
		$npatologia=trim(preg_replace('/\s+/', ' ',$r[6]));
		$ngarantia=trim(preg_replace('/\s+/', ' ',$r[12]));
		
		$condicion=$r[14];
		$cual=$r[15];
		
		$feve=$r[19];
		if($feve!='' AND $feve!='Err:509') $feve="'$feve'"; else $feve='null';
		
		$fprox=$r[26];
		if($fprox!='') $fprox="'$fprox'"; else $fprox='null';
		
		$obs=$r[27];
		
		$fmon=$r[32];
		if($fmon!='') {
			if(strlen($fmon)>8)
				$fmon="'$fmon'"; 
			else
				$fmon='CURRENT_DATE';
		} else 
			$fmon='null';
			
		$fmon=str_replace('-ago-','-08-',$fmon);
		
		$xproblema=$r[35];
		$xgarantia=$r[36];
		
		$rama=$r[37];
		
		$bandeja='';
		
		// (pst_patologia_interna='$xproblema' AND pst_garantia_interna='$xgarantia') OR 
		
		$tmp=cargar_registro("SELECT * FROM patologias_sigges_traductor WHERE (pst_problema_salud ilike '$npatologia' AND pst_garantia ilike '$ngarantia');");
		
		if(!$tmp)
			$tmp=cargar_registro("SELECT * FROM patologias_sigges_traductor WHERE (pst_patologia_interna='$xproblema' AND pst_garantia_interna='$xgarantia');");
		
		
		if($tmp)
			$pst_id=$tmp['pst_id'];
		else {
			print("ERROR [$i]: [$npatologia] [$ngarantia] --- [$xproblema] [$xgarantia] NO ENCONTRADOS.<br />");
			continue;
		}
		
		switch($condicion) {
			case 'Brecha Real - Citado': $condicion='Citados'; break;
			case 'Brecha Real - Citado Compra': $condicion='Citado Prestador Externo'; break;
			case 'Brecha Real - NSP 1': $condicion='NSP1'; break;
			
			case 'Brecha Real -Atendido Documento no Confecionado': $condicion='Brecha Real Documento No Confeccionado'; break;
			case 'Brecha Real - Atendido Documento no Confeccionado': $condicion='Brecha Real Documento No Confeccionado'; break;
			
      case 'Brecha Real - Falta Ficha': $condicion='Falta Ficha'; break;
			
			case 'Correo enviado menos de 30 Dias': $condicion='Correo Enviado'; break;
			case 'Correo enviado menor de 30 Dias': $condicion='Correo Enviado'; break;
			case 'Correo Enviado Menor a 30 Dias': $condicion='Correo Enviado'; break;
      case 'Correo enviado menos de 30 dias': $condicion='Correo Enviado'; break;
			
			case 'En Estudio Esquizofrenia': $condicion='Estudio Esquizofrenia'; break;
			
			case utf8_decode('Enviado a Extensión Horaria'): $condicion='Pendiente por Prestador'; break;
			case utf8_decode('Enviado a Extension Horaria'): $condicion='Pendiente por Prestador'; break;
			
			case utf8_decode('En Tabla Compra'): $condicion='Pendiente por Prestador'; break;
			
			case 'Prestacion otorgada': $condicion=utf8_decode('Prestación otorgada'); break;
			case 'Prestacion Otorgada': $condicion=utf8_decode('Prestación otorgada'); break;
			
			case 'Exceptuado - Temporalmente': $condicion=utf8_decode('Exceptuada Temporalmente'); break;
			case 'Exceptuado Temporalmente': $condicion=utf8_decode('Exceptuada Temporalmente'); break;
			
			case 'Derivacion no Pertinente': $condicion=utf8_decode('Derivación No Pertinente'); break;
			case 'Derivacion No Pertinente': $condicion=utf8_decode('Derivación No Pertinente'); break;
      
			case 'Brecha Real - Nomina de Estadistica': $condicion=utf8_decode('Brecha Real Falta Nómina de Estadística'); break;
			
			case 'Brecha Real - NSP 1 Compra': $condicion='NSP1'; break;
			case 'Brecha Real - Inubicable 1': $condicion='Brecha Real Inubicable'; break;
			case 'Brecha Real - Inubicable 2': $condicion='Brecha Real Inubicable'; break;
      
			case 'Sin Monitorear': $condicion=''; $fmon='null'; break;
      			
		}

		$condicion_l=str_replace('-','', $condicion);
		$condicion_l=str_replace(' ','%', $condicion_l);
		
		$tmp2=cargar_registro("SELECT * FROM lista_dinamica_condiciones WHERE nombre_condicion ILIKE '%$condicion_l%' ORDER BY nombre_condicion;");
		
		$id_condicion=$tmp2['id_condicion']*1;
		
		if($id_condicion==0) {
			print("$condicion<br/>");
      $fmon='null';
    }
	
    flush();
	
		
    $chk=cargar_registro("SELECT * FROM monitoreo_ges WHERE mon_pst_id=$pst_id AND mon_rut='$rut' AND mon_nombre='$nombre' AND mon_fecha_inicio='$fini' AND mon_fecha_limite='$ffin';");
		
    if(!$chk) {
		  
      if(isset($_GET['confirma'])) { 
      
        pg_query("INSERT INTO monitoreo_ges VALUES (DEFAULT, '$fing', 7, 7, null, null, false, $pst_id, '$rut', '$nombre', 0, '$fini', '$ffin', '$npatologia', '$ngarantia', $fmon, CURRENT_TIMESTAMP, '', '$rama');");
      
  		  if($fmon!='null')
			   pg_query("INSERT INTO monitoreo_ges_registro VALUES (DEFAULT, CURRVAL('monitoreo_ges_mon_id_seq'), 7, $fmon, $id_condicion, '$bandeja', '$obs', $fprox, '', '$cual', $feve);");

        $tmp=cargar_registro("SELECT CURRVAL('monitoreo_ges_mon_id_seq') AS id;");
        
        $nuevas++; 
        $mon_id=$tmp['id']*1;
       
      } else {
      
        $nuevas++;
        $mon_id=0;
      
      }
       
       
    } else {
    
      $mon_id=$chk['mon_id']*1; 
      //pg_query("UPDATE monitoreo_ges SET w"); 
    }
    
    $mon[$mon_id]['encontrado']=1; 
	  
	}
  
  $cerradas=0;
  $abiertas=0;
  
  foreach($mon AS $mon_id => $val) {

    if(!isset($val['encontrado'])) {
      $cerradas++;
    } else {
      $abiertas++;
    }

    flush();

    if(!isset($_GET['confirma'])) continue;

    if(!isset($val['encontrado'])) {
      pg_query("UPDATE monitoreo_ges SET mon_estado=true WHERE mon_id=".$mon_id);
    } else {
      pg_query("UPDATE monitoreo_ges SET mon_estado=false WHERE mon_id=".$mon_id);
    }
  
  }
  
  print("<br/><br/><br/>CERRADAS: $cerradas ABIERTAS: $abiertas NUEVAS: $nuevas TOTAL: <b><u>".($abiertas+$nuevas)."</u></b><br/><br/><br/>");

  //pg_query("COMMIT;");
    
?>
