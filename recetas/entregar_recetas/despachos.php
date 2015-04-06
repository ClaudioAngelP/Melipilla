<?php

  require_once('../../conectar_db.php');

  $receta_id=($_GET['receta_id']*1);
  
  $receta=cargar_registro("
      SELECT 
        receta_id,
        doc_rut,
        doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS doc_nombre,
        date_trunc('second', receta_fecha_emision) AS receta_emision,
        receta_comentarios,
        receta_diag_cod,
        diag_desc,
        COALESCE(receta_cronica, false) AS receta_cronica,
        tipotalonario_nombre,
        receta_numero,
        receta_tipotalonario_id
      FROM receta
      LEFT JOIN doctores ON receta_doc_id=doc_id
      LEFT JOIN diagnosticos ON receta_diag_cod=diag_cod
      LEFT JOIN receta_tipo_talonario 
      ON receta_tipotalonario_id=tipotalonario_id
      
      WHERE receta_id=$receta_id
  ");
  
  $detalle=cargar_registros_obj("
      SELECT *,
      (((recetad_dias*24)/recetad_horas)*recetad_cant) AS art_cantidad
      FROM recetas_detalle 
      JOIN articulo ON recetad_art_id=art_id
      WHERE recetad_receta_id=$receta_id
  ");

  
  $detalle_mov=cargar_registros_obj("
      SELECT *, date_trunc('second', log_fecha) AS log_fecha FROM logs 
      LEFT JOIN stock ON stock_log_id=log_id
      WHERE 
      log_recetad_id IN (
        SELECT recetad_id FROM recetas_detalle 
        WHERE recetad_receta_id=$receta_id
      )
      
  ");


?>

<html><title>Detalle de Despacho de Medicamentos</title>
  
<?php  cabecera_popup('../..'); ?>

<script>

imprimir_talonario = function() {

  win = window.opener.open('talonario.php?receta_id=<?php echo $receta_id; ?>',
                            'win_talonario');
  win.focus();
}

</script>


<body class='fuente_por_defecto popup_background'>

      <table width=100% style='font-size:11px;'>
      <tr><td style='text-align: right; width:100px;'>Fecha Emision:</td>
      <td colspan=7><i><?php echo $receta['receta_emision']; ?></i></td></tr>
      <tr> 
      <td style='text-align: right;'>Tipo de Receta:</td>
      <td colspan=7><b><?php echo htmlentities($receta['tipotalonario_nombre']); ?></b></td>
      </tr>
      
      <?php 
      
        if($receta['receta_tipotalonario_id']!=0) {
      
      ?>
      
      <tr> 
      <td style='text-align: right;'>N&uacute;mero Receta:</td>
      <td colspan=7 style='font-size: 16px;'>
      <b><?php echo $receta['receta_numero']; ?></b></td>
      </tr>
      
      <?php  } ?>
      
      <tr>
      <td style='text-align: right;'>Rut M&eacute;dico:</td>
      <td colspan=7>
      <b><?php echo $receta['doc_rut']; ?></b></td>
      </tr>
      <tr>
      <td style='text-align: right;'>Nombre M&eacute;dico:</td>
      <td colspan=7>
      <b><?php echo htmlentities($receta['doc_nombre']); ?></b></td>
      </tr>
      <tr>
      <td style='text-align: right;'>Diagn&oacute;stico:</td>
      <td colspan=6>
      <div align='text-align:justify; font-weight: bold;'>
      <b><i><?php echo htmlentities($receta['receta_diag_cod']); ?></i>
      <?php echo htmlentities($receta['diag_desc']); ?></b>
      </div>
      </td>
      </tr>
      <tr>
      <td style='text-align: right;'>Observaciones:</td>
      <td colspan=7>
      <div align='text-align:justify;'>
      <?php echo htmlentities($receta['receta_comentarios']); ?>
      </div>
      </td>
      </tr>
      <tr>
      <td style='text-align:right;'>Cr&oacute;nica:</td>
      <td><b><?php if($receta['receta_cronica']=='t') print('S&iacute;'); else print('No'); ?></b></td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr class='tabla_header' style='font-weight: bold;'>
      <td colspan=8>Detalle de Medicamentos Recetados y Entregados</td></tr>
      <tr class='tabla_header' style='font-weight: bold;'>
        <td>Fecha Despacho</td>
        <td>Codigo Int.</td>
        <td>Glosa</td>
        <td>Stock Recetado</td>
        <td>Entregado</td>
        <td>Saldo</td>
      </tr>

<?php 

    for($i=0;$i<count($detalle);$i++) {
      $arts[$detalle[$i]['art_id']]['art_codigo']=$detalle[$i]['art_codigo'];
      $arts[$detalle[$i]['art_id']]['art_glosa']=$detalle[$i]['art_glosa'];
      $arts[$detalle[$i]['art_id']]['recetad_cant']=$detalle[$i]['recetad_cant'];
      $arts[$detalle[$i]['art_id']]['recetad_horas']=$detalle[$i]['recetad_horas'];
      $arts[$detalle[$i]['art_id']]['recetad_dias']=$detalle[$i]['recetad_dias'];
      $arts[$detalle[$i]['art_id']]['saldo']=floor($detalle[$i]['art_cantidad']);      
      $arts[$detalle[$i]['art_id']]['art_cantidad']=floor(($detalle[$i]['recetad_dias']*24/$detalle[$i]['recetad_horas'])*$detalle[$i]['recetad_cant']);      
    }

    if($detalle_mov)
    for($i=0;$i<count($detalle_mov);$i++) {
    
      $det=$detalle_mov[$i];
      $art=$arts[$det['stock_art_id']];
      
      $arts[$det['stock_art_id']]['saldo']+=$det['stock_cant'];
      
      if($receta['receta_cronica']=='t') {
        $cantidad=floor($art['art_cantidad']);
      } else {
        $cantidad=floor($art['art_cantidad']);
      }
      
      ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
      
      print("
      <tr class='$clase'>
      <td style='text-align:center;text-style:italic;'>".$det['log_fecha']."</td>
      <td style='text-align:right;'>".$art['art_codigo']."</td>
      <td>".htmlentities($art['art_glosa'])."</td>
      <td style='text-align:right;'>".number_format($cantidad,1,',','.')."</td>
      <td style='text-align:right;'>".number_format(-($det['stock_cant']),1,',','.')."</td>
      <td style='text-align:right;'>".number_format($arts[$det['stock_art_id']]['saldo'],1,',','.')."</td>
      </tr>
      ");
    
    }
    
?>    

      <tr><td>&nbsp;</td></tr>
      <tr class='tabla_header' style='font-weight: bold;'>
      <td colspan=8>Res&uacute;men</td></tr>
      <tr class='tabla_header' style='font-weight: bold;'>
        <td>Codigo Int.</td>
        <td colspan=2>Glosa</td>
        <td>Stock Recetado</td>
        <td>Entregado</td>
        <td>Saldo</td>
      </tr>

    
<?php 

    $i=0;
    
    foreach($arts as $art) {
    
      $cantidad=($art['recetad_dias']*24/$art['recetad_horas'])*$art['recetad_cant'];
 
      $cuota=floor($cantidad/($art['recetad_dias']/30));
 
      $c=floor(($cantidad-$art['saldo'])/$cuota);
      $t=floor($cantidad/$cuota);

      $cantidad=floor($cantidad);

      ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
      
      print("
      <tr class='$clase'>
      <td style='text-align:right;'>".$art['art_codigo']."</td>
      <td colspan=2>".htmlentities($art['art_glosa'])."</td>
      <td style='text-align:right;'>".number_format($cantidad,1,',','.')."</td>
      <td style='text-align:center;'>( ".$c.' / '.$t." )</td>
      <td style='text-align:right;'>".number_format($art['saldo'],1,',','.')."</td>
      </tr>
      ");
      
      $i++;
    
    }

?>


      </table>
      <br><br>
    <center>
    <div class='boton'>
		<table><tr><td>
		<img src='../../iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_talonario();'>
		Imprimir Talonario...</a>
		</td></tr></table>
		</div>
    </center>


</body>
</html>
