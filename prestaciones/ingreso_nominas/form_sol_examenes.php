<?php
  require_once('../../conectar_db.php');
  /*
  $c=cargar_registros_obj("
  SELECT *, to_char(nom_fecha, 'D') AS dow  FROM nomina_detalle
  JOIN nomina USING (nom_id)
  JOIN especialidades ON nom_esp_id=esp_id
  JOIN doctores ON nom_doc_id=doc_id
  JOIN pacientes USING (pac_id)
  LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
  WHERE nomd_diag_cod = 'X' AND (nomd_estado IS NULL OR nomd_estado!='1')
  ORDER BY nom_fecha, nomd_hora
  ", true);
  */
  $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades where esp_recurso=true ORDER BY esp_desc", NULL, '', '');
  //$fecha1=date('d/m/Y', mktime(0,0,0,date('m'),date('d')-7,date('Y')));
  $fecha2=date('d/m/Y');
?>
<script>
  var bloquear_ajax=false;
  var examenes=new Array();
  //--------------------------------------------------------------------------
  buscar_citacion=function(nomd_id,esp_id,sol_id,sol_examd_nomd_id) {
    if(($('estado_solicitudes').value*1)!=2) {
      if($('txt_nomd_id').value!=nomd_id) {
        alert("No ha seleccionado examenes de la solicitud que desea agendar");
				return;
			}
			if($('txt_examenes').value=='') {
        alert("No ha seleccionado examenes para realizar agendamiento");
				return;
			}
    }
    top=Math.round(screen.height/2)-250;
    left=Math.round(screen.width/2)-340;
    new_win = window.open('prestaciones/ingreso_nominas/buscar_citacion.php?nomd_id='+nomd_id+'&esp_examen='+esp_id+'&sol_id='+sol_id+'&txt_examenes='+$('txt_examenes').value+'&sol_examd_nomd_id='+sol_examd_nomd_id,
    'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=no, width=1200, height=600, '+
    'top='+top+', left='+left);
    new_win.focus();
  }
  //--------------------------------------------------------------------------
  gestiones_citacion=function(nomd_id)
  {
    top=Math.round(screen.height/2)-250;
    left=Math.round(screen.width/2)-340;
    new_win = window.open('prestaciones/ingreso_nominas/gestionar_citacion.php?nomd_id='+nomd_id,
    'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
    'top='+top+', left='+left);
    new_win.focus();
  }
  //--------------------------------------------------------------------------
  imprimir_citacion=function(nomd_id,esp_id,sol_id) {
    if($('txt_nomd_id').value!=nomd_id) {
      alert("No ha seleccionado examenes de la solicitud que desea agendar");
      return;
    }
    if($('txt_examenes').value=='') {
      alert("No ha seleccionado examenes para realizar agendamiento");
      return;
    }
    top=Math.round(screen.height/2)-250;
    left=Math.round(screen.width/2)-340;
    new_win = window.open('prestaciones/ingreso_nominas/citaciones_examenes.php?nomd_id='+nomd_id+'&esp_examen='+esp_id+'&sol_id='+sol_id+'&txt_examenes='+$('txt_examenes').value+'&pac_id='+$('pac_id').value,
    'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
    'top='+top+', left='+left);
    new_win.focus();

  }
  //--------------------------------------------------------------------------
  listar_solicitudes=function() {
    var list=2;
    bloquear_ajax=true;
    $('txt_nomd_id').value=0;
    $('txt_examenes').value='';
    $j('#lista_examenes').html('<center><table><tr><td><img src=imagenes/loading_small.gif></td></tr></table></center>');
    $j.ajax(
      {
        url: 'prestaciones/ingreso_nominas/buscar_examenes.php',
        type: 'POST',
        dataType: 'json',
        async:false,
        data: {pac_id:$('pac_id').value,esp_id:$('esp_id').value,list:list,estado_solicitudes:$('estado_solicitudes').value,fecha1:$('fecha1').value,fecha2:$('fecha2').value},
        success: function(data)
        {
          registros_examenes=data;
          dibujar_examenes();
        }
      });
      //array_docs=new Array();
      bloquear_ajax=false;
    }

    //--------------------------------------------------------------------------
    dibujar_examenes = function() {
      if(registros_examenes[0]==false) {
        $j('#lista_examenes').html('<center><table><tr><td>NO SE HAN ENCONTRADO EX&Aacute;MENES</td></tr></table></center>');
      } else {
        var html="";
        var nomd_id=0;
        var color='';
        var cont=0;
        var pac_id=0;
        for(var i=0;i<registros_examenes[0].length;i++) {
          if(i==0) {
            html+='<table style="width:100%" border="0">';
              html+='<tr class="tabla_header">';
                html+='<td colspan=10>';
                  html+='<table style="width:100%">'
                    html+='<tr class="tabla_header">';
                    if((registros_examenes[0][i]['sol_nomd_id_original']*1)==0)
                      html+='<td style="text-align:left;font-size:12px;">Solicitud de Examenes Desde Nomina N&deg;:&nbsp;<b>ESPONTANEA</b></td>';
                    else
                      html+='<td style="text-align:left;font-size:12px;">Solicitud de Examenes Desde Nomina N&deg;:&nbsp;<b>'+registros_examenes[0][i]['sol_nomd_id_original']+'</b></td>';

                    color='';
                    var cadena='&nbsp;';
                    if(registros_examenes[0][i]['nomd_diag_cod_destino']=='T'){
                      color='#FF0033';
                      cadena='SUSPENDIDO ['+registros_examenes[0][i]['susp_desc']+']';
                    }
                      html+='<td style="font-weight:bold;text-align:right;background-color:'+color+'"">';
                        html+='<table style="width:100%">'
                          html+='<tr>'
                            html+='<td style="font-weight:bold;text-align:left;width:650px;">'
                              html+=''+cadena+'';
                            html+='</td>'
                            html+='<td>'
                              html+="<img src='iconos/layout.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Imprimir Citaci&oacute;n' title='Imprimir Citaci&oacute;n' onClick='imprimir_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+","+registros_examenes[0][i]['sol_esp_id']+","+registros_examenes[0][i]['sol_exam_id']+");' />";
                              if(registros_examenes[0][i]['sol_examd_nomd_id']=='0'){
                                html+="<img src='iconos/date_magnify.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Citaciones Similares' title='Citaciones Similares' onClick='buscar_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+","+registros_examenes[0][i]['sol_esp_id']+","+registros_examenes[0][i]['sol_exam_id']+",0);' />";
                              } else {
                                if(registros_examenes[0][i]['nomd_diag_cod_destino']=='T'){
                                  html+="<img src='iconos/date_magnify.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Citaciones Similares' title='Citaciones Similares' onClick='buscar_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+","+registros_examenes[0][i]['sol_esp_id']+","+registros_examenes[0][i]['sol_exam_id']+","+registros_examenes[0][i]['sol_examd_nomd_id']+");' />";
                                }
                              }
                              if($('estado_solicitudes').value==0) {
                                html+="<img src='iconos/phone.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Gestiones Citaci&oacute;n' title='Gestiones Citaci&oacute;n' onClick='gestiones_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+");' />";
                              }
                            html+='</td>'
                          html+='</tr>'
                        html+='</table>'
                      html+='</td>';
                    html+='</tr>';
                  html+='</table>';
                html+='</td>';
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;width:200px;" class="tabla_fila2">Tipo Solicitud:</td>';

                if((registros_examenes[0][i]['sol_origen']*1)==""){
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;LOCAL</td>';
                } else {
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['solicitud_origen']+'</td>';
                }
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Fecha Solicitud:</td>';
                html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['fecha_solicitud']+'</td>';
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Ingresado por Funcionario:</td>';
                html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['func_nombre']+'</td>';
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Profesional Solicitante:</td>';
                if(registros_examenes[0][i]['sol_doc_id']==0 || registros_examenes[0][i]['sol_doc_id']=='' || registros_examenes[0][i]['sol_doc_id']==null ){
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;SIN ASIGNAR</td>';
                } else {
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['doc_nombre']+'</td>';
                }
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Especialidad Solicitante:</td>';
                if(registros_examenes[0][i]['sol_esp_solicita']==0 || registros_examenes[0][i]['sol_esp_solicita']=='' || registros_examenes[0][i]['sol_esp_solicita']==null  || registros_examenes[0][i]['sol_esp_solicita']=="0"){
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;SIN ASIGNAR</td>';
                } else {
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['tbesp2_desc']+'</td>';
                }
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Servicio Solicitante:</td>';
                if(registros_examenes[0][i]['centro_nombre']==0 || registros_examenes[0][i]['centro_nombre']=='' || registros_examenes[0][i]['centro_nombre']==null  || registros_examenes[0][i]['centro_nombre']=="0") {
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;SIN ASIGNAR</td>';
                } else {
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['centro_nombre']+'</td>';
                }
              html+='</tr>';
              html+='<tr class="tabla_header">';
                html+='<td colspan="2" style="font-weight:bold;text-align:left;font-size:13px;"><i>Datos del Paciente:</i></td>';
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Rut Paciente:</td>';
                html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['pac_rut']+'</td>';
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Paciente:</td>';
                html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['nombre_paciente']+'</td>';
              html+='</tr>';
              html+='<tr>';
                html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Diagnostico:</td>';
                if(registros_examenes[0][i]['nomd_diag']!=''){
                  if(registros_examenes[0][i]['nomd_diag_cod']!="OK" && registros_examenes[0][i]['nomd_diag_cod']!="ALTA" && registros_examenes[0][i]['nomd_diag_cod']!="N" && registros_examenes[0][i]['nomd_diag_cod']!="T") {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;['+registros_examenes[0][i]['nomd_diag_cod']+'] : '+registros_examenes[0][i]['nomd_diag']+'</td>';
                  } else {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['nomd_diag']+'</td>';
                  }
                } else {
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;Sin Asignar</td>';
                }
              html+='</tr>';
              html+='<tr>';
                html+='<td colspan=10 class="tabla_header" style="text-align:center;border-color: #000000;border-left: 2px solid #000000;border-style: solid;border-width: 1px 1px 2px 2px;margin: 1px;padding: 0;">';
                  html+='<table style="width:100%" border="0">';
                    html+='<tr class="tabla_header">';
                      html+='<td style="text-align:center;">Tipo Ex&aacute;men</td>';
                      html+='<td style="text-align:center;width:150px;">Codigo</td>';
                      html+='<td style="text-align:center;width:80px;">Cantidad</td>';
                      html+='<td style="text-align:center;">Detalle Prestaci&oacute;n</td>';
                      if(registros_examenes[0][i]['sol_examd_nomd_id']!='0'){
                        html+='<td style="text-align:center;">Fecha Citaci&oacute;n</td>';
                        html+='<td style="text-align:center;">Hora Citaci&oacute;n</td>';
                      }
                      html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
                      if($('estado_solicitudes').value==1){
                          html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
                      }
                      html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
                    html+='</tr>';
                    color='';
                    //if(registros_examenes[0][i]['sol_examd_estado']=='1')
                    if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                      color='#CAFF70';
                    }
                    html+='<tr class="tabla_fila" style="background-color:'+color+'" onMouseOver="this.style.background=\'#9999FF\';" onMouseOut="this.style.background=\''+color+'\';">';
                      html+='<td style="text-align:center;font-size:10px;">'+registros_examenes[0][i]['sol_tipo_examen']+'</td>';
                      html+='<td style="text-align:center;">'+registros_examenes[0][i]['pc_codigo']+'</td>';
                      html+='<td style="text-align:center;">'+registros_examenes[0][i]['sol_examd_cant']+'</td>';
                      if(registros_examenes[0][i]['pc_grupo']!='' && registros_examenes[0][i]['pc_grupo']!=null){
                        html+='<td style="text-align:left;">'+registros_examenes[0][i]['pc_desc']+'&nbsp;--&nbsp;<b>['+registros_examenes[0][i]['pc_grupo']+']</b></td>';
                      } else {
                        html+='<td style="text-align:left;">'+registros_examenes[0][i]['pc_desc']+'</td>';
                      }

                      if(registros_examenes[0][i]['sol_examd_nomd_id']!='0'){
                        html+='<td style="text-align:center;">'+registros_examenes[0][i]['fecha_citacion']+'</td>';
                        html+='<td style="text-align:center;">'+registros_examenes[0][i]['hora_citacion']+'</td>';
                      }

                      html+='<td style="text-align:center;">';
                        if(registros_examenes[0][i]['sol_examd_nomd_id']=='0') {
                          html+='<input type="checkbox" id="chk_examen_'+registros_examenes[0][i]['sol_nomd_id_original']+'_'+cont+'" name="chk_examen_'+registros_examenes[0][i]['sol_nomd_id_original']+'_'+cont+'" onChange="check_examen('+registros_examenes[0][i]['sol_nomd_id_original']+','+cont+','+registros_examenes[0][i]['sol_examd_id']+');">';
                        }
                        else if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                          //html+='<input type="checkbox" id="chk_examen_'+registros_examenes[0][i]['sol_examd_id']+'" name="chk_examen_'+registros_examenes[0][i]['sol_examd_id']+'" onChange="" disabled checked>';
                          //html+='<img src="iconos/zoom.png" title="Ver Asignaci&oacute;n del Ex&aacute;men" style="cursor:pointer;" onClick="ver_asignacion();">';
                          html+='<img src="iconos/layout.png"  style="cursor:pointer;" alt="Imprimir Hoja AT." title="Imprimir Hoja AT." onClick="imprimir_citacion2('+registros_examenes[0][i]['sol_examd_nomd_id']+');" />';
                        }
                      html+='</td>';
                      if($('estado_solicitudes').value==1){
                          html+='<td style="text-align:center;">';
                            html+='<img src="iconos/script_edit.png"  style="cursor:pointer;" onClick="informe_examen('+registros_examenes[0][i]['sol_examd_id']+','+registros_examenes[0][i]['sol_examd_nomd_id']+');" />';
                          html+='</td>';
                      }

                      if(registros_examenes[0][i]['sol_examd_estado']=='1'){
                        html+='<td style="text-align:center;">';
                          html+='<img src="iconos/tick.png" title="Ex&aacute;men Realizado" style="cursor:pointer;" onClick="">';
                        html+='</td>';
                      } else {
                        html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
                      }
                    html+='</tr>';
                    cont=cont+1;
          } else {
            if(nomd_id==registros_examenes[0][i]['sol_nomd_id_original'] && pac_id==registros_examenes[0][i]['sol_pac_id']) {
              color='';
              if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                color='#CAFF70';
              }

              html+='<tr class="tabla_fila" style="background-color:'+color+'" onMouseOver="this.style.background=\'#9999FF\';" onMouseOut="this.style.background=\''+color+'\';">';
                html+='<td style="text-align:center;font-size:10px;">'+registros_examenes[0][i]['sol_tipo_examen']+'</td>';
                html+='<td style="text-align:center;">'+registros_examenes[0][i]['pc_codigo']+'</td>';
                html+='<td style="text-align:center;">'+registros_examenes[0][i]['sol_examd_cant']+'</td>';

                if(registros_examenes[0][i]['pc_grupo']!='' && registros_examenes[0][i]['pc_grupo']!=null) {
                  html+='<td style="text-align:left;">'+registros_examenes[0][i]['pc_desc']+'&nbsp;--&nbsp;<b>['+registros_examenes[0][i]['pc_grupo']+']</b></td>';
                } else {
                  html+='<td style="text-align:left;">'+registros_examenes[0][i]['pc_desc']+'</td>';
                }

                if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                  html+='<td style="text-align:center;">'+registros_examenes[0][i]['fecha_citacion']+'</td>';
                  html+='<td style="text-align:center;">'+registros_examenes[0][i]['hora_citacion']+'</td>';
                }

                html+='<td style="text-align:center;">';
                  if(registros_examenes[0][i]['sol_examd_nomd_id']=='0') {
                    html+='<input type="checkbox" id="chk_examen_'+registros_examenes[0][i]['sol_nomd_id_original']+'_'+cont+'" name="chk_examen_'+registros_examenes[0][i]['sol_nomd_id_original']+'_'+cont+'" onChange="check_examen('+registros_examenes[0][i]['sol_nomd_id_original']+','+cont+','+registros_examenes[0][i]['sol_examd_id']+');">';
                  }
                  else if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                    //html+='<input type="checkbox" id="chk_examen_'+registros_examenes[0][i]['sol_examd_id']+'" name="chk_examen_'+registros_examenes[0][i]['sol_examd_id']+'" onChange="" disabled checked>';
                    //html+='<img src="iconos/zoom.png" title="Ver Asignaci&oacute;n del Ex&aacute;men" style="cursor:pointer;" onClick="ver_asignacion();">';
                    html+='<img src="iconos/layout.png"  style="cursor:pointer;" alt="Imprimir Hoja AT." title="Imprimir Hoja AT." onClick="imprimir_citacion2('+registros_examenes[0][i]['sol_examd_nomd_id']+');" />';
                  }
                html+='</td>';

                if($('estado_solicitudes').value==1){
                    html+='<td style="text-align:center;">';
                      html+='<img src="iconos/script_edit.png"  style="cursor:pointer;" onClick="informe_examen('+registros_examenes[0][i]['sol_examd_id']+','+registros_examenes[0][i]['sol_examd_nomd_id']+');" />';
                    html+='</td>';
                }

                if(registros_examenes[0][i]['sol_examd_estado']=='1') {
                  html+='<td style="text-align:center;">';
                    html+='<img src="iconos/tick.png" title="Ex&aacute;men Realizado" style="cursor:pointer;" onClick="">';
                  html+='</td>';
                } else {
                  html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
                }
              html+='</tr>';
              cont=cont+1;
            } else {
              html+='</table>';
              html+='</td>';
              html+='</tr>';
              html+='</table>';
              html+='<input type="hidden" id="cant_'+registros_examenes[0][(i-1)]['sol_nomd_id_original']+'" name="cant_'+registros_examenes[0][(i-1)]['sol_nomd_id_original']+'" value="'+cont+'" />';
              html+='<br>';
              html+='<hr align="center" size="6" width="100%" color="maroon" />';
              html+='<br>';
              cont=0;

              html+='<table style="width:100%" border="0">';
                html+='<tr class="tabla_header">';
                  html+='<td colspan=10>';
                    html+='<table style="width:100%">'
                      html+='<tr class="tabla_header">';
                      if((registros_examenes[0][i]['sol_nomd_id_original']*1)==0)
                        html+='<td style="text-align:left;font-size:12px;">Solicitud de Examenes Desde Nomina N&deg;:&nbsp;<b>ESPONTANEA</b></td>';
                      else
                        html+='<td style="text-align:left;font-size:12px;">Solicitud de Examenes Desde Nomina N&deg;:&nbsp;<b>'+registros_examenes[0][i]['sol_nomd_id_original']+'</b></td>';

                      color='';
                      var cadena='&nbsp;';
                      if(registros_examenes[0][i]['nomd_diag_cod_destino']=='T'){
                        color='#FF0033';
                        cadena='SUSPENDIDO ['+registros_examenes[0][i]['susp_desc']+']';
                      }
                        html+='<td style="font-weight:bold;text-align:right;background-color:'+color+'"">';
                          html+='<table style="width:100%">'
                            html+='<tr>'
                              html+='<td style="font-weight:bold;text-align:left;width:650px;">'
                                html+=''+cadena+'';
                              html+='</td>'
                              html+='<td>'
                                html+="<img src='iconos/layout.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Imprimir Citaci&oacute;n' title='Imprimir Citaci&oacute;n' onClick='imprimir_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+","+registros_examenes[0][i]['sol_esp_id']+","+registros_examenes[0][i]['sol_exam_id']+");' />";
                                if(registros_examenes[0][i]['sol_examd_nomd_id']=='0'){
                                  html+="<img src='iconos/date_magnify.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Citaciones Similares' title='Citaciones Similares' onClick='buscar_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+","+registros_examenes[0][i]['sol_esp_id']+","+registros_examenes[0][i]['sol_exam_id']+",0);' />";
                                } else {
                                  if(registros_examenes[0][i]['nomd_diag_cod_destino']=='T'){
                                    html+="<img src='iconos/date_magnify.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Citaciones Similares' title='Citaciones Similares' onClick='buscar_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+","+registros_examenes[0][i]['sol_esp_id']+","+registros_examenes[0][i]['sol_exam_id']+","+registros_examenes[0][i]['sol_examd_nomd_id']+");' />";
                                  }
                                }
                                if($('estado_solicitudes').value==0) {
                                  html+="<img src='iconos/phone.png'  style='cursor:pointer;' width='22px;' height='22px;' alt='Gestiones Citaci&oacute;n' title='Gestiones Citaci&oacute;n' onClick='gestiones_citacion("+registros_examenes[0][i]['sol_nomd_id_original']+");' />";
                                }
                              html+='</td>'
                            html+='</tr>'
                          html+='</table>'
                        html+='</td>';
                      html+='</tr>';
                    html+='</table>';
                  html+='</td>';
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;width:200px;" class="tabla_fila2">Tipo Solicitud:</td>';
                  if((registros_examenes[0][i]['sol_origen']*1)==""){
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;LOCAL</td>';
                  } else {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['solicitud_origen']+'</td>';
                  }
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Fecha Solicitud:</td>';
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['fecha_solicitud']+'</td>';
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Ingresado por Funcionario:</td>';
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['func_nombre']+'</td>';
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Profesional Solicitante:</td>';
                  if(registros_examenes[0][i]['sol_doc_id']==0 || registros_examenes[0][i]['sol_doc_id']=='' || registros_examenes[0][i]['sol_doc_id']==null ){
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;SIN ASIGNAR</td>';
                  } else {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['doc_nombre']+'</td>';
                  }
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Especialidad Solicitante:</td>';
                  if(registros_examenes[0][i]['sol_esp_solicita']==0 || registros_examenes[0][i]['sol_esp_solicita']=='' || registros_examenes[0][i]['sol_esp_solicita']==null  || registros_examenes[0][i]['sol_esp_solicita']=="0"){
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;SIN ASIGNAR</td>';
                  } else {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['tbesp2_desc']+'</td>';
                  }
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Servicio Solicitante:</td>';
                  if(registros_examenes[0][i]['centro_nombre']==0 || registros_examenes[0][i]['centro_nombre']=='' || registros_examenes[0][i]['centro_nombre']==null  || registros_examenes[0][i]['centro_nombre']=="0") {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;SIN ASIGNAR</td>';
                  } else {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['centro_nombre']+'</td>';
                  }
                html+='</tr>';
                html+='<tr class="tabla_header">';
                  html+='<td colspan="2" style="font-weight:bold;text-align:left;font-size:13px;"><i>Datos del Paciente:</i></td>';
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Rut Paciente:</td>';
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['pac_rut']+'</td>';
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Paciente:</td>';
                  html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['nombre_paciente']+'</td>';
                html+='</tr>';
                html+='<tr>';
                  html+='<td style="font-weight:bold;text-align:right;font-size:12px;" class="tabla_fila2">Diagnostico:</td>';
                  if(registros_examenes[0][i]['nomd_diag']!=''){
                    if(registros_examenes[0][i]['nomd_diag_cod']!="OK" && registros_examenes[0][i]['nomd_diag_cod']!="ALTA" && registros_examenes[0][i]['nomd_diag_cod']!="N" && registros_examenes[0][i]['nomd_diag_cod']!="T") {
                      html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;['+registros_examenes[0][i]['nomd_diag_cod']+'] : '+registros_examenes[0][i]['nomd_diag']+'</td>';
                    } else {
                      html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;'+registros_examenes[0][i]['nomd_diag']+'</td>';
                    }
                  } else {
                    html+='<td style="text-align:left;" class="tabla_fila" colspan=9>&nbsp;Sin Asignar</td>';
                  }
                html+='</tr>';




              html+='<tr>';
              html+='<td colspan=10 class="tabla_header" style="text-align:center;border-color: #000000;border-left: 2px solid #000000;border-style: solid;border-width: 1px 1px 2px 2px;margin: 1px;padding: 0;">';
              html+='<table style="width:100%" border="0">';
              html+='<tr class="tabla_header">';
              html+='<td style="text-align:center;">Tipo Ex&aacute;men</td>';
              html+='<td style="text-align:center;width:150px;">Codigo</td>';
              html+='<td style="text-align:center;width:80px;">Cantidad</td>';
              html+='<td style="text-align:center;">Detalle Prestaci&oacute;n</td>';

              if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                html+='<td style="text-align:center;">Fecha Citaci&oacute;n</td>';
                html+='<td style="text-align:center;">Hora Citaci&oacute;n</td>';
              }

              html+='<td style="text-align:center;width:40px;">&nbsp;</td>';

              if($('estado_solicitudes').value==1){
                  html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
              }

              html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
              html+='</tr>';
              color='';

              if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                color='#CAFF70';
              }

              html+='<tr class="tabla_fila" style="background-color:'+color+'" onMouseOver="this.style.background=\'#9999FF\';" onMouseOut="this.style.background=\''+color+'\';">';
              html+='<td style="text-align:center;font-size:10px;">'+registros_examenes[0][i]['sol_tipo_examen']+'</td>';
              html+='<td style="text-align:center;">'+registros_examenes[0][i]['pc_codigo']+'</td>';
              html+='<td style="text-align:center;">'+registros_examenes[0][i]['sol_examd_cant']+'</td>';

              if(registros_examenes[0][i]['pc_grupo']!='' && registros_examenes[0][i]['pc_grupo']!=null) {
                html+='<td style="text-align:left;">'+registros_examenes[0][i]['pc_desc']+'&nbsp;--&nbsp;<b>['+registros_examenes[0][i]['pc_grupo']+']</b></td>';
              } else {
                html+='<td style="text-align:left;">'+registros_examenes[0][i]['pc_desc']+'</td>';
              }

              if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                html+='<td style="text-align:center;">'+registros_examenes[0][i]['fecha_citacion']+'</td>';
                html+='<td style="text-align:center;">'+registros_examenes[0][i]['hora_citacion']+'</td>';
              }

              html+='<td style="text-align:center;">';

              if(registros_examenes[0][i]['sol_examd_nomd_id']=='0') {
                html+='<input type="checkbox" id="chk_examen_'+registros_examenes[0][i]['sol_nomd_id_original']+'_'+cont+'" name="chk_examen_'+registros_examenes[0][i]['sol_nomd_id_original']+'_'+cont+'" onChange="check_examen('+registros_examenes[0][i]['sol_nomd_id_original']+','+cont+','+registros_examenes[0][i]['sol_examd_id']+');">';
              }
              else if(registros_examenes[0][i]['sol_examd_nomd_id']!='0') {
                //html+='<input type="checkbox" id="chk_examen_'+registros_examenes[0][i]['sol_examd_id']+'" name="chk_examen_'+registros_examenes[0][i]['sol_examd_id']+'" onChange="" disabled checked>';
                //html+='<img src="iconos/zoom.png" title="Ver Asignaci&oacute;n del Ex&aacute;men" style="cursor:pointer;" onClick="ver_asignacion();">';
                html+='<img src="iconos/layout.png"  style="cursor:pointer;" alt="Imprimir Hoja AT." title="Imprimir Hoja AT." onClick="imprimir_citacion2('+registros_examenes[0][i]['sol_examd_nomd_id']+');" />';
              }
              html+='</td>';

              if($('estado_solicitudes').value==1){
                  html+='<td style="text-align:center;">';
                    html+='<img src="iconos/script_edit.png"  style="cursor:pointer;" onClick="informe_examen('+registros_examenes[0][i]['sol_examd_id']+','+registros_examenes[0][i]['sol_examd_nomd_id']+');" />';
                  html+='</td>';
              }

              if(registros_examenes[0][i]['sol_examd_estado']=='1') {
                html+='<td style="text-align:center;">';
                html+='<img src="iconos/tick.png" title="Ex&aacute;men Realizado" style="cursor:pointer;" onClick="">';
                html+='</td>';
              } else {
                html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
              }
              html+='</tr>';
              cont=cont+1;
            }
          }
          nomd_id=registros_examenes[0][i]['sol_nomd_id_original'];
          pac_id=registros_examenes[0][i]['sol_pac_id'];
        }
        html+='</table>';
        html+='</td>';
        html+='</tr>';
        html+='</table>';
        html+='<input type="hidden" id="cant_'+registros_examenes[0][(i-1)]['sol_nomd_id_original']+'" name="cant_'+registros_examenes[0][(i-1)]['sol_nomd_id_original']+'" value="'+cont+'" />';
        html+='<br>';
        $j('#lista_examenes').html(html)
      }
    }
    //--------------------------------------------------------------------------
    informe_examen=function(sol_examd_id,nomd_id)
    {
        top=Math.round(screen.height/2)-325;
        left=Math.round(screen.width/2)-375;
        new_win = window.open('prestaciones/ingreso_nominas/form_informe.php?sol_examd_id='+sol_examd_id+'&nomd_id='+nomd_id,
        'win_nomina_form', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=1000, height=750, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
    //--------------------------------------------------------------------------

    graficar=function(tramite, total)
    {
      if((tramite*1)>0) {
        tramite=Math.round(tramite*100/total);
        resto=100-(tramite);
      } else {
        var html2="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0><tr>";
        html2+="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
        html2+="</tr></table>";
        return html2;
      }

      var html2="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0><tr>";
      if(tramite>0)
        html2+="<td style='width:$tramite%;background-color:#22CC22;'>&nbsp;</td>";

      if(resto>0)
        html2+="<td style='width:$resto%;background-color:#dddddd;'>&nbsp;</td>";

      html2+="</tr></table>";
      return html2;
    }
    //--------------------------------------------------------------------------
    limpiar_paciente = function() {
        $('pac_rut').value='';
        $('paciente').value='';
        $('pac_id').value=0;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    ver_examen_sol=function(index) {
      var titulo="Examenes Solicitados";
      top=Math.round(screen.height/2)-200;
      left=Math.round(screen.width/2)-375;
      var win = new Window("form_examenes",
      {
        className: "alphacube", top:top, left:left, width: 750, height: 300,
        title: '<img src="iconos/page_white_link.png"> '+titulo+'',
        minWidth: 500, minHeight: 150,
        maximizable: false, minimizable: false,
        wiredDrag: true, draggable: true,
        closable: true, resizable: false
      });
      win.setDestroyOnClose();
      win.setAjaxContent('prestaciones/ingreso_nominas/detalle_examenes.php',
      {
        method: 'post',
        parameters: 'sol_exam_id='+index,
        evalScripts: true
      });
      $("form_examenes").win_obj=win;
      win.showCenter();
      win.show(true);
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    check_examen=function(nomd_id,pos,sol_examd_id){
      if($j('#chk_examen_'+nomd_id+'_'+pos).is(':checked')) {
        if($('txt_nomd_id').value=='0') {
          $('txt_nomd_id').value=nomd_id;
          if($('txt_examenes').value=='') {
            $('txt_examenes').value=sol_examd_id;
          }
        } else {
          if($('txt_nomd_id').value==nomd_id) {
            if($('txt_examenes').value!='') {
              $('txt_examenes').value=$('txt_examenes').value+'|'+sol_examd_id;
            }
          } else {
            alert("No se puede seleccionar examenes de distintas solicitudes");
            $j('#chk_examen_'+nomd_id+'_'+pos).prop('checked', false);
            return;
          }
        }
      } else {
        if($('txt_nomd_id').value!='0') {
          if($('txt_examenes').value!='') {
            var exam = $('txt_examenes').value.split('|');
            $('txt_examenes').value='';
            if((exam.length)>1) {
              for(var i=0;i<exam.length;i++) {
                if(exam[i]!=sol_examd_id) {
                  if($('txt_examenes').value=='') {
                    $('txt_examenes').value=exam[i];
                  } else {
                    $('txt_examenes').value=$('txt_examenes').value+'|'+exam[i];
                  }
                }
              }
            } else {
              $('txt_nomd_id').value=0;
            }
          } else {
            $('txt_nomd_id').value=0;
          }
        }
      }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    imprimir_citacion2=function(nomd_id) {
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;
      new_win = window.open('prestaciones/ingreso_nominas/citaciones.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);
      new_win.focus();
    }

    imprimir_citacion3=function(nomd_id) {
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;
      new_win = window.open('prestaciones/ingreso_nominas/citaciones2.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);
      new_win.focus();
    }
</script>
<html>
  <center>
    <form id='form_reasignar'>
      <div class='sub-content' style='width:1100px;'>
        <div class='sub-content'>
          <img src='iconos/arrow_refresh.png' />
          <b>Listado de Solicitudes de Ex&aacute;menes para Agendamiento</b>
        </div>
        <div class='sub-content'>
          <table style='width:100%;font-size:10px;'>
            <tr>
              <td class='tabla_fila2' style='text-align:right;'>Paciente:</td>
              <td class='tabla_fila'>
                <input type='hidden' id='pac_id' name='pac_id' value='0' />
                <input type='text' size=10 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' style='font-size:16px;' />
              </td>
              <td class='tabla_fila'>
                <input type='text' id='paciente' name='paciente'  onDblClick='limpiar_paciente();' style='text-align:left;font-size:16px;' DISABLED size=40 />
              </td>
            </tr>
            <tr>
              <td class='tabla_fila2' style='text-align:right;'>Fecha Inicial:</td>
              <td class='tabla_fila'>
                <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;'  value='<?php echo $fecha2?>'>
                <img src='iconos/date_magnify.png' id='fecha1_boton'>
              </td>
            </tr>
            <tr>
              <td class='tabla_fila2' style='text-align:right;'>Fecha Final:</td>
              <td class='tabla_fila'>
                <input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo $fecha2?>'>
                <img src='iconos/date_magnify.png' id='fecha2_boton'>
              </td>
            </tr>
            <!--
            <tr>
              <td class='tabla_fila2' style='text-align:right;'>Profesional:</td>
              <td class='tabla_fila'>
                <input type='hidden' id='doc_id' name='doc_id' value='0' />
                <input type='text' size=10 id='doc_rut' name='doc_rut' value='' onDblClick='limpiar_profesional();' style='font-size:16px;'  />
              </td>
              <td class='tabla_fila'>
                <input type='text' id='profesional' name='profesional'  onDblClick='limpiar_profesional();' style='text-align:left;font-size:16px;' DISABLED size=40 />
              </td>
            </tr>
            -->
            <tr>
              <td class='tabla_fila2' style='text-align:right;'>Especialidad</td>
              <td class='tabla_fila' id='select_especialidades'>
                <select id='esp_id' name='esp_id' onChange='listar_solicitudes();'>
                  <!--<option value=-1 SELECTED>(Todas las Especialidades...)</option>-->
                  <option value='6117' SELECTED>IMAGENOLOG&Iacute;A HME</option>
                  <option value='6120' >LABARATORIO HME</option>
                  <!--<?php //echo $espechtml; ?>-->
                </select>
              </td>
              <td></td>
            </tr>
            <tr>
              <td class='tabla_fila2' style='text-align:right;'>Estado Solicitudes</td>
              <td class='tabla_fila' id='select_especialidades'>
                <select id='estado_solicitudes' name='estado_solicitudes' onChange='listar_solicitudes();'>
                  <option value="0" SELECTED>Solicitudes Pendientes</option>
                  <option value="1" >Solicitudes Agendadas</option>
                  <option value="2" >Solicitudes Suspendidas</option>
                </select>
              </td>
              <td></td>
            </tr>
            <tr>
              <td colspan='3'>
                <center>
                  <input type='button' id='lista_reasignar' onClick='listar_solicitudes();' value='Actualizar Listado...'>
                </center>
              </td>
            </tr>
          </table>
        </div>
        <input type="hidden" id="txt_nomd_id" name="txt_nomd_id" value="0" />
        <input type="hidden" id="txt_examenes" name="txt_examenes" value="" />
        <div class='sub-content2' style='height:350px;overflow:auto;' id='lista_examenes' >
        </div>
      </div>
    </form>
  </center>
</html>
<script>
  listar_solicitudes();
  /*
  seleccionar_profesional = function(d) {
    $('doc_rut').value=d[1];
    $('profesional').value=d[2].unescapeHTML();
    $('doc_id').value=d[0];
  }
  */
  /*
  limpiar_profesional = function(d) {
    $('doc_rut').value='';
    $('profesional').value='';
    $('doc_id').value=0;
  }
  */

  /*
  autocompletar_profesionales = new AutoComplete('doc_rut','autocompletar_sql.php',
  function() {
  if($('doc_rut').value.length<2) return false;
  return {
  method: 'get',
  parameters: 'tipo=doctor&nombre_doctor='+encodeURIComponent($('doc_rut').value)
  }
  }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_profesional);
  */

  seleccionar_paciente = function(d) {
    $('pac_rut').value=d[0];
    $('paciente').value=d[2].unescapeHTML();
    $('pac_id').value=d[4];
  }

  autocompletar_pacientes = new AutoComplete(
  'pac_rut', 'autocompletar_sql.php',
  function() {
  if($('pac_rut').value.length<2) return false;
	return {
  method: 'get',
  parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
  }
  }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);


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
</script>
