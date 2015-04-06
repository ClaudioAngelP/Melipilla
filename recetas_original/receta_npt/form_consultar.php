<?php require_once('../../conectar_db.php');

$centroshtml=desplegar_opciones("centro_costo", 
	"centro_ruta, centro_nombre",'',"length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=2",'ORDER BY centro_ruta');

	$f=cargar_registro("SELECT func_nombre FROM funcionario WHERE func_id=".$_SESSION['sgh_usuario_id'], true);

?>


<script>

validacion_fecha_recep  =function(obj){
	
	if(obj.value!=''){
		validacion_fecha(obj);	
	}else{
		obj.style.background='';	
	}

}


actualizar_listado = function() {
	
	$('xls').value=0;
	
	if($('tipo_inf').value==9){
			
			if(!$('nro_busca').value*1 || $('nro_busca').value==''){
				alert('Debe ingresar un Nro v&aacute;lido'.unescapeHTML()); return;
			}
			
			var myAjax=new Ajax.Request(
			'recetas/receta_npt/buscar_npt.php',
			{
			method:'get',
			parameters: 'tipo=1&'+$('nro_busca').serialize(),
			onComplete:function(r) {
			try {
				reg=r.responseText.evalJSON(true);				
				if(reg){
			
					$('func_id').value=reg.rnpt_func_id2;
					$('nomfuncio').value=reg.func2_rut;
					$('func_nombre').innerHTML=reg.func2_nombre.unescapeHTML();
					var func2=reg.rnpt_entrega_nombre.split('|');
					
					$('prov_rut').value=func2[0];
					$('prov_nombre').innerHTML=func2[1];
					
					$('temperatura').value=reg.rnpt_temperatura;
					
					var mom_recep=reg.rnpt_fecha_recep.split(' ');
					$('fecha_recep').value=mom_recep[0];
					$('hora').value=mom_recep[1];
					
					$('doc_asociado').value=reg.rnpt_doc_tipo;
					$('doc_num').value=reg.rnpt_doc_num;
					$('comentarios').value=reg.rnpt_comentario;
					
				} else {
				
					//$('desc_cama_'+id).innerHTML='<table><tr><td><img src="iconos/error.png"></td><td> <i>N&uacute;mero de cama no es v&aacute;lido.</i></td></tr></table>';
				
				}
				
				} catch(err) {
					alert(err);
				}
			}
		}
	);
	}

	var myAjax=new Ajax.Updater(
		'listado',
		'recetas/receta_npt/listado_npt.php',
		{
			method:'post',
			parameters: $('consulta').serialize()
		}
	);
	
}

visualizar_rnpt=function(rnpt_id) {

    receta = window.open('recetas/receta_npt/visualizar_rnpt.php?rnpt_id='+rnpt_id,
    'receta_rnpt', 'left='+((screen.width/2)-225)+',top='+((screen.height/2)-200)+',width=450,height=400,status=0,scrollbars=1');
			
    receta.focus();

}

imprimir_listado=function() {
	
	var val=$('tipo_inf').value*1;
	
	if(val==1) 
		titulo='Listado NPT Pacientes por Rango de Fechas';
	if(val==2) 
		titulo='Listado Total NPT por Servicios en Rango de Fechas';
	if(val==3) 
		titulo='Listado Total NPT por Pacientes en Rango de Fechas';
	if(val==4) 
		titulo='Listado Total NPT por Funcionario en Rango de Fechas';
	if(val==5) 
		titulo='Listado Total NPT por Recepcionar';
	
	
	var html="<img src='imagenes/logotipo_grande.jpg' style='width:100px;height:100px;'><br>Hospital Dr. Gustavo Fricke - Vi&ntilde;a del Mar<br />Servicio de Salud Vi&ntilde;a del Mar - Quillota<br />";
	html+="<h2>"+titulo+"</h2><br />";
	html+="Fecha Inicio: "+$('fecha1').value+"<br />";	
	html+="Fecha Final: "+$('fecha2').value+"<br />";	
	html+="<hr>";
		
	imprimirHTML(html+$('listado').innerHTML+'<hr><center>Revisado por: <?php echo $f['func_nombre']; ?></center>');	
		
}
	
descargar_xls=function() {

	$('xls').value=1;
		
	$('consulta').method='post';
	$('consulta').action='recetas/receta_npt/listado_npt.php';
		
	$('consulta').submit();
		
}

