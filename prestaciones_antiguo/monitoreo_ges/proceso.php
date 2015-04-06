<?php 

    require_once('../../conectar_db.php');
   
	/*


drop table lista_dinamica_caso;
drop table lista_dinamica_instancia;
drop table monitoreo_ges;
drop table monitoreo_ges_registro;
drop table lista_dinamica_proceso;
drop table lista_dinamica_condiciones;
drop table lista_dinamica_bandejas;
drop table patologias_sigges_traductor;


	 * */
   
    function tfecha($str) {
    
        $str = explode('/', trim($str));
        
        //return date('d/m/Y', mktime(0,0,0,$str[1]*1,(($str[0]*1)-1),(($str[2]*1)-4)));
        return date('d/m/Y', mktime(0,0,0,$str[1]*1,(($str[0]*1)),(($str[2]*1))));
    
    }

  $q=cargar_registros_obj("SELECT mon_id, mon_rut, mon_nombre, mon_fecha_inicio, mon_fecha_limite, mon_fecha_inicio::date, mon_fecha_limite::date FROM monitoreo_ges WHERE NOT mon_estado;");
  
  $nuevas=0;
  
  for($i=0;$i<sizeof($q);$i++) {
    $mon[$q[$i]['mon_id']*1]=$q[$i];
  }

    $errors=0;
    
    
	$f1=(file_get_contents($_FILES['vigentes']['tmp_name']));
	$t1=substr_count($f1,"</tr>");

	$f2=(file_get_contents($_FILES['vencidas']['tmp_name']));
    $t2=substr_count($f2,"</tr>");        
    
    $total=$t1+$t2;

	for($k=0;$k<2;$k++) {
    
    if($k==0) {
		$html=$f1;		// VIGENTES
		$table_size=8;
	}
	else 	  
	{	
		$html=$f2;		// VENCIDAS
		$table_size=9;
	}
	
	if(isset($registros)) unset($registros);
	 	
    preg_match_all('#<td class="htmltabladetalle1">(.*)</td>#', $html, $regtmp);
	 	
	for($i=0;$i<sizeof($regtmp[1]);$i++) {

		$num=floor($i/$table_size);
			
		$registros[$num][$i%$table_size]=$regtmp[1][$i];
	
	}		
		
	$totalr=sizeof($registros);

	for($i=0;$i<$totalr;$i++) {

		$reg=$registros[$i];

		if($k==0) {

			$test=explode('/',trim($reg[4]));
			$test2=explode('/',trim($reg[5]));
			
			if(count($test)!=3 OR count($test2)!=3) continue;

			$rut=(trim($reg[1])).'-'.trim(strtoupper($reg[2]));
			
			$pat=trim(preg_replace('/\s+/', ' ',$reg[0]));
			$nombre=pg_escape_string(trim($reg[3]));
			$finicio=tfecha($reg[4]);
			$flimite=tfecha($reg[5]);
			$garantia=trim(preg_replace('/\s+/', ' ',$reg[7]));
        
		} else {

			$test=explode('/',trim($reg[5]));
			$test2=explode('/',trim($reg[6]));
			
			if(count($test)!=3 OR count($test2)!=3) continue;

			$rut=pg_escape_string((trim($reg[0])).'-'.trim(strtoupper($reg[1])));

			$pat=trim(preg_replace('/\s+/', ' ',$reg[3]));
			$nombre=pg_escape_string(trim($reg[2]));
			$finicio=tfecha($reg[5]);
			$flimite=tfecha($reg[6]);
			$garantia=trim(preg_replace('/\s+/', ' ',$reg[8]));
        
		
		}

        $ptrad=cargar_registros_obj("
            SELECT * FROM patologias_sigges_traductor 
            WHERE   pst_problema_salud ilike '".pg_escape_string($pat)."' AND
                    pst_garantia ilike '".pg_escape_string($garantia)."'
        ");
        
        if(!$ptrad) {
			$error++;
			$ptrad[0]['pst_id']=-1;
		}

        $chk=cargar_registro("SELECT * FROM monitoreo_ges 
					WHERE mon_rut='$rut' AND 
					mon_fecha_inicio='$finicio' AND
					mon_fecha_limite='$flimite' AND
					(mon_pst_id=".$ptrad[0]['pst_id']." OR (mon_patologia='".pg_escape_string($pat)."' AND mon_garantia='".pg_escape_string($garantia)."'))
					");
					
		if(!$chk) {
   
			pg_query("INSERT INTO monitoreo_ges VALUES (
                    DEFAULT, CURRENT_TIMESTAMP, 
                    0, 0, null, null, 
                    false, 
                    ".$ptrad[0]['pst_id'].",
                    '$rut', '$nombre', 0, '$finicio', '$flimite',
                    '".pg_escape_string($pat)."', 
                    '".pg_escape_string($garantia)."'
                    )");
                    
            $tmp=cargar_registro("SELECT CURRVAL('monitoreo_ges_mon_id_seq') AS id;");
        
			$nuevas++; 
			$mon_id=$tmp['id']*1;


		} else {
		
			pg_query("UPDATE monitoreo_ges 
				SET mon_condicion=0, mon_fecha_monitoreo=CURRENT_TIMESTAMP
				WHERE mon_id=".$chk['mon_id']); 		

			$mon_id=$chk['mon_id']*1;
		
		}
                    
        $mon[$mon_id]['encontrado']=1; 

	 	$regs[$estado]++;
	 	
	 }

  } 


  foreach($mon AS $mon_id => $val) {

    if(!isset($val['encontrado'])) {
      $cerradas++;
    } else {
      $abiertas++;
    }

    flush();

    if(!isset($val['encontrado'])) {
      pg_query("UPDATE monitoreo_ges SET mon_estado=true WHERE mon_id=".$mon_id);
    } else {
      pg_query("UPDATE monitoreo_ges SET mon_estado=false WHERE mon_id=".$mon_id);
    }
  
  }
      
?>


<html>
<title>Abrir Proceso de Monitoreo GES</title>

<?php cabecera_popup('../..'); ?>

<script>

var puntero=0; var total=<?php echo $total; ?>

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/cog.png'> Actualizando Registro de Monitoreo GES</b>
</div>

<div class='sub-content'>
<img src='../../iconos/cog_go.png'> Resultado</b>
</div>

<div class='sub-content2' id='resultado' style='font-size:16px;'>
<?php print("<br/><br/><br/>CERRADAS: $cerradas<br /><br />NUEVAS: $nuevas<br /><br />ABIERTAS: $abiertas<br/><br/><br/>"); ?>
</div>


</body>
</html>
