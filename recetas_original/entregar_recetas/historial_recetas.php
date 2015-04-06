<?php

    require_once('../../conectar_db.php');
    
      $paciente = $_GET['paciente'];
      $bodega_id = $_GET['bodega_id'];
      
?>

<div class='sub-content3' style='overflow:auto;'>

	<table width='100%;'>
	
<?php
            
     $rec=cargar_registros_obj("
     
			SELECT receta_id,
			COALESCE(log_fecha, receta_fecha_emision)::date AS fecha,
			receta_cronica,
			receta_numero,
			recetad_dias, recetad_horas, recetad_cant,
			ceil((((recetad_dias*24)/recetad_horas)*recetad_cant)/COALESCE(art_unidad_cantidad, 1)) AS total,
			(-stock_cant) AS despachado,
			receta_bod_id, art_glosa, art_codigo, 
			COALESCE(art_unidad_adm, forma_nombre) AS fnombre,
			recetad_indicaciones
			FROM receta
			LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
			LEFT JOIN logs ON log_recetad_id=recetad_id
			LEFT JOIN stock ON stock_log_id=log_id
			JOIN articulo ON recetad_art_id=art_id
			LEFT JOIN bodega_forma ON art_forma=forma_id
			WHERE receta_paciente_id=$paciente
			ORDER BY COALESCE(log_fecha, receta_fecha_emision) DESC;", true);
 
      print('
				<tr class="tabla_header" style="font-weight: bold;">
     	 		<td colspan=7>Detalle de Medicamentos Recetados</td></tr>
  		    	<tr class="tabla_header" style="font-weight: bold;">
  	      	<td>Fecha Despacho</td>
  	      	<td>Codigo Int.</td>
  	      	<td>Glosa</td>
	        	<td>Dosis</td>
	        	<td>Cant. Prescrip.</td>
	        	<td>Cant. Desp.</td>
	        	</tr>
		');
			
			for($j=0;$j<sizeof($rec);$j++) {
	
	    			($j%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	    			
	    			print('<tr class='.$clase.'>
	    				<td style="text-align: center;"><b>'.$rec[$j]['fecha'].'</b></td>
	    				<td style="text-align: right;"><b>'.$rec[$j]['art_codigo'].'</b></td>
	    				<td>'.$rec[$j]['art_glosa'].'</td>
	    			');
					
					
					if($rec[$j]['recetad_indicaciones']!='') 
						$indicaciones='<br />'.$rec[$j]['recetad_indicaciones'];
					else
						$indicaciones='';
									
			      print('
			      	<td style="text-align:center;">
			      	<i><b>'.number_format(($rec[$j]['recetad_cant']*1),2,',','.').' 
			      	'.$rec[$j]['fnombre'].'</b> cada '.$rec[$j]['recetad_horas'].'
			      	 horas durante '.$rec[$j]['recetad_dias'].' d&iacute;a(s).
			      	 </i>'.$indicaciones.'</td>
			      	<td style="text-align:right;"><b>'.number_format($rec[$j]['total']*1,1,',','.').'</b></td>
			      	<td style="text-align:right;"><b>'.number_format($rec[$j]['despachado']*1,1,',','.').'</b></td>
			      	</tr>
			      ');  

			} 
			
?> 

</table>
</div>

