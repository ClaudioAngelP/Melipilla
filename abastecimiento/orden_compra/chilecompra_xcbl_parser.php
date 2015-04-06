<?php

  function xml_get_first($tagname, $xml_object) {
    
    $content = $xml_object->get_elements_by_tagname($tagname);
    $content = $content[0]->get_content();
    
    return $content;
    
  }

  if(!$xml_file = @fopen($_FILES['archivo']['tmp_name'], "r")) {
    die("Error al abrir archivo XML [".$_FILES['archivo']['name']."].");
  }

  $contenido="";
  
  while(!feof($xml_file)) {
    $contenido.=fgets($xml_file);
  }
  
  fclose($xml_file);
  
  //  $contenido=iconv("UTF-8", "ISO-8859-1", $contenido);
  
  if(!$xml_obj=@domxml_open_mem($contenido)) {
  
    die(json_encode(Array(false,"<strong>Error procesando archivo XML 
    [".$_FILES['archivo']['name']."].")));
    
    
  }
  
  $xml_doc = $xml_obj->document_element();
  
  $ordenes_obj = $xml_doc->get_elements_by_tagname('Order');
  
  foreach($ordenes_obj AS $orden_obj) {
  
    $nro_orden = xml_get_first('BuyerOrderNumber', $orden_obj);
    $fecha_emision = xml_get_first('OrderIssueDate', $orden_obj);
    $comentario_ref = xml_get_first('QuoteReference', $orden_obj);
    
    print("
    <table>
    <tr><td colspan=2>Datos Generales</td></tr>
    <tr><td style='text-align: right;'>Orden de Compra:</td>
    <td style='font-weight: bold;'>".$nro_orden."</td></tr>
    <tr><td style='text-align: right;'>Fecha de Emisi&oacute;n:</td>
    <td style='font-style: italic;'>".$fecha_emision."</td></tr>
    <tr><td style='text-align: right;'>Comentario de Referencia:</td>
    <td style='font-style: italic;'>".htmlentities($comentario_ref)."</td></tr>
    </table>
    ");
  
  }
  
?>
