<?php 

	require_once('../../conectar_db.php');
	
?>

<script>

validacion_fecha2=function(obj) {
	if($(obj).value=='') {
		$('obj').value='';
		$('obj').style.background='';
	} else {
		validacion_fecha(obj);
	}
}

descargar_xls=function() {

	$('xls').value=1;
		
	$('autorizar').method='post';
	$('autorizar').action='ficha_clinica/autorizar_antimicrobianos/listar_pacientes.php';
		
	$('autorizar').submit();
	
	listar_pacientes();
		
}

listar_pacientes=function(xls) {

	$('xls').value=0;

var params= $('autorizar').serialize();
	
	var myAjax=new Ajax.Updater(
		'lista_pacientes',
		'ficha_clinica/autorizar_antimicrobianos/listar_pacientes.php',
		{
			method:'post',
			parameters: params
		}
	);
	
}

guardar_aut=function(hospam_id) {

	var val=$('hospam_'+hospam_id).value*1;
	
	if(val==2 || val==3){
		var fund=prompt("Ingrese Fundamento:");
		//console.log(fund);
		if(fund==null || fund=='') return;
	}
	
	
	if(val==2) {

		  top=Math.round(screen.height/2)-125;
		  left=Math.round(screen.width/2)-475;
			
		  new_win = 
		  window.open('ficha_clinica/autorizar_antimicrobianos/modificar_aut.php?hospam_id='+hospam_id+'&fundamento='+fund,
		  'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
		  'menubar=no, scrollbars=no, resizable=no, width=950, height=250, '+
		  'top='+top+', left='+left);
					
		  new_win.focus();

		return;
	}
	
	var myAjax=new Ajax.Request(
		'ficha_clinica/autorizar_antimicrobianos/sql.php',
		{
			method:'post',
			parameters:'hospam_id='+hospam_id+'&estado='+val+'&fundamento='+fund,
			onComplete:function(r) {
				listar_pacientes();
			}
		}
	);
	
}

eliminar_paciente=function(pac_id) {
	
	var conf=confirm('&iquest;Est&aacute; seguro que desea eliminar al paciente del listado? - NO HAY OPCIONES PARA DESHACER.'.unescapeHTML());
	
	if(!conf) return;
	
	var myAjax=new Ajax.Request(
		'ficha_clinica/autorizar_farmacos/sql_eliminar.php',
		{
			method:'post',
			parameters:$('autf_id').serialize()+'&pac_id='+pac_id,
			onComplete:function(r) {
				listar_pacientes();
			}
		}
	);
	
}

