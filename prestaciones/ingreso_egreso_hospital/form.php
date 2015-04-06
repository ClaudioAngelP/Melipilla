<?php 

	require_once('../../conectar_db.php');
	require_once('../../ficha_clinica/ficha_basica.php');

	$ingreso=isset($_GET['ingreso']);

	$r=cargar_registro("SELECT COALESCE(MAX(hosp_folio),0) AS n FROM hospitalizacion;");
	
	$maxfolio=$r['n']+1;

?>

<script>

	cfecha=function(obj) {
		var val=obj.value;
		val=trim(val);
		if(val.length==6) {
			obj.value=''+val[0]+val[1]+'/'+val[2]+val[3]+'/20'+val[4]+val[5];
		} else if(val.length==8) {
			obj.value=''+val[0]+val[1]+'/'+val[2]+val[3]+'/'+val[4]+val[5]+val[6]+val[7];
		} else {
			obj.value=''+val;
		}

		calc_dias();
	}
	
	calc_dias=function() {
	
		try {
		
			if($('fecha0').value=='' ||
				$('fecha2').value=='') {
			
				$('dias_estadia').innerHTML='?';			
				return;			
				
			}		
		
			var f1=$('fecha0').value.split('/');
			var f2=$('fecha2').value.split('/');

			var d1=new Date( Date.parse(f1[1]+'/'+f1[0]+'/'+f1[2]) );
			var d2=new Date( Date.parse(f2[1]+'/'+f2[0]+'/'+f2[2]) );
			var one_day=1000*60*60*24;
	
			$('dias_estadia').innerHTML=Math.ceil((d2.getTime()-d1.getTime())/(one_day))+' d&iacute;as.';

		} catch(err) {
			
			$('dias_estadia').innerHTML='?';

		}
		
	}

	limpiar_formulario=function() {

		$('hosp_id').value=0;

		limpiar_ficha_basica();
		$('paciente_tipo_id').value=0;
		
		
		$('paciente_id').value='';
		$('paciente_rut').value='';
		$('doc_id').value='';
		
		$('hora1').value='12:00';								
		
		$('rut_medico').value='';
		$('nombre_medico').value='';							
		
		//$('nro_cama').value=reg.hosp_numero_cama;
		//$('disponible').value='1';

		$('prevision').value=0;
		$('prevision_clase').value=0;
		$('prevision_clase').style.display='';

		$('modalidad').value=0;
		$('ges').value=0;
                $("prog_social").style.display="none";
                $('motivo').value=0;
		$('procedencia').value=0;

		$('inst_sel').style.display='none';
		$('inst_id').value='';
		$('institucion').value='';

		$('centro_ruta0').value='';
		$('servicios0').value='';
		$('diagnosticos_egreso').value='';
		$('prestaciones').value='';

		
		<?php if($ingreso) { ?>
		$('egreso').checked=false;
		$('panel_egreso').style.display='none';
		<?php } ?>
		
		$('fecha0').value='';
		$('fecha1').value='';
		$('fecha2').value='';
		$('fecha3').value='';
		
		$('hora2').value='12:00';
		$('condicion').value=1;
		
		$('parto').value=-1;								

		$('nacimiento').value=-1;								

		diagnosticos=[];
		diagnosticos_egreso=[];
		traslados_int=[];
		prestaciones=[];

		redibujar('diag');
		redibujar('diagf');
		redibujar('pres');
		redibujar('tras');

	}

	cargar_hosp=function() {
	
		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_egreso_hospital/info_hospitalizacion.php',
			{
				method:'post',
				parameters:$('nro_folio').serialize(),
				onComplete:function(r) {
					try {

						reg=r.responseText.evalJSON(true);
						
						if(reg) {
							$('hosp_id').value=reg.hosp_id;
							$('paciente_id').value=reg.hosp_pac_id;
							$('paciente_rut').value=reg.pac_rut;
							$('doc_id').value=reg.hosp_doc_id;
							//$('paciente_tipo_id').value=2;
							if(reg.pac_rut=='')
							{
								$('paciente_tipo_id').value=2;
								$('paciente_rut').value=reg.hosp_pac_id;
							}
							else
							{
								
								$('paciente_tipo_id').value=0;
							}
							
							var fec=reg.hosp_fecha_ing.split(' ');				
							$('fecha0').value=fec[0];								
							$('hora1').value=fec[1];								
							
							if(reg.doc_rut!=null) {
								$('rut_medico').value=reg.doc_rut;
								$('nombre_medico').value=(reg.doc_paterno+' '+reg.doc_materno+' '+reg.doc_nombres).unescapeHTML();
							} else {
								$('rut_medico').value='';
								$('nombre_medico').value='';							
							}
							
							//$('nro_cama').value=reg.hosp_numero_cama;
							//$('disponible').value='1';

							$('prevision').value=reg.hosp_prevision;
							if(reg.hosp_prevision=='0')
                                                        {
								$('prevision_clase').value=reg.hosp_prevision_clase;
								$('prevision_clase').style.display='';
                                                                $('prog_social').style.display='none';
                                                                $("motivo").value=0;
							}
                                                        else
                                                        {
                                                                $("prevision_clase").value=0;
                                                                $('prevision_clase').style.display='none';
                                                                if(reg.hosp_prevision=='3')
                                                                {

                                                                    $('prog_social').style.display='';
                                                                }
                                                                else
                                                                {

                                                                    $('prog_social').style.display='none';
                                                                    $("motivo").value=0;
                                                                }
							}

							$('modalidad').value=reg.hosp_modalidad;
							$('ges').value=reg.hosp_ges;
							$('motivo').value=reg.hosp_motivo;
							$('procedencia').value=reg.hosp_procedencia;

							$('centro_ruta0').value=reg.hosp_centro_ruta;
							$('servicios0').value=reg.centro_nombre.unescapeHTML();

							if(reg.hosp_procedencia=='3') {
								$('inst_sel').style.display='';
								$('inst_id').value=reg.hosp_inst_id;
								$('institucion').value=reg.inst_nombre.unescapeHTML();
							} else {
								$('inst_sel').style.display='none';
								$('inst_id').value='';
								$('institucion').value='';
							}
							
							if(reg.hosp_fecha_egr!=null) {

								<?php if($ingreso) { ?>
								$('egreso').checked=true;
								$('panel_egreso').style.display='';
								<?php } ?>
								
								var fec=reg.hosp_fecha_egr.split(' ');
								
								$('fecha2').value=fec[0];
								$('hora2').value=fec[1];								
								$('condicion').value=reg.hosp_condicion_egr;
								
								if(reg.hosp_parto=='t')
									$('parto').value=0; 
								else if(reg.hosp_parto=='f')
									$('parto').value=1;
								else
									$('parto').value=-1;
																

								if(reg.hosp_nacimiento=='t')
									$('nacimiento').value=0; 
								else if(reg.hosp_nacimiento=='f') 
									$('nacimiento').value=1;		
								else
									$('nacimiento').value=-1;		
														
								
							} else {
							
								<?php if($ingreso) { ?>
								$('egreso').checked=false;
								$('panel_egreso').style.display='none';
								<?php } ?>
								
								$('fecha2').value='';
								$('hora2').value='12:00';
								$('condicion').value=1;
								
								$('parto').value=-1;								

								$('nacimiento').value=-1;								

							}

							diagnosticos=reg.diag1;
							diagnosticos_egreso=reg.diag2;
							traslados_int=reg.traslados;
							prestaciones=reg.prestaciones;
							
							calc_dias();

							buscar_paciente();

							redibujar('diag');
							redibujar('diagf');
							redibujar('pres');
							redibujar('tras');
							
							<?php if($ingreso) { ?>
								cargar_cama();
							<?php } ?>
							
						} else {
						
							limpiar_formulario();

						} 		
					
					} catch(err) {
					
						alert(err);
					
					}							
				}
			}		
		);	
	
	}

    ver_camas = function() {

      top=Math.round(screen.height/2)-115;
      left=Math.round(screen.width/2)-375;
        
      new_win = 
      window.open('prestaciones/ingreso_egreso_hospital/ver_camas.php',
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=no, resizable=no, width=750, height=300, '+
      'top='+top+', left='+left);
        
      new_win.focus();
    
    }

	 cargar_cama = function() {
	 
		$('desc_cama').innerHTML='<img src="imagenes/ajax-loader3.gif"> Espere un momento...';	 
	 
		var myAjax = new Ajax.Request(
		'prestaciones/ingreso_egreso_hospital/info_cama.php',
		{
			method:'post',
			parameters:'num_cama='+($('nro_cama').value*1),
			onComplete: function(r) {

				reg=r.responseText.evalJSON(true);
				
				if(reg) {

					if(reg.hosp_id=='' || reg.hosp_id==($('hosp_id').value*1) ) {
						$('disponible').value='1';
						var icono='accept.png';
						var msg=' <span style="color:#00FF00;">DISPONIBLE</span> ';
					} else {
						$('disponible').value='0';
						var icono='error.png';
						var msg=' <span style="color:#FF0000;">OCUPADO</span> ';
					}
					
					$('desc_cama').innerHTML=('<table><tr><td><img src="iconos/'+icono+'"></td><td><b>'+(reg.tcama_tipo) + '</b> / ' + (reg.cama_tipo)+' <b>['+msg+']</b></td></tr></table>');
					
				} else {
					$('desc_cama').innerHTML='<table><tr><td><img src="iconos/error.png"></td><td> <i>N&uacute;mero de cama no es v&aacute;lido.</i></td></tr></table>';
					$('disponible').value='0';
				}

			}		
		});
	 
	 }

	 serializar_arr = function(array) {

		var l=array.split(',');
		var tmp='';
				
		for(var i=0;i<l.length;i++) {	 	
	 		var arr=eval(l[i]);
	 		tmp+='arr_'+l[i]+'='+encodeURIComponent(arr.toJSON());
	 		if(i<l.length-1) tmp+='&';
	 	}

		return tmp;
		
	 }

	 bloquear_ingreso=false;
	 
	 guardar_hosp = function() {
	 
		if(bloquear_ingreso) return;	 
	 
	 	
		/*if($('procedencia').value=='-1') {
			alert( 'Debe seleccionar una procedencia.'.unescapeHTML() );
			return;
		}	 

		if($('doc_id').value=='0') {
			alert( 'Debe seleccionar un m&eacute;dico tratante.'.unescapeHTML() );
			return;
		}	
		*/ 

		var fec=$('fecha2').value.split('/');

                if($('prevision').value==3)
                {
                    if($('motivo').value==0)
                    {
                        alert('Debe seleccionar ley o Programa Social para guardar los datos');
                        return;
                    }
                }

		if(fec[2]*1!=$('anio').value*1) {
			alert( 'Fecha de Egreso no corresponde a a&ntilde;o de ingreso estipulado.'.unescapeHTML() );
			return;
		}


		try {
		
			if($('fecha0').value=='' ||
				$('fecha2').value=='') {
			
				alert('Fecha de Ingreso/Egreso incorrecta.');
				return;			
				
			}		
		
			var f1=$('fecha0').value.split('/');
			var f2=$('fecha2').value.split('/');

			var d1=new Date( Date.parse(f1[1]+'/'+f1[0]+'/'+f1[2]) );
			var d2=new Date( Date.parse(f2[1]+'/'+f2[0]+'/'+f2[2]) );

		} catch(err) {

			alert('Fecha de Ingreso/Egreso incorrecta.');
			return;			

		}
		
		if(d1>d2) {
		
			alert( 'Fecha de Ingreso es mayor que Fecha de Egreso.'.unescapeHTML() );		
			return;
			
		}

	 
		bloquear_ingreso=true;	 
	 
		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_egreso_hospital/sql_hosp.php',
			{
				method:'post',
				parameters: $('nro_folio').serialize()+'&'+$('paciente').serialize()+
								'&'+$('hosp').serialize()+'&'+serializar_arr('diagnosticos,diagnosticos_egreso,prestaciones,traslados_int'),
				onComplete: function(r) {
				
					if(r.responseText=='N' || r.responseText=='E') {
										
						alert('Egreso hospitalario ingresado exitosamente.');

						limpiar_formulario();
						
						if(r.responseText=='N') max_folio++;
						
						$('nro_folio').value=max_folio;
						$('nro_folio').select();
						$('nro_folio').focus();
						
						$('contenido').scrollTop=0;
						
					} else {
					
						alert(r.responseText);
						
					}
						
					bloquear_ingreso=false;						
											
					// cargar_cama();					
										
				}
			}		
		);	 
	 
	 }

