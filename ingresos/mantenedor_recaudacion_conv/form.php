<?php

  require_once("../../conectar_db.php");

	

	$itemshtml = desplegar_opciones("codigos_prestacion", 
	"DISTINCT anio,anio as p",'','true','ORDER BY anio'); 
	

	
	?>
	
	<script>
	
	bloquear_boton=true;
	bloquear_ingreso=false;
	
	
 
  
	
		
		verifica_tabla = function() {
			
		
			prod_id_text = document.getElementById('presta_codigo');
			prod_glosa_text = document.getElementById('prod_glosa');
			prod_modalidad_text = document.getElementById('modalidad');
			prod_anio_text = document.getElementById('anio');
			var str=prod_id_text.value;
			
			if(bloquear_boton) {
				alert('El c&oacute;digo ingresado no es v&aacute;lido.'.unescapeHTML());
				presta_codigo.select();
				return;
			}
			
			if(trim(prod_glosa_text.value)=='') {
				alert('El campo glosa est&aacute; vac&iacute;o.'.unescapeHTML());
				prod_glosa_text.select();
				return;
			}	
			
			if(trim(prod_anio_text.value)=='') {
				alert('El campo año est&aacute; vac&iacute;o.'.unescapeHTML());
				prod_anio_text.select();
				return;
			}		
			
			if((prod_modalidad_text.selectedIndex)==0) {
				alert('Debe seleccionar la modalidad.'.unescapeHTML());
				
				return;
			}	
			if(trim(prod_id_text.value)=='') {
				alert('El campo c&oacute;digo est&aacute; vac&iacute;o.'.unescapeHTML());
				prod_id_text.select();
				return;
			}		
			
			if((prod_modalidad_text.selectedIndex)==3) {
				if(str.length<11)
				alert('El c&oacute;digo ingresado no es v&aacute;lido para Farmacos.'.unescapeHTML());
				prod_id_text.select();
				return;
			
			}
			
			pasarcampos=$('mantenedor').serialize();
			if(bloquear_ingreso) return;
			
			bloquear_ingreso=true;
			
				var myAjax = new Ajax.Request(
				'ingresos/mantenedor_recaudacion_conv/sql.php', 
				{
					method: 'post', 
					parameters: pasarcampos,
					onComplete: function(pedido_datos) {
					
						if(pedido_datos.responseText=="3") {
							alert('Edici&oacute;n de C&oacute;digo de Recaudacion realizado exitosamente.'.unescapeHTML());
							limpiar();
						} else {
							if(pedido_datos.responseText=="2") {
							alert('ingreso de C&oacute;digo de Recaudacion realizado exitosamente.'.unescapeHTML());
							limpiar();
							} else {
							alert('Error: \r\n'+pedido_datos.responseText.unescapeHTML());
							}
						}
						
						
						bloquear_ingreso=false;
						bloquear_boton=false;
						
					}
				}		
				);
				
					//alert('Ingreso de C&oacute;digo de Recaudacion fallido, ya existe un codigo con esta modalidad.'.unescapeHTML());
			
			
			
		
		}
	
	
	limpiar=function() {
		
		$('anio').value='';
		$('doc_id').value='';
		$('rut_medico').value='';
		$('doc_rut').value='';
		
		$('nombre_medico').value='';
		$('val_tot_mai').value=0;
		$('val_tot_mai_2').value=0;
		$('cop_a').value=0;
		$('cop_b').value=0;
		$('cop_c').value=0;
		$('cop_d').value=0;
		$('pab').value=0;
		$('canasta').value='';
		
		
		$('mod').value='';
		$('presta_codigo').value='';
		$('prod_glosa').value='';
		$('modalidad').selectedIndex =0;
		$('porc_crs').value='';
		$('porc_med').value='';
	}
	
	cambiarCategoria = function ()
{
var e = document.getElementById("modalidad");
var dato = e.selectedIndex;

			if(dato==1 || dato==3)
			{
				$('VTM').innerHTML ='Valot Total MAI';
			$('VTM2').innerHTML ='Valot Total MAI (2)';
			$('CA').innerHTML ='Copago A';
			$('CB').innerHTML ='Copago B';
			$('CC').innerHTML ='Copago C';
			$('CD').innerHTML ='Copago D';
			$('PA').innerHTML ='Pabellon';

					return;
			}
		if(dato ==2){
			
				$('VTM').innerHTML ='Valot Total MAI';
			$('VTM2').innerHTML ='Valot Total MAI (2)';
			$('CA').innerHTML ='Nivel 1';
			$('CB').innerHTML ='Nivel 2';
			$('CC').innerHTML ='Copago N2';
			$('CD').innerHTML ='Nivel 3';
			$('PA').innerHTML ='Pabellon';
					return;
					
				}
		

}
	seleccionar_item=function(r) {
		
		
		$('mod').value=r[6];
		$('presta_codigo').value=r[4];
		$('prod_glosa').value=r[5].unescapeHTML();
		if(r[6]=='mai'){$('modalidad').selectedIndex =1;}else{
		if(r[6]=='mle'){$('modalidad').selectedIndex =2;}else{$('modalidad').selectedIndex =3;}
		}
		
		$('porc_med').value=r[18];
		$('porc_crs').value=r[17];
		
		
		if(r[6]=='mai' || r[6]=='Farmacia'){
			
			$('VTM').innerHTML ='Nivel 3';
			$('VTM2').innerHTML ='Nivel 3';
			$('CA').innerHTML ='Nivel 3';
			$('CB').innerHTML ='Particular';
			$('CC').innerHTML ='Particular';
			$('CD').innerHTML ='Particular';
			$('PA').innerHTML ='Pabellón';
	
			}else{
				$('VTM').innerHTML ='Nivel 3';
			$('VTM2').innerHTML ='Nivel 3';
			$('CA').innerHTML ='Nivel 3';
			$('CB').innerHTML ='Particular';
			$('CC').innerHTML ='Particular';
			$('CD').innerHTML ='Particular';
			$('PA').innerHTML ='Pabellón';
			}
		
		$('anio').value=r[7];
		$('val_tot_mai').value=r[8];
		$('val_tot_mai_2').value=r[9];
		$('cop_a').value=r[10];
		$('cop_b').value=r[11];
		$('cop_c').value=r[12];
		$('cop_d').value=r[13];
		$('pab').value=r[14];
		$('canasta').value=r[15];
		
		bloquear_boton=false;
	bloquear_ingreso=false;
	
	}

	autocompletar_items = new AutoComplete(
      'presta_codigo', 
      'ingresos/mantenedor_recaudacion_conv/autocompletar_prestacion_conv.php',
      function() {
        if($('presta_codigo').value.length<3) {
        	bloquear_boton=true;
        	return false;
        }else{
     		bloquear_boton=false;
        return {
          method: 'get',
          parameters: 'cadena='+encodeURIComponent($('presta_codigo').value)+'&'+'rut='+$('rut_medico').value
        }
        }
      }, 'autocomplete', 400, 300, 150, 0, 3, seleccionar_item);
      
      ingreso_rut=function(datos_medico) {
      
      $('rut_medico').value=datos_medico[1];
       $('doc_rut').value=datos_medico[1];
       $('doc_id').value=datos_medico[0];
      }
      
	 autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<2) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_rut);
	
	</script>
	<center>
	<table ><tr><td>
	
	<div class='sub-content' >
                <div class='sub-content'>
                    <img src='iconos/page_gear.png'>
                    <b>Mantenedor de Recaudaci&oacute;n</b>
                </div>
	<center>
		
	
  </div>
  
  <form id='mantenedor' name='mantenedor' 
    onSubmit='return false;'>
  
	
	<div class='sub-content' style='width: 720px;'>
	
	<div class='sub-content'>
	<img src='iconos/script.png' id='imagen_titulo'> 
	<b><span id='titulo_formulario'>Datos del C&oacute;digo</span></b></div>
	
	<div class='sub-content2' id='articulo'>
	<center>
	<table>
	<input type='hidden' name='mod' id='mod'>
	<input type='hidden' name='doc_rut' id='doc_rut'>
	<input type='hidden' name='doc_id' id='doc_id'>
	<tr>
		<td style='text-align: right;' class='form_titulo'>RUT/Nombre:</td><td colspan=3 class='form_campo'>
		<input type='text' id='rut_medico' name='rut_medico' size=10
    style='text-align: center;' disabled>
  		<input type='text' id='nombre_medico' name='nombre_medico' onKeyUp=''>

  </td></tr>
	
	<tr><td style='text-align: right;' class='form_titulo'>C&oacute;digo:</td><td colspan=3 class='form_campo'>
  <input type='text' name='presta_codigo' id='presta_codigo' >

  </td></tr>
	<tr><td style='text-align: right;' class='form_titulo'>Glosa:</td><td colspan=3 class='form_campo'><input type='text' name='prod_glosa' id='prod_glosa' size=35></td></tr>
	
	<tr>
		<td  style='text-align: right;' class='form_titulo'>Modalidad:</td><td colspan=3 class='form_campo'>

	
	<select name="modalidad" id="modalidad" onChange="cambiarCategoria();"><!--//onChange="cambiarCategoria"()-->
	<option value=0 selected>(No Asignado...)</option>
	<option value="mai">mai</option>
	<option value="mle">mle</option>
	<option value="Farmacia">Farmacia</option>
	</select>
	
	</td></tr>
	<tr>
	<tr><td style='text-align: right;' class='form_titulo'>Año:</td><td colspan=3 class='form_campo'><input type='text' name='anio' id='anio' size=35></td></tr>
	
	
	
	<tr>
	
	<td style='width: 10px; ' id='VTM'></td>