enviar_mail=function() {
	
	var mail=prompt("Ingrese email de destino de la planilla:");
	
	var myAjax=new Ajax.Request(
		'recetas/receta_npt/enviar_mail.php',
		{
			method:'post',
			parameters: $('consulta').serialize()+'&mails='+encodeURIComponent(mail),
			onComplete:function() {
				alert('Correo enviado exitosamente.');
			}
		}
	);
	
}

recepcionar_npt=function(tipo) {

	if($('prov_rut').value=='' || $('prov_nombre').value=='' 
		|| $('func_id').value=='' || $('temperatura').value==''
		|| $('doc_num').value==''){
		
		var alerthtml='Debe completar:\n\n';
		if($('prov_rut').value=='' || $('prov_nombre').value=='')
			alerthtml+='· Qui&eacute;n entrega. \n';
		if($('func_id').value=='')
			alerthtml+='· Funcionario que recibe. \n';
		if($('temperatura').value=='')
			alerthtml+='· Temperatura. \n';
		if($('doc_num').value=='')
			alerthtml+='· N&uacute;mero de documento. \n';
			
		if(!validacion_fecha($('fecha_recep')))
			alerthtml+='· Fecha. \n';
		
		if(!validacion_hora($('hora')))
			alerthtml+='· Hora. \n';
		
		
		alert(alerthtml.unescapeHTML());
		return;
	}
	
	
	var arr=$('ids').value.split('|');
	var ids='';
	var ids2='';
	for(var i=0;i<arr.length;i++){
		if($('chk_'+arr[i]).checked)
			ids+=arr[i]+'|';	
		else
			ids2+=arr[i]+'|';	
	}
	$('ids').value=ids;
	$('ids2').value=ids2;

	var myAjax=new Ajax.Request(
		'recetas/receta_npt/recepcionar_npt.php',
		{
			method:'post',
			parameters: $('consulta').serialize()+'&action=1&tipo='+tipo,
			onComplete:function(r) {
				doc = r.responseText;
				alert(r.responseText.unescapeHTML());
				cambiar_pagina('recetas/receta_npt/form_consultar.php');
			}
		}
	);

}

despachar_npt=function() {

	if($('nomfuncionpt').value=='' || $('fnpt_nombre').value==''){alert('Seleccione el funcionario que retira los medicamentos.'); return;}

	var arr=$('ids').value.split('|');
	var ids='';
	for(var i=0;i<arr.length;i++){
		if($('chk_'+arr[i]).checked)
			ids+=arr[i]+'|';	
	}
	$('ids').value=ids;

	var myAjax=new Ajax.Request(
		'recetas/receta_npt/recepcionar_npt.php',
		{
			method:'post',
			parameters: $('consulta').serialize()+'&action=2',
			onComplete:function(r) {
				res = r.responseText.evalJSON(true);
				//console.log(res);
				cambiar_pagina('recetas/receta_npt/form_consultar.php', function() { visualizador_documentos('Visualizar Pedido', 'id_pedido='+encodeURIComponent(res[1]))+''; }); 
			}
		}
	);

}

