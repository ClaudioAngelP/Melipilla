<?php 

	require_once('../../conectar_db.php');

	function iniciales($str) {
	$words = explode(" ", $str);
	$acronym = "";

	foreach ($words as $w) {
	  $acronym .= $w[0];
	} 
	
	return $acronym;
	}
	 $tipohtml = desplegar_opciones_sql("
    SELECT * FROM origen_paciente; 
  ",false);
	$ts=cargar_registros_obj("SELECT * FROM tipos_seguro ORDER BY ts_id;");

	$s=array();

	for($i=0;$i<sizeof($ts);$i++) {
		$s[$ts[$i]['ts_id']*1]=Array(htmlentities($ts[$i]['ts_nombre']),  htmlentities(iniciales($ts[$i]['ts_nombre'])));
	}
	
	$codigos=cargar_registros_obj("
		SELECT cp.codigo, cp.glosa, precio, transferen, copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, pago_fijo
		FROM codigos_prestacion AS cp
		ORDER BY codigo;
	", true);
	
	$codigos_conv=cargar_registros_obj("
		SELECT cp.codigo, cp.glosa, precio, transferen,copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, pago_fijo
		FROM codigos_prestacion_convenio AS cp
		ORDER BY codigo;
	", true);
	
	$cods=Array();
	
	for($i=0;$i<sizeof($codigos);$i++) {
		//$cods[($codigos[$i]['tipo'].''.$codigos[$i]['codigo'])]=$codigos[$i];
		$cods[($codigos[$i]['tipo'].''.$codigos[$i]['codigo'])]=$codigos[$i];
	}

	$cods_conv=Array();
	
	for($i=0;$i<sizeof($codigos_conv);$i++) {

		$cods_conv[($codigos_conv[$i]['tipo'].''.$codigos_conv[$i]['codigo'])]=$codigos_conv[$i];
	}
	
	$codigos2=cargar_registros_obj("
		SELECT dp_id, dp_valor FROM codigos_derecho_pabellon ORDER BY dp_valor desc;
	", true);
	
	$derpabs=Array();
	
	for($i=0;$i<sizeof($codigos2);$i++) {
		$derpabs[($codigos2[$i]['dp_id']*1)-1]=$codigos2[$i]['dp_valor']*1;
	}
	

	$func_id=$_SESSION['sgh_usuario_id']*1;
	$ac=cargar_registro("SELECT *, (ac_fecha_apertura::date<CURRENT_DATE) AS abierta FROM apertura_cajas WHERE func_id=$func_id AND ac_fecha_cierre IS NULL;");
	$valorizar=isset($_GET['valorizar']);
?>

<script>

<?php 

	if($ac) {
		if(substr($ac['ac_fecha_apertura'],0,10)!=date('d/m/Y')) {
			print("
			alert('ERROR GRAVE: \\n\\nDEBE REALIZAR CIERRE DE CAJA DE DIA ANTERIOR PENDIENTE. NO SE PUEDE RECAUDAR.'); 
			cambiar_pagina('ingresos/cierre_caja/form.php');
			</script>
			");
			
			exit();
		}
	}

?>

derpabs=<?php echo json_encode($derpabs); ?>;
codigos=<?php echo json_encode($cods); ?>;
codigos_conv=<?php echo json_encode($cods_conv); ?>;
utm=<?php echo $utm*1; ?>;
tipos_seguro=<?php echo json_encode($s); ?>;
interes=0;

var bloquear=0;

validacion_rut=function(obj) {

	obj.value=trim(obj.value);

	if( !comprobar_rut(obj.value) ) {
		obj.style.background='red';
		return false;
	} else {
		obj.style.background='yellowgreen';
		$('prev_id').value='';
		$('prev_id_or').value='';
		$('modalidad').value='mai';
		return true;	
	}

}


//NUEVO
	verificar_rut = function() {
    
    $('clirut').value=trim($('clirut').value);
	
	var str = $('clirut').value;
	var n = str.search("-"); 
	if(str.length>6){
	if(n*1==-1){
	
		//no existe guion 
		var res = str.slice(0,str.length-1);	
		var ver = str.slice(str.length-1,str.length);	
		$('clirut').value=res+'-'+ver;
	}
	}

	
	
      var texto = $('clirut').value;
      
      if(texto.charAt(0)=='R') {
        $('paciente_tipo_id').value=0;
        $('clirut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='P') {
        $('paciente_tipo_id').value=1;
        $('clirut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='I') {
        $('paciente_tipo_id').value=2;
        $('clirut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='F') {
        $('paciente_tipo_id').value=3;
        $('clirut').value=texto.substring(1,texto.length);
      }
      
      if($('paciente_tipo_id').value==0) {
      
        if(comprobar_rut($('clirut').value)) {
      
          $('clirut').style.background='inherit';
          validar_rut();
      
        } else {
		  alert('RUT INV&Aacute;LIDO'.unescapeHTML());
          $('clirut').style.background='red';
                
        }
        
      } else if($('paciente_tipo_id').value>0) {
      
          $('clirut').style.background='yellowgreen';
          validar_rut();
          
      }
      
    }
    
    busqueda_pacientes = function(objetivo, callback_func) {

      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-250;
  
      new_win =
      window.open('buscadores.php?tipo=pacientes', 'win_funcionarios',
        'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=500, height=300, '+
        'top='+top+', left='+left);
  
      new_win.objetivo_cod = objetivo;
      new_win.onCloseFunc = callback_func;
  
      new_win.focus();

    }
    
//FIN NUEVO

validacion_fecha = function(obj) {
		
	obj.value=trim(obj.value.replace(/-/gi,'/'));
	
	var fecha_ok=obj.value;
	
	switch(fecha_ok.length) {
		case 6:

			decada=(fecha_ok.charAt(4)+''+fecha_ok.charAt(5))*1;

			if(decada>50) 
				siglo='19';
			else
				siglo='20';
				
			fecha_ok=fecha_ok.charAt(0)+
						fecha_ok.charAt(1)+'/'+
						fecha_ok.charAt(2)+
						fecha_ok.charAt(3)+'/'+siglo+
						fecha_ok.charAt(4)+
						fecha_ok.charAt(5);
			break;						
		case 8:
			fecha_ok=fecha_ok.charAt(0)+
						fecha_ok.charAt(1)+'/'+
						fecha_ok.charAt(2)+
						fecha_ok.charAt(3)+'/'+
						fecha_ok.charAt(4)+
						fecha_ok.charAt(5)+
						fecha_ok.charAt(6)+
						fecha_ok.charAt(7);
			break;
	}
	
	if( !isDate(fecha_ok) ) {
		obj.style.background='red';
		return false;
	} else {
		obj.style.background='yellowgreen';
		obj.value=fecha_ok;
		return true;	
	}
		
		
}


validar_id = function(){
	
	var myAjax=new Ajax.Request('ingresos/datos_paciente_id.php',
	{
		method:'post',
		parameters: 'str='+encodeURIComponent($('cliid').value),
		onComplete: function(resp) 
		{
			
			try {
			
			d=resp.responseText.evalJSON(true);
			if(d){
			$edad_tipo='a';
			if(d['edad_anios']*1>=1){
					$('pac_edad').value=d['edad_anios']*1;	
					$edad_tipo='a';
				}else {
					if(d['edad_meses']*1<3){
						if(d['edad_meses']*1==0){
							$('pac_edad').value=d['edad_dias']*1;
							$edad_tipo='d';
						}else{
							$('pac_edad').value=d['edad_meses']*1;
							$edad_tipo='m';
						}
					 
					 	s=prompt('Ingrese el RUT del Padre/Madre: ','');		
					 }			
				}					
					
									
				
				
				$('pac_id').value=d['pac_id']*1;
				$('clirut').value=d['pac_rut'];
				$('cliedad').innerHTML='('+$('pac_edad').value+$edad_tipo+')';
				
				$('clipat').innerHTML=d['pac_appat'];
				$('climat').innerHTML=d['pac_apmat'];
				$('clinom').innerHTML=d['pac_nombres'];
								
				$('datos_titular').value=(d['pac_rut']+'|'+d['pac_nombres']+' '+d['pac_appat']+' '+d['pac_apmat']+'|'+d['pac_direccion']+', '+d['ciud_desc']+'|'+d['pac_fono']).unescapeHTML();
				
				
				//$('clidir').value=d['pac_direccion'].unescapeHTML();
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value=d['pac_fc_nac'].unescapeHTML();

				//$('prev_id').value=d['prev_id']*1;
				//$('prevision').value=d['prev_desc'].unescapeHTML();
				
				//if($('prevision').value=='') {
				//actualizar_prevision();
				//}
				$('prevision').readonly=true;
				
				if(d['pac_rut']!='')
					$('btn_sigges').disabled=false;
				else
					$('btn_sigges').disabled=true;
					
				cargar_prestaciones();
									
			} else {
			
				$('pac_id').value='';
				$('pac_edad').value='';
				$('clipat').innerHTML='';
				$('climat').innerHTML='';
				$('clinom').innerHTML='';
				$('datos_titular').value='';
				//$('clidir').value='';
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value='';
				$('cliid').focus();
				$('ver_func').hide();
				
				$('prevision').value='';
				$('prevision_or').value='';
				$('prevision').readonly=true;
				
				prestaciones=[];
				prestacionespab=[];
				alert('Paciente no encontrado.');
							
			}

			//validacion_rut($('clirut'));
			//validacion_fecha($('clifnac'));
			
			} catch(err) {
				alert(err);
			} 


		}	
	});

}

validar_rut = function() {

	if(bloquear) return;

	bloquear=1;
	
	$('clirut').value=$('clirut').value.toUpperCase();

	var myAjax=new Ajax.Request('ingresos/datos_paciente.php',
	{
		method:'post',
		parameters: 'str='+encodeURIComponent($('clirut').value)+'&tipo_rut='+encodeURIComponent($('paciente_tipo_id').value),
		onComplete: function(resp) {
			
			try {
			
			d=resp.responseText.evalJSON(true);
			
			if(d) {
			
				$('pac_id').value=d['pac_id']*1;
				$('pac_edad').value=d['edad_anios']*1;
				
				$('cliedad').innerHTML='('+$('pac_edad').value+'a)';
				
				$('clipat').innerHTML=d['pac_appat'];
				$('climat').innerHTML=d['pac_apmat'];
				$('clinom').innerHTML=d['pac_nombres'];
								
				$('datos_titular').value=(d['pac_rut']+'|'+d['pac_nombres']+' '+d['pac_appat']+' '+d['pac_apmat']+'|'+d['pac_direccion']+', '+d['ciud_desc']+'|'+d['pac_fono']).unescapeHTML();
				
				//$('clidir').value=d['pac_direccion'].unescapeHTML();
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value=d['pac_fc_nac'].unescapeHTML();

				//$('prev_id').value=d['prev_id']*1;
				//$('prevision').value=d['prev_desc'].unescapeHTML();
				
				//if($('prevision').value=='') {
				actualizar_prevision(1);
				
				//}
				$('prevision').readonly=true;
				
				if(d['pac_rut']!='')
					$('btn_sigges').disabled=false;
				else
					$('btn_sigges').disabled=true;
									
			} else {
				
				alert('Paciente no encontrado, debe crear al paciente en el menu de Consulta de datos del Paciente');
				actualizar_prevision(2);
				
				/*$('pac_id').value='';
				$('pac_edad').value='';
				$('clipat').innerHTML='';
				$('climat').innerHTML='';
				$('clinom').innerHTML='';
				$('datos_titular').value='';
				//$('clidir').value='';
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value='';
				$('clirut').focus();
				$('ver_func').hide();
				
				$('prevision').value='';
				$('prevision_or').value='';
				$('prevision').readonly=true;
				
				prestaciones=[];
				prestacionespab=[];*/
				
							
			}

			validacion_rut($('clirut'));
			//validacion_fecha($('clifnac'));
			
			} catch(err) {
				alert(err);
			} 

			bloquear=0;

		}	
	});

}

actualizar_prevision=function(num) {

	$('prevision').value='Cargando...';

	var myAjax=new Ajax.Request(
		'ingresos/certificar_paciente.php',
		{
			method:'post',parameters:'rut='+encodeURIComponent($('clirut').value)+'&tipo_rut='+encodeURIComponent($('paciente_tipo_id').value),

			onComplete:function(r) {
			
				var d=r.responseText.evalJSON(true);
				
				$('cargar_fonasa').hide();
			
				if(d) {
					
			
					$('prevision').focus();
					if(d['prais']!='000') {
									
						alert('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.');
					}
					
					if(d['error']=='1')
					{
					alert('FONASA devolvio ERROR para el RUT consultado y se ha cargado el ULTIMO tramo registrado.');
	
					}
					
					if(d['error']=='2')
					{
					alert('FONASA devolvio ERROR para el RUT consultado y se ha cargado el PRIMER tramo registarado.');
	
					}
					
					
					$('prev_id').value=d['prev_id']*1;
					
					if(d['prev_id']*1!=6){
						$('prevision').value=d['prev_desc'].unescapeHTML();
						$('prevision_or').value=d['prev_desc'].unescapeHTML();
					}else{
						$('prevision').value=d['desc'].unescapeHTML();
						$('prevision_or').value=d['desc'].unescapeHTML();
					}
					
					if(d['prev_id']*1==5){
						$('isapre_select').show();
						$('isapre_select').value='0';
						$('modalidad').value='mle';
						$('modalidad').disabled=true;
					}else{
						$('isapre_select').hide();
						$('isapre_select').value='0';
					}
					
					$('frec_id').value=d['frec_id']*1;	
						
						
					if(d['frec_id']*1>0) {
						$('ver_func').show();
					} else {
						$('ver_func').hide();
					}
					if(d['prais']!='000'){
						 $('prevision').value='PRAIS';
						 $('prevision_or').value='PRAIS';
						}
					
				cargar_prestaciones();

						
				} else {
				
					$('prev_id').value='';
					$('prev_id_or').value='';
					
					$('prevision').value='ERROR FONASA';
					$('prevision_or').value='ERROR FONASA';
					$('ver_func').hide();
					
				
				}
				
			}
		}
	);

}


redibujar_tabla=function() {
			calcular_totales();
			calcular_cuota();	
		
			var html='<table style="width:100%;font-size:10px;"><tr class="tabla_header">';
			html+='<td style="width:5%;">Modalidad</td><td style="width:12%;">Fecha</td>';
			html+='<td style="width:10%;">C&oacute;digo FONASA</td>';
			html+='<td>Descripci&oacute;n</td>';
			html+='<td style="width:3%;">Cant.</td>';
			html+='<td style="width:10%;">Valor</td>';
			html+='<td style="width:10%;">Copago</td>';
			html+='<td>Cobro</td>';
			html+='</tr>';
			
			
			
			if(derpab>0) {
			
				if(derpab<10){
					
				_derpab='0000'+derpab;
				prestacionespab[0]='DP'+_derpab;
				}else{
					 _derpab='000'+derpab;
					 prestacionespab[0]='DP'+_derpab;
					  
				}
				prestacionespab[1]='DERECHO A PABELL&Oacute;N - '+derpab;
				prestacionespab[2]=copago=Math.round(derpabs[derpab-1]*factor_pab);
				
				
			}
			
			if(derpab2>0) {
				
					if(derpab2<10){
						
					_derpab2='0000'+derpab2;
					prestacionespab[3]='DP'+_derpab2;
					}else{
						 _derpab2='000'+derpab2;
						 prestacionespab[3]='DP'+_derpab2;
						  
					}
					prestacionespab[4]='DERECHO A PABELL&Oacute;N - '+derpab2;
					prestacionespab[5]=copago=Math.round((derpabs[derpab2-1]/20)*factor_pab2)*10;
					
		
				}
			
			var suma=0;
			
			var ver_prop=false;			
			for(var i=0;i<prestaciones.length;i++) {
				
				p=prestaciones[i];
				
				/*if(codigos[p.codigo]!=undefined) 
					ttipo=codigos[p.codigo].tipo; 
				else 
					ttipo=$('modalidad').value;*/
					
				ttipo=p.modalidad;
				
				clase=(i%2==0)?'tabla_fila':'tabla_fila2';
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				html+='<td style="text-align:center;font-size:10px;">'+ttipo+'</td>';
				html+='<td style="text-align:center;font-size:10px;">'+p.fecha.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">'+p.codigo+'</td>';
				html+='<td style="font-size:10px;">'+p.glosa+'</td>';
				html+='<td style="text-align:right;">'+p.cantidad+'</td>';
				html+='<td style="text-align:right;">$'+number_format(p.precio*p.cantidad,0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(p.copago*p.cantidad,0,',','.')+'</td>';
				
				//html+='<td style="text-align:center;"><img src="iconos/delete.png" ';
				//html+=' style="cursor:pointer;" onClick="quitar_prod('+i+');"/></td>';
				
				html+='<td style="text-align:center;white-space:nowrap;">';
				html+='<select id="cobro_'+i+'" name="cobro_'+i+'" onChange="guardar_cobro('+i+');" style="font-size:9px;margin:0px;padding:0px;text-align:center;">';
				
				html+='<option value="S" '+(p.cobro=='S'?'SELECTED':'')+'>SI</option>';
				html+='<option value="N" '+(p.cobro=='N'?'SELECTED':'')+'>NO</option>';
				html+='<option value="GES" '+(p.cobro=='GES'?'SELECTED':'')+'>GES</option>';
				
				if(seguros.item!=undefined && seguros.item.length>0) {
					for(var s=0;s<seguros.item.length;s++) {
						var iniciales=tipos_seguro[seguros.item[s].tipo][1];
						html+='<option value="S'+s+'" '+(p.cobro=='S'+s?'SELECTED':'')+'>SEG.'+iniciales+'</option>';
					}
				}
				
				<?php /* if(_cax(325)) { ?>html+='<option value="MOD" '+(p.cobro=='MOD'?'SELECTED':'')+'>MOD.</option>';<?php } */?>
				html+='<option value="MOD" '+(p.cobro=='MOD'?'SELECTED':'')+'>MOD.</option>';
				
				
				html+='</select><img src="iconos/delete.png" style="cursor:pointer;width:12px;height:12px;" onClick="quitar_prod('+i+');" />'
				
				if(p.convenios!=undefined)
					html+='<img src="iconos/user_go.png" style="cursor:pointer;width:12px;height:12px;" onClick="convenio_prod('+i+');" />';
				
				html+='</td></tr>';				

				/*if(p.cobro=='S') 
					suma+=p.valor;*/

			}			

			if(derpab>0) {
				
				clase=(i++%2==0)?'tabla_fila':'tabla_fila2';
				
				if(derpab<10) _derpab='0000'+derpab;
				else _derpab='000'+derpab;
				
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				html+='<td style="text-align:center;font-size:10px;">'+$('modalidad').value+'</td>';
				html+='<td style="text-align:center;font-size:10px;">'+fecpab.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">DP'+_derpab+'</td>';
				html+='<td style="font-size:10px;color:gray;">DERECHO A PABELL&Oacute;N - '+derpab+'</td>';
				html+='<td style="text-align:right;">1</td>';
				html+='<td style="text-align:right;">$'+number_format(derpabs[derpab-1],0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(Math.round(derpabs[derpab-1]*factor_pab),0,',','.')+'</td>';
				html+='<td>&nbsp;</td></tr>';				
				
			}
			
			if(derpab2>0) {
				
				clase=(i++%2==0)?'tabla_fila':'tabla_fila2';
				
				if(derpab2<10) _derpab2='0000'+derpab2;
				else _derpab2='000'+derpab2;
				
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				html+='<td style="text-align:center;font-size:10px;">'+$('modalidad').value+'</td>';
				html+='<td style="text-align:center;font-size:10px;">'+fecpab2.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">DP'+_derpab2+'</td>';
				html+='<td style="font-size:10px;color:gray;">DERECHO A PABELL&Oacute;N - '+derpab2+'</td>';
				html+='<td style="text-align:right;">1</td>';
				html+='<td style="text-align:right;">$'+number_format(derpabs[derpab2-1],0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(Math.round((derpabs[derpab2-1]/20)*factor_pab2)*10,0,',','.')+'</td>';
				html+='<td>&nbsp;</td></tr>';				
				
			}
			
			html+='</table>';		
		
			$('lista_productos').innerHTML=html;
						
		}
		
		calcular_totales=function() {

			var suma_total=0;
			var suma=0;

			derpab=0; derpab2=0; fecpab='';fecpab=''; pabdental=false; pabconvenio=false; pabdental2=false; pabconvenio2=false;
			
			var prev_id=$('prev_id').value*1;
			var pac_edad=$('pac_edad').value*1;
			var cantPab=0;
			var asignapab=false;
			for(var i=0;i<prestaciones.length;i++) {
				var cod=codigos[$('modalidad').value+''+prestaciones[i].codigo];
				
				if(cod!=undefined) {
					if(cod.pab*1>0)
					{
						
						cantPab+=1;
						
					}
				}
			}
			
			for(var i=0;i<prestaciones.length;i++) {
				
				if(prestaciones[i].cobro!='S') continue;
				//alert(prestaciones[i].tipo_atencion);
				if(prestaciones[i].tipo_atencion=='PC052' || prestaciones[i].tipo_atencion=='PC053')
					{
						
						//alert(prestaciones[i].tipo_atencion);
						var cod=codigos_conv[$('modalidad').value+''+prestaciones[i].codigo];
				
						if(cod==undefined) continue;
					}else{
						
						var cod=codigos[$('modalidad').value+''+prestaciones[i].codigo];

					}
				if($('modalidad').value=='mle'||$('convenios_hosp').value==3 || $('convenios_hosp').value==4 || $('convenios_hosp').value==5 || $('convenios_hosp').value==6 || $('convenios_hosp').value==7 )
				{
					if(cod==undefined) cod=codigos['farmas'+prestaciones[i].codigo];
				}else{
				if(cod==undefined) cod=codigos['farmacia'+prestaciones[i].codigo];
				}
				if(cod==undefined) continue;
				
				if(cod.pab!='' && cod.pab!='00') {
					//alert('DEBE CANCELAR DERECHO A PABELLON ['+cod.pab+']');
					if($('modalidad').value=='mle' && ( $('pagare').value*1==1 || $('pagare').value*1==2 || $('pagare').value*1==0))
					{
						pab_id=2000;
					}
					
					
					if(cantPab>0) {
							if((cod.pab*1+pab_id)>derpab) {
								asignapab=true;
								if(derpab>0)
								{
									derpab2=derpab;
									fecpab2=fecpab;
									pabdental2=pabdental;
									pabconvenio2=pabconvenio;
								}
								
							derpab=(cod.pab*1)+pab_id;
							fecpab=prestaciones[i].fecha.substr(0,16);
									
							if(cod.codigo.substr(0,2)=='27') { 
								pabdental=true;
							} else {
								pabdental=false;
							}
							if(prestaciones[i].tipo_atencion=='PC052' || prestaciones[i].tipo_atencion=='PC053')
							{
								pabconvenio=true;
							
							}else{
								pabconvenio=false;
							}
								
							
							
						}
					}
					
				
				
					if(cantPab>1 && asignapab==false) {
						if((cod.pab*1+pab_id)>derpab2) {
						derpab2=(cod.pab*1)+pab_id;
						fecpab2=prestaciones[i].fecha.substr(0,16);
								
							if(cod.codigo.substr(0,2)=='27') { 
								pabdental2=true;
							} else {
								pabdental2=false;
							}
							if(prestaciones[i].tipo_atencion=='PC052' || prestaciones[i].tipo_atencion=='PC053')
							{
								pabconvenio2=true;
							
							}else{
								pabconvenio2=false;
							}
						}
					}
				}
				asignapab=false;
				suma_total+=Math.round(prestaciones[i].precio*prestaciones[i].cantidad);
				
				if($('pagare').value*1==1 || $('pagare').value*1==2 )
					suma+=Math.round(prestaciones[i].precio*prestaciones[i].cantidad);
				else
					suma+=Math.round(prestaciones[i].copago*prestaciones[i].cantidad);
				
			}
			
		
				
					if(!pabdental) {
							
							
						if(prev_id==1 || pac_edad>=60) {factor_pab=0; }
						else if(prev_id==2) {factor_pab=0; }
						else if(prev_id==3) {factor_pab=0.1; }
						else if(prev_id==4) {factor_pab=0.2; }
						else {factor_pab=1; }
						
					} else {
						if(prev_id==1 || pac_edad>=60) factor_pab=0;
						else if(prev_id==2) factor_pab=0.3; 
						else if(prev_id==3) factor_pab=0.5;
						else if(prev_id==4) factor_pab=0.8;
						else factor_pab=1;
					}
					
					if(!pabdental2) {
							
						if(prev_id==1 || pac_edad>=60) {factor_pab2=0; }
						else if(prev_id==2) {factor_pab2=0; }
						else if(prev_id==3){ factor_pab2=0.1; }
						else if(prev_id==4){ factor_pab2=0.2;}
						else{ factor_pab2=1; }
						
					} else {
						if(prev_id==1 || pac_edad>=60) factor_pab2=0;
						else if(prev_id==2) factor_pab2=0.3;
						else if(prev_id==3) factor_pab2=0.5;
						else if(prev_id==4) factor_pab2=0.8;
						else factor_pab2=1;
					}
					
				if($('modalidad').value=='mle')
				{
					
					factor_pab=1;
					factor_pab2=1;
				}
				
				
				if($('convenios_hosp').value==3 || $('convenios_hosp').value==4 || $('convenios_hosp').value==5 || $('convenios_hosp').value==6 || $('convenios_hosp').value==7  ) 
				{
				factor_pab=1;
				factor_pab2=1;
				}else{
					if($('convenios_hosp').value==9 || $('convenios_hosp').value==8){
						factor_pab=0;
						factor_pab2=0;
					}
				}
			
		if(pabconvenio) {factor_pab=1;}/*
				if(prev_id==1 || pac_edad>=60) factor_pab=1;
				else if(prev_id==2) factor_pab=0.6666;
				else if(prev_id==3) factor_pab=0.6666;
				else if(prev_id==4) factor_pab=0.6666;
				else factor_pab=1;
				//alert(prev_id);
			}*/

			
			if(derpab>0) {
				suma_total+=derpabs[derpab-1];
				if($('pagare').value*1==1 || $('pagare').value*1==2 )
					suma+=Math.round(derpabs[derpab-1]);
				else
					suma+=Math.round(derpabs[derpab-1]*factor_pab);
					
				if(derpab2>0) {
				suma_total+=derpabs[derpab2-1];
				if($('pagare').value*1==1 || $('pagare').value*1==2 )
					suma+=Math.round((derpabs[derpab2-1]/2));
				else
					suma+=Math.round((derpabs[derpab2-1]/20)*factor_pab2)*10;
					
				
				}
			}

			$('valor_total').innerHTML='$ '+number_format(suma_total,0,',','.')+'.-';
			$('valor_productos').innerHTML='$ '+number_format(suma,0,',','.')+'.-';
			$('proval').value=suma;
			//$('total_prods').innerHTML='$ '+number_format(suma,0,',','.')+'.-';
			var pagare_2=$('pagare').value*1;
			if(pagare_2==1 || pagare_2==2)
			{
				$('pie').value=0;
			//$('proval').value=0;
			}else{
				$('pie').value=suma;
			$('proval').value=suma;	
			}
		}
		
		calcular_pagare=function() {

			cargar_prestaciones();
		
			var pagare=$('pagare').value*1;
			var convenio=$('convenios_hosp').value*1;
			
			if(pagare==1 || pagare==2) {
				$('pie').value='';
				$('pie').readonly=true;
				$('btn_forma_pago').disabled=true;
				$('tr_pie').hide();
				$('cuonro').value='1';
				$('cuonro').readonly=true;
				$('titular_pagare').show();
			} else {
				$('pie').value='';
				$('pie').readonly=true;
				$('btn_forma_pago').disabled=false;			
				$('tr_pie').show();
				$('cuonro').readonly=false;
				$('titular_pagare').hide();
			}
			
			
			if(pagare==1 || pagare==2 || pagare==0){
				if($('modalidad').value=='mai')
				{
				$('prevision').value=$('prevision_or').value;
				pab_id=0;	
				}else{
					$('prevision').value=$('prevision_or').value;
				pab_id=2000;
				}
				
			}
			
			if(convenio==4)
			{
				$('prevision').value='DIPRECA';
				pab_id=1000;
			}
			
			if(convenio==8)
			{
				$('prevision').value='PRAIS';
				pab_id=0;
			}
			
			if(convenio==5)
			{
				$('prevision').value='FACH';
				pab_id=1000;
			}
			if(convenio==6)
			{
				$('prevision').value='GENDARMERIA';
				pab_id=1000;
			}
			if(convenio==7)
			{
				$('prevision').value='PDI';
				pab_id=1000;
			}
			if(convenio==3)
			{
				$('prevision').value='CAPREDENA';
				pab_id=1000;
			}
			
			if(convenio==9)
			{
				$('prevision').value='CARNET SANITARIO';
				pab_id=0;
			}
			
			calcular_totales();
			calcular_cuota();
		
		
		}
		
cargar_prestaciones=function() {
	$('prevision').value=$('prevision_or').value;
	if($('filtro_presta').value*1==3) {
		$('div_cuotas').show();
	} else {
		if($('filtro_presta').value*1==4) {
			$('isapre_select').hide();
			$('isapre_select').value='0';
			$('modalidad').hide();
			$('modalidad').value='mai';
			$('convenios_hosp').show(); 
			$('pagare').value='0';
		}else{
			if($('prevision_or').value=='ISAPRE'){
				$('isapre_select').show();
				$('isapre_select').value='0';
				$('modalidad').value='mle';
				$('modalidad').disabled=true;
				
			}
			$('div_cuotas').hide();	
			$('modalidad').show();
			$('convenios_hosp').hide();
			$('convenios_hosp').value='0';
		}
		
	}
	
	var myAjax=new Ajax.Request(
		'ingresos/prestaciones.php',
		{
			method:'post',
			parameters:$('pac_id').serialize()+'&'+$('filtro_presta').serialize()+'&'+$('prev_id').serialize()+'&'+$('modalidad').serialize()+'&'+$('frec_id').serialize()+'&'+$('pagare').serialize()+'&'+$('convenios_hosp').serialize(),
			onComplete:function(resp) {
				
				try {
					
					var datos=resp.responseText.evalJSON(true);
					
					if(datos) {
						prestaciones=datos;
					} else {
						prestaciones=[];
						prestacionespab=[];
					}
					redibujar_tabla();
				
				} catch(err) {
				
					alert(err);
					
				}
				
			}
		}
	);
	
}

calcular_cuota=function() {

	$('descuento').innerHTML='$ '+number_format($('total_descuento').value*1,0,',','.')+'.-';
	
	var proval=($('proval').value*1)-($('total_descuento').value*1);
	var pie=$('pie').value*1;
	var cuonro=$('cuonro').value*1;

	if(proval<=0) {
	
		$('pie').value='';
		$('pie').disabled=true;
		pie=0;

	} else {

		$('pie').disabled=false;

	}
	
	if(pie>=proval) {
		$('ingresa').value='Ingresar Recaudaci&oacute;n ... >>'.unescapeHTML();
		//$('cuotas0').style.display='none';
		//$('cuotas1').style.display='none';
		$('cuotas2').style.display='none';
		$('cuotas3').style.display='none';
		$('cuotas4').style.display='none';
	} else {
		if($('pagare').value*1==1 || $('pagare').value*1==2) {
		
			if($('pagare').value*1==1)
				$('ingresa').value='Ingresar Pagar&eacute; ... >>'.unescapeHTML();
			else if($('pagare').value*1==2)
				$('ingresa').value='Ingresar Garant&iacute;a ... >>'.unescapeHTML();
			
			//$('cuotas0').style.display='none';
			$('cuotas1').style.display='none';
			$('cuotas2').style.display='none';
			$('cuotas3').style.display='none';	
			$('cuotas4').style.display='none';	
		} else {
			$('ingresa').value='Ingresar Cr&eacute;dito ... >>'.unescapeHTML();
			//$('cuotas0').style.display='';
			$('cuotas1').style.display='';
			$('cuotas2').style.display='';
			$('cuotas3').style.display='';	
			$('cuotas4').style.display='';	
		}
	}	
	
	if(proval<=0 || cuonro==0) {
	
		$('valor_cuota').innerHTML='$ 0.-';
		$('total_interes').innerHTML='0%';
		$('saldo_credito').innerHTML='$ 0.-';

		if(proval<0) {
			$('afavor').style.display='';
			$('saldo_favor').innerHTML='$ '+number_format(-proval,0,',','.')+'.-';
		} else {
			$('afavor').style.display='none';
		}

	} else {

		$('afavor').style.display='none';

		var valcredito = ( proval - pie );
		
		if($('aplicaint').checked) {
			
			var inf=interes/100;
			var fact=Math.pow(1+inf,cuonro);
			
			var valcuota=Math.ceil(valcredito*((inf*fact)/(fact-1)));
			var valinteres=valcuota*cuonro;
			var tinteres=((valinteres/valcredito)-1)*100;
			
		} else {
			
			var valcuota=Math.ceil(valcredito/cuonro);
			var valinteres=valcuota*cuonro;
			var tinteres=0;
			
		}
		
		$('total_interes').innerHTML=number_format(tinteres,1,',','.')+'%';
		
		if( valcredito > 0 )
			$('saldo_credito').innerHTML='$ '+number_format(valcredito,0,',','.')+'.-';
		else
			$('saldo_credito').innerHTML='- $ '+number_format(-valcredito,0,',','.')+'.-';
			
		$('valor_credito').innerHTML='$ '+number_format(valinteres,0,',','.')+'.-';	
		$('valor_cuota').innerHTML='$ '+number_format(valcuota,0,',','.')+'.-';	
	
	}

}

imprimir_boletin=function(bolnum) {
	window.open('ingresos/imprimir_boletin.php?bolnum='+bolnum,'_blank');
}

ingresar_credito=function() {
	
	
	if($('btn_apertura_caja')!=undefined) {
		alert("Debe realizar su APERTURA DE CAJA para poder recaudar.");
		return;
	}
	
	
	if(prestaciones.length==0) {
		alert("El paciente no tiene prestaciones pendientes por recaudar.");
		return;
	}
	
	if($('pac_id').value*1==0) {
		alert( "Debe seleccionar paciente.".unescapeHTML() );
		return;
	}	
	
	if($('filtro_origen').value*1==0){
		alert( "Debe seleccionar Origen del paciente.".unescapeHTML() );
		return;
		
	}
	
	if($('prev_id').value*1==5){
	if($('isapre_select').value*1==0){
		alert( "Debe seleccionar la ISAPRE.".unescapeHTML() );
		return;
		
	}
	}

	if($('nbolnum').value!='' && !validacion_fecha($('nbolfec'))) {
		alert( "La fecha del comprobante no es v&aacute;lida.".unescapeHTML() );
		return;
	}	
	
	if(prestaciones.length>15) {
		alert( "La cantidad de prestaciones no puede ser mayor a 15.".unescapeHTML() );
		return;
	}
	/*if(!validacion_fecha($('clifnac'))) {
		alert( "La fecha de nacimiento no es v&aacute;lida.".unescapeHTML() );
		return;
	}*/	
	
	/*if(trim($('clirut').value)=='' ||
		trim($('clipat').value)=='' ||
		trim($('climat').value)=='' ||
		trim($('clinom').value)=='' ||
		trim($('clidir').value)=='') {
	
		alert( "Los datos del cliente est&aacute;n incompletos.".unescapeHTML() );	
		return;
	}*/

	var proval=($('proval').value*1)-($('total_descuento').value*1);

	if( (proval > 0 && ($('pie').value*1==0 && !($('pagare').value*1==1 || $('pagare').value*1==2)))) {
		alert( "Debe ingresar monto a cancelar no corresponde." );
		return;
	}
	
	if( ((proval - $('pie').value*1)<0 && !($('pagare').value*1==1 || $('pagare').value*1==2)) ) {
		alert( "Monto ingresado no es suficiente." );
		return;
	} 

	if( !($('pagare').value*1==1 || $('pagare').value*1==2) && ((proval - $('pie').value*1)>0 && ($('cuonro').value*1==0 || $('filtro_presta').value!='3') ) ) {
		alert( "Debe ingresar numero de cuotas para el saldo." );
		return;	
	}

	if(derpab>0) {

				if(derpab<10) _derpab='0000'+derpab;
				else _derpab='000'+derpab;
				
				var num=prestaciones.length;
				
				prestaciones[num]=new Object();
				prestaciones[num].presta_id=0;
				prestaciones[num].fecha=fecpab;
				prestaciones[num].codigo='DP'+_derpab;
				prestaciones[num].glosa='DERECHO A PABELL&Oacute;N - '+derpab;
				prestaciones[num].precio=Math.round(derpabs[derpab-1]);
				prestaciones[num].copago=Math.round(derpabs[derpab-1]*factor_pab);
				prestaciones[num].cantidad=1;
				prestaciones[num].cobro="S";
				
				if(derpab2>0) {

				if(derpab2<10) _derpab2='0000'+derpab2;
				else _derpab2='000'+derpab2;
				
					var num=prestaciones.length;
					prestaciones[num]=new Object();
					prestaciones[num].presta_id=0;
					prestaciones[num].fecha=fecpab2;
					prestaciones[num].codigo='DP'+_derpab2;
					prestaciones[num].glosa='DERECHO A PABELL&Oacute;N - '+derpab2;
					prestaciones[num].precio=Math.round(derpabs[derpab2-1]);
					prestaciones[num].copago=Math.round((derpabs[derpab2-1]/20)*factor_pab2)*10;
					prestaciones[num].cantidad=1;
					prestaciones[num].cobro="S";
			}
	
	}
	
	params=$('credito').serialize()+'&prestaciones='+encodeURIComponent(prestaciones.toJSON());
	
	if(seguros.item!=undefined)
		params+='&seguros='+encodeURIComponent(seguros.item.toJSON());
	else
		params+='&seguros=false';
	
	params+='&'+$('pac_id').serialize();
	
	$('ingresa').disabled=true;	
	
	var myAjax=new Ajax.Request(
	'creditos/sql_ingreso_credito.php',
	{
		method:'post',
		parameters: params,
		onComplete: function(resp) {
		
			try {		
		
				d=resp.responseText.evalJSON(true);
				imprimir_boletin(d[1]);
				cambiar_pagina('creditos/ingreso/form.php');
			
			} catch(err) {
			
				alert( 'ERROR:\n\n' + resp.responseText.unescapeHTML() );			
				$('ingresa').disabled=false;	
			
			}
		
		}	
	}	
	);


}

pago='';
seguros=new Object();

forma_pago=function() {

	var params='total='+($('pie').value*1)+'&prestaArray='+encodeURIComponent(prestaciones.toJSON())+'&derpabArray='+encodeURIComponent(prestacionespab.toJSON());
	

	l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-250;
        
    win = window.open('ingresos/forma_pago.php?'+params, 
                    '_forma_pago',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=490');
                    
    win.focus();


}

registro_seguros=function() {

    var params=$('pac_id').serialize();

    l=(screen.availWidth/2)-375;
    t=(screen.availHeight/2)-275;

    win = window.open('ingresos/registro_seguros.php?'+params,
                    '_forma_pago',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=750, height=550');

    win.focus();


}


datos_pagare=function() {

    var params=$('pac_id').serialize()+'&'+$('datos_titular').serialize();

    l=(screen.availWidth/2)-375;
    t=(screen.availHeight/2)-275;

    win = window.open('ingresos/registro_pagare.php?'+params,
                    '_forma_pago',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=750, height=550');

    win.focus();


}


definir_descuentos=function() {

	var params=($('bolnums').serialize()+'&'+$('total_descuento').serialize());

	 l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-250;
        
    win = window.open('creditos/ingreso/definir_descuentos.php?'+params, 
                    '_descuentos',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=490');
                    
    win.focus();

}

cambiar_prev=function(){

 pmanual = window.open('ingresos/modificar_prevision.php?prev_id='+$('prev_id').value,
				 'editar_prestacion', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=650,height=400,status=0,scrollbars=1');
					
pmanual.focus();
}

actualizar_prev_id=function(id,desc)
{
	$('prev_id_or').value=$('prev_id').value;
	$('prev_id').value=id;
	$('prevision_or').value=desc;
	$('prevision').value=desc;
	if(id==5||id==6)
	{
		$('modalidad').value='mle';
		$('modalidad').disabled=true;
	}else{
		$('modalidad').value='mai';
		$('modalidad').disabled=false;
	}
	
	if(id==5)
	{
		$('isapre_select').show(); 
	}else{
		$('isapre_select').hide(); 	
	}
	cargar_prestaciones();
}

ver_sigges=function() {
		
		 sigges = window.open('ficha_clinica/registro_sigges.php?rut='+encodeURIComponent($('clirut').value),
		 'sigges', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=650,height=400,status=0,scrollbars=1');
			
		 sigges.focus();
		
}

apertura_caja=function() {

	var myAjax=new Ajax.Request(
		'creditos/ingreso/sql_apertura.php',
		{
			method:'post',
			onComplete:function(r) {
				$('btn_apertura').innerHTML='';
			}
		}
	);

}


limpiar_recaudacion = function(){
	
	$('clirut').value='';
	$('pac_id').value='';
	$('pac_edad').value='';
	$('frec_id').value='';
	$('prev_id').value='';
	$('clipat').innerHTML='';
	$('climat').innerHTML='';
	$('clinom').innerHTML='';
	$('cliedad').innerHTML='';
	$('prevision').value='';
	$('prevision_or').VALUE='';
	$('td_cobertura').innerHTML='';
	$('filtro_presta').value='0';
	$('nboleta').value='';
	$('nbolnum').value='';
	$('lista_productos').innerHTML='';
	$('modalidad').value='mai';
	$('cod_presta2').value='';
	$('valor_presta').value='';
	$('cod_presta').value='';
	$('cant_presta').value='1';
	$('valor_total').innerHTML='$ 0.-'
	$('valor_productos').innerHTML='$ 0.-';
	$('pagare').checked=false;
	$('proval').value='0';
	$('descuento').innerHTML='$ 0.-';
	$('total_descuento').value='0';
	$('bolnums').value='';
	$('pie').value='';
	$('pago_efectivo').value='';
	$('pago_cheques').value='';
	$('pago_otras').value='';
	$('div_cuotas').hide();
	$('convenios_hosp').hide();
	$('isapre_select').hide(); 
}

guardar_cuenta=function() {

	if(derpab>0) {

                                if(derpab<10) _derpab='0000'+derpab;
                                else _derpab='000'+derpab;

                                var num=prestaciones.length;

                                prestaciones[num]=new Object();
                                prestaciones[num].presta_id=0;
                                prestaciones[num].fecha=fecpab;
                                prestaciones[num].codigo='DP'+_derpab;
                                prestaciones[num].glosa='DERECHO A PABELL&Oacute;N - '+derpab;
                                prestaciones[num].precio=Math.round(derpabs[derpab-1]);
                                prestaciones[num].copago=Math.round(derpabs[derpab-1]*factor_pab);
                                prestaciones[num].cantidad=1;
								prestaciones[num].modalidad='mle';
                                prestaciones[num].cobro="S";
                                
                            


        }
        
         if(derpab2>0) {

                                if(derpab2<10) _derpab2='0000'+derpab2;
                                else _derpab2='000'+derpab2;
   
                                var num=prestaciones.length;
                                prestaciones[num]=new Object();
                                prestaciones[num].presta_id=0;
                                prestaciones[num].fecha=fecpab2;
                                prestaciones[num].codigo='DP'+_derpab2;
                                prestaciones[num].glosa='DERECHO A PABELL&Oacute;N - '+derpab2;
                                prestaciones[num].precio=Math.round(derpabs[derpab2-1]);
                                prestaciones[num].copago=Math.round((derpabs[derpab2-1]/20)*factor_pab2)*10;
                                prestaciones[num].cantidad=1;
								prestaciones[num].modalidad='mle';
                                prestaciones[num].cobro="S";
                                
                               }

        params=$('credito').serialize()+'&prestaciones='+encodeURIComponent(prestaciones.toJSON());

        if(seguros.item!=undefined)
                params+='&seguros='+encodeURIComponent(seguros.item.toJSON());
        else
                params+='&seguros=false';

        params+='&'+$('pac_id').serialize()+'&'+$('hosp_id').serialize();

        $('ingresa').disabled=true;

        var myAjax=new Ajax.Request(
        'creditos/sql_guardar_cuenta.php',
        {
                method:'post',
                parameters: params,
                onComplete: function(resp) {
			alert('Cuenta guardada exitosamente');

			l=(screen.availWidth/2)-325;
			    t=(screen.availHeight/2)-250;

			 win = window.open('creditos/descargar_cuenta.php?'+$('hosp_id').serialize(),
                    		'_descuentos',
                    		'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    		'resizable=no, width=650, height=490');

    			win.focus();
		}
	});

}

abrir_cuenta=function() {

	var myAjax=new Ajax.Request(
                'ingresos/cuenta_corriente.php',
                {
                        method:'post',
                        parameters:$('hosp_id').serialize(),
                        onComplete:function(resp) {

                                try {

                                        var datos=resp.responseText.evalJSON(true);

                                        if(datos) {

						$('pac_id').value=datos[0].pac_id;

						if(datos[0].pac_rut!='') {
							$('clirut').value=datos[0].pac_rut;
							$('paciente_tipo_id').value='0';
						} else {
							$('clirut').value=datos[0].pac_ficha;
							$('paciente_tipo_id').value='3';
						}

						$('clipat').innerHTML=datos[0].pac_appat;
						$('climat').innerHTML=datos[0].pac_apmat;
						$('clinom').innerHTML=datos[0].pac_nombres;
						if(datos[0].prev_desc!=undefined) {
							$('prev_id').value=datos[0].prev_id;
							$('prevision').value=datos[0].prev_desc.unescapeHTML();
							$('prevision_or').value=datos[0].prev_desc.unescapeHTML();;
							$('modalidad').value=datos[0].bolmod;
						} else actualizar_prevision(1);

						$('pac_edad').value=datos[0].edad_anios*1;
		                                $('cliedad').innerHTML='('+$('pac_edad').value+'a)';

                                                prestaciones=datos[1];
                                        } else {
                                                prestaciones=[];
                                        }

                                        redibujar_tabla();

                                } catch(err) {

                                       // alert(err);

                                }

                        }
                }
        );	

}


</script>




<center>

<div class='sub-content' style='width:95%;'>

<form id='credito' name='credito' onSubmit='return false;'>

<input type='hidden' id='pac_id' name='pac_id' value='' />
<input type='hidden' id='pac_edad' name='pac_edad' value='' />
<input type='hidden' id='frec_id' name='frec_id' value='0' />
<input type='hidden' id='prev_id' name='prev_id' value='' />
<input type='hidden' id='prev_id_or' name='prev_id_or' value='' />
<input type='hidden' id='datos_titular' name='datos_titular' value='' />

<div class='sub-content'>
<table style='width:100%;font-size:12px;'><tr><td>
<img src='iconos/script.png'></td><td>
<?php if(!$valorizar) { ?><b>Recaudaci&oacute;n de Prestaciones</b> <?php } else { ?><b>Valorizaci&oacute;n de Prestaciones</b> <?php } ?>
</td>

<td id='btn_apertura'>
<?php 
if(!$valorizar) {
if(!$ac) {
?>
<input type='button' id='btn_apertura_caja' name='btn_apertura_caja' style='font-size:16px;color:red;border:2px solid yellow;' value='APERTURA CAJA' onClick='apertura_caja();' />
<?php } else { ?>
<?php 
if($ac['abierta']=='t') print('<span style="color:red;font-weight:bold;">');
echo 'APERTURA CAJA: '.substr($ac['ac_fecha_apertura'],0,16); 
if($ac['abierta']=='t') print('</span>');
?>
<?php } }  ?>
</td>

<td style='text-align:right;'>Cuenta Cte.:</td>
<td><input type='text' id='hosp_id' name='hosp_id' onKeyUp='if(event.which==13) abrir_cuenta();' /></td>

<td style='text-align:right;width:100px;<?php if($valorizar) echo "display:none;"; ?>'>
Nro. de Boleta:
</td>
<td <?php if($valorizar) echo "style='display:none;'"; ?>>
<input type='text' id='nboleta' name='nboleta'
style='text-align:center;display:none;' value='' size=10 />
<input type='text' id='nbolnum' name='nbolnum' style='display:none;'
style='text-align:center;' value='' size=10 />
</td>


<td style='text-align:right;width:100px;'>
Nro. de Boleta:
</td>
<td>
<input type='text' id='nboleta' name='nboleta'
style='text-align:center;' value='' size=10 />
<input type='text' id='nbolnum' name='nbolnum' style='display:none;'
style='text-align:center;' value='' size=10 />
</td>

<td style='text-align:right;<?php if($valorizar) echo "display:none;"; ?>'>
Fecha:
</td>

<td <?php if($valorizar) echo "style='display:none;'"; ?>>
<input type='text' id='nbolfec' name='nbolfec' style='text-align:center;' 
value='<?php echo date('d/m/Y'); ?>' onKeyUp='validacion_fecha(this);' size=10 />
</td>

</tr></table>
</div>

<div class='sub-content'>

<table style='width:100%;' cellpadding=1 cellspacing=0>
	
<tr>
	<td style='text-align:right;width:5%;'>Origen:</td>
<td style='width:5%;'>
<select id='filtro_origen' name='filtro_origen' style='font-size:12px;'>
	<?php echo $tipohtml; ?>
</select>
</td>
<td style='width:20px;'><center><img src='iconos/user.png' /></center></td>
<td style='width:20px; font-weight: bold;'>
<select id="paciente_tipo_id" name="paciente_tipo_id"
style="font-size:10px;" >
<option value=0 SELECTED>R.U.T.</option>
<option value=3>Nro. de Ficha</option>
<option value=1>Pasaporte</option>
<option value=2>COD. INT.</option>
</select>
</td><td style='width:20px;'><input type='text' id='clirut' name='clirut' size=11
style='text-align: center;font-size:13px;' onKeyUp='
if(event.which==13) { this.value=this.value.toUpperCase();
verificar_rut(); }
' maxlength=11 value='<?php echo $pac_rut; ?>'></td>
<td>&nbsp;
<img src='iconos/calendar.png' id='coberturas_paciente' onClick='ver_coberturas();'>
<img src='iconos/zoom_in.png' id='buscar_paciente'
onClick='
busqueda_pacientes("clirut", function() { verificar_rut(); });
'
onKeyUp="fix_bar(this);"
alt='Buscar Paciente...'
title='Buscar Paciente...'>
</td>
<td><input type='button' id='btn_sigges' name='btn_sigges' value='[SIGGES]' onClick='ver_sigges();' DISABLED /></td>
<td style='font-size:13px;'>
<span id='clipat' name='clipat' style='font-weight:bold;'></span>
<span id='climat' name='climat' style='font-weight:bold;'></span>
<span id='clinom' name='clinom' style='font-weight:bold;'></span>
<span id='cliedad' name='cliedad'></span>
</td>
<td style='text-align:right;width:5%;'>Previsi&oacute;n:</td>
<td style='font-weight:bold;width:10%;'>

<input type='text' id='prevision_or' name='prevision_or' HIDDEN />
<input type='text' id='prevision' name='prevision' READONLY 
onClick='cambiar_prev();' style='text-align:left;font-size:15px;color:blue;font-weight:bold;overflow:hidden;' size=20>
<img src='imagenes/ajax-loader1.gif' style='display:none;' id='cargar_fonasa' />
</td>

<td style='width:10%;'>
<select id='isapre_select' name='isapre_select'  style='font-size:12px; display:none;'>
<option value='0'>...</option>
<option value='1'>MASVIDA</option>
<option value='2'>VIDA TRES</option>
<option value='3'>BANMEDICA</option>
<option value='4'>CONSALUD</option>
<option value='5'>FERROSALUD</option>
<option value='6'>COLMENA</option>
<option value='7'>CRUZ BLANCA</option>
</select>
</td>
<td style='text-align:right;width:5%;'>Prestaciones:</td>
<td style='width:10%;'>
<select id='filtro_presta' name='filtro_presta' onChange='cargar_prestaciones();' style='font-size:12px;'>
<option value='0'>Solo Hoy</option>
<option value='1'>Hoy en Adelante</option>
<!---<option value='2'>Todas</option>--->
<option value='3'>Abonos</option>
<option value='4'>Convenios</option>
</select>
</td>

</tr>


</table>

</div>


<table style='width:100%;'>
<tr id='listado_prods'><td colspan=2>
<div class='sub-content2' style='height:<?php if($valorizar) echo '320'; else echo '160'; ?>px;overflow:auto;' id='lista_productos'>

</div>

<div class='sub-content' style='padding:0px;'>
<table style='width:100%;font-size:10px;' cellpadding=1 cellspacing=0>
<tr>
<td><center><img src='iconos/add.png'></center></td>
<td style='text-align:right;'>Prestaciones:</td>

<td style='text-align:right;width:100px;'>
Modalidad:
</td>
<td>
<select id='modalidad' name='modalidad' >
<option value='mai' SELECTED>INSTITUCIONAL</option>
<option value='mle'>PARTICULAR</option>
</select>
</td>

<td>
<select id='convenios_hosp' name='convenios_hosp' onChange='calcular_pagare();' style='display:none;'>
<option value='0'>(Seleccionar Convenio...)</option>
<option value='8'>PRAIS</option>
<option value='3'>CAPREDENA</option>
<option value='4'>DIPRECA</option>
<option value='5'>FACH</option>
<option value='6'>GENDARMERIA</option>
<option value='7'>PDI</option>
<option value='9'>Carnet Sanitario</option>
</select>
</td>

<td>
<input type='hidden' id='cod_presta2' name='cod_presta2' value='' />
<input type='hidden' id='valor_presta' name='valor_presta' value='' />
<input type='text' id='cod_presta' name='cod_presta' style='text-align:left;font-size:11px;' size=15 />
</td>
<td style='width:40%;'>
<input type='text' id='desc_presta' name='desc_presta' READONLY
style='text-align:left;width:100%;font-size:11px;' onDblClick='if(this.value!="") alert("C&Oacute;DIGO FONASA: ".unescapeHTML()+$("cod_presta2").value+"\n\nGLOSA:\n"+this.value);' />
</td>
<td>Cantidad:</td>
<td>
<input type='text' id='cant_presta' name='cant_presta' value='1' style='text-align:center;' size=5 onKeyUp='if(event.which==13) agregar_prestacion();' />
</td>
<td>
<input type='button' id='agrega_presta' name='agrega_presta' value='[Agregar...]' onClick='agregar_prestacion();' />
</td>
</tr>
</table>
</div>


</td></tr>

<tr id='datos_venta' <?php if($valorizar) echo "style='display:none;'"; ?>><td valign='top'>
<div class='sub-content'>
<img src='iconos/money.png'>
<b>Total a Pagar</b>
<span id='ver_func' style='color:red;font-weight:bold;display:none;'>(CONVENIO FUNCIONARIOS)</span>
</div>

<div class='sub-content'  <?php if($valorizar) echo "style='display:none;'"; ?>>
	



<table style='width:100%;' cellpadding=0 cellspacing=0><tr><td style='width:50%;'>
	



<table style='width:100%;' cellpadding=1 cellspacing=0>

<tr>
<td style='text-align:right;'>Valor Prestacion(es):</td>
<td colspan=3 id='valor_total'
style='font-size:18px;font-weight:bold;color:gray;'>$ 0.-</td>
</tr>

<tr>
<td style='text-align:right;'>Copago:</td>
<td colspan=3 id='valor_productos'
style='font-size:18px;font-weight:bold;color:blue;'>$ 0.-</td>
</tr>
<input type='hidden' id='proval' name='proval' value=0>

<tr style='display:none;'>
<td style='text-align:right;'>Descuento(s):</td>
<td colspan=2 id='descuento'
style='font-size:18px;font-weight:bold;color:red;'>$ 0.-</td>
<td>
<input type='button' style='font-size:11px;' 
onClick='definir_descuentos();' value='Definir...'>
</td>
</tr>
<input type='hidden' id='total_descuento' name='total_descuento' value=''>
<input type='hidden' id='bolnums' name='bolnums' value=''>


<tr>
<td style='text-align:right;'>Otras Opciones:</td>
<td colspan=2><select id='pagare' name='pagare' onChange='calcular_pagare();'>
<option value='0'>(Pago Normal...)</option>
<option value='1'>Pagar&eacute;</option>
<option value='2'>Garant&iacute;a</option>

</select></td>
<td>
<input type='button' id='titular_pagare' name='titular_pagare' 
style='display:none;' value='Datos del Titular...' onClick='datos_pagare();' />
</td>
</tr>

<tr id='tr_pie'>
<td style='text-align:right;'>Monto a Cancelar $:</td>
<td colspan=2><input type='text' id='pie' name='pie' 
onKeyUp='calcular_cuota();' size=10></td>
<td>
<input type='hidden' id='pago_efectivo' name='pago_efectivo' value=''>
<input type='hidden' id='pago_cheques' name='pago_cheques' value=''>
<input type='hidden' id='pago_otras' name='pago_otras' value=''>
<input type='button' style='font-size:11px;' id='btn_forma_pago' name='btn_forma_pago'
value='Forma de Pago...' onClick='forma_pago();'></td>
</tr>

<tr>
<td style='text-align:right;'>Seguros:</td>
<td colspan=4>
<input type='button' style='font-size:11px;' id='reg_seguros' name='reg_seguros' value='Registro de Seguros (0)' onClick='registro_seguros();'></td>
</tr>


</table>






</td><td style='width:50%;'>






<div id='div_cuotas' style='display:none;'>

<center>
<b><u>Cuotas de Prestaciones Odontol&oacute;gicas</u></b><br/><br/>

<table style='width:100%;' cellpadding=1 cellspacing=0>

<tr id='cuotas1' style=''>
<td style='text-align:right;'>Nro. de Cuotas:</td>
<td><input type='text' id='cuonro' name='cuonro' 
onKeyUp='calcular_cuota();' size=5 value="">
</td>
</tr>

<tr id='cuotas0' style='display:none;'>
<td style='text-align:right;'>Aplicar Inter&eacute;s:</td>
<td><input type='checkbox' id='aplicaint' name='aplicaint' 
onChange='calcular_cuota();' size=5>
</td>
<td>Inter&eacute;s:</td>
<td id='total_interes' style='text-align:center;font-weight:bold;'>0%</td>
</tr>

<tr id='cuotas4'>
<td style='text-align:right;'>D&iacute;a de Pago:</td>
<td>
<input type='text' id='diapago' name='diapago' 
size=5 value="<?php echo date('d'); ?>">
</td></tr>

<tr id='cuotas1'>
<td style='text-align:right;'>Saldo Cr&eacute;dito:</td>
<td id='saldo_credito' colspan=3 
style='font-size:18px;font-weight:bold;color:green;'>$ 0.-</td>
</tr>

<tr id='cuotas2'>
<td style='text-align:right;'>Valor Cr&eacute;dito:</td>
<td id='valor_credito' colspan=3 
style='font-size:18px;font-weight:bold;color:green;'>$ 0.-</td>
</tr>

<tr id='cuotas3'>
<td style='text-align:right;'>Valor Cuota:</td>
<td id='valor_cuota' colspan=3
style='font-size:18px;font-weight:bold;color:red;'>$ 0.-</td>
</tr>

<tr id='afavor' style='display:none;'>
<td style='text-align:right;'>Saldo a Favor:</td>
<td id='saldo_favor' colspan=3
style='font-size:18px;font-weight:bold;color:green;'>$ 0.-</td>
</tr>


</table>


</center>

</div>



</td></tr></table>



<center>
<input type='button' id='ingresa' name='ingresa'
onClick='ingresar_credito();'
value='Ingresar Recaudaci&oacute;n ... >>'>
</center>

</div>

</td></tr>

</table>

<?php if($valorizar) { ?>
<table style='width:100%;'>
<tr>
<td style='text-align:right;'>Valor Prestacion(es):</td>
<td colspan=3 id='valor_total2'
style='font-size:22px;font-weight:bold;color:gray;'>$ 0.-</td>
</tr>

<tr>
<td style='text-align:right;'>Copago:</td>
<td colspan=3 id='valor_productos2'
style='font-size:22px;font-weight:bold;color:blue;'>$ 0.-</td>
</tr>
<tr><td colspan=2>
<center>
<input type='button' id='ingresa' name='ingresa'
onClick='guardar_cuenta();'
value='Guardar Cuenta ... >>'>

<input type='button' id='limpiar' name='limpiar'
onClick='limpiar_recaudacion();'
value='(Limpiar Selecci&oacute;n...)'>
</center>
</td></tr></table>

<?php } ?>

</form>

</div>
</center>

<script> 

		prestaciones=[];
		prestacionespab=[];
		derpab=0;derpab2=0; fecpab='';fecpab2=''; factor_pab=1; factor_pab2=1;pab_id=0;

		/*seleccionar_comuna=function(d) {

			$('comdes').value=d[1].unescapeHTML();
			$('comcod').value=d[0]*1;				

		}*/
		//calcular_pagare();
		
					
		

		
		
		
		convenio_prod=function(n) {
			alert(("PRESTACI&Oacute;N EN CONVENIO CON PROFESIONAL:\n\n"+prestaciones[n].convenios).unescapeHTML());
		}

		guardar_cobro=function(n) {
		
			
			if($('cobro_'+n).value!='MOD')
				prestaciones[n].cobro=$('cobro_'+n).value;
			else {
				 pmanual = window.open('ingresos/modificar_prestacion.php?n='+n,
				 'editar_prestacion', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=650,height=400,status=0,scrollbars=1');
					
				 pmanual.focus();
				
			}
				
			if($('cobro_'+n).value=='N') {
				prestaciones=prestaciones.without(prestaciones[n]);
				redibujar_tabla();
			}
			
			calcular_totales();
			calcular_cuota();
		
		}
		
		quitar_prod=function(n) {
			prestaciones=prestaciones.without(prestaciones[n]);
			redibujar_tabla();
		}

	  
	  
    lista_prestaciones=function() {

        if($('cod_presta').value.length<3) return false;

        var params='tipo=prestacion&'+$('cod_presta').serialize()+'&'+$('modalidad').serialize();

        /*if($('auge').checked) {
          params='tipo=prestacion_patologia&pat_id=';
          params+=getRadioVal('i
		  nfo_prestacion','pat_id')+'&'+$('cod_presta').serialize();;
        }*/

        return {
          method: 'get',
          parameters: params
        }

    }
    
    seleccionar_prestacion = function(presta) {

      //$('codigo_prestacion').value=presta[0];
      //$('desc_presta').innerHTML='<center><b><u>Descripci&oacute;n de la Prestaci&oacute;n</u></b></center>'+presta[2];
	  $('cod_presta2').value=presta[0].unescapeHTML();
	  $('desc_presta').value=presta[2].unescapeHTML();
	  //$('valor_presta').value=presta[3].unescapeHTML();
	  $('cant_presta').value='1';
      $('cant_presta').select();
      $('cant_presta').focus();

    }
	
	info_codigo=function(codigo) {

		var cod;
	
		cod=codigos[$('modalidad').value+''+codigo];
		if(cod==undefined) cod=codigos['hsm'+codigo];
	
		if($('modalidad').value=='mle'||$('convenios_hosp').value==3 || $('convenios_hosp').value==4 || $('convenios_hosp').value==5 || $('convenios_hosp').value==6 || $('convenios_hosp').value==7 )
		{
			if(cod==undefined) cod=codigos['farmas'+codigo];
		}else{
			if(cod==undefined) cod=codigos['farmacia'+codigo];
		}
		
		return cod;
	
	}

	info_codigo2=function(codigo, modalidad) {

		var cod;
	
		cod=codigos[modalidad+''+codigo];
		if(cod==undefined) cod=codigos['hsm'+codigo];
		
		if($('modalidad').value=='mle'||$('convenios_hosp').value==3 || $('convenios_hosp').value==4 || $('convenios_hosp').value==5 || $('convenios_hosp').value==6 || $('convenios_hosp').value==7 )
		{
			if(cod==undefined) cod=codigos['farmas'+codigo];
		}else{
			if(cod==undefined) cod=codigos['farmacia'+codigo];
		}
		
		
		return cod;
	
	}

	
	calcular_precio = function(codigo, modalidad) {

		var prev_id=$('prev_id').value*1;
		var pac_edad=$('pac_edad').value*1;
		var total=0; var valor=0;
		
		
		var cod=info_codigo2(codigo, modalidad);
		if($('convenios_hosp').value!=3 && $('convenios_hosp').value!=4 && $('convenios_hosp').value!=5 && $('convenios_hosp').value!=6 && $('convenios_hosp').value!=7 && $('convenios_hosp').value!=8 && $('convenios_hosp').value!=9) {
		
			if(cod==undefined){ alert('Arancel no encontrado!');return [null,null];}
		}
		
		if(cod!=undefined){
			if( cod.pago_fijo=='t' ) {
				
				total=cod.precio*1;
				valor=cod.precio*1;
				
				return [total, valor];
			}
		} 
			
		
			if($('convenios_hosp').value==3 || $('convenios_hosp').value==4 || $('convenios_hosp').value==5 || $('convenios_hosp').value==6 || $('convenios_hosp').value==7 || $('convenios_hosp').value==8 || $('convenios_hosp').value==9) {
					if($('convenios_hosp').value==3 )//capedena copago d
					{
						cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) { valor=cod2.copago_d*1; total=cod2.copago_d*1; } else { alert('El c&oacute;digo seleccionado no es parte del convenio.'.unescapeHTML()); return[null,null]; }
					}
					
					if($('convenios_hosp').value==8 )//prais
					{
						cod2=info_codigo2(codigo, 'mai'); if(cod2!=undefined) { valor=cod2.copago_a*1; total=cod2.precio*1; } else { alert('El c&oacute;digo seleccionado no es parte del convenio.'.unescapeHTML()); return[null,null]; }

					}
					
					if($('convenios_hosp').value==9 )//carnet sanitario
					{
						cod2=info_codigo2(codigo, 'mai'); if(cod2!=undefined) { valor=cod2.copago_a*1; total=cod2.precio*1; } else { alert('El c&oacute;digo seleccionado no es parte del convenio.'.unescapeHTML()); return[null,null]; }

					}
					
					if($('convenios_hosp').value==4 || $('convenios_hosp').value==5 || $('convenios_hosp').value==6 || $('convenios_hosp').value==7)//dipreca copago c
					{
						cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) { valor=cod2.copago_c*1; total=cod2.copago_c*1; } else { alert('El c&oacute;digo seleccionado no es parte del convenio.'.unescapeHTML()); return[null,null];}

					}
		
			} else if($('modalidad').value=='mai') {
				total=cod.precio*1;
			
				if($('frec_id').value*1>0) {
				
					if(prev_id>=1 && prev_id<=4) valor=cod.copago_b*1;
					else if(prev_id==5) { cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) valor=cod.precio*1; else valor=cod.precio*1 }
					else if(prev_id==6) { cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) valor=cod2.copago_b*1; else valor=cod.precio*1 }
					else valor=cod.precio*1;
				
				} else {
			
					if(prev_id==6) valor=cod.precio*1;
					else if(prev_id==5) {  cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) { total=cod2.precio*1; valor=cod2.precio*1; } else { valor=cod.precio*1 }  }
					else if(prev_id>=1 && prev_id<=4 && pac_edad>=60) valor=0;
					else if(prev_id==1) valor=cod.copago_a*1;
					else if(prev_id==2) valor=cod.copago_b*1;
					else if(prev_id==3) valor=cod.copago_c*1;
					else if(prev_id==4) valor=cod.copago_d*1;
					else valor=cod.precio*1;
					
				}
			
			} else {

				if($('frec_id').value*1>0) {

					cod2=info_codigo2(codigo, 'mai'); if(cod2!=undefined) { total=cod2.precio*1; valor=cod2.precio*1; } else { total=cod.precio*1; valor=cod.precio*1; }
				
				} else {
			
					if(prev_id>=1 && prev_id<=4) { total=cod.transferen*1; valor=cod.transferen*1; }
					else if(prev_id==5) { total=cod.transferen*1; valor=cod.transferen*1; }
					else { total=cod.transferen*1; valor=cod.transferen*1; }
					
				}
			
			}
			
		
		
		return [total, valor];
	
	}
	  
	agregar_prestacion = function() {

		var num=prestaciones.length;
		
		/*var valores=$('valor_presta').value.split('|');
		var prev_id=$('prev_id').value*1;
		var pac_edad=$('pac_edad').value*1;*/
		var tmp_valor=calcular_precio($('cod_presta2').value, $('modalidad').value);
		//alert(prev_id+'= '+valor);
		
		if(tmp_valor[0]==null){ return;}
		prestaciones[num]=new Object();
		prestaciones[num].fecha='<?php echo date('d/m/Y H:i:s'); ?>';
		prestaciones[num].codigo=$('cod_presta2').value;
		prestaciones[num].glosa=($('desc_presta').value);
		prestaciones[num].cantidad=($('cant_presta').value*1);
		prestaciones[num].precio=tmp_valor[0];
		prestaciones[num].copago=tmp_valor[1];
		prestaciones[num].presta_id=0;
		prestaciones[num].cobro='S';
		prestaciones[num].tipo=$('modalidad').value;
		prestaciones[num].modalidad=$('modalidad').value;
		prestaciones[num].id_padre=-1;
		try {
		
			//console.log($('modalidad').value+''+$('cod_presta2').value);
			
			var cod=info_codigo($('cod_presta2').value);

			if(cod!=undefined && cod.canasta!='') {
			
				//console.log(codigos[$('modalidad').value+''+$('cod_presta2').value]);
				
				_num=num;
				
				if(cod.canasta.indexOf('x')>=0) {
					var tmp=cod.canasta.split('x');
					codigo=tmp[0];
					cantidad=tmp[1];
				} else {
					codigo=cod.canasta;
					cantidad='1';
				}

				var tmp=info_codigo(codigo);
				
				if(tmp!=undefined) {
				
					var tmp_valor=calcular_precio(codigo, $('modalidad').value);
				
					num=prestaciones.length;
				
					prestaciones[num]=new Object();
					prestaciones[num].fecha='<?php echo date('d/m/Y H:i:s'); ?>';
					prestaciones[num].codigo=tmp.codigo;
					prestaciones[num].glosa=tmp.glosa;
					prestaciones[num].cantidad=cantidad;
					prestaciones[num].precio=tmp_valor[0];
					prestaciones[num].copago=tmp_valor[1];
					prestaciones[num].presta_id=0;
					prestaciones[num].cobro='S';
					prestaciones[num].tipo=$('modalidad').value;
					prestaciones[num].modalidad=$('modalidad').value;
					prestaciones[num].id_padre=_num;
					
				}

			
			}
		
		} catch(err) {
		
			//console.log(err);
		
		}

		redibujar_tabla();

		  $('cod_presta2').value='';
		  $('desc_presta').value='';
		  //$('valor_presta').value='';
		  $('cant_presta').value='1';
		  $('cod_presta').select();
		  $('cod_presta').focus();
		
		
		
		
	}
	  
	  
	  
	  
    autocompletar_prestaciones = new AutoComplete(
      'cod_presta', 
      'autocompletar_sql.php',
      lista_prestaciones, 'autocomplete', 350, 100, 150, 1, 2, seleccionar_prestacion);
	  
	  
	  
	  
	  
	validacion_rut($('clirut'));
	//validacion_fecha($('clifnac'));

</script>