</script>

<center>
<div class='sub-content' style='width:750px;'>
<div class='sub-content' 
style='background-color:#cccccc;font-weight:bold;'>
<table cellpadding=0 cellspacing=0 style='width:100%;'><tr><td>
<img src='iconos/building.png'></td><td style='width:50%;font-size:14px;'>
<b>Ingreso/Egreso Hospitalario</b>
</td><td style='text-align:right;'>A&ntilde;o:</td><td>
<input type='text' id='anio' name='anio' value='' 
style='text-align:right;' size=10>
</td><td style='text-align:right;'>Nro. de Folio:</td><td>
<input type='text' id='nro_folio' name='nro_folio'
onKeyUp='if(event.which==13) cargar_hosp();' value='' 
style='text-align:right;' size=10>
</td></tr></table>
</div>

<?php desplegar_ficha_basica(); ?>

<form id='hosp' name='hosp' onSubmit='return false;'>
<input type='hidden' id='hosp_id' name='hosp_id' value='0'>

<div class='sub-content'>
<img src='iconos/building.png'> <b>Datos de Ingreso</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<!---
<tr><td style='text-align:right;'>
Previsi&oacute;n de Salud:
</td><td colspan=3>
<select id='prevision' name='prevision' onChange='
	if(this.value=="0") 
	 {
		$("prevision_clase").style.display="";
                $("prog_social").style.display="none";
                $("motivo").value="0";

        }
        else
        {
		$("prevision_clase").style.display="none";
                $("prevision_clase").value="0";
                if(this.value==3)
                {
                    $("prog_social").style.display="";
                }
                else
                {
                    $("prog_social").style.display="none";
                    $("motivo").value="0";
                }
        }

	
