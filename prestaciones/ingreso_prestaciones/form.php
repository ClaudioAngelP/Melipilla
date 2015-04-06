<?php

  require_once('../../conectar_db.php');
  $servs="'".str_replace(',','\',\'',_cav2(50))."'";
	$servicioshtml = desplegar_opciones_sql(   "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND          centro_medica AND centro_ruta IN (".$servs.")  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 
?>
<script>
    agregar_prestacion = function() {
      top=Math.round(screen.height/2)-165;      left=Math.round(screen.width/2)-340;
      new_win =       window.open('prestaciones/ingreso_prestaciones/form_prestaciones.php',      'win_talonarios', 'toolbar=no, location=no, directories=no, status=no, '+      'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+      'top='+top+', left='+left);
      new_win.focus();
    }
    listar_prestaciones = function() {
      var myAjax = new Ajax.Updater(      'listado_prestaciones',      'prestaciones/ingreso_prestaciones/listar_prestaciones.php',      {        method:'post',        parameters:$('info_prestacion').serialize()      });
    }
    eliminar_prestacion = function(id) {
      var myAjax = new Ajax.Request(      'prestaciones/ingreso_prestaciones/eliminar_prestacion.php',      {        method: 'post',        parameters: 'presta_id='+(id*1),        onComplete: function() {
          listar_prestaciones();
        }
      }
      );
    }
</script>
<center>
<div class='sub-content' style='width:750px;'>
<form id='info_prestacion' onSubmit='return false;'>
<div class='sub-content'><img src='iconos/table_edit.png'> <b>Planilla de Prestaciones Diarias</b></div>
<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='width:100px;text-align:right;'>Fecha:</td><td><input type='text' name='fecha1' id='fecha1' size=10  style='text-align: center;' value='<?php echo date("d/m/Y")?>'  onChange='listar_prestaciones();'>  <img src='iconos/date_magnify.png' id='fecha1_boton'></td></tr>
<tr><td style='width:100px;text-align:right;' >Servicio:</td><td><select id='centro_ruta' name='centro_ruta' onChange='listar_prestaciones();'><?php echo $servicioshtml; ?></select></td></tr>
</table>
</div>
<center><input type='button' id='agrega_presta' onClick='agregar_prestacion();'value='Agregar Prestaci&oacute;n...'></center>
<div class='sub-content2' style='height:280px;overflow:auto;'id='listado_prestaciones'>
</div>
<center>
  <table><tr><td>		<div class='boton'>		<table><tr><td>		<img src='iconos/printer.png'>		</td><td>		<a href='#' onClick='imprimir_listado();'> Imprimir Listado...</a>		</td></tr></table>		</div>		</td></tr></table>		</div>	</td></tr></table>
	</center>
</form>
</div>
</center>
<script>
    Calendar.setup({        inputField     :    'fecha1',         // id of the input field        ifFormat       :    '%d/%m/%Y',       // format of the input field        showsTime      :    false,        button          :   'fecha1_boton'
    });
    listar_prestaciones();
</script>
