<?php 

	require_once('../../conectar_db.php');

    $pat=pg_escape_string(trim(utf8_decode($_POST['pat'])));
    $estado=$_POST['estado']*1;
    $filtro=pg_escape_string(trim($_POST['filtro2']));
    $filtrogar=pg_escape_string(trim(utf8_decode($_POST['filtrogar'])));
    
    if($pat=="-1") {
		$pat_w="true";
	} else {
		$pat_w="trim(pst_patologia_interna)='".$pat."'";
	}
    
    
    //  AND (monr_estado=0 OR monr_estado IS NULL)
    
    if($estado==-2) {
		$estado_w='true';
	} elseif($estado==-1) {
		$estado_w='NOT mon_estado';
	} elseif($estado==0) {
		$estado_w='NOT mon_estado AND mon_fecha_limite>=CURRENT_DATE';
	} elseif($estado==1) {
		$estado_w='NOT mon_estado AND mon_fecha_limite<CURRENT_DATE';
	} elseif($estado==2) {
		$estado_w='mon_estado';
	} elseif($estado==3) {
		$estado_w="mon_estado AND mon_estado_sigges='Exceptuada'";
	}
	
	if($filtro!='') {
		$filtro_w="mon_rut ilike '%$filtro%' OR mon_nombre ilike '%$filtro%'";
	} else {
		$filtro_w='true';
	}

	if($filtrogar!='') {
		$filtrogar_w="trim(pst_garantia_interna)='".$filtrogar."'";
	} else {
		$filtrogar_w='true';
	}

    $lista=cargar_registros_obj("
    
		SELECT nombre_condicion, 
		SUM(CASE WHEN dias<=0 THEN 1 ELSE 0 END) AS vigentes, 
		SUM(CASE WHEN dias>0 THEN 1 ELSE 0 END) AS vencidos,
		COUNT(*) AS subtotal FROM (
    
        SELECT 

        *, 

		(CURRENT_DATE)-mon_fecha_limite AS dias,
		
		trim(pst_patologia_interna) AS pst_patologia_interna,

		trim(pst_garantia_interna) AS pst_garantia_interna

        FROM monitoreo_ges AS mg

        JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
        
        LEFT JOIN monitoreo_ges_registro AS mgr ON mgr.mon_id=mg.mon_id AND mgr.monr_estado=0
     
        LEFT JOIN lista_dinamica_condiciones ON id_condicion=COALESCE(monr_clase,'0')::integer
   
        WHERE $estado_w AND $pat_w AND $filtrogar_w AND $filtro_w
        
        ) AS foo
        
        GROUP BY nombre_condicion
        
        ORDER BY nombre_condicion

    ", true);

	if(!$lista) {
	
		print("<br /><br /><br />
		<img src='iconos/error.png' style='width:32px;height:32px;' />
		<br /><br />
		<font style='font-weight:bold;font-size:18px;'>NO HAY REGISTROS EN LA PATOLOGIA/ESTADO SOLICITADOS.</font>");
		
		exit();
		
	}


?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>Clasificaci&oacute;n</td>
		<td>Vigentes</td>
		<td>Vencidos</td>
		<td>Subtotal</td>
	</tr>
	
<?php 

	$ttotal['vigentes']=0;
	$ttotal['vencidos']=0;
	$ttotal['subtotal']=0;

	for($i=0;$i<sizeof($lista);$i++) {
		
		$clase=$i%2==0?'tabla_fila':'tabla_fila2';
		
		if($lista[$i]['nombre_condicion']=='')
			$lista[$i]['nombre_condicion']='<i>(Sin Clasificar...)</i>';
		
		if($lista[$i]['vencidos']==0) $color='green'; else $color='#ff4444';
		
		print("
		<tr class='$clase'>
		<td>".$lista[$i]['nombre_condicion']."</td>
		<td style='text-align:right;font-weight:bold;color:green;'>".number_format($lista[$i]['vigentes'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;color:$color;'>".number_format($lista[$i]['vencidos'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;'>".number_format($lista[$i]['subtotal'],0,',','.')."</td>
		</tr>
		");
		
		$ttotal['vigentes']+=$lista[$i]['vigentes']*1;
		$ttotal['vencidos']+=$lista[$i]['vencidos']*1;
		$ttotal['subtotal']+=$lista[$i]['subtotal']*1;
		
		
	}

		print("
		<tr class='tabla_header'>
		<td>Totales</td>
		<td style='text-align:right;font-weight:bold;'>".number_format($ttotal['vigentes'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;'>".number_format($ttotal['vencidos'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;'>".number_format($ttotal['subtotal'],0,',','.')."</td>
		</tr>
		");
	

?>	
	
	
</table>
