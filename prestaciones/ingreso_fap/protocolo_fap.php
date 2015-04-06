<?php

	require_once('../../conectar_db.php');
	
	$fap_id=$_GET['fap_id']*1;
	$ub=$_GET['ub']*1;

		$fap=cargar_registro("
			SELECT *, 
			fap_fecha::date AS fap_fecha,
			date_trunc('seconds',fap_fecha)::time AS fap_hora,
		    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	 		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	 		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias, fap_protocolo,
			COALESCE((
			SELECT hosp_id FROM hospitalizacion WHERE hosp_pac_id=pac_id AND hosp_fecha_egr IS NULL ORDER BY hosp_id DESC LIMIT 1 
			)::text,'(No encontrado...)') AS cta_cte
			FROM fap_pabellon
			LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id
			JOIN pacientes USING (pac_id)
			LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
			LEFT JOIN diagnosticos ON diag_cod=fap_diag_cod 
			LEFT JOIN clasifica_camas AS cc1 ON fap_pabellon.centro_ruta=cc1.tcama_id::text

			WHERE fap_id=$fap_id
		", true);
		
		$edad='';
      
		if($fap['edad_anios']*1>1) $edad.=$fap['edad_anios'].' a ';
		elseif($fap['edad_anios']*1==1) $edad.=$fap['edad_anios'].' a ';

		if($fap['edad_meses']*1>1) $edad.=$fap['edad_meses'].' m ';	
		elseif($fap['edad_meses']*1==1) $edad.=$fap['edad_meses'].' m ';

		if($fap['edad_dias']*1>1) $edad.=$fap['edad_dias'].' d';
		elseif($fap['edad_dias']*1==1) $edad.=$fap['edad_dias'].' d';


		$pr=cargar_registros_obj("SELECT *, (SELECT glosa FROM codigos_prestacion_recaudacion WHERE codigo=fappr_codigo LIMIT 1) AS glosa FROM fap_prestacion WHERE fap_id=$fap_id ORDER BY fappr_id", true);
		
		$tipoheridahtml = desplegar_opciones("fappab_tipo_herida", 
			"fapth_id, '[' || fapth_id || '] ' || fapth_desc",$fap['fapth_id'],'true','ORDER BY fapth_id'); 

		$tipoanestesia1html = desplegar_opciones("fappab_tipo_anestesia", 
			"fapta_id, fapta_desc",$fap['fapta_id1']*1,'true','ORDER BY fapta_id'); 

		$tipoanestesia2html = desplegar_opciones("fappab_tipo_anestesia", 
			"fapta_id, fapta_desc",$fap['fapta_id2']*1,'true','ORDER BY fapta_id'); 

	$presta=cargar_registros_obj("
                SELECT *, (SELECT glosa FROM codigos_prestacion_recaudacion WHERE fappr_codigo=codigo LIMIT 1) AS glosa FROM fap_prestacion
                WHERE fap_id=$fap_id ORDER BY fappr_id ASC
        ", true);
		
	
	$prestaciones=array();	
	
	for($i=0;$i<sizeof($presta);$i++) {
	
		$n=sizeof($prestaciones);
		
		$prestaciones[$n]->codigo=$presta[$i]['fappr_codigo'];	
		$prestaciones[$n]->desc=$presta[$i]['glosa'];	
		$prestaciones[$n]->cantidad=$presta[$i]['fappr_cantidad'];	
		$prestaciones[$n]->fappr_tipo=$presta[$i]['fappr_tipo'];	
		
	}
	
	$s=cargar_registros_obj("SELECT * FROM fap_suspension ORDER BY faps_id;", true);
	
	$suspensionhtml='';
	
	for($i=0;$i<sizeof($s);$i++) {
		$t=$s[$i]['faps_nombre'];
		if($fap['fap_suspension']==$t) $sel='SELECTED'; else $sel=''; 
		//$sel=(htmlentities($fap['fap_suspension'])==$t)?'SELECTED':'';
		$style=($s[$i]['faps_titulo']=='t')?'font-weight:bold;':'';
		
		$suspensionhtml.='<option value="'.$t.'" style="'.$style.'" '.$sel.'>'.$t.'</option>';
	}

?>

<html>
<title>Protocolo Quir&uacute;rgico</title>

<?php cabecera_popup('../..'); ?>

<script>


<?php if($presta) { ?>

	presta=<?php echo json_encode($prestaciones); ?>;
	
	<?php } else { ?>
	
	presta=[];	
	
	<?php } ?>

 suspendido = <?php echo json_encode($fap['fap_suspension']); ?>	
	
 guardar_informe = function() {
	 
	if(suspendido==''){
                if($('informe').value=='') {
                        alert("Debe ingresar el cuerpo del mensaje.");
                        return false;
                }
                
                if(presta.length==0){
					alert("Debe ingresar a lo menos una prestaci&oacute;n".unescapeHTML());
					return;
				}
				
				if($('fapta_id1').value=='-2') {
                        alert("Debe seleccionar la anestesia Principal");
                        return false;
                }
                
				if($('fapta_id1').value=='-2') {
                        alert("Debe seleccionar la anestesia Principal");
                        return false;
                }
                
                
				if($('fap_diagnostico_1').value=='') {
                        alert("Debe seleccionar un Diagn&oacute;stico Post.".unescapeHTML());
                        return false;
                }
	}

                var params=$('datos').serialize()+'&presta='+encodeURIComponent(presta.toJSON());
                //params+='&html='+encodeURIComponent(tinyMCE.activeEditor.getContent());
				//params+='&'+$('informe').serialize();
				//params+='&'+$('informe2').serialize();
				//params+='&'+$('indicaciones').serialize();
				//params+='&'+$('indicaciones2').serialize();

                var myAjax=new Ajax.Request(
                        'sql_protocolo.php',
                        {
                                method:'post',
                                parameters: params,
                                onComplete:function() {
                                        alert('Informe guardado exitosamente.');
  
										var fn = window.opener.abrir_fap(<?php echo $fap_id.',',$ub; ?>,0);
										fn();
                                        window.close();
                                        tinyMCE.activeEditor.destroy();
                                }
                        }
                );

        }
		
imprimir_informe = function() {
	
	var str = '';
	if(presta.length==0){
		str+="- Seleccionar a lo menos una prestaci&oacute;n\n".unescapeHTML();
	}
	
	if($('fap_diagnostico_1').value=='') {
        str+="- Seleccionar Diagn&oacute;stico Post.\n".unescapeHTML();
    }

	if($('fapta_id1').value=='-2') {
        str+="- Seleccionar la Anestesia Principal\n";
    }
    
    if($('informe').value=='') {
        str+="- Ingresar Descripci&oacute;n de la Operaci&oacute;n .".unescapeHTML();
    }
    
    if(suspendido=='')
		if(str!=''){
			alert('Debe llenar los siguientes campos:\n'+str+'\n\nAseg&uacute;rese Guardar antes de imprimir'.unescapeHTML());
			return;
		}
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-400;

      new_win = 
      window.open('imprimir_protocolo_fap.php?fap_id=<?php echo $fap_id; ?>',
      'win_protocolo', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=800, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();


}

agregar_prestacion = function() {
    	
			var codigo=$('cod_presta').value;
			var desc_presta=$('desc_presta').value;
			var cant=$('cantidad').value;

			if($('tipo').value!=5) {

				var num=presta.length;
				presta[num]=new Object();
				presta[num].codigo=codigo;
				presta[num].desc=desc_presta;
				presta[num].cantidad=cant;

				listar_prestaciones();
				
				$('cod_presta').value='';		
				$('cod_presta').select();
				$('cod_presta').focus();
						
				$('cambia_presta').value=1;

				
			} else {
				
				for(var a=0;a<presta.length;a++) {
					if(presta[a].codigo==codigo){
						alert("la prestaci&oacute;n ya existe, s&oacute;lo debe modificar la cantidad".unescapeHTML());
						return;
					}
				}
			
				var myAjax=new Ajax.Request(
					'definir_tipo_prestacion.php',
					{
						method:'post',
						parameters:$('pac_id').serialize()+'&codigo='+encodeURIComponent(codigo),
						onComplete:function(r) {

							var valor=r.responseText;
							
							try{
							var num=presta.length;
							presta[num]=new Object();
							presta[num].codigo=codigo;
							presta[num].desc=desc_presta;
							presta[num].cantidad=cant;
							presta[num].fappr_tipo=valor;
						}catch(e){
							
							alert(e);
							}
							
							listar_prestaciones();
							
							$('cod_presta').value='';
							$('cod_presta').select();
							$('cod_presta').focus();
							
							$('cambia_presta').value=1;
							
						}
					}
				);
			
			}

    }
    
    listar_prestaciones=function() {
    
		var html='<table style="width:100%;font-size:8px;"><tr class="tabla_header"><td>C&oacute;digo</td><td>Cant.</td><td>Descripci&oacute;n</td>';
		
		if($('tipo').value*1==5) 
			html+='<td>Tipo</td>';    
    
		html+='<td>Eliminar</td></tr>';    
    
		for(var i=0;i<presta.length;i++) {
			
			clase=(i%2==0)?'tabla_fila':'tabla_fila2';
	
			if(presta[i].desc.length>37) 
				var descr=presta[i].desc.substr(0,37)+'...';
			else
				var descr=presta[i].desc;	
		
			html+='<tr class="'+clase+'" ';
			html+='onMouseOver="this.className=\'mouse_over\';" ';
			html+='onMouseOut="this.className=\''+clase+'\';">';
			html+='<td style="text-align:center;">'+presta[i].codigo+'</td><td style="text-align:center;"><input type="text" name="cant_'+i+'" id="cant_'+i+'" value="'+presta[i].cantidad+'" onKeyUp="if(event.which==13) editar_cantidad('+i+')" onDblClick="this.readOnly=false;" size=5 style="text-align:center;" ReadOnly></td><td>'+descr+'</td>';
			
			if($('tipo').value*1==5) 
				html+='<td style="text-align:center;font-size:10px;font-weight:bold;">'+presta[i].fappr_tipo+'</td>';
			
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
    
   editar_cantidad = function(id){
		presta[id].cantidad=$('cant_'+id).value*1;
		$('cant_'+id).readOnly=true;
		listar_prestaciones();
		$('cambia_presta').value=1;
	}
    
    
    lista_prestaciones=function() {

        if($('cod_presta').value.length<3) return false;

        var params='tipo=prestacion&'+$('cod_presta').serialize()+'&farm=1';

        /*if($('auge').checked) {
          params='tipo=prestacion_patologia&pat_id=';
          params+=getRadioVal('info_prestacion','pat_id')+'&'+$('cod_presta').serialize();;
        }*/

        return {
          method: 'get',
          parameters: params
        }

    }
    
</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/layout_edit.png' />
<b>Protocolo Quir&uacute;rgico</b>
</div>

<form id='datos' name='datos' 
action='sql_generar.php' method='post' onSubmit='return false;'>
<input type='hidden' id='pac_id' name='pac_id' value='<?php echo $fap['pac_id']; ?>' />
<input type='hidden' id='fap_id' name='fap_id' value='<?php if($fap) echo $fap['fap_id']; else echo '-1'; ?>' />
<input type='hidden' id='tipo' name='tipo' value='5' />
<input type='hidden' id='prev_id' name='prev_id' value='<?php if($fap) echo $fap['prev_id']; else echo '-1'; ?>' />
<input type='hidden' id='ciud_id' name='ciud_id' value='<?php if($fap) echo $fap['ciud_id']; else echo '-1'; ?>' />
<input type='hidden' id='cambia_presta' name='cambia_presta' value='0' />

<div class='sub-content'>

<table style='width:100%;'>
<tr>
<td class='tabla_fila2'  style='text-align:right;width:25%;'>Ficha Cl&iacute;nica:</td>
<td class='tabla_fila' style='font-weight:bold;font-size:16px;' id='pac_ficha'>
<?php if($fap) echo $fap['pac_ficha']; else echo ''; ?>
</td>
<td class='tabla_fila2' style='text-align:right;'>
Nro. Folio:
</td><td class='tabla_fila' style='font-weight:bold;font-size:16px;'>
<?php if($fap) echo $fap['fap_fnumero']; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>R.U.N.:</td>
<td class='tabla_fila' style='font-weight:bold;font-size:16px;' id='pac_rut'>
<?php if($fap) echo formato_rut($fap['pac_rut']); else echo ''; ?>
</td>
<td class='tabla_fila2' style='text-align:right;'>
Cuenta Corriente:
</td><td class='tabla_fila' style='font-weight:bold;font-size:16px;'>
<?php if($fap) echo ($fap['cta_cte']); else echo ''; ?>
</td>
</tr>

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
<?php echo $fap['fapp_desc']; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha/Hora:</td>
<td class='tabla_fila' id='fecha_hora' style='text-align:center;font-weight:bold;'>
<?php if($fap) echo $fap['fap_fecha']; else echo date('d/m/Y'); ?> 
<?php if($fap) echo $fap['fap_hora']; else echo date('H:i'); ?>
</td>
<td class='tabla_fila2'  style='text-align:right;'>Servicio de Or&iacute;gen:</td>
<td class='tabla_fila'>
<input type='hidden' id='centro_ruta' name='centro_ruta' value='<?php if($fap) echo trim($fap['centro_ruta']); else echo ''; ?>' />
<?php echo trim($fap['tcama_tipo']); ?>
</td>
</tr>


<tr>
<td class='tabla_fila2'  style='text-align:right;'>Diagn&oacute;stico Pre.:</td>
<td class='tabla_fila' colspan=3>
<?php if($fap) echo $fap['fap_diag_cod'].' '.$fap['fap_diagnostico']; ?></td>
</tr>
<tr><td colspan=2>
<?php 

/*if($pr) {

print("<table style='width:100%;'><tr class='tabla_header'><td>#</td><td>C&oacute;digo</td><td>Descripci&oacute;n</td></tr>");

for($i=0;$i<sizeof($pr);$i++) {

print("<tr><td style='text-align:right;font-size:20px;'>".($i+1)."</td><td style='text-align:center;font-weight:bold;'>".$pr[$i]['fappr_codigo']."</td>");
print("<td>".$pr[$i]['glosa']."</td></tr>");


}

print("</table>");

}*/

?>

	<div class='sub-content2' id='lista_presta' 
	style='height:150px;overflow:auto;'>
	</div>

<div class='sub-content' id='agrega_presta'>
<table style='width:100%;' cellpadding=0 cellspacing=0>
<tr><td style='width:15px;'>
<center>
<img src='../../iconos/add.png' />
</center>
</td><td style='width:100px;text-align:right;'>
<!--<select id='modalidad' name='modalidad'>
<option value='mai'>MAI</option>
<option value='mle'>MLE</option>
</select>-->
Agregar Prest.:</td>
<td>
<input type='hidden' id='desc_presta' name='desc_presta' value='' />
<input type='text' id='cod_presta' name='cod_presta' size=10 />
</td><td style='text-align:right;display:none;'>
Cant.:
</td><td>
<input type='text' id='cantidad' name='cantidad' style='display:none;'
onKeyUp='if(event.which==13) agregar_prestacion();' size=3 />
</td></tr>
</table>
</td><td colspan=2>
<table style='width:100%;'>
	<?php 
		for($i=1;$i<4;$i++) { 
?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Cod. Diag. Post. (<?php echo $i; ?>):</td>
<td class='tabla_fila' colspan=3>

<input type='text' id='fap_diag_cod_<?php echo $i; ?>' name="fap_diag_cod_<?php echo $i; ?>"
value='<?php echo $fap['fap_diag_cod_'.$i]; ?>' READONLY size=5 style='font-weight:bold;text-align:center;' />
<input type='text' id='fap_diagnostico_<?php echo $i; ?>' name='fap_diagnostico_<?php echo $i; ?>' 
value='<?php echo htmlentities($fap['fap_diagnostico_'.$i]); ?>' onDblClick='this.value=""; $("fap_diag_cod_<?php echo $i; ?>").value="";' size=35>


</td>
</tr>

<?php 
		}
	
?>
</table>
</td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>
Suspensi&oacute;n de FAP:
</td><td class='tabla_fila'>
<select id='fap_suspension' name='fap_suspension' style='width:300px;'>
<option value=''><i>(No ha sido suspendido...)</i></option>
<?php echo $suspensionhtml; ?>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Tipo de Herida:
</td><td class='tabla_fila' colspan=3>
<select id='fapth_id' name='fapth_id'>
<?php echo $tipoheridahtml; ?>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Anestesia Principal:
</td><td class='tabla_fila' colspan=3>
<select id='fapta_id1' name='fapta_id1'>
<option value='-2'>(SIN DATO)</option>
<?php echo $tipoanestesia1html; ?>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Anestesia Secundaria:
</td><td class='tabla_fila' colspan=3>
<select id='fapta_id2' name='fapta_id2'>
<option value='-2'>(SIN DATO)</option>
<?php echo $tipoanestesia2html; ?>
</select>
</td></tr>

<!---

<tr style='display:none;'><td style='text-align:right;' class='tabla_fila2'>
Evaluaci&oacute;n Pre Anest&eacute;sica:
</td><td class='tabla_fila'>
<select id='fap_eval_pre' name='fap_eval_pre'>
<option value='-2' <?php echo (($fap['fap_eval_pre']*1)==-2?'SELECTED':''); ?>>(SIN DATO)</option>
<option value='1' <?php echo (($fap['fap_eval_pre']*1)==1?'SELECTED':''); ?>>SI</option>
<option value='0' <?php echo (($fap['fap_eval_pre']*1)==0?'SELECTED':''); ?>>NO</option>
</select>
</td></tr>


<tr style='display:none;'><td style='text-align:right;' class='tabla_fila2'>
Entrega Anestesista:
</td><td class='tabla_fila'>
<select id='fap_entrega_ane' name='fap_entrega_ane'>
<option value='1' <?php echo (($fap['fap_entrega_ane']*1)==1?'SELECTED':''); ?>>Si</option>
<option value='0' <?php echo (($fap['fap_entrega_ane']*1)!=1?'SELECTED':''); ?>>No</option>
</select>
</td></tr>

<tr style='display:none;'><td style='text-align:right;' class='tabla_fila2'>
E.V.A.:
</td><td class='tabla_fila'>
<select id='fap_eva' name='fap_eva'>
<?php 
	for($i=0;$i<11;$i++) {
		echo '<option value="'.$i.'" '.(($i==$fap['fap_eva']*1)?'SELECTED':'').'>'.$i.'</option>';
	}
?>
</select>
</td></tr>


<tr style='display:none;'><td style='text-align:right;' class='tabla_fila2'>
Nro. Hoja de Insumos: 
</td><td class='tabla_fila'>
<input id='fap_hoja_cargo' name='fap_hoja_cargo' 
style='text-align:center;' value='<?php echo $fap['fap_hoja_cargo']; ?>' />
</td></tr>


--->

<tr><td style='text-align:right;' class='tabla_fila2'>
Biopsia:
</td><td class='tabla_fila' colspan=3>
<select id='fap_biopsia' name='fap_biopsia'>
<option value='-2' <?php echo (($fap['fap_biopsia']*1)==-2?'SELECTED':''); ?>>(SIN DATO)</option>
<option value='1' <?php echo (($fap['fap_biopsia']*1)==1?'SELECTED':''); ?>>RAPIDA</option>
<option value='2' <?php echo (($fap['fap_biopsia']*1)==2?'SELECTED':''); ?>>DIFERIDA</option>
<option value='3' <?php echo (($fap['fap_biopsia']*1)==3?'SELECTED':''); ?>>AMBAS</option>
<option value='0' <?php echo (($fap['fap_biopsia']*1)==0?'SELECTED':''); ?>>NO</option>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Recuento de Compresas Conforme:
</td><td class='tabla_fila' colspan=3>
<select id='fap_entrega_ane' name='fap_entrega_ane'>
<option value='1' <?php echo (($fap['fap_entrega_ane']*1)==1 || ($fap['fap_entrega_ane']*1)==-1?'SELECTED':''); ?>>Si</option>
<option value='0' <?php echo (($fap['fap_entrega_ane']*1)==0?'SELECTED':''); ?>>No</option>
<option value='2' <?php echo (($fap['fap_entrega_ane']*1)==2?'SELECTED':''); ?>>No Corresponde</option>
</select>
</td></tr>

<tr class='tabla_header'><td colspan=4>
Hallazgos Intraoperatorios
</td></tr>

<tr><td colspan=4>
<textarea id='informe2' name='informe2' style='width:100%;height:100px;font-size:18px;'><?php echo $fap['fap_hallazgos']; ?></textarea>
</td></tr>


<tr class='tabla_header'><td colspan=4>
Descripci&oacute;n Operaci&oacute;n
</td></tr>

<tr><td colspan=4>
<textarea id='informe' name='informe' style='width:100%;height:250px;font-size:18px;'><?php echo $fap['fap_protocolo']; ?></textarea>
</td></tr>

<tr class='tabla_header'><td colspan=4>
Indicaciones
</td></tr>

<tr><td colspan=4>
<textarea id='indicaciones' name='indicaciones' style='width:100%;height:100px;font-size:18px;'><?php echo $fap['fap_indicaciones']; ?></textarea>
</td></tr>

<tr class='tabla_header'><td colspan=4>
Indicaciones Anestesia
</td></tr>

<tr><td colspan=4>
<textarea id='indicaciones2' name='indicaciones2' style='width:100%;height:100px;font-size:18px;'><?php echo $fap['fap_indicaciones_anestesia']; ?></textarea>
</td></tr>



<tr><td colspan=4>
<center><br />
<input type='button' id='boton_generar' 
onClick='guardar_informe();' value='-- Guardar Protocolo IQ... --' />

<input type='button' id='boton_imprimir' 
onClick='imprimir_informe();' value='-- Imprimir Protocolo IQ... --' />

<br /></center>
</td></tr>

</table>
</div>


</form>

</body>
</html>

<script>

	listar_prestaciones();

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
    
    seleccionar_diagnostico1 = function(datos_diag) {

      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('fap_diag_cod_1').value=cie10;
      	$('fap_diagnostico_1').value=datos_diag[2].unescapeHTML();

    }

   seleccionar_diagnostico2 = function(datos_diag) {

      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('fap_diag_cod_2').value=cie10;
      	$('fap_diagnostico_2').value=datos_diag[2].unescapeHTML();

    }

   seleccionar_diagnostico3 = function(datos_diag) {

      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('fap_diag_cod_3').value=cie10;
      	$('fap_diagnostico_3').value=datos_diag[2].unescapeHTML();

    }
	
	seleccionar_prestacion = function(presta) {

      //$('codigo_prestacion').value=presta[0];
      //$('desc_presta').innerHTML='<center><b><u>Descripci&oacute;n de la Prestaci&oacute;n</u></b></center>'+presta[2];
	  $('desc_presta').value=presta[2];
	  $('cantidad').value='1';
	  agregar_prestacion();
      $('desc_presta').select();
      $('desc_presta').focus();

    }
    /*
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
      */
      
      
      autocompletar_diagnostico1 = new AutoComplete(
      'fap_diagnostico_1', 
      '../../autocompletar_sql.php',
      function() {
        if($('fap_diagnostico_1').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('fap_diagnostico_1').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico1);

    autocompletar_diagnostico2 = new AutoComplete(
      'fap_diagnostico_2', 
      '../../autocompletar_sql.php',
      function() {
        if($('fap_diagnostico_2').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('fap_diagnostico_2').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico2);

    autocompletar_diagnostico3 = new AutoComplete(
      'fap_diagnostico_3', 
      '../../autocompletar_sql.php',
      function() {
        if($('fap_diagnostico_3').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('fap_diagnostico_3').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico3);
      
     autocompletar_prestaciones = new AutoComplete(
      'cod_presta', 
      '../../autocompletar_sql.php',
      lista_prestaciones, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_prestacion);

    autocompletar_centro = new AutoComplete(
    'centro_nombre', 
    '../../autocompletar_sql.php',
    function() {
    if($('centro_nombre').value.length<2) return false;
    return {
    method: 'get',
    parameters: 'tipo=centros_pabellon&cadena='+encodeURIComponent($('centro_nombre').value)
    }
    }, 'autocomplete', 150, 200, 150, 2, 3, seleccionar_centro);


	buscar_paciente=function() {
	
		$('paciente').disabled=true;	
	
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
	//setTimeout("$('pac_rut').focus()",200);

</script>
