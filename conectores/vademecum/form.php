<?php 

	require_once('../../conectar_db.php');

	$art_id=false;

	if(isset($_GET['art_id'])) {
		$art_id=$_GET['art_id']*1;
		$art=cargar_registro("SELECT * FROM articulo LEFT JOIN bodega_forma ON art_forma=forma_id WHERE art_id=$art_id");
	}

?>

<html>
<title>B&uacute;squeda VADEMECUM&copy;</title>

<?php cabecera_popup('../..'); ?>

<script>

buscar_meds = function() {

	$('listado').innerHTML='<center><br/><br/><img src="../../imagenes/ajax-loader3.gif" /><br/>Espere un momento...</center>';

	var myAjax=new Ajax.Updater(
		'listado',
		'buscar_nombre.php',
		{
		method:'get', parameters:$('nombre').serialize()
		}
	);

}

<?php if(!$art_id) { ?>
select_code = function(code) {

	window.opener.$('id_vademecum').value=code;
	window.close();

}
<?php } else { ?>
select_code = function(code) {

	var myAjax=new Ajax.Request(
		'sql_articulo.php',
		{method:'post',parameters:'art_id=<?php echo $art_id; ?>&id_vademecum='+encodeURIComponent(code),onComplete:function(r) {
			if(window.opener.$('id_vademecum')==null){
				var fn=window.opener.actualizar_lista.bind(window.opener);
				fn();
			} else {
				window.opener.$('id_vademecum').value=code;
			}
        		window.close();
		}});

}

<?php } ?>

 ajustar_div = function()
            {
                $('listado').style.height=window.innerHeight-180;
            }


</script>

<body class='fuente_por_defecto popup_background' onLoad='ajustar_div();' onResize='ajustar_div();'>
<div class='sub-content'>
<img src='../../iconos/pill.png' />
<b>B&uacute;squeda de Medicamentos en VADEMECUM&copy;</b>
</div>
<?php if($art_id) {?>
<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='font-size:16px;text-align:center;'><?php echo $art['art_codigo']; ?></td>
<td style='font-weight:bold;'><?php echo $art['art_glosa']; ?></td>
<td><?php echo $art['forma_nombre']; ?></td>
</tr>
</table>
</div>
<?php } ?>

<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='width:20%;text-align:right;'>Nombre del Medicamento:</td><td style='width:60%'><input type='text' id='nombre' name='nombre' style='width:100%;font-size:18px;' onKeyUp='if(event.which==13) buscar_meds();'></td><td><input type='button' id='buscar' name='buscar' value='[[ Realizar B&uacute;squeda... ]]' onClick='buscar_meds();' /></td></tr>
</table>
</div>

<div class='sub-content2' style='height:200px;overflow:auto;' id='listado'>

</div>

</body>
</html>
