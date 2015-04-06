<?php

  require_once('../../conectar_db.php');
  
  $esp_id=$_GET['esp_id']*1;
  $doc_id=$_GET['doc_id']*1;
  
  $fechas = cargar_registros_obj("
    SELECT DISTINCT
      date_trunc('day', cupos_fecha) AS cupos_fecha,
      cupos_horainicio, cupos_horafinal, cupos_id, 
      cupos_cantidad_n, cupos_cantidad_c, cupos_extras,
      esp_desc, cupos_cant_r
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    WHERE cupos_doc_id=$doc_id ORDER BY cupos_fecha, cupos_horainicio;
  ", true);

  $fechas_ausencias = cargar_registros_obj("
    SELECT DISTINCT
      ausencia_fechainicio, ausencia_fechafinal
    FROM ausencias_medicas
    WHERE (doc_id=$doc_id OR doc_id=0);
  ", true);
  
  $fechas_ausente=Array();
  
  for($i=0;$i<count($fechas_ausencias);$i++) {
  
    if($fechas_ausencias[$i]['ausencia_fechafinal']=='')
      $fechas_ausente[count($fechas_ausente)]=$fechas_ausencias[$i]['ausencia_fechainicio'];
    else {
      $finicio=explode('/',$fechas_ausencias[$i]['ausencia_fechainicio']);
      $ffinal=explode('/',$fechas_ausencias[$i]['ausencia_fechafinal']);
      
      $fi=mktime(0,0,0,$finicio[1],$finicio[0],$finicio[2]);
      $ff=mktime(0,0,0,$ffinal[1],$ffinal[0],$ffinal[2]);
      
      for(;$fi<=$ff;$fi+=86400) {
          $fechas_ausente[count($fechas_ausente)]=date('d/m/Y',$fi);
      }           
    }
  
  }
  
  $medico=cargar_registro("
    SELECT * FROM doctores 
    WHERE doc_id=$doc_id
  ");
  
  $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id");
  
  $horashtml='';
  
  for($i=8;$i<18;$i++) {
    
    ($i<10) ? $h='0'.$i : $h=$i;
    
    $horashtml.='<option value="'.$h.':00">'.$h.':00</option>';
    $horashtml.='<option value="'.$h.':30">'.$h.':30</option>';
    
  }
  
?>

<html>

<title>Definir Cupos para Atenci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<script>

  var calendario;
  
  var fechas_ocupadas = <?php echo json_encode($fechas); ?>;
  var fechas_ausente = <?php echo json_encode($fechas_ausente); ?>;
  
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
  
    calendario=Calendar.setup(
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

		/*if(fechas_ausente!==false)  
		for(var i=0;i<fechas_ausente.length;i++) {
		  
		  if(fecha==fechas_ausente[i]) return;
		  
		}*/

      
      $('fecha_horas').style.display='';
      $('no_fechas').style.display='none';
      $('mostrar_fecha').innerHTML=fecha;
      $('mostrar_fecha2').innerHTML=fecha;
      $('fecha').value=fecha;
      
      cargar_horas(fecha);
    
    }
  };
  
  cargar_horas=function(fecha) {

    var html='<table style="width:100%;font-size:12px;">';
    html+='<tr class="tabla_header"><td>Inicio</td><td>T&eacute;rmino</td>';
    html+='<td>N</td><td>C</td><td>R</td><td>E</td>';
    html+='<td>Especialidad</td><td>Acci&oacute;n</td></tr>';
    
    var c=0;
    
    fecha+=' 00:00:00';
    
    var ver_replicar=false;
    
    if(fechas_ocupadas!==false)
    for(var i=0;i<fechas_ocupadas.length;i++) {
    
      if(fechas_ocupadas[i].cupos_fecha==fecha) {
      
        (c%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
        c++;
        
        if(fechas_ocupadas[i].cupos_extras=='t') 
          var e='<center><img src="../../iconos/tick.png" width=8 height=8></center>';
        else
          var e='<center><img src="../../iconos/cross.png" width=8 height=8></center>';
        
        html+='<tr class="'+clase+'" ';
        html+='onMouseOver="this.className=\'mouse_over\';" ';
        html+='onMouseOut="this.className=\''+clase+'\';">';
        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_horainicio+'</td>';
        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_horafinal+'</td>';
        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_cantidad_n+'</td>';
        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_cantidad_c+'</td>';
        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_cant_r+'</td>';
        html+='<td style="text-align:center;">'+e+'</td>';
        html+='<td>'+fechas_ocupadas[i].esp_desc+'</td>';
        html+='<td><center><img src="../../iconos/delete.png" ';
        html+='style="cursor:pointer;" onClick="eliminar_rango('+fechas_ocupadas[i].cupos_id+');">';
        html+='</center></td></tr>';
        
        ver_replicar=true;
      
      }
    
    }
    
    if(ver_replicar) {
      $('replicar_agenda').style.display='';
    } else {
      $('replicar_agenda').style.display='none';
    }
    
    html+='</table>';
    
    $('rango').innerHTML=html;
     
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
    var fecha3=d+'/'+m+'/'+y;
    
    if(fechas_ausente!==false)  
    for(var i=0;i<fechas_ausente.length;i++) {
      
      if(fecha3==fechas_ausente[i]) clase='ausente';
      
    }
    
    if(fechas_ocupadas!==false)  
    for(var i=0;i<fechas_ocupadas.length;i++) {
      
      if(fecha2==fechas_ocupadas[i].cupos_fecha) clase='fechaset';
      
    }
  
    if(clase=='')
      return false;
    else
      return clase;
  
  }  
  
  function guardar_cupos() {
  
    params = $('form_cupos').serialize();
    params += '&fechas='+encodeURIComponent(fechas_sel.toJSON()); 
  
    var myAjax=new Ajax.Request(
    'sql_cupos.php',
    {
      method: 'post',
      parameters: params,
      onComplete: function (resp) {
        try {
          datos=resp.responseText.evalJSON(true);          
                    
        } catch(err) {
          alert(err);
        }
      }
    }
    );
  
  }
  
  agregar_rango=function() {
  
    var myAjax=new Ajax.Request(
    'sql_cupos.php',
    {
      method:'post',
      parameters:$('form_cupos').serialize(),
      onComplete: function(resp) {
		
		if(resp.responseText=='1') {
			alert('ERROR: Ya hay cupos creados para este profesional en el rango especificado.');
			return;
		}

		if(resp.responseText=='2') {
			alert('ERROR: Cupos sin nomina, contacte al administrador.');
			return;
		}
		  
        fechas_ocupadas=resp.responseText.evalJSON(true);
        cargar_horas($('fecha').value);
      }
    }
    );
  
  }
  
  eliminar_rango=function(cupos_id) {
  
    var myAjax=new Ajax.Request(
    'sql_cupos.php',
    {
      method:'post',
      parameters: 'eliminar=1&doc_id=<?php echo $doc_id; ?>&cupos_id='+cupos_id,
      onComplete: function(resp) {
        fechas_ocupadas=resp.responseText.evalJSON(true);
        cargar_horas($('fecha').value);
      }
    }
    );
  
  
  }
  
  calcular_total_cupos=function() {
  
    $('cantt').value=($('cantn').value*1)+($('cantc').value*1);
    
    $('cantti').value=$('cantt').value;
  
  }
  
  calcular_total_tipo=function() {
  
    var d=($('cantti').value*1)+($('cantth').value*1)+($('cantte').value*1);
    
    if($('cantt').value!=d) {
    
      $('cantt').style.color='red';
    
    } else {
    
      $('cantt').style.color='';
    
    }
  
  }
  
  replicar_agenda = function() {
  
	 var params='doc_id=<?php echo $doc_id; ?>&'+$('fecha').serialize();
   
   var l=(screen.width/2)-290;
	 var t=(screen.height/2)-200;
  
    replicar = window.open('replicar_agenda.php?'+params,
		'replicar', 'left='+l+',top='+t+',width=580,height=400,status=0,scrollbars=1');
			
		replicar.focus();

  
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
  }

  .ausente {
    background-color: black;
  }

</style>

<body class='fuente_por_defecto popup_background' onLoad='init();'>

<div class='sub-content'>
<table>
<tr><td style='text-align:right;'>
Profesional:</td>
<td style='font-weight:bold;'><?php echo htmlentities($medico['doc_rut'].' - '.$medico['doc_paterno'].' '.$medico['doc_materno'].' '.$medico['doc_nombres']); ?></td>
</tr>
<tr><td style='text-align:right;'>
Especialidad:</td>
<td style='font-weight:bold;'><?php echo htmlentities($esp['esp_desc']); ?></td>
</tr>
</table>
</div>

<table><tr>
<td valign='top'>
<div id='calendario' name='calendario'>

</div>

<center>
<input type='button' id='replicar_agenda' name='replicar_agenda'
style='display:none;' onClick='replicar_agenda();' 
value='Replicar este Agendamiento...'>
</center>

</td>

<td>

<div class='sub-content2'
style='width:450px;height:360px;overflow:auto;'>

<table style='width:100%; height:300px;' id='no_fechas'>
<tr><td>
<center>Seleccione fecha para crear <br>cupos de atenci&oacute;n.</center>
</td></tr>
</table>


<form id='form_cupos' name='form_cupos' onClick=''>

<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>'>
<input type='hidden' id='doc_id' name='doc_id' value='<?php echo $doc_id; ?>'>
<input type='hidden' id='fecha' name='fecha' value=''>

<div id='fecha_horas' name='fecha_horas' 
style='width:100%; height:350px;overflow:auto;display:none;'>

<div class='sub-content'>
Definir Rango de Horas para el <span id='mostrar_fecha' style='font-weight:bold;font-size:14px;'></span>
</div>

<table style='width:100%;'>

<tr>
<td style='text-align:right;'>Hora:</td>
<td colspan=4>
<select id='desde' name='desde'>
<?php echo $horashtml; ?>
</select>

hasta

<select id='hasta' name='hasta'>
<?php echo $horashtml; ?>
</select>

</td>
</tr>

<tr>

<td style='text-align:center;font-weight:bold;font-size:11px;' colspan=5>Cupos de Atenci&oacute;n</td>

</tr>

<tr>
<td style='text-align:right;'>Nuevos:</td>
<td>
<input type='text' id='cantn' name='cantn' size=5 
style='text-align:center;'
onKeyUp='calcular_total_cupos();'>
</td>

<td colspan=3>
<input type='checkbox' id='extras' name='extras'>
Permitir Cupos Extras
</td>
</tr>

<tr>
<td style='text-align:right;'>Control:</td>
<td>
<input type='text' id='cantc' name='cantc' size=5 
style='text-align:center;'
onKeyUp='calcular_total_cupos();'>
</td>

<td style='text-align:center;font-size:10px;'>Reservados</td>
<td style='text-align:center;font-size:10px;'>Institucional</td>
<td style='text-align:center;font-size:10px;'>Honorarios</td>
<td style='text-align:center;font-size:10px;'>Especiales</td>

</tr>

<tr>
<td style='text-align:right;'>Total:</td>
<td>
<input type='text' id='cantt' name='cantt' size=5 DISABLED
style='text-align:center;'>
</td>

<td style='text-align:center;'>
<input type='text' id='canttr' name='canttr' size=5
onKeyUp='calcular_total_tipo();'
style='text-align:center;' value='0'>
</td>

<td style='text-align:center;'>
<input type='text' id='cantti' name='cantti' size=5
onKeyUp='calcular_total_tipo();'
style='text-align:center;'>
</td>

<td style='text-align:center;'>
<input type='text' id='cantth' name='cantth' size=5
onKeyUp='calcular_total_tipo();'
style='text-align:center;'>
</td>

<td style='text-align:center;'>
<input type='text' id='cantte' name='cantte' size=5
onKeyUp='calcular_total_tipo();'
style='text-align:center;'>

</td>

</tr>

<tr><td colspan=5>
<center>
<input type='button' value='Agregar Rango...' onClick='agregar_rango();'>
</center>
</td></tr>

</table>

<div class='sub-content'>
Horas de Atenci&oacute;n durante el d&iacute;a <span id='mostrar_fecha2' style='font-weight:bold;font-size:14px;'></span>
</div>

<div class='sub-content2' style='height:100px;overflow:auto;' id='rango'>

</div>

</div>

</form>

</div>

</td>

</tr>
</table>


</body>
</html>