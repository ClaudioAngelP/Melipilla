<?php
  require_once('../../conectar_db.php');
  function img($t) {    if($t=='t') return '<center><img src="../../iconos/tick.png"                                   width=12 height=12></center>';    else return '<center><img src="../../iconos/cross.png"                                   width=12 height=12></center>';  }

  $ca_id = $_POST['casos_auge']*1;  
  if($ca_id!=-1) {

	  list($ca)=cargar_registros_obj("	   SELECT * FROM casos_auge WHERE ca_id=$ca_id	 ");	
	  if($ca) {
	  	$pac_id=$ca['ca_pac_id'];
	  } else { 
	  	$pac_id=$_POST['pac_id'];
	  	$ca['id_sigges']=0;
	  }
  
  	$caso_w='id_caso='.$ca['id_sigges'];
  
  } else { 
 
	$pac_id=$_POST['pac_id'];
	$caso_w='true'; 
  
  }
  /*$lista = cargar_registros_obj("  	SELECT 
    COALESCE(pac_rut, pac_pasaporte, pac_id::text) AS pac_codigo,    pac_appat,pac_apmat,pac_nombres, presta_desc,    presta_codigo_i, glosa, presta_id, presta_cant, presta_compra,    pac_id, presta_fecha, inst_id, presta_fecha::date AS presta_fecha,
    prestacion.id_sigges, porigen_nombre, esp_desc
    	FROM prestacion		JOIN pacientes USING (pac_id)	LEFT JOIN codigos_prestacion ON codigo = presta_codigo_i	LEFT JOIN especialidades ON especialidades.esp_id=prestacion.esp_id	LEFT JOIN prestacion_origen USING (porigen_id)		WHERE pac_id=$pac_id AND $caso_w	ORDER BY prestacion.presta_fecha DESC
  ");*/  	$hosp_id=$_POST['hosp_id']*1;		if($hosp_id!=0){		$hosp_w=cargar_registro("SELECT *,COALESCE(hosp_fecha_egr,CURRENT_TIMESTAMP) as fecha_e FROM hospitalizacion WHERE hosp_id=$hosp_id;");		$hosp_q_p="presta_fecha>='".$hosp_w['hosp_fecha_ing']."' AND presta_fecha<='".$hosp_w['fecha_e']."'";		$hosp_q_n="nom_fecha>='".$hosp_w['hosp_fecha_ing']."' AND nom_fecha<='".$hosp_w['fecha_e']."'";		$hosp_q_r="receta_fecha_emision>='".$hosp_w['hosp_fecha_ing']."' AND receta_fecha_emision<='".$hosp_w['fecha_e']."'";	}else{		$hosp_q_p="true";		$hosp_q_n="true";		$hosp_q_r="true";	}  	$lista = cargar_registros_obj("  	SELECT * FROM (	SELECT 		presta_desc,		presta_codigo_i, glosa, presta_id, presta_cant, presta_compra,		pac_id, inst_id, presta_fecha::date AS presta_fecha,		prestacion.id_sigges, porigen_nombre, esp_desc,		'00:00:00' AS nomd_hora, '' AS doc_rut, '' AS doc_paterno, '' AS doc_materno, '' AS doc_nombres, 		'' AS diag_desc, '' AS nomd_diag_cod, '' AS nomd_tipo, '' AS nomd_extra    	FROM prestacion		LEFT JOIN codigos_prestacion ON codigo = presta_codigo_i	LEFT JOIN especialidades ON especialidades.esp_id=prestacion.esp_id	LEFT JOIN prestacion_origen USING (porigen_id)		WHERE pac_id=$pac_id AND NOT prestacion.porigen_id=1 AND $hosp_q_p	UNION	SELECT 				esp_desc AS presta_desc, nomd_codigo_presta AS presta_codigo_i,   		esp_desc AS glosa, -1 AS presta_id, 1 AS presta_cant, false AS presta_compra,		pac_id, 0 AS inst_id, nom_fecha::date AS presta_fecha, 0 AS id_sigges, 'C.A.E.' AS porigen_nombre,		esp_desc,		nomd_hora, doc_rut, doc_paterno, doc_materno, doc_nombres, 		COALESCE(diag_desc, cancela_desc) AS diag_desc, 		nomd_diag_cod, nomd_tipo, nomd_extra			FROM nomina_detalle 	JOIN nomina USING (nom_id)	JOIN especialidades ON nom_esp_id=esp_id	LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod	LEFT JOIN doctores ON nom_doc_id=doc_id	LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id  	WHERE pac_id=$pac_id AND $hosp_q_n		UNION 		SELECT art_glosa AS presta_desc, art_codigo AS presta_codigo_i,	art_glosa AS glosa, -2 AS presta_id, ABS(SUM(stock_cant)) AS presta_cant, false as presta_compra,	receta_paciente_id AS pac_id, 0 as inst_id, log_fecha::date AS presta_fecha, 0 as id_sigges, 'FARMACIA' AS porigen_nombre, 	'' AS esp_desc,	'00:00:00' AS nomd_hora, '' AS doc_rut, '' AS doc_paterno, '' AS doc_materno, '' AS doc_nombres, 	'' AS diag_desc, '' AS nomd_diag_cod, '' AS nomd_tipo, '' AS nomd_extra		FROM receta 	JOIN recetas_detalle ON recetad_receta_id=receta_id	JOIN logs ON log_recetad_id=recetad_id	JOIN stock ON stock_log_id=log_id	JOIN articulo ON stock_art_id=art_id	WHERE receta_paciente_id=$pac_id AND $hosp_q_r	GROUP BY art_glosa, art_codigo, receta_paciente_id, log_fecha	) AS foo	ORDER BY presta_fecha DESC	", true);
