<?php 

	require_once('conectar_db.php');

	$pac_id=$_GET['pac_id'];
	
	$pacientes = pg_query($conn, "
    SELECT 
    pac_rut, 
    pac_rut, 
    pac_appat || ' ' || pac_apmat || ' ' || pac_nombres,
    pac_ficha, pac_id, prev_id, prev_desc, pac_fc_nac,
    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	 date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	 date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
	 '' AS edad, prev_id, ciud_id

    FROM pacientes
    LEFT JOIN prevision USING (prev_id)
    WHERE
	    pac_id=$pac_id
    
    ");
  
    if(pg_num_rows($pacientes)>0)
    for($i=0; $i<pg_num_rows($pacientes); $i++) {
  
      $array[$i]=pg_fetch_row($pacientes);
  
      for($u=0;$u<count($array[$i]);$u++) {
        $array[$i][$u]=htmlentities($array[$i][$u]);
      }

		$array[$i][11]='';
      
      if($array[$i][8]*1>1) $array[$i][11].=$array[$i][8].' a&ntilde;os ';
		elseif($array[$i][8]*1==1) $array[$i][11].=$array[$i][8].' a&ntilde;o ';

		if($array[$i][9]*1>1) $array[$i][11].=$array[$i][9].' meses ';	
		elseif($array[$i][9]*1==1) $array[$i][11].=$array[$i][9].' mes ';

		if($array[$i][10]*1>1) $array[$i][11].=$array[$i][10].' d&iacute;as';
		elseif($array[$i][10]*1==1) $array[$i][11].=$array[$i][10].' d&iacute;a';

    }
    else
    $array='';

    print(json_encode($array));

?>