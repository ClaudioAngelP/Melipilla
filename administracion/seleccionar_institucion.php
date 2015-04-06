<?php

  require_once('../conectar_db.php');
  
  $cnombre=$_GET['cnombre'];
  $cid=$_GET['cid'];
  
  $b=isset($_GET["buscar"]);
  
  if($b AND trim($_GET["buscar"])=='') {
    $b=false;
  }
  
  if($b) {
    
    $cadena=pg_escape_string($_GET["buscar"]);
    
    $inst=cargar_registros_obj("
      SELECT * FROM instituciones 
      WHERE 
      inst_servicio_cod_ifl IN ($sgh_servicios_ifl)
      AND inst_nombre ILIKE '%$cadena%'
      ORDER BY inst_nombre
    ");
    
    $a=array();
    
    for($i=0;$i<count($inst);$i++) {
    
      $n=count($a);
      $a[$n]=$inst[$i]['inst_servicio_cod_ifl'];
    
    }
    
    $a=array_values(array_unique($a));
    
    $busca='';
    
    for($i=0;$i<count($a);$i++)
      if(($i+1)<count($a))
        $busca.=$a[$i].','; else $busca.=$a[$i];
        
    if($busca=='') $busca='-1';
    
  } else {
  
    $busca=$sgh_servicios_ifl;
  
  }
  
  $servicios=cargar_registros_obj("
    SELECT * FROM servicios_salud
    WHERE servicio_codigo_ifl IN (".$busca.") 
    ORDER BY servicio_nombre 
  ");
  
  

?>

<html>
<title>Seleccionar Instituci&oacute;n de Salud</title>

<?php cabecera_popup('..'); ?>

<script>

function sel(id, nombre) {
  window.opener.$('<?php echo $cid; ?>').value=id;
  window.opener.$('<?php echo $cnombre; ?>').value=nombre;
  window.close();
}

function mostrar(id) {
  
  if($('contenido_'+id).style.display=='') {
    $('contenido_'+id).style.display='none';
    $('icono_'+id).src='../iconos/treenode_open.png';
  } else {
    $('contenido_'+id).style.display='';
    $('icono_'+id).src='../iconos/treenode_close.png';
  }

}

</script>

<body class='fuente_por_defecto popup_background' onLoad='$("buscar").focus();'>

<div class='sub-content'>
<img src='../iconos/building.png'>
<b>Servicios de Salud - Buscar Instituciones</b>
</div>

<div class='sub-content'>
<table style='width:100%;text-align:center;'>
<tr><td style='text-align:right;'>Buscar:</td>
<td>
<form id='frm' name='frm' action='seleccionar_institucion.php'
method='get' onSubmit=''>
<input type='hidden' id='cid' name='cid' value='<?php echo htmlentities($cid); ?>'>
<input type='hidden' id='cnombre' name='cnombre' value='<?php echo htmlentities($cnombre); ?>'>
<input type='text' id='buscar' name='buscar' size=30 value=''>
</td>
<td>
<input type='button' 
onClick='frm.submit();'
value='Realizar B&uacute;squeda...'>
</td>
</tr>
</table>
</div>

<div class='sub-content2' style='height:300px;overflow:auto;'>
<table style='width:100%;'>
<?php 

if($servicios)
for($i=0;$i<count($servicios);$i++) {

  if($b) {
    $display='';
    $icono='treenode_close.png';
  } else {
    $display='display:none;';
    $icono='treenode_open.png';
  }

  print("
  <tr onClick='mostrar(".$servicios[$i]['servicio_id'].");'>
  <td style='text-align:left;border:1px solid #555555;cursor:pointer;'>
  
  <table style='border:none;'><tr><td>
  <img src='../iconos/$icono' 
  id='icono_".$servicios[$i]['servicio_id']."'>
  </td><td>
  ".htmlentities($servicios[$i]['servicio_nombre'])."
  </td></tr></table>
  </td></tr>
  <tr id='contenido_".$servicios[$i]['servicio_id']."' 
  style='$display' class='tabla_fila'>
  <td>
  <table style='width:100%;'>
  ");
  
  if(!$b)
  $inst=cargar_registros_obj("
    SELECT * FROM instituciones 
    WHERE inst_servicio_cod_ifl=".$servicios[$i]['servicio_codigo_ifl']."
    ORDER BY inst_nombre
  ");
  
  if($inst)
  for($n=0;$n<count($inst);$n++) {
  
    $clase=($n%2==0)?'tabla_fila':'tabla_fila2';
  
    print("<tr class='$clase' style='cursor:pointer;'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"$clase\"'
    onClick='sel(".$inst[$n]['inst_id'].",\"".$inst[$n]['inst_nombre']."\");'>
    <td style='width:25px;'></td><td>
    ".htmlentities($inst[$n]['inst_nombre'])."
    </td></tr>");
  
  }
  
  print("
  </table>
  </td>
  </tr>
  ");

}
else
print("
No hay coincidencias para su b&uacute;squeda.
");

?>
</table>

</div>

</body>
</html>
