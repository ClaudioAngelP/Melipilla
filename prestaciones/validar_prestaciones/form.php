<?php 



?>

<script>

	listar_pacientes=function() {
	
		$('lista_pacientes').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando...';	
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/validar_prestaciones/listar_pacientes.php',
			{
				method:'post',
				parameters:$('fecha1').serialize()	
			}		
		);	
		
	}

	abrir_pac=function(pac_id) {
	
		$('lista_pacientes').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando...';	
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/validar_prestaciones/detalle_paciente.php',
			{
				method:'post',
				evalScripts:true,
				parameters:$('fecha1').serialize()+'&pac_id='+pac_id	
			}		
		);	
		
	}
	
	cargar_casos=function(pac_id) {
	 
		var myAjax=new Ajax.Request(
			'prestaciones/casos_vigentes.php',
			{
				method:'post',
				parameters:'pac_id='+pac_id,
				onComplete: function(r) {
					var d=r.responseText.evalJSON(true);
					
					var html='<select id="ca_id" name="ca_id" onChange="">';
					
					for(var i=0;i<d.length;i++) {
						html+='<option value="'+d[i].ca_id+'">'+d[i].ca_patologia+'</option>';
					}
					
					html+="<option value='0'>Eventos sin Caso...</option>";
					html+="</select>";
					
					$('casos').innerHTML=html;
					
					//cargar_caso();
					
				}						
			}		
		);	 
	 	
	 }	 	

	
	lista_prestaciones=function(pac_id, prev_id) {
		
		$('lista_prestaciones').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando...';	
	
		var myAjax=new Ajax.Updater(
			'lista_prestaciones',
			'prestaciones/validar_prestaciones/lista_prestaciones.php',
			{
				method:'post',
				parameters:$('fecha1').serialize()+'&pac_id='+pac_id+'&prev_id='+prev_id	
			}		
		);	
				
			
	}


	actualizar_fecha=function() {

      top=Math.round(screen.height/2)-165;      left=Math.round(screen.width/2)-340;
      new_win =       window.open('prestaciones/procesar_prestaciones.php?fecha='+encodeURIComponent($('fecha1').value),      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+      'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+      'top='+top+', left='+left);
      new_win.focus();	
	
	}

	registro_monitoreo_ges=function() {

      top=Math.round(screen.height/2)-225;      left=Math.round(screen.width/2)-340;
      new_win =       window.open('prestaciones/validar_prestaciones/registro_ges.php?'+$('ca_id').serialize(),      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+      'menubar=no, scrollbars=yes, resizable=no, width=680, height=450, '+      'top='+top+', left='+left);
      new_win.focus();	
	
	}


</script>

<center>
<div class='sub-content' style='width:850px;'>
<div class='sub-content'>
<img src='iconos/chart_organisation.png' />
<b>Validaci&oacute;n Estad&iacute;stica de Prestaciones</b>
</div>

<div class='sub-content' id='buscar_nominas'>
<table style='width:100%;'>
<tr><td style='width:100px;text-align:right;'>Fecha:</td><td><input type='text' name='fecha1' id='fecha1' size=10  style='text-align: center;' value='<?php echo date("d/m/Y")?>'  onChange='listar_pacientes();'>  <img src='iconos/date_magnify.png' id='fecha1_boton'>
  <input type='button' value='Actualizar Fecha...' 
  onClick='actualizar_fecha();' style='font-size:10px;' /></td></tr>
</table>

</div>

<div class='sub-content2' id='lista_pacientes' style='height:350px;overflow:auto;'>


</div>

</div>
</center>

<script> 

    Calendar.setup({        inputField     :    'fecha1',         // id of the input field        ifFormat       :    '%d/%m/%Y',       // format of the input field        showsTime      :    false,        button          :   'fecha1_boton'
    });
    
	listar_pacientes(); 

</script>