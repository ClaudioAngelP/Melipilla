<?php

  require_once('../../conectar_db.php');

  $doc_id=$_GET['doc_id'];
  
  $ii=true;

?>

<html>
<title>Seleccionar Especialidades</title>

<?php cabecera_popup('../..'); ?>

<script>

guardar_estado = function(esp_id, estado) {

  var myAjax=new Ajax.Request(
  'sql_especialidades.php',
  {
    method:'post',
    parameters:'doc_id=<?php echo $doc_id; ?>&esp_id='+esp_id+'&val='+estado
  }
  );

}

crear_cupos=function(esp_id) {

    l=(screen.availWidth/2)-350;
    t=(screen.availHeight/2)-225;
        
    win = window.open('crear_cupos.php?doc_id=<?php echo $doc_id; ?>&esp_id='+esp_id, 
                    '_self',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=700, height=450');
                    
    //win.focus();
    //window.close();

}

</script>

<body class='fuente_por_defecto popup_background'>

<table style='width:100%;'>

<tr class='tabla_header'>

<td style='width:75%;'>Especialidades y Procedimientos</td>
<td>Practica Esp./Proc.</td>
<td>Definir Horario</td>

</tr>

<?php 

    $esp = cargar_registros_obj("
      SELECT *, (SELECT count(*) FROM cupos_atencion WHERE cupos_esp_id=esp_id AND cupos_doc_id=$doc_id) AS cupos FROM especialidades 
          where (esp_codigo_ifl_usuario = '' or esp_codigo_ifl_usuario is null)
      ORDER BY esp_desc
    ");
    
    if($esp)
    for($i=0;$i<count($esp);$i++) {
    
      ($ii=!$ii) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
      if($esp[$i]['cupos']*1==0) {
        $d='display:none;';
        $c=''; 
      } else {
        $d='';
        $c='CHECKED';
      }
          
      print("
      <tr class='$clase'
      onMouseOver='this.className=\"mouse_over\";'
      onMouseOut='this.className=\"$clase\";'>
      <td>".$lvl.' '.htmlentities($esp[$i]['esp_desc'])."</td>
      <td>
      <center>
        <input type='checkbox' onClick='
          if(this.checked) $(\"ccupos_".$esp[$i]['esp_id']."\").style.display=\"\";
          else $(\"ccupos_".$esp[$i]['esp_id']."\").style.display=\"none\";
          guardar_estado(".$esp[$i]['esp_id'].", this.checked);
        ' $c>
      </center>
      </td>
      <td>
      <center>
        <img src='../../iconos/date.png' style='cursor:pointer;$d'
        id='ccupos_".$esp[$i]['esp_id']."'
        onClick='crear_cupos(".$esp[$i]['esp_id'].");'>
      </center>
      </td>
      </tr>
      ");
      
     }
  
?>

</table>

</body>
</html>
