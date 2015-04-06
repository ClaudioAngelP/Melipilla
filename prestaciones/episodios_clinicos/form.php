<?php
  require_once('../../conectar_db.php');  require_once('../../ficha_clinica/minibuscador_pacientes.php');
?>
<script>

	cargar_caso=function() {
	
		var myAjax=new Ajax.Updater(
			'tab_eventos_content',
			'prestaciones/episodios_clinicos/listar_eventos.php',
			{
				method:'post',
				parameters:$('casos_auge').serialize()+'&'+$('pac_id').serialize()	
			}		
		);	

		var myAjax=new Ajax.Updater(
			'tab_prestaciones_content',
			'prestaciones/episodios_clinicos/listar_prestaciones.php',
			{
				method:'post',
				parameters:$('casos_auge').serialize()+'&'+$('pac_id').serialize()
			}		
		);	
		
	}

  ver_prestaciones = function() {
    tab_up('tab_prestaciones');    tab_down('tab_canasta');    tab_down('tab_eventos');    tab_down('tab_resumen');
  }

  ver_canasta = function() {
    tab_down('tab_prestaciones');    tab_up('tab_canasta');    tab_down('tab_eventos');    tab_down('tab_resumen');
  }

  ver_eventos = function() {
    tab_down('tab_prestaciones');    tab_down('tab_canasta');    tab_up('tab_eventos');    tab_down('tab_resumen');
  }

  ver_resumen = function() {
    tab_down('tab_prestaciones');    tab_down('tab_canasta');    tab_down('tab_eventos');    tab_up('tab_resumen');
  }
  // listar_episodios();</script>

<center>
<div class='sub-content' style='width:950px;'>
<div class='sub-content'>
<img src='iconos/magnifier.png' />
<b>Ficha Cl&iacute;nica Integral de Pacientes</b>
</div>

<form id='nuevo_episodio' name='nuevo_episodio' onSubmit='return false;'>
<input type='hidden' id='ca_id' name='ca_id' value=0><input type='hidden' id='pat_id' name='pat_id' value=0><input type='hidden' id='detpat_id' name='detpat_id' value=0><input type='hidden' id='inter_id' name='inter_id' value=0>

<div class='sub-content'>
<table style='width:100%;'>
<tr><td class='tabla_fila2' style='text-align:right;width:100px;'>R.U.T.:</td>
<td class='tabla_fila'>
<input type='hidden' id='pac_id' name='pac_id' value='0' />
<input type='text' size=35 id='pac_rut' name='pac_rut' value='' />
</td>
<td class='tabla_fila2'  style='text-align:right;'>Nro. Ficha Cl&iacute;nica:</td>
<td class='tabla_fila' style='text-align:center;font-weight:bold;width:25%;font-size:16px;' id='pac_ficha'>

</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Nombre:</td>
<td class='tabla_fila' style='font-size:14px;font-weight:bold;' id='pac_nombre'>
</td>
<td class='tabla_fila2'  style='text-align:right;'>Previsi&oacute;n:</td>
<td class='tabla_fila' id='prev_desc'>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha de Nac.:</td>
<td class='tabla_fila' id='pac_fc_nac' style='text-align:center;'></td>
<td class='tabla_fila2' colspan=2 style='text-align:center;' id='pac_edad'>
Edad:<b>(n/a)</b>
</td>
</tr>

<tr>
<td style='text-align:right;' class='tabla_fila2'>Casos AUGE:</td>
	<td id='casos' colspan=3 class='tabla_fila'>
	</td>
</tr>
<tr>
<td colspan=4>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_prestaciones' style='cursor: default;' 
      onClick='ver_prestaciones();'>
      <img src='iconos/chart_line.png'>
      Prestaciones Trazadoras</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_eventos' style='cursor: pointer;'
      onClick='ver_eventos();'>
      <img src='iconos/table_refresh.png'>
      Documentos GES</div>
		</td><td>
		<div class='tabs_fade' id='tab_canasta' style='cursor: pointer;'
      onClick='ver_canasta();'>
      <img src='iconos/script_edit.png'>
      Regs. Monitoreo</div>
		</td><td>
		<div class='tabs_fade' id='tab_resumen' style='cursor: pointer;'
      onClick='ver_resumen();'>
      <img src='iconos/table.png'>
      Listas de Espera</div>
		</td><td>
		<div class='tabs_fade' id='tab_resumen' style='cursor: pointer;'
      onClick='ver_flujo();'>
      <img src='iconos/chart_organisation.png'>
      Flujo Patolog&iacute;a</div>
		</td>
      </tr>
      </table>