ver_campos=function(){
	
	if($('tipo_inf').value==4){
		$('tr_fecha1').style.display='';
		$('tr_fecha2').style.display='';
		$('tr_cc').style.display='none';
		$('func_lbl').innerHTML='Funcionario:';
		$('tr_func').style.display='';
		$('tr_prov').style.display='none';
		$('tr_temp').style.display='none';
		$('tr_fecha_recep').style.display='none';
		
		$('tr_doc').style.display='none';
		$('tr_coment').style.display='none';
		$('btn_recep').style.display='none';
		$('btn_modif').style.display='none';
		$('btn_desp').style.display='none';
		$('chk_all').style.display='none';
		$('lbl_chk').style.display='none';
	}else if($('tipo_inf').value==5){
		$('tr_fecha1').style.display='';
		$('tr_fecha2').style.display='';
		$('tr_nro_doc').value='';
		$('tr_nro_doc').style.display='none';
		$('tr_cc').style.display='none';
		$('func_lbl').innerHTML='Recibido por:';
		$('tr_func').style.display='';
		$('tr_func_npt').style.display='none';
		$('tr_prov').style.display='';
		$('tr_temp').style.display='';
		$('tr_fecha_recep').style.display='';
		$('fecha_lbl').innerHTML='Fecha Recepci&oacute;n:';
		validacion_fecha_recep($('fecha_recep'));
		
		$('tr_doc').style.display='';
		$('tr_coment').style.display='';
		$('btn_recep').style.display='';
		$('btn_modif').style.display='none';
		$('btn_desp').style.display='none';
		$('chk_all').style.display='';
		$('lbl_chk').style.display='';
	}else if($('tipo_inf').value==6){
		$('tr_fecha1').style.display='';
		$('tr_fecha2').style.display='';
		$('tr_cc').style.display='';
		$('tr_func').style.display='none';
		$('tr_func_npt').style.display='';
		$('tr_prov').style.display='none';
		$('tr_temp').style.display='none';
		$('tr_fecha_recep').style.display='';
		$('fecha_lbl').innerHTML='Fecha Despacho:';
		validacion_fecha_recep($('fecha_recep'));
		
		$('tr_doc').style.display='none';
		$('tr_coment').style.display='none';
		$('btn_recep').style.display='none';
		$('btn_modif').style.display='none';
		$('btn_desp').style.display='';
		$('chk_all').style.display='';
		$('lbl_chk').style.display='';
	}else if($('tipo_inf').value==9){
		$('tr_nro_doc').value='';
		$('tr_nro_doc').style.display='';
		$('tr_fecha1').style.display='none';
		$('tr_fecha2').style.display='none';
		$('tr_cc').style.display='none';
		$('func_lbl').innerHTML='Recibido por:';
		$('tr_func').style.display='';
		$('tr_func_npt').style.display='none';
		$('tr_prov').style.display='';
		$('tr_temp').style.display='';
		$('tr_fecha_recep').style.display='';
		$('fecha_lbl').innerHTML='Fecha Recepci&oacute;n:';
		validacion_fecha_recep($('fecha_recep'));
		
		$('tr_doc').style.display='';
		$('tr_coment').style.display='';
		$('btn_modif').style.display='';
		$('btn_recep').style.display='none';
		$('btn_desp').style.display='none';
		$('chk_all').style.display='';
		$('lbl_chk').style.display='';
	}else{
		$('tr_fecha1').style.display='';
		$('tr_fecha2').style.display='';
		$('tr_cc').style.display='none';
		$('tr_func').style.display='none';
		$('tr_func_npt').style.display='none';
		$('tr_prov').style.display='none';
		$('tr_temp').style.display='none';
		$('tr_fecha_recep').style.display='none';
		
		$('tr_doc').style.display='none';
		$('tr_coment').style.display='none';
		$('btn_recep').style.display='none';
		$('btn_desp').style.display='none';
		$('chk_all').style.display='none';
		$('lbl_chk').style.display='none';
	}
	

}

check_all = function(){

	var chks=$('ids').value.split('|');

	for(var i=0;i<chks.length;i++){
		if($('chk_all').checked)
			$('chk_'+chks[i]).checked=true;
		else
			$('chk_'+chks[i]).checked=false;			
	}
	
	
}


</script>


<center>

<div class='sub-content' style='width:1000px;'>

<div class='sub-content'>
<img src='iconos/database_go.png' />
<b>Consultar Recetario Nutrici&oacute;n Parenteral</b>
</div>

<form id='consulta' name='consulta' onSubmit='return false;'>

<div class='sub-content'>

<input type='hidden' id='xls' name='xls' value='0' />

