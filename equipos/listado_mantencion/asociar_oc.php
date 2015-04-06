<?php 

    require_once('../../conectar_db.php');
    
    $eot_id=$_GET['eot_id']*1;
    
?>

<html>
<title>Asociar Orden de Compra a &Oacute;rden de Trabajo</title>

<?php cabecera_popup('../..'); ?>

<script>

asociar_oc=function() {

    orden.submit();

}

</script>

<body class="popup_background fuente_por_defecto">

<form id='orden' name='orden' onSubmit='return false;' action='sql_orden.php' method='post'> 
<input type='hidden' id='accion' name='accion' value='agregar'>
<input type='hidden' id='eot_id' name='eot_id' value='<?php echo $eot_id; ?>'>

<div class='sub-content'>
<img src='../../iconos/folder_page.png'>
Informaci&oacute;n de la Orden de Compra
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;'>Nro. Orden de Compra:</td>
<td>
<input type='text' id='nro_orden' name='nro_orden' value=''>
</td>
<td>
<input type='button' id='usar_oc' name='usar_oc' 
style='display:none;' value='Asociar...' onClick='asociar_oc();'>
</td>
</tr>

</table>

</div>

<div class='sub-content2' id='info_oc' style='height:200px;overflow:auto;'>

(Seleccione Orden de Compra para ver detalles...)

</div>

</form>

</body>
</html>

<script>

    mostrar_oc=function(v) {
  
        $('info_oc').innerHTML='<center><img src="../../imagenes/ajax-loader3.gif"><br><br>Cargando...</center>';

        var myAjax=new Ajax.Updater('info_oc','info_oc.php',
                                    {method:'post',parameters:'orden_id='+v[0],
                                    onComplete: function() { $('usar_oc').style.display=''; } } );
    
    
    }

  autocompletar_orden = new AutoComplete(
    'nro_orden', 
    '../../autocompletar_sql.php',
    function() {
      if($('nro_orden').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=orden_compra&cadena='+encodeURIComponent($('nro_orden').value)
      }
    }, 'autocomplete', 300, 200, 250, 2, 3, mostrar_oc);


</script>
