<?php

  require_once('../../conectar_db.php');

  if(isset($_GET['eot_id'])) {
    $eot_id=$_GET['eot_id']*1;
    list($eot)=cargar_registros_obj("SELECT * FROM equipo_orden_trabajo WHERE eot_id=$eot_id");
    $titulo="O.T. <b>#$eot_id</b>";
    $eagenda_id=0;
  } else {
    $eagenda_id=$_GET['eagenda_id']*1;
    list($eagenda)=cargar_registros_obj("SELECT * FROM equipo_agenda_preventiva WHERE eagenda_id=$eagenda_id");
    $titulo="Nueva O.T.";
    $eot_id=-1;
  }


  $t = cargar_registros_obj("
    SELECT * FROM tecnico ORDER BY tec_nombre
  ");

?>

<html>

<title>Asignar Orden de Trabajo</title>

<?php cabecera_popup('../..'); ?>

<script>

function sel_tecnico() {

    var chks=$('tecnicos').getElementsByTagName('input');
    

	 var pasar=false;
    
    for(var n=0;n<chks.length;n++) {
    
        if(chks[n].checked) pasar=true;
    
    }
    
    if(!pasar) {
    
        alert('Debe seleccionar al menos un t&eacute;cnico para ejecutar el trabajo.'.unescapeHTML());
        return;
    
    }
	
	
  
  var myAjax=new Ajax.Request(
  'sql.php',
  {
    method:'post',
    parameters:'eot_id=<?php echo $eot_id; ?>&eagenda_id=<?php echo $eagenda_id; ?>&'+$('tecnicos').serialize(),
    onComplete: function(resp) {
    
      var r = resp.responseText.evalJSON(true);
      
      if(r==0) {
        var fn = window.opener.listar_eot.bind(window.opener);
        fn();
      } else {
        abrir_eot(r);
      }
      
      window.close();
      
    }
  }
  );
  
}

abrir_eot = function (eot_id) {

  var l=(screen.availWidth/2)-250;
  var t=(screen.availHeight/2)-200;
      
  win = window.open('../../visualizar.php?eot_id='+eot_id, 'ver_eot',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=500, height=415');
                    
  win.focus();

}


</script>

<body class='fuente_por_defecto popup_background'>

<form id='tecnicos' name='tecnicos' onsubmit='return false;'>

<div class='sub-content'>
<img src='../../iconos/database_link.png'>
Seleccionar T&eacute;cnico(s) para <?php echo $titulo; ?>
</div>

<div class='sub-content2' style='height:200px;overflow:auto;'>

<table style='width:100%;'>

<tr class='tabla_header'>
<td>&nbsp;</td>
<td>RUT</td>
<td>Nombre Completo</td>
</tr>

<?php 

  for($i=0;$i<count($t);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
    print("
    <tr class='$clase' style='cursor:pointer;'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"$clase\";'>
    <td><center>
    <input type='checkbox' id='t_".$t[$i]['tec_id']."' name='t_".$t[$i]['tec_id']."'>
    </center></td>
    <td style='text-align:right;width:30%;'>".$t[$i]['tec_rut']."</td>
    <td>".$t[$i]['tec_nombre']."</td>
    </tr>
    ");
  
  }

?>

</table>
</div>

</form>

<center>
<input type='button' value='Seleccionar T&eacute;cnico(s)...' onClick='sel_tecnico();'>
</center>

</body>
</html>
