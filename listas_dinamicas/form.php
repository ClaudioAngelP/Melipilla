<?php 

	require_once('../conectar_db.php');
	
	$listahtml=desplegar_opciones_sql("
		SELECT DISTINCT codigo_bandeja, nombre_bandeja
		FROM lista_dinamica_bandejas 
		WHERE codigo_bandeja IN ('".str_replace(',',"','",_cav(49))."')
		ORDER BY nombre_bandeja;
	");

?>

<script>

	validacion_fecha2=function(obj) {
		
		if(obj.value=='') {
			obj.style.background='';
			return true;
		} else {
			validacion_fecha(obj);
		}
		
	}

	cargar_lista=function() {
		
		$('xls').value=0;
		
		$('boton_act').value='-- Cargando... --';
		$('boton_act').disabled=true;
		
		if($('lista_id').value!='-1' && $('lista_id').value!='-2') {
			$('sel_mul').show();
			var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/generar_tabla.php',{
				method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
			});
		} else if($('lista_id').value=='-1') {
			$('sel_mul').hide();
			var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/resumen_bandejas.php',{
				method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
			});
		} else if($('lista_id').value=='-2') {
			if($('filtro_cond')==undefined || $('filtro_cond').value=='1') {
				$('sel_mul').hide();
				var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/resumen_condiciones.php',{
					method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
				});
			} else {
				$('sel_mul').show();
				var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/generar_tabla.php',{
					method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
				});				
			}
		}
			
	}


	cargar_listado=function() {
		
		$('xls').value=0;

		$('boton_act').value='-- Cargando... --';
		$('boton_act').disabled=true;
		
		if($('lista_id').value!='-1' && $('lista_id').value!='-2') {
			$('sel_mul').show();
			var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/generar_tabla.php',{
				method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
			});
		} else if($('lista_id').value=='-1') {
			$('sel_mul').hide();
			var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/resumen_bandejas.php',{
				method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
			});
		} else if($('lista_id').value=='-2') {
			if($('filtro_cond')==undefined || $('filtro_cond').value=='1') {
				$('sel_mul').hide();
				var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/resumen_condiciones.php',{
					method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
				});
			} else {
				$('sel_mul').show();
				var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/generar_tabla.php',{
					method:'post',parameters:$('datos_listado').serialize(), evalScripts: true, onComplete:function() {
					$('boton_act').value='-- Actualizar --';
					$('boton_act').disabled=false;
				}
				});				
			}
		}
			
	}


	descargar_xls=function() {

		$('xls').value=1;

		$('datos_listado').target='_self';

		$('datos_listado').action='listas_dinamicas/generar_tabla_xls.php';
	
		$('datos_listado').submit();
			
	}
	
	
	function chequear_fechas() {

        var chequear=true;

        $$('input[class="ld_fechas"]').each(function(element) {
							   var tmp=element.name.split('_');
							   var monr_id=tmp[2]*1;
							   if($("sel_"+monr_id).value!='') {
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
                               }
                        });

        return chequear;

	}

	
	guardar_listado=function() {
		
		if(!chequear_fechas()) return;
		
		var myAjax=new Ajax.Request(
			'listas_dinamicas/sql_tabla.php',
			{
				method:'post', parameters: $('datos_listado').serialize(),
				onComplete:function() {
					cargar_lista();
				}
			}
		);
		
	}

	reg_instancia=function(monr_id) {
		
		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/registrar_instancia.php?monr_id='+monr_id,'ver_casos',
							'width=800, height=600, toolbar=false, scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}
	
	ver_caso=function(mon_id) {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/visualizar_caso.php?mon_id='+mon_id,'ver_casos',
							'width=800, height=600, toolbar=false, scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}

	crear_caso=function() {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/crear_caso.php?'+$('lista_id').serialize(),'ver_casos',
							'width=800, height=600, toolbar=false scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}

	seleccion_multiple=function() {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('','sel_multiple',
							'width=800, height=600, toolbar=false scrollbars=yes'+
							', top='+top+', left='+left);

		$('datos_listado').target='sel_multiple';

		$('datos_listado').action='listas_dinamicas/seleccion_multiple.php';

		$('datos_listado').submit();
		
							
		win.focus();
		
	}


	desactivar_filtro = function(refrescar) {
		
		$("lista_tabla").innerHTML="";
		
		if(refrescar==1) cargar_listado();
		
	}

	seltodas=function() {
		
		var val=$('todas').checked;
		
		if(val) {
			$$('input[class="sel_patologia"]').each(function(element) {
				element.checked=true;
			});
			$$('input[class="sel_garantia"]').each(function(element) {
				element.checked=true;
			});
		} else {
			$$('input[class="sel_patologia"]').each(function(element) {
				element.checked=false;
			});
			$$('input[class="sel_garantia"]').each(function(element) {
				element.checked=false;
			});
		}
		
	}


	cual_seltodas=function() {
		
		var val=$('cual_todas').checked;
		
		if(val) {
			$$('input[class="sel_cual"]').each(function(element) {
				element.checked=true;
			});
		} else {
			$$('input[class="sel_cual"]').each(function(element) {
				element.checked=false;
			});
		}
		
	}
	


	examinar_proc=function() {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/examinar_procesos.php?'+$('lista_id').serialize(),'ver_proc',
							'width=950, height=700, toolbar=false, scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}

	editar_acta=function() {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/acta.php','ver_acta',
							'width=950, height=700, toolbar=false, scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}

	
	abrir_monitoreo=function(mon_id) {

		mon_ges = window.open('prestaciones/monitoreo_ges/form_monitoreo.php?mon_id='+mon_id,
		'proceso_ges', 'left='+((screen.width/2)-425)+',top='+((screen.height/2)-300)+',width=950,height=600,status=0,scrollbars=1');
				
		mon_ges.focus();

	}

	redividir_bandeja=function() {
		
			var myAjax=new Ajax.Request('listas_dinamicas/redividir_bandeja.php',{
				method:'post',parameters:$('lista_id').serialize()+'&'+$('redividir').serialize(), 
				evalScripts: true, onComplete:function() {
					cargar_lista();
				}
			});
		
	}
	
	cargar_lista();
	
	validacion_fecha2($('fecha_limite'));


</script>

<center>

<form id='datos_listado' name='datos_listado' method='post'
target='_self' onSubmit='return false;'>

<input type='hidden' id='xls' name='xls' value='0' />

<input type='hidden' id='pat' name='pat' value='' />
<input type='hidden' id='filtrogar' name='filtrogar' value='' />

<div class='sub-content' style='width:950px;'>
<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='width:25px;'>
<img src='iconos/table_lightning.png' style='width:20px;height:20px;' />
</td><td>
<select id='lista_id' name='lista_id' style='font-size:14px;' onChange=''>
<option value='-1' SELECTED><i>(Resumen de Bandejas...)</i></option>
<?php if(_cax(57)) { ?><option value='-2'><i>(Resumen de Condiciones...)</i></option><?php } ?>
<?php echo $listahtml; ?>
</select>
<input type='button' style='font-size:10px;' id='boton_act' name='boton_act' value='-- Actualizar --' onClick='cargar_lista();' />
<input type='button' style='font-size:10px;' id='boton_filtro' name='boton_filtro' value='-- Desactivar Filtros --' onClick='desactivar_filtro(1);' />
</td><td style='text-align:right;'>
<?php if(_cax(57)) { ?> <input type='button' style='font-size:10px;' id='sel_acta' name='sel_acta' value='Acta Directorio GES...' onClick='editar_acta();' /> <?php } ?>
<input type='button' style='font-size:10px;' id='sel_mul' name='sel_mul' value='Selecci&oacute;n M&uacute;ltiple...' onClick='seleccion_multiple();' />
</td></tr>
<tr><td>
<img src='iconos/table.png' style='width:20px;height:20px;' />
</td>
<td colspan=2 style='font-size:12px;'><select id='tipo_gar' name='tipo_gar'>
<option value='0'>(Todas...)</option>
<option value='1'>Vencidas</option>
<option value='2'>Vigentes</option>
</select> <?php if(_cax(57)) { ?>
<input type='checkbox' id='directorio' name='directorio' /> Directorio GES 
<?php } ?> 
<select id='agrupar' name='agrupar'>
<option value='0' SELECTED>(No agrupar...)</option>
<option value='1'>Agrupar Patolog&iacute;as</option>
<option value='2'>Agrupar Cual</option>
</select>
| Fecha L&iacute;mite: 
<input type='text' size=10 id='fecha_limite' name='fecha_limite' style='text-align:center;' onKeyUp='validacion_fecha2(this);' value='' />
| Dif. Fecha: 
<input type='text' size=5 id='fecha_dif' name='fecha_dif' style='text-align:center;' value='' />

Total de Registros: <span id='cant_total' style='font-weight:bold;font-size:16px;'>0</span>
</td>
</tr>
</table>
</div>

<div id='lista_tabla' class='sub-content2' style='height:350px;overflow:auto;'>

</div>

<center>
<table style='width:100%;'>
<tr>
<td style='text-align:left;font-size:14px;'> 



</td>
<td style='text-align:right;'>

<input type='button' value='-- Guardar Registros... --' onClick='guardar_listado();' />
<input type='button' value='-- Imprimir Listado... --' onClick='imprimir_listado();' />
<input type='button' value='-- Descargar XLS... --' onClick='descargar_xls();' />
<input type='button' value='-- Examinar Proceso... --' onClick='examinar_proc();' />

</td>
</tr>
</table>
</center>

</div>

</form>

</center>

