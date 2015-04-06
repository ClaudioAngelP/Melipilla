<?php

  require_once('../../conectar_db.php');
  
  $func_id=$_SESSION['sgh_usuario_id']*1;
  $filtro=$_POST['filtro'];
  $fecha1=pg_escape_string($_POST['fecha1']);
  $fecha2=pg_escape_string($_POST['fecha2']);
  //permiso_id in (700,701,702,703,704)

  $lista_morosos = cargar_registros_obj(
  "

select *, pacientes.prev_id as prevision,date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias
		 from nomina_detalle  
join nomina using(nom_id)
join pacientes using(pac_id)
join prevision on pacientes.prev_id=prevision.prev_id
where nomd_pago=0 and 
nom_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' and pacientes.prev_id in ($filtro)
  "
  );
  
  
  $ids='';
  
   for($i=0;$i<sizeof($lista_morosos)-1;$i++)
  {
  	$ids.=$lista_morosos[$i]['nomd_id'].'|';
  }
  $ids.=$lista_morosos[sizeof($lista_morosos)-1]['nomd_id'];
 
 
 
  print('<td>
<input type="hidden" name="ids" id="ids" size=10
  value="'.$ids.'">
  
</td>');
  
  print('
  <table width=100%>
  <tr class="tabla_header">
  <td><b>N°</b></td>
  <td><b>RUT</b></td>
  <td><b>Paciente</b></td>
    <td><b>Edad</b></td>
  <td><b>Prevision</b></td>
  <td><b>Fecha</b></td>
  <td><b>Prestacion</b></td>
  <td><b>Estado</b></td>
  </tr>
  
  ');
  
  for($i=0;$i<sizeof($lista_morosos);$i++) {
    $var=false;
	  $lock=false;
    $presta_paciente = $lista_morosos[$i];
    
    
    ($i%2==1)? $clase='tabla_fila': $clase='tabla_fila2';	
	
	 print('<tr class="'.$clase.'" style="height:30px;"
    onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
    onMouseOut="this.className=this.clase;">
    <td>
    '.htmlentities($i).'
    </td>
    <td>
    '.htmlentities($presta_paciente['pac_rut']).'
    </td>
	<td>
    '.htmlentities($presta_paciente['pac_nobres']).' '.htmlentities($presta_paciente['pac_appat']).' '.htmlentities($presta_paciente['pac_apmat']).'
    </td>');
	
	$edad_tipo='a';
			if($presta_paciente['edad_anios']*1>=1){
				print('<td>
			    '.htmlentities($presta_paciente['edad_anios']*1).' a'.'
			    </td>');
				}else {
					if($presta_paciente['edad_meses']*1<3){
						if($presta_paciente['edad_meses']*1==0){
								print('<td>
							    '.htmlentities($presta_paciente['edad_dias']*1).' d'.'
							    </td>');
							
						}
					 
					 		
					 }else{
							print('<td>
						    '.htmlentities($presta_paciente['edad_meses']*1).' m'.'
						    </td>');
							
						}		
				}		
    
    
	print('
    
    <td>
    '.htmlentities($presta_paciente['prev_desc']).'
    </td>
    <td>
    '.htmlentities(substr($presta_paciente['nom_fecha'],0,10)).' - '.htmlentities($presta_paciente['nomd_hora']).'
    </td>
    <td>
    '.htmlentities($presta_paciente['nomd_codigo_presta']).'
    </td>
    ');
	//valores N y D no se que significa
		if($presta_paciente['nomd_diag_cod']==null)
		{
			 print('<td> Atendido </td>');
		}else{
			
			if($presta_paciente['nomd_diag_cod']=='T')
			{
				print('<td>
				    Trasladado
				    </td>
				');
			}else{
			
				if($presta_paciente['nomd_diag_cod']=='NSP')
				{
					print('<td>
					    No se Presenta
					    </td>
					');
				}else{
			
					if($presta_paciente['nomd_diag_cod']=='X')
					{
						print('<td>
						    Removido
						    </td>
						');
					}else{
						if($presta_paciente['nomd_diag_cod']=='N')
						{
							print('<td>
							    No Atendido
							    </td>
							');
						}else{
							print('<td>
							   Atendido
							    </td>
							');
					}
					}
				}
			}
			 /*
			  print('<span class="texto_tooltip"
						
						onClick="abrir_recepcion(\''.$presta_paciente['doc_id'].'\');">'.
	                       $presta_paciente['doc_num'].
	                       '</span>
						   '); 
			  
			  */
		}
	
	} 
	
	
  print('</table>');
?>
