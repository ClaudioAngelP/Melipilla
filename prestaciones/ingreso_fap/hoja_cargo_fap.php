
<?php 

	require_once('../../conectar_db.php');
	
	$fap_id=$_GET['fap_id']*1;
	
	$ctacte=cargar_registro("SELECT * FROM hospitalizacion WHERE hosp_pac_id=(SELECT pac_id FROM fap_pabellon WHERE fap_id=$fap_id) AND hosp_fecha_egr IS NULL ORDER BY hosp_fecha_ing LIMIT 1;");
	
	if($ctacte)
		$hosp_id=$ctacte['hosp_id']*1;
	else
		$hosp_id=0;
	
	$r=cargar_registros_obj("SELECT *, upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat
											FROM fap_pabellon
											JOIN pacientes USING (pac_id)
											LEFT JOIN comunas ON comunas.ciud_id=pacientes.ciud_id
											WHERE fap_id=".$fap_id);
											
	
?>

<html>
<title>Hoja de Cargo IQ</title>

<?php cabecera_popup('../..'); ?>

<script>
	
	cambiar_pantalla=function() {
		
		var val=$('opciones').value*1;
		
		switch(val) {
			
			case 0:
				//$('datos_generales').show();
				$('registro_prestaciones').hide();
				$('registro_hoja_cargo').hide();
				$('registro_recien_nacidos').hide();
				//$('registro_antibioticos').hide();
				//$('solicitud_traslado').hide();
				//$('registro_epicrisis').hide();
				break;

			case 1:
				//$('datos_generales').hide();
				$('registro_prestaciones').show();
				$('registro_hoja_cargo').hide();
				$('registro_recien_nacidos').hide();
				//$('registro_antibioticos').hide();
				//$('solicitud_traslado').hide();
				//$('registro_epicrisis').hide();
				break;

			case 2:
				//$('datos_generales').hide();
				$('registro_prestaciones').hide();
				$('registro_hoja_cargo').show();
				$('registro_recien_nacidos').hide();
				//$('registro_antibioticos').hide();
				//$('solicitud_traslado').hide();
				//$('registro_epicrisis').hide();
				break;

			case 3:
				//$('datos_generales').hide();
				$('registro_prestaciones').hide();
				$('registro_hoja_cargo').hide();
				$('registro_recien_nacidos').show();
				//$('registro_antibioticos').hide();
				//$('solicitud_traslado').hide();
				//$('registro_epicrisis').hide();
				break;

			case 4:
				//$('datos_generales').hide();
				$('registro_prestaciones').hide();
				$('registro_hoja_cargo').hide();
				$('registro_recien_nacidos').hide();
				//$('registro_antibioticos').show();
				//$('solicitud_traslado').hide();
				//$('registro_epicrisis').hide();
				break;

			case 5:
				//$('datos_generales').hide();
				$('registro_prestaciones').hide();
				$('registro_hoja_cargo').hide();
				$('registro_recien_nacidos').hide();
				//$('registro_antibioticos').hide();
				//$('solicitud_traslado').show();
				//$('registro_epicrisis').hide();
				break;

			/*case 6:
				$('datos_generales').hide();
				$('registro_prestaciones').hide();
				$('registro_hoja_cargo').hide();
				$('registro_recien_nacidos').hide();
				$('registro_antibioticos').hide();
				$('solicitud_traslado').hide();
				$('registro_epicrisis').show();
				break;*/
				
		}
		
	}
	
	listado_hoja_cargo=function() {
		
		var myAjax=new Ajax.Updater(
			'listado_hoja_cargo',
			'listado_hoja_cargo.php',
			{
				method:'post',
				parameters:'hosp_id=<?php echo $hosp_id; ?>&fap_id=<?php echo $fap_id; ?>'
			}
		);
		
	}

	agregar_hoja_cargo=function() {
		
		if($('art_id').value=='') {
			alert('Debe seleccionar el art&iacute;culo a cargar al paciente.'.unescapeHTML());
			return;
		}
		
		var myAjax=new Ajax.Request(
			'sql_agregar_hoja_cargo.php',
			{
				method:'post',
				parameters:'hosp_id=<?php echo $hosp_id; ?>&fap_id=<?php echo $fap_id; ?>&'+
					$('art_id').serialize()+'&'+
					$('art_cantidad').serialize(),
				onComplete:function() {
					listado_hoja_cargo();
					$('art_id').value='';
					$('art_codigo').value='';
					$('art_glosa').value='';
					$('art_cantidad').value='1';
					$('art_forma').innerHTML='';
					$('art_codigo').focus();
				}
			}
		);
		
	}

	eliminar_hc=function(hosphc_id) {

		var myAjax=new Ajax.Request(
			'sql_agregar_hoja_cargo.php',
			{
				method:'post',
				parameters:'hosphc_id='+hosphc_id,
				onComplete:function() {
					listado_hoja_cargo();
				}
			}
		);
		
	}


	listado_prestaciones=function() {
		
		var myAjax=new Ajax.Updater(
			'listado_prestaciones',
			'listado_prestaciones.php',
			{
				method:'post',
				parameters:'hosp_id=<?php echo $hosp_id; ?>&fap_id=<?php echo $fap_id; ?>'
			}
		);
		
	}
	
	agregar_prestacion=function() {

		if($('nombre_presta').value=='') {
			alert('Debe seleccionar la prestaci&oacute;n a cargar al paciente.'.unescapeHTML());
			return;
		}
		
		var myAjax=new Ajax.Request(
			'sql_agregar_prestacion.php',
			{
				method:'post',
				parameters:'hosp_id=<?php echo $hosp_id; ?>&fap_id=<?php echo $fap_id; ?>&'+$('codigo_presta').serialize()+'&'+
					$('nombre_presta').serialize()+'&'+
					$('cant_presta').serialize(),
				onComplete:function() {
					//alert('Su solicitud se ha enviado a Infectolog&iacite;a. \n Consulte el estado de su solicitud en esta misma pantalla.'.unescapeHTML());
					listado_prestaciones();
					$('codigo_presta').value='';
					$('nombre_presta').value='';
					$('cant_presta').value='1';
					$('codigo_presta').focus();
					
				}
			}
		);
		
	}


	realizar_hospp=function(hospp_id) {

		var myAjax=new Ajax.Request(
			'sql_agregar_prestacion.php',
			{
				method:'post',
				parameters:'hospp_id='+hospp_id,
				onComplete:function() {
					listado_prestaciones();
				}
			}
		);
		
	}

	eliminar_hospp=function(hospp_id) {

		var myAjax=new Ajax.Request(
			'sql_agregar_prestacion.php',
			{
				method:'post',
				parameters:'hospp_id='+hospp_id+'&remover=1',
				onComplete:function() {
					listado_prestaciones();
				}
			}
		);
		
	}
	
	
	listado_recien_nacidos=function() {
		
		var myAjax=new Ajax.Updater(
			'listado_recien_nacidos',
			'../asignar_camas/listado_recien_nacidos.php',
			{
				method:'post',
				parameters:'hosp_id=<?php echo $hosp_id; ?>'
			}
		);
		
	}


	agregar_recien_nacido=function() {

		if($('rn_peso').value*1==0) {
			alert('Debe ingresar el peso en gramos del reci&eacute;n nacido.'.unescapeHTML());
			return;
		}

		if($('rn_apgar').value*1==0) {
			alert('Debe ingresar el APGAR del reci&eacute;n nacido.'.unescapeHTML());
			return;
		}
		
		var myAjax=new Ajax.Request(
			'../asignar_camas/sql_agregar_recien_nacido.php',
			{
				method:'post',
				parameters:'hosp_id=<?php echo $hosp_id; ?>&fap_id=<?php echo $fap_id; ?>&'+
					$('rn_condicion').serialize()+'&'+
					$('rn_sexo').serialize()+'&'+
					$('rn_peso').serialize()+'&'+
					$('rn_apgar').serialize(),
				onComplete:function() {
					listado_recien_nacidos();
					$('rn_condicion').value='0';
					$('rn_sexo').value='0';
					$('rn_peso').value='';
					$('rn_apgar').value='';
				}
			}
		);
		
	}

	eliminar_parto=function(hospp_id) {

		var myAjax=new Ajax.Request(
			'sql_agregar_recien_nacido.php',
			{
				method:'post',
				parameters:'hospp_id='+hospp_id,
				onComplete:function() {
					listado_recien_nacidos();
				}
			}
		);
		
	}


</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/script_edit.png' />
<b>Hoja de Cargo IQ</b>
</div>

<form id='info' name='info' onSubmit='return false;'>
<input type='hidden' id='fap_id' name='fap_id' value='<?php echo $fap_id; ?>' />
<input type='hidden' id='hosp_id' name='hosp_id' value='<?php echo $hosp_id; ?>' />
</form>

<div class='sub-content'>


<table style='width:100%;'>

<tr>
<td style='text-align:right;width:100px;'>Cta.Cte.:</td>
<td style='font-weight:bold;font-size:18px;color:blue;'><?php echo $hosp_id; ?></td>
</tr>


<tr>
<td style='text-align:right;width:100px;'>R.U.T.:</td>
<td style='font-weight:bold;font-size:16px;color:green;'><?php echo $r[0]['pac_rut']; ?></td>
</tr>

<tr>
<td style='text-align:right;width:100px;'>Ficha:</td>
<td style='font-weight:bold;font-size:16px;color:green;'><?php echo $r[0]['pac_ficha']; ?></td>
</tr>

<tr>
<td style='text-align:right;'>Nombre:</td>
<td style='font-weight:bold;font-size:16px;color:yellowgreen;'><?php echo $r[0]['pac_nombres'].' '.$r[0]['pac_appat'].' '.$r[0]['pac_apmat']; ?></td>
</tr>

</table>

</div>

<center>
<table style='width:95%;font-size:20px;'>
	<tr class='tabla_header'>
		<td style='text-align:right;font-weight:bold;'>Secci&oacute;n:</td>
		<td>

			<select id='opciones' name='opciones' style='font-size:20px;width:100%;' onChange='cambiar_pantalla();'>
			<!--- <option value='0' SELECTED>Datos Generales Hospitalizaci&oacute;n</option> -->
			<!--- <option value='6'>Registro de Epicrisis</option> -->
			<option value='2'>Registro de Hoja de Cargo</option>
			<option value='1'>Registro de Prestaciones</option>
			<option value='3'>Registro de Reci&eacute;n Nacidos</option>
			<!-- <option value='4'>Solicitud de Antibi&oacute;ticos Restringidos</option> -->
			<!--- <option value='5'>Solicitud de Traslado</option> -->
			</select>
		
		
		</td>
	</tr>
</table>
</center>

<div class='sub-content' id='registro_prestaciones' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/building.png' /> <b>Registro de Prestaciones</b> 
</div>

<table style='width:100%;'>
	<tr>
		<td> 
		<center><img src='../../iconos/add.png'></center>
		</td>
		<td style='text-align:right;'>Prestaci&oacute;n:</td>
		<td>
		<input type='text' id='codigo_presta' name='codigo_presta' value='' />
		</td>
		<td>
		<input type='text' size=40 id='nombre_presta' name='nombre_presta' READONLY />
		</td>
		<td style='text-align:right;'>Cant:</td>
		<td>
		<input type='text' size=5 id='cant_presta' name='cant_presta' value="1" />
		</td>
		<td>
		<input type='button' value='-- Agregar --' onClick='agregar_prestacion();' />
		</td>
	</tr>
</table>


<div class='sub-content2' id='listado_prestaciones' style='height:300px;overflow:auto;'>

</div>

</div>

<div class='sub-content' id='registro_hoja_cargo' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/pill.png' /> <b>Hoja de Cargo Paciente</b> 
</div>

<table style='width:100%;'>
	<tr>
		<td> 
		<center><img src='../../iconos/add.png'></center>
		</td>
		<td style='text-align:right;'>Art&iacute;culo:</td>
		<td>
		<input type='hidden' id='art_id' name='art_id' value='' />
		<input type='text' id='art_codigo' name='art_codigo' value='' />
		</td>
		<td>
		<input type='text' size=40 id='art_glosa' name='art_glosa' READONLY />
		</td>
		<td style='text-align:right;'>Cant:</td>
		<td>
		<input type='text' size=5 id='art_cantidad' name='art_cantidad' style='text-align:right;' value='1' />
		<span id='art_forma' style='font-weight:bold;'></span>
		</td>
		<td>
		<input type='button' value='-- Agregar --' onClick='agregar_hoja_cargo();' />
		</td>
	</tr>
</table>


<div class='sub-content2' id='listado_hoja_cargo' style='height:300px;overflow:auto;'>

</div>

</div>


<div class='sub-content' id='registro_recien_nacidos' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/user_add.png' /> <b>Registro de Reci&eacute;n Nacidos</b> 
</div>


<table style='width:100%;'>
	<tr>
		<td> 
		<center><img src='../../iconos/add.png'></center>
		</td>
		<td style='text-align:right;'>Datos R.N.:</td>
		<td>
		<select id='rn_condicion' name='rn_condicion'>
		<option value='0'>VIVO</option>
		<option value='1'>FALLECIDO</option>
		</select>
		</td>
		<td>
		<select id='rn_sexo' name='rn_sexo'>
		<option value='0'>MASCULINO</option>
		<option value='1'>FEMENINO</option>
		<option value='2'>INDEFINIDO</option>
		</select>
		</td>
		<td style='text-align:right;'>Peso:</td><td>
		<input type='text' size=10 id='rn_peso' name='rn_peso' value=''>
		</td>
		<td style='text-align:right;'>APGAR:</td><td>
		<input type='text' size=10 id='rn_apgar' name='rn_apgar' value=''>
		</td>
		<td>
		<input type='button' value='-- Agregar --' onClick='agregar_recien_nacido();' />
		</td>
	</tr>
</table>


<div class='sub-content2' id='listado_recien_nacidos' style='height:300px;overflow:auto;'>

</div>


</div>


</body>
</html>

<script>
	
      ingreso_presta=function(datos_presta) {
      	      	
      	$('codigo_presta').value=datos_presta[0];
      	$('nombre_presta').value=datos_presta[2].unescapeHTML();
      	
      }

      autocompletar_prestaciones = new AutoComplete(
      	'codigo_presta', 
      	'autocompletar_sql.php',
      function() {
		  
        if($('codigo_presta').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=prestacion&cod_presta='+encodeURIComponent($('codigo_presta').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_presta);


      ingreso_hc=function(datos_art) {
      	      	
      	$('art_id').value=datos_art[0];
      	$('art_codigo').value=datos_art[1];
      	$('art_glosa').value=datos_art[2].unescapeHTML();
      	$('art_forma').innerHTML=datos_art[3];
      	$('art_cantidad').focus();
      	
      }

      autocompletar_hoja_cargo = new AutoComplete(
      	'art_codigo', 
      	'autocompletar_sql.php',
      function() {
        if($('art_codigo').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=articulo&art_codigo='+encodeURIComponent($('art_codigo').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_hc);

		
	listado_hoja_cargo();
	listado_prestaciones();
	listado_recien_nacidos();
	
	cambiar_pantalla();

</script>
