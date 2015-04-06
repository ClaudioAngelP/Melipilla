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
                    
  $servs="'".str_replace(',','\',\'',_cav2(100))."'";

    $servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica AND centro_ruta IN (".$servs.")
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 


?>

<script>
		
    listar_equipos=function() {
    
      var myAjax=new Ajax.Updater(
      'listado_equipos',
      'equipos/listado_usuario/listado_equipos.php',
      {
        method: 'post',
        parameters: $('filtro').serialize()
      }
      );
    
    }

    abrir_equipo=function(equipo_id) {

        l=(screen.availWidth/2)-250;
        t=(screen.availHeight/2)-200;
        
        win = window.open('equipos/visualizar_equipo.php?equipo_id='+equipo_id, 'ver_equipo',
                          'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                          'resizable=no, width=500, height=415');
                          
        win.focus();

        //cambiar_pagina('equipos/ingreso_equipos/form.php?equipo_id='+equipo_id);
    
    }

</script>

<center>

<div class='sub-content' style='width:700px;'>

<div class='sub-content'>
<img src='iconos/book_open.png'>
<b>Listado de Equipos M&eacute;dicos Asignados</b>
</div>

<div class='sub-content'>
<form id='filtro' name='filtro' onSubmit='return false;'>

<table style='width:100%;'>


<tr>
<td style='text-align:right;'>
Ubicaci&oacute;n:
</td>
<td>
<select id='centro_ruta' name='centro_ruta'>
<?php echo $servicioshtml; ?>
</select>
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
<td style='text-align:right;'>
Estado:
</td>
<td>
<select id='estado' name='estado'>
<option value='-1'>(Todos...)</option>
<option value=0>En uso normal.</option>
<option value=1>En mantenci&oacute;n preventiva.</option>
<option value=2>En mantenci&oacute;n correctiva.</option>
<option value=3>En garant&iacute;a.</option>
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

<div class='sub-content2' style='height:300px;' id='listado_equipos'>

</div>
</div>

</center>

<script> listar_equipos(); </script>