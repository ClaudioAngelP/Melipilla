<?php

  require_once('../../conectar_db.php');

  $pat_id=($_GET['pat_id']*1);

  $ramas=cargar_registros_obj("
    SELECT * FROM patologias_auge_ramas 
    WHERE pat_id=$pat_id ORDER BY rama_nombre
  ");

?>

  <html>
  <title>Ramas de la Patolog&iacute;a</title>

<?php
    
  cabecera_popup('../..');

?>

<script>

var pat_id=<?php echo $pat_id; ?>;
var ramas=<?php echo json_encode($ramas); ?>;

listar=function() {

  html='<table style="width:100%;">';
  html+='<tr class="tabla_header"><td>Descripci&oacute;n</td><td style="width:50px;">Acci&oacute;n</td></tr>';
  
  if(ramas)
  for(var i=0;i<ramas.length;i++) {
  
    (i%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
    
    html+='<tr class="'+clase+'"><td style="text-align:left;"';
    html+='>'+ramas[i].rama_nombre+'</td>';
    html+='<td><center><img src="../../iconos/delete.png" ';
    html+='onClick="eliminar_rama('+ramas[i].patrama_id+');" style="cursor:pointer;"></center></td></tr>';
  
  }
  
  ((i+1)%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
  
  html+='<tr class="'+clase+'"><td style="text-align:center;">';
  html+='<input type="text" id="rama_nombre" name="rama_nombre" ';
  html+='size=10 style="width:100%;" value="">';
  html+='</td><td><center><img src="../../iconos/add.png" ';
  html+='onClick="agregar_rama();" style="cursor:pointer;">';
  html+='</table>';

  $('listado').innerHTML=html;

}

agregar_rama = function() {

  params=$('form').serialize();
  
  var myAjax=new Ajax.Request(
  'sql_ramas.php',
  {
    method:'post', parameters: params,
    onComplete:function(resp) {
    
      ramas = resp.responseText.evalJSON(true); listar();
    
    }
  });

}

eliminar_rama = function(patrama_id) {

  params=$('pat_id').serialize()+'&eliminar='+patrama_id;
  
  var myAjax=new Ajax.Request(
  'sql_ramas.php',
  {
    method:'post', parameters: params,
    onComplete:function(resp) {
    
      ramas = resp.responseText.evalJSON(true); listar();
    
    }
  });

}


</script>

<body class='fuente_por_defecto popup_background'>

<center>
<div class='sub-content'>
<img src='../../iconos/chart_organisation.png'>
Ramas de la Patolog&iacute;a
</div>
<form id='form' name='form' onSubmit='return false;'>
<input type='hidden' id='pat_id' name='pat_id' value='<?php echo $pat_id; ?>'>
<div id='listado' name='listado' class='sub-content2'
style='height:190px;overflow:auto;'>

</div>
</form>
</center>

</body>
</html>

<script> listar(); </script>