<table style='width:100%;'>

	<tr>
	    <td style='text-align: right;width:150px;' class='tabla_fila2'>Informe:</td>
	    <td colspan=3 class='tabla_fila'>
	        <select id='tipo_inf' name='tipo_inf' onChange='ver_campos(); if(this.value!=9) actualizar_listado();'>
	        <?php if(!_cax(41)){ ?>
	        <option value='1'>Detalle Completo</option>
	        <option value='2'>Totales por Servicio</option>
	        <option value='3'>Totales por Paciente</option>
	        <option value='4'>Totales por Funcionario</option>
	        <option value='5'>Recepci&oacute;n de NPT</option>
	        <option value='6'>Despacho de NPT</option>
	        <option value='8'>Indicador</option>
	        <?php } ?>
	        <option value='7'>Resumen Recepci&oacute;n</option>       
	        <?php if(_cax(42)) { ?>
	        <option value='9'>Consultar/Modificar Recepci&oacute;n de NPT</option>
	        <?php } ?>
	        </select>
	    </td>
	</tr>
	<tr name='tr_nro_doc' id='tr_nro_doc' style='display:none;'>
		<td style='text-align: right;' class='tabla_fila2'>Nro. Documento:</td>
		<td colspan=3 class='tabla_fila'>
			<input type='text' name='nro_busca' id='nro_busca' size=10>
		<input type='button' value='-- Buscar... --' onClick='actualizar_listado();' />
		</td>
	</tr>
	<tr id='tr_fecha1' name='tr_fecha1' >
	    <td style='text-align: right;' class='tabla_fila2'>Fecha Inicio:</td>
	    <td colspan=3 class='tabla_fila'>
	        <input type='text' name='fecha1' id='fecha1' size=10
	        style='text-align: center;' value='<?php echo date("d/m/Y") ?>'>
	        <img src='iconos/date_magnify.png' id='fecha1_boton'>
	    </td></tr><tr id='tr_fecha2' name='tr_fecha2' >
	    <td style='text-align:right;' class='tabla_fila2'>Fecha Final:</td>
	    <td colspan=3 class='tabla_fila'>
	        <input type='text' name='fecha2' id='fecha2' size=10
	        style='text-align: center;' value='<?php echo date("d/m/Y") ?>'>
	        <img src='iconos/date_magnify.png' id='fecha2_boton'>
	    </td>
	</tr>
	<tr>
		<td colspan=4><hr>
		</td>
	</tr>
	<tr id='tr_cc' name='tr_cc' style='display:none;'>
	    <td style='text-align: right;' class='tabla_fila2'>Centro de Costo:</td>
	    <td colspan=3 class='tabla_fila'>
	    <div id='div_centro_costo'>
	        <select id='centro_costo' name='centro_costo'>
	          <?php echo $centroshtml; ?>
	        </select>
	      </div>
	    </td>
	</tr>
	<tr id='tr_func' name='tr_func' style='display:none;'>
	    <td style='text-align: right;' id='func_lbl' class='tabla_fila2'>Funcionario:</td>
	    <td colspan=3 class='tabla_fila'>
	    <input type='hidden' name='func_id' id='func_id'>
	        <input type='text' name='nomfuncio' id='nomfuncio' size=10
	        style='text-align: center;'
	        onDblClick='this.value=""; $("func_id").value=""; $("func_nombre").innerHTML=""'>
	        &nbsp;<b><span style='font-size:12;' id='func_nombre' name='func_nombre'></span></b>
	    </td>
	</tr>
	
	<tr id='tr_func_npt' name='tr_func_npt' style='display:none;'>
	    <td style='text-align: right;' id='func_lbl_npt' class='tabla_fila2'>Recibido por:</td>
	    <td colspan=3 class='tabla_fila'>
	    <input type='hidden' name='fnpt_id' id='fnpt_id' value=0>
	        <input type='text' name='nomfuncionpt' id='nomfuncionpt' size=10
	        style='text-align: center;'
	        onDblClick='this.value=""; $("fnpt_id").value=0; $("fnpt_nombre").value=""; '
	        onKeyUp='if(event.which==13) $("fnpt_nombre").focus();'>
	        &nbsp;<b><input type='text' style='font-size:12;' id='fnpt_nombre' name='fnpt_nombre' size=25></b>
	    </td>
	</tr>
	<tr id='tr_prov' name='tr_prov' style='display:none;'>
	    <td style='text-align: right;' class='tabla_fila2'>Entregado por:</td>
	    <td colspan=3 class='tabla_fila'>
	        <input type='text' name='prov_rut' id='prov_rut' size=10 
	        style='text-align: center;' onDblClick='this.value=""; $("prov_rut").value=""; $("func_nombre").innerHTML=""'>
	        &nbsp;<input type='text' id='prov_nombre' name='prov_nombre' size=35
	        style='text-align: center;' />
	    </td>
	</tr>
	 <tr id='tr_temp' name='tr_temp' style='display:none;'>
	    <td style='text-align:right;' class='tabla_fila2'>Temperatura:</td>
	    <td colspan=3 class='tabla_fila'>
	        <input type='text' name='temperatura' id='temperatura' size=10
	        style='text-align: center;'>Celsius
	    </td>
	 </tr>
	 <tr id='tr_fecha_recep' name='tr_fecha_recep' style='display:none;'>
	    <td style='text-align:right;' class='tabla_fila2'><span name='fecha_lbl' id='fecha_lbl' ></span></td>
	    <td colspan=3 class='tabla_fila'>
	        <input type='text' name='fecha_recep' id='fecha_recep' size=10
	        style='text-align: center;' value='<?php echo date("d/m/Y")?>'
	        onKeyUp='validacion_fecha_recep(this);'>
	        <img src='iconos/date_magnify.png' id='fecha_recep_boton'>
	    &nbsp;<span class='tabla_fila2'>Hora:</span>
	        <input type='text' id='hora' name='hora' style='text-align: center;' size=10 value='<?php echo date("H:i:s");?>' onBlur='validacion_hora(this);'>
	    </td>
	</tr>
	
	<tr id='tr_doc' name='tr_doc' style='display:none;'>
		<td style='text-align:right;' class='tabla_fila2'>Documento:</td>
		<td colspan=3 class='tabla_fila'>
			<select id='doc_asociado' name='doc_asociado' onChange='$("doc_num").focus();'>
				<option value='0' SELECTED>Gu&iacute;a de Despacho</option>
				<option value='1'>Factura</option>
				<option value='2'>Boleta</option>
			</select>
			&nbsp; <span class='tabla_fila2'>N&uacute;mero:</span><input type='text' id='doc_num' name='doc_num' size=10/>
			<input type='hidden' name='ids2' id='ids2' value='0'>
		</td>
	</tr>
	<tr id='tr_coment' name='tr_coment' style='display:none;'>
		<td style='text-align:right;' class='tabla_fila2'>Comentarios:</td>
		<td colspan=3 class='tabla_fila'>
			<textarea id='comentarios' name='comentarios' cols="60" rows="2"/></textarea>
		</td>
	</tr>