'>
<option value='0'>Ley Salud (18.469)</option>
<option value='1'>ISAPRE</option>
<option value='2'>Particular</option>
<option value='3'>Otra</option>
</select>
<select id='prevision_clase' name='prevision_clase'>
<option value='0'>A</option>
<option value='1'>B</option>
<option value='2'>C</option>
<option value='3'>D</option>
</select>
</td></tr>
--->

<tr><td style='text-align:right;'>
Modalidad de Atenci&oacute;n:
</td><td colspan=3>
<select id='modalidad' name='modalidad'>
<option value='0'>MAI</option>
<option value='1'>MLE</option>
</select>
</td></tr>

<tr><td style='text-align:right;'>
GES:
</td><td colspan=3>
<select id='ges' name='ges'>
<option value='0'>No</option>
<option value='1'>S&iacute;</option>
</select>
</td></tr>

<tr id='prog_social' name='prog_social' style='display:none;'><td style='text-align:right;'>
Ley/Programa Social:
</td><td colspan=3>
<select id='motivo' name='motivo'>
<option value='0'></option>
<option value='1'>Ley 18.490 Accidentes del Transporte</option>
<option value='2'>Ley 16.744 Accidentes del Trabajo y Enfermedades Profesionales</option>
<option value='3'>Ley 16.744 Accidente Escolar</option>
<option value='4'>PRAIS</option>
<option value='5'>Chile Solidario</option>
<option value='6'>Chile Crece Contigo</option>
<option value='7'>Otro Programa Social</option>
</select>
</td></tr>

