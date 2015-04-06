<?php

  require_once('../../conectar_db.php');
  
  $inter_id=$_GET['inter_id']*1;
  $control_id=$_GET['control_id']*1;
  
  if(isset($_GET['doc_id']) AND ($_GET['doc_id']*1)!=0) {
    $doc_where=' AND cupos_doc_id='.$_GET['doc_id']*1;
  } else {
    $doc_where='';
  }
  
  list($inter)=cargar_registros("
    SELECT 
    inter_especialidad, 
    inter_pac_id 
    FROM interconsulta WHERE inter_id=$inter_id
  ", true);

  $esp_id=$inter[0];
  $pac_id=$inter[1];
  
  list($especialidad)=cargar_registros("
    SELECT esp_desc FROM especialidades WHERE esp_id=$esp_id
  ", true);

  list($paciente)=cargar_registros("
    SELECT 
      pac_rut, 
      pac_appat || ' ' || pac_apmat || ' ' || pac_nombres 
    FROM pacientes WHERE pac_id=$pac_id
  ", true);
  
  if(!_cax(53))
    $cupos_restantes='(cupos_cantidad_n+cupos_cantidad_c)-(cupos_ocupados+cupos_cant_r)>0';
  else
    $cupos_restantes='(cupos_cantidad_n+cupos_cantidad_c)-(cupos_ocupados)>0';
    
  $fechas = cargar_registros_obj("
    SELECT * FROM (
    SELECT DISTINCT
      date_trunc('day', cupos_fecha) AS cupos_fecha,
      cupos_horainicio, cupos_horafinal, cupos_id, 
      cupos_cantidad_n::smallint, cupos_cantidad_c::smallint, 
      cupos_extras, cupos_doc_id, 
      doc_rut, doc_paterno, doc_materno, doc_nombres, cupos_cant_r,
      (SELECT COUNT(*) FROM cupos_asigna AS ca 
        WHERE ca.cupos_id=cupos_atencion.cupos_id)::smallint AS cupos_ocupados
    FROM cupos_atencion
    JOIN doctores ON cupos_doc_id=doc_id
    WHERE cupos_esp_id=$esp_id AND cupos_fecha >= now()::date $doc_where
    ) AS foo WHERE ($cupos_restantes OR cupos_extras)
  ", true);
  
  $chk=cargar_registro("
    SELECT * FROM cupos_asigna 
    WHERE inter_id=$inter_id AND control_id=$control_id
  ");
  
?>

<html>

<title>Definir Hora de Atenci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<script>

  var asigna_id=<?php echo ($chk)?$chk['asigna_id']:'0'; ?>;
  var fechas_ocupadas = <?php echo json_encode($fechas); ?>;
  var hrs_sel;
  
  for(var i=0;i<=23;i++) {
    for(var j=0;j<60;j+=15) {
        
        if(i<10) si='0'+i; else si=i;
        if(j<10) sj='0'+j; else sj=j;
        
        var hr = si+':'+sj; 
        hrs_sel+='<option value="'+hr+'">'+hr+'</option>';
        
    }
  }
  
  function init() {
  
    Calendar.setup(
      {
        flat : "calendario", 
        flatCallback : cambio_fecha,
        dateStatusFunc : estado_fecha
      }
    );
    
  }
  
  function cambio_fecha(calendar) {
    
    // Beware that this function is called even if the end-user only
    // changed the month/year.  In order to determine if a date was
    // clicked you can use the dateClicked property of the calendar:
    
    if (calendar.dateClicked) {
  
      var y = calendar.date.getFullYear();
      var m = calendar.date.getMonth();     // integer, 0..11
      var d = calendar.date.getDate();      // integer, 1..31
  
      if(d<10) d='0'+d;
      if((m+1)<10) m='0'+(m+1); else m=(m+1);
    
      var fecha=d+'/'+m+'/'+y;
      
      generar_planilla(fecha, -1); 
      
      $('fecha').value=fecha;
      $('fecha_horas').style.display='';
      $('no_fechas').style.display='none';
    
    }
  };

  function estado_fecha(date, y, m, d) {
  
    // Devuelve 'disabled' para desactivar la fecha...
    // Devuelve false para dejar la fecha intacta...
    // Devuelve '(string)' para usar esa clase...
  
    var clase='';
    var fecha=d+'/'+(m+1)+'/'+y;
    
    if(d<10) d='0'+d;
    if((m+1)<10) m='0'+(m+1); else m=(m+1);
     
    var fecha2=d+'/'+m+'/'+y+' 00:00:00';
        
    for(var i=0;i<fechas_ocupadas.length;i++) {
      
      if(fecha2==fechas_ocupadas[i].cupos_fecha) clase='fechaset';
      
    }
  
    if(clase=='')
      return false;
    else
      return clase;
  
  }
  
  function generar_planilla(fecha, doc_id) {
  
    fechat=fecha+' 00:00:00';

    if(doc_id==-1) {

      var docs=new Array();
    
      for(var i=0;i<fechas_ocupadas.length;i++) {
        
        if(fechat==fechas_ocupadas[i].cupos_fecha) {
          
          var fnd=false;
          
          for(var n=0;n<docs.length;n++)
            if(docs[n][0]==fechas_ocupadas[i].cupos_doc_id) fnd=true;
          
          if(!fnd) {
            var num=docs.length;
            docs[num]=new Array();
            docs[num][0]=fechas_ocupadas[i].cupos_doc_id;
            docs[num][1]=fechas_ocupadas[i].doc_rut;
            docs[num][2]=fechas_ocupadas[i].doc_paterno;
            docs[num][3]=fechas_ocupadas[i].doc_materno;
            docs[num][4]=fechas_ocupadas[i].doc_nombres;
          }
          
        }
        
      }

      if(docs.length==1) {
      
        doc_id=docs[0][0];
      
      } else {
      
        $('datos_medico').style.display='none';
      
        var html='<center><br><h2>Seleccione M&eacute;dico</h2><br>';
        html+='<table style="width:100%;" class="lista_small">';
        html+='<tr class="tabla_header">';
        html+='<td>R.U.T. M&eacute;dico</td>';
        html+='<td>Nombre M&eacute;dico</td></tr>';
        
        for(var n=0;n<docs.length;n++) {
          
          (n%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
          
          html+='<tr class="'+clase+'" style="cursor:pointer;"';
          html+='onClick="generar_planilla(\''+fecha+'\', '+docs[n][0]+')" ';
          html+='onMouseOver="this.className=\'mouse_over\';" ';
          html+='onMouseOut="this.className=\''+clase+'\'">';
          html+='<td style="text-align:right;">'+docs[n][1]+'</td>';
          html+='<td style="font-weight:bold;">'+docs[n][2]+' ';
          html+=docs[n][3]+' ';
          html+=docs[n][4]+'</td>';
          html+='</tr>';
          
        }
        
        html+='</table>';
        
        $('fecha_horas').innerHTML=html;
    
        $('fecha_horas').style.display='';
        $('no_fechas').style.display='none';

        return;
      
      }
    
    }
    
    var myAjax=new Ajax.Request(
    'cargar_cupos.php',
    {
      method: 'get',
      parameters: 'esp_id=<?php echo $esp_id; ?>&fecha='+fecha+
                  '&doc_id='+doc_id,
      onComplete: function(resp) {
      
        datos=resp.responseText.evalJSON(true);
        
        var html='<table style="width:100%;" class="horas">';
        fecha=fecha+' 00:00:00';
          
        $('doc_id').value=doc_id;  
          
        for(var i=0;i<fechas_ocupadas.length;i++) {
        
          if(fecha==fechas_ocupadas[i].cupos_fecha && doc_id==fechas_ocupadas[i].cupos_doc_id) {
          
            $('datos_medico').style.display='';
            var html2='<center><b>'+fechas_ocupadas[i].doc_rut+'</b><br><br>';
            html2+=fechas_ocupadas[i].doc_paterno+' '+fechas_ocupadas[i].doc_materno+' '+fechas_ocupadas[i].doc_nombres+'</center>';
          
            $('datos_medico').innerHTML=html2;
          
            hinit=fechas_ocupadas[i].cupos_horainicio.split(':');
            hinit=hinit[0]*1;
            
            minit=fechas_ocupadas[i].cupos_horainicio.split(':');
            minit=minit[1]*1;
            
            hfinit=fechas_ocupadas[i].cupos_horafinal.split(':');
            hfinit=hfinit[0]*1;
            
            mfinit=fechas_ocupadas[i].cupos_horafinal.split(':');
            mfinit=mfinit[1]*1;
            
            interinit=(hinit*60)+minit;
            interfinit=(hfinit*60)+mfinit;
            
            total_cupos=((fechas_ocupadas[i].cupos_cantidad_n*1)+(fechas_ocupadas[i].cupos_cantidad_c)*1);
            minutos=(interfinit-interinit);
            
            intervalo=Math.floor(minutos/total_cupos);
            
            cupo_id=fechas_ocupadas[i].cupos_id*1;
            
            horas=true;
            hr=hinit; min=minit;
            
            while(horas) {
                
                if(hr<10) shr='0'+hr; else shr=hr;
                if(min<10) smin='0'+min; else smin=min;
            
                var fnd=false;
                var cmp=shr+':'+smin+':00';
            
                for(var j=0;j<datos.length;j++) {
                  if(datos[j].asigna_hora==cmp) {
                    fnd=true; break;
                  }  
                }
            
                if(!fnd) 
                  clase='libre';
                else if (asigna_id!=0 && datos[j].asigna_id==asigna_id)
                  clase='seleccionado'; 
                else 
                  clase='ocupado';
            
                html+='<tr onMouseOver="this.className=\'mouse_over\';"';
                html+=' onMouseOut="this.className=\'\';" style="cursor:pointer;" ';
                if(!fnd)
                html+='onClick="tomar_hora(\''+shr+'\',\''+smin+'\', '+cupo_id+');"';
                
                html+='><td style="width:80px;text-align:center;"';
                html+='>'+shr+':'+smin+'</td>';
                html+='<td id="hr_'+shr+'_'+smin+'" class="'+clase+'">';
                
                if(fnd) {
                  html+=datos[j].pac_appat+' '+datos[j].pac_apmat+' '+datos[j].pac_nombres;
                } else
                  html+='Asignar Hora...';
                
                html+='</td><td style="text-align:center;">';
                
                if(fnd)
                  if(datos[j].control_id!=0) html+='C'; else html+='N';
                else
                  html+='&nbsp;';
                  
                html+='</td></tr>';
                
                min+=intervalo;
                
                if(min>59) {
                  hr++; min-=60;
                }
                
                if(hr>=hfinit && min>=mfinit) horas=false;
            }
            
            if(fechas_ocupadas[i].cupos_extras=='t') {
            
                for(var j=0;j<datos.length;j++) {
                  if(datos[j].asigna_hora=='00:00:00') {
                    
                    html+='<tr onMouseOver="this.className=\'mouse_over\';"';
                    html+=' onMouseOut="this.className=\'\';" style="cursor:pointer;" ';
                    html+='><td style="width:80px;text-align:center;"';
                    html+='>00:00</td>';
                    html+='<td id="hr_'+shr+'_'+smin+'" class="ocupado">';
                    html+=datos[j].pac_appat+' '+datos[j].pac_apmat+' '+datos[j].pac_nombres;
                    html+='</td><td style="text-align:center;">';
                    
                    if(datos[j].control_id!=0) html+='C'; else html+='N';
                    
                    html+='</td></tr>';
                        
                  }  
                }
            
                html+='<tr onMouseOver="this.className=\'mouse_over\';"';
                html+=' onMouseOut="this.className=\'\';" style="cursor:pointer;"';
                html+='onClick="tomar_hora(\'00\',\'00\', '+cupo_id+');">'
                html+='<td style="width:80px;text-align:center;"';
                html+='>00:00</td>';
                html+='<td id="hr_00_00" class="libre">';
                html+='Asignar Hora Extra...</td><td>&nbsp;</td></tr>';
            
            }
                    
          }
          
        }
        
        html+='</table>';
        
        $('fecha_horas').innerHTML=html;
        
        $('fecha_horas').style.display='';
        $('no_fechas').style.display='none';


        if(datos) {
            for(var n=0;n<datos.length;n++) 
              ubicar_lista(datos[n]);
        }

      }
    }
    );

    //ver_asignados(fecha, doc_id);

  }
  
  tomar_hora = function(hr, min, id) {
  
    var params='inter_id=<?php echo $inter_id; ?>&';
    params+='control_id=<?php echo $control_id; ?>&';
    params+='hr='+hr+'&min='+min+'&id='+id;
  
    var myAjax=new Ajax.Request(
    'sql.php',
    {
      method: 'post',
      parameters: params,
      onComplete: function(resp) {
        try {
          datos=resp.responseText.evalJSON(true);
          if(datos) {
            alert('Cupo asignado exitosamente.');
            asigna_id=datos[1]*1;
            generar_planilla($('fecha').value, $('doc_id').value);
            var func = window.opener.listado.bind(window.opener);
            func();
          } else {
            alert('ERROR: '+resp.responseText);
          }
        } catch(err) {
          alert(err);
        }
      }
    }
    );
  
  }

  liberar_hora = function() {
  
    var params='inter_id=<?php echo $inter_id; ?>&';
    params+='control_id=<?php echo $control_id; ?>&';
    params+='asigna_id=<?php echo $chk['asigna_id']; ?>';
  
    var myAjax=new Ajax.Request(
    'sql.php',
    {
      method: 'post',
      parameters: params,
      onComplete: function(resp) {
        try {
          if(resp.responseText=='true') {
            alert('Agendamiento liberado exitosamente.');
            var func = window.opener.listado.bind(window.opener);
            func();
            //window.close();
          } else {
            alert('ERROR: '+resp.responseText);
          }
        } catch(err) {
          alert(err);
        }
      }
    }
    );
  
  }

  eliminar_hora = function() {
  
    var conf=confirm("&iquest;Desea eliminar al paciente de la lista de espera? -- No hay opciones para deshacer.".unescapeHTML());
    
    if(!conf) return;
    
    var params='eliminar=1&inter_id=<?php echo $inter_id; ?>&';
    params+='control_id=<?php echo $control_id; ?>';
    <?php if($chk) { ?>
    params+='&asigna_id=<?php echo $chk['asigna_id']; ?>';
    <?php } ?>
  
    var myAjax=new Ajax.Request(
    'sql.php',
    {
      method: 'post',
      parameters: params,
      onComplete: function(resp) {
        try {
          if(resp.responseText=='true') {
            alert('Agendamiento liberado exitosamente.');
            var func = window.opener.listado.bind(window.opener);
            func();
            //window.close();
          } else {
            alert('ERROR: '+resp.responseText);
          }
        } catch(err) {
          alert(err);
        }
      }
    }
    );
  
  }

  
  function ubicar_lista(pac) {
  
    hr = pac[0].split(':');
    
    $('hr_'+hr[0]+'_'+hr[1]).innerHTML=pac[1]+' '+pac[2]+' '+pac[3];
    $('hr_'+hr[0]+'_'+hr[1]).className='ocupado';
    
  }
  
</script>

<style>

  .fechaset {
    background-color: #99EE99;
    font-weight:bold;
    color: #FFAAAA;
  }
  
  .horas {
  
    background-color: #FFFFFF;
    border: 1px solid black;
  
  }
  
  .horas td {
    border: 1px solid black;
  }
  
  .libre {
    background-color: #99EE99;
  }
  
  .ocupado {
    background-color: #DDDDDD;
    font-size:10px;
  }

  .seleccionado {
    background-color: #9999EE;
    font-size:10px;
  }

</style>

<body class='fuente_por_defecto popup_background' onLoad='init();'>

<div class='sub-content'>
<table>
<tr><td style='text-align:right;'>
Especialidad:</td>
<td style='font-weight:bold;'><?php echo $especialidad[0]; ?></td>
</tr>
<tr><td style='text-align:right;'>
Paciente:</td>
<td style='font-weight:bold;'><?php echo $paciente[0].' '.$paciente[1]; ?></td>
</tr>
</table>
</div>

<table style='width:100%;'><tr>
<td valign='top'>
<div id='calendario' name='calendario'>

</div>

<input type='hidden' id='doc_id' name='doc_id' value=-1>
<br>
<div class='sub-content' id='datos_medico' style='display:none;'>


</div>

<center>

<?php if($chk) { ?>
<input type='button' value=' -- Devolver a Lista de Espera -- ' 
onClick='liberar_hora();'><br>
<?php } ?>

<input type='button' value=' -- Eliminar de Lista de Espera -- ' 
onClick='eliminar_hora();'>

</center>

</td>

<td>

<div class='sub-content2'
style='width:430px;height:330px;overflow:auto;'>

<table style='width:100%; height:320px;' id='no_fechas'>
<tr><td>
<center>Seleccione Fecha para tomar hora.</center>
</td></tr>
</table>


<form id='form_cupos' name='form_cupos' onClick=''>

<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>'>
<input type='hidden' id='doc_id' name='doc_id' value='<?php echo $doc_id; ?>'>
<input type='hidden' id='fecha' name='fecha' value=''>

<div id='fecha_horas' name='fecha_horas' 
style='width:100%; height:320px;overflow:auto;display:none'>



</div>

</form>

</div>

</td>

</tr>
</table>


</body>
</html>
