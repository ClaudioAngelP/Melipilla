<?php

  require_once('../../conectar_db.php');
  
  $esp_id=$_GET['esp_id']*1;
  $tipo=$_GET['tipo'];
  
  if($tipo=='N') $stipo=' (Nuevos)'; else $stipo=' (Controles)';
  
  $esp=cargar_registro("
    SELECT * FROM especialidades WHERE esp_id=$esp_id
  ");
  
  $lista=cargar_registros_obj("
    SELECT 
    date_trunc('day', inter_ingreso)::date,
    pac_rut,
    pac_appat, pac_apmat, pac_nombres,
    prior_desc,
    interconsulta.inter_id,
    date_trunc('minute', cupos_fecha + asigna_hora)
    FROM interconsulta 
    JOIN pacientes ON inter_pac_id=pac_id
    JOIN prioridad ON prior_id=inter_prioridad
    LEFT JOIN cupos_asigna 
          ON interconsulta.inter_id=cupos_asigna.inter_id
    LEFT JOIN cupos_atencion 
          ON cupos_asigna.cupos_id=cupos_atencion.cupos_id
    WHERE inter_estado=1 AND inter_especialidad=$esp_id
    AND asigna_id IS NULL
    ORDER BY inter_ingreso 
  ");

  if($tipo=='N')
    $twhere='AND cupos_asigna.control_id=0';
  else
    $twhere='AND NOT cupos_asigna.control_id=0';

  $cupos=cargar_registros_obj("
    SELECT DISTINCT
      cupos_id, 
      cupos_cantidad_n, 
      cupos_cantidad_c,
      (
        SELECT COUNT(*) FROM cupos_asigna 
        WHERE cupos_atencion.cupos_id=cupos_asigna.cupos_id
          $twhere
      ) AS utilizados
    FROM cupos_atencion
    WHERE cupos_esp_id=$esp_id
  ");
  
  $cdisp=0;
  
  for($i=0;$i<count($cupos);$i++)
    if($tipo=='N')
      $cdisp+=$cupos[$i]['cupos_cantidad_n']-$cupos[$i]['utilizados'];
    else
      $cdisp+=$cupos[$i]['cupos_cantidad_c']-$cupos[$i]['utilizados'];
    
  $p=($cdisp*100)/count($lista);
  
  if($p>100) $p=100;
  
?>

<html>

<title>Agendamiento Autom&aacute;tico de Cupos de Atenci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<script>

  pacs=window.opener.lista_espera;
  cnt=0;
  npacs=0;
  cupos=<?php echo $cdisp; ?>;
  
  function listar_pacs() {
  
    npacs=pacs.length;
  
    $('numero_pacs').innerHTML=npacs;
    $('porc').innerHTML=number_format(((cupos*100)/npacs),2,',','')+'%';
  
    var html='<table style="width:100%;" class="lista_small">';
    html+='<tr class="tabla_header"><td>RUT</td><td>Nombre</td><td colspan=2>Plazo</td><td>Hora Asignada</td></tr>';
    
    for(var i=0;i<pacs.length;i++) {
    
      (i%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
    
      if(pacs[i][9]<30) {
        var color='color:red;font-weight:bold;';
      } else if(pacs[i][9]>=30 && pacs[i][9]<60) {
        var color='color:orange;';
      } else if(pacs[i][9]>=60) {
        var color='color:green;';
      }
    
      html+='<tr class="'+clase+'">';
      html+='<td style="text-align:right;">'+pacs[i][1]+'</td>';
      html+='<td>'+pacs[i][2]+' '+pacs[i][3]+' '+pacs[i][4]+'</td>';
      html+='<td style="text-align:center;">'+number_format(pacs[i][8])+'</td>';
      html+='<td style="text-align:center;'+color+'">'+number_format(pacs[i][9])+'%</td>';
      html+='<td id="hora_'+i+'" style="text-align:center;font-weight:bold;">&nbsp;</td>';
      html+='</tr>';
    
    }
    
    html+='</table>';
    
    $('lista').innerHTML=html;
  
  }
  
  
  
  function iniciar_agenda() {
      
    $('iniciar').style.display='none';
    $('cuenta').style.display='';
  
    var pct=cnt*100/pacs.length;
  
    $('contador').innerHTML=number_format(pct)+'%';

    if(cnt>=pacs.length) {
      alert('Proceso finalizado exitosamente.');
      $('cuenta').style.display='none';
      var func = window.opener.listado.bind(window.opener);
      func();
      return;
    }
  
    p=pacs[cnt].toJSON();
    
    $('hora_'+cnt).innerHTML='<img src="../../imagenes/ajax-loader3.gif">';
    
    var myAjax=new Ajax.Request(
    'agendar_cupo.php',
    {
      method:'post', parameters: 'esp_id=<?php echo $esp_id; ?>&p='+encodeURIComponent(p),
      onComplete: function(resp) {
      
          $('hora_'+cnt).innerHTML=resp.responseText;
      
          cnt++;
          iniciar_agenda();
      
      }
    });
  
  }
  
</script>


<body class='fuente_por_defecto popup_background' onLoad='listar_pacs();'>


<div class='sub-content' style='text-align:center;'>
<u>Agendamiento Autom&aacute;tico</u>
<h3><?php echo htmlentities($esp['esp_desc']); echo $stipo; ?></h3>
</div>

<div class='sub-content2'>

<center>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:250px;'>N&uacute;mero de Pacientes en Espera:</td>
<td id='numero_pacs'>

</td>
</tr>

<tr>
<td style='text-align:right;'>N&uacute;mero de Cupos Disponibles:</td>
<td><?php echo number_format($cdisp); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Demanda Cubierta por Oferta:</td>
<td id='porc' style='font-weight:bold;'></b></td>
</tr>

</table>

</center>


</div>

<div class='sub-content2' id='lista' style='height:180px;overflow:auto;'>

</div>

<span id='iniciar'>
  <center>
    <div class='boton'>
		<table><tr><td>
		<img src='../../iconos/cog_go.png'>
		</td><td>
		<a href='#' onClick='iniciar_agenda();'>
		Iniciar proceso autom&aacute;tico...</a>
		</td></tr></table>
		</div>
  </center>
</span>

<div id='cuenta' style='display:none;font-size:24px;text-align:right;'>
Completado: 
<span id='contador' style='font-weight:bold;'></span>
<img src='../../imagenes/ajax-loader2.gif'>
</div>
 

</body>

</html>
