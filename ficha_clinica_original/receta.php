<?php
    require_once('../conectar_db.php');
    if(isset($_GET['cheque']))
    {
        $cheque = 1; 
        $med_mostrar='med_control_corto';
        $funcion_meds='buscar_medicamentos_controlados';
    }
    else
    {
        $cheque = 0;
        $med_mostrar='med_corto';
        $funcion_meds='buscar_medicamentos';
    }
    if(isset($_GET['directo']))
    {
        $directo=true;
    }
      
    $tipos_recetas = pg_query($conn, "
    SELECT tipotalonario_id, COALESCE(tipotalonario_adquiriente, false), COALESCE(tipotalonario_funcionario, 0), art_id FROM receta_tipo_talonario;
    ");
      
    $_tipo='';
      
    for($a=0;$a<pg_num_rows($tipos_recetas);$a++)
    {
        $_tiporec = pg_fetch_row($tipos_recetas);
        $_tipo .= '<input type="hidden" id="tiporeceta_'.$_tiporec[0].'" name="tiporeceta_'.$_tiporec[0].'" value="'.$_tiporec[1].''.$_tiporec[2].'">';
        $_tipo .= '<input type="hidden" id="art_receta_'.$_tiporec[0].'" name="art_receta_'.$_tiporec[0].'" value="'.$_tiporec[3].'">';
    }
      
    $paciente = ($_GET['paciente']*1);
      
    //$centroshtml = desplegar_opciones("centro_costo", "centro_ruta, centro_nombre, length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel, centro_medica", '1', 'centro_medica AND centro_nivel=2',	'ORDER BY centro_ruta');
      
    $servicioshtml = desplegar_opciones("centro_costo", "centro_ruta, centro_nombre, length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel, centro_medica", '1', "centro_medica",	'ORDER BY centro_ruta');
      
    $talonarioshtml = desplegar_opciones("receta_tipo_talonario", "tipotalonario_id, tipotalonario_nombre", '1', '1=1', 'ORDER BY tipotalonario_id');
      
    $tmp=cargar_registro("SELECT COUNT(*) AS cantidad FROM receta WHERE receta_paciente_id=$paciente");
      
    if($tmp)
        $nrec=$tmp['cantidad']*1;
    else
        $nrec=0;
    
?>
<script>
    cargar_ultima_receta=function()
    {
        var myAjax=new Ajax.Request('ficha_clinica/cargar_ultima_receta.php',
        {
            method:'post',
            parameters:'pac_id=<?php echo $paciente; ?>&'+$('bodega_id').serialize(),
            onComplete:function(datos)
            {
                var r=datos.responseText.evalJSON(true);
		$('rut_medico').value=r.doc_rut;
		$('nombre_medico').value=r.doc_paterno+' '+r.doc_materno+' '+r.doc_nombres;
		$('centro_servicio').value=r.receta_centro_ruta;
		medicamentos=new Array();
		conta_med=0;
		for(var i=0;i<r.detalle.length;i++)
                {
                    medicamentos[conta_med]=new Array();
                    medicamentos[conta_med][0]=r.detalle[i].art_id;
                    medicamentos[conta_med][1]=r.detalle[i].recetad_cant;
                    medicamentos[conta_med][2]=r.detalle[i].recetad_horas;
                    medicamentos[conta_med][3]=r.detalle[i].recetad_dias;
                    medicamentos[conta_med][4]=r.detalle[i].art_codigo;
                    medicamentos[conta_med][5]=r.detalle[i].art_glosa;
                    medicamentos[conta_med][6]=r.detalle[i].stock;
                    //medicamentos[conta_med][7]=($('articulo_control').value*1);
                    medicamentos[conta_med][8]=r.detalle[i].art_unidad_cantidad_adm;
                    medicamentos[conta_med][9]=r.detalle[i].art_unidad_adm;
                    medicamentos[conta_med][10]=r.detalle[i].forma_nombre;
                    totalua = Math.ceil(1*((medicamentos[i][3]*24))/(medicamentos[i][2])*(medicamentos[i][1])/(medicamentos[i][8]*1));
                    conta_med++;
                }
                redibujar_tabla();
		calcular_totales();
            }
	});
    }
      
    var medicamentos=new Array();
    var conta_med=0;
    bloquear_ingreso=false;
    receta_control=<?php echo $cheque; ?>;
    mostrar_opciones_receta = function()
    {
        if(receta_control==1)
        {
            var tipo_receta = $('tipo_talonario').value;
            var opciones_talonario = $('tiporeceta_'+tipo_receta).value;
            var art_talonario = $('art_receta_'+tipo_receta).value;
            if(opciones_talonario.charAt(0)=='t')
            {
                $('adquiriente').style.display='';
            }
            else
            {
                $('adquiriente').style.display='none';
            }
            if(art_talonario==-1)
            {
                $('nro_receta').disabled=true;
                $('nro_receta').value='';
            }
            else
            {
                $('nro_receta').disabled=false;
                //$('nro_receta').value='';
            }
        }
        else
        {
            $('adquiriente').style.display='none';
        }
        
            for(var n=0;n<medicamentos.length;n++)
            {
                if(medicamentos[n]!=null)
                    if(tipo_receta!=medicamentos[n][7])
                        medicamentos[n]=null;
            }
        
        redibujar_tabla();
    }
      
    comprobar_rut_adquiriente = function ()
    {
        adq_rut_obj = $('adq_rut');
        adq_rut_obj.value=trim(adq_rut_obj.value);
        if(comprobar_rut(adq_rut_obj.value))
        {
            adq_rut_obj.style.background='';
            $('adq_rut_correcto').value=1;
            var myAjax=new Ajax.Request('ficha_clinica/datos_adquiriente.php',
            {
                method:'post',
                parameters: $('adq_rut').serialize(),
                onComplete:function(r)
                {
                    try 
                    {
                        var datos=r.responseText.evalJSON(true);
                        if(datos.adq_nombres!=undefined)
                        {
                            $('adq_nombres').value=datos.adq_nombres;
                            $('adq_appat').value=datos.adq_appat;
                            $('adq_apmat').value=datos.adq_apmat;
                            $('adq_direccion').value=datos.adq_direccion;
                            $('codigo').focus();
                        }
                        else
                        {
                            $('adq_nombres').value='';
                            $('adq_appat').value='';
                            $('adq_apmat').value='';
                            $('adq_direccion').value='';
                            $('adq_nombres').focus();
			}
                    }
                    catch(err)
                    {
                        alert(err);
                    }
                }
            });
        }
        else
        {
            adq_rut_obj.style.background='yellow';
            $('adq_rut_correcto').value=0;
        }
    }
      
      setear_adquiriente = function() {
      
        retira = $('adq_retira').value;
        
        if(retira==0) {
        
          $('adq_rut').value=$('paciente_rut').value;
          $('adq_appat').value=$('paciente_paterno').value;
          $('adq_apmat').value=$('paciente_materno').value;
          $('adq_nombres').value=$('paciente_nombre').value;
          $('adq_direccion').value=$('paciente_dire').value;
          $('adq_rut_correcto').value=1;
          
          $('adq_rut').disabled=true;
          $('adq_appat').disabled=true;
          $('adq_apmat').disabled=true;
          $('adq_nombres').disabled=true;
          $('adq_direccion').disabled=true;
        
        } else {
        
          $('adq_rut').value='';
          $('adq_appat').value='';
          $('adq_apmat').value='';
          $('adq_nombres').value='';
          $('adq_direccion').value='';
          $('adq_rut_correcto').value=0;
          
          $('adq_rut').disabled=false;
          $('adq_appat').disabled=false;
          $('adq_apmat').disabled=false;
          $('adq_nombres').disabled=false;
          $('adq_direccion').disabled=false;
        
        }
      
      }
      

      agregar_art = function() {
      
          if(!$('articulo_id')) {
            
            alert('Seleccione Medicamento y Dosis.');
            $('codigo').select();
            return;
      
          }
      
          if($('articulo_id').value=='' || ($('total').innerHTML*1)==0) {
            
            alert('Seleccione Medicamento y Dosis.');
            $('codigo').select();
            return;
      
          }
          
          codigo = $('codigo').value;
          nombre = $('detalle_prod').innerHTML;
          
          medicamentos[conta_med]=new Array();
          medicamentos[conta_med][0]=$('articulo_id').value;
          
          if($('art_tipo_adm').value*1!=2) {
			  medicamentos[conta_med][1]=$('cant').value;
			  medicamentos[conta_med][2]=$('horas').value;
			  medicamentos[conta_med][3]=$('dias').value;
		  } else {
			  medicamentos[conta_med][1]=($('cant1').value*1)+($('cant2').value*1)+($('cant3').value*1);
			  if(!$('cronica').checked)
				medicamentos[conta_med][2]=24;
			  else
				medicamentos[conta_med][2]=1;
			  medicamentos[conta_med][3]=$('dias').value;			  
		  }
          
          medicamentos[conta_med][4]=codigo;
          medicamentos[conta_med][5]=nombre;
          medicamentos[conta_med][6]=($('articulo_stock').value*1);
          medicamentos[conta_med][7]=($('articulo_control').value*1);

          medicamentos[conta_med][8]=($('relacion_ua').value*1);
          medicamentos[conta_med][9]=($('unidad_nombre').innerHTML);
          medicamentos[conta_med][10]=($('unidad_ua').innerHTML);
          
          medicamentos[conta_med][11]=($('art_tipo_adm').value*1);
          
          if($('art_tipo_adm').value*1!=2) {
			  medicamentos[conta_med][12]=0;
			  medicamentos[conta_med][13]=0;
			  medicamentos[conta_med][14]=0;
		  } else {
			  medicamentos[conta_med][12]=$('cant1').value*1;
			  medicamentos[conta_med][13]=$('cant2').value*1;
			  medicamentos[conta_med][14]=$('cant3').value*1;
		  }
			
			
          //if(receta_control) {
			  // DEBE ACTIVARSE CUANDO LOS MEDICAMENTOS
			  // ESTEN CLASIFICADOS COMO PSIC/BENZO...
			  // SOLUCION DE PARCHE... 02/05/2011
              //$('tipo_talonario').value=$('articulo_control').value;
              //mostrar_opciones_receta();
          //}

          redibujar_tabla();
          
          $('lista_medicamentos').scrollTop=
                                  $('lista_medicamentos').scrollHeight;
          
          conta_med++;
          
          $('articulo_id').value='';
          
          $('cant').value='';
          
          $('cant1').value='';
          $('cant2').value='';
          $('cant3').value='';
          
          $('horas').value='';
          $('dias').value='';
          
          $('detalle_prod').innerHTML='';
          
          calcular_cant();
          
          $('codigo').value='';
          $('codigo').focus();
          
      }
    	
      
      abrir_articulo = function(d) {
      
      if($('codigo').value=='') return;
      
      for(i=0;i<=conta_med;i++) {
      
              if(medicamentos[i]==null) continue;
              
              if(medicamentos[i][4]==$('codigo').value) {
                $('cant').value=medicamentos[i][1];
                $('horas').value=medicamentos[i][2];
                $('dias').value=medicamentos[i][3];
                
                calcular_cant();
                medicamentos[i]=null;
                redibujar_tabla();
                break;
              }
              
      }
      
      if($('articulo_id')) $('articulo_id').value='';
      
      $('detalle_prod').innerHTML='<img src="imagenes/ajax-loader1.gif">';
      
			/*var myAjax = new Ajax.Updater(
				'detalle_prod', 
				'mostrar.php', 
				{
					method: 'get', 
					evalScripts: true,
					parameters: 'tipo=<?php echo $med_mostrar?>&'+$('codigo').serialize()+'&'+$('bodega_id').serialize(),
					onComplete: function(respuesta) {
            
						$('cant').select();
				    
				}
        
			}
			
			);*/
			
			var myAjax=new Ajax.Request(
			'ficha_clinica/datos_medicamento.php',
			{
					method:'post',
					parameters:'art_id='+d[5]+'&bod_id='+($('bodega_id').value*1)+'&pac_id='+($('paciente_id').value*1),
					onComplete:function(r) {
						
						try {
						
							var d=r.responseText.evalJSON(true);
							
							$('articulo_id').value=d.art_id;

							$('detalle_prod').innerHTML=d.art_glosa;

							$('unidad_nombre').innerHTML=d.art_unidad_administracion;
							$('unidad_ua').innerHTML=d.forma_nombre;
							$('relacion_ua').value=d.art_unidad_cantidad_adm;
							
							$('art_tipo_adm').value=d.art_tipo_adm;
							
							//d.dias<=30 && 
							
							if(d.dias_trans<=30 && d.dias>d.dias_trans ) {
								$('art_despacho').show();
								$('art_despacho').innerHTML='<img src="iconos/error.png"> El dia <b>'+d.log_fecha+'</b> se despach&oacute; <b>'+d.despacho+' '+d.forma_nombre+'</b> para <b>'+Math.floor(d.dias)+'</b> d&iacute;as; se acaba en <b><u>'+(Math.floor(d.dias)-d.dias_trans*1)+' d&iacute;as m&aacute;s</u></b>.';
								alert('El dia '+d.log_fecha+' se despacho '+d.despacho+' '+d.forma_nombre+' para '+Math.floor(d.dias)+' dias; se acaba en '+(Math.floor(d.dias)-d.dias_trans*1)+' dias mas');
							} else {
								$('art_despacho').hide();								
							}
							
							
							if(d.art_tipo_adm==2) {
								// INSULINAS
								$('cant_insulina').show();
								$('cant_normal').hide();
							} else {
								// NORMAL
								$('cant_insulina').hide();
								$('cant_normal').show();
							}
							
							$('articulo_stock').value=d.stock*1;
							//$('campo_stock').innerHTML='Saldo: <b>'+d.stock+'</b>';
							
							//$('campo_tipo_adm').value=d.art_tipo_adm;
							
							$('cant').focus();
													
						} catch(err) {
						
							alert(err);
						
						}
						
					}
				});

      
      }
      
      calcular_cant = function() {
      
          if($('art_tipo_adm').value*1==2)
			var cantidad=($('cant1').value*1)+($('cant2').value*1)+($('cant3').value*1);
		  else
        	var cantidad=$('cant').value;
		  
		  if($('art_tipo_adm').value*1==2) {
			
			if($('cronica').checked) {
				var horas=24;
			} else {
				var horas=24;
			}
		  
		  } else  {
			  
			if($('horas').value*1<1) {
				$('horas').value="";
			}
          
        	var horas=$('horas').value*1;
        	
          }
		  
          if(receta_control==0 && $('cronica').checked) 
            valor = Math.ceil(((($('dias').value*30))/horas*(cantidad))/($('relacion_ua').value*1));
          else
            valor=Math.ceil((($('dias').value*24))/horas*(cantidad)/($('relacion_ua').value*1));
    
          if(!isNaN(valor) || ($('horas').value*1)>0) {
            $('total').innerHTML=valor;
          } else {
            $('total').innerHTML='0';
          }
          
      
      }
      
      
      redibujar_tabla= function() {
    
      tabla_html='<table width=100% id=\"seleccion\"><tr class=\"tabla_header\" style=\"font-weight: bold;\"> <td>C&oacute;digo Int.</td><td>Nombre</td><td>Cant.</td><td>U.A.</td><td>Acci&oacute;n</td></tr>';    
    
      for(i=0;i<=conta_med;i++) {
      
          if(medicamentos[i]==null) continue;
          
          codigo=medicamentos[i][4];
          nombre=medicamentos[i][5];
          art_stock=medicamentos[i][6];
          
          if(receta_control==1 || !$('cronica').checked) {
      
          	  rangofec='d&iacute;as';
              rangofec2='horas'; 
              cantfec=medicamentos[i][3];

			  dosis = medicamentos[i][1] + ' <b>' + medicamentos[i][9] + '</b> <i>cada</i> ' + medicamentos[i][2] + '<i> ' + rangofec2 + ',  durante</i> ' + cantfec + ' <i>' + rangofec + '.</i>';

				if(medicamentos[i][11]==2) {
					dosis+=' <i>(M:'+medicamentos[i][12]+' T:'+medicamentos[i][13]+' N:'+medicamentos[i][14]+')</i>';
				}

          } else { 

              rangofec='meses';
              rangofec2='d&iacute;as';
              cantfec=(medicamentos[i][3]);

	          dosis = medicamentos[i][1] + ' <b>' + medicamentos[i][9] + '</b> <i>cada</i> ' + medicamentos[i][2] + '<i> ' + rangofec2 + ',  durante</i> ' + cantfec + ' <i>' + rangofec + '.</i>';

				if(medicamentos[i][11]==2) {
					dosis+=' <i>(M:'+medicamentos[i][12]+' T:'+medicamentos[i][13]+' N:'+medicamentos[i][14]+')</i>';
				}
	          
          }
          
          
          if(receta_control==0 && $('cronica').checked)
	   {
            totalua = Math.ceil(((medicamentos[i][3]*30))/(medicamentos[i][2])*(medicamentos[i][1])/(medicamentos[i][8]*1));
	     

          }
	   else
	   {
            totalua = Math.ceil(1*((medicamentos[i][3]*24))/(medicamentos[i][2])*(medicamentos[i][1])/(medicamentos[i][8]*1));
          }
          
          if(receta_control==1 || !$('cronica').checked) {
            
            if(totalua>art_stock) texto_color='color: red;'
            else                  texto_color='color: blue';
            
            tabla_html+='<tr class=\"tabla_fila\"><td style=\"text-align: right;\"><b>'+codigo+'</b></td><td>'+nombre+'</td><td style="text-align:right;">'+number_format(totalua,2)+'.-</td><td style="font-weight:bold;">'+medicamentos[i][10]+'</td><td rowspan=2><center><img src=\"iconos/delete.png\" onClick=\"quitar_art('+i+');\" alt=\"Quitar Medicamento...\" title=\"Quitar Medicamento\" style="cursor: pointer;"></center></td></tr><tr class=\"tabla_fila2\"><td style="text-align:right;"><b>Dosis/Stock:</b></td><td>'+dosis+'</td><td style=\"text-align: right; '+texto_color+'\">'+number_format(art_stock,2)+'.-</td><td style="font-weight:bold;">'+medicamentos[i][10]+'</td></tr>';
          
          } else {
          
            totaldesp=Math.ceil(totalua/medicamentos[i][3]);
          
            if(totaldesp>art_stock)
                texto_color='color: red;'
            else
                texto_color='color: blue';
          
            tabla_html+='<tr class=\"tabla_fila\"><td style=\"text-align: right;\"><b>'+codigo+'</b></td><td>'+nombre+'</td><td style="text-align:right;">'+number_format(totaldesp,2)+'.- / ('+number_format(totalua,2)+'.-)</td><td style="font-weight:bold;">'+medicamentos[i][10]+'</td><td rowspan=2><center><img src=\"iconos/delete.png\" onClick=\"quitar_art('+i+');\" alt=\"Quitar Medicamento...\" title=\"Quitar Medicamento\" style="cursor: pointer;"></center></td></tr><tr class=\"tabla_fila2\"><td style="text-align:right;"><b>Dosis/Stock:</b></td><td>'+dosis+'</td><td style=\"text-align: right; '+texto_color+'\">'+number_format(art_stock,2)+'.-</td><td style="font-weight:bold;">'+medicamentos[i][10]+'</td></tr>';
          }
      
      }
          
      tabla_html+="</table>";
      
      $('lista_medicamentos').innerHTML=tabla_html;
      
      }
      
      limpiar_art = function() {
        if(confirm('Est&aacute; seguro que desea limpiar la lista de medicamentos seleccionados?'.unescapeHTML())) {
            conta_med=0;
            medicamentos = new Array();
            $('lista_medicamentos').innerHTML='';
            $('observaciones').value='';
            $('detalle_prod').innerHTML='';
        }
      }
      
      quitar_art = function(numero) {
      
        medicamentos[numero]=null;
        
        redibujar_tabla();
      
      } 
      
      
    verifica_receta_tabla = function ()
    {
        if(!validacion_fecha($('fecha_retro')))
        {
            alert('Debe ingresar una fecha v&aacute;lida para la recepci&oacute;n.'.unescapeHTML());
            return;
        }
      
        cadena='';
                
        if (directo) {
        	
          if($('rut_medico').value=='') {
            alert('No ha seleccionado M&eacute;dico emisor de la receta.'.unescapeHTML());
            return;
          }

			 /*
          
          if($('centro_costo').value==-1) {
            alert('Debe seleccionar Centro de Costo/Servicio.');
            return;
          }
          
          */

          if($('centro_servicio').value==-1) {
            alert('Debe seleccionar Centro de Costo/Servicio.');
            return;
          }
          if($('centro_servicio').value=="-1") {
            alert('Debe seleccionar Centro de Costo/Servicio.');
            return;
          }
          
        }
        
        if($('adquiriente').style.display=='') {
        
          if($('adq_rut_correcto').value!=1) {
            alert('RUT de Adquiriente ingresado no es v&aacute;lido.'.unescapeHTML());
            return;
          } 
          
          if($('adq_retira').value*1==1) {
			if(trim($('adq_appat').value)=='' || trim($('adq_apmat').value)=='' || trim($('adq_nombres').value)=='') {
				alert('Falta llenar campos en la secci&oacute;n de Adquiriente.'.unescapeHTML());
				return;
			}
		  }
          
          if($('adq_retira').value*1==0) {
            $('adq_rut').disabled=false;
            $('adq_appat').disabled=false;
            $('adq_apmat').disabled=false;
            $('adq_nombres').disabled=false;
            $('adq_direccion').disabled=false;
          }
          
          campos_adquiriente='&'+$('adq_rut').serialize()+'&'+
                                  $('adq_appat').serialize()+'&'+
                                  $('adq_apmat').serialize()+'&'+
                                  $('adq_nombres').serialize()+'&'+
                                  $('adq_direccion').serialize();

          if($('adq_retira').value*1==0) {
            $('adq_rut').disabled=true;
            $('adq_appat').disabled=true;
            $('adq_apmat').disabled=true;
            $('adq_nombres').disabled=true;
            $('adq_direccion').disabled=true;
          }
                                  
        } else {
        
          campos_adquiriente='';
        
        }
        
        for(i=0;i<medicamentos.length;i++) {
        
          if(medicamentos[i]==null) continue;
        
          ///if($('cronica').checked) {
          //if(receta_control==1 || $('cronica').checked)
          if(receta_control==0 && $('cronica').checked) 
          {
            cantidad_art = Math.ceil(((medicamentos[i][3]*30))/(medicamentos[i][2])*(medicamentos[i][1])/(medicamentos[i][8]*1));
            cantidad_art=Math.floor(cantidad_art/medicamentos[i][3]);
          } else {
            cantidad_art = Math.ceil(1*((medicamentos[i][3]*24))/(medicamentos[i][2])*(medicamentos[i][1])/(medicamentos[i][8]*1));
          }
            
          if(medicamentos[i][6]<cantidad_art && $('despachar').checked) {
          
            var msg='No hay stock disponible el siguiente medicamento:\n\n';
            msg+='['+medicamentos[i][4]+'] '+medicamentos[i][5]+' -- (Saldo:'+medicamentos[i][6]+'/Despacho:'+cantidad_art+')';
            
            msg+='\n\n Â¿Desea ingresar la Receta de todas maneras?';
            if(!confirm(msg))
                return;
            }
          
          cadena+=medicamentos[i][0]+'/'+medicamentos[i][1]+'/'+medicamentos[i][2]+'/'+medicamentos[i][3]+'/'+medicamentos[i][8]+'/'+medicamentos[i][12]+'/'+medicamentos[i][13]+'/'+medicamentos[i][14]+'!';
        
        }
        
        $('medica').value=cadena;
        
        if (cadena=='') {
            alert('No ha seleccionado ning&uacute;n medicamento a&uacute;n.'.unescapeHTML());
            return;
        }
        
        $('nomd_diag_cod').disabled=false;
        
        if(directo) {
          
          $('rut_medico').disabled=false;
          campos_adicionales='&'+$('bodega_id').serialize()+'&directo';
        
        } else {
          
          campos_adicionales='';
          
        }
        
        if(bloquear_ingreso) {
        
          alert("Su solicitud se est&aacute; procesando.".unescapeHTML());
          return;
        
        }
        
        bloquear_ingreso=true;
        
        
      var myAjax = new Ajax.Request(
			'ficha_clinica/sql_receta.php', 
			{
				method: 'get', 
				parameters: $('receta').serialize()+campos_adicionales+campos_adquiriente,
				onComplete: function(pedido_datos) {
				
				    try {
              datos = pedido_datos.responseText.evalJSON(true);
				    } catch(err) { 
              alert(err);
              bloquear_ingreso=false;
              return;
            }
            
            if(datos[0]==true) {
				      
              try {
                es_cronica = $('cronica').checked;
              } catch(err) {
                es_cronica = false;
              }
              
            //  if(!directo || es_cronica) {
                alert('Receta ingresada exitosamente.');
             // } 
              
              if(es_cronica) {
                win = window.open('recetas/entregar_recetas/talonario.php?receta_id='+datos[1],
                            'win_talonario');
                win.focus();
              }
              
              if (mostrar_recetario)  mostrar_recetas();
              
              
              try {
              //alert($('despachar').checked);
                           
              if((!es_cronica) || (es_cronica && $('despachar').checked)) {
                limpiar_ficha_basica();
                
              }
              } catch(err) {
              alert(err);
              }
              $('win_receta').win_obj.close();
				    } else {
				    
              alert(datos[1].unescapeHTML());
              bloquear_ingreso=false;
              
            }
					
				}
				
			}
			
			);
      
      }
      
      cargar_servicios = function () {
      
        // centros = $('centro_costo');
        servs = $('centro_servicio');
        
        //valor = centros.value;
        
        servicios = servs.options;
        
        seleccionado = false;
        
        for(i=0;i<servicios.length;i++) {
          
          valoropt = servicios[i].value.substring(0,valor.length);
          
          if(valoropt==valor || servicios[i].value==-1) {
            servicios[i].style.display='';
          } else {
            servicios[i].style.display='none';
          }
          
        }
        
        servs.value=-1;
      
      }
      
      ocultar_servicios = function() {
      
        servs = $('centro_servicio');
        
        servicios = servs.options;
        
        for(i=0;i<servicios.length;i++) {
          if(servicios[i].value==-1) {
            servicios[i].style.display='';
          } else {
             servicios[i].style.display='none';
          }
        }
      }
      
      <?php 
      
            if($directo) {
              print('directo=true;'); 
            } else {
              print('directo=false;');
            }
             
       ?>
      
      if(directo) {
      
      ingreso_rut=function(datos_medico) {
      
      $('rut_medico').value=datos_medico[1];
      
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
      
      }
      
      
      validacion_fecha($('fecha_retro'));
      
      </script>
      
      <form name='receta' id='receta'>
 
 
<?php
if(!$cheque)
{
?>      
    <script>
        actualizar_cronica=function()
        {
            var val = $('cronica').checked;
            if(val)
            {
                <?php
                    if($nrec>0 and $cheque == 0)
                    {
                ?>
                        $('boton_copiar').style.display='none';
                <?php
                    }
                ?>
                $('despaprimera').style.display='';
                $('rangodosis').value=30;
                $('rangodosis').disabled=true;
                $('rangodosis2').value=24;
                $('rangodosis2').disabled=true;
		//$('cant_cronica').style.display='';
            }
            else
            {
                <?php
                    if($nrec>0 and $cheque == 0)
                    {
                ?>
                    $('boton_copiar').style.display='';
                <?php
                    }
                ?>
                $('despaprimera').style.display='none';
                $('rangodosis').value=1;
                $('rangodosis').disabled=true;
                $('rangodosis2').value=1;
                $('rangodosis2').disabled=true;
                //$('cant_cronica').style.display='none';
		$('horas').value='24';
		$('dias').value='1';
            }
        }
        autocompletar_medicamentos = new AutoComplete(
        'codigo', 
        'autocompletar_sql.php',
        function() {
            if($('codigo').value.length<3) return false;
      
            return {
            method: 'get',
            parameters: 'tipo=buscar_meds&'+$('codigo').serialize()+'&'+$('bodega_id').serialize()
            }
        }, 'autocomplete', 350, 200, 250, 1, 3, abrir_articulo);
      
        mostrar_opciones_receta();
    </script>
    <div class='sub-content'>
        <input type='checkbox' value='1' id='cronica' name='cronica' onClick='actualizar_cronica();'> <b>Receta Cr&oacute;nica</b> (<i>Despacho Parcializado de Medicamentos</i>) 
    </div>
<?php
}
else
{
?>

  <script>
      
      autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      'autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_meds_controlados&'+$('codigo').serialize()+
                      '&'+$('bodega_id').serialize()+'&'+$('tipo_talonario').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 3, abrir_articulo);
   
   
   
   
    </script>
      
  
  <div class='sub-content'>
  
  <input type='hidden' id='cheque' name='cheque' value=''>
  <?php echo $_tipo;  // Cadenas con los parámetros para cada tipo de receta ?>

  <table>
  <tr>
  <td style='text-align: right;'>Tipo de Talonario:</td>
  <td>
  <select id='tipo_talonario' name='tipo_talonario' onChange='mostrar_opciones_receta();'>
  <?php echo $talonarioshtml?>
  </select>
  </td>
  </tr>
  <tr>
  <td style='text-align: right;'>N&uacute;mero de Receta:</td>
  <td>
  <input type='text' id='nro_receta' name='nro_receta' 
  style='text-align:right;' onKeyUp='if(event.which==13) $("nombre_medico").focus();'
  value=''>
  </td>
  </tr>
  </table>
  
  </div>

<?php

}

if($directo) {

?>

    <div class='sub-content'>
    <table>
        <tr>
    <td style='text-align: right;'>Fecha:</td>
    <td> 
    <input type='text' id='fecha_retro' name='fecha_retro' style='text-align: center;' size=10 value='<?php echo date("d/m/Y")?>' onBlur='validacion_fecha(this);'>
    <img src='iconos/date_magnify.png' id='fecha_retro_btn' name='fecha_retro_btn'>
    </td>
    </tr>
    
    <tr>
    <td style='text-align: right;'>M&eacute;dico:</td>
    <td> 
    <input type='text' id='rut_medico' name='rut_medico' size=10
    style='text-align: center;' disabled>
    <input type='text' id='nombre_medico' name='nombre_medico' size=35 onKeyUp=''>
    </td>
    </tr>
    
	<!--

    <tr>
    <td style='text-align: right;'>Centro de Costo:</td>
    <td>
    <select id='centro_costo' name='centro_costo'
    onClick='cargar_servicios();'>
    <option value=-1>(Seleccionar...)</option>
    <?php echo $centroshtml?>
    </select>
    </td>
    </tr>
    
	-->    
    
    <tr>
    <td style='text-align: right;'>Servicio/Programa:</td>
    <td>
    <input type='text' id='centro_costo' name='centro_costo' size=35
    style='text-align: center;'>
    <input type='hidden' id='centro_servicio' name='centro_servicio' value="-1">
    </td>
    </tr>
    
    <tr>
    <td style='text-align:right;'><input type='checkbox' id='prov_alta' name='prov_alta' /></td>
    <td>Provisi&oacute;n para <b>Alta del Paciente</b></td>
    </tr>
    
    <?php
        if($nrec>0 and $cheque == 0)
        {
    ?>
            <tr>
                <td colspan=2>
                    <center>
                        <input type='button' id='boton_copiar' name='' value='[Copiar Ultima Receta]' onClick='cargar_ultima_receta();' />
                    </center>
                </td>
            </tr>
    <?php
        }
    ?>
    
    </table>
    
    
    
    </div>
    
    <div class='sub-content' id='adquiriente' style='display: none;' >
    
      <table>
      <tr>
      <td style='text-align: right;'>Retira:</td>
      <td>
      <select id='adq_retira' onChange='setear_adquiriente();'>
      <option value=0 SELECTED>Paciente</option>
      <option value=1>Adquiriente</option>
      </select>
      </td>
      </tr>
      
      <tr>
      <td style='text-align: right;'>RUT:</td>
      <td>
      <input type='text' id='adq_rut' name='adq_rut' value='' size=10 onKeyUp='comprobar_rut_adquiriente(); if(event.which==13) $("adq_appat").focus(); '>
      <input type='hidden' id='adq_rut_correcto' name='adq_rut_correcto' 
      value=0>
      </td>
      </tr>
      
      <tr>
      <td style='text-align: right;'>Paterno/Materno:</td>
      <td>
      <input type='text' id='adq_appat' name='adq_appat' value='' onkeyup='if(event.which==13) $("adq_apmat").focus();'>
      <input type='text' id='adq_apmat' name='adq_apmat' value='' onkeyup='if(event.which==13) $("adq_nombres").focus();'>
      </td>
      </tr>
      
      <tr>
      <td style='text-align: right;'>Nombres:</td>
      <td>
      <input type='text' id='adq_nombres' name='adq_nombres' value='' size=30 onkeyup='if(event.which==13) $("codigo").focus();'>
      </td>
      </tr>
      
      <tr>
      <td style='text-align: right;'>Direcci&oacute;n:</td>
      <td>
      <input type='text' id='adq_direccion' name='adq_direccion' value='' size=30>
      </td>
      </tr>
            
      </table>
        
    </div>

<?php

}

?>

      <div class='sub-content'>
      
      <input type='hidden' id='paciente' name='paciente' 
      value='<?php echo $paciente?>'>
      
      <input type='hidden' id='medica' name='medica' value=''>
      
      <input type='hidden' id='articulo_id' name='articulo_id' value=''>
      <input type='hidden' id='relacion_ua' name='relacion_ua' value=''>
      
      <input type='hidden' id='articulo_stock' name='articulo_stock' value=''>
      <input type='hidden' id='articulo_control' name='articulo_control' value='0'>

      <input type='hidden' id='art_tipo_adm' name='art_tipo_adm' value='0'>
      
      <table style='width:100%;'>
      <tr><td style='text-align: right;width:20%;'>Cod. Medicamento:</td><td colspan=2>
      <input type='text' id='codigo' name='codigo'>
      </td></tr>
      <tr><td style='text-align: right;'>Dosis:</td><td>
      
      <div id='cant_normal'>
      
      <input type='text' id='cant' name='cant' size=3  style='text-align: right;'     onKeyUp='
      calcular_cant();
      if(event.which==13) $("horas").focus();
      '><span id='unidad_nombre' name='unidad_nombre' style='font-weight:bold;'></span>
      
      cada 
      <input type='text' id='horas' name='horas' size=3  style='text-align: right;'
      onKeyUp='
      calcular_cant();
      if(event.which==13) $("dias").focus();
      '>
      <select id='rangodosis2' DISABLED>
      <option value=1 SELECTED>horas</option>
      <option value=24>d&iacute;as</option>
      </select>
      
      </div>
      
      <div id='cant_insulina' style='display:none;'>
      
	  <table>
		  <tr>
			  <td><input type='text' id='cant1' name='cant1' size=3  style='text-align: right;'     onKeyUp='
			  calcular_cant();
			  '></td><td>Ma&ntilde;ana</td>
		</tr><tr>
			  <td><input type='text' id='cant2' name='cant2' size=3  style='text-align: right;'     onKeyUp='
			  calcular_cant();
			  '></td><td>Tarde</td>
		</tr><tr>			  
			  <td><input type='text' id='cant3' name='cant3' size=3  style='text-align: right;'     onKeyUp='
			  calcular_cant();
			  '></td><td>Noche</td>
			</tr>
	  </table>
      
      </div>
      
      </td><td>
      
       por
      <input type='text' id='dias' name='dias' size=3  style='text-align: right;'     onKeyUp='
      calcular_cant();
      if(event.which==13) agregar_art();
      '>
      
      <select id='rangodosis' onChange='calcular_cant();' DISABLED>
      <option value=1 SELECTED>d&iacute;as.</option>
      <option value=30>meses.</option>
      </select>
      
      </td></tr>
      
      
      <tr><td style='text-align: right;'>Medicamento:</td>
      
      <td id='detalle_prod' colspan=2></td></tr>
      
      
      <tr><td style='text-align: right;'>Total:</td>
      <td style='font-weight: bold;' colspan=2>
      <span id='total'>0</span> <span id='unidad_ua'>U.A.</span>
      </td></tr>
  
		<tr><td style='display:none;text-align:center;font-size:14px;' id='art_despacho' colspan=3></td></tr>
  
      
      <tr><td colspan=3>
      <div class='sub-content2' id='lista_medicamentos' 
      name='lista_medicamentos'
      style='height: 120px; min-height: 120px; overflow: auto;'>
      
      
      
      
      </div>
      </td>
      </tr>
      <tr><td colspan=3>
      
      <table>

		<tr>
      <td colspan='3'>
      
      <center>
      <span id='despaprimera' style='display:none;'>
      <input type='checkbox' id='despachar' name='despachar' CHECKED>
      Despachar primera dosis.
      </center>  
      </span>
      </center>
      
          
      </td>
      </tr>
      <tr>
      	<td style='text-align:right;'>Diag. CIE10:</td>
			<td colspan=3>
			<input type='text' id='nomd_diag_cod' name='nomd_diag_cod' 
			value='<?php echo $ndet['nomd_diag_cod']; ?>' DISABLED size=5 style='font-weight:bold;text-align:center;' />
			<input type='text' id='nomd_diagnostico' 
			value='<?php echo $ndet['nomd_diag']; ?>' name='nomd_diagnostico' size=30
			onDblClick='$("nomd_diag_cod").value=""; $("nomd_diagnostico").value="";' onKeyUp='if(event.which==13) $("num_serie").focus();'/>
		</td>
		</tr>
      <tr>
      <td style='text-align: right;'>Nros de Serie:</td><td>
      <input type='text' id='num_serie' name='num_serie' style='text-align:left;' size=40 onKeyUp='if(event.which==13) $("observaciones").focus();'>
      </td>
      </tr>
      <tr><td style='text-align: right;' valign='top'>Observaciones:</td>
      <td colspan=2>
      <input type='text' id='observaciones' 
      name='observaciones' size=40>
      </td></tr></table>
      
      </td>
      </tr>
      </table>
      
      
    <center><table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='verifica_receta_tabla();'>Ingresar Receta...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='limpiar_art();'>
		Limpiar Selecci&oacute;n...</a>
		</td></tr></table>
    
      </div>
      
      </form>
      
		</td></tr></table>
      
    </div>
    
<script> 
    
    
    setear_adquiriente();
    
    mostrar_opciones_receta();
    
    if(receta_control==0)
    {
        if(!directo)
            $('codigo').focus();
        else
            $('nombre_medico').focus();
    }
    else
    {
        $('nro_receta').focus();
    }
    <?php if(_cax(1000))
    {
        if(!$cheque)
        {
    ?>
            $('cronica').checked=true;
            actualizar_cronica();
   <?php
        }
    }
    ?>
    seleccionar_centro_costo = function(d)
    {
        $('centro_costo').value=d[2].unescapeHTML();
	$('centro_servicio').value=d[0];
    }
    autocompletar_centro_costo = new AutoComplete(
      'centro_costo', 
      'autocompletar_sql.php',
      function() {
        if($('centro_costo').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=centro_costo&cadena='+encodeURIComponent($('centro_costo').value)
        }
      }, 'autocomplete', 500, 200, 150, 2, 3, seleccionar_centro_costo);
      
      
    ingreso_diagnosticos=function(datos_diag)
    {
        var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	$('nomd_diag_cod').value=cie10;
      	$('nomd_diagnostico').value=datos_diag[2].unescapeHTML();
    }

      autocompletar_diagnosticos = new AutoComplete(
      	'nomd_diagnostico', 
      	'autocompletar_sql.php',
      function() {
        if($('nomd_diagnostico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('nomd_diagnostico').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos);
      
      

</script>
    
