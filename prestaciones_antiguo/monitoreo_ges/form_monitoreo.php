<?php 

	require_once('../../conectar_db.php');

	$mon_id=$_GET['mon_id']*1;
	
	$m=cargar_registro("SELECT * FROM monitoreo_ges 
	LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
	LEFT JOIN patologias_auge USING (pst_patologia_interna)
	WHERE mon_id=$mon_id", true);
	
	$mr=cargar_registro("
    SELECT *, (monr_fecha_evento IS NOT NULL AND monr_fecha_evento<CURRENT_DATE) AS reclasificar FROM monitoreo_ges_registro 
      LEFT JOIN monitoreo_ges USING (mon_id)
      LEFT JOIN lista_dinamica_condiciones ON id_condicion=monr_clase::integer
      LEFT JOIN lista_dinamica_bandejas ON codigo_bandeja=monr_subclase
    WHERE mon_id=$mon_id AND monr_estado=0
    ORDER BY monr_fecha ASC;
  ");
	
	$sin_monitorear=($mr['cnt']*1==0 OR $mr['lista_remonitorear']=='t' OR $mr['reclasificar']=='t')?true:false;
	
	$pac=cargar_registro("
		SELECT *,
		UPPER(pac_appat) as pac_appat, UPPER(pac_apmat) AS pac_apmat, UPPER(pac_nombres) AS pac_nombres,
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	 	date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	 	date_part('day',age(now()::date, pac_fc_nac)) as edad_dias
		FROM pacientes 
		LEFT JOIN comunas USING (ciud_id)
		LEFT JOIN prevision USING (prev_id)
		WHERE pac_rut='".$m['mon_rut']."'");
	
	$nreg=pg_query("SELECT monr_id FROM monitoreo_ges_registro WHERE mon_id=$mon_id");

	$pat_id = $m['pat_id']*1;

	$pac_id = $pac['pac_id']*1;
	
	$listado = cargar_registros_obj("

		SELECT 
			detalle_patauge.*, 
			codigos_prestacion.*
		FROM detalle_patauge 
		JOIN codigos_prestacion ON codigo = detalle_patauge.presta_codigo 
		WHERE detalle_patauge.pat_id=$pat_id 

	");

	if($pac_id!=0)
		$presta=cargar_registros_obj("
		
		SELECT * FROM (

		SELECT 

			presta_desc,
			presta_codigo_i, glosa, presta_id, presta_cant, presta_compra,
			pac_id, inst_id, presta_fecha::date AS presta_fecha,
			prestacion.id_sigges, porigen_nombre, esp_desc,
			'00:00:00' AS nomd_hora, '' AS doc_rut, '' AS doc_paterno, '' AS doc_materno, '' AS doc_nombres, 
			'' AS diag_desc, '' AS nomd_diag_cod, '' AS nomd_tipo, '' AS nomd_extra
		
		FROM prestacion
		
		LEFT JOIN codigos_prestacion ON codigo = presta_codigo_i
		LEFT JOIN especialidades ON especialidades.esp_id=prestacion.esp_id
		LEFT JOIN prestacion_origen USING (porigen_id)
		
		WHERE pac_id=$pac_id AND NOT prestacion.porigen_id=1

		UNION

		SELECT 
			
			esp_desc AS presta_desc, nomd_codigo_presta AS presta_codigo_i,   
			esp_desc AS glosa, -1 AS presta_id, 1 AS presta_cant, false AS presta_compra,
			pac_id, 0 AS inst_id, nom_fecha::date AS presta_fecha, 0 AS id_sigges, 'C.A.E.' AS porigen_nombre,
			esp_desc,
			nomd_hora, doc_rut, doc_paterno, doc_materno, doc_nombres, 
			COALESCE(diag_desc, cancela_desc) AS diag_desc, 
			nomd_diag_cod, nomd_tipo, nomd_extra
			
		FROM nomina_detalle 
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
		LEFT JOIN doctores ON nom_doc_id=doc_id
		LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
	  
		WHERE pac_id=$pac_id
		
		UNION 
		
		SELECT art_glosa AS presta_desc, art_codigo AS presta_codigo_i,
		art_glosa AS glosa, -2 AS presta_id, ABS(SUM(stock_cant)) AS presta_cant, false as presta_compra,
		receta_paciente_id AS pac_id, 0 as inst_id, log_fecha::date AS presta_fecha, 0 as id_sigges, 'FARMACIA' AS porigen_nombre, 
		'' AS esp_desc,
		'00:00:00' AS nomd_hora, '' AS doc_rut, '' AS doc_paterno, '' AS doc_materno, '' AS doc_nombres, 
		'' AS diag_desc, '' AS nomd_diag_cod, '' AS nomd_tipo, '' AS nomd_extra
		
		FROM receta 
		JOIN recetas_detalle ON recetad_receta_id=receta_id
		JOIN logs ON log_recetad_id=recetad_id
		JOIN stock ON stock_log_id=log_id
		JOIN articulo ON stock_art_id=art_id
		WHERE receta_paciente_id=$pac_id
		GROUP BY art_glosa, art_codigo, receta_paciente_id, log_fecha

		) AS foo

		ORDER BY presta_fecha DESC		
		
		");
	else
		$presta=false;

	function img($t) {
    if($t=='t') return '<center><img src="iconos/tick.png" 
                                  width=8 height=8></center>';
    else return '<center><img src="iconos/cross.png" 
                                  width=8 height=8></center>';
	}

	function dibujar_fila($n,$l) {

    GLOBAL $listado, $presta;

    if($l%2==0) $clase='tabla_fila'; else $clase='tabla_fila2';

    switch($listado[$n]['detpat_uplazo']) {
      case 0: $up='min.'; break;
      case 1: $up='hr.'; break;
      case 2: $up='d&iacute;a(s)'; break;
      case 3: $up='mes(es)'; break;
      case -1: case -2: $up='';
    }

    if($listado[$n]['presta_codigo']=='') {
      $listado[$n]['detpat_plazo']='No Aplicable'; $up='';
    } else if($listado[$n]['detpat_uplazo']=='-2') 
      $listado[$n]['detpat_plazo']='Sin Plazo';
    else if($listado[$n]['detpat_plazo']=='0') 
      $listado[$n]['detpat_plazo']='*';

    $fnd=false;

    for($j=0;$j<count($presta);$j++) {

      if((!$presta[$j]['asigna']) 

          AND ($presta[$j]['presta_codigo']==$listado[$n]['presta_codigo'])) {

        $listado[$n]['presta_fecha']=$presta[$j]['presta_fecha'];

        $listado[$n]['intervalo']=$presta[$j]['intervalo'];

        $presta[$j]['asigna']=1;

        $fnd=true; break;

      }

    }

    if($fnd) {
      $prestacion=$listado[$n]['presta_fecha'];
    } else {
      $prestacion='<i>(n/a)</i>';
    }

    if($fnd)
    switch($listado[$n]['intervalo']) {
      case $listado[$n]['intervalo']<=3600: 
        $lapso=number_format($listado[$n]['intervalo']/60,2,',','.').' m'; break;
      case ($listado[$n]['intervalo']>3600 and $listado[$n]['intervalo']<=86400): 
        $lapso=number_format($listado[$n]['intervalo']/3600,2,',','.').' h'; break;
      default: 
        $lapso=number_format($listado[$n]['intervalo']/86400,2,',','.').' d'; break;
    } else
      $lapso='<i>(n/a)</i>';

    print('<tr class="'.$clase.'" id="detpat_'.$listado[$n]['detpat_id'].'"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\';"
    style="cursor:pointer;">
    <td>
    
		<table style="color:inherit;">
		<tr>'.str_repeat('<td style="width:15px;">&nbsp;</td>',$l).'
		<td style="font-size:12px;font-weight:bold;">
		'.$listado[$n]['presta_codigo'].'
		</td><td style="font-size:11px;">
		'.htmlentities(strtoupper($listado[$n]['glosa'])).'
		</td></tr>
		</table>
		
    </td>
    <td style="font-size:11px;"><center>
    '.$listado[$n]['detpat_plazo'].' '.$up.'</center></td>
    <td>'.img($listado[$n]['detpat_sigges']).'</td>
    <td style="text-align:center;">'.($prestacion).'</td>
    <td style="text-align:center;">'.$lapso.'</td>
    </tr>');

    for($i=0;$i<count($listado);$i++) {
      if($listado[$i]['detpat_padre_id']==$listado[$n]['detpat_id'])
        dibujar_fila($i,$l+1);
    }
    
  }




?>

<html>
<title>Registro de Monitoreo GES</title>

<?php cabecera_popup('../..'); ?>

<script>

var _acciones=[];
var listas=<?php echo json_encode($l); ?>;

abrir_ficha=function(pac_id) {

    win_pac = window.open('../../visualizar.php?pac_id='+pac_id,
    'proceso_ges', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=850,height=450,status=0,scrollbars=1');
			
    win_pac.focus();

}

	 historial_info=function(hosp_id) {
    	
      top=Math.round(screen.height/2)-200;
      left=Math.round(screen.width/2)-325;
        
      new_win = 
      window.open('../asignar_camas/historial_hosp.php?hosp_id='+hosp_id,
      'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=650, height=400, '+
      'top='+top+', left='+left);
        
      new_win.focus();
    	
	 }   	

	 imprimir_deis=function(hosp_id) {
    	
      window.open('../ingreso_egreso_hospital/imprimir_egreso.php?hosp_id='+hosp_id, '_self');
            	
	 }   	


guardar_monitoreo=function() {

    if($('monges_clase').value=='') {
		alert("Debe seleccionar validaci&oacute;n para el monitoreo v&aacute;lida.".unescapeHTML());
		return;
	}

	if($('monr_fecha_proxmon').value!='') {
		if(!validacion_fecha($('monr_fecha_proxmon'))) {
			alert("No ha ingresado fecha de pr&oacute;ximo monitoreo v&aacute;lida.".unescapeHTML());
			return;
		}
	}

	if($('monr_fecha_evento').value!='') {
		if(!validacion_fecha($('monr_fecha_evento'))) {
			alert("No ha ingresado fecha de evento v&aacute;lida.".unescapeHTML());
			return;
		}
	}

	//var params='&acciones='+encodeURIComponent(_acciones.toJSON());
	
	var myAjax=new Ajax.Request(
		'sql_monitoreo.php',
		{
			method: 'post',
			parameters: $('monitoreo_ges').serialize(),
			onComplete:function(r) {
				
				if(r.responseText=='') {
					alert("Monitoreo guardado exitosamente.");
					var fn=window.opener.listado_proceso.bind(window.opener);
					fn(); window.close();
				} else {
					alert('ERROR: '+r.responseText);
				}
				
			}
		}
	);
	
}

ver_registro_monitoreo=function() {
	
	 var myAjax=new Ajax.Updater(
                'listado_registro',
                '../../listas_dinamicas/visualizar_caso.php',
                {
                        method:'get',
                        parameters:'mon_id=<?php echo $mon_id; ?>&modo=1'
                }
        );

}

ver_hosp=function() {

  tab_down('tab_protocolo');
  tab_up('tab_hosp');
  tab_down('tab_prestaciones');
  tab_down('tab_documentos');

}

ver_protocolo=function() {

  tab_up('tab_protocolo');
  tab_down('tab_hosp');
  tab_down('tab_prestaciones');
  tab_down('tab_documentos');

}

ver_prestaciones=function() {

  tab_down('tab_protocolo');
  tab_down('tab_hosp');
  tab_up('tab_prestaciones');
  tab_down('tab_documentos');

}

ver_documentos=function() {

  tab_down('tab_protocolo');
  tab_down('tab_hosp');
  tab_down('tab_prestaciones');
  tab_up('tab_documentos');

}

	abrir_ic = function(id) {

		inter_ficha = window.open('../../interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

		inter_ficha.focus();

	}

	abrir_ipd = function(id) {

		inter_ficha = window.open('../../interconsultas/visualizar_ipd.php?ipd_id='+id,
		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

		inter_ficha.focus();

	}

	abrir_oa = function(id) {

		inter_ficha = window.open('../../interconsultas/visualizar_oa.php?oa_id='+id,
		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

		inter_ficha.focus();

	}

	abrir_presta = function(id) {

		inter_ficha = window.open('../visualizar_prestacion.php?presta_id='+id,
		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

		inter_ficha.focus();

	}

	eliminar_monr = function(monr_id) {

		if(!confirm('&iquest;Est&aacute; seguro que desea eliminar el registro? - Quedar&aacute; respaldado en tabla interna.'.unescapeHTML())) {
			return;
		}

		var myAjax=new Ajax.Request(
			'sql_eliminar_monitoreo.php',
			{
				method:'post',
				parameters:'monr_id='+monr_id,
				onComplete: function() {
					ver_registro_monitoreo();
				}
			}
		);

	}


</script>

<body class='fuente_por_defecto popup_background'>

<form id='monitoreo_ges' name='monitoreo_ges' onSubmit='return false;'>

<input type='hidden' id='mon_id' name='mon_id' value='<?php echo $mon_id; ?>' />

<table style='width:100%;font-size:14px;'>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>R.U.T.:</td>
<td class='tabla_fila' style='font-size:14px;' colspan=3><b><?php echo $m['mon_rut']; ?></b> [<i>Ficha:</i> <b><u><?php echo $pac['pac_ficha']; ?></u></b>]
<!--- <img src='../../iconos/magnifier.png' onClick='abrir_ficha(<?php echo $pac['pac_id']*1; ?>);' style='cursor:pointer;' /> -->
</td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre SIGGES:</td>
<td class='tabla_fila' style='font-weight:bold;'><?php echo $m['mon_nombre']; ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Edad:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['edad_anios'].' a '.$pac['edad_meses'].' m '.$pac['edad_dias'].' d '); ?></td>
</tr>

<!----<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre GIS:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim(($pac['pac_nombres']." ".$pac['pac_appat']." ".$pac['pac_apmat'])); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Nac./Def.:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_fc_nac'].' --- '.$pac['pac_fc_def']); ?></td>
</tr>--->

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre Reg. Civil:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila' id='nombre_regcivil'>
<input type='button' id='' name='' value='[Consultar Registro Civil...]' onClick='ver_registro_civil();' />
</td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Reg. Civil Nac./Def.:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila' id='fechas_regcivil'>
<input type='button' id='' name='' value='[Consultar Registro Civil...]' onClick='ver_registro_civil();' />
</td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Direcci&oacute;n:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_direccion']); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Ciudad:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['ciud_desc']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Tel&eacute;fono Fijo:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_fono']); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Tel&eacute;fono Celular:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_celular']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>email:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_mail']); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Previsi&oacute;n (Cert. FONASA):</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['prev_desc']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Patolog&iacute;a:</td>
<td class='tabla_fila' style='font-size:16px;' colspan=3><i><?php echo $m['mon_patologia']; ?></i></td>
</tr>
<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Garant&iacute;a:</td>
<td class='tabla_fila' style='font-size:16px;' colspan=3><i><?php echo $m['mon_garantia']; ?></i></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Inicio:</td>
<td class='tabla_fila' style='font-size:18px;'><?php echo $m['mon_fecha_inicio']; ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha L&iacute;mite:</td>
<td class='tabla_fila' style='font-size:18px;'><?php echo $m['mon_fecha_limite']; ?></td>
</tr>

<?php if($m['mon_estado_sigges']!='') { ?>

<tr>

<td class='tabla_fila2' style='width:15%;text-align:right;'>Estado SIGGES:</td>

<td class="tabla_fila" style="color:blue;font-size:14px;font-weight:bold;" colspan=3>
<?php 

	echo '<b>['.$m['mon_fecha_sigges'].']</b> '.$m['mon_estado_sigges']; 
	
	if($m['mon_causal_sigges']!='')
		echo ' <i>('.$m['mon_causal_sigges'].')</i>';
	
?>
</td>

</tr>

<?php } ?>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Estado Actual:</td>

<?php 
	
	switch($m['mon_condicion']) {
		case 0: echo '<td class="tabla_fila" style="color:blue;font-size:14px;" colspan=3>GARANTIA VIGENTE</td>'; break;
		case 1: echo '<td class="tabla_fila" style="color:red;font-size:14px;" colspan=3>GARANTIA VENCIDA</td>'; break;
		case 2: echo '<td class="tabla_fila" style="color:black;font-size:14px;" colspan=3>GARANTIA CERRADA</td>'; break;
	} 
	
?>
</tr>

</table>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_prestaciones' style='cursor: pointer;'
      onClick='ver_prestaciones();'>
      <img src='../../iconos/time.png'>
      Prestaciones</div>
	   </td><td>
		  <div class='tabs_fade' id='tab_hosp' style='cursor: pointer;'
      onClick='ver_hosp();'>
      <img src='../../iconos/building.png'>
      Hospitalizaciones</div>
	   </td><td>
		<div class='tabs_fade' id='tab_protocolo' style='cursor: default;' 
      onClick='ver_protocolo();'>
      <img src='../../iconos/chart_organisation.png'>
      Protocolo</div>
		</td><td>
		<div class='tabs_fade' id='tab_documentos' style='cursor: pointer;'
      onClick='ver_documentos();'>
      <img src='../../iconos/layout.png'>
      Documentos Asociados</div>
	   </td></tr>
      </table>



<div id='tab_protocolo_content'
class='tabbed_content' style='overflow:auto;height:200px;display:none;'>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>C&oacute;d. Prestaci&oacute;n</td>
<td>Plazo</td>
<td>SIGGES</td>
<td>Fecha Prestaci&oacute;n</td>
<td>Lapso Transcurrido</td>
</tr>

<?php  

	$etapas=array(0,0,0,0);

    for($etapa=0;$etapa<4;$etapa++) {

      print('<tr class="tabla_header" id="ges_etapa_'.$etapa.'"><td colspan=6 style="text-align:center;font-weight:bold;
              font-size:14px;">Etapa de ');
      
      switch($etapa) 
      {
        case 0: echo 'Sospecha'; break;
        case 1: echo 'Diagn&oacute;stico'; break;
        case 2: echo 'Tratamiento'; break;
        default: echo 'Seguimiento'; break;
      }

      print('</td></tr>');
     

      if($listado)
      for($i=0;$i<count($listado);$i++) {
        if($listado[$i]['detpat_padre_id']==0 AND $listado[$i]['detpat_etapa']==$etapa) {
          dibujar_fila($i,0); $etapas[$etapa]=1;
        }
      }

    }

?>

</table>

</div>

<div id='tab_hosp_content'
class='tabbed_content' style='overflow:auto;height:200px;display:none;'>


<table style='width:100%;font-size:14px;' class='lista_small celdas'>
<tr class='tabla_header' style='background-color:#cccccc;'>

<td style='width:30%;'><div align="center">Ubicaci&oacute;n</td>
<td style='width:5%;'><div align="center">Cama</td>
<td style='width:10%;'><div align="center">Condici&oacute;n del Paciente</td>
<td style='width:10%;'><div align="center">Fecha de Ingreso</td>
<td style='width:10%;'><div align="center">Fecha de Egreso</td>
<td style='width:35%;'><div align="center">Necesidades</td>
<td style='width:35%;'><div align="center">Historial</td>
<td style='width:35%;'><div align="center">DEIS</td>
</tr>


<?php

	$lista_h=cargar_registros_obj("
		SELECT *, COALESCE(hosp_cama_egreso, hosp_numero_cama) AS cama
		FROM hospitalizacion
		
		JOIN pacientes ON pac_rut='".$m['mon_rut']."' AND hosp_pac_id=pac_id
		LEFT JOIN tipo_camas ON
			cama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND cama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)
		LEFT JOIN clasifica_camas ON 
			tcama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND tcama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)
			
		ORDER BY hosp_fecha_egr DESC;
	", true);
	
	if($lista_h) {

	for($i=0;$i<sizeof($lista_h);$i++) {
		
		$hosp_id=$lista_h[$i]['hosp_id']*1;

		($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
		
		$estado='Pendiente';
		
		$ubica='<b>'.$lista_h[$i]['tcama_tipo'].'</b> / '.$lista_h[$i]['cama_tipo'].'';	

		$uval=pg_query("SELECT * FROM hospitalizacion_registro WHERE hosp_id = $hosp_id ORDER BY hreg_fecha DESC LIMIT 1;");
		
		if($v=pg_fetch_assoc($uval)) {
			$hcon_id=$v['hcon_id']*1; // idem
		} else {
			$hcon_id=1;
		}
		
		$tmp=cargar_registro("SELECT hcon_nombre FROM hospitalizacion_condicion WHERE hcon_id=$hcon_id;", true);
		
		$condicion=$tmp['condicion'];

		$tmp=cargar_registro("SELECT hospn_observacion FROM hospitalizacion_necesidades WHERE hosp_id=$hosp_id ORDER BY hospn_fecha DESC LIMIT 1;", true);
		
		$necesidades=$tmp['hospn_observacion'];
		
		if($condicion=='') $condicion='<i>----</i>';

		if($necesidades=='') $condicion='<i>(Sin Registros...)</i>';
		
		if($lista_h[$i]['hosp_fecha_egr']=='') {
			$lista_h[$i]['hosp_fecha_egr']='Vigente';
			$color='color:blue;';
		} else {
			$color='';
		}

		
    print("
    <tr class='$clase'>
	 <td style='font-weight:bold;text-align:left;font-size:12px;'>".$ubica."</td>    
	 <td style='font-weight:bold;text-align:right;font-size:14px;'>".(($lista_h[$i]['cama']*1-$lista_h[$i]['tcama_num_ini']*1)+1)."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:12px;'>".$condicion."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:14px;'>".substr($lista_h[$i]['hosp_fecha_ing'],0,16)."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:14px;$color'>".substr($lista_h[$i]['hosp_fecha_egr'],0,16)."</td>    
	 <td style='text-align:center;font-weight:bold;'>".($necesidades)."</td>
	 <td>
	<center><img src='../../iconos/report_magnify.png' style='cursor:pointer;'
	onClick='historial_info(".$hosp_id.");' /></center>			
	</td><td>
	<center><img src='../../iconos/layout.png' style='cursor:pointer;'
	onClick='imprimir_deis(".$hosp_id.");' /></center>			
	</td>
	</tr>
    ");    

		
	}

	} else {
		
	  $clase='#eeeeee';

	  print("
		<tr style='background-color:$clase;'>
		 <td style='font-weight:bold;text-align:center;font-size:16px;' colspan=8>(No tiene hospitalizaciones registradas...)</td>    
		</tr>
	  ");
	  
  }


?>

</table>

</div>

<div id='tab_prestaciones_content' class='tabbed_content' 
style='overflow:auto;height:200px;'>

&nbsp;

</div>

<div id='tab_documentos_content' class='tabbed_content' 
style='overflow:auto;height:200px;display:none;'>

&nbsp;

</div>



<?php if(pg_num_rows($nreg)>0) { ?>

<div class='sub-content'>
<img src='../../iconos/layout_edit.png' />
Gesti&oacute;n de Monitoreo
</div>

<div id='listado_registro' class='sub-content2' style=''>

<?php 

	$c=0;

	while($r=pg_fetch_assoc($nreg)) {
		
		$c!=$c; $clase=($c?'tabla_fila':'tabla_fila2');
		
		print("
			<tr class='$clase'>
			<td style='text-align:center;' valign='top'>".$r['monr_fecha']."<br>".htmlentities($r['func_nombre'])."</td>
			<td>".htmlentities($r['monr_observaciones'])."</td>
			<td></td>
			</tr>
		");
		
	}

?>

</div>

<?php } ?>


<?php if(_cax(55) OR $sin_monitorear) {	// IF PERFIL DE RECLASIFICAR... ?>

<div class='sub-content'>
<img src='../../iconos/layout_edit.png' />
<?php if($sin_monitorear) { // IF RECLASIFICAR?> 
	Agregar Informaci&oacute;n de Monitoreo GES
<?php } else { ?>
	RECLASIFICAR Registro de Monitoreo GES
<?php } // END IF RECLASIFICAR?>
</div>

<table style='width:100%;font-size:12px;'>

<tr>
<td class='tabla_fila2' style='width:30%;text-align:right;'>Condici&oacute;n (*):</td>
<td class='tabla_fila'>
<input type='text' id='monges_clase' name='monges_clase' value='' DISABLED size=10 style='text-align:center;font-size:10px;' />
<input type='text' id='monges_subclase' name='monges_subclase' value='' size=45 style='font-size:12px;' 
onDblClick='liberar_validacion();' />
<span id='subcondiciones'></span>
<br />
<!--<input type='text' id='monges_descripcion' name='monges_descripcion' value='' size=45 style='font-size:12px;' 
onDblClick='liberar_validacion();' />-->
</td>
</tr>


<tr>
<td class='tabla_fila2' style='width:30%;text-align:right;'>Bandeja de Destino:</td>
<td class='tabla_fila'>
<div id='bandeja_destino' style='font-size:16px;'>
<i>(Seleccione la Condici&oacute;n Actual...)</i>
</div>
<input type='hidden' id='id_condicion' name='id_condicion' value='' />
<input type='hidden' id='codigo_bandeja' name='codigo_bandeja' value='' />
</td>
</tr>

<tr id='tr_especialidad'>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Especialidad:</td>
<td><select id='mon_cod_especialidad' name='mon_cod_especialidad' style='width:250px;'>
<option value=''>(No especificada...)</option>
<?php 

  if($m['codigos_sel']!='')
    $w_cods="esp_cod_especialidad IN ('".str_replace(',',"','",$m['codigos_sel'])."')";
  else
    $w_cods="esp_cod_especialidad IS NOT NULL";

	$esp=cargar_registros_obj("
  	SELECT DISTINCT esp_cod_especialidad, esp_nombre_especialidad 
  	FROM especialidades WHERE $w_cods 
  	ORDER BY esp_nombre_especialidad;
	");
	
  if($esp)
	for($i=0;$i<sizeof($esp);$i++) {
	
		if($m['mon_cod_especialidad']==$esp[$i]['esp_cod_especialidad'])
			$sel='SELECTED';
		else
			$sel='';
	
		print("<option value='".$esp[$i]['esp_cod_especialidad']."' $sel >".$esp[$i]['esp_nombre_especialidad']."</option>");
		
	}
	
?>
</select></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:30%;text-align:right;'>Fecha Evento:</td>
<td class='tabla_fila'>
<input type='text' id='monr_fecha_evento' name='monr_fecha_evento' value='' 
onBlur='validacion_fecha(this);' size=10 style='font-size:12px;text-align:center;' />
</td>
</tr>

<tr>
<td class='tabla_fila2' style='width:30%;text-align:right;'>Fecha Prox. Monitoreo:</td>
<td class='tabla_fila'>
<input type='text' id='monr_fecha_proxmon' name='monr_fecha_proxmon' value='' 
onBlur='validacion_fecha(this);' size=10 style='font-size:12px;text-align:center;' />
</td>
</tr>

<tr>
<td class='tabla_fila2' style='width:30%;text-align:right;' valign='top'>Observaciones Generales:</td>
<td class='tabla_fila'>
<textarea id='mon_observaciones' name='mon_observaciones' value='' style='width:100%;height:50px;'>
</textarea>
</td>
</tr>

<!---<tr>
<td class='tabla_fila2' style='width:30%;text-align:right;'>Iniciar Proceso:</td>
<td class='tabla_fila'>
<select id='lista_id' name='lista_id' style='font-size:12px;'>
<option value='-1'>(Seleccionar Acci&oacute;n...)</option>
<option value='19'>Informe de Monitoreo - Atenci&oacute;n Abierta</option>
<option value='22'>Informe de Monitoreo - Atenci&oacute;n Cerrada</option>
</select>
</td>
</tr>--->

</table>

<center><br /><br />
<input type='button' id='' name='' value='Guardar Registro del Monitoreo...' onClick='guardar_monitoreo();' />
</center>

<?php }	// END IF PERFIL DE RECLASIFICAR... ?>


</form>

</body>
</html>

<script>
      
	ver_registro_monitoreo();
	
	<?php if($pac_id!=0) { ?>
	
	var myAjax=new Ajax.Updater(
	'tab_prestaciones_content',
	'listar_prestaciones.php',
	{
				method:'post',
				parameters:'pac_id=<?php echo $pac_id; ?>'
	}		
	);	

	var myAjax=new Ajax.Updater(
	'tab_documentos_content',
	'listar_documentos.php',
	{
				method:'post',
				parameters:'pac_id=<?php echo $pac_id; ?>'
	}		
	);	


	<?php } ?>

<?php if(_cax(55) OR $sin_monitorear) {	// IF PERFIL DE RECLASIFICAR... ?>

	validacion_fecha($('monr_fecha_proxmon'));
	validacion_fecha($('monr_fecha_evento'));


<?php if(_cax(55) AND !$sin_monitorear) { ?>

	seleccionar_validacion = function(d) {
		
		$('monges_clase').value='OK';
		$('monges_subclase').value=d[1].unescapeHTML();
		$('bandeja_destino').innerHTML=d[3];
		$('bandeja_destino').style.color='green';
		$('codigo_bandeja').value=d[4];
		$('id_condicion').value=d[0];
		
		var html='';
		
		if(d[5]!='') {
			
			var subcond=d[5].split('|');
			
			html='<select id="monr_subcondicion" name="monr_subcondicion">';
			
			for(var i=1;i<subcond.length;i++) {
				html+='<option value="'+subcond[i]+'">'+subcond[i]+'</option>';
			}
			
			html+='</select>';
			
		}
		
		$('subcondiciones').innerHTML=html;
		
	}
	
	liberar_validacion = function() {

		$('monges_clase').value='';
		$('monges_subclase').value='';
		$('bandeja_destino').innerHTML='<i>(Seleccione la Condici&oacute;n Actual...)</i>';
		$('bandeja_destino').style.color='';
		$('codigo_bandeja').value='';
		$('id_condicion').value='';
		//$('monges_descripcion').value='';
		
		$('subcondiciones').innerHTML='';
		
	}

	autocompletar_validaciones = new AutoComplete(
      'monges_subclase', 
      'autocompletar_sql.php',
      function() {
        if($('monges_subclase').value.length<2) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=bandejas&'+$('monges_subclase').serialize()
        }
      }, 'autocomplete', 550, 100, 150, 1, 4, seleccionar_validacion);

<?php }	else { ?>

	seleccionar_validacion = function(d) {
		
		$('monges_clase').value='OK';
		$('monges_subclase').value=d[1].unescapeHTML();
		$('bandeja_destino').innerHTML='(Directorio GES)';
		$('bandeja_destino').style.color='green';
		$('codigo_bandeja').value='';
		$('id_condicion').value=d[0];
		
		var html='';
		
		if(d[2]!='') {
			
			var subcond=d[2].split('|');
			
			html='<select id="monr_subcondicion" name="monr_subcondicion">';
			
			for(var i=1;i<subcond.length;i++) {
				html+='<option value="'+subcond[i]+'">'+subcond[i]+'</option>';
			}
			
			html+='</select>';
			
		}
		
		$('subcondiciones').innerHTML=html;
		
	}
	
	liberar_validacion = function() {

		$('monges_clase').value='';
		$('monges_subclase').value='';
		$('bandeja_destino').innerHTML='<i>(Seleccione la Condici&oacute;n Actual...)</i>';
		$('bandeja_destino').style.color='';
		$('codigo_bandeja').value='';
		$('id_condicion').value='';
		//$('monges_descripcion').value='';
		
		$('subcondiciones').innerHTML='';
		
	}

	autocompletar_validaciones = new AutoComplete(
      'monges_subclase', 
      'autocompletar_sql.php',
      function() {
        if($('monges_subclase').value.length<2) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=condiciones&'+$('monges_subclase').serialize()
        }
      }, 'autocomplete', 550, 100, 150, 1, 1, seleccionar_validacion);

<?php } ?>

<?php }	// END IF PERFIL DE RECLASIFICAR... ?>

/*
	autocompletar_validaciones2 = new AutoComplete(
      'monges_descripcion', 
      'autocompletar2_sql.php',
      function() {
		  
        if($('monges_descripcion').value.length<2) return false;
        if($('monges_subclase').value=='' || $('monges_clase').value=='') return false;
      
        return {
          method: 'get',
          parameters: $('monges_subclase').serialize()+'&'+$('monges_descripcion').serialize()
        }
      }, 'autocomplete', 450, 400, 150, 1, 1, seleccionar_validacion_detalle);
*/

	<?php 
	
		for($i=0;$i<sizeof($etapas);$i++) {
			if($etapas[$i]==0) echo "$('ges_etapa_".$i."').hide(); ";
		}
	
	?>
	
	ver_registro_civil=function() {
	
		$('nombre_regcivil').innerHTML="<img src='../../imagenes/ajax-loader1.gif'> Cargando...";
		$('fechas_regcivil').innerHTML="<img src='../../imagenes/ajax-loader1.gif'> Cargando...";
		
		var myAjax=new Ajax.Request(
			'registro_civil.php',
			{
				method:'get',
				parameters: 'rut=<?php echo $m['mon_rut']; ?>',
				onComplete:function(r) {
					
					var datos=r.responseText.evalJSON(true);
					
					datos[4]=datos[4].replace(/-/gi,'/');
					datos[5]=datos[5].replace(/-/gi,'/');
					datos[5]=datos[5].replace('//','');
					
					var nombre_regcivil=datos[0]+' '+datos[1]+' '+datos[2];
					nombre_regcivil = nombre_regcivil.replace(/@/gi, '&Ntilde;');
					
					$('nombre_regcivil').innerHTML=nombre_regcivil;
					$('fechas_regcivil').innerHTML=datos[4]+' --- '+datos[5];
									
				}
			}
		);
	
	}

</script>
