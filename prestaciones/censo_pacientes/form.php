<?php 
	require_once('../../conectar_db.php');
	$ccamashtml = desplegar_opciones_sql("SELECT tcama_id, tcama_tipo FROM clasifica_camas WHERE tcama_id>58 ORDER BY tcama_num_ini", NULL, '', "");
	//$ccamashtml = desplegar_opciones_sql("SELECT tcama_id, tcama_tipo FROM clasifica_camas ORDER BY tcama_num_ini", NULL, '', "");
?>
<script>

	listado=function() {
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/censo_pacientes/listado_pacientes.php',
			{  
				method:'post',
				parameters: $('fecha').serialize()+'&'+$('tcamas').serialize()+'&'+$('hora').serialize(),
				evalScripts: true	
			}	
		);
	
	}

	imprimir_listado=function() {
	
		var html="<h2>Categorizaci&oacute;n diaria de Pacientes</h2><br />";
		html+="Fecha: "+$('fecha').value+"<br />";	
		html+="<hr>";
		
		imprimirHTML(html+$('lista_pacientes').innerHTML);	
		
	}
	
	descargar_xls=function() {

		$('xls').value=1;
		
		$('censo').method='post';
		$('censo').action='prestaciones/censo_pacientes/listado_pacientes.php';
		
		$('censo').submit();
		
	}
	
	completa_info=function(hosp_id) {
    	
      top=Math.round(screen.height/2)-200;
      left=Math.round(screen.width/2)-325;
        
      new_win = 
      window.open('prestaciones/asignar_camas/informacion_hosp.php?hosp_id='+hosp_id,
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=850, height=540, '+
      'top='+top+', left='+left);
        
      new_win.focus();
    	
	 }   
	 
	 historial_info=function(hosp_id) {
    	
      top=Math.round(screen.height/2)-200;
      left=Math.round(screen.width/2)-325;
        
      new_win = 
      window.open('prestaciones/asignar_camas/historial_hosp.php?hosp_id='+hosp_id,
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=950, height=450, '+
      'top='+top+', left='+left);
        
      new_win.focus();
    	
	 }   	
		

	guardar_censo=function() {
		
		$('xls').value=0;
	
		var myAjax=new Ajax.Request(
			'prestaciones/censo_pacientes/sql.php',
			{
				method:'post',
				parameters:$('censo').serialize(),
				onComplete:function(r) {
					alert('Censo guardado exitosamente.');
					listado();
					return;
				}	
			}		
		);
		
	}
	
	categorizar_paciente = function(tipo,id,hosp_id) {
		    
      $('clase_'+hosp_id).blur();
    
    var win = new Window("win_cat", {className: "alphacube", top:30, left:0, 
                          width: 950, height: 430, 
                          title: '<img src="iconos/pill.png"> Categorizaci&oacute;n de Pacientes',
                          minWidth: 500, minHeight: 400, scrollbars: true,
                          maximizable: false, minimizable: false,
                          wiredDrag: true });
                          
    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
    
    if(tipo==0)
		var ver='tipo=0&hosp_id='+id;
	else
		var ver='tipo=1&censo_id='+id;
		
		win.setAjaxContent('prestaciones/censo_pacientes/categorizacion_pac.php', 
				{
					method: 'get', 
					parameters: ver,
					evalScripts: true
		
				});
	
    
    
    $('win_cat').win_obj = win;
		
    win.setDestroyOnClose();
    win.showCenter();
    win.show(true);
      
    return win;
			 
    }
    


</script>

<center>

<form id='censo' name='censo' onSubmit='return false;'>

<input type='hidden' id='xls' name='xls' value='0' />

<div class='sub-content' style='width:90%;'>
<div class='sub-content'>
<img src='iconos/building.png'>
<b>Gesti&oacute;n Centralizada de Camas - Categorizaci&oacute;n diaria de Pacientes</b>
</div>
<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='text-align:right;width:100px;'>Fecha:</td>
<td>
<input type='text' size=10 style='text-align:center;' 
id='fecha' name='fecha' onChange='listado();' 
value='<?php echo date('d/m/Y'); ?>'/>
<img src='iconos/calendar.png' id='fecha_boton' />
</td>
</tr>
<tr>
<td style='text-align:right;'>
Sector:
</td>
<td>
<select id='tcamas' name='tcamas' onChange='listado();'>
<?php echo $ccamashtml; ?>
</select>
</td>

<td style='text-align:right;'>
Hora:
</td>
<td>
<select id='hora' name='hora' onChange='listado();'>
<option value='11:00' SELECTED>11:00</option>
<!--<option value='12:00'>12:00</option>
<option value='15:00'>15:00</option>
<option value='23:59'>23:59</option>-->
</select>
</td>

</tr>

<tr id='tr_reg' style=''>
<td style='text-align:right;'>Total Pacientes: </td>
<td id='td_reg_t' name='td_reg_t' style='text-align:left;font-weight:bold;'>0</td>
<td style='text-align:right;'>Total Registros Categorizados: </td>
<td id='td_reg_c' name='td_reg_c' style='text-align:left;font-weight:bold;'>0</td>
</tr>

</table>
</div>
<center>
<input type='button' value='-- Actualizar Listado --' onClick='listado();' />
<input type='button' value='-- Descargar XLS --' onClick='descargar_xls();' />
<input type='button' value='-- Imprimir Listado... --' onClick='imprimir_listado();' />
<!--Cantidad de Registros: 
								<span id='cant_registros' style='font-weight:bold;'>0</span>-->
</center>
<div class='sub-content2' style='height:280px;overflow:auto;' 
id='lista_pacientes'>

</div>

<center>
<input type='button' onClick='guardar_censo();' 
value=' -- Guardar Categorizaci&oacute;n Diaria -- '>
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


	 

</script>