<div class='tabbed_content' style='height:250px;overflow:auto;'
id='tab_prestaciones_content'>

</div>

<div class='tabbed_content' style='height:250px;overflow:auto;display:none;'
id='tab_eventos_content'>

</div>
<div class='tabbed_content' style='height:250px;overflow:auto;display:none;'
id='tab_canasta_content'>

</div>

<div class='tabbed_content' style='height:250px;overflow:auto;display:none;'
id='tab_resumen_content'>

</div>


</td>
</tr>
</table>

<center>
<input onClick='sincronizar_pac();' type='button' id='sincroniza' 
value='Sincronizar Paciente con SIGGES...' style='display:none;' />
</center>

</div>

</form>

</div>

</div>


</center>

<script>

	abrir_ic = function(id) {
		inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
		inter_ficha.focus();
	}

	abrir_ipd = function(id) {
		inter_ficha = window.open('interconsultas/visualizar_ipd.php?ipd_id='+id,		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
		inter_ficha.focus();
	}

	abrir_oa = function(id) {
		inter_ficha = window.open('interconsultas/visualizar_oa.php?oa_id='+id,		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
		inter_ficha.focus();
	}

	abrir_presta = function(id) {
		inter_ficha = window.open('prestaciones/visualizar_prestacion.php?presta_id='+id,		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
		inter_ficha.focus();
	}


	 cargar_casos=function() {
	 
		$('casos').innerHTML='<img src="imagenes/ajax-loader1.gif" /> Cargando...';	 
	 
		var myAjax=new Ajax.Request(
			'prestaciones/casos_vigentes.php',
			{
				method:'post',
				parameters:$('pac_id').serialize(),
				onComplete: function(r) {
					var d=r.responseText.evalJSON(true);
					
					var html='<select id="casos_auge" name="casos_auge" onChange="cargar_caso();">';

					html+="<option value='0' SELECTED>(Eventos sin Caso...)</option>";
					
					for(var i=0;i<d.length;i++) {
						html+='<option value="'+d[i].ca_id+'">'+d[i].ca_patologia+'</option>';
					}

					html+="<option value='-1'>(Mostrar Todo...)</option>";
					
					html+="</select>";
					
					$('casos').innerHTML=html;
					
					cargar_caso();
					
				}						
			}		
		);	 
	 	
	 }	 	

    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('pac_nombre').innerHTML=d[2];
		$('pac_id').value=d[4];
		$('pac_ficha').innerHTML=d[3];
		$('prev_desc').innerHTML=d[6];
		$('pac_fc_nac').innerHTML=d[7];
		$('pac_edad').innerHTML='Edad: <b>'+d[11]+'</b>';    

		//$('prev_id').value=d[12];
		//$('ciud_id').value=d[13];

		$('sincroniza').style.display='';
		
		cargar_casos();
    	
    }

    autocompletar_pacientes = new AutoComplete(      'pac_rut',       'autocompletar_sql.php',      function() {        if($('pac_rut').value.length<2) return false;
        return {          method: 'get',          parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)        }      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);


		sincronizar_pac=function() {
		
			$('casos').innerHTML='<img src="imagenes/ajax-loader1.gif" /> Sincronizando a SIGGES ...';		
		
			$('sincroniza').disabled=true;		
		
			var myAjax=new Ajax.Request(
				'conectores/sigges/descargar_datos.php',
				{
					method:'post',
					parameters:'auto=1&confirma=1&pac='+$('pac_rut').value,
					onComplete:function(resp) {

					$('casos').innerHTML='<img src="imagenes/ajax-loader1.gif" /> Sincronizando a Sistemas Locales...';		
						
					$('sincroniza').disabled=false;		
					cargar_casos();							
					
					}	
				}			
			);		
				
		}

</script>
