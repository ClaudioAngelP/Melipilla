<?php

  require_once("../../conectar_db.php");
  
  $buscar = pg_escape_string($_GET['codigo']);
  
  $buscar = str_replace('*', '%', $buscar);
  
  $arts = pg_query("
  SELECT art_id, art_codigo, art_glosa, forma_nombre 
  FROM articulo LEFT JOIN bodega_forma ON forma_id=art_forma
  WHERE (art_codigo) ILIKE '".$buscar."' AND art_activado ORDER BY art_codigo
  ");
  
  print("<table cellpadding=0 cellspacing=0 
  style='width: 100%; font-size: 10px;'>
  <tr class='tabla_header'>
  <td style='width: 70px;'>C&oacute;digo Int.</td><td>Glosa Art&iacute;culo</td></tr>");
  
  $numarts = pg_num_rows($arts);
  
  $cadena_arts = '';
  
  for($i=0;$i<$numarts;$i++) {
  
    ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
  
    $_arts = pg_fetch_row($arts);
  
    if($i<($numarts-1))
      $cadena_arts .= $_arts[0].'!';
    else
      $cadena_arts .= $_arts[0];
      
    print("
    <tr class='$clase' 
    onClick='seleccionar_art(".$_arts[0].", \"".$_arts[1]."\");' 
    style='cursor: pointer;'>
      <td rowspan=2 style='text-align: center;'>".htmlentities($_arts[1])."</td>
      <td>".htmlentities($_arts[2])."</td>
    </tr>
    <tr class='$clase' 
    onClick='seleccionar_art(".$_arts[0].", \"".$_arts[1]."\");' 
    style='cursor:pointer;'>
      <td><i>".htmlentities($_arts[3])."</i></td>
    </tr>
    ");
    
    if($numarts==1) 
      print("<script> seleccionar_art(".$_arts[0].", \"".$_arts[1]."\"); </script>");
      
  }
  
  print('
  <input type="hidden" name="art_ids" id="art_ids" value="'.$cadena_arts.'">
  </table>');

?>
