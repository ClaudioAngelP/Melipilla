<?php

  require_once('../../conectar_db.php');

  $receta_id=($_GET['receta_id']*1);
  if(isset($_GET['edicion'])) {
    $edicion=true;
  } else {
    $edicion=false;
  }

  $medicamentos_q = pg_query($conn, "
      SELECT
      recetad_id,
      art_id,
      art_codigo,
      art_glosa,
      COALESCE(clasifica_nombre,'(No Asignado...)'),
      COALESCE(forma_nombre,'(No Asignado...)'),
      ((recetad_dias*24)/recetad_horas*recetad_cant),
      recetad_cant,
      recetad_horas,
      recetad_dias,
      COALESCE((
         SELECT
			   SUM(stock_cant)
			   FROM stock
			   JOIN logs ON log_id=stock_log_id
			   WHERE log_recetad_id=recetad_id
      ),0) AS stock_entregado

      FROM recetas_detalle
      JOIN articulo ON recetad_art_id=art_id
      LEFT JOIN bodega_clasificacion ON art_clasifica_id=clasifica_id
      LEFT JOIN bodega_forma ON art_forma=forma_id
      WHERE recetad_receta_id=".$receta_id."
  ");


  print('
  <table width="100%">
  <tr class="tabla_header" style="font-weight: bold;">
  <td colspan=3>Datos del Medicamento</td>
  <td colspan=3>Dosis</td>
  <td colspan=2>Totales</td>
  <td rowspan=2>Diferencia</td>
  ');

  if($edicion)
    print('<td width=10% rowspan=2>Editar</td>');


  print('
  </tr>
  <tr class="tabla_header" style="font-weight: bold;">
  <td width=15%>C&oacute;digo Int.</td>
  <td width=40% colspan=2>Glosa</td>
  <td>Cant.</td>
  <td>c/Horas</td>
  <td>c/D&iacute;as</td>
  <td>Recetado</td>
  <td>Entregado</td>
  </tr>
  ');

  for($i=0;$i<pg_num_rows($medicamentos_q);$i++) {
    $medicamento_a = pg_fetch_row($medicamentos_q);

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';

    print('
    <tr class="'.$clase.'" id="fila_recetad_'.$medicamento_a[0].'">
    <td style="text-align: right; font-weight: bold;">
    '.htmlentities($medicamento_a[2]).'
    </td>
    <td style="font-weight: bold;" colspan=2>
    '.htmlentities($medicamento_a[3]).'
    </td>
    <td style="text-align: right;">'.($medicamento_a[7]).'</td>
    <td style="text-align: right;">'.($medicamento_a[8]).'</td>
    <td style="text-align: right;">'.($medicamento_a[9]).'</td>
    <td style="text-align: right;">'.($medicamento_a[6]).'</td>
    <td style="text-align: right; font-weight: bold;">'.($medicamento_a[10]*-1).'</td>
    <td style="text-align: right;">
    '.($medicamento_a[6]+$medicamento_a[10]).'
    </td>
    ');

    if($edicion)
      print('
      <td><center>
      <img src="iconos/page_edit.png" id="_edita_recetad_'.$medicamento_a[0].'"
      style="cursor: pointer;"
      onClick="__editar_recetad('.$medicamento_a[0].');">

      <img src="iconos/accept.png" id="_acepta_recetad_'.$medicamento_a[0].'"
      style="cursor: pointer; display: none;"
      onClick="__aceptar_recetad('.$medicamento_a[0].');">

      <img src="iconos/delete.png" id="_cancela_recetad_'.$medicamento_a[0].'"
      style="cursor: pointer; display: none;"
      onClick="__cancelar_recetad('.$medicamento_a[0].');">
      </center>

      </td>
      ');

    print('
  <input type="hidden"
  id="__cant_'.$medicamento_a[0].'" name="__cant_'.$medicamento_a[0].'"
  value="'.$medicamento_a[7].'">
  <input type="hidden"
  id="__horas_'.$medicamento_a[0].'" name="__horas_'.$medicamento_a[0].'"
  value="'.$medicamento_a[8].'">
  <input type="hidden"
  id="__dias_'.$medicamento_a[0].'" name="__dias_'.$medicamento_a[0].'"
  value="'.$medicamento_a[9].'">
  <input type="hidden"
  id="__cant_n_'.$medicamento_a[0].'" name="__cant_n_'.$medicamento_a[0].'"
  value="'.$medicamento_a[7].'">
  <input type="hidden"
  id="__horas_n_'.$medicamento_a[0].'" name="__horas_n_'.$medicamento_a[0].'"
  value="'.$medicamento_a[8].'">
  <input type="hidden"
  id="__dias_n_'.$medicamento_a[0].'" name="__dias_n_'.$medicamento_a[0].'"
  value="'.$medicamento_a[9].'">
  </tr>
    ');

    $entregas = pg_query($conn, "
    SELECT

    date_trunc('second', log_fecha),
    func_nombre,
    bod_glosa,
    -(stock_cant) AS cantidad,
    log_id

    FROM receta

    JOIN recetas_detalle ON recetad_receta_id=receta_id
    JOIN logs ON log_recetad_id=recetad_id
    JOIN stock ON stock.stock_log_id=logs.log_id
    JOIN funcionario ON receta_func_id=func_id
    JOIN bodega ON stock_bod_id=bod_id

    WHERE receta_id=".$receta_id." AND stock_art_id=".$medicamento_a[1]."

    ORDER BY log_fecha"
    );
   // JOIN funcionario ON log_func_if=func_id   
    $cadena='';

    for($a=0;$a<pg_num_rows($entregas);$a++) {

      $entrega_a = pg_fetch_row($entregas);

      $cadena.=$entrega_a[4];
      if($a<(pg_num_rows($entregas)-1)) $cadena.='|';

      print("
      <tr id='fila_log_".$entrega_a[4]."'>
      <td rowspan=2>&nbsp;</td>
      <td style='text-align: center;' rowspan=2>".$entrega_a[0]."</td>
      <td style='text-align: right; font-weight: bold;'>Funcionario:</td>
      <td colspan=4>".htmlentities($entrega_a[1])."</td>
      <td style='text-align: right;' rowspan=2>".$entrega_a[3]."</td>
      ");

      if($edicion)
      print('
      <td rowspan=2>
      <center>


      </center>
      <input type="hidden"
      id="valor_log_'.$entrega_a[4].'" name="valor_log_'.$entrega_a[4].'"
      value="'.$entrega_a[3].'">
      <input type="hidden"
      id="n_valor_log_'.$entrega_a[4].'" name="n_valor_log_'.$entrega_a[4].'"
      value="'.$entrega_a[3].'">
      </td>');

      print("
      </tr>
      <tr>
      <td style='text-align: right; font-weight: bold;'>Ubicaci&oacute;n:</td>
      <td colspan=4>".htmlentities($entrega_a[2])."</td>
      </tr>
      ");

    }

  print('<input type="hidden"
  id="logs_recetad_'.$medicamento_a[0].'"
  name="logs_recetad_'.$medicamento_a[0].'"
  value="'.$cadena.'">');


  }


   print('
  </table>
  ');



?>



