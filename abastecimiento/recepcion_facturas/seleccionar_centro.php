<?php

  require_once('../../conectar_db.php');
  
  $centro = $_GET['centro_ruta'];
  
  $centros = pg_query($conn,"
	SELECT
	centro_ruta,
	centro_nombre,
	length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel,
	(
	SELECT COUNT(*)
	FROM centro_costo AS cnt
	WHERE cnt.centro_ruta ~ ('^'||centro_costo.centro_ruta||'\\\.')
	)
	AS centro_contiene,
	centro_medica,
	centro_gasto
	FROM
	centro_costo
	ORDER BY
	centro_ruta
	");
  
?>  

<html><title>Seleccionar Centro de Costo</title>

<?php cabecera_popup('../..'); ?>

<script>

  seleccionar_centro = function (centro_ruta, centro_nombre) {
  
    window.opener.$('centro_ruta').value=centro_ruta;
    window.opener.$('centro_nombre').value=centro_nombre;
    window.close();
  
  }

</script>

<body class='fuente_por_defecto popup_background' 
topmargin=0 leftmargin=0 rightmargin=0>

<table style='width:100%; font-size:12px;'>
<tr class='tabla_header'>
<td>Centros de Costo</td>
</tr>
<?php   
  
  for($i=0;$i<pg_num_rows($centros);$i++) {
	
		$datos=pg_fetch_row($centros);
		
		if($datos[0]!=$centro) {
      if(($datos[2])==1) {
				$clase='tabla_fila';
				$estilofuente = "<b>";
		  } else {
				$clase='tabla_fila2';
				$estilofuente = "";
		  }
		} else {
      $clase='mouse_over';
      $estilofuente="<i><b>";
    }
		
		($datos[3]>0)?$bullet='bullet_toggle_plus.png':$bullet='bullet_orange.png';
		
		$espaciado = str_repeat("<img src='../../iconos/blank.gif'>", $datos[2]);
		
		print("
		<tr class='$clase' id='ruta_".$datos[0]."'
		onMouseOver='this.className=\"mouse_over\"'
		onMouseOut='this.className=\"$clase\"'
    onClick='seleccionar_centro(\"".$datos[0]."\",\"".$datos[1]."\");'>
		<td>$espaciado<img src='../../iconos/$bullet'>$estilofuente
		<span id='texto_".$datos[0]."'>".htmlentities($datos[1])."</span>
		</td>
		</tr>
		");
	
	}
  
?>

</table>
</body>
</html>
