<?php

  require_once('../../conectar_db.php');

  $servs="'".str_replace(',','\',\'',_cav2(50))."'";

  $servicioshtml = desplegar_opciones_sql( 
  	"SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  	length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica AND centro_ruta IN (".$servs.")
  	ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 
	
	$espechtml=desplegar_opciones_sql("
		SELECT esp_id, esp_desc FROM especialidades
		ORDER BY esp_desc	
	", NULL, '', '');
	
?>

<script>

	 dnomina='';
	 lnomina='';

    listar_nominas = function() {

		$('lista_nominas').value='Actualizar Listado...';

		$('guardar_registros').style.display='none';
		$('copiar_registros').style.display='none';
				
		$('buscar_nominas').style.display='';
		$('datos_nomina').style.display='none';

      $('listado_nominas').style.height='260px';

		$('folio_nomina').disabled=false;

      $('agregar_pacientes').style.display='none';
      
      $('select_nominas').innerHTML='';
      $('select_nominas').style.display='none';
      
      //$('crear_nominas').style.display='';
      
      $('eliminar_nominas').style.display='none';

      var myAjax = new Ajax.Updater(
      'listado_nominas',
      'prestaciones/ingreso_nominas/listar_nominas.php',
      {
        method:'post',
        evalScripts:true,
        parameters:$('info_nominas').serialize()
      });

    }


	abrir_nomina=function(nom_id, tipo) {

		if(tipo==0)
			var params='nom_id='+nom_id;
		else
			var params='nom_folio='+nom_id;
		
		if($('folios_nominas')!=null)	
			params+='&'+$('folios_nominas').serialize();
			
		params+='&'+$('orden').serialize();
		
		var myAjax = new Ajax.Updater(
      'listado_nominas',
      'prestaciones/ingreso_nominas/abrir_nomina.php',
      {
      	
        method:'post',
        parameters:params,
        evalScripts:true,
        onComplete: function(resp) {

				try {

				if(resp.responseText=='') {
					alert('N&oacute;mina no encontrada.'.unescapeHTML());
					return;	
				}

				$('folio_nomina').disabled=true;

				$('buscar_nominas').style.display='none';
				$('datos_nomina').style.display='';
        	
        		$('listado_nominas').style.height='210px';
        		
        		$('agregar_pacientes').style.display='';
        		
        		$('lista_nominas').value='Volver Atr&aacute;s...'.unescapeHTML();
        		
				$('guardar_registros').style.display='';
				//$('copiar_registros').style.display='';
				
				$('listado_nominas').scrollTop=0;

		      //$('crear_nominas').style.display='none';
				
				} catch(err) {
					
					alert(err);
						
				}
					
        }
        
      });
		
	}
	
	calcular_totales=function() {
		
		return;
	
		var num_ausente=0;
		var num_presente=0;
		var num_nuevo=0;
		var num_control=0;	
		var num_extra=0;
		var num_masc=0;
		var num_feme=0;
		var num_altas=0;
		
		var geta=[0,0,0,0,0,0];
		var getn=['< 10','10-14','15-19','20-24','25-64','> 65'];
	
		for(var i=0;i<dnomina.length;i++) {
		
			r=dnomina[i];		
		
			if($('proc')==null) {		
		
				var val=$('nomd_diag_cod_'+r.nomd_id).value;

				if(val=='NSP')
					num_ausente++;
				else
					num_presente++;	
				
			} else {
				
				var val=$('nomd_diag_cod_'+r.nomd_id).checked;
				
				if(!val) {
					num_ausente++;
					val='NSP';
				} else {
					num_presente++;
					val='';
				}	
						
			}	
			
			if(val!='NSP') {
			
				var val=$('nomd_tipo_'+r.nomd_id).value;
				
				if(val=='N')
					num_nuevo++;
				else
					num_control++;
					
				var val=$('nomd_extra_'+r.nomd_id).value;
				
				if(val=='S')
					num_extra++;
					
				if($('proc')==null) {

					var val=$('nomd_destino_'+r.nomd_id).value*1;
					
					if(val==6 || val==9)
						num_altas++;

				}

				var val=$('nomd_edad_'+r.nomd_id).innerHTML*1;

				if(val<10) { geta[0]++; }
				if(val>=10 && val<=14) { geta[1]++; }
				if(val>=15 && val<=19) { geta[2]++; }
				if(val>=20 && val<=24) { geta[3]++; }
				if(val>=25 && val<=64) { geta[4]++; }
				if(val>=65) { geta[5]++; }
				
				var val=$('nomd_sexo_'+r.nomd_id).innerHTML;
				
				if(val=='M')
					num_masc++;
				else
					num_feme++;
				
			}
				
		}
		
		if(dnomina.length>0 && num_presente>0) {
			var factor=100/dnomina.length;
			var factor2=100/num_presente;
		} else {
			var factor=0;
			var factor2=0;
		}
		
		var html='<table style="width:100%;font-size:8px;"><tr><td>';
		
		html+='<table style="width:100%;font-size:8px;" cellpadding=0 cellspacing=0><tr class="tabla_header"><td colspan=3>Indicadores de la N&oacute;mina</td></tr>';
		
		html+='<tr class="tabla_fila"><td style="text-align:right;width:40%;">Asisten:</td><td style="font-weight:bold;text-align:center;width:20%;">'+num_presente+'</td><td style="text-align:center;">'+number_format(num_presente*factor,2,',','.')+'%</td></tr>';	
		html+='<tr class="tabla_fila2"><td style="text-align:right;">Ausentes:</td><td style="font-weight:bold;text-align:center;">'+num_ausente+'</td><td style="text-align:center;">'+number_format(num_ausente*factor,2,',','.')+'%</td></tr>';	
		html+='<tr class="tabla_fila"><td style="text-align:right;">Pac. Nuevos:</td><td style="font-weight:bold;text-align:center;">'+num_nuevo+'</td><td style="text-align:center;">'+number_format(num_nuevo*factor2,2,',','.')+'%</td></tr>';	
		html+='<tr class="tabla_fila2"><td style="text-align:right;">Pac. Control:</td><td style="font-weight:bold;text-align:center;">'+num_control+'</td><td style="text-align:center;">'+number_format(num_control*factor2,2,',','.')+'%</td></tr>';	
		html+='<tr class="tabla_fila"><td style="text-align:right;">Cant. Extras:</td><td style="font-weight:bold;text-align:center;">'+num_extra+'</td><td style="text-align:center;">'+number_format(num_extra*factor,2,',','.')+'%</td></tr>';	
		html+='<tr class="tabla_fila2"><td style="text-align:right;">Masc./Fem.:</td><td style="font-weight:bold;text-align:center;">'+num_masc+'/'+num_feme+'</td><td style="text-align:center;">'+number_format(num_masc*factor2,0,',','.')+'%/'+number_format(num_feme*factor2,0,',','.')+'%</td></tr>';	
		html+='<tr class="tabla_fila"><td style="text-align:right;">Altas:</td><td style="font-weight:bold;text-align:center;">'+num_altas+'</td><td style="text-align:center;">'+number_format(num_altas*factor2,2,',','.')+'%</td></tr>';
		
		html+='</table>';
		
		html+='</td><td>';

		html+='<table style="width:100%;">';
		
		html+='<table style="width:100%;" cellpadding=0 cellspacing=0><tr class="tabla_header"><td colspan=3>Grupos Et&aacute;reos</td></tr>';
		
		for(var j=0;j<getn.length;j++) {
			var clase=(j%2==0)?'tabla_fila':'tabla_fila2';		
			html+='<tr class="'+clase+'"><td style="text-align:right;width:40%;">'+getn[j]+':</td><td style="font-weight:bold;text-align:center;width:20%;">'+geta[j]+'</td><td style="text-align:center;">'+number_format(geta[j]*factor2,2,',','.')+'%</td></tr>';
		}	
				
		html+='</table>'
		
		html+='</td></tr></table>';
		
		$('indicadores').innerHTML=html;		
		
	}
	
	guardar_registros=function() {
	
		var myAjax = new Ajax.Request(
		'prestaciones/ingreso_nominas/sql.php',
		{
			method:'post',
			parameters: $('info_nominas').serialize(),
			onComplete:function() {
				
				alert('Registro guardado exitosamente.');
				listar_nominas();
					
			}	
		}		
		);	
		
	}
	
	cargar_diagnostico=function(nomd_id) {

		var val=trim($('nomd_diag_cod_'+nomd_id).value);

		$('nomd_diag_cod_'+nomd_id).value=val;	
	
		if(val=='NSP') {
				$('nomd_diag_'+nomd_id).value='';
				//calcular_totales();
				return;
		}	
	
		$('nomd_diag_'+nomd_id).value='(Cargando...)';	
	
		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_nominas/diagnosticos.php',
			{
				method:'post',
				parameters:'diag_cod='+encodeURIComponent($('nomd_diag_cod_'+nomd_id).value),
				onComplete:function(resp) {
					
					$('nomd_diag_'+nomd_id).value=resp.responseText;
					//calcular_totales();
					
				}	
			}		
		);	
		
	}
	
	limpiar_paciente=function() {
		
		$('pac_rut').value='';
		$('paciente').value='';
		$('pac_id').value='0';

		
	}
	
	agregar_paciente=function() {
	
					  top=Math.round(screen.height/2)-250;
					  left=Math.round(screen.width/2)-325;

					  new_win = 
					  window.open('prestaciones/ingreso_nominas/form_paciente.php?'+$('nom_id').serialize()+'&'+$('pac_id').serialize()+'&'+$('nomd_hora').serialize()+'&'+$('duracion').serialize(),
					  'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
					  'menubar=no, scrollbars=yes, resizable=no, width=650, height=500, '+
					  'top='+top+', left='+left);

					  new_win.focus();					
					
					return;
					
					
					// Guardar Cupo tomado (ahora es en el formulario de pacientes!!!)...
					var myAjax=new Ajax.Request(
						'prestaciones/ingreso_nominas/sql_tomar_cupo.php',
						{
							method:'post',
							parameters:$('nom_id').serialize()+'&'+$('pac_id').serialize()+'&'+$('nomd_hora').serialize(),
							onComplete:function(resp2) {
								
								imprimir_citacion(resp2.responseText*1);
								//$('paciente').disabled=false;	
								abrir_nomina($("folio_nomina").value, 1);								
								
								$('pac_rut').value='';
								$('paciente').value='';
								$('pac_id').value='0';

							}	
						}					
					);		
					
		
	}
	
	eliminar=function(nomd_id) {
		
		var conf=confirm( "&iquest;Desea eliminar el registro de la n&oacute;mina?".unescapeHTML() );
		
		if(!conf) return;

		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_nominas/sql_eliminar_cupo.php',
			{
				method:'post',
				parameters:'nomd_id='+nomd_id,
				onComplete:function() {
					abrir_nomina($("nom_id").value*1, 0);														
				}	
			}		
		);
		
	}

	buscar_cupos=function() {
	
      top=Math.round(screen.height/2)-275;
      left=Math.round(screen.width/2)-400;

      new_win = 
      window.open('prestaciones/ingreso_nominas/form_buscar.php',
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=800, height=550, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}
	
	solicitar_ficha=function() {
	
      top=Math.round(screen.height/2)-275;
      left=Math.round(screen.width/2)-400;

      new_win = 
      window.open('prestaciones/archivo_fichas/solicitar_ficha.php',
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=800, height=250, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}


	registrar=function(nomd_id) {
	
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('prestaciones/ingreso_nominas/form_proc.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}

	informe=function(nomd_id) {
	
      top=Math.round(screen.height/2)-325;
      left=Math.round(screen.width/2)-375;

      new_win = 
      window.open('prestaciones/ingreso_nominas/form_informe.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=850, height=650, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}

	
	imprimir_listado = function() {
	
	   _general = $('datos_nomina').innerHTML;
  		_detalle = $('listado_nominas').innerHTML;
  
  		_separador2 = '<hr><h3>Detalle de N&oacute;mina</h3></hr>';
  
  		imprimirHTML(_general+_separador2+_detalle);	
		
	}
	
	copiar_nomina=function() {
	
      top=Math.round(screen.height/2)-165;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('prestaciones/ingreso_nominas/form_copiar.php?nom_id='+$('nom_id').value*1,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}

	eliminar_nomina=function() {

		var conf=confirm( "&iquest;Desea eliminar la n&oacute;mina? -- No hay opciones para deshacer.".unescapeHTML() );
		if(!conf) return;
		
		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_nominas/sql_eliminar.php',
			{
				method:'post',
				parameters:$('nom_id').serialize(),
				onComplete:function(r) {
					
					alert('N&oacute;mina eliminada exitosamente.'.unescapeHTML());
					listar_nominas();
					
				}						
			}		
		);

	}

	crear_nomina=function() {
	
      top=Math.round(screen.height/2)-165;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('prestaciones/ingreso_nominas/form_nomina.php',
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
      'top='+top+', left='+left);

      new_win.focus();
			
		
	}

	imprimir_citacion=function(nomd_id) {
	
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('prestaciones/ingreso_nominas/citaciones.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}

	
	imprimir_citacion2=function(nomd_id) {
	
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('prestaciones/ingreso_nominas/citaciones2.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}

	 gestiones_citacion=function(nomd_id) {

      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win =
      window.open('prestaciones/ingreso_nominas/gestionar_citacion.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();

        }

</script>

<center>

<div class='sub-content' style='width:980px;'>

<form id='info_nominas' onSubmit='return false;'>

<div class='sub-content'>
<table style='width:100%;' cellpadding=0 cellspacing=0><tr>
<td style='width:30px;'>
<img src='iconos/table_edit.png'>
</td><td style='font-size:14px;width:200px;'><b>N&oacute;minas de Atenci&oacute;n</b></td>
<td>
<select id='orden' name='orden' style='font-size:11px;'>
<option value='0'>Ordenar por Folio, Nro. Ficha</option>
<option value='1'>Ordenar por Nro. Ficha</option>
<option value='2'>Ordenar por Paterno, Materno, Nombres</option>
</select>
</td>
<td style='width:100px;text-align:right;'>Nro. N&oacute;mina:</td>
<td style='width:100px;'>
<input type='text' 
id='folio_nomina' name='folio_nomina' size=10 style='text-align:center;'
onKeyUp='if(event.which==13) abrir_nomina($("folio_nomina").value, 1);'>
</td>
<td style='text-align:center;' style='width:250px;display:none;font-size:10px;'
id='select_nominas'>

</td>
</tr></table>
</div>

<div class='sub-content' id='buscar_nominas'>

<table style='width:100%;'>

<tr>
<td style='width:100px;text-align:right;'>Fecha:</td>
<td>
<input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'
  onChange='listar_nominas();'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'>
  <input type='button' value='[VER HOY: <?php echo date('d/m/Y'); ?>]' onClick='$("fecha1").value="<?php echo date("d/m/Y"); ?>";$("esp_id").value="-1";listar_nominas();' >
</td>
</tr>
<tr>
<td style='width:100px;text-align:right;'>Especialidad:</td>
<td id='select_especialidades'>
<select id='esp_id' name='esp_id' onChange='listar_nominas();'>
<option value=-1 SELECTED>(Todas las Especialidades...)</option>
<?php echo $espechtml; ?>
</select>
</td>
</tr>

</table>

</div>

<div class='sub-content' id='datos_nomina' style='display:none;'>
<table style='width:100%;'>

<tr>
<td style='width:100px;text-align:right;'>Nro. N&oacute;mina:</td>
<td id='nro_nomina' style='font-size:16px;font-weight:bold;'></td>

<td style='width:100px;text-align:right;'>Profesional:</td>
<td id='medico_nomina' style='font-size:20px;'></td>

</tr>

<tr>
<td style='width:100px;text-align:right;'>Fecha:</td>
<td id='fecha_nomina' style='font-size:24px;font-weight:bold;'></td>
<td style='width:100px;text-align:right;'>Especialidad:</td>
<td id='esp_nomina' style='font-size:18px;'></td>
</tr>


<tr>
<td style='width:100px;text-align:right;'>Estado:</td>
<td>
<select id='estado_nomina' name='estado_nomina'>
<option value=0>Completa</option>
<option value=1>Incompleta</option>
<option value=2>Vac&iacute;a</option>
<option value=3>Ausencia del Profesional</option>
</select>
</td>
</tr>

</table>

<center>
<input type='button' id='lista_nominas' 
onClick='listar_nominas();'
value='Actualizar Listado...'>
</center>

</div>


<div class='sub-content' id='agregar_pacientes' style='display:none;'>
<table style='width:100%;'>
<tr>
<td style='width:20px;'><img src='iconos/add.png' /></td>
<td style='width:100px;text-align:right;'>Agregar Paciente:</td>
<td id='td_horas' style='text-align:center;'>
<select id='nomd_hora' name='nomd_hora'>
<option value='00:00'>EXTRA</option>
</select>
</td>
<td style='width:150px;text-align:center;'>
<input type='hidden' id='pac_id' name='pac_id' value='0' />
<input type='text' size=20 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' />
</td>
<td id='td_duracion' style='display:none;'>
<select id='duracion' name='duracion'>
<option value='1'>15 min</option>
<option value='2'>30 min</option>
<option value='3'>45 min</option>
<option value='4'>1 hr</option>
<option value='6'>1 hr 30 min</option>
<option value='8'>2 hr</option>
<option value='10'>2 hr 30 min</option>
<option value='12'>3 hr</option>


</select>
</td>
<td>
<input type='text' id='paciente' name='paciente' 
style='text-align:left;' DISABLED size=45 />
<input type='button' value='[[ AGREGAR ]]' onClick='agregar_paciente();' />
</td></tr>
</table>
</div>

<div class='sub-content2' style='height:260px;overflow:auto;'
id='listado_nominas'>

</div>


<center>

  <table><tr><td id='guardar_registros' style='display:none;'>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/disk.png'>
		</td><td>
		<a href='#' onClick='guardar_registros();'> Guardar Registros...</a>
		</td></tr></table>
		</div>
	</td><td id='buscar_nominas'>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/date_magnify.png'>
		</td><td>
		<a href='#' onClick='buscar_cupos();'> Buscador de Cupos...</a>
		</td></tr></table>
		</div>
	</td>
	<td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/date_magnify.png'>
		</td><td>
		<a href='#' onClick='solicitar_ficha();'> Solicitar Ficha Espont&aacute;nea...</a>
		</td></tr></table>
		</div>
	</td><!---<td id='crear_nominas' style='display:none;'>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/pencil.png'>
		</td><td>
		<a href='#' onClick='crear_nomina();'> Crear N&oacute;mina Nueva...</a>
		</td></tr></table>
		</div>
	</td>----><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_listado();'> Imprimir Listado...</a>
		</td></tr></table>
		</div>
	</td><td id='copiar_registros' style='display:none;'>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/disk_multiple.png'>
		</td><td>
		<a href='#' onClick='copiar_nomina();'> Copiar N&oacute;mina...</a>
		</td></tr></table>
		</div>
	</td><td id='eliminar_nominas' style='display:none;'>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/cross.png'>
		</td><td>
		<a href='#' onClick='eliminar_nomina();'> Eliminar N&oacute;mina...</a>
		</td></tr></table>
		</div>
	</td></tr></table>
	
</center>

</form>

</div>

</center>

<script>

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'

    });

    listar_nominas();
    
    
    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('paciente').value=d[2];
		$('pac_id').value=d[4];
		//$('pac_ficha').innerHTML=d[3];
		//$('prev_desc').innerHTML=d[6];
		//$('pac_fc_nac').innerHTML=d[7];
		//$('pac_edad').innerHTML='Edad: <b>'+d[11]+'</b>';    

		//$('prev_id').value=d[12];
		//$('ciud_id').value=d[13];

		//$('sincroniza').style.display='';
		
		//cargar_casos();
		
		//listar_recetas(d[4]);
    	
    }

    autocompletar_pacientes = new AutoComplete(
      'pac_rut', 
      'autocompletar_sql.php',
      function() {
        if($('pac_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);

    
</script>

