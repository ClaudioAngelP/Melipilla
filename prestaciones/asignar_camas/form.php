<?php 
	require_once('../../conectar_db.php');
	
	date_default_timezone_set('America/Halifax');
	$ccamashtml = desplegar_opciones_sql("SELECT tcama_id, tcama_tipo FROM clasifica_camas WHERE tcama_id>58 ORDER BY tcama_num_ini", NULL, '', "");
?>
<script>
	revertir_alta = function(id) {
		var myAjax = new Ajax.Request('prestaciones/asignar_camas/revierte_alta.php',
		{
			method:'post',
			parameters:'cta_cte='+id,
			onComplete: function(r) {
				try{
					//resp=r.responseText.evalJSON(true);
					alert("Alta de paciente revertida exitosamente!");
					listado();
				}catch(err){
					alert(err);
				}
			}
		});
	}
	
	
	listado=function() {
	
		var params=$('filtro').serialize()+'&'+$('busqueda').serialize()+'&'+$('tcamas').serialize()+'&'+$('esp_id').serialize()+'&'+$('cuentaCte').serialize()+'&'+$('fecha_hosp').serialize()+'&'+$('fecha_hosp2').serialize();		
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/asignar_camas/listado_pacientes.php',
			{  method:'post', parameters:params 	}	
			
		);
	
	}
	
	listar_hosp=function() {
	
	$('listado').style.display='';
    $('listado').innerHTML='<br><img src="imagenes/ajax-loader2.gif"><br><br>';

	$('xls').value=0;
	
	var myAjax=new Ajax.Updater(
		'listado',
		'prestaciones/informes_camas/listado_camas.php',
		{
			method:'post',
			parameters: $('regs').serialize()
		}	
	);

}
	
	descargar_xls=function() {

	$('xls').value=1;
		
	$('regs').method='post';
	$('regs').action='prestaciones/asignar_camas/listado_pacientes.php';
		
	$('regs').submit();
		
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
    	
      top=Math.round(screen.height/2)-200;
      left=Math.round(screen.width/2)-325;
        
      new_win = 
      window.open('prestaciones/asignar_camas/informacion_hosp.php?hosp_id='+hosp_id,
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=850, height=650, '+
      'top='+top+', left='+left);
        
      new_win.focus();
    	
	 }   
	 
	 historial_info=function(hosp_id) {
    	
      top=Math.round(screen.height/2)-200;
      left=Math.round(screen.width/2)-325;
        
      new_win = 
      window.open('prestaciones/asignar_camas/historial_hosp.php?hosp_id='+hosp_id,
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=650, height=400, '+
      'top='+top+', left='+left);
        
      new_win.focus();
    	
	 }   	

        imprimir_deis=function(hosp_id) {
            //window.open('prestaciones/ingreso_egreso_hospital/imprimir_egreso.php?hosp_id='+hosp_id, '_black');
            var ruta='prestaciones/ingreso_egreso_hospital/imprimir_egreso.php';
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-340;
            win_deis_pdf = window.open(ruta+'?hosp_id='+hosp_id,
            'Desis_pdf', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
            'top='+top+', left='+left);
            win_deis_pdf.focus();
            return;
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

     imprimir_listado=function() {
	
		var html="<h2>Informe Gesti&oacute;n de Camas</h2><br />";
		html+="Fecha: "+$('fecha').value+"<br />";	
		html+="<hr>";
		
		imprimirHTML(html+$('lista_pacientes').innerHTML);	
		
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
<input type='hidden' id='xls' name='xls' value='0' />
<div class='sub-content' style='width:980px;'>

<div class='sub-content'>
<img src='iconos/building.png'>
<b>Gesti&oacute;n Centralizada de Camas - Asignaci&oacute;n y Edici&oacute;n</b>
</div>

<div>
<table style='width:100%;'>

<tr><td style='text-align:right;'>
Filtro:
</td><td  style='width:30%;'>
<select id='filtro' name='filtro'>
<option value='0'>En Espera de Asignaci&oacute;n de Cama...</option>
<option value='1'>Pacientes Hospitalizados...</option>
<option value='2' SELECTED>Todos los Pacientes...</option>
<option value='3'>Pacientes sin M&eacute;dico Tratante...</option>}
<option value='4'>Hospitalizaciones Anuladas...</option>
<option value='5'>Pacientes Hospitalizados (Con y Sin Camas)...</option>
</select>
</td>
<td>
Servicio:&nbsp;
<select id='tcamas' name='tcamas' onChange='listado();'>
<option value='-1'>(Ver Todo...)</option>
<?php echo $ccamashtml; ?>
</select>
</td>
</tr>


<tr id='especialidad_tr'>
<td id='tag_esp' style='text-align:right;'>
Especialidad:</td><td>
<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $r[0]['hosp_esp_id']*1; ?>'>
<input type='text' id='especialidad'  name='especialidad' value='<?php echo $r[0]['esp_desc']; ?>' 
onDblClick='$("esp_id").value=""; $("especialidad").value="";' size=35>
</td>
</tr>
<!--<tr> texto de fecha<td style='text-align:right;width:250px;'>Fecha Ingreso:
</td><td colspan="2">-->
<!-- input de fecha<input type='text' id='fecha' name='fecha' size=10
style='text-align:center;' 
value='<?php echo date('d/m/Y'); ?>' />
<img src='iconos/date_magnify.png' id='fecha_boton' />
</td></tr>-->

<tr><td style='text-align:right;'>
Buscar Paciente:
</td><td colspan="2">
<input type='text' id='busqueda' name='busqueda' size=45 />&nbsp;&nbsp;(Por: RUT, Ficha, Nombre o Apellidos)
</td></tr>

<tr><td style='text-align:right;'>
Cta. Corriente:
</td><td colspan="2">
<input type='text' id='cuentaCte' name='cuentaCte' size=20 />
</td></tr>

<tr>
<td id='tag_esp' style='text-align:right;'>
Fecha Inicio:</td><td>
<input type='text' name='fecha_hosp' id='fecha_hosp' value="<?php echo date('d/m/y'); ?>" size='10'>
<img src='iconos/date_magnify.png' name='fecha_boton1' id='fecha_boton1'>
</td>
</tr>

<tr>
<td id='tag_esp' style='text-align:right;'>
Fecha T&eacute;rmino:</td><td>
<input type='text' name='fecha_hosp2' id='fecha_hosp2' value="<?php echo date('d/m/y'); ?>" size='10' onchange='listado();'>
<img src='iconos/date_magnify.png' name='fecha_boton2' id='fecha_boton2'>
</td>
</tr>

<tr><td colspan="3">
<center>
<input type='button' onClick='listado();' value='-- Actualizar Listado --' />
<input type='button' value='-- Obtener Listado XLS... --' onClick='descargar_xls();' />
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
        inputField     :    'fecha_hosp',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton1'
    });
    
    Calendar.setup({
        inputField     :    'fecha_hosp2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton2'
    });
  
   ingreso_especialidades=function(datos_esp) {
      $('esp_id').value=datos_esp[0];
      $('especialidad').value=datos_esp[2].unescapeHTML();
    }
      
    autocompletar_especialidades = new AutoComplete(
      'especialidad', 
      'autocompletar_gcamas.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=especialidad_subespecialidad&esp_desc='+encodeURIComponent($('especialidad').value)
        }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);


	listado(); 

</script>
