<?php

  require_once('../../conectar_db.php');

  $codigo = ($_GET['prod_id']);
  $bodega = ($_GET['bodega']*1);
  $control = ($_GET['control']*1);

      $codigo = pg_escape_string($_GET['prod_ids']);
      $codigos = str_replace('!', ',', $codigo);

      $detalle_gasto = pg_query($conn, "
      SELECT
      date_trunc('second', log_fecha) AS log_fecha,
      tipotalonario_medicamento_clase,
      receta_numero,
      art_codigo,
      art_glosa,
      -(SUM(stock_cant)),
      receta.receta_id,
      articulo.art_id
      FROM logs
      JOIN stock ON
        stock_log_id=log_id
        AND stock_art_id IN ($codigos)
        AND stock_bod_id = $bodega
      JOIN articulo ON stock_art_id=art_id
      JOIN recetas_detalle ON log_recetad_id=recetad_id
      JOIN receta ON recetad_receta_id=receta_id
      JOIN receta_tipo_talonario ON tipotalonario_id=receta_tipotalonario_id
      LEFT JOIN reposicion_detalle ON
          reposicion_detalle.receta_id=receta.receta_id
      WHERE articulo.art_reposicion AND
      articulo.art_control=$control AND
      log_tipo = 9 AND reposicion_detalle.repo_id IS NULL
      GROUP BY log_fecha, tipotalonario_medicamento_clase,
      receta_numero,art_codigo,art_glosa,receta.receta_id,articulo.art_id
      ORDER BY art_codigo
      ");

      $totales = pg_query($conn, "
      SELECT art_codigo, art_glosa,
      -(SUM(stock_cant)), art_id
      FROM stock
      JOIN articulo ON stock_art_id=art_id
      JOIN logs ON stock_log_id=log_id
      JOIN recetas_detalle ON log_recetad_id=recetad_id
      JOIN receta ON recetad_receta_id=receta_id
      LEFT JOIN reposicion_detalle ON
          reposicion_detalle.receta_id=receta.receta_id
      WHERE articulo.art_reposicion AND
        stock.stock_art_id IN ($codigos) AND
        articulo.art_control=$control AND
        stock.stock_bod_id=$bodega AND
        log_tipo = 9 AND reposicion_detalle.repo_id IS NULL
      GROUP BY art_codigo, art_glosa, art_id
       ORDER BY art_codigo
      ");

?>

<html><title>Reposici&oacute;n de Art&iacute;culos</title>

<?php cabecera_popup('../..'); ?>

<script>

imprimir_reposicion = function() {

  window.opener.imprimirHTML($('reposicion').innerHTML);

}

var bloquear=false;

generar_reposicion = function() {

  if(bloquear) return;

  recetas = $('id_recetas').value.split(',');
  
  pasar=false;
  
  for(i=0;i<recetas.length-1;i++) {
  
    if($('receta_chk_'+recetas[i]).checked) {
      pasar=true; break;
    }
  
  }
  
  if(!pasar) {
    alert('No hay recetas v&aacute;lidas para realizar una reposici&oacute;n.'.unescapeHTML()); return;
  }

  bloquear=true;

  var myAjax = new Ajax.Request(
  'generar_reposicion.php',
  {
    method: 'post',
    parameters: $('repo').serialize(),
    onComplete: function(respuesta) {
      dato=respuesta.responseText.evalJSON(true);
      if(dato[0]) {
        window.opener.visualizador_documentos('Visualizar Pedido', 'id_pedido='+encodeURIComponent(dato[1]));
        window.close();
      } else {
        alert('ERROR:\n\n'+respuesta.responseText);
        bloquear=false;
      }
    }
  }
  );

}

recalcular = function() {

  recetas = $('id_recetas').value.split(',');
  arts = $('articulos').value.split(',');
  
  for(j=0;j<arts.length-1;j++) {
    $('art_total_'+arts[j]).value=0;
  }
  
  
  for(i=0;i<recetas.length-1;i++) {
  
    if($('receta_chk_'+recetas[i]).checked) {
      art_id=$('receta_art_'+recetas[i]).value;
      cant=($('receta_cant_'+recetas[i]).value)*1;
      $('art_total_'+art_id).value=($('art_total_'+art_id).value*1)+cant;
    }
  
  }

  for(j=0;j<arts.length-1;j++) {
    $('art_total_ver_'+arts[j]).innerHTML = number_format($('art_total_'+arts[j]).value*1, 1, ',', '.');
  }
  
}

</script>

<body class='fuente_por_defecto popup_background'>
<center>


<table><tr><td>
<div class='boton'>
	<table><tr><td>
	<img src='../../iconos/printer.png'>
	</td><td>
	<a href='#' onClick='imprimir_reposicion();'><span id='texto_boton'>Informe Preeliminar...</span></a>
	</td></tr></table>
</div>
</td><td>
<div class='boton'>
	<table><tr><td>
	<img src='../../iconos/accept.png'>
	</td><td>
	<a href='#' onClick='generar_reposicion();'><span id='texto_boton'>Generar Reposici&oacute;n...</span></a>
	</td></tr></table>
</div>

</td></tr></table>

<div id='reposicion'>

<form id='repo' name='repo'>
<input type='hidden' id='bodega' name='bodega' value='<?=$bodega?>'>
<table style='width:100%;font-size:11px;'>

<tr class='tabla_header' style='font-weight:bold;'>
<td>Fecha / Hora</td>
<td>Tipo Receta</td>
<td># Receta</td>
<td>C&oacute;digo Art.</td>
<td style='width:40%;'>Glosa</td>
<td>Cantidad</td>
<td>Validez</td>
</tr>

<?php 

  $id_recetas='';

  for($i=0;$i<pg_num_rows($detalle_gasto);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    $fila = pg_fetch_row($detalle_gasto);
    
    print('
    <tr class="'.$clase.'"
    onMouseOver="this.clase=this.className; this.className=\'mouse_over\';"
    onMouseOut="this.className=this.clase;"
    >
    
    <input type="hidden" 
    id="receta_art_'.$fila[6].'" name="receta_art_'.$fila[6].'" 
    value="'.$fila[7].'">
    <input type="hidden" 
    id="receta_cant_'.$fila[6].'" name="receta_cant_'.$fila[6].'" 
    value="'.$fila[5].'">
    
    <td style="text-align:center;">'.$fila[0].'</td>
    <td>'.$fila[1].'</td>
    <td style="text-align:right;">'.htmlentities($fila[2]).'</td>
    <td style="text-align:right;">'.htmlentities($fila[3]).'</td>
    <td>'.htmlentities($fila[4]).'</td>
    <td style="text-align:right;">'.number_format($fila[5], 1, ',', '.').'</td>
    <td style="text-align:center;">
    <input type="checkbox" onClick="recalcular();"
    id="receta_chk_'.$fila[6].'" name="receta_chk_'.$fila[6].'" value=""
    CHECKED>
    </td>
    </tr>
    ');
    
    $id_recetas.=$fila[6].',';
  
  }

?>

</table>
<input type='hidden' id='id_recetas' name='id_recetas' value='<?=$id_recetas?>'>

<table style='width:100%;font-size:12px;'>

<tr class='tabla_header' style='font-weight:bold;'>
<td>C&oacute;digo Art.</td>
<td>Glosa</td>
<td>Cantidad</td>
</tr>

<?php 

  $pedido = '';

  for($i=0;$i<pg_num_rows($totales);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    $fila = pg_fetch_row($totales);
    
    print('
    <tr class="'.$clase.'">
    <td style="text-align:right;">'.$fila[0].'</td>
    <td>'.htmlentities($fila[1]).'</td>
    <td style="text-align:right;" id="art_total_ver_'.$fila[3].'"
    >'.number_format($fila[2],1,',','.').'</td>
    <input type="hidden" 
    id="art_total_'.$fila[3].'" name="art_total_'.$fila[3].'" 
    value="'.$fila[2].'"> 
    </tr>
    ');
    
    $pedido.=$fila[3].',';
  
  }

?>
</table>
<input type='hidden' id='articulos' name='articulos' value='<?=$pedido?>'>
</form>
</div>
