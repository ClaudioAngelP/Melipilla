<?php

  require_once('../../conectar_db.php');
  
 

  $lista_morosos = cargar_registros_obj(
  "	select* from cierre_prestaciones
join funcionario on func_id=cierre_func_id "
  );
  
  
  $ids='';
  
  $t=explode('|',$ids);
   for($i=0;$i<sizeof($lista_morosos)-1;$i++)
  {
  	$ids.=$lista_morosos[$i]['cierre_id'].'|';
  }
  $ids.=$lista_morosos[sizeof($lista_morosos)-1]['cierre_id'];
 
 
 
  print('<td>
<input type="hidden" name="ids" id="ids" size=10
  value="'.$ids.'">
  
</td>');
  
  
  print('
  <table width=100%>
  <tr class="tabla_header">
  <td><b>N°</b></td>
  <td><b>ID</b></td>
  <td><b>Funcionario</b></td>
  <td><b>Fecha</b></td>
<td><b>Ver</b></td>
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
    '.htmlentities($presta_paciente['cierre_id']).'
    </td>
	<td>
    '.htmlentities($presta_paciente['func_nombre']).' '.htmlentities($presta_paciente['func_rut']).'
    </td>
    <td>
    '.htmlentities($presta_paciente['cierre_fecha']).'
    </td>
     <td>
			    <center>
			    <img src="iconos/magnifier.png" style="cursor: pointer;"
			    alt="Revisar Orden..."
			    title="Revisar Orden..."
			    onClick="ver_cierre(\''.$presta_paciente['cierre_id'].'\');">
			   
			  </td>
    ');
	
	
	
	} 
	
	
  print('</table>');
?>
