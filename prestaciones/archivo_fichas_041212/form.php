<?php	require_once('../../conectar_db.php'); error_reporting(E_ALL);
	
	$espechtml=desplegar_opciones_sql("
		SELECT esp_id, esp_desc FROM especialidades
		ORDER BY esp_desc	
	", NULL, '', '');
	
?>
<script>

actualizar_list = function(){
	
	var data = $('fecha1').value;
	var tipo = $('tipo_inf').value;
	
		var myAjax=new Ajax.Updater(
			'listado_esp',
			'prestaciones/archivo_fichas/esp_select.php',
			{
				method:'post',
				parameters:'data='+data+'&tipo='+tipo
			}	
		);
		
	if(tipo==4)
		$('tr_fecha').style.display='';
	else
		$('tr_fecha').style.display='none';
}

listar_nominas=function() {

	$('listado_nominas').innerHTML='<br><br><br><img src="imagenes/ajax-loader3.gif"><br>Espere un momento...';

	if($('tipo_inf').value<4){
	var myAjax=new Ajax.Updater(
		'listado_nominas',
		'prestaciones/archivo_fichas/listar_nominas.php',
		{
			method:'post',
			parameters:$('info_nominas').serialize()
		}	
	);
	}else{
	var myAjax=new Ajax.Request(
		'prestaciones/archivo_fichas/informe.php',
		{
			method:'post',
			parameters:$('info_nominas').serialize(),
			onComplete: function(r) {
				
				datos=r.responseText.evalJSON(true);

				$('listado_nominas').innerHTML=datos[0];
				$('total').innerHTML=datos[1];
			}
		}	
	);
	}
	
}


guardar_estado=function(tipo) {

	var arr=$('ids').value.split('|');
	var ids='';

	for(var i=0;i<arr.length;i++){
		if($('chk_'+arr[i]).checked)
			ids+=arr[i]+'|';	
	}
	$('ids').value=ids;
	
	var myAjax=new Ajax.Request(
		'prestaciones/archivo_fichas/sql.php',
		{
			method:'post',
			parameters: ids,
			onComplete:function(r) {
				resp = r.responseText;
				alert(resp.unescapeHTML());
				listar_nominas();
			}
		}
	);

}

mover_ficha=function(nid,pid) {
	
	
	var myAjax=new Ajax.Request(
		'prestaciones/archivo_fichas/sql.php',
		{
			method:'post',
			parameters: 'act=1&nid='+nid+'&pid='+pid,
			onComplete:function(r) {
				resp = r.responseText;
				//alert(resp.unescapeHTML());
				//listar_nominas();
			}
		}
	);

}

mover_espontanea=function(eid,pid) {
	
	var myAjax=new Ajax.Request(
		'prestaciones/archivo_fichas/sql.php',
		{
			method:'post',
			parameters: 'act=3&eid='+eid+'&pid='+pid,
			onComplete:function(r) {
				resp = r.responseText;
				//alert(resp.unescapeHTML());
				//listar_nominas();
			}
		}
	);

}

recibir_ficha=function(aid) {
	
	var myAjax=new Ajax.Request(
		'prestaciones/archivo_fichas/sql.php',
		{
			method:'post',
			parameters: 'act=2&aid='+aid,
			onComplete:function(r) {
				resp = r.responseText;
				//alert(resp.unescapeHTML());
				//listar_nominas();
			}
		}
	);

}

 imprimir_reporte=function(){
	 
	 var fecha = new Date();
	 var dd = fecha.getDate(); 
	 var mm = fecha.getMonth()+1;
	 var yyyy = fecha.getFullYear(); 
	 if(dd<10){dd='0'+dd} 
	 if(mm<10){mm='0'+mm} 
	
	 fecha = dd+'/'+mm+'/'+yyyy;
	 	  
	 _general = '<table width="100%"><tr><td style="text-align:left;"><hr><h5>Servicio de Salud Metropolitano Norte</h5></hr></td>';
	 _general += '<td style="text-align:right;">'+fecha+'</td></tr>';
	 _general += '<tr><td style="text-align:left;"><h5>Intituto Psiquiatrico Dr. Jos&eacute; Horwitz Barak</h5></td></tr>';
	 _general += '<tr><td style="text-align:left;"><hr><h4>Listado de Fichas Solicitadas para el d&iacute;a '+$('fecha1').value+'.</h4></hr></td></tr>';
	 _general += '</table>';
	 
	 imprimirHTML(_general+$('listado_nominas').innerHTML);
	 
	}


imprimir_etiqueta=function(pac_id) {

      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win =
      window.open('prestaciones/archivo_fichas/generar_pdf.php?pac_id='+pac_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();

        }

</script>

<center>
<div class='sub-content' style='width:880px;'>
<form id='info_nominas' onSubmit='return false;'>
<div class='sub-content'>
<table>
	<tr>
		<td style='width:30px;'><img src='iconos/table_edit.png'></td>
		<td style='font-size:14px;'><b>Listado de Fichas Programadas</b></td>
		<td><select id='tipo_inf' name='tipo_inf' onChange='actualizar_list();'>
			<option value=1>(Salida de Fichas...)</option>
			<option value=2>(Entrada de Fichas...)</option>
			<option value=3>(Fichas Espont&aacute;neas...)</option>
			<option value=4>(Informe...)</option>
		</select></td>
	</tr>
</table></div>
<div class='sub-content' id='buscar_nominas'><center>
	<table style='width:100%;' cellpadding=0 cellspacing=0><tr>
		<td style='text-align:right;'>Fecha:</td>
		<td><input type='text' name='fecha1' id='fecha1' size=10
			style='text-align: center;' value='<?php echo date("d/m/Y")?>' onChange='actualizar_list();'>
			<img src='iconos/date_magnify.png' id='fecha1_boton'></td>
		<td style='text-align:right;'>Especialidad:</td>
		<td id='select_especialidades'>
			<div id='listado_esp' name='listado_esp' ></div>
		</td>
	</tr><tr id='tr_fecha' name='tr_fecha' style='display:none;'>
		<td style='text-align:right;'>Fecha Fin:</td>
		<td><input type='text' name='fecha2' id='fecha2' size=10
			style='text-align: center;' value='<?php echo date("d/m/Y")?>' onChange='actualizar_list();'>
			<img src='iconos/date_magnify.png' id='fecha1_boton'></td>
		<td>Total:&nbsp;<span id='total' name='total'></span></td>
		</tr>
			<tr><td style='text-align:right;'>Profesional:</td>
		<td colspan=3><input type='text' id='nombre_medico' name='nombre_medico' size=25 
			onDblClick='this.value=""; $("doc_nombre").innerHTML="(Todos los Profesionales)"; $("doc_id").value=-1;'>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<span id='doc_nombre' name='doc_nombre'>(Todos los Profesionales...)</span>
			<input type='hidden' id='doc_id' name='doc_id' value='-1'></td>
	</tr><tr>
		<td colspan=4><center>
			<input type='button' id='actualiza' name='actualiza' value='-- Actualizar Listado... --' onClick='listar_nominas();'/>
		</center></td>
	</tr>
	</table></center>
</div>

<div class='sub-content2' style='height:360px;overflow:auto;'id='listado_nominas'>

</div>

<center>

  <table><tr><td>
		<!--<div class='boton'>
		<table><tr><td>
		<img src='iconos/pencil.png'>
		</td><td>
		<a href='#' onClick='guardar_estado();'> Guardar Listado...</a>
		</td></tr></table>
		</div>-->
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_reporte();'> Imprimir Listado...</a>
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
    
    ingreso_rut=function(datos_medico) {
		
	  $('doc_id').value=datos_medico[3];	
      $('nombre_medico').value=datos_medico[1];
      $('doc_nombre').innerHTML=datos_medico[0];
      
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


	actualizar_list();
	
</script>
