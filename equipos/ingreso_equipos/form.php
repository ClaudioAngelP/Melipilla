<?php

  require_once('../../conectar_db.php');
  
  if(isset($_GET['equipo_id'])) {
    
    $equipo_id=$_GET['equipo_id']*1;
    $equipo=cargar_registro("
      SELECT * FROM equipos_medicos WHERE
      equipo_id=$equipo_id
    ");
    
    $equipo['equipo_mant_prov']*=1;
    
    if($equipo['equipo_foto']!='')
      $equipo['foto']='fotos/'.$equipo['equipo_foto'];
    else
      $equipo['foto']='imagenes/sin_fotografia.jpg';
    
    $centro=cargar_registro("
      SELECT centro_nombre FROM centro_costo WHERE 
      centro_ruta='".pg_escape_string($equipo['equipo_centro_ruta'])."'
    ");

    $centro2=cargar_registro("
      SELECT centro_nombre FROM centro_costo WHERE 
      centro_ruta='".pg_escape_string($equipo['equipo_centro_ruta2'])."'
    ");
    
    $prov=cargar_registro("
      SELECT prov_rut, prov_glosa FROM proveedor WHERE
      prov_id=".($equipo['equipo_prov_id']*1)."
    ");
    
  } else {
    
    $equipo_id=0;
    $equipo['equipo_eclase_id']='';
    $equipo['equipo_marca']='';
    $equipo['equipo_modelo']='';
    $equipo['equipo_serie']='';
    $equipo['equipo_inventario']='';
    $equipo['equipo_centro_ruta']='';
    $equipo['equipo_centro_ruta2']='';
    $equipo['equipo_nuevo']='t';
    $equipo['equipo_garantia']='';
    $equipo['equipo_garantia_medida']='';
    $equipo['equipo_preventiva']='';
    $equipo['equipo_mant_prov']='0';
    $equipo['equipo_foto']='';
    $equipo['equipo_prov_id']=0;
    $equipo['equipo_accesorios']='';
    
    $equipo['equipo_vida_estandar']=1;
    $equipo['equipo_vida_extendida']=0;
    $equipo['equipo_fecha_fabricacion']=date('d/m/Y');
    $equipo['equipo_fecha_mantprev']=date('d/m/Y');

    $equipo['equipo_comentarios']='';
    
    $prov['prov_rut']='';
    $prov['prov_glosa']='';
    $centro['centro_nombre']='';
    $centro2['centro_nombre']='';
    $equipo['foto']='imagenes/sin_fotografia.jpg';
    
    
  }
    
  $eclasehtml = desplegar_opciones("equipo_medico_clase", "eclase_id, eclase_nombre",
                  $equipo['equipo_eclase_id'],
                  'true', 'ORDER BY eclase_nombre'); 
  

  // Genera formulario de Características Técnicas
  
  $tecnicas='<table style="width:100%;">
  <tr class="tabla_header">
  <td>Grupo</td>
  <td>Propiedad</td>
  <td>Valor</td>
  </tr>
  ';

  $g=cargar_registros_obj("
    SELECT * FROM equipo_grupo_ctec
    ORDER BY cgrupo_orden;
  ");
  
  for($j=0;$j<count($g);$j++) {

    $cgrupo_id=$g[$j]['cgrupo_id']*1;

    $c=cargar_registros_obj("
      SELECT * FROM equipo_caracteristicas_tecnicas
      WHERE cgrupo_id=$cgrupo_id
      ORDER BY ecar_id;
    ");
  
    $tecnicas.='<tr class="tabla_fila2"><td 
                style="text-align:center;font-weight:bold;
                font-decoration:italic;"
                rowspan='.(count($c)+1).'>
                '.htmlentities($g[$j]['cgrupo_nombre']).'</td></tr>';
  
    for($i=0;$i<count($c);$i++) {
    
      $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
      
      $tecnicas.="
      <tr class='$clase'>
      <td style='text-align:right;width:30%;font-style:italic;'
      >".htmlentities($c[$i]['ecar_nombre']).":</td>
      <td>
      ";
      
      $v=cargar_registro("SELECT * FROM equipos_medicos_ctec 
                          WHERE ecar_id=".$c[$i]['ecar_id']."
                          AND equipo_id=".$equipo_id);
      
      if(!$v)   $valor='';
      else      $valor=htmlentities($v['ctec_valor']);
      
      switch($c[$i]['ecar_tipo']) {
      
      case 0: 
          $tecnicas.="
          <input type='text' size=10 
          id='ecar_".$c[$i]['ecar_id']."' name='ecar_".$c[$i]['ecar_id']."'
          value='$valor' style='text-align:right;'> 
          <i><b>".htmlentities($c[$i]['ecar_unidad'])."</b></i>
          "; break;
      case 1: case 3:
          $tecnicas.="
          <input type='text' size=20 
          id='ecar_".$c[$i]['ecar_id']."' name='ecar_".$c[$i]['ecar_id']."'
          value='$valor'> 
          <i><b>".htmlentities($c[$i]['ecar_unidad'])."</b></i>
          "; break;
      case 2:
          if($v!='') $chk='CHECKED'; else $chk='';
          $tecnicas.="
          <input type='checkbox' 
          id='ecar_".$c[$i]['ecar_id']."' name='ecar_".$c[$i]['ecar_id']."'
          $chk> 
          <i><b>".htmlentities($c[$i]['ecar_unidad'])."</b></i>
          "; break;
      
          
      }
  
      $tecnicas.="</td></tr>";
  
    }
  
  }
  
  $tecnicas.='</table>';


?>

<script>

		seleccionar_centro = function(n) {
    
      params='n='+n+'&centro_ruta='+encodeURIComponent($('centro_ruta'+n).value);
    
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('equipos/ingreso_equipos/seleccionar_centro.php?'+
      params,
      'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
		}
		
		quitar_centro = function() {
    
      $('centro_ruta2').value='';
      $('centro_nombre2').value='';
    
    }
		
		seleccionar_foto = function() {
    
      top=Math.round(screen.height/2)-100;
      left=Math.round(screen.width/2)-150;
      
      new_win = 
      window.open('equipos/ingreso_equipos/seleccionar_foto.php',
      'win_foto', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=300, height=200, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
		}
		
		guardar_equipo=function() {
    
      var myAjax=new Ajax.Request(
      'equipos/ingreso_equipos/sql.php',
      {
        method:'post',
        parameters:$('equipo').serialize(),
        onComplete: function(resp) {
        
          var datos=resp.responseText.evalJSON(true);
          
          alert('Equipo ingresado exitosamente.');
          
          cambiar_pagina('equipos/ingreso_equipos/form.php');
        
        }
      }
      );
    
    }

    eliminar_equipo=function() {
    
      var conf = confirm('&iquest;Desea eliminar el equipo? - No hay opciones para deshacer.'.unescapeHTML());
      
      if(!conf) return;
      
      var myAjax=new Ajax.Request(
      'equipos/ingreso_equipos/sql.php',
      {
        method:'post',
        parameters:$('equipo').serialize()+'&eliminar=1',
        onComplete: function(resp) {
        
          var datos=resp.responseText.evalJSON(true);
          
          alert('Equipo eliminado exitosamente.');
          
          cambiar_pagina('equipos/ingreso_equipos/form.php');
        
        }
      }
      );
    
    }

  mostrar_proveedor=function(datos) {
    $('prov_id').value=datos[3];
    $('nombre_prov_id').value=datos[2].unescapeHTML();
  }
    
  liberar_proveedor=function() {
    $('prov_id').value=0;
    $('nombre_prov_id').value='';
    $('_prov_id').value='';
    $('_prov_id').focus();
  }
    
  autocompletar_proveedores = new AutoComplete(
    '_prov_id', 
    'autocompletar_sql.php',
    function() {
      if($('_prov_id').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('_prov_id').value)
      }
    }, 'autocomplete', 350, 200, 250, 1, 2, mostrar_proveedor);


    ver_generales = function() {
    
      tab_up('tab_general');
      tab_down('tab_tecnicas');
    
    }

    ver_tecnicas = function() {
    
      tab_down('tab_general');
      tab_up('tab_tecnicas');
    
    }

</script>

<form id='equipo' name='equipo' onsubmit='return false;'>

<input type='hidden' id='equipo_id' name='equipo_id' value=<?php echo $equipo_id; ?>>

<center>

<div class='sub-content' style='width:650px;'>
<div class='sub-content'>
<img src='iconos/application_form_edit.png'>
<b><?php if($equipo_id==0) echo 'Ingreso'; else echo 'Edici&oacute;n'; ?> 
de Inventario</b> 
</div>

<div class='sub-content'>

<table style='width:100%'>
<tr>
<td style='text-align:right;'>
Clasificaci&oacute;n:
</td>
<td>
<select id='eclase_id' name='eclase_id' value=<?php echo $equipo['equipo_eclase_id']; ?>>
<?php echo $eclasehtml; ?>
</select>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Marca:
</td>
<td>
<input type='text' size=30 id='marca' name='marca'
value='<?php echo htmlentities($equipo['equipo_marca']); ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Modelo:
</td>
<td>
<input type='text' size=20 id='modelo' name='modelo'
value='<?php echo htmlentities($equipo['equipo_modelo']); ?>'>
</td>
</tr>

<tr>
<td style='text-align: right;'>Proveedor:</td>
<td colspan=3>
<input type='hidden' id='prov_id' name='prov_id' value=<?php echo $equipo['equipo_prov_id']; ?>>
<input type='text' id='_prov_id' name='_prov_id' onDblClick='liberar_proveedor();' size=15 value=<?php echo $prov['prov_rut']; ?>>
<input type='text' id='nombre_prov_id' name='nombre_prov_id' size=40 DISABLED
value='<?php echo htmlentities($prov['prov_glosa']); ?>'>

</td>
</tr>

<tr>
<td style='text-align:right;'>
Nro. de Serie:
</td>
<td>
<input type='text' size=30 id='serie' name='serie'
value='<?php echo $equipo['equipo_serie']; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Nro. de Inventario:
</td>
<td>
<input type='text' size=30 id='inventario' name='inventario'
value='<?php echo $equipo['equipo_inventario']; ?>'>
</td>
</tr>
</table>

</div>


		<table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_generales();'>
      <img src='iconos/report.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_tecnicas' style='cursor: pointer;'
      onClick='ver_tecnicas();'>
      <img src='iconos/book_open.png'>
      Caracter&iacute;sticas T&eacute;cnicas</div>
		  </td></tr>
    </table>
		
<div class='tabbed_content' id='tab_general_content'>

<center>

<table style='width:100%;'>

<tr>
<td style='text-align:right;'>
Servicio (Propietario):
</td>
<td>
    
    <input type='hidden' id='centro_ruta1' name='centro_ruta1' value='<?php echo $equipo['equipo_centro_ruta']; ?>'>
    
		<input type='text' id='centro_nombre1' name='centro_nombre1' 
    value='<?php echo htmlentities($centro['centro_nombre']); ?>' disabled size=50>
    <img src='iconos/zoom_in.png' onClick='seleccionar_centro(1);'
    id='centro_ruta_icono' name='centro_ruta_icono' style='cursor:pointer;'>
    

</td>
</tr>

<tr>
<td style='text-align:right;'>
Servicio (Ubicaci&oacute;n):
</td>
<td>
    
    <input type='hidden' id='centro_ruta2' name='centro_ruta2' value='<?php echo $equipo['equipo_centro_ruta2']; ?>'>
    
		<input type='text' id='centro_nombre2' name='centro_nombre2' 
    value='<?php echo htmlentities($centro2['centro_nombre']); ?>' disabled size=50>
    <img src='iconos/zoom_in.png' onClick='seleccionar_centro(2);'
    id='centro_ruta_icono' name='centro_ruta_icono' style='cursor:pointer;'>
    <img src='iconos/cross.png' onClick='quitar_centro();'
    id='centro_ruta_del' name='centro_ruta_del' style='cursor:pointer;'>
    
</td>
</tr>

<tr>
<td style='text-align:right;'>
Equipo Nuevo:
</td>
<td>
<input type='checkbox' 
id='equipo_nuevo' name='equipo_nuevo' 
<?php if($equipo['equipo_nuevo']=='t') echo 'CHECKED'; ?>>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Garant&iacute;a:
</td>
<td>
<input type='text' size=5 id='tiempo' name='tiempo' 
value='<?php echo $equipo['equipo_garantia']; ?>'>
<select id='medida' name='medida'>
<option value=0 <?php if($equipo['equipo_garantia_medida']==0) echo 'SELECTED'; ?>>meses.</option>
<option value=1 <?php if($equipo['equipo_garantia_medida']==1) echo 'SELECTED'; ?>>a&ntilde;os.</option>
</select>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Periodicidad Mant. Preventiva:
</td>
<td>

<table cellpadding=0 cellspacing=0><tr><td>

<select id='preventiva' name='preventiva'>
<option value=0 <?php if($equipo['equipo_preventiva']==0) echo 'SELECTED'; ?>>Mensual</option>
<option value=1 <?php if($equipo['equipo_preventiva']==1) echo 'SELECTED'; ?>>Bimensual</option>
<option value=2 <?php if($equipo['equipo_preventiva']==2) echo 'SELECTED'; ?>>Trimestral</option>
<option value=3 <?php if($equipo['equipo_preventiva']==3) echo 'SELECTED'; ?>>Semestral</option>
<option value=4 <?php if($equipo['equipo_preventiva']==4) echo 'SELECTED'; ?>>Anual</option>
</select>

</td><td>
&nbsp;Inicio Mant. Preventivas:&nbsp;
</td><td>

<input type='text' id='fecha_mantprev' name='fecha_mantprev' 
value='<?php echo $equipo['equipo_fecha_mantprev']; ?>'
size='10' style='text-align:center;'>
<img src='iconos/date_magnify.png' id='fecha_mantprev_boton'>

</td></tr>
</table>


</td>
</tr>



<tr>
<td style='text-align:right;'>
Mantenciones Prev. Proveedor:
</td>
<td>
<input type='text' style='text-align:right;' size=5
id='equipo_mant_prov' name='equipo_mant_prov' 
value='<?php echo $equipo['equipo_mant_prov']; ?>'>
</td>
</tr>


<tr>
<td style='text-align:right;'>
Vida &Uacute;til Est&aacute;ndar:
</td>
<td>
<input type='text' id='vida_estandar' name='vida_estandar' 
value='<?php echo $equipo['equipo_vida_estandar']; ?>'
size='5' style='text-align:right;'> a&ntilde;o(s).
</td>
</tr>

<tr>
<td style='text-align:right;'>
Vida &Uacute;til Extendida:
</td>
<td>
<input type='text' id='vida_extendida' name='vida_extendida' 
value='<?php echo $equipo['equipo_vida_extendida']; ?>'
size='5' style='text-align:right;'> a&ntilde;o(s).
</td>
</tr>

<tr>
<td style='text-align:right;'>
Fecha de Fabricaci&oacute;n:
</td>
<td>
<input type='text' id='fecha_fabricacion' name='fecha_fabricacion' 
value='<?php echo $equipo['equipo_fecha_fabricacion']; ?>'
size='10' style='text-align:center;'>
<img src='iconos/date_magnify.png' id='fecha_fabricacion_boton'>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Accesorios del Equipo:
</td>
<td>
<input type='text' style='text-align:left;' size=50
id='equipo_accesorios' name='equipo_accesorios' 
value='<?php echo htmlentities($equipo['equipo_accesorios']); ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;' valign='top'>
Comentarios:
</td>
<td>
<textarea style='text-align:left;' cols=40 rows=4
id='comentarios' name='comentarios'
><?php echo htmlentities($equipo['equipo_comentarios']); ?></textarea>
</td>
</tr>


<tr>
<td style='text-align:right;' valign='top'>
Fotograf&iacute;a:
</td>
<td>
<table cellpadding=0 cellspacing=0>
<tr><td>
<input type='hidden' id='nombre_foto' name='nombre_foto' value='<?php echo htmlentities($equipo['equipo_foto']); ?>'>
<img src='<?php echo htmlentities($equipo['foto']); ?>' id='foto' name='foto' 
style='width:200px;height:150px;border:1px solid black;'>
</td></tr>
<tr><td>
<center>
<input type='button' value='-- Seleccionar Foto... --' onClick='seleccionar_foto();'>
</center>
</td></tr>
</table>

</td>
</tr>


</table>

</center>

</div>

<div class='tabbed_content' 
id='tab_tecnicas_content' style='display:none;height:350px;overflow:auto;'>
<?php echo $tecnicas; ?>
</div>


    <center>
   <table><tr><td>
    
    
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/disk.png'>
		</td><td>
		<a href='#' onClick='guardar_equipo();'>
		
    <?php 
    if($equipo_id==0) echo 'Guardar Inventario...';
    else echo 'Guardar cambios a Inventario...';
    ?>
    
    </a>
		</td></tr></table>
		</div>
		
		</td><td>
		
		<?php if($equipo_id!=0) { ?>
    
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='eliminar_equipo();'>
		Eliminar del Inventario...
    </a>
		</td></tr></table>
		</div>

		<?php } ?>

    </td></tr></table>		

    </center>
  
</div>

</form>

<script>

    Calendar.setup({
        inputField     :    'fecha_mantprev',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_mantprev_boton'
    });
    Calendar.setup({
        inputField     :    'fecha_fabricacion',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha_fabricacion_boton'
    });


</script>
