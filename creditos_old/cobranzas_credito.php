<?php 
	
	require_once('../conectar_db.php');
	
	if(isset($_POST['crecod'])) {
	
		$crecod=$_POST['crecod']*1;	
		$cc_tipo=$_POST['cc_tipo']*1;	
		$cc_desc=pg_escape_string(utf8_decode($_POST['cc_desc']));	
		$func_id=$_SESSION['sgh_usuario_id']*1;
		
		pg_query("
			INSERT INTO creditos_cobranza VALUES (
				DEFAULT,
				$crecod,
				$cc_tipo,
				current_timestamp,
				$func_id,
				'$cc_desc',
				0			
			);		
		");
		
		exit();	
		
	} 

	$crecod=$_GET['crecod'];
	
	$c=cargar_registros_obj("
		SELECT * FROM creditos 
		JOIN clientes USING (clirut)		
		WHERE crecod=$crecod	
	", true);
	
	$cc=cargar_registros_obj("
		SELECT *, date_trunc('second', cc_fecha) AS cc_fecha 
		FROM creditos_cobranza
		JOIN funcionario USING (func_id)
		WHERE crecod=$crecod
		ORDER BY creditos_cobranza.cc_fecha DESC 	
	", true);

	$r=cargar_registro("SELECT * FROM clientes WHERE clirut='".$c[0]['clirut']."'");	

?>

<html>
<title>Gesti&oacute;n de Cobranza Cr&eacute;dito #<?php echo $crecod; ?></title>

<?php cabecera_popup('..'); ?>

<script>

function guardar() {

	if(trim($('cc_desc').value)=='') {
		alert('Debe ingresar el detalle del registro.');
		return;	
	}

	var myAjax=new Ajax.Request(
		'cobranzas_credito.php',
		{
			method:'post',
			parameters:$('cobra').serialize(),
			onComplete: function() {
				location.reload();	
			}	
		}	
	);
	
}

function ver_credito() {

	window.open('visualizar_credito.php?crecod=<?php echo $crecod; ?>','_self');
	
}


</script>

<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<table cellpadding=0 cellspacing=0 style='width:100%;'><tr><td>
<img src='../iconos/money.png' /></td><td>
<b>Gesti&oacute;n de Cobranza Cr&eacute;dito #<?php echo $crecod; ?></b>
</td><td style='width:40%;text-align:right;'>
<input type='button' value=' - Ver Cr&eacute;dito... - ' 
onClick="ver_credito();" />
</td></tr></table>
</div>

<div class='sub-content'>
<table style='width:100%;font-size:9px;'>

<tr>
<td style='text-align:right;width:30%;'>R.U.T.:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['clirut'].'-'.$r['clidv']); ?></td>
</tr>

<tr>
<td style='text-align:right;width:30%;'>Paterno:</td>
<td style='font-weight:bold;'><?php echo trim(htmlentities($r['clipat']).' '.htmlentities($r['climat']).' '.htmlentities($r['clinom'])); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Direcci&oacute;n:</td>
<td><?php echo htmlentities($r['clidir']); ?></td>
</tr>
<tr>
<td style='text-align:right;'>Tel&eacute;fono Fijo:</td>
<td><?php echo htmlentities($r['clifon']); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono M&oacute;vil:</td>
<td><?php echo htmlentities($r['clicel']); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Observaciones:</td>
<td><?php echo htmlentities($r['cliobs']); ?></td>
</tr>

</table>
</div>

<div class='sub-content2' style='height:130px;overflow:auto;'>

<table style='width:100%;font-size:11px;'>

<?php 

	if($cc)
	for($i=0;$i<sizeof($cc);$i++) {
	
		switch($cc[$i]['cc_tipo']) {
			case 0: $tipo='Carta de Cobranza'; break;	
			case 1: $tipo='Telef&oacute;nico'; break;	
			case 2: $tipo='Personal'; break;	
			case 3: $tipo='Correspondencia'; break;	
		}	
	
		print("
			<tr class='tabla_fila'>
			<td style='text-align:center;'><b>".$tipo."</b></td>
			<td style='text-align:center;'><b><i>".$cc[$i]['cc_fecha']."</i></b></td>
			<td>".$cc[$i]['func_nombre']."</td>			
			</tr>
			<tr class='tabla_fila2'>
			<td colspan=3><pre>".$cc[$i]['cc_desc']."</pre></td>
			</tr>		
		");	
		
	}		

?>

</table>

</div>

<div class='sub-content'>

<form id='cobra' name='cobra' method='post' onSubmit='return false;'
action='cobranzas_credito.php?crecod=<?php echo $crecod; ?>'>

<input type='hidden' id='crecod' name='crecod' value='<?php echo $crecod; ?>' />

<table style='width:100%;'>

<tr><td style='text-align:right;'>
Tipo de Gesti&oacute;n:
</td><td>
<select id='cc_tipo' name='cc_tipo'>
<option value=1>Contacto Telef&oacute;nico</option>
<option value=2>Contacto Personal</option>
<option value=3>Env&iacute;o de Correspondencia</option>
</select>
</td></tr>
<tr><td style='text-align:right;' valign='top'>
Detalles:
</td><td>
<textarea id='cc_desc' name='cc_desc' cols=50 rows=4></textarea>
</td></tr>

<tr><td colspan=2>
<center>
<input type='button' value=' - Guardar Registro... - ' 
onClick='guardar();' />
</center>

</td></tr>

</table>

</form>

</div>

</body>

</html>