<td style='width: 10px;' id='VTM2'></td>
<td style='width: 10px;'id='CA'></td>
<td style='width: 10px;'id='CB'></td>
<td style='width: 10px;'id='CC'></td>
<td style='width: 10px;'id='CD'></td>
<td style='width: 10px;'id='PA'></td>
<tr>

<td style='width: 10px;'><input type='text' id='val_tot_mai' name='val_tot_mai' style='width: 70px;'text-align:right;' value='0' /></td>
<td style='width: 10px;'><input type='text' id='val_tot_mai_2' name='val_tot_mai_2' style='width: 70px;'text-align:right;' value='0';' /></td>
<td style='width: 10px;'><input type='text' id='cop_a' name='cop_a' style='width: 70px;'text-align:right;' value='0'  /></td>
<td style='width: 10px;'><input type='text' id='cop_b' name='cop_b' style='width: 70px;'text-align:right;' value='0' /></td>
<td style='width: 10px;'><input type='text' id='cop_c' name='cop_c' style='width: 70px;'text-align:right;' value='0'/></td>
<td style='width: 10px;'><input type='text' id='cop_d' name='cop_d' style='width: 70px;'text-align:right;' value='0' /></td>

<td style='width: 10px;'><input type='text' id='pab' name='pab' style='width: 70px;'text-align:right;' value='0' /></td>

</tr>

</tr>

	<td style='text-align: right;' class='form_titulo'>Canasta (separar por comas).:</td>
	<td colspan=3 class='form_campo'>
 	<input type='text' name='canasta' id='canasta' ></td>
 	
 	<tr>
 		
 		<td style='text-align: right;' class='form_titulo'>% Prof. (solo n&uacute;meros).:</td>
	<td colspan=3 class='form_campo'>
 	<input type='text' name='porc_med' id='porc_med' ></td>
 		<td style='text-align: right;' class='form_titulo'>% CRS (solo n&uacute;meros).:</td>
	<td colspan=3 class='form_campo'>
 	<input type='text' name='porc_crs' id='porc_crs' ></td>
 		
 	</tr>
 	
 	
 	
  
	</form>
	<br>
	<center>
	
	
	
  <div class='boton' id='guardar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/accept.png'>
	</td><td>
	<a href='#' onClick='verifica_tabla();'><span id='texto_boton'>Guardar Convenio...</span></a>
	</td></tr></table>
	</div>
	
	
	</center>
		
	</div>
	
	</div>
	
  </td></tr></table>
  
  <br>
	
	
	</center>
	</div>
</td></tr></table>
</center>	
  <script> $('prof_rut').focus(); </script>