<tr>
<td colspan=4>

<center>

<input type='button' value='-- Actualizar Listado... --' onClick='actualizar_listado();' />

<input type='button' value='-- Imprimir Listado... --' onClick='imprimir_listado();' />

<input type='button' value='-- Descargar XLS... --' onClick='descargar_xls();' />

<input type='button' value='-- Enviar email... --' onClick='enviar_mail();' />
&nbsp; &nbsp;
<span id='lbl_chk' name='lbl_chk' style='text-align:center;display:none;' value='Marcar Todo'>Marcar Todo</span>
<input type='checkbox' style='display:none;' name='chk_all' id='chk_all' onClick='check_all();' CHECKED>

</center>
</td>

</tr>

</table>

<!--</form>-->

</div>

<div class='sub-content2' style='height:350px;overflow:auto;' id='listado' name='listado'>
</div>
</form>
<center><input type='button' id='btn_recep' style='display:none;' 
		value='-- Recepcionar NPT... --' onClick='recepcionar_npt(0);' />
		<input type='button' id='btn_modif' style='display:none;' 
		value='-- Guardar Cambios Recepci&oacute;n NPT... --' onClick='recepcionar_npt(1);' />
		<input type='button' id='btn_desp' style='display:none;' 
		value='-- Despachar NPT... --' onClick='despachar_npt();' />
</center>

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
    Calendar.setup({
        inputField     :    'fecha_recep',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha_recep_boton'
    });
    
	validacion_hora($('hora'));
	actualizar_listado();
	
	ingreso_func=function(funcionario) {
		$('func_id').value=funcionario[3];
		$('nomfuncio').value=funcionario[1];
		$('func_nombre').innerHTML=funcionario[2];
    }

	autocompletar_funcionario = new AutoComplete(
      'nomfuncio', 
      'autocompletar_sql.php',
      function() {
        if($('nomfuncio').value.length<2) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=funcionarios&'+$('nomfuncio').serialize()
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_func);
      
      ingreso_func_npt=function(funcionario) {
		$('fnpt_id').value=funcionario[3];
		$('nomfuncionpt').value=funcionario[1];
		$('fnpt_nombre').value=funcionario[2];
    }

	autocompletar_func_npt = new AutoComplete(
      'nomfuncionpt', 
      'autocompletar_sql.php',
      function() {
        if($('nomfuncionpt').value.length<2) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=funcionarios_npt&'+$('nomfuncionpt').serialize()
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_func_npt);
      
    
</script>
