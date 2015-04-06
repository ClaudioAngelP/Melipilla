<?php 
	
	require_once('../../conectar_db.php');
	
	$formula=utf8_decode($_GET['formula']);

	$p=explode('|',$formula);
	
	$f=sizeof($p);

?>

<html>
<title>Calculadora</title>

<?php cabecera_popup('../..'); ?>

<script>

	x=0;

	function formula() {
		try {

<?php 

	for($i=1;$i<$f-1;$i++) {
		$fld=explode('=',$p[$i]);
		echo 'var '.$fld[0].' = $("fld_'.$i.'").value; ';
	}
	
	echo $p[$f-1].';';	
	
?>

		x=Math.ceil(x);

		if(x>0) {
			$('total').innerHTML='$ '+number_format(x,0,',','.')+'.-';
			$('acepta').disabled=false;
		} else {
			$('total').innerHTML='$ 0.-';
			$('acepta').disabled=true;
		}		
				
		} catch(err) {
			
			$('total').innerHTML='$ 0.-';
			$('acepta').disabled=true;		
		
		}	
	}
	
	function aceptar() {

		window.opener.productos[indice].prod_v=x;
		window.opener.$('valp_'+indice).value=x;
		//window.opener.calcular_totales();
		window.opener.redibujar_tabla();
		window.close();
	
	}
		

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/calculator.png'>
<b><?php echo htmlentities($p[0]); ?></b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<?php 

	for($i=1;$i<$f-1;$i++) {
		$fld=explode('=',$p[$i]);
		print("
			<tr>
			<td style='text-align:right;'>".htmlentities($fld[1]).":</td>
			<td><input type='text' onKeyUp='formula();' 
			id='fld_$i' name='fld_$i' /></td>
			</tr>		
		");	
	}

	
?>
	<tr><td style='text-align:right;'>Total:</td>
	<td style='font-weight:bold;' id='total'>$ 0.-</td></tr>
</table>
</div>

<center>
<input type='button' id='acepta' name='acepta' DISABLED
onClick='aceptar();' value='-- Aceptar... --' />
</center>

</body>
</html>

<script> $('fld_1').focus(); </script>