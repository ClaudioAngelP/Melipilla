<?php 

	require_once('../../conectar_db.php');
	
	$ps_refcliente=utf8_decode($_GET['ps_refcliente']);
	
	$d=explode('|',htmlentities($ps_refcliente));
	
?>

<html>
<title>Definir Propietario</title>

<?php cabecera_popup('../..'); ?>

<script>


var bloquear=0;

validar_rut = function() {

	if(bloquear) return;

	bloquear=1;

	var myAjax=new Ajax.Request('../../ingresos/info_cliente.php',
	{
		method:'get',
		parameters: $('clirut').serialize(),
		onComplete: function(resp) {
			d=resp.responseText.evalJSON(true);
			
			if(d) {
			
				$('clipat').value=d['clipat'].unescapeHTML();
				$('climat').value=d['climat'].unescapeHTML();
				$('clinom').value=d['clinom'].unescapeHTML();
				$('clidir').value=d['clidir'].unescapeHTML();
				$('comcod').value=d['comcod'].unescapeHTML();
				$('comdes').value=d['comdes'].unescapeHTML();
				$('clifon').value=d['clifon'].unescapeHTML();
				$('clicel').value=d['clicel'].unescapeHTML();
				$('climail').value=d['climail'].unescapeHTML();
				$('cliobs').value=d['cliobs'].unescapeHTML();
				$('clifnac').value=d['clifnac'].unescapeHTML();
				$('prodesc').focus();
				
			} else {
			
				$('clipat').value='';
				$('climat').value='';
				$('clinom').value='';
				$('clidir').value='';
				$('comcod').value='';
				$('comdes').value='';
				$('clifon').value='';
				$('clicel').value='';
				$('climail').value='';
				$('cliobs').value='';
				$('clifnac').value='';
				$('clipat').focus();
							
			}

			validacion_rut($('clirut'));
			validacion_fecha($('clifnac'));

			bloquear=0;

		}	
	});

}

function guardar() {
	
	if(!validacion_rut($('clirut')) && trim($('clirut').value)!='') {
		$('clirut').value='';	
	}

	var datos=$('clirut').value+'|';
	datos+=$('clipat').value+'|';
	datos+=$('climat').value+'|';
	datos+=$('clinom').value+'|';
	datos+=$('clifnac').value+'|';
	datos+=$('clidir').value+'|';
	datos+=$('comcod').value+'|';
	datos+=$('comdes').value+'|';
	datos+=$('clifon').value+'|';
	datos+=$('clicel').value+'|';
	datos+=$('climail').value+'|';
	datos+=$('cliobs').value;

	window.opener.$('ps_refcliente').value=datos;
	window.close();
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/user.png' />
<b>Datos del Propietario</b>
</div>

<div class='sub-content'>

<table style='width:100%;' cellpadding=1>
<tr>
<td style='text-align:right;width:30%;'>R.U.T.:</td>
<td style='font-weight:bold;'>
<input type='text' id='clirut' name='clirut' value='<?php echo $d[0]; ?>' 
onKeyUp='validacion_rut(this); if(event.which==13 && validacion_rut(this)) validar_rut();' 
onBlur='if(validacion_rut(this)) validar_rut();'
size=10>
</td>
</tr>

<tr>
<td style='text-align:right;width:30%;'>Paterno:</td>
<td style='font-weight:bold;'>
<input type='text' id='clipat' name='clipat' size=15 value='<?php echo $d[1]; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Materno:</td>
<td style='font-weight:bold;'>
<input type='text' id='climat' name='climat' size=15 value='<?php echo $d[2]; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Nombres:</td>
<td style='font-weight:bold;'>
<input type='text' id='clinom' name='clinom' size=15 value='<?php echo $d[3]; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Fecha de Nac.:</td>
<td style='font-weight:bold;'>
<input type='text' id='clifnac' name='clifnac'  value='<?php echo $d[4]; ?>'
style='text-align:center;' size=10 onBlur='validacion_fecha(this);'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Direcci&oacute;n:</td>
<td><input type='text' id='clidir' name='clidir' size=20 value='<?php echo $d[5]; ?>'></td>
</tr>

<tr>
<td style='text-align:right;'>Comuna:</td>
<td>
<input type='hidden' id='comcod' name='comcod' value='<?php echo $d[6]; ?>'>
<input type='text' id='comdes' name='comdes' size=20 value='<?php echo $d[7]; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono Fijo:</td>
<td><input type='text' id='clifon' name='clifon' size=15 value='<?php echo $d[8]; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono M&oacute;vil:</td>
<td><input type='text' id='clicel' name='clicel' size=15  value='<?php echo $d[9]; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>e-mail:</td>
<td><input type='text' id='climail' name='climail' size=15 value='<?php echo $d[10]; ?>'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Observaciones:</td>
<td><input type='text' id='cliobs' name='cliobs' size=15 value='<?php echo $d[11]; ?>'>
</td>
</tr>

</table>

<center><br /><br />
<input type='button' id='guarda' name='guarda' onClick='guardar();' 
value='-- Aceptar Datos de Propietario... --' />
<br /><br />
</center>

</div>

</body>
</html>


<script>

		seleccionar_comuna=function(d) {

			$('comdes').value=d[1].unescapeHTML();
			$('comcod').value=d[0]*1;				

		}


		autocompletar_comunas = new AutoComplete(
      'comdes', 
      '../../autocompletar_sql.php',
      function() {
        if($('comdes').value.length<2) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=comunas&'+$('comdes').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 1, seleccionar_comuna);


	validacion_rut($('clirut'));
	validacion_fecha($('clifnac'));


</script>
