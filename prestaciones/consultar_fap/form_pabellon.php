<?php 

	require_once('../../conectar_db.php');

	$servhtml = desplegar_opciones_sql('SELECT * FROM centro_costo
	WHERE centro_hosp ORDER BY centro_nombre');
	
	$pabellonhtml = desplegar_opciones("fappab_pabellones", 
	"fapp_id, fapp_desc",'1=1','true','ORDER BY fapp_desc'); 
?>



<script>


listar_fap=function() {

	$('xls').value=0;
	$('listado').innerHTML='<br><br><br><img src="imagenes/ajax-loader3.gif"><br>Espere un momento...';
	
	if($('tipo').value==12 && !validacion_hora2($('hora1'))){
		alert('La hora inicial ingresada es incorrecta');
		$('listado').innerHTML='';
		return;
	}
	
	if($('tipo').value==12 && !validacion_hora2($('hora2'))){
		alert('La hora final ingresada es incorrecta');
		$('listado').innerHTML='';
		return;
	}

	if($('tipo').value!=5)	
		var url='prestaciones/consultar_fap/listado_informe_pabellon.php';
	else
		var url='prestaciones/ingreso_fap/informe_pabellon.php';
	

	var myAjax=new Ajax.Updater(
		'listado',
		url,
		{
			method:'post',
			parameters:$('consulta').serialize(),
			evalScripts: true
		}	
	);
	
}

descargar_nominas=function() {

	$('xls').value=1;

	if($('tipo').value!=5)	
		var url='prestaciones/consultar_fap/listado_informe_pabellon.php';
	else
		var url='prestaciones/ingreso_fap/informe_pabellon.php';

	$('consulta').action=url;

	$('consulta').submit();
	
}

fix_tabla=function() {

	if($('tipo').value*1!=0) {
		//$("tr_informe").style.display="none";
		$("cant_reg").style.display="";	
		$("tr_cant_reg").style.display="";
	}else{
		//$("tr_informe").style.display="";
		$("cant_reg").style.display="none";	
		$("tr_cant_reg").style.display="none";
	}
	
	if($('tipo').value==11) {
		$("servicio_tr").style.display="";	
		$('pabellon_tr').style.display="none";
		$('hora_tr').style.display="none";
	}else if($('tipo').value==12){
		$("servicio_tr").style.display="none";	
		$('pabellon_tr').style.display="";
		$('hora_tr').style.display="";
	}else{
		$("servicio_tr").style.display="none";	
		$('pabellon_tr').style.display="none";
		$('hora_tr').style.display="none";
	}
	
	
}

validacion_hora2 = function (obj) {
	var obj=$(obj);
	
	if(trim(obj.value)=='') {
		obj.value='';
		obj.style.background='skyblue';
		return true;
	} else
		return validacion_hora(obj);
}

</script>

<center>
<div class='sub-content' style='width:950px;'>
<div class='sub-content'>
<img src='iconos/table.png' />
<b>Informes Pabell&oacute;n</b> 
</div>

<div class='sub-content'>

<form id='consulta' name='consulta' 
method='post' action='prestaciones/consultar_fap/listado_informe_pabellon.php' 
onSubmit='return false;' />

<input type='hidden' id='xls' name='xls' value='0' />

<table style='width:100%;'>

<tr><td style='width:100px;text-align:right;'>Tipo:</td><td><select id='tipo' name='tipo' onClick='fix_tabla();'>
<option value='' SELECTED>(Seleccione ...)</option>
<?php if(_cax(213)) { ?> <option value=5>Intervenciones por Paciente</option> <?php } ?>
<?php if(_cax(214)) { ?> <option value=6>Intervenciones por C&oacute;digo</option> <?php } ?>
<?php if(_cax(215)) { ?> <option value=7>Informe Estad&iacute;stico </option> <?php } ?>
<?php if (_cax(216)){ ?> <option value=8> Informe Estad&iacute;stico Completo</option> <?php } ?> 
<?php if(_cax(218)) { ?> <option value=9>Intervenciones Suspendidas </option> <?php } ?>
<?php if(_cax(219)) { ?> <option value=10>Informe Estad&iacute;stico (Incluye Glosa) </option> <?php } ?>
<?php if (_cax(217)){ ?><option value=11>D&iacute;as estada Pre Operatoria </option><?php } ?>
<?php if (_cax(221)){ ?><option value=12>Intervenci&oacute;n por Pabell&oacute;n</option><?php } ?>
<?php if(_cax(222)){ ?><option value=13>Reporte de Protocolo Operatorio</option><?php } ?>
</select>
</td></tr>

  <tr>
	  <td style='text-align: right;'>Fecha Inicio:</td>
	  <td><input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
		  <img src='iconos/date_magnify.png' id='fecha1_boton'></td>
  </tr>
  <tr>
	  <td style='text-align: right;'>Fecha Final:</td>
	  <td><input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
          <img src='iconos/date_magnify.png' id='fecha2_boton'></td>
         
  </tr>
  <tr id='hora_tr'>
	 <td style='text-align:right;'>Hora:</td>
	 <td><input type='text' id='hora1' name='hora1' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;font-weight:bold;font-size:14px;' />
		hasta <input type='text' id='hora2' name='hora2' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;font-weight:bold;font-size:14px;' />
	</td>
  </tr>
  <tr id='servicio_tr'>
      <td style='text-align:right;'>Servicio:</td>
	  <td><input type="hidden" id='centro_ruta0' name='centro_ruta0' value='<?php echo $r[0]['hosp_servicio']; ?>'>
		  <input type="text" id='servicios0' name='servicios0' class="autocomplete" 
		  onDblClick='$("centro_ruta0").value=""; $("servicios0").value="";' value='<?php echo $r[0]['tcama_tipo_ing']; ?>'>
	  </td>
  </tr>
   <tr id='pabellon_tr'>
      <td style='text-align:right;'>Pabell&oacute;n:</td>
	  <td><select id='fap_numpabellon' name='fap_numpabellon'>
			<option value=''>(Todos...)</option>
			<?php echo $pabellonhtml; ?>
		  </select>
	  </td>
  </tr>  
  <tr id='tr_cant_reg'>
	<td colspan=2 style='text-align: right;'><span id='cant_reg'></span></td>
  </tr>
  
  
    
<tr><td colspan=2 style='text-align:center;'>
<center>
<input type='button' id='actualiza' name='actualiza' 
onClick='listar_fap();'
value='-- Actualizar Listado... --' />

<input type='button' id='descarga' name='descarga' 
onClick='descargar_nominas();'
value='-- Descargar Informe en XLS... --' />

</center>
</td></tr>

</table>

</form>

</div>

<div class='sub-content2' id='listado' style='height:250px;overflow:auto;'>

</div>

</div>

</center>

  <script>
  
    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });


	seleccionar_serv2 = function(d) {

	$('centro_ruta0').value=d[0].unescapeHTML();
	$('servicios0').value=d[2].unescapeHTML(); 

    }

	   autocompletar_servicios2 = new AutoComplete(
      'servicios0', 
      'autocompletar_sql.php',
      function() {
        if($('servicios0').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hospitalizacion&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);
  
  fix_tabla();
  
  </script>
  