<tr><td style='text-align:right;'>
Procedencia del Paciente:
</td><td colspan=3>
<select id='procedencia' name='procedencia'
onChange='
	if(this.value==1 || this.value==3) 
		$("inst_sel").style.display=""; 
	else
		$("inst_sel").style.display="none";	
'>
<option value='0'>Unidad de Emergencia</option>
<option value='1'>APS</option>
<option value='2'>Atenci&oacute;n de Especialidades</option>
<option value='3'>Otro Hospital</option>
<option value='4'>Otra Procedencia</option>
</select>
</td></tr>

    <tr id='inst_sel' style='display:none;'>
		<td style='text-align: right;'>Instituci&oacute;n de Procedencia:</td>
		<td style='text-align: left;' colspan=3>
		<input type='hidden' id='inst_id' name='inst_id' value=''>
		<input type='text' id='institucion' name='institucion' size=40>
	  </td>
    </tr>


<!---

<tr><td style='text-align:right;'>
N&uacute;mero Cama:
</td><td colspan=3>
<input type='hidden' id='disponible' name='disponible' value='0'>
<input type='text' style='text-align:center;font-weight:bold;' 
id='nro_cama' name='nro_cama' size=10 onKeyUp='cargar_cama();'>
<input type='button' onClick='ver_camas();' 
value='- Disponibilidad Camas -'>
</td></tr>

