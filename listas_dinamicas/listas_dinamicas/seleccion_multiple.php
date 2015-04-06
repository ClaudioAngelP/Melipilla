<?php 

	require_once('../conectar_db.php');
	
    $pat=pg_escape_string(trim(utf8_decode($_POST['pat'])));
    $filtrogar=pg_escape_string(trim(utf8_decode($_POST['filtrogar'])));
    
    $filtro=false;
    
    if($pat=="") {
		$pat_w="true";
	} else {
		$pat_w="trim(pst_patologia_interna)='".$pat."'";
		$filtro=true;
	}
    
	if($filtrogar!='') {
		$filtrogar_w="trim(pst_garantia_interna)='".$filtrogar."'";
		$filtro=true;
	} else {
		$filtrogar_w='true';
	}

	$lista_id=$_POST['lista_id']*1;

	$li=cargar_registro("SELECT * FROM lista_dinamica WHERE lista_id=$lista_id");	
	
	$dest=cargar_registro("SELECT array_to_string(lista_id_destino,',') AS ids FROM lista_dinamica WHERE lista_id=$lista_id");
	
	if($dest['ids']!='') {

		$lavanzar=pg_query("
			SELECT lista_id, lista_nombre FROM lista_dinamica 
			WHERE lista_id IN (".$dest['ids'].");
		");
		
		$lista_html='';
		
		while($v=pg_fetch_assoc($lavanzar)) {
			$lista_html.='<option value="'.$v['lista_id'].'" style="color:green;">'.htmlentities($v['lista_nombre']).' &gt;&gt;</option>';		
		}
		

	} else
		$lista_html='';

	$lista=cargar_registro("SELECT COUNT(*) AS total FROM (SELECT *, 
					(CURRENT_DATE-in_fecha::date)::integer AS dias2,
					(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
					trim(pst_patologia_interna) AS pst_patologia_interna,
					trim(pst_garantia_interna) AS pst_garantia_interna
					FROM lista_dinamica_instancia 
					JOIN lista_dinamica_caso USING (caso_id)
					LEFT JOIN monitoreo_ges USING (mon_id)
					LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
					JOIN pacientes USING (pac_id)
					WHERE lista_id=$lista_id AND in_estado=0 AND $pat_w AND $filtrogar_w) AS foo;");
	

?>

<html>
<title>Selecci&oacute;n M&uacute;ltiple de Registros</title>

<?php cabecera_popup('..'); ?>

<script>

validar_cantidad=function() {
	
	var val=$('cantidad').value*1;
	
	if(val<0 || val><?php echo $lista['total']*1; ?>) {
		$('cantidad').style.background='red';
	} else {
		$('cantidad').style.background='yellowgreen';
	}
	
}

guardar_seleccion=function() {
	
	
	if(!confirm('&iquest;Est&aacute; seguro que desea modificar todos los registros con el mismo valor?'.unescapeHTML())) {
		return;
	}
	
	var myAjax=new Ajax.Request(
		'sql_seleccion_multiple.php',
		{
			method:'post',
			parameters:$('seleccion').serialize(),
			onComplete:function(r) {
				
				alert(r.responseText);
				
			}
		}
	);
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<form id='seleccion' name='seleccion' >

<input type='hidden' id='lista_id' name='lista_id' value='<?php echo $lista_id; ?>' />

<input type='hidden' id='pat' name='pat' value='<?php echo htmlentities($pat); ?>' />
<input type='hidden' id='filtrogar' name='filtrogar' value='<?php echo htmlentities($filtrogar); ?>' />


<table style='width:100%;'>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Seleccionar (min:1 max:<?php echo $lista['total']; ?>)</td>
		<td class='tabla_fila'>
		<input type='text' id='cantidad' name='cantidad' 
		style='background-color:yellowgreen;'
		onKeyUp='validar_cantidad();' value='<?php echo $lista['total']; ?>' />
		</td>
	</tr>


<?php
	
		$id=0;

		if($li['lista_campos_tabla']!='') {
		
			$campos=explode('|', $li['lista_campos_tabla']);
			$valores=explode('|', $r['in_valor_tabla']);
			
			for($i=0;$i<sizeof($campos);$i++) {
			
				if(strstr($campos[$i],'>>>')) {
					$cmp=explode('>>>',$campos[$i]);
					$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
				} else {
					$cmp=$campos[$i]; $tipo=2;
				}
				
				print("<tr><td class='tabla_fila2' style='text-align:right;'>".$nombre.":</td><td class='tabla_fila'>");
				
				if($tipo==0) {

					if(isset($valores[$i])) 
						$vact=($valores[$i]=='true')?'CHECKED':'';
					else 
						$vact='';

					print("<input type='checkbox' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' $vact />");	

				} elseif($tipo==1) {

					if(isset($valores[$i])) 
						$vact=($valores[$i]=='true')?'CHECKED':'';
					else 
						$vact='CHECKED';

					print("<input type='checkbox' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' $vact />");
									
				} elseif($tipo==5) {
				
					$opts=explode('//', $cmp[2]);
								
					if(isset($valores[$i])) 
						$vact=$valores[$i];
					else 
						$vact='';

					print("<select id='campo_".$i."_".$id."' name='campo_".$i."_".$id."'>");
					
					for($k=0;$k<sizeof($opts);$k++) {
						
						$opts[$k]=trim($opts[$k]);
						
						if($vact==$opts[$k]) $sel='SELECTED'; else $sel='';
						
						print("<option value='".$opts[$k]."' $sel>".$opts[$k]."</option>");	
					}			
					
					print("</select>");		
					
				} elseif($tipo==10) {

					if(isset($valores[$i])) 
						$vact=$valores[$i];
					else 
						$vact='';
					
					print("<textarea id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' style='width:100%;height:20px;'>$vact</textarea>");
									
				} else {

					if(isset($valores[$i])) 
						$vact=$valores[$i];
					else 
						$vact='';
					
					print("<input type='text' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' value='$vact' />");
									
				}	
				
				print("</td></tr>");	
				
			}
			
		}
			
		print("<tr><td class='tabla_fila2' style='text-align:right;'>Estado:</td>");
			
		print("<td class='tabla_fila'><select id='sel_0' name='sel_0'>
		<option value='0'>(Sin Cambios...)</option>
		$vhtml
		$lista_html
		</select></td></tr>");
			

?>	
	
	
</table>

<center>
<br /><br />
<input type='button' id='' name='' value='--- Guardar Registros... ---' />

</center>

</form>

</body>


</html>




