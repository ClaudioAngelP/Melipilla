<?php
  require_once('../../conectar_db.php');
  function img($t) {

  $filtro= pg_escape_string(utf8_decode(trim($_POST['filtro'])));
  
  switch($tipo) {

		case 1: $tipo_w='(fap_tipo_atencion=1)'; break;
		case 2: $tipo_w='(fap_tipo_atencion=2)'; break;
		case 3: $tipo_w='(fap_tipo_atencion=3 OR fap_tipo_atencion=4)'; break;
		case 5: $tipo_w='(fap_tipo_atencion=5)'; break;
  	
  }
  
	if($filtro=='') {
		$filtro_w='true';	
	} else {
		$filtro_w="((pac_appat || ' ' || pac_apmat || ' ' || 
					   pac_nombres || ' ' || pac_rut || ' ' || pac_ficha) ILIKE '%$filtro%')";
	}				  
  
  
  if($tipo!=5) {
  	
  $lista = cargar_registros_obj("
	  
	  
			pacientes.pac_appat, 
			pacientes.pac_apmat, 
			pacientes.pac_nombres, 
			pacientes.pac_id, 
			pacientes.pac_rut, 
			pacientes.pac_ficha
			
	  FROM fap
	  
  
  } else {

  $lista = cargar_registros_obj("
	  
	  
			pacientes.pac_appat, 
			pacientes.pac_apmat, 
			pacientes.pac_nombres, 
			pacientes.pac_id, 
			pacientes.pac_rut, 
			pacientes.pac_ficha,
			fappab_pabellones.*, 
			funcionario.*,
			date_trunc('second',fap_fecha)::time AS fap_hora
			
	  FROM fap_pabellon
	  LEFT JOIN funcionario ON funcionario.func_id=fap_pabellon.func_id		
	  WHERE fap_fecha::date='$fecha' AND $filtro_w
	  
  	
  }
?>
<table style='width:100%;' class='lista_small'>
<tr class='tabla_header'>
<?php if($tipo==5) { ?> <td>Hora</td> <td>Pabell&oacute;n</td> <?php } ?>
<td>Nro. Ficha</td>

<?php if($tipo!=5) { ?>
<td>Paterno</td>

<?php } else { ?>
<td>Nombre</td>
<?php } ?>

<?php if($tipo==5) { ?> <td>Funcionario</td> <?php } ?>

<?php if($tipo==5) { ?> <td>Imprimir</td> <?php } ?>
</tr>
<?php 
  if($lista)
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    print("
    ");
    
	 if($tipo==5) {
	    print("
	    ");
	    print("
	    ");
	 }    
    
    print("
    ");
    
    if($tipo!=5)
	    print("
	    ");
    else 
	    print("
	    ");    
    
	if($tipo==5) {
    print("
    ");		
	}    
    
    if(_cax(208)) {
    print("<td>
    } elseif($lista[$i]['func_id']==$_SESSION['sgh_usuario_id'] AND $lista[$i]['func_id2']==0) {
    print("<td>
    } elseif($tipo<5) { 
    print("<td>
    } else {
    	print("<td><center>&nbsp;</center></td>");	
    }
    
    if($tipo==5) {
    print("
    <td>
    ");	
    }
    
	 print("
    </tr>
  }
?>
</table>


<script>
	
	datos_fap=<?php echo json_encode($lista); ?>;
	
	if(datos_fap)
   	$('ver_refs').innerHTML='Total de Registros: <b>'+datos_fap.length+'</b>';
   else
   	$('ver_refs').innerHTML='<i>No hay registros.</i>';

</script>