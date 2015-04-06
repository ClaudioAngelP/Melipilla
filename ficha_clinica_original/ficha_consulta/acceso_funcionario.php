<?php

  require_once('../../conectar_db.php');
  
  GLOBAL $accesos_func;
  
  function desplegar_checks($tabla, $campos, $valsel, 
                            $where, $order, $name, $_permiso_id,
                            $inittag='', $endtag='') {
  
    GLOBAL $conn;
    GLOBAL $accesos_func;
  
    $opciones = pg_query($conn, "
    SELECT $campos FROM $tabla WHERE $where $order
    ");
    
    $html='';
    
    for($i=0;$i<pg_num_rows($opciones);$i++) {
    
      $opcion = pg_fetch_row($opciones);
      
      if(_ca($accesos_func, $_permiso_id, $opcion[0])) {
        $checked='CHECKED';
      } else {
        $checked='';
      }
      
      $html.='
      <tr><td>
      <input type="checkbox" 
      id="'.$name.'_'.$opcion[0].'" 
      name="'.$name.'_'.$opcion[0].'"
      value="'.$opcion[0].'" '.$checked.'>
      </td><td> 
      '.$inittag.''.htmlentities($opcion[1]).''.$endtag.'
      </td></tr>
      ';
    
    }
    
    return $html;
  
  }
  
  $id = ($_GET['func_id']*1);
  
  print('
  <form id="accesos_func" name="accesos_func">
  <table width=100%>');
  
  $accesos_act = pg_query($conn,
  "SELECT acceso_id, permiso_id, valor FROM func_acceso 
  WHERE func_id=".$id
  );
  
  for($m=0;$m<pg_num_rows($accesos_act);$m++)
  $accesos_func[$m] = pg_fetch_row($accesos_act);

  
  $grupos = pg_query($conn,
  "
  SELECT * FROM func_permgrupo ORDER BY permgrupo_id
  ");
  
  for($g=0;$g<pg_num_rows($grupos);$g++) {
  
  $grupo=pg_fetch_row($grupos);
  
  print('<tr class="tabla_header">
  <td colspan=2 style="text-align: left;">
  
  <img src="iconos/key.png">
  <b>'.htmlentities($grupo[1]).'</b>
  
  </td></tr>');
  
  // Obtiene la lista de permisos actuales del sistema
  // y la une con la correspondiente tabla de accesos actuales
  // para poner todo en un array.
  
  $accesos = pg_query($conn, "
  SELECT func_permiso.permiso_id, permiso_nombre, permiso_tipo, 
  COALESCE(ax1.valor,'') AS valor,
  COALESCE(ax2.valor,'') AS valor2
  FROM func_permiso 
  LEFT JOIN func_acceso AS ax1
  ON ax1.permiso_id=func_permiso.permiso_id
  AND ax1.func_id=".$id." AND NOT ax1.acceso_ruta
  LEFT JOIN func_acceso AS ax2
  ON ax2.permiso_id=func_permiso.permiso_id
  AND ax2.func_id=".$id." AND ax2.acceso_ruta
  WHERE permgrupo_id=".$grupo[0]."
  ORDER BY permiso_orden
  ");
  
  for($i=0;$i<pg_num_rows($accesos);$i++) {
  
    $acceso = pg_fetch_row($accesos);
  
    if(($i%2)==0) {
				$clase='tabla_fila';
		} else {
				$clase='tabla_fila2';
		}
  
    print('
    <tr id="fila_acceso_'.($acceso[0]*1).'"
    class="'.$clase.'">
    <td valign="top">
    <img src="iconos/bullet_key.png">    
    '.htmlentities($acceso[1]).'</td>
    <td>
    ');
    
    switch($acceso[2]) {
	     case 0: 
          if(_ca($accesos_func, $acceso[0], '1')) {
            $checked='CHECKED';
          } else {
            $checked='';
          }
          
          print('
          <table>
          <tr>
          <td><input type="checkbox" 
          id="acceso_check_'.($acceso[0]*1).'" 
          name="acceso_check_'.($acceso[0]*1).'" '.$checked.'></td>
          <td><i>Permitido</i></td>
          </tr>
          </table>
          ');
       break;
        
	     case 1:
	     
	         print('<table>');
	         print(desplegar_checks('bodega', 'bod_id, bod_glosa', $acceso[3], 
           'bod_proveedores=true', 'ORDER BY bod_glosa', 
           'acceso_check_'.($acceso[0]*1),$acceso[0]));
	         print('</table>');
	         
       break;
       
       case 2:
	     
	         print('<table>');
	         print(desplegar_checks('bodega', 'bod_id, bod_glosa', $acceso[3], 
           'bod_inter=true', 'ORDER BY bod_glosa',
           'acceso_check_'.($acceso[0]*1),$acceso[0]));
	         print('</table>');
	         
       break;
       
       case 3:
	     
	         print('<table>');
	         print(desplegar_checks('bodega', 'bod_id, bod_glosa', $acceso[3], 
           'bod_proveedores=true OR bod_inter=true', 'ORDER BY bod_glosa',
           'acceso_check_'.($acceso[0]*1),$acceso[0]));
	         print('</table>');
	         
       break;
       
       case 4:
	     
	         print('<table>');
	         print(desplegar_checks('bodega', 'bod_id, bod_glosa', $acceso[3], 
           'bod_proveedores=false AND bod_inter=false', 'ORDER BY bod_glosa',
           'acceso_check_'.($acceso[0]*1),$acceso[0]));
	         print('</table>');
	         
       break;
       
       case 5:
       
	         print('<div class="sub-content2" 
           style="height:130px;font-size:10px;overflow:auto;">
           <table>');
           
           print(desplegar_checks('bodega', 'bod_id, bod_glosa', $acceso[3], 
           '1=1', 'ORDER BY bod_glosa',
           'acceso_check_'.($acceso[0]*1),$acceso[0]));
	         
           print(desplegar_checks('centro_costo', 'centro_ruta, centro_nombre', 
           $acceso[4], 
           "centro_gasto", 
           'ORDER BY centro_ruta',
           'acceso_check_'.($acceso[0]*1),$acceso[0], 
           '<font style="font-style:italic;color:#555555;">', 
           '</font>'));
	         
           print('</table></div>');
	         
       break;
	     
       case 6:
	     
	         print('<table>');
	         print(desplegar_checks('bodega', 'bod_id, bod_glosa', $acceso[3], 
           'bod_controlados=true', 'ORDER BY bod_glosa',
           'acceso_check_'.($acceso[0]*1),$acceso[0]));
	         print('</table>');
	         
       break;
       
       case 7:
	     
	         print('<table>');
	         print(desplegar_checks('bodega', 'bod_id, bod_glosa', $acceso[3], 
           'bod_despacho=true', 'ORDER BY bod_glosa',
           'acceso_check_'.($acceso[0]*1),$acceso[0]));
	         print('</table>');
	         
       break;

       case 8:
       
	         print('<div class="sub-content2" 
           style="height:130px;font-size:10px;overflow:auto;">
           <table>');
           
           print(desplegar_checks('centro_costo', 'centro_ruta, centro_nombre', 
           $acceso[4], 
           "true", 
           'ORDER BY centro_ruta',
           'acceso_check_'.($acceso[0]*1),$acceso[0], 
           '<font style="font-style:italic;color:#555555;">', 
           '</font>'));
	         
           print('</table></div>');
	         
       break;

       case 10:
       
	        print('<div class="sub-content2" 
           style="height:130px;font-size:10px;overflow:auto;">
           <table>');
           
           print(desplegar_checks('especialidades', 'esp_id, esp_desc', 
           $acceso[4], 
           "esp_padre_id > 0", 
           'ORDER BY esp_desc',
           'acceso_check_'.($acceso[0]*1),$acceso[0], 
           '<font style="font-style:italic;color:#555555;">', 
           '</font>'));
	         
           print('</table></div>');
	         
       break;


       
    }
    
    print('</td></tr>');
  
  }
  
  }
  
  print('</table></form>');

?>

  <br>
  <center>
	<div class='boton'>
	<table><tr><td>
	<img src='iconos/building_go.png'>
	</td><td>
	<a href='#' onClick='guardar_accesos();'><span id='guardar_texto'>Guardar  Accesos del Usuario...</span></a>
	</td></tr></table>
	</div>
	</center>
	
	<script>
	
	guardar_accesos = function() {
	    
	    var myAjax = new Ajax.Request(
      'administracion/funcionarios/sql_accesos.php',
      {
        method: 'post',
        parameters: $('func_id').serialize()+'&'+$('accesos_func').serialize(),
        onComplete: function(respuesta) {
          
          if(respuesta.responseText=='OK') {
            $("func_accesos").win_obj.close();
          } else {
            alert('ERROR:\n\n'+respuesta.responseText);
            $("func_accesos").win_obj.close();
          }
          
        }
  
      });
	
    	
  }
  
  seleccionar_todo = function(valor) {
  
    checks = $('accesos_func').getElementsByTagName('input');
    
    for(i=0;i<checks.length;i++) {
      checks.checked=valor;
    }
  
  }
	
	</script>
