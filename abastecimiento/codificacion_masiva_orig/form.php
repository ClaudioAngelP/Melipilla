<?php 

	require_once('../../conectar_db.php');
	
?>

<script>


	abrir_articulo = function(d, obj) {
		
		try {
		
//		console.log(d);
			var obj_name=$(obj).name.split('_');
			var contador=obj_name[2]*1;
		
			if($('art_id_'+contador).value!=d[5])
			$('guardar_'+contador).show();
		
			$('art_id_'+contador).value=d[5];
		
			$('codigo_art_'+contador).value=d[0];
			$('glosa_'+contador).innerHTML=d[2];
		
	} catch(err) {
		
		alert(err);
		alert(d[0]);
		
	}
		
	}


actualizar_lista=function() {
	
	
	$('lista_codigos').innerHTML='<br /><br /><img src="imagenes/ajax-loader3.gif"><br />Cargando...';
	
	var myAjax=new Ajax.Updater(
		'lista_codigos',
		'abastecimiento/codificacion_masiva/listar_codigos.php',
		{
			method: 'post',
			parameters: $('filtro').serialize(),
			evalScripts: true
		}
	);
	
}

modificar_glosa=function(i) {
	
	params='glosa='+encodeURIComponent(glosas[i].orserv_glosa)+'&art_id='+$('art_id_'+i).value*1;
	
	if(!confirm("Se van a modificar --- "+glosas[i].cuenta+" Ordenes de Compra ---, no hay opciones para deshacer. &iquest;Est&aacute; seguro?".unescapeHTML())) return;
	
	var myAjax=new Ajax.Request(
		'abastecimiento/codificacion_masiva/sql.php',
		{
			method:'post',
			parameters: params,
			onComplete:function(d) {
				alert(d.responseText);
			}
		}
	); 
	
}

</script>

<center>

<div class='sub-content' style='width:850px;'>
<div class='sub-content'>
<img src='iconos/package.png' />
<b>Codificaci&oacute;n Masiva de Ordenes de Compra</b>
<select id='filtro' name='filtro' onChange='actualizar_lista();'>
<option value='1'>Ver Pendientes</option>
<option value='2'>Ver Asignadas</option>
</select>
</div>

<div class='sub-content2' style='height:400px;overflow:auto;' id='lista_codigos'>


</div>

</div>

</center>


<script>

actualizar_lista();

</script>
