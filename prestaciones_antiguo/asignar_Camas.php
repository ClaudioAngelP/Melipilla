<?php 

	require_once('../../conectar_db.php');
		
			date_default_timezone_set('America/Halifax');
	
	$ccamashtml = desplegar_opciones_sql( 
	  "SELECT tcama_id, tcama_tipo 
		FROM clasifica_camas  
	   ORDER BY tcama_num_ini", NULL, '', "");

?>

<script>
	listado=function() {
	
		var params=$('filtro').serialize()+'&'+$('busqueda').serialize()+'&'+$('tcamas').serialize();	
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/asignar_camas/listado_pacientes.php',
			{  method:'post', parameters:params 	}	
			
		);
	
	}
	
	eliminar_hosp=function(hosp_id) {
		
		var conf=confirm("&iquest;Est&aacute; seguro que desea eliminar al paciente? - No hay opciones para deshacer.".unescapeHTML());
		
		if(!conf) return;
	
		var myAjax=new Ajax.Request(
			'prestaciones/asignar_camas/sql_eliminar.php',
			{
				method:'post',
				parameters:'hosp_id='+hosp_id,
				onComplete:function() {
					listado();	
				}	
			}		
		);	
		
	}

   ver_camas = function(id) {

      top=Math.round(screen.height/2)-115;
      left=Math.round(screen.width/2)-375;
        
      new_win = 
      window.open('prestaciones/ingreso_egreso_hospital/ver_camas.php',
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=no, resizable=no, width=750, height=300, '+
      'top='+top+', left='+left);
        
		new_win.reg_id=id;        
        
      new_win.focus();
    
    }


   gestion_camas = function(id) {

      top=Math.round(screen.height/2)-265;
      left=Math.round(screen.width/2)-400;
              
      new_win = 
      window.open('prestaciones/movimiento_camas/form.php',
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=no, resizable=no, width=800, height=580, '+
      'top='+top+', left='+left);
		        
		new_win.reg_id=id;        
        
      new_win.focus();
    
    }

   visualizar_camas = function(id) {

      top=Math.round(screen.height/2)-265;
      left=Math.round(screen.width/2)-400;
              
      new_win = 
      window.open('prestaciones/movimiento_camas/visualizar_camas.php',
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=no, resizable=no, width=800, height=580, '+
      'top='+top+', left='+left);
		        
		new_win.reg_id=id;        
        
      new_win.focus();
    
    }
    
    completa_info=function(hosp_id) {
    	
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-275;
        
      new_win = 
      window.open('prestaciones/asignar_camas/informacion_hosp.php?hosp_id='+hosp_id,
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=no, resizable=no, width=550, height=300, '+
      'top='+top+', left='+left);
        
      new_win.focus();
    	
	 }    	


	 cargar_cama = function(id) {
	 
		//$('desc_cama').innerHTML='<img src="imagenes/ajax-loader3.gif"> Espere un momento...';	 
	 
		var myAjax = new Ajax.Request(
		'prestaciones/ingreso_egreso_hospital/info_cama.php',
		{
			method:'post',
			parameters:'num_cama='+($('nro_cama_'+id).value*1),
			onComplete: function(r) {

				try {

				reg=r.responseText.evalJSON(true);
				
				if(reg) {

					var ncamas=$('lista_pacientes').getElementsByTagName('input');
					var encontrado=false;
					
					for(var i=0;i<ncamas.length;i++) {
						if( 	ncamas[i].type=='text' && 
								ncamas[i].id!='nro_cama_'+id && 
								ncamas[i].value*1==$('nro_cama_'+id).value*1
							) {
							
							encontrado=true; break;
							
						}
					}

					if(!encontrado && (reg.hosp_id=='' || reg.hosp_id==id) ) {

						var icono='accept.png';
						var msg='<span style="color:#00FF00;">Asignado</span>';

					} else {

						var icono='error.png';
						var msg='<span style="color:#FF0000;">Ocupado</span>';

					}
					
					$('desc_cama_'+id).innerHTML=('<table><tr><td><img src="iconos/'+icono+'"></td><td><b>'+(reg.tcama_tipo) + '</b> / ' + (reg.cama_tipo)+'<br><b>['+msg+']</b></td></tr></table>');
					
				} else {
				
					$('desc_cama_'+id).innerHTML='<table><tr><td><img src="iconos/error.png"></td><td> <i>N&uacute;mero de cama no es v&aacute;lido.</i></td></tr></table>';
				
				}
				
				} catch(err) {
					alert(err);
				}

			}		
			
		});
	 
	}

	guardar_camas=function() {
	
		var myAjax=new Ajax.Request(
			'prestaciones/asignar_camas/sql_camas.php',
			{
				method:'post',
				parameters: $('regs').serialize(),
				onComplete: function(resp) {
				
					//var r=resp.responseText.evalJSON(true);
					
					alert( "Asignaci&oacute;n guardada exitosamente.".unescapeHTML() );
					
					listado();
					
				}
			}		
		);	
	
	}

</script>

<center>

<form id='regs' name='regs' onSubmit='return false;'>

<div class='sub-content' style='width:980px;'>

<div class='sub-content'>
<img src='iconos/building.png'>
<b>Gesti&oacute;n Centralizada de Camas - Asignaci&oacute;n de Camas</b>
</div>

<div>
<table style='width:100%;'>

<tr><td style='text-align:right;'>
Filtro:
</td><td  style='width:30%;'>
<select id='filtro' name='filtro' onChange='if(this.value!=1) {
	$("tcamas").disabled=true;
} else {
	$("tcamas").disabled=false;
}'>
<option value='0'>En Espera de Asignaci&oacute;n de Cama...</option>
<option value='1'>Pacientes Ingresados...</option>
<option value='2'>Todos los Pacientes...</option>
</select>
</td>
<td>
Sector:&nbsp;
<select id='tcamas' name='tcamas' onChange='listado();' disabled="disabled" >
<?php echo $ccamashtml; ?>
</select>
</td>
</tr>

<tr><td style='text-align:right;width:250px;'>Fecha Ingreso:
</td><td colspan="2">
<input type='text' id='fecha' name='fecha' size=10
style='text-align:center;' 
value='<?php echo date('d/m/Y'); ?>' />
<img src='iconos/date_magnify.png' id='fecha_boton' />
</td></tr>

<tr><td style='text-align:right;'>
Buscar:
</td><td colspan="2">
<input type='text' id='busqueda' name='busqueda' size=45 />
</td></tr>

<tr><td colspan="3">
<center>
<input type='button' onClick='listado();' value='-- Actualizar Listado --' />
</center>
</td></tr>

</table>

</div>

<div class='sub-content2' style='height:290px;overflow:auto;' 
id='lista_pacientes'>

</div>

<center>
<?php if(_cax(251)) { ?>
<input type='button' onClick='gestion_camas();' 
value=' -- Gesti&oacute;n y Movimiento de Camas -- '>
<?php } ?>
<input type='button' onClick='visualizar_camas();' 
value=' -- Visualizar Estado de Camas -- '>
</center>

</div>

</form>

</center>

<script> 

    Calendar.setup({
        inputField     :    'fecha',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton'
    });

	listado(); 

</script>