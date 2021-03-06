<?php

	require_once('../../conectar_db.php');
	
	$tipof=$_GET['tipo']*1;

	if(isset($_GET['fap_id'])) {
		
		$fap_id=$_GET['fap_id']*1;
		
		$fap=cargar_registro("
			SELECT *, 
			fap_fecha::date AS fap_fecha,
			date_trunc('seconds',fap_fecha)::time AS fap_hora,
		    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	 		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	 		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias
			FROM fap_pabellon
			JOIN pacientes USING (pac_id)
			LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
			LEFT JOIN diagnosticos ON diag_cod=fap_diag_cod 
			LEFT JOIN centro_costo USING (centro_ruta)
			WHERE fap_id=$fap_id
		");

		$edad='';

		if($fap['func_id']!=$_SESSION['sgh_usuario_id'] AND $fap['func_id2']==0)
			exit('NO AUTORIZADO A MODIFICAR.');

      if($fap['edad_anios']*1>1) $edad.=$fap['edad_anios'].' a&ntilde;os ';
		elseif($fap['edad_anios']*1==1) $edad.=$fap['edad_anios'].' a&ntilde;o ';

		if($fap['edad_meses']*1>1) $edad.=$fap['edad_meses'].' meses ';	
		elseif($fap['edad_meses']*1==1) $edad.=$fap['edad_meses'].' mes ';

		if($fap['edad_dias']*1>1) $edad.=$fap['edad_dias'].' d&iacute;as';
		elseif($fap['edad_dias']*1==1) $edad.=$fap['edad_dias'].' d&iacute;a';

				
	} else {
		
		$fap=false;
		$fap_id=0;
		
	}		

	$pabellonhtml = desplegar_opciones("fappab_pabellones", 
	"fapp_id, fapp_desc",($fap?$fap['fap_numpabellon']:50),'true','ORDER BY fapp_desc'); 

	
	$s=cargar_registros_obj("SELECT * FROM fap_suspension ORDER BY faps_id;", true);
	
	$suspensionhtml='';
	
	for($i=0;$i<sizeof($s);$i++) {
		$t=$s[$i]['faps_nombre'];
		$sel=(htmlentities($fap['fap_suspension'])==$t)?'SELECTED':'';
		$style=($s[$i]['faps_titulo']=='t')?'font-weight:bold;':'';
		
		$suspensionhtml.='<option value="'.$t.'" style="'.$style.'" $sel>'.$t.'</option>';
	}

?>

<html>
<title>Generaci&oacute;n de FAP</title>

<?php cabecera_popup('../..'); ?>

<script>

	function generar_fap() {
		
		if($('pac_id').value*1==-1) {
			alert( 'Debe seleccionar paciente.' );
			return;	
		}			

		if($('centro_ruta').value=='') {
			alert( 'Debe seleccionar servicio de or&iacute;gen.'.unescapeHTML() );
			return;	
		}		

		if($('fap_suspension').value!='') {
			var tmp=$('fap_suspension').value.split(' ');
			if(tmp[0].length==2) {
				alert( 'Debe seleccionar motivo de suspensi&oacute;n v&aacute;lido.'.unescapeHTML() );
				return;	
			}
		}
		
		if($('fap_numpabellon').value=='50' && $('fap_suspension').value==''){
			alert("Debe modificar n&uacute;mero de Pabell&oacute;n".unescapeHTML());
			return;
		}			
		
		if($('fap_suspension').value=='')
			var perm_gen=<?php echo json_encode(_cax(220)); ?>;
			if(!perm_gen)
				if(!comprobar_campos()){
					return;
				}
		
		$('boton_generar').disabled=true;	
	
		var myAjax=new Ajax.Request(
			'sql_generar.php',
			{
				method:'post',
				parameters:$('datos').serialize()+'&confirma=0',
				onComplete:function(r) {
					if(r.responseText.charAt(0)!='[') {
									
						var fn=window.opener.listar_fap.bind(window.opener);
						fn();
						window.close();
						//window.open('imprimir_fap.php?fap_id='+(trim(r.responseText)*1),'_self');
						
					} else {
						
						var conf=confirm(("El paciente ya registra un FAP en el sistema para hoy. &iquest;Est&aacute; seguro que viene por "+((r.responseText.charAt(1)*1)+1)+"a. vez?").unescapeHTML());
						
						if(!conf) {

							$('boton_generar').disabled=false;	
							
							return;
						}	

						var myAjax=new Ajax.Request(
							'sql_generar.php',
							{
								method:'post',
								parameters:$('datos').serialize()+'&confirma=1',
								onComplete:function(r) {

									var fn=window.opener.listar_fap.bind(window.opener);
									fn();
									window.close();
									//window.open('imprimir_fap.php?fap_id='+(trim(r.responseText)*1),'_self');

								}
							});

					}
				}
			}
		);
		
	}

function cargar_checklist() {

	var myAjax=new Ajax.Updater(
		'checklist',
		'cargar_checklist.php',
		{ method:'post',evalScripts:true,parameters:'fap_id=<?php echo $fap_id; ?>&selector=9' }
		);	

}

 function comprobar_campos(){
  
	var estado=true;

		if(document.datos.campo_1.value==''){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\n&#191;Cu&aacute;l es su nombre&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_2.value==''){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\n&#191;Cu&aacute;l es su fecha de nacimiento&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_3[0].checked && !document.datos.campo_3[1].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo, Tiene brazalete".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_4[0].checked && !document.datos.campo_4[1].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo, &#191;Existen alergias conocidas&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_4[0].checked && document.datos.campo_5.value==''){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe especificar la alergia".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_6[0].checked && !document.datos.campo_6[1].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo, Consentimiento Informado".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_8[0].checked && !document.datos.campo_8[1].checked && !document.datos.campo_8[2].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo de, la Persona que suministra la informaci&oacute;n".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_10[0].checked && !document.datos.campo_10[1].checked && !document.datos.campo_10[2].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar si Asistente Verifica localizaci&oacute;n Quir&uacute;rgica".unescapeHTML());
			estado=false;
			return false;
		}
	
	return estado;
  
  }	
	
</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/table.png' />
<b>Generar FAP de Pabell&oacute;n</b>
</div>

<form id='datos' name='datos' 
action='sql_generar.php' method='post' onSubmit='return false;'>

<input type='hidden' id='fap_id' name='fap_id' value='<?php if($fap) echo $fap['fap_id']; else echo '-1'; ?>' />

<input type='hidden' id='prev_id' name='prev_id' value='<?php if($fap) echo $fap['prev_id']; else echo '-1'; ?>' />
<input type='hidden' id='ciud_id' name='ciud_id' value='<?php if($fap) echo $fap['ciud_id']; else echo '-1'; ?>' />

<div class='sub-content'>
<table style='width:100%;'>

<tr style='display:none;'><td class='tabla_fila2' style='text-align:right;width:200px;'>Paciente:</td>
<td class='tabla_fila' colspan=3>
<select onChange='
if (this.value*1==0) {
	$("ingreso_pac").style.display="none";	
	$("busca_pac").style.display="";	
} else {
	$("ingreso_pac").style.display="";	
	$("busca_pac").style.display="none";		
}
'>
<option value='0'>Buscar</option>
<option value='1' SELECTED>Ingreso</option>
</select>
</td></tr>

<tr><td class='tabla_fila2' style='text-align:right;width:200px;'>Buscar:</td>
<td class='tabla_fila' colspan=3>
<table style='display:none;' id='busca_pac' cellpadding=0 cellspacing=0><tr><td>
<input type='hidden' id='pac_id' name='pac_id' value='<?php if($fap) echo $fap['pac_id']; else echo '-1'; ?>' />
<input type='text' size=45 id='pac_rut' name='pac_rut' value='<?php if($fap) echo $fap['pac_rut']; else echo ''; ?>' />
</td></tr></table>

<table id='ingreso_pac' 
cellpadding=0 cellspacing=0>
<tr>
<td style='width:100px;text-align:center;'>
<select id='paciente_tipo_id' name='paciente_tipo_id'>
<option value='0' SELECTED>R.U.T.</option>
<option value='3'>Nro. de Ficha</option>
<option value='5'>Cuenta Cte.</option>
</select>
</td>
<td>
<input type='text' id='paciente' name='paciente' 
style='text-align:center;' size=10
onKeyUp='this.value.toUpperCase(); if(event.which==13) buscar_paciente();' />
</td></tr>
</table>

</td></tr>


<tr>
<td class='tabla_fila2'  style='text-align:right;'>Ficha Cl&iacute;nica:</td>
<td colspan=3 class='tabla_fila' style='text-align:center;font-weight:bold;font-size:16px;' id='pac_ficha'>
<?php if($fap) echo $fap['pac_ficha']; else echo ''; ?>
</td>
</tr>

<!--
<td class='tabla_fila2' style='text-align:right;'>
Nro. de FAP:
</td><td class='tabla_fila'>
<input type='text' id='fapnro' name='fapnro' value='<?php if($fap) echo $fap['fap_fnumero']; ?>' DISABLED />
</td>
-->

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Nombre:</td>
<td class='tabla_fila' colspan=3 style='font-size:12px;font-weight:bold;' id='pac_nombre'>
<?php if($fap) echo trim($fap['pac_appat'].' '.$fap['pac_apmat'].' '.$fap['pac_nombres']); else echo ''; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha de Nac.:</td>
<td class='tabla_fila' id='pac_fc_nac'>
<?php if($fap) echo trim($fap['pac_fc_nac']); else echo ''; ?>
</td>
<td class='tabla_fila2' colspan=2 style='text-align:center;' id='pac_edad'>
Edad:<b><?php if($fap) echo $edad; else echo '(n/a)'; ?></b>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Previsi&oacute;n:</td>
<td class='tabla_fila' id='prev_desc'>
<?php if($fap) echo trim($fap['prev_desc']); else echo ''; ?>
</td>
<td class='tabla_fila2'  style='text-align:right;'>N&uacute;m. Pabell&oacute;n:</td>
<td class='tabla_fila'>
<select id='fap_numpabellon' name='fap_numpabellon'>
<?php echo $pabellonhtml; ?>
</select>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha/Hora:</td>
<td class='tabla_fila' id='fecha_hora' style='text-align:center;font-weight:bold;'>
<input type='text' size=8 id='fecha' name='fecha' style='text-align:center;' 
value='<?php if($fap) echo $fap['fap_fecha']; else echo date('d/m/Y'); ?>' /> 
<input type='text' size=5 id='hora' name='hora'  style='text-align:center;'
value='<?php if($fap) echo $fap['fap_hora']; else echo date('H:i'); ?>' />
</td>
<td class='tabla_fila2'  style='text-align:right;'>Servicio de Or&iacute;gen:</td>
<td class='tabla_fila'>
<input type='hidden' id='centro_ruta' name='centro_ruta' value='<?php if($fap) echo trim($fap['centro_ruta']); else echo ''; ?>' />
<input type='text' id='centro_nombre' name='centro_nombre' value='<?php if($fap) echo trim($fap['centro_nombre']); else echo ''; ?>' />
</td>
</tr>


<tr>
<td class='tabla_fila2'  style='text-align:right;'>Diagn&oacute;stico Pre.:</td>
<td class='tabla_fila' colspan=3>
<input type='text' id='diag_cod' name='diag_cod' size=60 value='<?php if($fap) echo $fap['fap_diag_cod']; ?>' /></td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Suspensi&oacute;n FAP:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_suspension' name='fap_suspension'>
<option value=''><i>(No ha sido suspendido...)</i></option>
<?php echo $suspensionhtml; ?>
</select>
</td>
</tr>

<tr style='display:none;'>
<td class='tabla_fila2'  style='text-align:right;'>Nro. Hoja de Cargo:</td>
<td class='tabla_fila' colspan=3>
<input type='text' id='fap_hoja_cargo' name='fap_hoja_cargo' size=20 value='<?php if($fap) echo $fap['fap_hoja_cargo']; ?>' /></td>
</tr>

<tr><td colspan=4>

<div class='sub-content'>
<img src='../../iconos/table_edit.png' />
<b>Pausa de Seguridad (Recepci&oacute;n)</b>
</div>

<div class='sub-content' id='checklist'>

</div>
</td></tr>

</table>

<center><br />
<input type='button' id='boton_generar' 
onClick='generar_fap();' value='-- Generar FAP... --' />
<br /></center>

</div>

</form>

</body>
</html>

<script>

    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('pac_nombre').innerHTML=d[2];
		$('pac_id').value=d[4];
		$('pac_ficha').innerHTML=d[3];
		$('prev_desc').innerHTML=d[6];
		$('pac_fc_nac').innerHTML=d[7];
		$('pac_edad').innerHTML='Edad: <b>'+d[11]+'</b>';    

		$('prev_id').value=d[12];
		$('ciud_id').value=d[13];
    	
    }
    
    seleccionar_centro = function(d) {

      $('centro_ruta').value=d[0];
      $('centro_nombre').value=d[2];

    }


    autocompletar_pacientes = new AutoComplete(
      'pac_rut', 
      '../../autocompletar_sql.php',
      function() {
        if($('pac_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);

    autocompletar_centro = new AutoComplete(
      'centro_nombre', 
      '../../autocompletar_sql.php',
      function() {
        if($('centro_nombre').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hospitalizacion&cadena='+encodeURIComponent($('centro_nombre').value)
        }
      }, 'autocomplete', 150, 200, 150, 2, 3, seleccionar_centro);


	buscar_paciente=function() {
	
		$('paciente').disabled=true;	
		
		if($('paciente_tipo_id').value*1==0){
			busca_paciente_fonasa($('paciente').value);
		}
		
		var myAjax=new Ajax.Request(
			'../../registro.php',
			{
				method:'get',
				parameters:'tipo=paciente&'+$('paciente_tipo_id').serialize()+'&paciente_rut='+encodeURIComponent($('paciente').value),
				onComplete:function(resp) {

					if(resp.responseText=='') {
						$('paciente').disabled=false;	
						alert('Paciente no encontrado.');
						return;	
					}

					$('paciente').disabled=false;

					try {

						var d=resp.responseText.evalJSON(true);

						var myAjax=new Ajax.Request('../../datos_paciente.php',
						{
							method:'get', parameters:'pac_id='+d[0],
							onComplete:function(d) {
								var r=d.responseText.evalJSON(true);
								seleccionar_paciente(r[0]);								
							}									
						});						
										
					} catch(err) {
						
	   				$('paciente').disabled=false;	
						alert(err);
							
					}			
						
				}						
			}		
		);	
		
	}
	
	busca_paciente_fonasa = function(rutdv){
	
		var myAjax=new Ajax.Request(
			'../ingreso_egreso_hospital/carga_paciente_fonasa.php',
			{
				method:'post',
				parameters:'tipo=paciente&'+'&paciente_rut='+rutdv+'&'+$('paciente_tipo_id').serialize(),
				onComplete:function() {
						
				}
			}		
		);	
	}


	setTimeout("$('pac_rut').focus();cargar_checklist();",200);

</script>
