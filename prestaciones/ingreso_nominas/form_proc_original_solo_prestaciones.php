<?php 

	require_once('../../conectar_db.php');
	
	$nomd_id=$_GET['nomd_id']*1;
	
	$ndet=cargar_registro("
	  SELECT 
		pacientes.*, nomina_detalle.*, nomina.*, diag_desc, 
		date_part('year',age(pac_fc_nac)) as edad  
	  FROM nomina_detalle
	  JOIN nomina USING (nom_id)
	  JOIN pacientes USING (pac_id)
	  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	  WHERE nomd_id=$nomd_id
	  ORDER BY nomd_folio, 
	  (CASE WHEN trim(both from pac_ficha)='' THEN '0' 
	  	ELSE pac_ficha END)::bigint	
	", true);
	
	$pac_id=$ndet['pac_id']*1;
	$esp_id=$ndet['nom_esp_id']*1;

	$proc=cargar_registro("SELECT * FROM procedimiento
											WHERE esp_id=$esp_id");
	
	$presta=cargar_registros_obj("SELECT * FROM procedimiento_codigo
											LEFT JOIN codigos_prestacion ON pc_codigo=codigo 
											WHERE esp_id=$esp_id", true);
											
	$p=cargar_registros_obj("
		SELECT * FROM nomina_detalle_prestaciones
		JOIN codigos_prestacion ON nomdp_codigo=codigo
		LEFT JOIN procedimiento_codigo USING (pc_id)  
		WHERE nomd_id=$nomd_id	
		ORDER BY nomdp_codigo
	", true);

	$tmp=Array();
	
	if($p) {
		for($i=0;$i<sizeof($p);$i++) {
			
			$tmp[$i]->codigo=$p[$i]['nomdp_codigo'];
			$tmp[$i]->cantidad=$p[$i]['nomdp_cantidad']*1;

			if($p[$i]['pc_desc']!='')
				$tmp[$i]->desc='<b>['.$p[$i]['pc_desc'].']</b> '.$p[$i]['glosa'];
			else
				$tmp[$i]->desc=$p[$i]['glosa'];
				
			$tmp[$i]->pc_id=$p[$i]['pc_id']*1;
			
		}		
	}

	if($proc['esp_orden_atencion']=='t') {

	$oa=cargar_registros_obj("SELECT * FROM orden_atencion 
	WHERE (oa_pac_id=$pac_id AND oa_estado=1) OR oa_id=".$ndet['oa_id']);
	
	$oahtml='';	
	
	if($oa)
	for($i=0;$i<sizeof($oa);$i++) {
				
		if($oa[$i]['oa_id']==$ndet['oa_id'])
			$sel='SELECTED'; 
		else 
			$sel='';
			
		if($oa[$i]['oa_folio']!=0)
			$oahtml.='<option value="'.$oa[$i]['oa_id'].'" '.$sel.' >Nro. Folio: '.$oa[$i]['oa_folio'].'</option>';
		else 	
			$oahtml.='<option value="'.$oa[$i]['oa_id'].'" '.$sel.' >Cod. Interno: #'.$oa[$i]['oa_id'].'</option>';
			
	}		
	
	}
	
?>

<html>
<title>Registro de Ex&aacute;menes y Procedimientos</title>

<?php cabecera_popup('../..'); ?>

<script>

	function limpiar_oa() {

			$('centro_ruta_oa').value='';		
			$('centro_nombre_oa').value='';		
			$('esp_id_oa').value=0;		
			$('esp_desc_oa').value='';		
			$('doc_id_oa').value=0;		
			$('doc_rut_oa').value='';		
			$('doc_nombre_oa').value='';
			
			$('inst_id_oa').value=0;		
			$('inst_desc_oa').value='';		
			$('esp_id2_oa').value=0;		
			$('esp_desc2_oa').value='';		
			$('prof_id_oa').value=0;		
			$('prof_rut_oa').value='';		
			$('prof_nombre_oa').value='';

		
	}		

	function cargar_oa() {
		
		var myAjax=new Ajax.Request(
			'cargar_oa.php',
			{
				method:'post',
				parameters:$('oa_id').serialize()+'&'+$('nomd_id').serialize(),
				onComplete:function(r) {
					
					var datos=r.responseText.evalJSON(true);

					if(!datos) {

						$('fecha_oa').value='<?php echo date('d/m/Y'); ?>';
						$('tipo_oa').value=1;
			
						limpiar_oa();
						
						fix_fields();
									
						validacion_fecha($('fecha_oa'));
						
						$('tipo_oa').disabled=false;		
						
						return;			

						
					}

					$('fecha_oa').value=datos.oa_fecha;		

					limpiar_oa();
					
					if(datos.doc_id!=0 || datos.origen_tipo==1) {
						//console.log('interna');
						$('tipo_oa').value=1;					
						$('centro_ruta_oa').value=datos.oa_centro_ruta;		
						$('centro_nombre_oa').value=datos.centro_nombre;
						$('esp_id_oa').value=datos.oa_especialidad;		
						$('esp_desc_oa').value=datos.esp_desc.unescapeHTML();		
						$('doc_id_oa').value=datos.oa_doc_id;		
						$('doc_rut_oa').value=datos.doc_rut;		
						$('doc_nombre_oa').value=(datos.doc_paterno+' '+datos.doc_materno+' '+datos.doc_nombres).unescapeHTML();		
					} else {
						//console.log('externa');					
						$('tipo_oa').value=2;					
						$('inst_id_oa').value=datos.inst_id;		
						$('inst_desc_oa').value=datos.inst_nombre.unescapeHTML();
						$('esp_id2_oa').value=datos.esp_id;		
						$('esp_desc2_oa').value=datos.esp_desc.unescapeHTML();
						$('prof_id_oa').value=datos.prof_id;		
						$('prof_rut_oa').value=datos.prof_rut;		
						$('prof_nombre_oa').value=(datos.prof_paterno+' '+datos.prof_materno+' '+datos.prof_nombres).unescapeHTML();		
					}		

					if(datos.oa_id!=0 || datos.inter_id!=0)
						$('tipo_oa').disabled=true;		
					else
						$('tipo_oa').disabled=false;

					fix_fields();

					validacion_fecha($('fecha_oa'));		
					
				}	
			}		
		);	
		
	}

	function guardar_procedimientos() {

	<?php if($proc['esp_orden_atencion']=='t') { ?>

		/*
		if(!validacion_fecha($('fecha_oa'))) {
			alert(('Debe ingresar una fecha de solicitud v&aacute;lida.').unescapeHTML());
			return;	
		}

		if(!$('centro_nombre_oa').disabled && $('centro_ruta_oa').value=='') {
			alert('Debe seleccionar el servicio solicitante.');
			return;	
		}

		if(!$('esp_desc_oa').disabled && $('esp_id_oa').value*1==0) {
			alert('Debe seleccionar la unidad solicitante.');
			return;	
		}

		if(!$('doc_nombre_oa').disabled && $('doc_id_oa').value*1==0) {
			alert(('Debe seleccionar el m&eacute;dico solicitante.').unescapeHTML());
			return;	
		}
		*/

	<?php } ?>

	<?php if(!$presta OR sizeof($presta)>1) { ?>

		//if(presta.length==0) {
			//alert(('Debe ingresar las prestaciones realizadas.').unescapeHTML());
			//return;	
		//}
		
	<?php } ?>

		var params='&presta='+encodeURIComponent(presta.toJSON());

		$('nomd_diag_cod').disabled=false;

		var myAjax=new Ajax.Request(
			'sql_procedimientos.php',
			{
				method:'post',
				parameters:$('datos').serialize()+params,
				onComplete: function() {
					alert('Registro completado exitosamente.');
					//window.close();	
				}	
			}		
		);	
	}

	fix_fields=function() {
	
		var t=$('tipo_oa').value*1;
		
		if(t==1) {
			
			$('serv_tr').style.display='';	
			$('unid_tr').style.display='';	
			$('medi_tr').style.display='';	

			$('inst_tr').style.display='none';	
			$('espe_tr').style.display='none';	
			$('prof_tr').style.display='none';	
			
		} else {
			
			$('serv_tr').style.display='none';	
			$('unid_tr').style.display='none';	
			$('medi_tr').style.display='none';	

			$('inst_tr').style.display='';	
			$('espe_tr').style.display='';	
			$('prof_tr').style.display='';	
			
		}				
		
	}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/table_edit.png' />
<b>Registro de Ex&aacute;menes/Procedimientos</b>
</div>

<form id='datos' name='datos' onSubmit='return false;'>
<input type='hidden' id='nomd_id' name='nomd_id' value='<?php echo $nomd_id; ?>' />
<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>' />
<input type='hidden' id='cambia_presta' name='cambia_presta' value='0' />


<?php if ($proc['esp_orden_atencion']=='t')  { ?>

<div class='sub-content'>
<table style='width:100%;'>

<tr><td style='text-align:right;' class='tabla_fila2'>
Orden de Atenci&oacute;n:
</td><td class='tabla_fila'>
<select id='oa_id' name='oa_id' onChange='cargar_oa();'>
<?php echo $oahtml; ?>
<option value='0'>(Sin Orden de Atenci&oacute;n...)</option>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Tipo:
</td><td class='tabla_fila'>
<select id='tipo_oa' name='tipo_oa' onChange='fix_fields();'>
<option value='1'>Local</option>
<option value='2'>Externa</option>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Fecha Solicitud:
</td><td class='tabla_fila'>
<input type='text' id='fecha_oa' name='fecha_oa' style='text-align:center;' 
value='<?php echo date('d/m/Y'); ?>' size=15 onKeyUp='validacion_fecha(this);' />
</td></tr>

<tr id='serv_tr'><td style='text-align:right;' class='tabla_fila2'>
Servicio Solicitante:
</td><td class='tabla_fila'>
<input type='hidden' id='centro_ruta_oa' name='centro_ruta_oa' value='' />
<input type='text' id='centro_nombre_oa' name='centro_nombre_oa' value='' size=35 
onDblClick='$("centro_ruta_oa").value=""; $("centro_nombre_oa").value="";' />
</td></tr>

<tr id='unid_tr'><td style='text-align:right;' class='tabla_fila2'>
Unidad Solicitante:
</td><td class='tabla_fila'>
<input type='hidden' id='esp_id_oa' name='esp_id_oa' value='' />
<input type='text' id='esp_desc_oa' name='esp_desc_oa' value='' size=35 
onDblClick='$("esp_id_oa").value=""; $("esp_desc_oa").value="";' />
</td></tr>

<tr id='medi_tr'><td style='text-align:right;' class='tabla_fila2'>
Profesional Solicitante:
</td><td class='tabla_fila'>
<input type='hidden' id='doc_id_oa' name='doc_id_oa' value='' />
<input type='text' id='doc_rut_oa' name='doc_rut_oa' value='' size=15 style='text-align:center;' DISABLED />
<input type='text' id='doc_nombre_oa' name='doc_nombre_oa' value='' size=35 
onDblClick='$("doc_id_oa").value=""; $("doc_rut_oa").value=""; $("doc_nombre_oa").value="";' />
</td></tr>




<tr id='inst_tr' style='display:none;'><td style='text-align:right;' class='tabla_fila2'>
Instituci&oacute;n Solicitante:
</td><td class='tabla_fila'>
<input type='hidden' id='inst_id_oa' name='inst_id_oa' value='' />
<input type='text' id='inst_desc_oa' name='inst_desc_oa' value='' size=35 
onDblClick='$("inst_id_oa").value=""; $("inst_desc_oa").value="";' />
</td></tr>

<tr id='espe_tr' style='display:none;'><td style='text-align:right;' class='tabla_fila2'>
Especialidad Solicitante:
</td><td class='tabla_fila'>
<input type='hidden' id='esp_id2_oa' name='esp_id2_oa' value='' />
<input type='text' id='esp_desc2_oa' name='esp_desc2_oa' value='' size=35 
onDblClick='$("esp_id2_oa").value=""; $("esp_desc2_oa").value="";' />
</td></tr>

<tr id='prof_tr' style='display:none;'><td style='text-align:right;' class='tabla_fila2'>
Profesional Solicitante:
</td><td class='tabla_fila'>
<input type='hidden' id='prof_id_oa' name='prof_id_oa' value='' />
<input type='text' id='prof_rut_oa' name='prof_rut_oa' value='' size=15 style='text-align:center;' DISABLED />
<input type='text' id='prof_nombre_oa' name='prof_nombre_oa' value='' size=35 
onDblClick='$("prof_id_oa").value=""; $("prof_rut_oa").value=""; $("prof_nombre_oa").value="";' />
</td></tr>

</table>
</div>

<?php } ?>


<div class='sub-content'>

<table style='width:100%;'>

<tr><td style='width:100px;text-align:right;' class='tabla_fila2'>
R.U.T.:
</td><td class='tabla_fila' style='text-align:left;font-size:16px;'>
<?php echo $ndet['pac_rut']; ?>
</td><td style='width:100px;text-align:right;' class='tabla_fila2'>
Nro. Ficha:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;font-weight:bold;'>
<?php echo $ndet['pac_ficha']; ?>
</td></tr>

<tr><td style='width:100px;text-align:right;' class='tabla_fila2'>
Nombre Paciente:
</td><td colspan=3 class='tabla_fila' style='text-align:left;font-weight:bold;font-size:16px;'>
<?php echo $ndet['pac_nombres'].' '.$ndet['pac_appat'].' '.$ndet['pac_apmat']; ?>
</td></tr>

<tr>
<td style='text-align:right;' class='tabla_fila2'>Or&iacute;gen:</td>
<td class='tabla_fila' colspan=3>
<select id='origen' name='origen'>
<option value='A' <?php if($ndet['nomd_origen']=='A') echo 'SELECTED'; ?>>Ambulatorio</option>
<option value='H' <?php if($ndet['nomd_origen']=='H') echo 'SELECTED'; ?>>Hospitalizado</option>
<option value='U' <?php if($ndet['nomd_origen']=='U') echo 'SELECTED'; ?>>Urgencias</option>
</select>
</td>
</tr>

<tr>
<td style='text-align:right;' class='tabla_fila2'>Diag. CIE10:</td>
<td class='tabla_fila' colspan=3>
<input type='text' id='nomd_diag_cod' name='nomd_diag_cod' 
value='<?php echo $ndet['nomd_diag_cod']; ?>' DISABLED size=5 style='font-weight:bold;text-align:center;' />
<input type='text' id='nomd_diagnostico' 
value='<?php echo $ndet['nomd_diag']; ?>' name='nomd_diagnostico' size=35
onDblClick='$("nomd_diag_cod").value=""; $("nomd_diagnostico").value="";' />
</td>
</tr>

<tr><td style='width:100px;text-align:right;' class='tabla_fila2' valign='top'>
Observaciones:
</td><td colspan=3 class='tabla_fila' style='text-align:left;font-weight:bold;'>
<textarea id='observaciones' name='observaciones' cols=55 rows=3><?php echo $ndet['nomd_observaciones']; ?></textarea>
</td></tr>

</table>

</div>


<?php if(!$presta OR sizeof($presta)>1) { ?>

<div class='sub-content'>
<img src='../../iconos/table.png'>
<b>Registro de Prestaciones</b>
</div>
	
<div class='sub-content2' id='lista_presta' 
style='height:120px;overflow:auto;'>
	
</div>

<div class='sub-content'>

<table style='width:100%;' cellpadding=0 cellspacing=0>
<tr><td style='width:15px;'>
<center>
<img src='../../iconos/add.png' />
</center>
</td><td style='width:100px;text-align:right;'>Agregar Prest.:</td>
<td>
<input type='hidden' id='desc_presta' name='desc_presta' value='' />
<input type='hidden' id='pc_id' name='pc_id' value='0' />
<input type='text' id='cod_presta' name='cod_presta' size=10 />
</td><td style='text-align:right;'>
Cant.:
</td><td>
<input type='text' id='cantidad' name='cantidad'
onKeyUp='if(event.which==13) agregar_prestacion();' size=3 />
</td></tr>
</table>

</div>

<?php } else { ?>

<div class='sub-content'>
<center><b><u>Prestaci&oacute;n Recibida</u></b></center><br />
<table style='width:100%;'><tr><td style='text-align:center;width:100px;font-weight:bold;font-size:18px;'>
<?php echo $presta[0]['pc_codigo']; ?>
</td><td style='text-align:justify;'>
<?php echo $presta[0]['glosa']; ?>
</td></tr></table>
</div>


<?php } ?>

<?php 

	if($proc['esp_campos']!='') {

	print("
		<div class='sub-content'>
		<table style='width:100%;'>
	");
	
	$campos=explode('|', $proc['esp_campos']);
	
	$valores=cargar_registros_obj("
			SELECT * FROM nomina_detalle_campos WHERE nomd_id=$nomd_id 
			ORDER BY nomdc_offset	
	");
	
	for($i=0;$i<sizeof($campos);$i++) {
	
		if(strstr($campos[$i],'>>>')) {
			$cmp=explode('>>>',$campos[$i]);
			$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
		} else {
			$cmp=$campos[$i]; $tipo=2;
		}
		
		print("<tr>
			<td style='width:200px;text-align:right;' class='tabla_fila2'>$nombre :</td>
			<td class='tabla_fila'>");
		
		if($tipo==0) {

			if(isset($valores[$i]['nomdc_valor'])) 
				$vact=($valores[$i]['nomdc_valor']=='true')?'CHECKED':'';
			else 
				$vact='';

			print("<input type='checkbox' id='campo_$i' name='campo_$i' $vact />");	

		} elseif($tipo==1) {

			if(isset($valores[$i]['nomdc_valor'])) 
				$vact=($valores[$i]['nomdc_valor']=='true')?'CHECKED':'';
			else 
				$vact='CHECKED';

			print("<input type='checkbox' id='campo_$i' name='campo_$i' $vact />");
							
		} elseif($tipo==5) {
		
			$opts=explode('//', $cmp[2]);
						
			if(isset($valores[$i]['nomdc_valor'])) 
				$vact=$valores[$i]['nomdc_valor'];
			else 
				$vact='';

			print("<select id='campo_$i' name='campo_$i'>");
			
			for($k=0;$k<sizeof($opts);$k++) {
				
				$opts[$k]=trim($opts[$k]);
				
				if($vact==$opts[$k]) $sel='SELECTED'; else $sel='';
				
				print("<option value='".$opts[$k]."' $sel>".$opts[$k]."</option>");	
			}			
			
			print("</select>");		
			
		} else {

			if(isset($valores[$i]['nomdc_valor'])) 
				$vact=$valores[$i]['nomdc_valor'];
			else 
				$vact='';
			
			print("<input type='text' id='campo_$i' name='campo_$i' value='$vact' />");
							
		}	
		
		print("</td></tr>");	
		
	}	

	print("
		</table>
		</div>
	");
	
	}
	
?>

<center><br />
<input type='button' id='' name='' onClick='guardar_procedimientos();'
 value='--- Guardar Registro ---' />
</center>

</form>

</body>
</html>

<script>

	 presta=<?php echo json_encode($tmp); ?>;

    agregar_prestacion = function() {
    	
			var codigo=$('cod_presta').value;
			var desc_presta=$('desc_presta').value;
			var cant=$('cantidad').value;
			var pc_id=$('pc_id').value;
			
			var num=presta.length;
			presta[num]=new Object();
			presta[num].codigo=codigo;
			presta[num].desc=desc_presta;
			presta[num].cantidad=cant;
			presta[num].pc_id=pc_id;

			listar_prestaciones();
			
			$('cod_presta').select();
			$('cod_presta').focus();
			
			$('cambia_presta').value=1;

    }
    
    listar_prestaciones=function() {
    
		var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>C&oacute;digo</td><td>Cant.</td><td>Descripci&oacute;n</td><td>Eliminar</td></tr>';    
    
		for(var i=0;i<presta.length;i++) {
			
			clase=(i%2==0)?'tabla_fila':'tabla_fila2';
	
			if(presta[i].desc.length>37) 
				var descr=presta[i].desc.substr(0,67)+'...';
			else
				var descr=presta[i].desc;	
		
			html+='<tr class="'+clase+'" ';
			html+='onMouseOver="this.className=\'mouse_over\';" ';
			html+='onMouseOut="this.className=\''+clase+'\';">';
			html+='<td style="text-align:center;font-weight:bold;">'+presta[i].codigo+'</td><td style="text-align:center;">'+presta[i].cantidad+'</td><td>'+descr+'</td>';
			html+='<td><center><img src="../../iconos/delete.png" style="cursor: pointer;" onClick="eliminar_prestacion('+i+');"></center></td></tr>';		
			
		}   
		
		html+='</table>' 

		$('lista_presta').innerHTML=html;
    	
    }

    eliminar_prestacion = function(id) {

		presta=presta.without(presta[id]);
		
		listar_prestaciones();

		$('cambia_presta').value=1;

    }

	<?php if(!$presta OR sizeof($presta)>1) { ?>

    lista_prestaciones=function() {

        if($('cod_presta').value.length<3) return false;

        var params='tipo=proc_prestacion&'+$('esp_id').serialize()+'&'+$('cod_presta').serialize();

        /*if($('auge').checked) {
          params='tipo=prestacion_patologia&pat_id=';
          params+=getRadioVal('info_prestacion','pat_id')+'&'+$('cod_presta').serialize();;
        }*/

        return {
          method: 'get',
          parameters: params
        }

    }

    seleccionar_prestacion = function(presta) {

      //$('codigo_prestacion').value=presta[0];
      //$('desc_presta').innerHTML='<center><b><u>Descripci&oacute;n de la Prestaci&oacute;n</u></b></center>'+presta[2];
      
      if(presta[3]!='')
			$('desc_presta').value='<b>['+presta[2]+']</b> '+presta[3];
		else
			$('desc_presta').value=presta[2];
					
		$('pc_id').value=presta[4]*1;
		$('cantidad').value='1';
      
      $('cantidad').select();
      $('cantidad').focus();

    }

    autocompletar_prestaciones = new AutoComplete(
      'cod_presta', 
      '../../autocompletar_sql.php',
      lista_prestaciones, 'autocomplete', 450, 100, 150, 1, 3, seleccionar_prestacion);
      
      listar_prestaciones();
      
    <?php } ?>




	<?php if($proc['esp_orden_atencion']=='t') { ?>

   seleccionar_centro = function(d) {

      $('centro_ruta_oa').value=d[0];
      $('centro_nombre_oa').value=d[2];

    }

    autocompletar_centro = new AutoComplete(
      'centro_nombre_oa', 
      '../../autocompletar_sql.php',
      function() {
        if($('centro_nombre_oa').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=centros_pabellon&cadena='+encodeURIComponent($('centro_nombre_oa').value)
        }
      }, 'autocomplete', 250, 200, 150, 2, 3, seleccionar_centro);

    seleccionar_especialidad = function(d) {

      $('esp_id_oa').value=d[0];
      $('esp_desc_oa').value=d[2].unescapeHTML();

    }
    
    autocompletar_especialidades = new AutoComplete(
      'esp_desc_oa', 
      '../../autocompletar_sql.php',
      function() {
        if($('esp_desc_oa').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=subespecialidad&cadena='+encodeURIComponent($('esp_desc_oa').value)
        }

      }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad);

      ingreso_rut=function(datos_medico) {
      	$('doc_id_oa').value=datos_medico[3];
      	$('doc_rut_oa').value=datos_medico[1];
      	$('doc_nombre_oa').value=datos_medico[0].unescapeHTML();
      }

      autocompletar_medicos = new AutoComplete(
      'doc_nombre_oa', 
      '../../autocompletar_sql.php',
      function() {
        if($('doc_nombre_oa').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&nombre_medico='+encodeURIComponent($('doc_nombre_oa').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);







    seleccionar_inst1 = function(d) {
    
      $('inst_id_oa').value=d[0];
      $('inst_desc_oa').value=d[2].unescapeHTML();
    
    }
    
    autocompletar_institucion1 = new AutoComplete(
      'inst_desc_oa', 
      '../../autocompletar_sql.php',
      function() {
        if($('inst_desc_oa').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('inst_desc_oa').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst1);


    seleccionar_especialidad2 = function(d) {

      $('esp_id2_oa').value=d[0];
      $('esp_desc2_oa').value=d[2].unescapeHTML();

    }
    
    autocompletar_especialidades2 = new AutoComplete(
      'esp_desc2_oa', 
      '../../autocompletar_sql.php',
      function() {
        if($('esp_desc2_oa').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=especialidad_sigges&cadena='+encodeURIComponent($('esp_desc2_oa').value)
        }

      }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad2);

      ingreso_rut2=function(datos_medico) {
      	$('prof_id_oa').value=datos_medico[3];
      	$('prof_rut_oa').value=datos_medico[1];
      	$('prof_nombre_oa').value=datos_medico[0].unescapeHTML();
      }

      autocompletar_profesionales = new AutoComplete(
      'prof_nombre_oa', 
      '../../autocompletar_sql.php',
      function() {
        if($('prof_nombre_oa').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=profesionales&nombre_profesional='+encodeURIComponent($('prof_nombre_oa').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut2);



		cargar_oa();

	<?php } ?>

      ingreso_diagnosticos=function(datos_diag) {
      	
      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('nomd_diag_cod').value=cie10;
      	$('nomd_diagnostico').value=datos_diag[2].unescapeHTML();
      	
      }

      autocompletar_diagnosticos = new AutoComplete(
      	'nomd_diagnostico', 
      	'../../autocompletar_sql.php',
      function() {
        if($('nomd_diagnostico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('nomd_diagnostico').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos);


</script>