<tr><td style='text-align:right;'>
Tipo Cama:
</td><td id='desc_cama' style='height:30px;' colspan=3>
<i>(Debe seleccionar cama...)</i>
</td></tr>

--->

<tr><td style='text-align:right;width:30%;'>
Hora Ingreso:</td><td>
<input type='text' id='hora1' name='hora1' size=10 value='12:00'>
</td></tr>

<tr><td style='text-align:right;width:30%;'>
Fecha Ingreso:</td><td>

	<input type='text' name='fecha0' id='fecha0' size=10
  style='text-align: center;'  onBlur="cfecha(this);" onFocus='this.select();'
  value=''>
  <img src='iconos/date_magnify.png' id='fecha0_boton'>
</td></tr>

<tr>
<td style='text-align:right;width:30%;'>
Servicio Ingreso:
</td>
<td>
<input type="hidden" id='centro_ruta0' name='centro_ruta0' value=''>
<input type="text" id='servicios0' name='servicios0'>
</td>
</tr>

</table>
</div>

<div class='sub-content' <?php if($ingreso) echo 'style="display:none;"'; ?> >
<table style='width:100%;'>
<tr><td style='width:20px;'>
<img src='iconos/arrow_refresh.png'></td>
<td style='width:35%;'><b>Traslados</b>
</td><td style='text-align:right;'>Agregar Traslados:</td>
<td>
	<input type='text' name='fecha3' id='fecha3' size=10
  style='text-align: center;'  onBlur="cfecha(this);" onFocus='this.select();'
  value=''>
  <img src='iconos/date_magnify.png' id='fecha3_boton'>
  
</td>
<td>
<input type="text" id='servicios' name='servicios'>
</td></tr>
</table>
<div class='sub-content2' id='traslas'>
(No hay registro de traslados.)
</div>
</div>


<div class='sub-content' <?php if($ingreso) echo 'style="display:none;"'; ?> >
<table style='width:100%;'>
<tr><td style='width:20px;'>
<img src='iconos/layout.png'></td>
<td style='width:55%;'><b>Diagn&oacute;sticos de Ingreso</b>
</td><td style='text-align:right;'>Agregar Diagn&oacute;stico:</td><td>
<input type="text" id='diagnosticos' name='diagnosticos'>
</td></tr>
</table>
<div class='sub-content2' id='diags'>
(No hay registro de diagn&oacute;sticos de ingreso.)
</div>
</div>

<div class='sub-content' <?php if($ingreso) echo 'style="display:none;"'; ?> >
<img src='iconos/building_go.png'> <b>Datos de Egreso</b>

