<?php
    require_once('../../conectar_db.php');
    $esp_id=$_GET['esp_id']*1;
    $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=".$esp_id, true);
    $pro=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=".$esp_id, true);
   
       
    $presta=cargar_registros_obj("SELECT *,
    (SELECT COUNT(*) FROM nomina_detalle_prestaciones WHERE nomina_detalle_prestaciones.pc_id=prestaciones_tipo_atencion.pta_id) AS cnt
    FROM prestaciones_tipo_atencion where esp_id=$esp_id AND activado!=false",true);
    
    if(!$presta)
        $presta=array();
    
    $tipo_atencion=cargar_registros_obj("SELECT DISTINCT nom_motivo from nomina where nom_motivo is not null order by nom_motivo", true);
    if(!$tipo_atencion)
        $tipo_atencion=array();
    
?>
<html>
    <title><?php if($esp_id!=0) echo 'Editar'; else echo 'Crear nueva';?> Especialidad/Unidad</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        presta=<?php echo json_encode($presta); ?>;
        tipo_atencion=<?php echo json_encode($tipo_atencion); ?>;
        
        agregar_codigo=function() {
            if($('codigo').value=='')
            {
                alert(('Debe ingresar C&oacute;digo de Fonasa.').unescapeHTML());
                return;
            }
            if($('select_nom_motivo').value=='0')
            {
                alert(('Debe ingresar Tipo de Atenci&oacute;n.').unescapeHTML());
                return;
            }
            if($('doc_id').value=='0')
            {
                alert(('Debe ingresar Profesional M&eacute;dico.').unescapeHTML());
                return;
            }
            
            guardar_tabla();
            
            var num=presta.length;
            presta[num]=new Object();
            presta[num].pta_id=0;
            presta[num].presta_codigo=$('codigo').value;
            presta[num].presta_desc=$('desc').value;
            presta[num].nom_motivo=$('select_nom_motivo').value;
            presta[num].doc_id=$('doc_id').value;
            presta[num].doc_nombres=$('nombre_doctor').value;
            presta[num].cnt=0;
            listar_prestaciones();
            limpiar(1);
            limpiar(2);
            $('select_nom_motivo').value="0";
        }

        guardar_tabla=function(){
            for(var i=0;i<presta.length;i++) {
                
                var pta_id=presta[i].pta_id;
                if(pta_id!=0) {
                    presta[i].presta_codigo=$('pc_codigo_'+pta_id).value;
                    presta[i].presta_desc=$('pc_desc_'+pta_id).value;
                    presta[i].nom_motivo=$('pc_nom_motivo_'+pta_id).value;
                    presta[i].doc_id=$('pc_docid_'+pta_id).value;
                    presta[i].doc_nombres=$('pc_docnombre_'+pta_id).value;
		}
                else {
                    presta[i].presta_codigo=$('pc_codigo_0_'+i).value;
                    presta[i].presta_desc=$('pc_desc_0_'+i).value;
                    presta[i].nom_motivo=$('pc_nom_motivo_0_'+i).value;
                    presta[i].doc_id=$('pc_docid_0_'+i).value;
                    presta[i].doc_nombres=$('pc_docnombre_0_'+i).value;
                }	
            }
        }
        
        
        
        
        
        
		desactivar_presta=function(num) {
			var myAjax=new Ajax.Request('actualizar_listado_prestaciones.php',
            {
                method:'post',
                parameters: 'pta_id='+num+'&esp_id='+<?php echo $esp_id;?>+'',
                asynchronous: true,
                onComplete: function(resp)
                {
                    presta=resp.responseText.evalJSON(true);
                    listar_prestaciones();
                }
            });
        }
		

		listar_prestaciones=function() {
			var html='<table style="width:100%;">';
            html+='<tr class="tabla_header">';
                html+='<td style="width:10%;">C&oacute;digo FONASA</td>';
                html+='<td style="width:50%px;">Descripci&oacute;n</td>';
                html+='<td style="width:10%;">Tipo Atenci&oacute;n</td>';
                html+='<td style="width:20%;">Profesional M&eacute;dico</td>';
                html+='<td style="width:5%;">Regs.</td>';
                html+='<td style="width:5%;">Desactivar</td>';
            html+='</tr>';
			for(var i=0;i<presta.length;i++) {
                var clase=(i%2==0)?'tabla_fila':'tabla_fila2';				
				html+='<tr class="'+clase+'">';
				if(presta[i].pta_id!=0) {
                    html+='<td><input type="text" style="width:100%;text-align:center;" id="pc_codigo_'+presta[i].pta_id+'" name="pc_codigo_'+presta[i].pta_id+'" value="'+presta[i].presta_codigo+'" size="12" readonly/></td>';
                    html+='<td><input type="text" style="width:100%;" id="pc_desc_'+presta[i].pta_id+'" name="pc_desc_'+presta[i].pta_id+'" value="'+presta[i].presta_desc+'"   readonly/></td>';
                    html+='<td><input type="text" style="width:100%;" id="pc_nom_motivo_'+presta[i].pta_id+'" name="pc_nom_motivo_'+presta[i].pta_id+'" value="'+presta[i].nom_motivo+'" size="8" readonly/></td>';
                    if(presta[i].doc_id!='')
                    {
                        html+='<td>';
                            html+='<table style="width:100%;">';
                                html+='<tr>';
                                    html+='<td>';
                                        html+='<input type="hidden" id="pc_docid_'+presta[i].pta_id+'" name="pc_docid_'+presta[i].pta_id+'" value="'+presta[i].doc_id+'" readonly/>';   
                                        html+='<input type="text" style="width:100%;" id="pc_docnombre_'+presta[i].pta_id+'" name="pc_docnombre_'+presta[i].pta_id+'" value="'+presta[i].doc_nombres+'" readonly/>';
                                    html+='</td>';
                                html+='</tr>';
                            html+='</table>';
                        html+='</td>';
                    }
                    else
                    {
                        html+='<td>';
                            html+='<table style="width:100%;">';
                                html+='<tr>';
                                    html+='<td>';
                                        html+='<input type="hidden" id="pc_docid_'+presta[i].pta_id+'" name="pc_docid_'+presta[i].pta_id+'" value="'+presta[i].doc_id+'" readonly/>';   
                                        html+='<input type="text" style="width:100%;" id="pc_docnombre_'+presta[i].pta_id+'" name="pc_docnombre_'+presta[i].pta_id+'" value="'+presta[i].doc_nombres+'" size="10" readonly/>';
                                    html+='</td>';
                                    html+='<td>';
                                        html+='<img src="../../iconos/cross.png" style="cursor:pointer;" onClick="" />';
                                    html+='</td>';
                                html+='</tr>';
                            html+='</table>';
                        html+='</td>';
                    }
		}
                else {
                    html+='<td><input type="text" style="width:100%;text-align:center;" id="pc_codigo_0_'+i+'" name="pc_codigo_0_'+i+'" value="'+presta[i].presta_codigo+'" readonly/></td>';
                    html+='<td><input type="text" style="width:100%;" id="pc_desc_0_'+i+'" name="pc_desc_0_'+i+'" value="'+presta[i].presta_desc+'" readonly/></td>';			
                    html+='<td><input type="text" style="width:100%;" id="pc_nom_motivo_0_'+i+'" name="pc_nom_motivo_0_'+i+'" value="'+presta[i].nom_motivo+'" size="8" readonly/></td>';
                    if(presta[i].doc_id!='')
                    {
                        html+='<td>';
                            html+='<table style="width:100%;">';
                                html+='<tr>';
                                    html+='<td>';
                                        html+='<input type="hidden" id="pc_docid_0_'+i+'" name="pc_docid_0_'+i+'" value="'+presta[i].doc_id+'" readonly/>';   
                                        html+='<input type="text" style="width:100%;" id="pc_docnombre_0_'+i+'" name="pc_docnombre_0_'+i+'" value="'+presta[i].doc_nombres+'" readonly/>';
                                    html+='</td>';
                                html+='</tr>';
                            html+='</table>';
                        html+='</td>';
                    }
                    else
                    {
                        html+='<td>';
                            html+='<table style="width:100%;">';
                                html+='<tr>';
                                    html+='<td>';
                                        html+='<input type="hidden" id="pc_docid_0_'+i+'" name="pc_docid_0_'+i+'" value="'+presta[i].doc_id+'" readonly/>';   
                                        html+='<input type="text" style="width:100%;" id="pc_docnombre_0_'+i+'" name="pc_docnombre_0_'+i+'" value="'+presta[i].doc_nombres+'" size="10" readonly/>';
                                    html+='</td>';
                                    html+='<td>';
                                        html+='<img src="../../iconos/cross.png" style="cursor:pointer;" onClick="" />';
                                    html+='</td>';
                                html+='</tr>';
                            html+='</table>';
                        html+='</td>';
                    }
                    
                    
                    
                    
                    
                    
		}
                
		html+='<td><center>'+presta[i].cnt+'</center></td>';
		if(presta[i].cnt==0)
                    html+='<td><center><img src="../../iconos/delete.png" style="cursor:pointer;" onClick="desactivar_presta('+presta[i].pta_id+');" /></center></td>';
		else
                    html+='<td>&nbsp;</td>';
                
                html+='</tr>';
                
		}
		html+='</table>';
            
            
            
            
            $('prestaciones').innerHTML=html;
	}

        guardar_esp=function() {
            guardar_tabla();
            var params='&presta='+encodeURIComponent(presta.toJSON());
            var myAjax=new Ajax.Request('sql_editar.php', {
                method:'post',
		parameters:$('datos').serialize()+params,
                onComplete:function() {
                    alert('Cambios guardados exitosamente.');
                    //window.close();	
		}	
            });
        }
        
        limpiar=function(index){
            if(index=='1'){
                $('codigo').value="";
                $('desc').value="";
            }
            if(index=='2'){
                $('doc_id').value="0";
                $('nombre_doctor').value="";
            }
        }
    </script>
    <body>
        <form id='datos' name='datos' onSubmit='return false;'>
            <input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>' />
            <div class='sub-content'>
                <img src='../../iconos/layout_edit.png'>
                <b><?php if($esp_id!=0) echo 'Editar'; else echo 'Crear nueva'; ?> Especialidad/Unidad</b>
            </div>
            <div class='sub-content'>
                <table style='width:100%;'>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Unidad:</td>
                        <td class='tabla_fila'>
                            <input type='text' id='esp_desc' name='esp_desc' size=30 value='<?php echo $esp['esp_desc']; ?>' />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Cod. Interno:</td>
                        <td class='tabla_fila'>
                            <input type='text' id='esp_codigo_int' name='esp_codigo_int' size=10 value='<?php echo $esp['esp_codigo_int']; ?>' />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Genera Proc./Ex&aacute;menes:</td>
                        <td class='tabla_fila'>
                            <input type='checkbox' id='proce' name='proce' <?php if($pro) echo 'CHECKED'; ?> />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Genera Informe Cl&iacute;nico:</td>
                        <td class='tabla_fila'>
                            <input type='checkbox' id='informe' name='informe' <?php if($pro['esp_informe']=='t') echo 'CHECKED'; ?> />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Asocia Orden de Atenci&oacute;n:</td>
                        <td class='tabla_fila'>
                            <input type='checkbox' id='orden' name='orden' <?php if($pro['esp_orden_atencion']=='t') echo 'CHECKED'; ?> />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Genera Sesiones:</td>
                        <td class='tabla_fila'>
                            <input type='checkbox' id='orden' name='orden' <?php if($pro['esp_sesiones']=='t') echo 'CHECKED'; ?> />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Equipos Asociados:</td>
                        <td class='tabla_fila'>
                            <textarea cols=60 rows=5 id='equipos' name='equipos'><?php echo $pro['esp_equipos']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' style='text-align:right;' class='tabla_fila2'>Campos Din&aacute;micos:</td>
                        <td class='tabla_fila'>
                            <textarea cols=60 rows=5 id='campos' name='campos'><?php echo $pro['esp_campos']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
						<td colspan=2>
							<table style='width:100%;'>
								<tr class="tabla_header">';
									<td style="width:10%;">C&oacute;digo FONASA</td>
									<td style="width:50%px;">Descripci&oacute;n</td>
									<td style="width:10%;">Tipo Atenci&oacute;n</td>
									<td style="width:20%;">Profesional M&eacute;dico</td>
									<td style="width:5%;">Agregar</td>
								</tr>
								<tr>
									<td style="width:100px;">
										<input type="text" id="codigo" name="codigo" style="width:100%;text-align:center;" ondblclick="limpiar(1);"/>
									</td>
									<td>
										<input type="text" id="desc" name="desc" style="width:100%;" readonly/>
									</td>
									<td style="text-align:center;">
										<select id="select_nom_motivo" name="select_nom_motivo">
											<option value="0">Selecionar Tipo Atenci&oacute;n</option>
											<?php
												for($i=0;$i<sizeof($tipo_atencion);$i++)
												{
													print('<option>'.$tipo_atencion[$i]['nom_motivo'].'</option>');
												}
											?>
										</select>
									</td>
									<td>
										<input type="hidden" id="doc_id" name="doc_id" value="0" />
										<input type="text" id="nombre_doctor" name="nombre_doctor" style="width:100%;" ondblclick="limpiar(2);"/>
									</td>
									<td>
										<center><img src="../../iconos/add.png"  style="cursor:pointer;" onClick="agregar_codigo();" /></center>
									</td>
								</tr>
							</table>
						</td>
                    </tr>
                </table>
            </div>
            <div class='sub-content2' id='prestaciones' name='prestaciones'>
            </div>
            <br />
            <center>
                <input type='button' value=' - Guardar Especialidad - ' onClick='guardar_esp();' />
            </center>
        </form>
    </body>
</html>
<script>
    listar_prestaciones();
    
    
    seleccionar_medico=function(datos_medico) {
        $('doc_id').value=datos_medico[0];
        $('nombre_doctor').value=datos_medico[2];
    }
    
    
    autocompletar_medicos = new AutoComplete(
    'nombre_doctor', 
    '../../autocompletar_sql.php',
    function() {
    if( $('nombre_doctor').value.length < 3 ) return false;
    return {
    method: 'get',
    parameters: 'tipo=doctor&'+$('nombre_doctor').serialize()
    }
    }, 'autocomplete', 350, 200, 250, 1, 2, seleccionar_medico);
    
    
    seleccionar_prestacion = function(presta)
    {
        $('codigo').value=presta[0].unescapeHTML();
        $('desc').value=presta[2].unescapeHTML();
    }
    
    
    
    autocompletar_prestaciones = new AutoComplete(
    'codigo', 
    '../../autocompletar_sql.php',
    function() {
    if($('codigo').value.length<2) return false;
    return {
    method: 'get',
    parameters: 'tipo=prestacion&cod_presta='+encodeURIComponent($('codigo').value)
    }
    }, 'autocomplete', 350, 100, 150, 1, 2, seleccionar_prestacion);
    
    
    
    /*
    autocompletar_prestaciones = new AutoComplete(
    'codigo', 
    '../../autocompletar_sql.php',
    lista_prestaciones, 'autocomplete', 350, 100, 150, 1, 2, seleccionar_prestacion);
    */
    
    
</script>
