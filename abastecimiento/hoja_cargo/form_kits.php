<?php 

	require_once('../../conectar_db.php');
	
	$bod_id=$_GET['bod_id']*1;

?>

<html>

<title>Mantenedor de KITs de Art&iacute;culos</title>

<?php cabecera_popup('../..'); ?>

<script>

function init() {
	
	listar_kits();
	
}

function listar_kits() {
	
	var myAjax=new Ajax.Updater(
		'listado_kits',
		'listado_kits.php',
		{
			method:'post',
			parameters:$('bod_id').serialize()
		}
	);
	
}

function guardar(kit_id) {
	
	var myAjax=new Ajax.Updater(
		'listado_kits',
		'listado_kits.php',
		{
			method:'post',
			parameters:$('bod_id').serialize()+'&'+$('kit_codigo_'+kit_id).serialize()+'&'+$('kit_nombre_'+kit_id).serialize()+'&'+$('kit_detalle_'+kit_id).serialize()+'&kit_id='+kit_id,
			onComplete:function() {
				alert('Kit guardado exitosamente.');
			}
		}
	);
	
}

function eliminar(kit_id) {
	
	if(!confirm('&iquest;Est&aacute; seguro que desea remover este kit?'.unescapeHTML()))
		return;
	
	var myAjax=new Ajax.Updater(
		'listado_kits',
		'listado_kits.php',
		{
			method:'post',
			parameters:$('bod_id').serialize()+'&'+$('kit_codigo_'+kit_id).serialize()+'&'+$('kit_nombre_'+kit_id).serialize()+'&'+$('kit_detalle_'+kit_id).serialize()+'&eliminar=1&kit_id='+kit_id
		}
	);
	
	
}

</script>

<body class='fuente_por_defecto popup_background' onLoad='init();'>

<input type='hidden' id='bod_id' name='bod_id' value='<?php echo $bod_id; ?>' />

<div class='sub-content'>
<img src='../../iconos/package.png' /> <b>Mantenedor de Kits</b>
</div>

<div class='sub-content2' id='listado_kits'>


</div>
</body>

</html>
