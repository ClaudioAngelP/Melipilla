<?php

  require_once('../../conectar_db.php');

  $asigna_id=$_GET['asigna_id']*1;
  
  list($a)=cargar_registros_obj("
    SELECT *, cupos_fecha::date AS cupos_fecha FROM cupos_asigna 
    JOIN cupos_atencion USING (cupos_id)
    JOIN interconsulta USING (inter_id)
    JOIN pacientes ON inter_pac_id=pac_id
    JOIN especialidades ON esp_id=cupos_esp_id
    WHERE asigna_id=$asigna_id
  ");
  
  list($pac)=cargar_registros_obj("
    SELECT * FROM pacientes WHERE pac_id=".$a['pac_id']."
  ");
  
  $fecha=date("d/m/Y", mktime(0,0,0,date('m')+1,date('d'),date('Y')));
  $fecha_hoy=date("d/m/Y");
  
  $horat='';
  
  if($a['asigna_asiste']=='1' AND $a['asigna_destino']==1) {
    list($c)=cargar_registros_obj("
      SELECT * FROM controles 
      WHERE inter_id=".$a['inter_id']." AND 
            control_id_anterior=".$a['control_id']."
    ");
    $fecha=$c['control_fecha'];
    $h=cargar_registros_obj("
      SELECT * FROM cupos_asigna 
      WHERE inter_id=".$a['inter_id']." AND control_id=".$c['control_id']."
    ");
    if($h) {
      $horat=$h[0]['asigna_hora'];
    } else {
      $horat='';
    }
  }
  
  $esp_id=$a['cupos_esp_id'];
  $doc_id=$a['cupos_doc_id'];

  if(!_cax(53))
    $cupos_restantes='(cupos_cantidad_n+cupos_cantidad_c)-(cupos_ocupados+cupos_cant_r)>0';
  else
    $cupos_restantes='(cupos_cantidad_n+cupos_cantidad_c)-(cupos_ocupados)>0';
  
  $fechas = cargar_registros_obj("
    SELECT * FROM (
    SELECT DISTINCT
      date_trunc('day', cupos_fecha) AS cupos_fecha,
      cupos_horainicio, cupos_horafinal, cupos_id, 
      cupos_cantidad_n, cupos_cantidad_c, cupos_extras, cupos_doc_id, 
      doc_rut, doc_paterno, doc_materno, doc_nombres, cupos_cant_r,
      (SELECT COUNT(*) FROM cupos_asigna AS ca 
        WHERE ca.cupos_id=cupos_atencion.cupos_id)::smallint AS cupos_ocupados
    FROM cupos_atencion
    JOIN doctores ON cupos_doc_id=doc_id
    WHERE cupos_esp_id=$esp_id AND cupos_fecha >= now()::date 
    AND cupos_doc_id=$doc_id
    ) AS foo WHERE ($cupos_restantes OR cupos_extras)
  ", true);
 

?>

<html>
<title>Registro M&eacute;dico/Estad&iacute;stico de Atenci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<script>

var __fecha='<?php echo $fecha; ?>';
var __fecha_hoy='<?php echo $fecha_hoy; ?>';

var fechas_ocupadas=<?php echo json_encode($fechas); ?>;

act = function() {

  var val=$('destino').value;
  
  if(val==1 && $('asiste').value==1) {
  
    $('destino').disabled=false;
    $('sel_fecha').style.display='';
    $('sel_horas').style.display='none';
  
  } else {

    $('fecha').value=__fecha_hoy;
    $('sel_fecha').style.display='none';
    $('sel_horas').style.display='none';
               
    if($('asiste').value!='1') {
      $('destino').value=1;
      $('destino').disabled=true;
    }
  
  }

}

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

      $('fecha').value=fecha;
      $('fecha_horas').innerHTML='<img src="../../imagenes/ajax-loader1.gif"> Cargando...';
      
      var fechac=fecha+' 00:00:00';
      
      var fnd=false;
      
      for(var i=0;i<fechas_ocupadas.length;i++)
          if(fechac==fechas_ocupadas[i].cupos_fecha) {
            fnd=true; break;
          }
      
      if(fnd) generar_select(fecha);
      else {
        $('fecha_horas').innerHTML='';
        $('sel_horas').style.display='none';
      }       
      
    }
  };


  function generar_select(fecha) {

    var myAjax=new Ajax.Request(
    '../lista_espera/cargar_cupos.php',
    {
      method: 'get',
      parameters: 'esp_id=<?php echo $esp_id; ?>&fecha='+fecha+
                  '&doc_id=<?php echo $doc_id; ?>',
      onComplete: function(resp) {
      
        datos=resp.responseText.evalJSON(true);
        
        $('sel_horas').style.display='';
        
        var html='<select id="horas" name="horas">';
        fecha=fecha+' 00:00:00';
          
        doc_id=<?php echo $doc_id; ?>;  
          
        for(var i=0;i<fechas_ocupadas.length;i++) {
        
          if(fecha==fechas_ocupadas[i].cupos_fecha && doc_id==fechas_ocupadas[i].cupos_doc_id) {
          
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
                
                html+='<option value="'+shr+':'+smin+'">'+shr+':'+smin+'</option>';
                
                min+=intervalo;
                
                if(min>59) {
                  hr++; min-=60;
                }
                
                if(hr>=hfinit && min>=mfinit) horas=false;
            }
            
            if(fechas_ocupadas[i].cupos_extras=='t')
              html+='<option value="00:00">Cupo Extra</option>';
                 
                    
          }
          
        }
        
        html+='</select>';
        
        $('fecha_horas').innerHTML=html;

      }
    }
    );


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

</style>


<body class='fuente_por_defecto popup_background'>

<form id='datos' name='datos' method='post' action='sql_registro.php'>
<input type='hidden' id='asigna_id' name='asigna_id' value='<?php echo $asigna_id; ?>'>

<div class='sub-content'>
<img src='../../iconos/layout.png'>
<b>Definir Registro Cl&iacute;nico/Estad&iacute;stico de Atenci&oacute;n</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;'>R.U.T:</td>
<td><b><?php echo $pac['pac_rut']; ?></b></td>
</tr>

<tr>
<td style='text-align:right;'>Nombre:</td>
<td><b><?php echo htmlentities($pac['pac_appat'].' '.$pac['pac_apmat'].' '.$pac['pac_nombres']); ?></b></td>
</tr>

<tr>
<td style='text-align:right;'>Atendido el:</td>
<td><b><?php echo $a['cupos_fecha'].'</b> '.$a['asigna_hora']; ?></td>
</tr>

</table>

</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:150px;'>Asiste:</td>
<td>
<select id='asiste' name='asiste' onClick='act();'>
<option value=1 <?php if($a['asigna_asiste']==1) echo 'SELECTED'; ?>>Si</option>
<option value=0 <?php if($a['asigna_asiste']==0) echo 'SELECTED'; ?>>No</option>
<option value=2 <?php if($a['asigna_asiste']==2) echo 'SELECTED'; ?>>Ausencia del M&eacute;dico</option>

</select>
</td>
</tr>

<tr>
<td style='text-align:right;width:150px;'>Destino:</td>
<td>
<select id='destino' name='destino' onChange='act();' 
<?php if($a['asigna_asiste']!=1) echo 'DISABLED'; ?> >
<option value=1 <?php if($a['asigna_destino']<=1) echo 'CHECKED'; ?>>Control</option>
<option value=2 <?php if($a['asigna_destino']==2) echo 'CHECKED'; ?>>Derivaci&oacute;n (Interconsulta Interna)</option>
<option value=4 <?php if($a['asigna_destino']==4) echo 'CHECKED'; ?>>Derivaci&oacute;n (Interconsulta Externa)</option>
<option value=3 <?php if($a['asigna_destino']==3) echo 'CHECKED'; ?>>Alta M&eacute;dica</option>
<option value=5 <?php if($a['asigna_destino']==5) echo 'CHECKED'; ?>>Retornado a A.P.S.</option>
</select>
</td>
</tr>

<tr id='sel_fecha' style='<?php if($a['asigna_asiste']!=1) echo 'display:none;'; ?>'>
<td style='text-align:right;' valign='top'>Fecha:</td>
<td>

<table cellpadding=0 cellspacing=0><tr><td>
<input type='hidden' id='fecha' name='fecha' value='<?php echo $fecha ?>'>
<div id='fecha1' style=''>
</div>
</td>
</tr></table>

</td>
</tr>

<tr id='sel_horas' style='<?php if($horat=='') echo 'display:none;'; ?>'>
<td style='text-align:right;' valign='top'>Hora:</td>
<td id='fecha_horas'>
<?php if($horat=='') { ?>
(En Lista de Espera para Control...)
<?php } else { echo '<font style="font-size:14px;font-weight:bold;">'.$horat.'</font>'; } ?>
</td>
</tr>
<tr>
<td colspan=2>
<center>
<input type='button' onClick='$("datos").submit();'
value='-- Guardar Destino Atenci&oacute;n --'>
</center>
</td>
</tr>

</table>

</div>

</form>


</body>
</html>

<script>

    Calendar.setup(
      {
        flat : "fecha1", 
        flatCallback : cambio_fecha,
        date: "<?php $f=explode('/',$fecha); echo $f[2].'/'.$f[1].'/'.$f[0]; ?>",
        dateStatusFunc : estado_fecha
      }
    );
    
</script>