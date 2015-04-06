<?php
	// Script de Despliegue de Información
	// Sistema de Gestión
	// Hospital San Martín de Quillota
	// ==========================================================================
	// Rodrigo Carvajal J.
	// Soluciones Computacionales Viña del Mar LTDA.
	// ==========================================================================
	require_once('conectar_db.php');
	if($_GET['tipo']=='proveedor') 
	{
		$id = strtoupper($_GET['id']);
		$query="SELECT * FROM proveedor WHERE prov_rut='$id' LIMIT 1";
		$provee = pg_query($conn,$query);
		if(pg_num_rows($provee)>0) 
		{
			$datos = pg_fetch_row($provee);
			print("
			<input type='hidden' name='proveedor_encontrado'  id='proveedor_encontrado' value='".$datos[0]."'>
			<table>
			<tr><td class='derecha' width=50><b>Nombre:</b></td><td width=210><b>".htmlentities($datos[2])."</b></td></tr>
			<tr><td class='derecha'>Direcci&oacute;n:</td>	<td>".htmlentities($datos[3])."</td></tr>
			<tr><td class='derecha'>Ciudad:</td>			<td>".htmlentities($datos[4])."</td></tr>
			<tr><td class='derecha'>Tel&eacute;fono:</td>	<td>".$datos[5]."</td></tr>
			<tr><td class='derecha'>F&aacute;x:</td>		<td>".$datos[6]."</td></tr>
			<tr><td class='derecha'>e-mail:</td>			<td>".$datos[7]."</td></tr>
			</table>");
		}
		else 
		{
			print("<input type='hidden' name='proveedor_encontrado'  id='proveedor_encontrado' value='0'>
			<center><b>(Proveedor no encontrado...)</b></center>");
		}

	}

if($_GET['tipo']=='articulo_corto') {

	$id = $_GET['codigo'];
	
	$articulo = pg_query($conn, "SELECT 
  art_id, art_glosa
  FROM articulo 
  WHERE art_codigo='$id' LIMIT 1");
	
	if(pg_num_rows($articulo)>0) {
	
		$datos = pg_fetch_row($articulo);
	
		print("
		<input type='hidden' name='articulo_id'  id='articulo_id' value='".$datos[0]."'>
		<span id='tooltip_sel' class='texto_tooltip'>
    ".htmlentities($datos[1])."</span>
    <script>
    TooltipManager.addAjax('tooltip_sel', { url: 'popups.php', options: {method: 'get', parameters: 'tipo=articulo&art_id=".($datos[0]*1)."'}}, 300, 300);
    </script>
    ");
	} else {
		print("
			<input type='hidden' name='articulo_id'  id='articulo_id' value=''>
		  <i><b>(Art&iacute;culo no encontrado...)</b></i>
		");
	}

}

if($_GET['tipo']=='med_corto' OR $_GET['tipo']=='med_control_corto') {

	$id = $_GET['codigo'];
	$bodega = ($_GET['bodega_id']*1);

	if($_GET['tipo']=='med_corto') {
    $condicion='AND art_control=0';
  } else {
    $condicion='AND (NOT art_control=0)';
  }

	$articulo = pg_query($conn, "SELECT
  art_id, art_glosa, calcular_stock_trans(art_id, ".$bodega."), art_control
  FROM articulo
  WHERE
  art_codigo='".pg_escape_string($id)."'
  AND (art_item='2204004001' OR art_item='2204005001' OR art_item='2204004003' OR art_id=6543 OR art_id=540)
  $condicion LIMIT 1");

	if(pg_num_rows($articulo)>0) {

		$datos = pg_fetch_row($articulo);

		print("
		<input type='hidden' name='articulo_id'  id='articulo_id' value='".$datos[0]."'><input type='hidden' name='articulo_stock'  id='articulo_stock' value='".$datos[2]."'><input type='hidden' name='articulo_control'  id='articulo_control' value='".$datos[3]."'>
		<span id='tooltip_sel'><b>
    ".htmlentities($datos[1])."</b></span>
    ");
	} else {
		print("
			<input type='hidden' name='articulo_id'  id='articulo_id' value=''>
		  <i><b>(Art&iacute;culo no encontrado...)</b></i>
		");
	}

}


if($_GET['tipo']=='diagnostico') {

	$codigo = $_GET['codigo'];

	$diagnostico = pg_query($conn, "SELECT
  diag_desc
  FROM diagnosticos
  WHERE diag_cod='".$codigo."' LIMIT 1");

	if(pg_num_rows($diagnostico)>0) {
	
		$datos = pg_fetch_row($diagnostico);
	
		print("
		<input type='hidden' name='diag_existe'  id='diag_existe' value=1>
		<span>".htmlentities($datos[0])."</span>");
	} else {
		print("
		<input type='hidden' name='diag_existe'  id='diag_existe' value=0>
		<span>(Diagn&oacute;stico no encontrado...)</span>");
		
	}

}


if($_GET['tipo']=='stock' or $_GET['tipo']=='stock_receta') {

	$id = $_GET['codigo_art'];
	$bodega = $_GET['bodega_origen'];
	
	$producto = pg_query($conn, "
	SELECT
	
	art_codigo,
	art_glosa,
	art_nombre,
	clasifica_nombre,
	forma_nombre,
	art_id
	
	FROM articulo
	
	LEFT JOIN bodega_clasificacion
	ON art_clasifica_id=clasifica_id
	
	LEFT JOIN bodega_forma
	ON art_forma=forma_id
	
	WHERE art_codigo=$id
	
	LIMIT 1
	
	");
	
	$prod=pg_fetch_row($producto);
	
	if($prod[3]=='')	$prod[3]='(No Aplicable...)';
	if($prod[4]=='')	$prod[4]='(No Aplicable...)';
	
	
	$stock = pg_query($conn, "
	SELECT
	stock_vence,
	SUM(stock_cant),
	(stock_vence - current_date) AS vence
	 
	FROM stock 
	
	WHERE 
	stock_art_id=".$prod[5]."
	AND
	stock_bod_id=$bodega
	
	GROUP BY stock_vence
	HAVING SUM(stock_cant)>0
	ORDER BY stock_vence
	
	");
	
	
	printf("
	<center>
	<table>
	<tr class='tabla_header'><td colspan=2><b>Informaci&oacute;n del Producto:</b></td></tr>
	<tr><td class='tabla_fila' style='text-align: right;'><b>".$prod[0]."</b></td>					
	<td class='tabla_fila2' width=350>
  <span id='tooltip_sel' class='texto_tooltip'>
    ".htmlentities($prod[1])."</span>
    <script>
    TooltipManager.addAjax('tooltip_sel', { url: 'popups.php', options: {method: 'get', parameters: 'tipo=articulo&art_id=".($prod[5]*1)."'}}, 300, 300);
    </script>
  </td></tr>
	<tr><td class='tabla_fila' style='text-align: right;'><i>Clasificaci&oacute;n:</i></td>	
	<td class='tabla_fila2'>".htmlentities($prod[3])."</td></tr>
	<tr><td class='tabla_fila' style='text-align: right;'><i>Forma Farm.:</i></td>			
	<td class='tabla_fila2'>".htmlentities($prod[4])."</td></tr>
	</table>
	");
	
	print("
	<table><tr><td valign='top'>
	
	<table width=260>
	<tr class='tabla_header'><td colspan=2><b>Lotes</b></td></tr>
	<tr class='tabla_header'>
	<td><i>Vencimiento (".pg_num_rows($stock).")</i></td>
	<td><i>Stock</i></td></tr>");
	
	$total=0;
	$vencido=0;
	
	for($i=0;$i<pg_num_rows($stock);$i++) {
		$datos = pg_fetch_row($stock);
		
		if($datos[2]<0) {
			$vencido+=($datos[1]*1);
			$color = "<font color='red'>";
		} else {
			$total+=($datos[1]*1);
			$color = "";
		}
		
		if($datos[0]=='') $datos[0]='(No Aplicable...)';
		
		print(
		"<tr class='tabla_fila2'>
		<td class='derecha'><i>".$color.$datos[0]."</i></td>
		<td class='derecha'><b>".$color.$datos[1]."</b></td>
		</tr>");
		
	}
	
	print("</table>
	
	</td><td valign='top'>
	
	<table width=180>
	<tr class='tabla_header'><td colspan=2><b>Totales</b></td></tr>
	<tr class='tabla_fila2'><td class='derecha'><i><b>Total Disp.:</b></i></td><td class='derecha'>
	<b>".($total)."</b></td></tr>
	<tr class='tabla_fila2'><td class='derecha'><i>Total General:</i></td><td class='derecha'>"
	.($total+$vencido)."</td></tr>
	");
	
	if($vencido>0) {
		print("<tr class='tabla_fila2'><td class='derecha'><i><font color='red'>Total Vencido:</i></td>
		<td class='derecha'><font color='red'>".$vencido."</td></tr>");
	}
	
	print("</table>
	
	");
	
	if($_GET['tipo']=='stock') {
	
		// Muestra Stock para Traslados, solo campo Cantidad.
	
		print("
		<div class='sub-content'>
			<table width=160><tr><td>Cantidad:</td><td>
			<input type='text' id='cantidad' name='cantidad' style='text-align: right;' size=6
			onKeyUp='
				if((this.value*1)>$total) { this.value=$total; }
			'
			onKeyPress='
			if((this.value*1)>0 && event.which==13) {
				this.value=(this.value*1);
				seleccionar_articulo(".$prod[5].",this.value,0);
				this.value=\"\";
			}
			'>
		"); 
		
	} else {

		// Muestra Stock para Recetas, permite ingresar dosis y calcula Cantidad.
	
		print("
		
		<div class='sub-content'>
			<table>
			<tr><td style='text-align: right;'>Dosis:</td>
			<td colspan=2>
			<input id='dosis' type='text' size=2 style='text-align: right;' onChange='calcular_cantidad();'>
			</td></tr>
			<tr><td style='text-align: right;'>cada</td>
			<td>
			<input id='horas' type='text' size=2 style='text-align: right;' onChange='calcular_cantidad();'>
			</td><td>horas</td></tr>
			<tr><td style='text-align: right;'>durante</td>
			<td>
			<input id='dias' type='text' size=2 style='text-align: right;' onChange='calcular_cantidad();'>
			</td><td>d&iacute;as.</td></tr>
			
			<tr><td style='text-align: right;'>Cantidad:</td><td colspan=2>
			<input type='text' id='cantidad' name='cantidad' style='text-align: right;' size=3
			onKeyUp='
			if((this.value*1)>$total) { this.value=$total; }
			'
			onBlur='
			if((this.value*1)>0) {
				seleccionar_art($id,this.value);
			}
			'>
		"); 

	}
	
	print("
	</td></tr></table>
	</div>
	
	</td></tr></table>
	
	</center>
	");

}

if($_GET['tipo']=='sel_stock') {

	$id=($_GET['id'])*1;
	$bodega=($_GET['bodega_origen'])*1;
	$cantidad=($_GET['cantidad'])*1;
	$tipo=($_GET['venc_tipo'])*1;
	
	$articulo = pg_query($conn, "
	SELECT
	art_codigo, 
	art_glosa
	FROM
	articulo
	WHERE art_id=$id
	LIMIT 1
	");
	
	$art_dato = pg_fetch_row($articulo);
	
	$lotes = pg_query($conn, "
		SELECT 
		stock_vence,
		SUM(stock_cant),
		(stock_vence - current_date) AS vence
	 
		FROM stock 
	
		WHERE 
		stock_art_id=$id
		AND
		stock_bod_id=$bodega
		
		GROUP BY stock_vence
		HAVING SUM(stock_cant)>0
		ORDER BY stock_vence
	");
	
	$indice=1;
	
	$devolver[0][0] = htmlentities($art_dato[0]);
	$devolver[0][1] = htmlentities($art_dato[1]);
	$devolver[0][2] = $cantidad;
	$temp = pg_fetch_row($lotes,0);
	$devolver[0][3] = $temp[0];

	for($i=1;$i<=pg_num_rows($lotes);$i++) {
		$lote_dato = pg_fetch_row($lotes,$i-1);
		
		  if($cantidad) {
		
			if(($tipo==0 and $lote_dato[2]>0) or ($tipo==1 and $lote_dato[2]<=0)) {
				if($cantidad>=$lote_dato[1]) {
					$devolver[$indice][0]=$lote_dato[0];
					$devolver[$indice][1]=$lote_dato[1];
					$cantidad-=$lote_dato[1];
					$indice++;
				} else {
					$devolver[$indice][0]=$lote_dato[0];
					$devolver[$indice][1]=$cantidad;
					$cantidad=0;
					$indice++;
				}
			}
			
			}
			
		}
	
	
	print(json_encode($devolver));
	

}

	if($_GET['tipo']=='items_listado') {

		$items = pg_query($conn, "
		SELECT * FROM item_presupuestario ORDER BY item_codigo
		");
		
		print("
			
			<html>

			<script src='prototype.js' 	type='text/javascript'></script>
			<script src='common.js' 	type='text/javascript'></script>

			<link id=\"one\" rel=\"stylesheet\" type=\"text/css\" href=\"css/interface.css\">
	
			<body style='font-family: Arial, Helvetica, sans-serif;'
			leftmargin=0 topmargin=0 rightmargin=0>
	
			<table width='100%'>
			<tr class='tabla_header'>
			<td><b>C&oacute;digo</b></td>
			<td><b>Glosa</b></td>
			</tr>
		");
		
		for($i=0;$i<pg_num_rows($items);$i++) {

			$datos = pg_fetch_row($items);
			
			($i%2==0) ? 	$clase='tabla_fila' : $clase='tabla_fila2';

			print("
			<tr class='".$clase."'
			onMouseOver='this.className=\"mouse_over\"'
			onMouseOut='this.className=\"$clase\"'>
			<td style='font-size: 10px;'>".$datos[0]."</td>
			<td style='font-size: 10px;'>".$datos[1]."</td></tr>
			");		
			
		}

		print("
			</table>
			</body>
			</html>
		");
		
	}
	
	if($_GET['tipo']=='listado_centros_corto') {
	
	if(isset($_GET['id'])) { $id=$_GET['id']; } else { $id=''; }
	
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
	AS centro_contiene
	FROM
	centro_costo
	ORDER BY
	centro_ruta
	");
	
	print("
	
  <html>
	<style>
	
	.selector {
	font-family: Arial, Helvetica, sans-serif;
  	font-size: 12px;
  	border-bottom: 1px solid #BBBBBB;
  	vertical-align: center;
  	}
  
  .selector td {
  vertical-align: top;
  font-size: 12px;
  
  }
  
  .mouse_over {
  border: 1px solid black;
  background-color: #BBBBBB;
  }
  
  .mouse_over td {
  vertical-align: top;
  font-size: 12px;
  }
  
  .marcado {
  border: 1px solid black;
  background-color: #888888;
  vertical-align: top;
  }
  
  </style>
  
  <body bgcolor='white' topmargin=0 leftmargin=0 rightmargin=0>
  <table id='centros' width=100% cellpadding=0 cellspacing=0>");
	
	for($i=0;$i<pg_num_rows($centros);$i++) {
	
		$datos=pg_fetch_row($centros);
		
		if(($datos[2])==1) {
				$clase='tabla_fila';
				$estilofuente = "<b>";
		} else {
				$clase='tabla_fila2';
				$estilofuente = "";
		}
		
		if($datos[0]==$id) { $clase='marcado'; }
		
		$datos[3]>0 ? $bullet='bullet_toggle_plus.png' : $bullet='bullet_orange.png';
		
		$espaciado = str_repeat("<img src='iconos/blank.gif'>", $datos[2]-1);
		
		print("
		<tr class='".$clase."' id='ruta_".$datos[0]."'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"".$clase."\";'
		onClick='parent.asignar_centro(\"".$datos[0]."\",\"".htmlentities($datos[1])."\");'
		>
		<td class='selector'>
    
    <table><tr><td>
    $espaciado<img src='iconos/$bullet'>
    </td>
    <td>$estilofuente
		<span id='texto_".$datos[0]."'>".htmlentities($datos[1])."</span>
		</td></tr></table>
		
    </td>
		</tr>
		");
	
	}
	
	print("</table></body> 
	</html>
	
	");
	
  }
  
  if($_GET['tipo']=='estado_interconsultas' or $_GET['tipo']=='revisar_interconsultas') {

		if($_GET['tipo']=='estado_interconsultas') {
			$institucion = $_GET['institucion'];
			$buscar = $_GET['buscar'];
		
			$orden = $_GET['orden'];
		
			if(isset($_GET['ascendente'])) {
				$ascen = '';
			} else {
				$ascen='DESC';
			}
		
		
			switch ($orden) {
				case 0: $orden='inter_ingreso'; break;
				case 1: $orden='pac_rut'; break;
				case 2: $orden='pac_appat, pac_apmat, pac_nombres'; break;
				case 3: $orden='esp_desc'; break;
				case 4: $orden='inter_folio'; break;
			}
		
			if(trim($buscar)!='') {
			$condicion="
			WHERE (
			inter_ingreso ILIKE '%$buscar%' OR
			pac_rut ILIKE '%$buscar%' OR
			pac_appat ILIKE '%$buscar%' OR
			pac_apmat ILIKE '%$buscar%' OR
			pac_nombres ILIKE '%$buscar%' OR
			esp_desc ILIKE '%$buscar%'
			) AND
			inter_instsol=$institucion
			";
			} else {
			$condicion="WHERE inter_instsol=$institucion";
			}
		
			$resultado = pg_query($conn, "
			SELECT inter_folio, inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 
			esp_desc, inter_estado 
			FROM interconsulta 
			
			LEFT JOIN pacientes ON inter_pac_id=pac_id
			LEFT JOIN especialidades ON inter_especialidad=esp_id
			
			$condicion
			ORDER BY $orden
			$ascen
			");
			
			print("<table width=100%>
			<tr class='tabla_header' style='font-weight: bold;'>
			<td>Fecha Ing.</td>
			<td>Nro. Folio</td>
			<td>Rut Paciente</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombre</td>
			<td>Especialidad</td>
			<td>Estado</td>
			</tr>
			");
		
		} else {
			
			$especialidad=($_GET['especialidad']*1);
			
			$resultado = pg_query($conn, "
			SELECT inter_folio, inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 
			esp_desc, inter_estado, instsol_desc,inter_instsol
			FROM interconsulta 
			
			LEFT JOIN pacientes ON inter_pac_id=pac_id
			LEFT JOIN especialidades ON inter_especialidad=esp_id
			LEFT JOIN institucion_solicita ON inter_instsol=instsol_id
		
			WHERE inter_especialidad=$especialidad AND inter_estado=0
			ORDER BY inter_folio
			");
		
			print("<table width=100%>
			<tr class='tabla_header' style='font-weight: bold;'>
			<td>Fecha Ing.</td>
			<td>Procedencia</td>
			<td>Nro. Folio</td>
			<td>Rut Paciente</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombre</td>
			<td>Estado</td>
			</tr>
			");
		}
		
		
		
		for($i=0;$i<pg_num_rows($resultado);$i++) {
			
			$fila = pg_fetch_row($resultado);
			
			for($a=0;$a<count($fila);$a++) $fila[$a] = htmlentities($fila[$a]);
			
			($i%2)==1	?	$clase='tabla_fila'	: $clase='tabla_fila2';
			
			switch($fila[7]) {
				case 0: $imagen = 'iconos/time.png'; 
						$texto='En espera...'; break;
				case 1: $imagen = 'iconos/tick.png'; 
						$texto='Aceptado'; break;
				case 2: $imagen = 'iconos/cross.png'; 
						$texto='Rechazado'; break;
				case 3: $imagen = 'iconos/arrow_branch.png'; 
						$texto='Derivado...'; break;
				case 4: $imagen = 'iconos/user_go.png'; 
						$texto='Atendido y Retornado'; break;
				case 5: $imagen = 'iconos/user_add.png'; 
						$texto='Atendido y Permanente'; break;
			}
			
			if($_GET['tipo']=='estado_interconsultas') {
		
			print("
			<tr class='".$clase."'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'
			onClick='abrir_ficha(".$fila[0].");'
			>
			<td style='text-align: right;'><i>".$fila[1]."</i></td>
			<td style='text-align: right;'><i>".$fila[0]."</i></td>
			<td style='text-align: right;'><b>".$fila[2]."</b></td>
			<td><b>".$fila[3]."</b></td>
			<td><b>".$fila[4]."</b></td>
			<td><b>".$fila[5]."</b></td>
			<td>".$fila[6]."</td>
			<td><center>
			<img src='".$imagen."' alt='".$texto."' title='".$texto."'>
			</center></td>
			</tr>
			");
		
			} else {
		
			print("
			<tr class='".$clase."'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'
			");
			
			if($i==0) print("onClick='abrir_ficha(".$fila[0].",".$fila[9].");'");
			
			print(">
			<td style='text-align: right;'><i>".$fila[1]."</i></td>
			<td style='text-align: center;'><i>".$fila[8]."</i></td>
			<td style='text-align: right;'><b>".$fila[0]."</b></td>
			<td style='text-align: right;'><b>".$fila[2]."</b></td>
			<td><b>".$fila[3]."</b></td>
			<td><b>".$fila[4]."</b></td>
			<td><b>".$fila[5]."</b></td>
			<td><center>
			<img src='".$imagen."' alt='".$texto."' title='".$texto."'>
			</center></td>
			</tr>
			");
			
			}	
		}
		
		print("</table>");
		
  }
  
  if($_GET['tipo']=='inter_ficha' OR $_GET['tipo']=='revisar_inter_ficha') {
  
  	$folio=$_GET['nro_folio'];
  		
		$datos=pg_query($conn, "
		
		SELECT 
		
		inter_folio, 
		inter_ingreso, 
		pac_rut, 
		pac_appat, 
		pac_apmat, 
		pac_nombres,
		pac_fc_nac,
		pac_direccion,
		ciud_desc,
		prov_desc,
		reg_desc,
		sex_desc,
		prev_desc,
		sang_desc,
		getn_desc
		
		FROM interconsulta 
		
		LEFT JOIN pacientes ON inter_pac_id=pac_id
		LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id
		LEFT JOIN provincias ON comunas.prov_id=provincias.prov_id
		LEFT JOIN regiones ON provincias.reg_id=regiones.reg_id
		LEFT JOIN sexo ON pacientes.sex_id=sexo.sex_id
		LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
		LEFT JOIN grupo_sanguineo ON pacientes.sang_id=grupo_sanguineo.sang_id
		LEFT JOIN grupos_etnicos ON pacientes.getn_id=grupos_etnicos.getn_id
		
		WHERE inter_folio=$folio
		
		");
		
		$datos2 = pg_query("
    
    SELECT
    
    esp_desc,
		inter_fundamentos,
		inter_examenes,
		inter_comentarios,
		inter_estado,
		inter_rev_med,
		inter_prioridad,
		instsol_desc,
		inter_instsol,
		inter_motivo,
    inter_diag_cod,
		diag_desc,
		COALESCE(garantia_nombre, pat_glosa),
		COALESCE(garantia_id, pat_id)
		
    FROM interconsulta
    
    LEFT JOIN especialidades ON inter_especialidad=esp_id
		LEFT JOIN institucion_solicita ON inter_instsol=instsol_id
		LEFT JOIN diagnosticos ON inter_diag_cod=diag_cod
		LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id
		LEFT JOIN patologias_auge ON inter_pat_id=pat_id
		
    WHERE inter_folio=$folio
		
    ");
    		
		$inter = pg_fetch_row($datos);
		$inter2 = pg_fetch_row($datos2);
		
    $institucion=$inter2[8];

		switch($inter2[9]) {
      case 0: $inter2[9]='Confirmaci&oacute;n Diagn&oacute;stica'; break;
      case 1: $inter2[9]='Realizar Tratamiento'; break;
      case 2: $inter2[9]='Seguimiento'; break;
      default: $inter2[9]='Otro Motivo'; break;
    }
		
		
		for($a=0;$a<count($inter);$a++) $inter[$a] = htmlentities($inter[$a]);
		
		switch($inter2[4]) {
				case 0: $imagen = 'iconos/time.png'; 
						$texto='En espera...'; break;
				case 1: $imagen = 'iconos/tick.png'; 
						$texto='Aceptado'; break;
        case 2: $imagen = 'iconos/cross.png'; 
						$texto='Rechazado'; break;
				case 3: $imagen = 'iconos/arrow_branch.png';
						$texto='Derivado...'; break;
				case 4: $imagen = 'iconos/user_go.png'; 
						$texto='Atendido y Retornado'; break;
				case 5: $imagen = 'iconos/user_add.png'; 
						$texto='Atendido y Permanente'; break;
		}
			
		
		print("
		<html>
		
		<title>Ficha de Interconsulta</title>
		
		");
    
    cabecera_popup('.');
    
    print("
		<script>
		
		guardar_resolucion = function() {
		
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=guardar_resolucion&'+$('resolucion').serialize(),
				onComplete: function (pedido_datos) {
					
          if(pedido_datos.responseText=='OK') {
						
					  alert('Resoluci&oacute;n guardada exitosamente.'.unescapeHTML());
						window.opener.realizar_busqueda();
						window.opener.focus();
						window.close();
												
					} else {
					
						alert('ERROR:\\n'+pedido_datos.responseText);
						
					}
				}
			}
			
			);
					
		}
		
		  function abrir_diagnosticos() {
      
      diag_win = window.open('registro.php?tipo=buscar_diag',			'diagwin', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			
			diag_win.focus();
  }

		
		</script>
	
		<style>

		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10px;
		}

		</style>
	
		
		<body topmargin=0 leftmargin=0 rightmargin=0>
		
		<div class='sub-content'>
		<div class='sub-content'><img src='iconos/script.png'> <b>Ficha de Interconsulta</b></div>
		<div class='sub-content2'>
		<center>
		<table>
		<tr><td style='text-align:right;'>Procedencia:</td><td> <b>".$inter2[7]."</b></td></tr>
		<tr><td style='text-align:right;'>N&uacute;mero de Folio:</td><td><b>".$inter[0]."</b></td></tr>
		</table>
		</center>
		</div>
		
		<div class='sub-content'><img src='iconos/user_orange.png'> <b>Datos de Paciente</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>RUT:</td>				<td><b>".$inter[2]."</b></td></tr>
		<tr><td style='text-align:right;'>Apellido Paterno:</td>
		<td><b><i>".$inter[3]."</i></b></td></tr>
		<tr><td style='text-align:right;'>Apellido Materno:</td>
		<td><b><i>".$inter[4]."</i></b></td></tr>
		<tr><td style='text-align:right;'>Nombre(s):</td>			
		<td><b><i>".$inter[5]."</i></b></td></tr>
		<tr><td style='text-align:right;'>Fecha de Nacimiento:</td>
    <td>".$inter[6]."</td></tr>
		<tr><td style='text-align:right;'>Edad:</td>
    <td id='paciente_edad'>".$inter[6]."</td></tr>
		<tr><td style='text-align:right;'>Direcci&oacute;n:</td>				
    <td>".$inter[7]."</td></tr>
		<tr><td style='text-align:right;'>Comuna:</td>				
    <td><b>".$inter[8]."</b>, ".$inter[9].", <i>".$inter[10]."</i>.- </td></tr>
		<tr><td style='text-align:right;'>Sexo:</td>				
    <td>".$inter[11]."</td></tr>
		<tr><td style='text-align:right;'>Previsi&oacute;n:</td>	
    <td>".$inter[12]."</td></tr>
		<tr><td style='text-align:right;'>Grupo Sangu&iacute;neo:</td>			
    <td><b>".$inter[13]."</b></td></tr>
		<tr><td style='text-align:right;'>Grupo &Eacute;tnico:</td>
    <td>".$inter[14]."</td></tr>
		</table>
		</div>
		
		<div class='sub-content'><img src='iconos/chart_organisation.png'> <b>Informaci&oacute;n de Interconsulta</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>Especialidad:</td>		
		<td width=60%><b>".$inter2[0]."</b></td></tr>
		<tr><td style='text-align:right;'>Motivo Derivaci&oacute;n:</td>		
		<td width=60%>".$inter2[9]."</td></tr>
    <tr><td style='text-align:right;' valign='top'>Diagn&oacute;stico (Pres.):</td>		
		<td width=60%><b>".$inter2[10]."</b><br>".htmlentities($inter2[11])."</td></tr>
    <tr><td style='text-align:right;' valign='top'>Sospecha AUGE:</td>
    ");
    
    
    if($inter2[13]!=1)
    	print("<td><b>".$inter2[12]."</b></td></tr>");
  	else
    	print("<td>No hay sospecha.</td></tr>");
  	
    print("<tr><td style='text-align:right;' valign='top'>Fundamentos Cl&iacute;nicos:</td>				
		<td>".$inter2[1]."</td></tr>
		");
		
		if(trim($inter2[2])!="")
		print("
		<tr><td style='text-align:right;' valign='top'>Ex&aacute;menes Comp.:</td>
		<td>".$inter2[2]."</td></tr>");
		
		if(trim($inter2[3])!="")
		print("
		<tr><td style='text-align:right;' valign='top'>Comentarios:</td>			
		<td>".$inter2[3]."</td></tr>");
		
		print("
		</table>
		</div>");
		
		if($_GET['tipo']=='inter_ficha') {
		
		if($inter2[6]==1)   $inter2[6]='Baja';
    if($inter2[6]==2)   $inter2[6]='Media';
    if($inter2[6]==3)   $inter2[6]='Alta';
    
    print("
		<div class='sub-content'><img src='iconos/page_edit.png'> 
		<b>Resoluci&oacute;n M&eacute;dica</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;' width=150>Estado Actual:</td>				
		<td>
		
		<table><tr><td><img src='".$imagen."'></td><td> <b>".$texto."</b>
		</td></tr></table>
		
		</td></tr>
		
    ");
    
    
    if($inter2[4]==1)
    print("
    <tr><td style='text-align:right;' valign='top'>Prioridad:</td>
		<td>".$inter2[6]."</td></tr>
		");
		
		if(trim($inter2[5])!='') 		
		print("		
		<tr><td style='text-align:right;' valign='top'>Observaciones:</td>
		<td>".$inter2[5]."</td></tr>
		");
		
		print("
    
    </table>
		</div>");
		
		} else {
		
		$prioridadhtml = desplegar_opciones("prioridad", 
		"prior_id, prior_desc",'prior_id=0','true','ORDER BY prior_id'); 
	
		print("
		<div class='sub-content'><img src='iconos/page_edit.png'> 
		<b>Resoluci&oacute;n M&eacute;dica</b></div>
		<div class='sub-content2'>
		<form name='resolucion' id='resolucion'>
		<table>
		<tr><td style='text-align:right;' width=150>Estado del Caso:</td>				
		<td>
		
		<input type='hidden' name='folio' id='folio' value='".$folio."'>
		<input type='hidden' name='institucion' id='institucion' value='".$institucion."'>
		<select id='estado' name='estado'>
		<option value=1>Aceptado</option>
		<option value=2>Rechazado...</option>
		<option value=3>Derivado...</option>
		</select>
		
		</td></tr>
		
		<tr><td style='text-align:right;' width=150>Prioridad:</td>				
		<td>
		
		<select id='prioridad' name='prioridad'>
		".$prioridadhtml."
    </select>
		
		</td></tr>
		
		<tr><td style='text-align:right;'>Cod. Diagn&oacute;stico:</td>
    <td>
    <input type='text' id='diag' name='diag' size=3>
    <img src='iconos/zoom_in.png' onClick='abrir_diagnosticos();'>
    </TD></tr>
		<tr><td style='text-align:right;' valign='top'>Observaciones:</td>
		<td>
		<textarea cols=30 rows=3 
    id='observaciones' name='observaciones'></textarea>
		</td></tr>
		
		</table>
		</div>
		
		<center><div class='boton'><table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='guardar_resolucion();'>Guardar Resoluci&oacute;n...</a>
		</td></tr></table></div>
		</center>
		</form>
		
		");
		
		
		}
		
		print("
		</div>
		</body>
		<script>
		$('paciente_edad').innerHTML = '<i>'+window.opener.calc_edad($('paciente_edad').innerHTML)+'</i>';
		</script>
		</html>
		");
		
  }
  
  if($_GET['tipo']=='buscar_presta') {

    $buscar=$_GET['buscar'];
    
    $diags = pg_query($conn,"
    SELECT
    grupo,
    sub_grupo,
    presta,
    glosa
    FROM mai
    WHERE
    diag_glosa ILIKE '%$buscar%'
    ");
    
    print("
    <table width=100%>
    <tr class='tabla_header' style='font-weight: bold;'>
    <td>C&oacute;digo</td>
    <td>Descripci&oacute;n</td>
    </tr>
    ");
    
    for($i=0;$i<pg_num_rows($diags);$i++) {
    
    $fila=pg_fetch_row($diags);
    ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
  
    
    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\"'
    onMouseOut='this.className=\"$clase\"'
    >
    <td><b><i>".$fila[0]."</i></b></td>
    <td><i>".htmlentities($fila[1])."</i></td>
    </tr>
    ");
    
    }

    print("</table>");

  }

  
?>