<?php if($ingreso) { ?>

(<input type="checkbox" id='egreso' name='egreso'
onClick="

	if(this.checked) 
		$('panel_egreso').style.display=''; 
	else 
		$('panel_egreso').style.display='none'; 

"> Generar Egreso Hospitario)

<?php } ?>

</div>

<div id='panel_egreso' style='<?php if($ingreso) echo 'display:none;'; ?>'>

<div class='sub-content'>

<table style='width:100%;'>
<tr><td style='text-align:right;'>
Fecha Egreso:
</td><td>
	<input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' onBlur="cfecha(this);" onFocus='this.select();' 
  value=''>
  <img src='iconos/date_magnify.png' id='fecha2_boton'>
</td><td style='text-align:right;'>Hora:</td><td>
<input type='text' id='hora2' name='hora2' size=10 value='12:00'>
</td></tr>

<tr><td style='text-align:right;'>Dias de Hospitalizaci&oacute;n:</td>
<td id='dias_estadia' style='font-weight:bold;'>?</td>
</tr>

<tr><td style='text-align:right;'>
Condici&oacute;n al Egreso:
</td><td colspan=3>
<select id='condicion' name='condicion'>
<option value='1' SELECTED>Vivo</option>
<option value='2'>Fallecido</option>
</select>
</td></tr>

<tr>
<td style='text-align:right;'>Parto:</td>
<td><select id='parto' name='parto'>
<option value=-1>(No aplica...)</option>
<option value=0>S&iacute;</option>
<option value=1>No</option>
</select></td>
<td colspan=2 rowspan=2>(Solo para Egresos Obstetricos)</td>
</tr>

<tr>
<td style='text-align:right;'>Nacimiento:</td>
<td><select id='nacimiento' name='nacimiento'> 

<option value=-1>(No aplica...)</option>
<option value=0>Vivo</option>
<option value=1>Fallecido</option>
    </select>
</td>
</tr>

</table>

</div>

<div class='sub-content'>
	<table style='width:100%;'>
		<tr>
			<td style='width:20px;'>
				<img src='iconos/layout.png'>
			</td>
			<td style='width:55%;'>
				<b>Diagn&oacute;sticos de Egreso</b>
			</td>
			<td style='text-align:right;'>
				Agregar Diagn&oacute;stico:
			</td>
			<td>
				<input type="text" id='diagnosticos_egreso' name='diagnosticos_egreso'>
			</td>
		</tr>
	</table>
	<div class='sub-content2' id='diagsf'>
		(No hay registros de diagn&oacute;sticos de egreso.)
	</div>
</div>

<div class='sub-content'>
	<table style='width:100%;'>
		<tr>
			<td style='width:20px;'>
				<img src='iconos/layout.png'>
			</td>
			<td style='width:35%;'>
				<b>Intervenciones Quir&uacute;rgicas</b>
			</td>
			<td style='text-align:right;'>
				Agregar Intervenci&oacute;n:
			</td>
			<td>
				<input type='text' name='fecha1' id='fecha1' size=10
  				style='text-align: center;' onBlur="cfecha(this);" onFocus='this.select();' 
  				value=''>
  				<img src='iconos/date_magnify.png' id='fecha1_boton'>
			</td>
			<td>
				<input type="text" id='prestaciones' name='prestaciones'>
			</td>
		</tr>
	</table>
	<div class='sub-content2' id='prestas'>
		(No hay registro de intervenciones quir&uacute;rgicas.)
	</div>
</div>

<div class='sub-content'>
<table style='width:100%;'>

<tr>
<td style='text-align: right;'>Profesional Tratante:</td>
<td colspan=3> 
<input type='hidden' id='doc_id' name='doc_id' value='0'>
<input type='text' id='rut_medico' name='rut_medico' size=10
style='text-align: center;' disabled>
<input type='text' id='nombre_medico' name='nombre_medico' size=35>
</td>
</tr>    

</table>
</div>


</div>

</form>

<center>
<br /><br />
<input type='button' id='ingresar' 
value='- Guardar datos de Hospitalizaci&oacute;n -' onClick='guardar_hosp();'>
<br /><br />
</center>

</div>

</center>

<script>

      ingreso_rut=function(datos_medico) {
      	$('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);


	traslados_int=[];
	diagnosticos=[];
	diagnosticos_egreso=[];
	prestaciones=[];
	
	diag=[]; diagf=[]; pres=[]; tras=[];	
	
	diag[0]='diags';
	diagf[0]='diagsf';
	pres[0]='prestas';	
	tras[0]='traslas';	
	
	diag[1]='diagnosticos';
	diagf[1]='diagnosticos_egreso';
	pres[1]='prestaciones';	
	tras[1]='traslados_int';	
	
	diag[2]=['#','Cod. CIE10','Descripci&oacute;n','Bajar','Subir','Eliminar'];
	diagf[2]=['#','Cod. CIE10','Descripci&oacute;n','Bajar','Subir','Eliminar'];
	pres[2]=['#','Cod. Prestaci&oacute;n','Descripci&oacute;n','Fecha','Bajar','Subir','Eliminar'];
	tras[2]=['#','Servicio','Fecha','Eliminar'];

	diag[3]=[-5,0,1,-1,-2,-3];
	diagf[3]=[-5,0,1,-1,-2,-3];
	pres[3]=[-5,0,1,2,-1,-2,-3];	
	tras[3]=[-5,1,2,-3];	

	diag[4]=['text-align:right;width:20px;','text-align:center;font-weight:bold;','width:60%;text-align:justify;padding:2px;','','',''];
	diagf[4]=['text-align:right;width:20px;','text-align:center;font-weight:bold;','width:60%;text-align:justify;padding:2px;','','',''];
	pres[4]=['text-align:right;width:20px;','text-align:center;font-weight:bold;','width:40%;text-align:justify;padding:2px;','text-align:center;','','',''];
	tras[4]=['text-align:right;width:20px;','width:60%;text-align:center;font-weight:bold;','text-align:center;padding:2px;','text-align:center;',''];

	insertar=function(arr, val) {
	
		if(!arr) arr=[];
		
		var n=arr.length;
		arr[n]=val;

	}

    seleccionar_diagnostico = function(d) {

		insertar(diagnosticos, [d[0], d[2]] ); 
		redibujar('diag');
		$('diagnosticos').select();
		$('diagnosticos').focus();

    }

    seleccionar_diagnostico2 = function(d) {

		insertar(diagnosticos_egreso, [d[0], d[2]] ); 
		redibujar('diagf');
		$('diagnosticos_egreso').select();
		$('diagnosticos_egreso').focus();

    }

    seleccionar_prestacion = function(d) {

		insertar(prestaciones, [ d[0], d[2], $('fecha1').value ] ); 
		redibujar('pres');

		$('fecha1').select();
		$('fecha1').focus();

    }

	 orden_fecha=function(a,b) {
	 	var f1=a[2].split('/');
	 	var f2=b[2].split('/');
	 	fa=(f1[2]+''+f1[1]+''+f1[0])*1;
	 	fb=(f2[2]+''+f2[1]+''+f2[0])*1;
	 	return (fa-fb);
	 }

    seleccionar_serv = function(d) {

		insertar(traslados_int, [ d[0], d[2], $('fecha3').value ] );

		traslados_int.sort(orden_fecha);
		
		$('servicios').value=d[2].unescapeHTML(); 

		redibujar('tras');

		$('fecha3').select();
		$('fecha3').focus();

    }

    seleccionar_serv2 = function(d) {

		$('centro_ruta0').value=d[0].unescapeHTML();
		$('servicios0').value=d[2].unescapeHTML(); 

    }


    
    subir=function(array, n) {
    
		var dat=eval(array);
    	var arr=eval(dat[1]);
		
		var tmp = arr[n-1];
		arr[n-1]=arr[n];
		arr[n]=tmp;

		redibujar(array);
		
    }
    
    bajar=function(array, n) {
    
		var dat=eval(array);
    	var arr=eval(dat[1]);

		var tmp = arr[n+1];
		arr[n+1]=arr[n];
		arr[n]=tmp;
		
		redibujar(array);
    
    }
    
    eliminar=function(array, n) {
    
		var dat=eval(array);
    	var arr=eval(dat[1]);

		var tmp=[];
		
		for(var i=0;i<arr.length;i++)
			if(i!=n) 
				insertar(tmp, arr[i]);
			
		eval(dat[1]+'=tmp');

		redibujar(array);
    
    }
    
    redibujar=function(datos) {    	
		
		dat=eval(datos);		
		
		var obj=dat[0];
		var arr=eval(dat[1]);		
		
		var hdr=dat[2]; var row=dat[3]; var stl=dat[4];		
		
		var html='<table style="width:100%;font-size:12px;"><tr class="tabla_header">';
		
		for(var i=0;i<hdr.length;i++) 
			html+='<td>'+hdr[i]+'</td>';	    	
    	
		html+='</tr>';    	
    	
		for(var i=0;i<arr.length;i++) {
	
			var clase=(i%2==0)?'tabla_fila':'tabla_fila2';	
		
			html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
			
			for(var j=0;j<row.length;j++) {
			
				html+='<td style="'+stl[j]+'">';			
			
				if(row[j]==-5) 
					html+=(i+1)+'</td>';
				else if(row[j]==-3) 
					html+='<center><img src="iconos/delete.png" style="cursor:pointer;" onClick="eliminar(\''+datos+'\', '+i+');"/></center></td>';		
				else if(row[j]==-2) { 
					if(i>0)					
						html+='<center><img src="iconos/arrow_up.png" style="cursor:pointer;" onClick="subir(\''+datos+'\', '+i+');"/></center></td>';
					else
						html+='&nbsp;</td>';		
				} else if(row[j]==-1) { 
					if(i<arr.length-1)					
						html+='<center><img src="iconos/arrow_down.png" style="cursor:pointer;" onClick="bajar(\''+datos+'\', '+i+');"/></center></td>';		
					else
						html+='&nbsp;</td>';		
				} else 
					html+=arr[i][row[j]]+'</td>';		

				
			}

			html+='</tr>';
		
		}    	

    	
		html+='</tr></table>';
		
		$(obj).innerHTML=html;    	
    	
    }

    autocompletar_diagnostico = new AutoComplete(
      'diagnosticos', 
      'autocompletar_sql.php',
      function() {
        if($('diagnosticos').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=diagnostico&cadena='+encodeURIComponent($('diagnosticos').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico);

    autocompletar_diagnostico2 = new AutoComplete(
      'diagnosticos_egreso', 
      'autocompletar_sql.php',
      function() {
        if($('diagnosticos_egreso').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=diagnostico&cadena='+encodeURIComponent($('diagnosticos_egreso').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico2);

    autocompletar_prestaciones = new AutoComplete(
      'prestaciones', 
      'autocompletar_sql.php',
      function() {
        if($('prestaciones').value.length<2) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=intervenciones_quirurgicas&cod_presta='+encodeURIComponent($('prestaciones').value)
        }
      }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_prestacion);

    seleccionar_inst = function(d) {
    
      $('inst_id').value=d[0];
      $('institucion').value=d[2].unescapeHTML();
    
    }

    autocompletar_institucion = new AutoComplete(
      'institucion', 
      'autocompletar_sql.php',
      function() {
        if($('institucion').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=hospitales&cadena='+encodeURIComponent($('institucion').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst);

    autocompletar_servicios = new AutoComplete(
      'servicios', 
      'autocompletar_sql.php',
      function() {
        if($('servicios').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hosp&cadena='+encodeURIComponent($('servicios').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv);


    autocompletar_servicios2 = new AutoComplete(
      'servicios0', 
      'autocompletar_sql.php',
      function() {
        if($('servicios0').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hosp&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);



    Calendar.setup({
        inputField     :    'fecha0',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha0_boton'
    });

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });


    Calendar.setup({
        inputField     :    'fecha2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha2_boton'
    });

    Calendar.setup({
        inputField     :    'fecha3',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha3_boton'
    });

	

	max_folio=<?php echo $maxfolio; ?>;
	
	$('nro_folio').value=max_folio;

	//$('nro_folio').select();	
	//$('nro_folio').focus();
	$('anio').value='<?php echo date('Y'); ?>';
	$('anio').select();
	$('anio').focus();

	var inputs=$('contenido').getElementsByTagName('input');

	for(var i=0;i<inputs.length;i++) {

		Event.observe(inputs[i], 'focus', function() {
			this.setStyle({border: '2px solid red'});
		});

		Event.observe(inputs[i], 'blur', function() {
			this.setStyle({border: ''});
		});

	}

	var inputs=$('contenido').getElementsByTagName('select');

	for(var i=0;i<inputs.length;i++) {

		Event.observe(inputs[i], 'focus', function() {
			this.setStyle({border: '3px solid red'});
		});

		Event.observe(inputs[i], 'blur', function() {
			this.setStyle({border: ''});
		});

	}


</script>
