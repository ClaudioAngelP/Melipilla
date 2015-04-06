<?php

  require_once('../conectar_db.php');
  
  $orden=$_GET['orden'];
  $prov_rut=$_GET['prov_rut'];
  $func=$_GET['func'];
  
?>

<html>
<title>Buscar &Oacute;rden de Compra</title>
<?php cabecera_popup('..'); ?>

<script>

  mostrar_proveedor=function(datos) {
    $('id_proveedor').value=datos[3];
    $('rut_proveedor').value=datos[1];
    $('nombre_proveedor').value=datos[2].unescapeHTML();
    cargar_ordenes();
  }

  mostrar_articulo=function(datos) {
    $('id_articulo').value=datos[5];
    $('codigo_articulo').value=datos[1];
    $('nombre_articulo').value=datos[2].unescapeHTML();
    cargar_ordenes();
  }
  
  liberar_proveedor=function() {
    $('id_proveedor').value=-1;
    $('nombre_proveedor').value='';
    $('rut_proveedor').value='';
    $('nombre_proveedor').focus();
    cargar_ordenes();
  }

  liberar_articulo=function() {
    $('id_articulo').value=-1;
    $('codigo_articulo').value='';
    $('nombre_articulo').value='';
    $('nombre_articulo').focus();
    cargar_ordenes();
  }
  
  cargar_ordenes=function() {
  
    params=$('id_articulo').serialize();
    params+='&'+$('id_proveedor').serialize();
  
    $('lista_ordenes').innerHTML=
    '<img src="../imagenes/ajax-loader2.gif">';
  
    var myAjax = new Ajax.Updater(
    'lista_ordenes',
    'listar_ordenes.php',
    {
      method: 'get',
      parameters: params
    }
    );
  
  }
  
  abrir_orden_compra = function(orden_id) {

  l=(screen.availWidth/2)-250;
  t=(screen.availHeight/2)-200;
  
  win = window.open('../visualizar.php?orden_id='+orden_id, 'ver_orden',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=500, height=415');
                    
  win.focus();

  }
  
  usar_orden = function(orden_numero, prov_rut) {
  
    window.opener.$('<?php echo $orden; ?>').value=orden_numero;
    window.opener.$('<?php echo $prov_rut; ?>').value=prov_rut;
    tempfunc= window.opener.<?php echo $func; ?>.bind(window.opener);
    tempfunc();
    //window.opener.cargar_oc();
    window.close();
  
  }


</script>

<body class="fuente_por_defecto popup_background">

<div class='sub-content'>
<div class='sub-content'>
<img src='../iconos/folder_explore.png'> Filtrar Listado
</div>
<table style="width:100%;">
<tr>
<td style="text-align:right;">Proveedor:</td>
<td>
<input type="hidden" id="id_proveedor" name="id_proveedor" value="-1">
<input type="text" id="rut_proveedor" name="rut_proveedor" size=15 
style='text-align:right;' DISABLED>
<input type="text" id="nombre_proveedor" name="nombre_proveedor" size=50
onDblClick='liberar_proveedor();'>

</td>
</tr>
<tr>
<td style="text-align:right;">Art&iacute;culo:</td>
<td>
<input type="hidden" id="id_articulo" name="id_articulo" value="-1">
<input type="text" id="codigo_articulo" name="codigo_articulo" size=15 
style='text-align:right;' DISABLED>
<input type="text" id="nombre_articulo" name="nombre_articulo" size=50
onDblClick='liberar_articulo();'>

</td>
</tr>

</table>
</div>

<div id='lista_ordenes' 
class='sub-content2' style='height:300px;overflow:auto;'>



</div>

</body>

<script>

  autocompletar_proveedores = new AutoComplete(
    'nombre_proveedor', 
    '../autocompletar_sql.php',
    function() {
      if($('nombre_proveedor').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('nombre_proveedor').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_proveedor);

  autocompletar_articulos = new AutoComplete(
    'nombre_articulo', 
    '../autocompletar_sql.php',
    function() {
      if($('nombre_articulo').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=buscar_arts&codigo='+encodeURIComponent($('nombre_articulo').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_articulo);


</script>


</html>
