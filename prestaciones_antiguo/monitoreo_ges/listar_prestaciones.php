<?php
  require_once('../../conectar_db.php');
  function img($t) {

  $ca_id = $_POST['casos_auge']*1;
  if($ca_id!=-1) {

	  list($ca)=cargar_registros_obj("
	  if($ca) {
	  	$pac_id=$ca['ca_pac_id'];
	  } else { 
	  	$pac_id=$_POST['pac_id'];
	  	$ca['id_sigges']=0;
	  }
  
  	$caso_w='id_caso='.$ca['id_sigges'];
  
  } else { 
 
	$pac_id=$_POST['pac_id'];
	$caso_w='true'; 
  
  }

    COALESCE(pac_rut, pac_pasaporte, pac_id::text) AS pac_codigo,
    prestacion.id_sigges, porigen_nombre, esp_desc
    
  ");*/

<table style='width:100%;' class='lista_small'>
<?php 

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    print("
    ");
	 
		print("<td>".img(($lista[$i]['inst_id']==$sgh_inst_id)?'t':'f')."</td>");
    print("<td>".img(($lista[$i]['id_sigges']*1>0)?'t':'f')."</td>");
		print("
		<td><center><img src='../../iconos/magnifier.png' style='cursor:pointer;'
		onClick='abrir_presta(".$lista[$i]['presta_id'].");'></center></td>
  }
?>
</table>