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

	$lista_id=pg_escape_string($_POST['lista_id']);

	$li=cargar_registro("SELECT * FROM lista_dinamica_bandejas WHERE codigo_bandeja='$lista_id';");	
		

?>

<html>
<title>Selecci&oacute;n M&uacute;ltiple de Registros</title>

<?php cabecera_popup('..'); ?>

<script>

validar_cantidad=function() {
	
	var val=$('cantidad').value*1;
	
	if(val<0 || val>($('max_cant').innerHTML*1)) {
		$('cantidad').style.background='red';
	} else {
		$('cantidad').style.background='yellowgreen';
	}
	
}

function chequear_fechas() {

        var chequear=true;

        $$('input[class="ld_fechas"]').each(function(element) {
								if(trim(element.value)=='') {
									element.style.background='';
									element.value='';
								} else {
									
									if(!validacion_fecha(element) ) {
										alert("Fecha ingresada no es v&aacute;lida.".unescapeHTML());
										$(element).focus();
										chequear=false;
									}
									
								}
                        });

        return chequear;

}


guardar_seleccion=function() {
	
	 if(!chequear_fechas()) return;
	
	if(!confirm('&iquest;Est&aacute; seguro que desea modificar todos los registros con el mismo valor?'.unescapeHTML())) {
		return;
	}
	
	var myAjax=new Ajax.Request(
		'sql_seleccion_multiple.php',
		{
			method:'post',
			parameters:$('seleccion').serialize(),
			onComplete:function(r) {
				
				alert('Modificaci&oacute;n realizada exitosamente.'.unescapeHTML());
				var fn=window.opener.cargar_lista.bind(window.opener);
				fn();	window.close(); 
				
			}
		}
	);
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<form id='seleccion' name='seleccion' >

<input type='hidden' id='ids' name='ids' value='' />
<input type='hidden' id='lista_id' name='lista_id' value='<?php echo $lista_id; ?>' />

<table style='width:100%;'>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Seleccionar (min:1 max:<span id='max_cant'></span>)</td>
		<td class='tabla_fila'>
		<input type='text' id='cantidad' name='cantidad' 
		style='background-color:yellowgreen;'
		onKeyUp='validar_cantidad();' value='' />
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
									
				} elseif($tipo==3) {

                                print("<input type='text' class='ld_fechas' style='text-align:center;' size=10 onBlur='validacion_fecha(this);' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' value='".$valores[$i]."' />");

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
						
						print("<option value='".htmlentities($opts[$k])."' $sel>".htmlentities($opts[$k])."</option>");	
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
			
		print("
	
		<tr><td class='tabla_fila2' style='text-align:right;'>Estado:</td>
		<td class='tabla_fila' id='selector'>
		</td></tr>
    
    ");
    
     if(_cax(57)) { 
     
     $b=cargar_registros_obj("SELECT * FROM lista_dinamica_bandejas ORDER BY nombre_bandeja;", true);
     
     $bandejas='';
     
     for($i=0;$i<sizeof($b);$i++) {
        $bandejas.="<option value='".$b[$i]['codigo_bandeja']."'>".$b[$i]['nombre_bandeja']."</option>";
     }
     
     print("
      <tr><td class='tabla_fila2' style='text-align:right;' valign='top'>Bandeja Especial:</td>
		  <td class='tabla_fila' id='selector'>
		  <select id='codigo_bandeja_n' name='codigo_bandeja_n'>
      <option value=''>(Selecci&oacute;n de Directorio...)</option>
      $bandejas
      </select>
      </td></tr>
		 ");
     
     }

    
    print("
	
		<tr><td class='tabla_fila2' style='text-align:right;' valign='top'>Comentarios:</td>
		<td class='tabla_fila' id='selector'>
		<textarea id='in_comentarios' name='in_comentarios' style='width:350px;height:60px;'></textarea>
		</td></tr>
			
		
		");
			

?>	
	
	
</table>

<center>
<br /><br />
<input type='button' id='' name='' value='--- Guardar Registros... ---' onClick='guardar_seleccion();' />

</center>

</form>

</body>


</html>




<script>

	selmul=window.opener.selmul;
	selmul_dest=window.opener.selmul_dest;
	
	$('max_cant').innerHTML=selmul.length;
	$('cantidad').value=selmul.length;
	
	var ids='';
	
	for(var i=0;i<selmul.length;i++) {
		ids+=selmul[i]+'|';
	}

	var html='<select id="sel_0" name="sel_0">';

	for(var i=0;i<selmul_dest.length;i++) {
		html+=selmul_dest[i];
	}
	
	html+='</select>';
	
	$('ids').value=ids;
	
	$('selector').innerHTML=html;

</script>
