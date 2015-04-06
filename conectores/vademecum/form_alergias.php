<?php 

	require_once('../../conectar_db.php');

	$pac_id=$_GET['pac_id']*1;

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<title>B&uacute;squeda de Alergias - VADEMECUM&copy;</title>

<?php cabecera_popup('../..'); ?>

<script>

buscar_meds = function() {

	$('listado').innerHTML='<center><br/><br/><img src="../../imagenes/ajax-loader3.gif" /><br/>Espere un momento...</center>';

	var myAjax=new Ajax.Updater(
		'listado',
		'buscar_alergias.php',
		{
		method:'get', parameters:$('nombre').serialize()
		}
	);

}

listado_alergias = function() {

        $('listado_paciente').innerHTML='<center><br/><br/><img src="../../imagenes/ajax-loader3.gif" /><br/>Espere un momento...</center>';

        var myAjax=new Ajax.Updater(
                'listado_paciente',
                'listado_alergias.php',
                {
                method:'post', parameters:'pac_id=<?php echo $pac_id; ?>'
                }
        );

}

select_code = function(code) {

	var myAjax=new Ajax.Request(
		'sql_alergia.php',
		{
			method:'post',
			parameters:'id_alergia='+encodeURIComponent(code)+'&pac_id=<?php echo $pac_id; ?>',
			onComplete:function(r) {
				listado_alergias();
			}
		});

}

eliminar_alergia=function(al_id) {

	if(!confirm(htmlDecode("&iquest;Est&aacute; seguro que desea eliminar esta alergia?."))) return;

	 var myAjax=new Ajax.Request(
                'sql_alergia.php',
                {
                        method:'post',
                        parameters:'al_id='+encodeURIComponent(al_id)+'&pac_id=<?php echo $pac_id; ?>',
                        onComplete:function(r) {
                                listado_alergias();
                        }
                });

}

 ajustar_div = function()
            {
                $('listado').style.height=((window.innerHeight-160)*0.5)+'px';
		$('listado_paciente').style.height=((window.innerHeight-160)*0.5)+'px';
            }


</script>

<body class='fuente_por_defecto popup_background' onLoad='ajustar_div();listado_alergias();' onResize='ajustar_div();'>

<div class='sub-content'>
<img src='../../iconos/user_delete.png' />
<b>Registro de Alergias del Paciente</b>
</div>
<div class='sub-content2' style='height:450px;overflow:auto;' id='listado_paciente'>

</div>

<div class='sub-content'>
<img src='../../iconos/pill_delete.png' />
<b>B&uacute;squeda de Alergias en VADEMECUM&copy;</b>
</div>
<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='width:20%;text-align:right;'>Tipo Alergia:</td><td style='width:60%'><input type='text' id='nombre' name='nombre' style='width:100%;font-size:18px;' onKeyUp='if(event.which==13) buscar_meds();'></td><td><input type='button' id='buscar' name='buscar' value='[[ Realizar B&uacute;squeda... ]]' onClick='buscar_meds();' /></td></tr>
</table>
</div>

<div class='sub-content2' style='height:450px;overflow:auto;' id='listado'>

</div>

</body>
</html>
