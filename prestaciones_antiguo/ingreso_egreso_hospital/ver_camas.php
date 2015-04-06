<?php 

	require_once('../../conectar_db.php');
	
	$ccamashtml = desplegar_opciones_sql( 
	  "SELECT tcama_num_ini || '-' || tcama_num_fin, tcama_tipo 
		FROM clasifica_camas  
	   ORDER BY tcama_num_ini", NULL, '', "");
	   
	$ccamas=cargar_registros_obj("SELECT * FROM clasifica_camas ORDER BY tcama_num_ini;", true);
	$tcamas=cargar_registros_obj("SELECT * FROM tipo_camas ORDER BY cama_num_ini;", true);
	$ucamas=cargar_registros_obj("SELECT * FROM hospitalizacion 
											JOIN pacientes ON pac_id=hosp_pac_id
											WHERE hosp_fecha_egr IS NULL", true);
	
	for($i=0;$i<sizeof($ucamas);$i++) {
		$uso[$ucamas[$i]['hosp_numero_cama']]='<b>' . $ucamas[$i]['pac_rut'] . '</b> ' . $ucamas[$i]['pac_appat'] . ' ' . $ucamas[$i]['pac_apmat'] . ' ' . $ucamas[$i]['pac_nombres'];
	}

?>

<html>
<title>Disponibilidad de Camas</title>

<?php cabecera_popup('../..'); ?>

<script>

	ccamas=<?php echo json_encode($ccamas); ?>;
	tcamas=<?php echo json_encode($tcamas); ?>;
	ucamas=<?php echo json_encode($uso); ?>;
	
	function paso2() {
	
		var o='<table style="width:100%;"><tr class="tabla_header"><td style="width:10%;">Nro.</td><td>Tipo</td><td style="width:50%;">Estado</td></tr>';
		var v=$('clasifica_cama').value.split('-');
		
		cc=0;		
		
		for(var i=0;i<tcamas.length;i++) {
			if(v[0]*1<=tcamas[i].cama_num_ini &&
				v[1]*1>=tcamas[i].cama_num_fin) {
				
					for(var n=tcamas[i].cama_num_ini;n<=tcamas[i].cama_num_fin;n++) {
						
						(cc%2==0)?clase='tabla_fila':clase='tabla_fila2';						
						
						o+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';"';
						o+='onMouseOut="this.className=\''+clase+'\';" '
						
						if(!ucamas[n])								
							o+=' onClick="usar_cama('+n+')" style="cursor:pointer;" ';											
						
						o+='><td style="text-align:center;font-weight:bold;">'+n;
						o+='</td><td style="text-align:center;">'+tcamas[i].cama_tipo;
						o+='</td><td>';
						
						if(ucamas[n])
							o+=ucamas[n];
						else
							o+='<span style="color:#00BB00;font-weight:bold;">Disponible</span>';						
						
						o+='</td></tr>';						

						cc++;
						
					}					


				}
		}	
		
		o+='</table>';
		
		$('lista_camas').innerHTML=o;
	
	}

	function usar_cama(ncama) {
	
		window.opener.$('nro_cama').value=ncama;
		fn=window.opener.cargar_cama.bind(window.opener);
		fn();	
		window.close();
	
	}	
	
</script>

<body class='fuente_por_defecto popup_background' onLoad='paso2();'>

<div class='sub-content'>
<table style='width:100%;'
<tr><td style='text-align:right;width:150px;'>
Clasificaci&oacute;n:
</td><td>
<select id='clasifica_cama' name='clasifica_cama'
onClick='paso2();'>
<?php echo $ccamashtml; ?>
</select>
</td></tr>
<tr><td colspan=2>
<div class='sub-content2' id='lista_camas' 
style='height:230px;overflow:auto;'>

</div>
</td></tr>
</table>


</div>

</body>
</html>