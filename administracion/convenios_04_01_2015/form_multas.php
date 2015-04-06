<?php

require_once('../../conectar_db.php');
	
$tipo=$_GET['tipo']*1;
$multa_id=$_GET['multa_id']*1;

if(isset($_POST['multa_licitacion_id'])) {
	
	$licitacion=$_POST['multa_licitacion_id']*1;
	$numero=$_POST['multa_numero']*1;
	$estado=$_POST['multa_estado']*1;
	$descripcion=htmlentities($_POST['multa_descripcion']);
	$concepto=$_POST['multa_concepto']*1;
	$fecha_not=$_POST['multa_fecha_noti'];
	$fecha_des=$_POST['multa_fecha_desc'];
	$fecha_res=$_POST['multa_fecha_reso'];
	$num_res=$_POST['multa_numero_res'];
	$monto=$_POST['multa_monto']*1;
	$cancelada=$_POST['cancela']; 
	$func_id=$_SESSION['sgh_usuario_id']*1;
	$expediente=$_POST['multa_numero_exp']*1;
	$adjunto='';
	$tipo=$_POST['tipo'];
	$multa_id=$_POST['multa_id']*1;
	
	if($fecha_not == '') $fecha_not='null'; else $fecha_not= "'$fecha_not'";
	if($fecha_des == '') $fecha_des='null'; else $fecha_des= "'$fecha_des'";
	if($fecha_res == '') $fecha_res='null'; else $fecha_res= "'$fecha_res'";
	
			
	if($_FILES["multa_archivo"]["name"]!=''){
		if ($_FILES["multa_archivo"]["error"] > 0){

			//print("<script>window.alert('Error al enviar el archivo.');window.close();</script>");
		}else{
			$fname=$_FILES["multa_archivo"]["name"];
			$ftype=$_FILES["multa_archivo"]["type"];
			$fsize=$_FILES["multa_archivo"]["size"]; 
			$md5=md5_file($_FILES["multa_archivo"]["tmp_name"]);

			move_uploaded_file($_FILES["multa_archivo"]["tmp_name"], 'adjunto_multas/'.$md5);
			
			
			
			
			if($tipo==0){

				pg_query("INSERT INTO convenio_multa 
							VALUES(DEFAULT,$licitacion,$numero,$estado,
									'$descripcion',$concepto,
									$fecha_not,$fecha_des,$fecha_res,
									'$num_res',$monto,
									$func_id,'$fname|$ftype|$fsize|$md5',$expediente,'$cancelada');");
			}else{
				pg_query("UPDATE convenio_multa SET 
						convm_estado=$estado ,
						convm_descripcion='$descripcion',
						convm_concepto=$concepto,
						convm_fecha_notificacion='$fecha_not',
						convm_fecha_descargo=$fecha_des,
						convm_resolucion=$fecha_res,
						convm_numero_res=$num_res,
						convm_monto=$monto,
						convm_nro_expediente=$expediente,
						convm_adjunto='$fname|$ftype|$fsize|$md5'
						WHERE covnm_id=$multa_id");
			}
			print("<script>window.alert('Multa Guardada y archivo enviado exitosamente.');
					var fn=window.opener.listado_multas.bind(window.opener);
						fn();
						
			 </script>");
		//window.close();	  
		}
	exit();
	}else{ 
		if($tipo==0){
			pg_query("INSERT INTO convenio_multa
			 			VALUES(DEFAULT,$licitacion,$numero,$estado,
							   '$descripcion',$concepto,$fecha_not,
							   $fecha_des,$fecha_res,'$num_res',$monto,
							   $func_id,'$adjunto',$expediente,'$cancelada');");
			//print("INSERT INTO convenio_multa VALUES(DEFAULT,$licitacion,$numero,$estado,'$descripcion',$concepto,'$fecha_not','$fecha_des','$fecha_res','$num_res',$monto,$func_id,'$adjunto');");
		}else{
			pg_query("UPDATE convenio_multa SET 
						convm_estado=$estado ,
						convm_descripcion='$descripcion',
						convm_concepto=$concepto,
						convm_fecha_notificacion=$fecha_not,
						convm_fecha_descargo=$fecha_des,
						convm_resolucion=$fecha_res,
						convm_numero_res='$num_res',
						convm_monto=$monto,
						convm_nro_expediente=$expediente,
						convm_adjunto='$adjunto'
						WHERE covnm_id=$multa_id");
		}
		print("<script>window.alert('Multa Guardada!.');
				var fn=window.opener.listado_multas.bind(window.opener);
						fn();
						window.close();
		 </script>");
	}
}


$licitacion = $_GET['licitacion']*1;
$licitacion_nro = $_GET['licitacion_nro'];

$num = cargar_registro("SELECT (max(convm_numero)+1) as num_multa FROM convenio_multa");
$numero_multa=$num['num_multa']*1;

if($tipo==1){
	$multa_datos=cargar_registro("SELECT * FROM convenio_multa JOIN convenio ON convm_convenio_id=convenio_id WHERE covnm_id=$multa_id;");
}

?>

<title>Agregar Multas </title>

<?php cabecera_popup('../../'); ?>

<script>
mostrar_adjunto = function(){

	$('div_adjunto').style.display='';

}

guardar_multa = function(){
	
	if($('multa_numero').value==''){
		alert('Debe ingresar el n&uacute;mero de multa'.unescapeHTML());
		return;
	}
	
	if($('multa_estado').value=='0'){
		alert('Debe seleccionar el estado de la multa'.unescapeHTML());
		return;
	}
	
	if($('multa_concepto').value=='0'){
		alert('Debe seleccionar el concepto de la multa'.unescapeHTML());
		return;
	}
	
	if($('multa_estado').value!='1'){
		
		if($('multa_fecha_desc').value==''){
			alert('Debe ingresar la fecha de descargos'.unescapeHTML());
			return;
		}
		
		if($('multa_fecha_reso').value==''){
			alert('Debe ingresar la fecha de resoluci&oacute;n'.unescapeHTML());
			return;
		}
		
		if($('multa_numero_res').value==''){
			alert('Debe ingresar el n&uacute;mero de resoluci&oacute;n de la multa'.unescapeHTML());
			return;
		}
	}else{
		if($('multa_fecha_noti').value==''){
			alert('Debe ingresar la fecha de notificaci&oacute;n'.unescapeHTML());
			return;
		}
		
		if($('multa_numero_exp').value==''){
			alert('Debe ingresar el n&uacute;mero de expediente'.unescapeHTML());
			return;
		}
	}
	
	if($('multa_monto').value==''){
		alert('Debe ingresar el monto de la multa'.unescapeHTML());
		return;
	}
	
	$('form_multas').submit();
}

enviar_archivo = function() {

	  top=Math.round(screen.height/2)-750;
      left=Math.round(screen.width/2)-75;

	  var sendfile =window.open('multas_adjuntos.php?'+$('multa_id').serialize(),
	        'win_chat_file', 'toolbar=no, location=no, directories=no, status=no, '+
			'menubar=no, scrollbars=yes, resizable=no, width=800, height=200, '+
			'top='+top+', left='+left);

	  sendfile.focus();
	  listar_adjuntos($('multa_id').value);
}

listar_adjuntos=function(id) {

	 var myAjax = new Ajax.Updater(
	  'div_adjunto',
	  'listado_adjunto_multa.php',
	  {
		method: 'get',
		parameters: 'multa_id='+(id*1),
		evalScripts: true
	  }
	  );
}

eliminar_adjunto=function(mad_id,multa_id)
{
      var myAjax = new Ajax.Updater('div_adjunto','listado_adjunto_multa.php',
      {
          method: 'get',
          parameters: 'adjunto_id='+(mad_id*1)+'&multa_id='+(multa_id*1),
          evalScripts: true
      });
}

comprueba_campos = function(){

	var campo = $('multa_id').value*1;

	if(campo!=0){
		$('btn_adj').style.display='';
		listar_adjuntos(campo);
	}else{
		$('btn_adj').style.display='none';
	}
}

</script>
<center>
<form name='form_multas' id='form_multas' method='post' action='form_multas.php' enctype="multipart/form-data" onsubmit='return false;'>
	<div class='sub-content'>
		<img src="../../iconos/exclamation.png"> Agregar Multas
	</div>
	<div class='sub-content'>
		<center>
		<table>
			<tr>
				<td style='text-align: right;'>Licitaci&oacute;n:</td>
				<td><input type='hidden' id='multa_licitacion_id' name='multa_licitacion_id' value='<?php if($tipo==1){ echo $multa_datos['convenio_id']; }else{ echo $licitacion; }?>'>
					<input type='text' name='multa_licitacion_nro' style='text-align:center; font-weight:bold;font-size:14px' value='<?php if($tipo==1){ echo $multa_datos['convenio_licitacion']; }else{ echo $licitacion_nro; }?>' size='15' DISABLED></td>
					<input type='hidden' id='tipo' name='tipo' value='<?php echo $tipo; ?>'>
					<input type='hidden' id='multa_id' name='multa_id' value='<?php echo $multa_id; ?>'>
			</tr>
		</table>
		</center>
	</div>
	
	<div class='sub-content'>
		<table>
			<tr>
				<td style='text-align: right;'>N&uacute;mero:</td>
				<td><input type='text' name='multa_numero' id='multa_numero' style='text-align:center;' size='10' value='<?php if($tipo==1){ echo $multa_datos['convm_numero']; }else{ echo $numero_multa; }?>' readOnly></td>
			</tr>
			<tr>
				<td style='text-align: right;'>Estado:</td>
				<td>
					<select name='multa_estado' id='multa_estado'>
						<option value='0' <?php if($tipo==1 AND ($multa_datos['convm_estado']*1)==0){ echo "SELECTED"; }?>>(Seleccione...)</option>
						<option value='1' <?php if($tipo==1 AND ($multa_datos['convm_estado']*1)==1){ echo "SELECTED"; }?>>En Tr&aacute;mite</option>
						<option value='2' <?php if($tipo==1 AND ($multa_datos['convm_estado']*1)==2){ echo "SELECTED"; }?>>Aplicada</option>
						<option value='3' <?php if($tipo==1 AND ($multa_datos['convm_estado']*1)==3){ echo "SELECTED"; }?>>Rebajada</option>
						<option value='4' <?php if($tipo==1 AND ($multa_datos['convm_estado']*1)==4){ echo "SELECTED"; }?>>Revocada</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style='text-align: right;'>Descripci&oacute;n:</td>
				<td><textarea id="multa_descripcion" rows="3" cols="40" value='' name="multa_descripcion"><?php if($tipo==1){ echo $multa_datos['convm_descripcion']; }?></textarea></td>
			</tr>
			<tr>
				<td style='text-align: right;'>Concepto:</td>
				<td>
					<select name='multa_concepto' id='multa_concepto'>
						<option value='0' <?php if($tipo==1 AND ($multa_datos['convm_concepto']*1)==0){ echo "SELECTED"; }?>>(Seleccione...)</option>
						<option value='1' <?php if($tipo==1 AND ($multa_datos['convm_concepto']*1)==1){ echo "SELECTED"; }?>>Leve</option>
						<option value='2' <?php if($tipo==1 AND ($multa_datos['convm_concepto']*1)==2){ echo "SELECTED"; }?>>Grave</option>
					</select>
				</td>
			</tr>

			<tr>
				<td style='text-align: right;'>Fecha Notificaci&oacute;n:</td>
				<td><input type='text' name='multa_fecha_noti' id='multa_fecha_noti' style='text-align:center;' size='10' value='<?php if($tipo==1){ echo $multa_datos['convm_fecha_notificacion']; }?>' onblur="validacion_fecha(this);">
					<img id="btn_fecha_noti" src="../../iconos/date_magnify.png"></td>
			</tr>
			<tr>
				<td style='text-align: right;'>Fecha descargos:</td>
				<td><input type='text' name='multa_fecha_desc' id='multa_fecha_desc' style='text-align:center;' size='10' value='<?php if($tipo==1){ echo $multa_datos['convm_fecha_descargo']; }?>' onblur="validacion_fecha(this);">
					<img id="btn_fecha_desc" src="../../iconos/date_magnify.png"></td>
			</tr>
			<tr>
				<td style='text-align: right;'>Fecha Resoluci&oacute;n:</td>
				<td><input type='text' name='multa_fecha_reso' id='multa_fecha_reso' style='text-align:center;' size='10' value='<?php if($tipo==1){ echo $multa_datos['convm_resolucion']; }?>' onblur="validacion_fecha(this);">
					<img id="btn_fecha_reso" src="../../iconos/date_magnify.png"></td>
			</tr>
			<tr>
				<td style='text-align: right;'>N&uacute;mero Res.:</td>
				<td><input type='text' name='multa_numero_res' id='multa_numero_res' style='text-align:center;' size='10' value='<?php if($tipo==1){ echo $multa_datos['convm_numero_res']; }?>'></td>
			</tr>
			<tr>
				<td style='text-align: right;'>N&uacute;mero Exp.:</td>
				<td><input type='text' name='multa_numero_exp' id='multa_numero_exp' style='text-align:center;' size='10' value='<?php if($tipo==1){ echo $multa_datos['convm_nro_expediente']; }?>'></td>
			</tr>
			<tr>
				<td style='text-align: right;'>Monto (UTM):</td>
				<td><input type='text' name='multa_monto' id='multa_monto' style='text-align:center;' size='10' value='<?php if($tipo==1){ echo $multa_datos['convm_monto']; }?>'></td>
				 
			</tr>
			<tr>
				<td style='text-align: right;'>Cancelada</td>
				<td></d><input type="checkbox" name="cancela" id='cancela' value="Cancelada">
			</tr>
			
		</table>
	</div>
	
	<div class='sub-content' id='div_adjunto'>
		<!--<center>
		<input type='hidden' id='h_adjunto' name='h_adjunto' value=''>
		<input type='file' id='multa_archivo' name='multa_archivo' />
		</center>-->
	</div>	
	
	<input type='button' value='(Guardar...)' onClick='guardar_multa();'>
	<input type='button' style='display:none;' id='btn_adj' value='(Adjuntar...)' onclick='enviar_archivo();'>
</form>
</center>

<script>
  
    Calendar.setup({
        inputField     :    'multa_fecha_noti',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'btn_fecha_noti'
    });
    Calendar.setup({
        inputField     :    'multa_fecha_desc',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'btn_fecha_desc'
    });
    Calendar.setup({
        inputField     :    'multa_fecha_reso',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'btn_fecha_reso'
    });
    
  comprueba_campos();
</script>