?>
<table style='width:100%;' class='lista_small'><tr class='tabla_header'><td style='width:10%;'>Fecha</td><td>C&oacute;d. Prestaci&oacute;n</td><td>Origen</td><td style='width:50%;'>Descripci&oacute;n Prestaci&oacute;n</td><td>Cant.</td><td>Ver</td></tr>
<?php 
  if($lista)  for($i=0;$i<count($lista);$i++) {
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    print("    <tr class='$clase'    onMouseOver='this.className=\"mouse_over\";'    onMouseOut='this.className=\"".$clase."\";'>    <td style='text-align:center;'>".$lista[$i]['presta_fecha']."</td>    <td style='text-align:center;font-size:18px;'>".$lista[$i]['presta_codigo_i']."</td>    <td style='text-align:center;font-size:10px;'>".($lista[$i]['porigen_nombre'])."</td>    ");        if($lista[$i]['presta_id']*1==-1) {		print("<td><b><u>".(trim($lista[$i]['esp_desc']))."</u></b> (Tipo: <b>".($lista[$i]['nomd_tipo']=='C'?'Control':'Nuevo')."</b> Sobrecupo: <b>".$lista[$i]['nomd_extra']."</b>)<br>		<b>[".$lista[$i]['nomd_hora']."]</b> Profesional: <i>".$lista[$i]['doc_paterno']." ".$lista[$i]['doc_materno']." ".$lista[$i]['doc_nombres']."</i><br>");				if($lista[$i]['nomd_diag_cod']=='NSP' OR $lista[$i]['nomd_diag_cod']=='X')		print("		<span style='color:red;'>Estado: <b>".$lista[$i]['nomd_diag_cod']."</b> <i>".$lista[$i]['diag_desc']."</i></span>		");		elseif($lista[$i]['nomd_diag_cod']=='')		print("		<span style='color:blue;'>Estado: <b>".$lista[$i]['nomd_diag_cod']."</b> <i>Citaci&oacute;n Pendiente</i></span>		");		else		print("		<span style='color:green;'>Diag. CIE10: <b>".$lista[$i]['nomd_diag_cod']."</b> <i>".$lista[$i]['diag_desc']."</i></span>		");						print("</td><td style='text-align:right;'>".($lista[$i]['presta_cant'])."</td>");			} elseif($lista[$i]['presta_id']*1==-2) {				print("<td style='color:green;font-weight:bold;'>".($lista[$i]['presta_desc'])."</td>		<td style='text-align:right;'>".number_format($lista[$i]['presta_cant']*1,0,',','.')."</td>");    	} else 		print("<td>".($lista[$i]['presta_desc'])."</td>		<td style='text-align:right;'>".($lista[$i]['presta_cant'])."</td>");          	if($lista[$i]['presta_id']*1>0) 
		print("
		<td><center><img src='../../iconos/magnifier.png' style='cursor:pointer;'
		onClick='abrir_presta(".$lista[$i]['presta_id'].");'></center></td>		");	else		print("		<td><center>&nbsp;</center></td>		");                print("</tr>");
  }
?>
</table>