<?php

  require_once('../../conectar_db.php');
  $servs="'".str_replace(',','\',\'',_cav2(50))."'";
	$servicioshtml = desplegar_opciones_sql( 
?>
<script>
    agregar_prestacion = function() {
      top=Math.round(screen.height/2)-165;
      new_win = 
      new_win.focus();
    }
    listar_prestaciones = function() {
      var myAjax = new Ajax.Updater(
    }
    eliminar_prestacion = function(id) {
      var myAjax = new Ajax.Request(
          listar_prestaciones();
        }
      }
      );
    }
</script>
<center>
<div class='sub-content' style='width:750px;'>
<form id='info_prestacion' onSubmit='return false;'>
<div class='sub-content'>
<div class='sub-content'>
<table style='width:100%;'>
<tr>
<tr>
</table>
</div>
<center>
<div class='sub-content2' style='height:280px;overflow:auto;'
</div>
<center>
  <table><tr><td>
	
</form>
</div>
</center>
<script>
    Calendar.setup({
    });
    listar_prestaciones();
</script>