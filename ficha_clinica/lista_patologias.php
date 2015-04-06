<?php 

	require_once('../conectar_db.php');

?>

<table style='width:100%;'>
	<tr style='background-color:#dddddd;'>
		<td colspan=3>Patolog&iacute;as Activas</td>
	</tr>

<?php 
	
	$pac_id=$_GET['pac_id']*1;
	$bod_id=$_GET['farma']*1;

	if(isset($_GET['accion'])) {
		
		if($_GET['accion']=='eliminar') {
			
			$pacpat_id=$_GET['pacpat_id']*1;
			
			// pacpat_func_id IS NULL OR
			
			pg_query("DELETE FROM pacientes_patologia WHERE pacpat_id=$pacpat_id AND pacpat_func_id=".$_SESSION['sgh_usuario_id']);
			
		}
		
		if($_GET['accion']=='agregar') {
			
			$pacpat_desc=pg_escape_string(strtoupper(trim(utf8_decode($_GET['pacpat_desc']))));
			
			$chk=cargar_registro("SELECT * FROM pacientes_patologia WHERE pac_id=$pac_id AND pacpat_descripcion ILIKE '%$pacpat_desc%'");
			
			if(!$chk) {
				pg_Query("INSERT INTO pacientes_patologia VALUES (DEFAULT, $pac_id, '$pacpat_desc', null, null, ".$_SESSION['sgh_usuario_id'].");");
			}
						
		}
		
	}
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id;");
	
	$l=cargar_registros_obj("SELECT * FROM monitoreo_ges WHERE mon_rut='".$pac['pac_rut']."' ORDER BY mon_patologia;", true);
	
	$x=0;
	
	if($l)
	for($i=0;$i<sizeof($l);$i++) {
		$clase=($x%2==0)?'tabla_fila':'tabla_fila2';
		print("<tr class='$clase'><td style='color:blue;'>".$l[$i]['mon_patologia']." (".$l[$i]['mon_garantia'].")</td><td>&nbsp;</td><td>&nbsp;</td></tr>");
		$x++;
	}

	$l=cargar_registros_obj("SELECT * FROM pacientes_patologia LEFT JOIN funcionario ON pacpat_func_id=func_id WHERE pac_id=$pac_id ORDER BY pacpat_descripcion;", true);
	
	if($l)
	for($i=0;$i<sizeof($l);$i++) {
		$clase=($x%2==0)?'tabla_fila':'tabla_fila2';
		print("<tr class='$clase'><td style='color:green;'>".$l[$i]['pacpat_descripcion']."</td><td style='font-size:11px;'>".$l[$i]['func_nombre']."</td><td style='width:50px;'><center><img src='iconos/delete.png' style='cursor:pointer;' onClick='eliminar_pat(".$l[$i]['pacpat_id'].");'></center></td></tr>");
		$x++;
	}

	$clase=($x%2==0)?'tabla_fila':'tabla_fila2';
	print("<tr class='$clase'><td style='color:green;' colspan=2><input type='hidden' id='pacpat_valid' name='pacpat_valid' /><input type='text' id='pacpat_nueva' name='pacpat_nueva' style='width:100%;' value='' /></td><td style='width:50px;'><center><img src='iconos/add.png' style='cursor:pointer;' onClick='agregar_pat();'></center></td></tr>");

?>

</table>

<?php if(($bod_id==3) OR ($bod_id==4)){?>

<script>

	seleccionar_patologia = function(d) {

      $('pacpat_valid').value=d[0];
      $('pacpat_nueva').value=d[1].unescapeHTML();
   }
    
    autocompletar_patologia = new AutoComplete(
      'pacpat_nueva', 
      'autocompletar_sql.php',
      function() {
        if($('pacpat_nueva').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=control_patologia&'+$('pacpat_nueva').serialize()+
					  '&bod='+encodeURIComponent($('bodega_id').value)
        }

      }, 'autocomplete', 650, 100, 150, 1, 1, seleccionar_patologia);

</script>

<?php } ?>
