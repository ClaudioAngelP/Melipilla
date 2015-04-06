<?php

  require_once('../conectar_db.php');
  
  $tipo=($_GET['tipo']*1);
  $id_articulo = ($_GET['tipotalonario_id']*1);
  $id_bodega = ($_GET['bodega_id'])*1;
  $art_num = ($_GET['art_num']*1);
  $nro_talonarios = ($_GET['nro_talonarios']*1);
  $cadena_actual=($_GET['cadena_actual']);
  
  $talonarios_vigentes = pg_query("
  SELECT 
  talonario_id, talonario_numero, talonario_inicio, talonario_final 
  FROM talonario
  LEFT JOIN pedido_detalle ON talonario_pedidod_id=pedidod_id
  WHERE talonario_estado=0 AND talonario_bod_id=".$id_bodega." AND 
  talonario_tipotalonario_id=".tipo_talonario($id_articulo)." AND
  COALESCE(pedidod_estado,true);
  ");
  
  print("
    <html><title>Seleccionar Talonarios</title>
  ");
  
  cabecera_popup('..');
  
  $func_tal=funcionario_talonario($id_articulo);
  
  if($func_tal==1) {
    $carg_func = 'cargar_funcionario';
    $busc_func = 'buscar_funcionarios';
  } else if($func_tal==2) {
    $carg_func = 'cargar_medico';
    $busc_func = 'buscar_medicos';
  }
  
  $talcadena='';
	
	for($i=0;$i<pg_num_rows($talonarios_vigentes);$i++) {
  
    $talid = pg_fetch_result($talonarios_vigentes, $i, 0);
  
    $talcadena .= $talid;
    
    if($i<pg_num_rows($talonarios_vigentes)-1) $talcadena.=','; 
  
  }
  
  $talonarios_sels = explode('|',$cadena_actual);
  
  function tal_sel($tal_id) {
  
    GLOBAL $talonarios_sels;
    
    for($i=0;$i<count($talonarios_sels);$i++) {
  
      $talodata = explode('-', $talonarios_sels[$i]);
  
      if($talodata[0]==$tal_id) return true;
    
    }
    
    return false;
  
  }
  
  function tal_func($tal_id) {
  
    GLOBAL $talonarios_sels;
    
    for($i=0;$i<count($talonarios_sels);$i++) {
  
      $talodata = explode('-', $talonarios_sels[$i]);
  
      if($talodata[0]==$tal_id) return $talodata[1];
    
    }
    
    return false;
  
  }
  
  
?>

<script>

var talonarios = new Array();


tiposel=<?php echo $tipo; ?>;
nro_sel=<?php echo $nro_talonarios; ?>;
talonarios=[<?php echo $talcadena; ?>];

guardar_talonarios = function() {

  selcadena='';
  
  var talselx=0;
  
  for(i=0;i<talonarios.length;i++) {
  
    if($('seltal_'+talonarios[i]).checked) {
      selcadena+=talonarios[i]+'-';
      
      if(tiposel==1) {
        
        id_func=$('funcid_'+talonarios[i]).value*1;
        
        if(id_func==0) {
          alert('Debe seleccionar un funcionario responsable para el '+
                'talonario.');
          $('func_'+talonarios[i]).select();
          $('func_'+talonarios[i]).focus();
          
          return;
        }
        
        selcadena+=id_func;
      
      } else
        selcadena+='0';
        
      if(i<talonarios.length-1) 
        selcadena+='|';
        
      talselx++;
    }
    
  }

  if(talselx<nro_sel) {
    alert('Faltan talonarios por seleccionar. Tiene '+talselx+' de '+
    nro_sel+'.');
    return;
  }
  
  window.opener.articulos[<?php echo $art_num; ?>][8]=selcadena;
    
  window.close();
  
}

function chequear_checkbox() {

  var talosels=0;

  for(i=0;i<talonarios.length;i++) {
    if($('seltal_'+talonarios[i]).checked) {
      if(tiposel==1) {
        $('func_'+talonarios[i]).disabled=false;
        $('funcbuscar_'+talonarios[i]).style.display='';
      }
      talosels++;
    } else if(tiposel==1) {
        $('funcid_'+talonarios[i]).value='';
        $('func_'+talonarios[i]).value='';
        $('func_'+talonarios[i]).disabled=true;
        $('funcbuscar_'+talonarios[i]).style.display='none';
        $('func_tal_'+talonarios[i]).innerHTML='(Seleccionar...)';
        
    }
      
  }
  
  $('ver_contador').innerHTML='Ha seleccionado <b>'+
  talosels+' de '+nro_sel+'</b>';
  
  if(talosels==nro_sel) {
    
    for(i=0;i<talonarios.length;i++)
      if(!$('seltal_'+talonarios[i]).checked)
        $('seltal_'+talonarios[i]).disabled=true;
    
  } else {
  
    for(i=0;i<talonarios.length;i++)
        $('seltal_'+talonarios[i]).disabled=false;
  
  }

}

function cargar_funcionario(id_tal) {
  
    var myAjax = new Ajax.Request(
    'nombre_funcionario.php',
    {
      method: 'get',
      parameters: 'func_rut='+encodeURIComponent($('func_'+id_tal).value),
      onComplete: function(respuesta) {
      
        func_arr = respuesta.responseText.evalJSON();
      
        $('funcid_'+id_tal).value=func_arr[0];
        $('func_tal_'+id_tal).innerHTML=func_arr[1];
        
      }
    }
    );
  
  }

function cargar_medico(id_tal) {
  
    var myAjax = new Ajax.Request(
    'nombre_medico.php',
    {
      method: 'get',
      parameters: 'func_rut='+encodeURIComponent($('func_'+id_tal).value),
      onComplete: function(respuesta) {
      
        func_arr = respuesta.responseText.evalJSON();
      
        $('funcid_'+id_tal).value=func_arr[0];
        $('func_tal_'+id_tal).innerHTML=func_arr[1];
        
      }
    }
    );
  
  }
  

</script>

<body class='fuente_por_defecto popup_background'>
  
  <table style='width:100%'>
  <tr class='tabla_header'>
  <td colspan=<?php if($tipo==0) echo '4'; else echo '6'; ?> 
  id='ver_contador'>
  Ha seleccionado <b>0 de <?php echo $nro_talonarios; ?></b></td>
  </tr>
  <tr class='tabla_header' style='font-weight: bold;'>
  <td>Sel.</td>

  <td>Nro. Inicial</td>
  <td>Nro. Final</td>
  
<?php     //<td>Nro. Talonario</td>

  if($tipo==1) {
  
?>

  <td style='width:300px;' colspan=2>
  
  <?php 
  
  if($func_tal==1) print('Funcionario Responsable');
  if($func_tal==2) print('M&eacute;dico Responsable');
  
  
  ?>
  
  </td>

<?php
  
  }

?>
  </tr>

<?php

  for($i=0;$i<pg_num_rows($talonarios_vigentes);$i++) {

    (($i%2)==0) ? $clase='tabla_fila' : $clase='tabla_fila2';

    $datostal = pg_fetch_row($talonarios_vigentes);

    if(tal_sel($datostal[0]))
      $checkbox = 'checked';
    else
      $checkbox = '';

    print('
    <tr class="'.$clase.'">
    <td><center>
    <input type="checkbox" '.$checkbox.' onClick="chequear_checkbox();"');

    //if($tipo==1)
    //print('onMouseUp="activar_func('.$datostal[0].');"');

    print('
    id="seltal_'.$datostal[0].'" name="seltal_'.$datostal[0].'">
    </center></td>

    <td style="text-align: right;">'.$datostal[2].'</td>
    <td style="text-align: right;">'.$datostal[3].'</td>
    ');

    if($tipo==1) {

    if(!tal_sel($datostal[0])) {

    print('<td>
    <input type="hidden" id="funcid_'.$datostal[0].'"
    name="funcid_'.$datostal[0].'" value="">
    <input type="text" id="func_'.$datostal[0].'" size=10
    onKeyPress="'.$carg_func.'('.$datostal[0].');"
    name="func_'.$datostal[0].'" style="font-size:10px;">
    <img src="../iconos/zoom_in.png" id="funcbuscar_'.$datostal[0].'"
    onClick="'.$busc_func.'($(\'func_'.$datostal[0].'\'),
            function() { '.$carg_func.'('.$datostal[0].'); }, \'../\' );">
    </td><td style="width:200px;font-size:10px;" id="func_tal_'.$datostal[0].'">
    (Seleccionar...)
    </td>');
    
    } else {
    
    $id_func=tal_func($datostal[0]);
    
    if($func_tal==1)
      $func = cargar_registro('SELECT func_rut, func_nombre FROM funcionario WHERE func_id='.$id_func);
    elseif($func_tal==2)
      $func = cargar_registro("SELECT doc_rut AS func_rut, doc_paterno || ' ' ||  doc_materno || ' ' || doc_nombres AS func_nombre FROM doctores WHERE doc_id=".$id_func);
    
    
    print('<td>
    <input type="hidden" id="funcid_'.$datostal[0].'" 
    name="funcid_'.$datostal[0].'" value="'.$id_func.'">
    <input type="text" id="func_'.$datostal[0].'" size=10
    onKeyPress="'.$carg_func.'('.$datostal[0].');" 
    value="'.$func['func_rut'].'"
    name="func_'.$datostal[0].'" 
    style="font-size:10px;">
    <img src="../iconos/zoom_in.png" id="funcbuscar_'.$datostal[0].'"
    onClick="'.$busc_func.'($(\'func_'.$datostal[0].'\'), 
            function() { '.$carg_func.'('.$datostal[0].'); }, \'../\' );">
    </td><td style="width:200px;font-size:10px;" id="func_tal_'.$datostal[0].'">
    '.$func['func_nombre'].'
    </td>');
    
    
    }
    
    }
    
    print('
    </tr>
    ');
  
  }
  
?>

  <tr>
  <td colspan=<?php if($tipo==0) echo '4'; else echo '6'; ?> >
  <center>
	<table><tr><td>
		
		<div class='boton'>
		<table><tr><td>
		<img src='../iconos/accept.png'>
		</td><td>
		<a href='#' onClick='guardar_talonarios();'>Seleccionar Talonarios...</a>
		</td></tr></table>
		</div>
		
  </td></tr></table>
  </center>
	
  </td>
  </tr>
  </table>
  </body>
  
  <script>
    
    if(nro_sel>=talonarios.length) {
    
      for(a=0;a<talonarios.length;a++) {
      
        $('seltal_'+talonarios[a]).checked=true;
      
      }
      
      $('ver_contador').innerHTML='Ha seleccionado <b>'+
      nro_sel+' de '+nro_sel+'</b>';
  
    }
    
    chequear_checkbox();
      
  </script>
  
  </html>