ver_detalle = function(fila) {
    
      params= 'hospam_id='+encodeURIComponent(fila);
    
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('ficha_clinica/autorizar_antimicrobianos/ver_detalle.php?'+
      params,
      'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=700, height=500, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
}

ver_campos = function(){
	
	if($('tipo_inf').value==1){
		$('tr1').style.display='none';
		$('tr2').style.display='none';
		$('tr3').style.display='none';
		$('tr4').style.display='none';
		$('tr_visador').style.display='none';
	}else{
		$('tr1').style.display='';
		$('tr2').style.display='';
		$('tr3').style.display='';
		$('tr4').style.display='';

	}
	
}

</script>

<form id='autorizar' name='autorizar' onSubmit='return false;'>

<input type='hidden' id='xls' name='xls' value='0' />

<center>
<div class='sub-content' style='width:950px;'>
<div class='sub-content'><img src='iconos/pill.png' /> Autorizaci&oacute;n de F&aacute;rmacos ANTIMICROBIANOS</div>

<div class='sub-content'>

<table style='width:100%';>
<tr>
		<td style='text-align:right;' class='tabla_header'>Tipo Informe:</td>
		<td class='tabla_fila' colspan='3'><select id='tipo_inf' name='tipo_inf' onChange='ver_campos();'>
		<option value='0' SELECTED>(Detalle General...)</option>
		<option value='1'>Totales / Detalle</option>
		</select></td>
	</tr>
	<tr id='tr1' name='tr1' style='display:none;'>
		<td style='text-align:right;' class='tabla_header'>Tipo Paciente:</td>
		<td class='tabla_fila'><select id='tipo_pac' name='tipo_pac' onChange='listar_pacientes();'>
		<option value='0' SELECTED>(Todos...)</option>
		<option value='1'>Pediatricos</option>
		<option value='2'>Adultos</option>
		</select></td>
		<td style='text-align:right;' class='tabla_header'>Tipo Validaci&oacute;n:</td>
		<td class='tabla_fila'  style='width:50%';><select id='tipo_val' name='tipo_val' 
			onChange='listar_pacientes(); if(this.value!=0 && this.value!=4) $("tr_visador").style.display=""; else $("tr_visador").style.display="none";'>
		<option value='4'>(Todos...)</option>
		<option value='0' SELECTED>Pendientes</option>
		<option value='1'>Aceptados</option>
		<option value='2'>Modificados</option>
		<option value='3'>Rechazados</option>
		</select></td>
	</tr>
	<tr id='tr2' name='tr2' style='display:none;'>
		<td style='text-align:right;' class='tabla_header'>Paciente:</td>
		<td class='tabla_fila' colspan=3><input type='hidden' name='pac_id' id='pac_id' value='-1'/>
			<input type='text' name='busca_paciente' id='busca_paciente' 
				onDblClick='this.value=""; $("pac_nombre").innerHTML=""; $("pac_id").value=-1; listar_pacientes()' 
				onKeyUp='if(event.which==13) listar_pacientes();' />&nbsp;
			<b><span style='font-size:12;' id='pac_nombre' name='pac_nombre'></span></b>
		</td>
	</tr>
	<tr id='tr3' name='tr3' style='display:none;'>
		<td style='text-align:right;' class='tabla_header'>Medicamento:</td>
		<td colspan=3 class='tabla_fila'>
		<input type='hidden' id='art_id' name='art_id' value='-1' />
		<input type='text' size=10 id='art_codigo' name='art_codigo' value='' 
		onDblClick='this.value=""; $("art_glosa").innerHTML=""; $("art_id").value=-1; listar_pacientes()' 
		onKeyUp='if(event.which==13) listar_pacientes();' />
		&nbsp;<b><span style='font-size:12;' id='art_glosa' name='art_glosa'></span></b>
		</td>		
	</tr>
	<tr id='tr4' name='tr4' style='display:none;'>
		<td style='text-align:right;' class='tabla_header'>Servicio:</td>
		<td colspan=3 class='tabla_fila'>
		<input type='hidden' id='tcama_id' name='tcama_id' value='' />
		<input type='text' size=10 id='tcama_tipo' name='tcama_tipo' value='' 
		onDblClick='this.value=""; $("tcama_tipo").innerHTML=""; $("tcama_id").value=""; listar_pacientes()' 
		onKeyUp='if(event.which==13) listar_pacientes();' />
		</td>		
	</tr>
	<tr name='tr_visador' id='tr_visador' style='display:none;'> <!-- style='display:none;'  -->
		<td style='text-align:right;' class='tabla_header'>Visador:</td>
		<td class='tabla_fila' colspan=3><input type='hidden' name='func_id' id='func_id' value='-1'/>
			<input type='text' name='nomfuncio' id='nomfuncio' 
				onDblClick='this.value=""; $("func_nombre").innerHTML=""; $("func_id").value=-1; listar_pacientes()' 
				onKeyUp='if(event.which==13) listar_pacientes();' />&nbsp;
			<b><span style='font-size:12;' id='func_nombre' name='func_nombre'></span></b>
		</td>
	</tr>
	<tr>
		<td style='text-align:right;' class='tabla_header'>Fecha Inicio:</td>
		<td class='tabla_fila'>
    <input type='text' name='fecha1' id='fecha1' size=10
    style='text-align: center;' value='<?php echo date("d/m/Y")?>'
    onBlur='listar_pacientes();' onKeyUp='if(event.which==13) listar_pacientes();'>
    <img src='iconos/date_magnify.png' id='fecha1_boton'>
    </td>
    <td style='text-align:right;' class='tabla_header'>Fecha T&eacute;rmino:</td>
		<td class='tabla_fila'>
    <input type='text' name='fecha2' id='fecha2' size=10
    style='text-align: center;' value='<?php echo date("d/m/Y")?>'
    onBlur='listar_pacientes();' onKeyUp='if(event.which==13) listar_pacientes();'>
    <img src='iconos/date_magnify.png' id='fecha2_boton'>
    </td>
	</tr>
	
	
</table>


<div class='sub-content2' id='lista_pacientes' style='height:300px;overflow:auto;'>



</div>
<center>
<div class='boton'>
   <table>
        <tr>
            <td>
                <img src='iconos/page_excel.png'>
            </td>
            <td>
                <a href='#' onClick='descargar_xls();'> Descargar XLS (MS Excel) ...</a>
            </td>
        </tr>
    </table>
</div>
</center>

</div>
</center>

</form>

<script>

ingreso_paciente=function(paciente) {
		$('pac_id').value=paciente[4];
		$('busca_paciente').value=paciente[1];
		$('pac_nombre').innerHTML=paciente[2];
		listar_pacientes();	
    }

autocompletar_paciente = new AutoComplete(
      'busca_paciente', 
      'autocompletar_sql.php',
      function() {
        if($('busca_paciente').value.length<2) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=pacientes&'+$('busca_paciente').serialize()
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_paciente);

ingreso_ab=function(datos_art) {
      	      	
      	$('art_id').value=datos_art[0];
      	$('art_codigo').value=datos_art[1];
      	$('art_glosa').innerHTML=datos_art[2].unescapeHTML();
      	//$('art_forma').innerHTML=datos_art[3];
      	listar_pacientes();
      	
      }

      autocompletar_medicamentos = new AutoComplete(
      	'art_codigo', 
      	'ficha_clinica/autorizar_antimicrobianos/autocompletar_sql.php',
      function() {
        if($('art_codigo').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicamento_restringido&art_codigo='+encodeURIComponent($('art_codigo').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_ab);  


ingreso_servicio=function(datos_tcama) {
      	      	
      	$('tcama_id').value=datos_tcama[0];
      	$('tcama_tipo').value=datos_tcama[2];
      	//$('art_glosa').innerHTML=datos_art[2].unescapeHTML();
      	listar_pacientes();
      	
      }

autocompletar_tservicio = new AutoComplete(
      	'tcama_tipo', 
      	'ficha_clinica/autorizar_antimicrobianos/autocompletar_sql.php',
      function() {
        if($('tcama_tipo').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=servicios_hospitalizacion&cadena='+encodeURIComponent($('tcama_tipo').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_servicio);  



ingreso_func=function(funcionario) {
		$('func_id').value=funcionario[3];
		$('nomfuncio').value=funcionario[1];
		$('func_nombre').innerHTML=funcionario[2];
		listar_pacientes();
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
            
      
Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
      });
      Calendar.setup({
        inputField     :    'fecha2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha2_boton'
      });
ver_campos();
listar_pacientes();

</script>
