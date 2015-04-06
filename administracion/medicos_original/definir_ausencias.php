<?php

  require_once('../../conectar_db.php');
  
  $doc_id=$_GET['doc_id']*1;
  
  if($doc_id==0) {
    $titulo='Globales';
  } else {
    $doc=cargar_registros_obj("SELECT * FROM doctores WHERE doc_id=$doc_id");
    $titulo='de M&eacute;dico';
  }
  
  $ausencias=cargar_registros_obj("
    SELECT * FROM ausencias_medicas 
    JOIN ausencias_motivos ON motivo_id=ausencia_motivo 
    WHERE doc_id=$doc_id ORDER BY ausencia_fechainicio
  ", true);
  
  $motivoshtml=str_replace("\n",'',desplegar_opciones("ausencias_motivos", "motivo_id, motivo_desc",
  '1', 'true', 'ORDER BY motivo_desc')); 
  

?>

<html>
<title>Definir Ausencias</title>

<?php cabecera_popup('../..'); ?>


<script>

var ausencias=<?php echo json_encode($ausencias); ?>

listar_ausencias=function() {

  var html='<table style="width:100%;"><tr class="tabla_header">';
  html+='<td>Fecha Inicio</td>';
  html+='<td>Fecha T&eacute;rmino</td>';
  html+='<td>Motivo</td>';
  html+='<td>Acciones</td></tr>';
  
  for(var i=0;i<ausencias.length;i++) {
  
    clase=(i%2==0)?'tabla_fila':'tabla_fila2';
  
    html+='<tr class="'+clase+'">';
    
    if(ausencias[i].ausencia_fechafinal!=null) {
      html+='<td style="text-align:center;">'+ausencias[i].ausencia_fechainicio+'</td>';  
      html+='<td style="text-align:center;">'+ausencias[i].ausencia_fechafinal+'</td>';  
    } else {
      html+='<td style="text-align:center;" colspan=2>'+ausencias[i].ausencia_fechainicio+'</td>';  
    }
    
    html+='<td style="text-align:center;">'+ausencias[i].motivo_desc+'</td>';  
    html+='<td style="text-align:center;"><img src="../../iconos/delete.png" ';
    html+='onClick="borrar_ausencia('+ausencias[i].ausencia_id+');" ';
    html+='style="cursor:pointer;"></td>';  
    html+='</tr>';
  
  }
  
  clase=(i%2==0)?'tabla_fila':'tabla_fila2';

  html+='<tr class="'+clase+'">';
    
  html+='<td style="text-align:center;">';
  html+='<input type="text" id="fecha1" name="fecha1" size=10>';
  html+='<img src="../../iconos/calendar.png" id="fecha1_boton"></td>';  
  html+='<td style="text-align:center;">';  
  html+='<input type="text" id="fecha2" name="fecha2" size=10>';
  html+='<img src="../../iconos/calendar.png" id="fecha2_boton"></td>';  
    
  html+='<td style="text-align:center;"><select id="motivo" name="motivo">';
  html+="<?php echo $motivoshtml; ?></select></td>";  
  html+='<td style="text-align:center;"><img src="../../iconos/add.png" ';
  html+='onClick="agregar_ausencia();" style="cursor:pointer;"></td>';  
  html+='</tr>';
  
  html+='</table>';
  
  $('lista_ausencias').innerHTML=html;
  
  configurar_calendarios();

}

agregar_ausencia = function() {

  var params=$('fecha1').serialize()+'&';
  params+=$('fecha2').serialize()+'&';
  params+=$('motivo').serialize()+'&doc_id=<?php echo $doc_id; ?>';

  var myAjax=new Ajax.Request(
    'sql_ausencias.php',
    {
      method:'post', parameters:params,
      onComplete: function(resp) {
        ausencias=resp.responseText.evalJSON(true);
        listar_ausencias();
      }
    }
  );

}

borrar_ausencia = function(ausencia_id) {

  var params='ausencia_id='+ausencia_id+'&doc_id=<?php echo $doc_id; ?>';

  var myAjax=new Ajax.Request(
    'sql_ausencias.php',
    {
      method:'post',parameters:params,
      onComplete: function(resp) {
        ausencias=resp.responseText.evalJSON(true);
        listar_ausencias();
      }
    }
  );

}

configurar_calendarios=function() {

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


}





</script>

<body class='fuente_por_defecto popup_background' onLoad='listar_ausencias();'>

<div class='sub-content'>
<img src='../../iconos/calendar_delete.png'>
<b>Definir Ausencias M&eacute;dicas <?php echo $titulo; ?></b>
</div>

<div class='sub-content2' id='lista_ausencias'
style='height:325px;overflow:auto;'>



</div>

</body>
</html>

