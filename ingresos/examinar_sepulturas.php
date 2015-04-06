<?php 

	require_once('../conectar_db.php');
	
	$clase=pg_escape_string(utf8_decode($_GET['clase']));
	$codigo=pg_escape_string(utf8_decode($_GET['codigo']));

	$l=cargar_registros_obj("
		SELECT * FROM inventario 
		WHERE sep_clase='$clase' AND sep_codigo='$codigo'
		ORDER BY sep_clase, sep_codigo, sep_ninicio
	");

	//if(!$l) {
		$l=cargar_registros_obj("
		
				SELECT 
					ps_clase AS sep_clase, ps_codigo AS sep_codigo,
					MIN(ps_numero) AS sep_ninicio,
					MAX(ps_numero) AS sep_nfinal,
					COUNT(*) AS uso,
					0 AS sep_cant_e,
					0 AS sep_cant_r,
					0 AS sep_id			
				FROM propiedad_sepultura
				WHERE ps_vigente AND ps_clase='$clase' AND ps_codigo='$codigo'
				GROUP BY sep_clase, sep_codigo
						
		");		
	
	/*}	
	
					UNION 
				
				SELECT 
					sep_clase, sep_codigo,
					MIN(sep_numero) AS sep_ninicio,
					MAX(sep_numero) AS sep_nfinal,
					COUNT(*) AS uso,
					0 AS sep_cant_e,
					0 AS sep_cant_r,
					0 AS sep_id			
				FROM uso_sepultura
				WHERE us_vigente AND sep_clase='$clase' AND sep_codigo='$codigo'
				GROUP BY sep_clase, sep_codigo

	*/

?>

<html>
<title>Disponibilidad de Sepulturas</title>

<?php cabecera_popup('..'); ?>

<script>

function editar(clase, codigo, numero) {

	window.open("editar_sepulturas.php?clase="+encodeURIComponent(clase)+"&codigo="+encodeURIComponent(codigo)+"&numero="+numero*1, '_self');

}

</script>

<body class='popup_background fuente_por_defecto'>

<table style='width:100%;font-size:12px;'>

<?php 

	print("<tr><td colspan=6 
			style='font-weight:bold;font-size:20px;text-align:center;'>
			Visualizando: <u>".$l[0]['sep_clase']." ".$l[0]['sep_codigo']."</u>
			</td></tr>");	

?>

<tr class='tabla_header'>
<td rowspan=2 style='width:100px;'>Nro.</td>
<td colspan=5>Propietario</td>
<td rowspan=2 style='width:20px;'>Estado</td>
<td rowspan=2 style='width:20px;'>Editar</td>

</tr>

<tr class='tabla_header'>
<td>Bolet&iacute;n</td>
<td>R.U.T.</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
</tr>
	
<?php 


	for($i=0;$i<sizeof($l);$i++) {
	
		for($j=$l[$i]['sep_ninicio']*1;$j<=$l[$i]['sep_nfinal']*1;$j++) {

		$fclase=(($i+$j)%2==0)?'tabla_fila':'tabla_fila2';	
			
		print("
			<tr class='$fclase'
			onMouseOver='this.className=\"mouse_over\"'
			onMouseOut='this.className=\"$fclase\"'>
			<td style='text-align:center;font-weight:bold;'>$j</td>
		");	

		$r=cargar_registro("SELECT * FROM propiedad_sepultura
									LEFT JOIN clientes USING (clirut) 
									WHERE ps_clase='$clase' AND ps_codigo='$codigo' AND
									ps_letra='' AND ps_numero=$j AND ps_vigente", true);

		if($r) {

			print("<td style='text-align:center;font-weight:bold;'>".number_format($r['bolnum'],0,',','.')."</td>");

			if($r['clirut']!=0) {			
				print("<td style='text-align:right;'><i>".$r['clirut']."-".$r['clidv']."</i></td>
				<td>".$r['clipat']."</td>
				<td>".$r['climat']."</td>
				<td>".$r['clinom']."</td>");
			} else {
				$d=explode('|',$r['ps_refcliente']);
				print("<td style='text-align:right;'><i>".$d[0]."</i></td>
				<td colspan=3>".$d[1]."</td>");
			}
			
			print("<td><center><img src='../iconos/cross.png'></center></td>");
			
		} else {
			print("
			<td colspan=5>Disponible</td>
			<td><center><img src='../iconos/tick.png'></center></td>			
			");
		}
		
		print("<td><center><img style='cursor:pointer;' 
					onClick='editar(\"$clase\",\"$codigo\",".$j.");' 
					src='../iconos/pencil.png'></center></td>");
		
		print("</tr>");	
		
		}
	
	}
	
?>

</body>
</html>