<?php

  require_once('../../conectar_db.php');
  
  $clasificahtml = desplegar_opciones_sql("
                    SELECT eclase_id, eclase_nombre 
                    FROM equipo_medico_clase; 
                    ");

  $marcahtml = desplegar_opciones_sql("
                    SELECT DISTINCT equipo_marca, equipo_marca 
                    FROM equipos_medicos; 
                    ");

  $modelohtml = desplegar_opciones_sql("
                    SELECT DISTINCT equipo_modelo, equipo_modelo
                    FROM equipos_medicos; 
                    ");

?>

<script>

		seleccionar_centro = function() {
    
      params='centro_ruta='+encodeURIComponent($('centro_ruta').value);
    
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('abastecimiento/recepcion_gastos/seleccionar_centro.php?'+
      params,
      'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
		}
		
		listar_equipos=function() {
    
      var myAjax=new Ajax.Updater(
      'listado_equipos',
      'equipos/inventario_equipos/listado_equipos.php',
      {
        method: 'post',
        parameters: $('filtro').serialize()
      }
      );
    
    }

    abrir_equipo=function(equipo_id) {
    
      cambiar_pagina('equipos/ingreso_equipos/form.php?equipo_id='+equipo_id);
    
    }

</script>

<center>

<div class='sub-content' style='width:700px;'>

<div class='sub-content'>
<img src='iconos/book_open.png'>
<b>Inventario de Equipos M&eacute;dicos</b>
</div>

<div class='sub-content'>
<form id='filtro' name='filtro' onSubmit='return false;'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;'>
Centro de Costo:
</td>
<td colspan=5>

    <input type='hidden' id='centro_ruta' name='centro_ruta' value=''>
    
		<input type='text' id='centro_nombre' name='centro_nombre' 
    value='(Todos...)' disabled size=50>
    <img src='iconos/zoom_in.png' onClick='seleccionar_centro();'
    id='centro_ruta_icono' name='centro_ruta_icono'>

</td>
</tr>

<tr>
<td style='text-align:right;'>
Clasificaci&oacute;n:
</td>
<td colspan=3>
<select id='clasificacion' name='clasificacion'>
<option value='-1'>(Todas...)</option>
<?php echo $clasificahtml; ?>
</select>
</td>
</tr>
<tr>
<td style='text-align:right;'>
Marca:
</td>
<td>
<select id='marca' name='marca'>
<option value='-1'>(Todas...)</option>
<?php echo $marcahtml; ?>
</select>
</td>

<td style='text-align:right;'>
Modelo:
</td>
<td>
<select id='modelo' name='modelo'>
<option value='-1'>(Todos...)</option>
<?php echo $modelohtml; ?>
</select>
</td>

</tr>

<tr>
<td colspan=6>
<center>
<input type='button' value='Actualizar Listado...' onClick='listar_equipos();' />
</center>
</td>
</tr>


</table>

</form>

</div>

<div class='sub-content2' style='height:300px;overflow:auto;' id='listado_equipos'>

</div>
</div>

</center>

<script> listar_equipos(); </script>
