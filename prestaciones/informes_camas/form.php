<?php 
	require_once('../../conectar_db.php');
	$servhtml = desplegar_opciones_sql('SELECT * FROM centro_costo WHERE centro_hosp ORDER BY centro_nombre'); 
	$ccamas = cargar_registros_obj("SELECT * FROM clasifica_camas WHERE tcama_id>58 ORDER BY tcama_num_ini", true);
	$ccamas2 = cargar_registros_obj("SELECT * FROM tipo_camas ORDER BY cama_num_ini", true);
	$tcamas=cargar_registros_obj("SELECT * FROM tipo_camas ORDER BY cama_num_ini;", true);
	
	$htmlfuncionarios = desplegar_opciones_sql("SELECT DISTINCT func_id, (func_nombre) FROM funcionario 
	JOIN func_acceso USING (func_id) WHERE permiso_id=262 ORDER BY (func_nombre)", NULL, '', "font-style:italic;color:#555555;");
?>
<script>
	ccamas=<?php echo json_encode($ccamas) ?>;
	tcamas=<?php echo json_encode($ccamas2) ?>;
	
	select_ccamas = function(){
		var val=$('tcama_id').value;
		if(val=='-1') {
			$("ccama").innerHTML='';
			$("cama").innerHTML='';
			//$("imagen").innerHTML='';
		
			//$('pac_cama_tr').hide();
					
			//$('intercambio').value=0;
			//$('hosp_id2').value=0;
			return;
		}
	      
		var id=$('tcama_id').value.split(';');
		
		//console.log('ID:'+id);
		
		var tcama_id=id[0]*1;
		var tcama_num_ini=id[1]*1;
		var tcama_num_fin=id[2]*1;

		//console.log('tcama_id:'+id);
		//console.log('tcama_num_ini:'+tcama_num_ini);
		//console.log('tcama_num_fin:'+tcama_num_fin);
		
		html="<select id='ccama_id' name='ccama_id' onchange='select_camas();'><option value='-1'>(Seleccionar...)</option>";

		for (i=0;i<tcamas.length;i++){
			//console.log('tcama_num_ini:'+tcamas[i].cama_num_ini+' >= '+tcama_num_ini+' AND <= '+tcama_num_fin);
			//console.log('tcama_num_fin:'+tcamas[i].cama_num_fin);
			if(tcamas[i].cama_num_ini*1>=tcama_num_ini && tcamas[i].cama_num_ini*1<=tcama_num_fin) {
				html+="<option value='"+tcamas[i].cama_id+";"+tcamas[i].cama_num_ini+";"+tcamas[i].cama_num_fin+"'>"+tcamas[i].cama_tipo+"</option>";	
			}
		}
	
		html+="</select>";
		
		$("ccama").innerHTML=html;
		$("cama").innerHTML='';
		$("imagen").innerHTML='';
	}
	
	//PASA EL PRIMER CAMPO, MODIFICAR SERVICIO
	select_camas=function() {
		var val=$('ccama_id').value;
	   
		if(val=='-1') {
			$("cama").innerHTML='';
			$("imagen").innerHTML='';
			$('pac_cama_tr').hide();
			$('intercambio').value=0;
			$('hosp_id2').value=0;
			return;
		}
   
		var id=$('tcama_id').value.split(';');
		
		var tcama_id=id[0]*1;
		var tcama_num_ini=id[1]*1;
		var tcama_num_fin=id[2]*1;
   
		var id=$('ccama_id').value.split(';');
		
		var cama_id=id[0]*1;
		var cama_num_ini=id[1]*1;
		var cama_num_fin=id[2]*1;

		
		html="<select id='cama_id' name='cama_id' ><option value='-1'>(Seleccionar...)</option>";

		var j = 1;
		
		for (i=cama_num_ini;i<=cama_num_fin;i++){
			
			html+="<option value='"+i+"'>"+j+"</option>";	
			j++;
		}
	
		html+="</select>";
		
		$("cama").innerHTML=html;
		$("imagen").innerHTML='';

	}
	
	
	actualizar_campos=function() {
		var valor=$('tipo_informe').value*1;
	
		if(valor==1) {
		
			$('fecha_1').hide();
			$('fecha_2').hide();
			$('especialidad_tr').show();
			$('servicio_tr').show();
			$('rut_medico_tr').show();
			$('nombre_medico_tr').show();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').show();
			$('filtro_tr').show();
			$('tiempo_tr').show();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		
		} else if(valor==2) {

			$('fecha_1').hide();
			$('fecha_2').hide();
			$('especialidad_tr').show();
			$('servicio_tr').show();
			$('rut_medico_tr').show();
			$('nombre_medico_tr').show();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').show();
			$('filtro_tr').show();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==3) {

			$('fecha_1').hide();
			$('fecha_2').hide();
			$('especialidad_tr').show();
			$('servicio_tr').show();
			$('rut_medico_tr').show();
			$('nombre_medico_tr').show();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').show();
			$('filtro_tr').show();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==4) {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').show();
			$('servicio_tr').show();
			$('rut_medico_tr').show();
			$('nombre_medico_tr').show();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').show();
			$('dias_tr').show();
			$('filtro_tr').show();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==5) {

			$('fecha_1').hide();
			$('fecha_2').hide();
			$('especialidad_tr').show();
			$('servicio_tr').show();
			$('rut_medico_tr').show();
			$('nombre_medico_tr').show();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').show();
			$('filtro_tr').show();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==6) {

			$('fecha_1').hide();
			$('fecha_2').hide();
			$('especialidad_tr').hide();
			$('servicio_tr').show();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').show();
			$('filtro_tr').hide();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').show();
			$('modificar_tr').hide();
			$('tr_estado_cama').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==7 || valor==8 || valor==25 || valor==26) {
			$('fecha_1').show();
            $('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').show();
            $('rut_medico_tr').hide();
            $('nombre_medico_tr').hide();
            $('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
            $('procedencia_tr').hide();
            $('condicion_tr').hide();
            $('dias_tr').hide();
            $('filtro_tr').hide();
            $('tiempo_tr').hide();
            $('tipo_camas_tr').hide();
            $('modificar_tr').hide();
            $('tr_estado_cama').hide();
            $('imprimir_urgencia_pdf').hide();
            $('tr_modulos').hide();
            $('btn_printrecetas').hide();
            $('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==9) {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==10) {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').show();
			$('nombre_medico_tr').show();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==11) {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();

		} else if(valor==12 || valor==13 || valor==14 || valor==15) {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').show();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();

		} else if(valor==16) {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').show();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').show();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==17) {

			$('fecha_1').show();
			$('fecha_2').hide();
			$('especialidad_tr').hide();
			$('servicio_tr').show();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==18){
			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('modificar_tr').hide();
			$('tr_estado_cama').hide();
			$('filtro_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==19){
			$('fecha_1').show();
			$('fecha_2').show();
			$('filtro_tr').show();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').show();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==20){
			//INFORME HISTORIAL DE CAMAS
			$('fecha_1').hide();
			$('fecha_2').hide();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('modificar_tr').show();
			$('tr_estado_cama').show();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==22){
		
			//$('fecha_1').show();
			//$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').show();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('modificar_tr').hide();
			$('tr_estado_cama').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==23) {

			$('fecha_1').hide();
			$('fecha_2').hide();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').show();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').show();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').show();
			$('tr_modulos').show();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==24) {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('rut_medico_tr1').hide();
			$('nombre_medico_tr1').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		} else if(valor==27) {
			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').show();
			$('rut_medico_tr').show();
			$('nombre_medico_tr').show();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').show();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('tr_modulos').hide();
			
			$('tr_hora').show();
			$('btn_listar').hide();
			$('btn_excel').hide();
			$('imprimir_urgencia_pdf').hide();
			$('btn_printrecetas').show();
			$('filtro_tr').hide();
		} else {

			$('fecha_1').show();
			$('fecha_2').show();
			$('especialidad_tr').hide();
			$('servicio_tr').hide();
			$('rut_medico_tr').hide();
			$('nombre_medico_tr').hide();
			$('procedencia_tr').hide();
			$('condicion_tr').hide();
			$('dias_tr').hide();
			$('filtro_tr').hide();		
			$('tiempo_tr').hide();
			$('tipo_camas_tr').hide();
			$('tr_estado_cama').hide();
			$('modificar_tr').hide();
			$('imprimir_urgencia_pdf').hide();
			$('tr_modulos').hide();
			$('btn_printrecetas').hide();
			$('btn_listar').show();
			$('tr_hora').hide();
			$('btn_excel').show();
		}
	}

	listar_hosp=function() {
	
		$('listado').style.display='';
		$('listado').innerHTML='<br><img src="imagenes/ajax-loader2.gif"><br><br>';

		$('xls').value=0;
	
		var myAjax=new Ajax.Updater('listado','prestaciones/informes_camas/listado_camas.php',
		{
			method:'post',
			parameters: $('datos').serialize(),
			evalScripts: true
		});
	}

	descargar_xls=function() {
		$('xls').value=1;
		$('datos').method='post';
		$('datos').action='prestaciones/informes_camas/listado_camas.php';
		$('datos').submit();
	}	

	completa_info=function(hosp_id) {
		top=Math.round(screen.height/2)-200;
		left=Math.round(screen.width/2)-325;
		new_win = window.open('prestaciones/asignar_camas/informacion_hosp.php?hosp_id='+hosp_id,
		'win_camas', 'toolbar=no, location=no, directories=no, status=no, '+
		'menubar=no, scrollbars=yes, resizable=no, width=850, height=540, '+
		'top='+top+', left='+left);
		new_win.focus();
	}  
	 
	imprimir_urgencia_pdf1 = function(){
		if($('funcionarios').value=='-1' || $('nombre_medico').value=='' || $('nombre_medico1').value=='') {
			alert('Debe completar todos los campos para generar el archivo PDF.'.unescapeHTML());
			return;
		}
		window.open('prestaciones/informes_camas/imprimir_urgencia_pdf.php?doc_id='+$('doc_id').value*1+'&funcionarios='+$('funcionarios').value+'&doc_id1='+$('doc_id1').value*1+'&nombre_medico1='+$('nombre_medico1').value+'&nombre_medico='+$('nombre_medico').value , '_blank');
	}
	
	validacion_hora2 = function (obj)
    {
        var obj=$(obj);
        if(trim(obj.value)=='')
        {
            obj.value='';
            obj.style.background='skyblue';
            return true;
	}
        else
            return validacion_hora(obj);
    }
	
	print_recetas=function()
    {
        if(!validacion_hora2($('hora1')))
            {
                alert('Hora m&iacute;nima incorrecta.'.unescapeHTML());
		$('hora1').select();
		$('hora1').focus();
		return;
            }

            if(!validacion_hora2($('hora2')))
            {
                alert('Hora m&aacute;xima incorrecta.'.unescapeHTML());
		$('hora2').select();
		$('hora2').focus();
		return;
            }
        
        win = window.open('recetas/entregar_recetas/talonario.php?receta_numero=0&gestion_cama=1&'+$('datos').serialize(),'win_talonario');
        win.focus();
    }
    
    limpiar_paciente=function() {
		$('pac_rut').value='';
		$('paciente').value='';
		$('pac_id').value='0';
        //$('txt_paciente').value='';
	}
	
</script>
<center>
	<div class='sub-content' style='width:950px;'>
		<div class='sub-content'>
			<img src='iconos/table.png'>
			<b>Informes de Gesti&oacute;n Centralizada de Camas</b>
		</div>
		<div class='sub-content'>
			<form id='datos' name='datos' method="post" onSubmit='return false;'>
				<input type='hidden' id='xls' name='xls' value='0' />			
				<table style='width:100%;'>
					<tr>
						<td style='text-align:right;'>Tipo de Informe:</td>
						<td>
							<select id='tipo_informe' name='tipo_informe' onchange="actualizar_campos();">
								<option value='1' SELECTED>Pacientes en Espera de Camas</option>
                                <option value='2' >Pacientes Hospitalizados</option>
                                <option value='3' >Altas (Todos...)</option>
                                <option value='4' >Altas (Rango de Fechas)</option>
                                <option value='5' >Consulta O.I.R.S.</option>
                                <option value='6' >Camas Disponibles</option>
                                <option value='7' >Totales por Categor&iacute;a R-D (Por Servicio)</option>
								<option value='26' >Totales por Categor&iacute;a R-D (Por Servicio/Sala)</option>
                                <option value='8' >Totales por Categor&iacute;a R-D (Por D&iacute;a)</option>
								<option value='25'>Total Servicio/Sala Detallado Pacientes</option>
                                <option value='9' >Hospitalizados de Urgencias</option>
                                <option value='10' >Egresos de Pacientes por M&eacute;dico</option>
                                <option value='11' >Letalidad Hospitalaria</option>
                                <option value='12' >Promedio D&iacute;as de Estada</option>
                                <option value='13' >&Iacute;ndice Ocupacional</option>
                                <option value='14' >&Iacute;ndice de Rotaci&oacute;n</option>
                                <option value='15' >Intervalo de Sustituci&oacute;n</option>
                                <option value='16' >Ingreso Hospitalario</option>
                                <option value='17' >D&iacute;as Cama Disponibles</option>
                                <option value='18' >Ctas. Corrientes creadas (Rangos de Fechas)</option>
                                <option value='19' >Ctas. Corrientes (Por Pacientes)</option>
								<option value='20' >Historial de Camas</option>
								<option value='21' >Hospitalizaciones Anuladas</option>
								<option value='22' >Listado de Camas</option>
								<option value='23' >Informe Estado de Pacientes / Unidad de Emergencia</option>
								<option value='24'>Informe Hospitalizados Neonatolog&iacute;a</option>
								<option value='27' >Listado de Recetas Emitidas</option>
							</select>
						</td>
					</tr>
					<tr id='fecha_1'>
						<td style='text-align: right;'>Fecha Inicio:</td>
  						<td>
  							<input type='text' name='fecha1' id='fecha1' size=10
  							style='text-align: center;' value='<?php echo date("d/m/Y"); ?>' onBlur='validacion_fecha(this);'>
  							<img src='iconos/date_magnify.png' id='fecha1_boton'>
  						</td>
  					</tr>
  					<tr id='fecha_2'>
  						<td style='text-align: right;'>Fecha Final:</td>
  						<td><input type='text' name='fecha2' id='fecha2' size=10
  						style='text-align: center;' value='<?php echo date("d/m/Y"); ?>' onBlur='validacion_fecha(this);'>
  						<img src='iconos/date_magnify.png' id='fecha2_boton'>
  						</td>
  					</tr>
  					<tr id="tr_hora" style="display:none;">
						<td style='text-align:right;'>Hora:</td>
                        <td>
							Desde <input type='text' id='hora1' name='hora1' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;' />
							Hasta <input type='text' id='hora2' name='hora2' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;' />
						</td>
					</tr>
  					<tr id='tr_ccama' name='tr_ccama'></tr>
					<tr id='especialidad_tr'>
						<td id='tag_esp' style='text-align:right;'>(Sub)Especialidad:</td>
						<td>
							<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $r[0]['hosp_esp_id']*1; ?>'>
							<input type='text' id='especialidad'  name='especialidad' value='<?php echo $r[0]['esp_desc']; ?>' onDblClick='$("esp_id").value=""; $("especialidad").value="";' size=35>
						</td>
					</tr>
					<tr id='servicio_tr'>
						<td style='text-align:right;width:30%;'>Servicio:</td>
						<td>
							<?php
							$ccamas = cargar_registros_obj("SELECT * FROM clasifica_camas  WHERE tcama_id>58 ORDER BY tcama_num_ini", true);
							?>
							<select id='centro_ruta0' name='centro_ruta0'>
								<option value='' SELECTED>(Seleccione servicio de origen)</option>
								<?php 
								for($i=0;$i<sizeof($ccamas);$i++) {
									if($ccamas[$i]['tcama_id']==$fap[0]['centro_ruta']) $sel='SELECTED'; else $sel='';
									print("<option value='".$ccamas[$i]['tcama_id']."' $sel>".$ccamas[$i]['tcama_tipo']."</option>");
								}
								?>
							</select>
						</td>
					</tr>
					<!--AUTOCOMPLETAR MEDICO-->
					<tr id='rut_medico_tr'>
						<td style='text-align:right;'>Rut M&eacute;dico</td><!--RUT MEDICO-->
						<td>
							<input type='text' id='rut_medico' name='rut_medico' size=10
							style='text-align: center;' value='<?php echo $r[0]['doc_rut']; ?>' readOnly>
						</td>
					</tr>
					<tr id='nombre_medico_tr'>
						<td style='text-align:right;'>M&eacute;dico:</td>
						<td>
							<input type='hidden' id='doc_id' name='doc_id' value='<?php echo $r[0]['hosp_doc_id']; ?>'>
							<input type='text' id='nombre_medico' name='nombre_medico' size=35 onDblClick='$("rut_medico").value="";$("doc_id").value="";$("nombre_medico").value="";' value='<?php echo trim($r[0]['doc_paterno'].' '.$r[0]['doc_materno'].' '.$r[0]['doc_nombres']); ?>' />
						</td>
					</tr>
					<!--CIERRE AUTOCOMPLETAR MEDICO-->
  					<!--AUTOCOMPLETAR TRAUMATOLOGO-->
					<tr id='rut_medico_tr1'>
						<td style='text-align:right;'>Rut M&eacute;dico</td>
						<td>
							<input type='text' id='rut_medico1' name='rut_medico1' size=10 style='text-align: center;' value='<?php echo $r[0]['doc_rut']; ?>' readOnly>
						</td>
					</tr>
					<tr id='nombre_medico_tr1'>
						<td style='text-align:right;'>Traumat&oacute;logo:</td>
						<td>
							<input type='hidden' id='doc_id1' name='doc_id1' value='<?php echo $r[0]['hosp_doc_id']; ?>'>
							<input type='text' id='nombre_medico1' name='nombre_medico1' size=35 onDblClick='$("rut_medico1").value="";$("doc_id1").value="";$("nombre_medico1").value="";' value='<?php echo trim($r[0]['doc_paterno'].' '.$r[0]['doc_materno'].' '.$r[0]['doc_nombres']); ?>' />
						</td>
					</tr>
					<!--CIERRE AUTOCOMPLETAR TRAUMATOLOGO-->
  					<!--ENFERMERAS URGENCIA-->
  					<tr id='tr_modulos'><td style='text-align:right;'>Enfermero(a):</td>
						<td>
							<select id='funcionarios' name='funcionarios'>
								<option value='-1'>(Seleccione...)</option>
								<?php echo $htmlfuncionarios; ?>
							</select>
						</td>
					</tr>
  					<!--ENFERMERAS URGENCIA-->
  					<tr id='procedencia_tr'>
						<td style='text-align:right;'>Procedencia de Ingreso:</td>
						<td>
							<select id='procedencia' name='procedencia'>
								<option value="-1" SELECTED>(Cualquiera...)</option>
								<option value='0'>U. Emergencia Adulto (UEA)</option>
								<option value='1'>U. Emergencia Infantil (UEI)</option>
								<option value='2'>U. Emergencia Maternal (UEGO)</option>
								<option value='4'>Obstetricia y Ginecolog&iacute;a</option>
								<option value='5'>Hospitalizaci&oacute;n</option>
								<option value='6'>Atenci&oacute;n Ambulatoria</option>
								<option value='3'>Otro Hospital</option>
							</select>
						</td>
					</tr>
					<!-- AGREGA LOS CAMPOS DINAMICOS AL FORMULARIO --> 
					<tr id='modificar_tr' name='modificar_tr'>
						<td style='text-align:right;white-space:no-wrap;' >Modificar Serv. / Sala / Cama:</td>
						<td>
							<span id='tcama' name='tcama'></span>
							<span id='ccama' name='ccama'></span>
							<span id='cama' name='cama'></span>
							<span id='imagen' name='imagen'></span>
						</td>
					</tr>
					<tr id='tr_estado_cama'>
						<td style='text-align:right;white-space:no-wrap;'>Estado Cama:</td>
						<td><span id='estado_cama'></span></td>
					</tr>
					<tr id='condicion_tr'>
						<td style='text-align:right;'>Condici&oacute;n Egreso:</td>
						<td>
							<select id='condicion_egreso' name='condicion_egreso'>
								<option value="0" SELECTED>(Cualquiera...)</option>
								<option value="1">Alta a Domicilio</option>
								<option value="2">Derivaci&oacute;n</option>
								<option value="3">Fallecido</option>
								<option value="4">Fugado</option>
								<option value="5">Otro...</option>
							</select>
						</td>
					</tr>
					<tr id='dias_tr'>
						<td style='text-align:right;'>Dias Hospitalizado:</td>
						<td>
							desde <input type='text' id='dias_desde' name='dias_desde' size=5 value='' style='text-align:right;' />
							hasta <input type='text' id='dias_hasta' name='dias_hasta' size=5 value='' style='text-align:right;' />
						</td>
					</tr>
					<tr id='filtro_tr'>
						<td style='text-align:right;'>Buscar Paciente:</td>
						<td>
							<input type='text' id='filtro' name='filtro' size=40 value='' />&nbsp;&nbsp;(Por: RUT, Ficha, Nombre o Apellidos)
						</td>
					</tr>
					<tr id='auto_paciente_tr'>
						<td style='text-align:right;'>RUT Paciente:</td>
						<td>
							<input type='hidden' id='pac_id' name='pac_id' value='0' />
                            <input type='text' size=20 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' />
                            <input type='text' id='paciente' name='paciente' style='text-align:left;' DISABLED size=45 />
                        </td>
					</tr>
					<tr id='tiempo_tr'>
						<td style='text-align:right;'>Tiempo Esperando:</td>
						<td>
							<select id='tiempo_espera' name='tiempo_espera'>
								<option value='0'>(Todos...)</option>
								<option value='1'>00-12 hrs.</option>
								<option value='2'>12-24 hrs.</option>
								<option value='3'>24-48 hrs.</option>
							</select>
						</td>
					</tr>
					<tr id='tipo_camas_tr'>
						<td style='text-align:right;'>Tipo:</td>
						<td>
							<select id='tipo_camas' name='tipo_camas'>
								<option value='0'>(Todos...)</option>
								<option value='1'>Hospitalizado</option>
								<option value='2'>Ambulatorio</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan=2>
							<center>
								<input type='button' id="btn_listar" onClick='listar_hosp();' value='-- Actualizar Listado... --'>
								<input type='button' id='imprimir_urgencia_pdf' onClick='imprimir_urgencia_pdf1();' value='-- Descargar PDF... --'>
								<input type='button' id="btn_excel"  onClick='descargar_xls();' value='-- Obtener Archivo XLS... --'>
								<input id="btn_printrecetas" type='button' onClick='print_recetas();' value='-- Imprimir recetas Emitidas... --' style="display: none;">
								&nbsp;&nbsp;&nbsp;&nbsp;Cantidad de Registros: <span id='cant_registros' style='font-weight:bold;'>0</span>
							</center>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div class='sub-content2' style='height:400px;overflow:auto;' id='listado'>
		</div>
	</div>
</center>
<script>
	Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });
	
	html="<select id='tcama_id' name='tcama_id' onchange='select_ccamas();'><option value='-1'>(Sin Movimiento...)</option>";
	for (i=0;i<ccamas.length;i++){
		html+="<option value='"+ccamas[i].tcama_id+";"+ccamas[i].tcama_num_ini+";"+ccamas[i].tcama_num_fin+"'>"+ccamas[i].tcama_tipo+"</option>";	
	}
	
	html+="</select>";
	
	$("tcama").innerHTML=html;

    ingreso_especialidades=function(datos_esp) {
      $('esp_id').value=datos_esp[0];
      $('especialidad').value=datos_esp[2].unescapeHTML();
    }
      
    autocompletar_especialidades = new AutoComplete(
      'especialidad', 
      'autocompletar_gcamas.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=especialidad_subespecialidad&esp_desc='+encodeURIComponent($('especialidad').value)
        }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);

    /**seleccionar_serv2 = function(d) {

	$('centro_ruta0').value=d[0].unescapeHTML();
	$('servicios0').value=d[2].unescapeHTML(); 

    }*/

    /**autocompletar_servicios2 = new AutoComplete(
      'servicios0', 
      'autocompletar_sql.php',
      function() {
        if($('servicios0').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hospitalizacion&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);*/

      
      //AUTOCOMPLETAR MEDICO
      ingreso_rut=function(datos_medico) {
      	$('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);
	
	 //CIERRE AUTOCOMPLETAR MEDICO


	 //AUTOCOMPLETAR TRAUMATOLOGO
      ingreso_rut1=function(datos_medico1) {
      	$('doc_id1').value=datos_medico1[3];
      	$('rut_medico1').value=datos_medico1[1];
      }

      autocompletar_medicos1 = new AutoComplete(
      'nombre_medico1', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico1').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos1&'+$('nombre_medico1').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut1);
      
      
      
	seleccionar_paciente = function(d)
    {
		$('pac_rut').value=d[0];
		$('paciente').value=d[2].unescapeHTML();
		$('pac_id').value=d[5];
	}

    autocompletar_pacientes = new AutoComplete(
	'pac_rut', 
    'autocompletar_sql.php',
    function() {
	if($('pac_rut').value.length<2) return false;
    return {
		method: 'get',
        parameters: 'tipo=pacientes_edad&nompac='+encodeURIComponent($('pac_rut').value)
	}
	}, 'autocomplete', 500, 200, 150, 1, 4, seleccionar_paciente);
	
	actualizar_campos();
</script>
  
  
  
  
