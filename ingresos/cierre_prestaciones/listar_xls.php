<?php

  require_once('../../conectar_db.php');
  
  $func_id=$_SESSION['sgh_usuario_id']*1;
  $filtro=$_GET['filtro'];
  $fecha1=pg_escape_string($_GET['fecha1']);
  $fecha2=pg_escape_string($_GET['fecha2']);
  //permiso_id in (700,701,702,703,704)

  $lista_morosos = cargar_registros_obj(
  "

select *, pacientes.prev_id as prevision from nomina_detalle  
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
 
 

	    header("Content-type: application/vnd.ms-excel");
      	header("Content-Disposition: filename=\"HistorialPedidos--.XLS\";");
    	$strip_html=true;
  	
 
  
  print('
  <table width=100%>
  <tr >
  <td><b>Nro</b></td>
  <td><b>RUT</b></td>
  <td><b>Paciente</b></td>
  <td><b>Prevision</b></td>
  <td><b>Prestacion</b></td>
  <td><b>Estado</b></td>
  </tr>
  
  ');
  
  for($i=0;$i<sizeof($lista_morosos);$i++) {
    $var=false;
	  $lock=false;
    $presta_paciente = $lista_morosos[$i];
    
    ($i%2==1)? $clase='tabla_fila': $clase='tabla_fila2';	
	
	 print('
	  <table width=100%>
	  <tr>
    <td>
    '.htmlentities($i).'
    </td>
    <td>
    '.htmlentities($presta_paciente['pac_rut']).'
    </td>
	<td>
    '.htmlentities($presta_paciente['pac_nobres']).' '.htmlentities($presta_paciente['pac_appat']).' '.htmlentities($presta_paciente['pac_apmat']).'
    </td>
    <td>
    '.htmlentities($presta_paciente['prev_desc']).'
    </td>
    <td>
    '.htmlentities($presta_paciente['nomd_codigo_presta']).'
    </td>
    ');
	//valores N y D no se que significa
		if($presta_paciente['nomd_diag_cod']==null)
		{
			 print('<td> N/A </td>');
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
						print('<td>
						    N/A
						    </td></tr>
						     </table>
						');
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
