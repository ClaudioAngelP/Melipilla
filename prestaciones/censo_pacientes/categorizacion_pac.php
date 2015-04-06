<?php	require_once('../../conectar_db.php');
	
	$tipo=$_GET['tipo'];
	$back=$_GET['back'];
	
	if($tipo==1){
		$censo_id=$_GET['censo_id'];
		$censo=cargar_registro("SELECT * FROM censo_diario WHERE censo_id=".$censo_id.";");
		$sel=explode(';',$censo['censo_riesgodependencia']);
		$hosp_id=$censo['hosp_id'];
		$hist=1;
	}else{
		$hosp_id=$_GET['hosp_id'];
		$hist=0;
	}
	
	$hosp=cargar_registro("SELECT * FROM (
			SELECT *, h1.hosp_fecha_ing::date AS hosp_fecha_ingreso, 
			h1.hosp_id AS id, COALESCE((
				SELECT ptras_cama_destino 
				FROM paciente_traslado AS p1
				WHERE p1.hosp_id=h1.hosp_id 
				ORDER BY ptras_fecha DESC, ptras_id DESC
				LIMIT 1
			),hosp_numero_cama) AS cama_censo
			FROM hospitalizacion AS h1
			WHERE h1.hosp_id=$hosp_id
			) AS foo
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN censo_diario ON 
				censo_diario.hosp_id=foo.hosp_id 
			LEFT JOIN tipo_camas ON
				cama_num_ini<=cama_censo AND cama_num_fin>=cama_censo
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=cama_censo AND tcama_num_fin>=cama_censo
		ORDER BY cama_censo, foo.hosp_fecha_ing	");
?>
<html>
	<title>Categorizaci&oacute;n de Paciente</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php cabecera_popup('../../'); ?>
<script>

calcular_dependencia = function (){

	var total=0;
	var dep=[];
	for(var i=1;i<7;i++){
		if($('sel'+i).value!=''){
			dep=($('sel'+i).value).split('|');
			
			total+=(dep[0]*1);
		}
	}

	$('total_dep').value=total;
	$('lbl_dep').innerHTML=total;
	
	$('cat').value='';
	$('lbl_cat').innerHTML='';
}

calcular_riesgo = function (){

	var total=0;
	var ries=[];
	for(var i=7;i<15;i++){
		if($('sel'+i).value!=''){
			ries=($('sel'+i).value).split('|');
			total+=(ries[0]*1);
		}
	}

	$('total_ries').value=total;
	$('lbl_ries').innerHTML=total;
	
	$('cat').value='';
	$('lbl_cat').innerHTML='';
}

calcular_categorizacion = function(){
	
		var seleccionados='';
			
		for(var i=1;i<15;i++){
			if($('sel'+i).value==''){
				alert('Debe seleccionar...');
				$('sel'+i).focus();
				return;
			}else{
					//var sel=($('sel'+i).value).split('|');
					//seleccionados+=sel[1]+'|';
				seleccionados+=$('sel'+i).value+';';
			}
		}
		
		if($('total_dep').value>=0 && $('total_dep').value<=6){
			depen='3';
		}else if ($('total_dep').value>=7 && $('total_dep').value<=12){
			depen='2';
		}else if ($('total_dep').value>12){
			depen='1';
		}
		
		if($('total_ries').value>=0 && $('total_ries').value<=5){
			riesgo='D';
		}else if ($('total_ries').value>=6 && $('total_ries').value<=11){
			riesgo='C';
		}else if ($('total_ries').value>=12 && $('total_ries').value<=18){
			riesgo='B';
		}else if ($('total_ries').value>18){
			riesgo='A';
		}
		
		$('cat').value=seleccionados.slice(0,-1);
		$('lbl_cat').innerHTML=riesgo+depen;
	
}

seleccionar_cat = function(){
	
	var hosp=<?php echo $hosp_id; ?>;
	if($('cat').value==''){
		alert('Debe calcular la categorizaci&oacute;n'.unescapeHTML());
		return;
	}
	
	var firstSelect = document.getElementById('clase_'+hosp),
    optionsHTML = [];
	optionsHTML.push("<option value='"+$('lbl_cat').innerHTML+"'>"+$('lbl_cat').innerHTML+"</option>");
	firstSelect.innerHTML = optionsHTML.join('\n');
	$('clase_'+hosp).value=$('lbl_cat').innerHTML;
	$('sel_'+hosp).value=$('cat').value;
	$('win_cat').win_obj.close();
	
}
</script>     
<body class='fuente_por_defecto popup_background'> 
<form name='categorizacion' id='categorizacion' >
	<div class='sub-content2'>
		<div class='sub-content'>
			<!--<center>-->
			<table width='70%'>
				<tr>
					<td class='tabla_fila' width='10%'>R.U.T.:</td><td class='tabla_fila2'><b><label id='rut' name='rut'><?php echo htmlentities($hosp['pac_rut']); ?></label></b></td>
					<td class='tabla_fila' width='15%'>Paciente:</td><td class='tabla_fila2'><b><label id='pac' name='pac'><?php echo ($hosp['pac_nombres'].' '.$hosp['pac_appat'].' '.$hosp['pac_apmat']); ?></label></b></td>
				</tr>
				<tr>
					<td class='tabla_fila'>Ficha:</td><td class='tabla_fila2'><b><label id='ficha' name='ficha'><?php echo htmlentities($hosp['pac_ficha']); ?></label></b></td>
					<td class='tabla_fila'>Ubicaci&oacute;n:</td>
					<td class='tabla_fila2'><b><?php echo ($hosp['tcama_tipo']).' / '.($hosp['cama_tipo']).'</b> <i>Cama:</i> <b>['.($hosp['hosp_numero_cama']*1-$hosp['cama_num_ini']*1+1).']'; ?></b></td>
				</tr>
			</table>	
			<!--</center>-->
		</div>
		<div class='sub-content'>
			<center>
				<table><tr><td>
					<div>
						<table width='450'>
				<tr>
					<td colspan=2 class='tabla_fila' style='text-align:center;'><b>Cuidados de Dependencia</b></td>
				</tr>
				<tr>
					<td class='tabla_fila2' width='65%'
					title='Cambio de ropa de cama y/o personal, o Cambio de pañales, o toallas o apósitos higiénicos'>
						1.- Cuidados en confort y Bienestar (Higiene)</td>
					<td class='tabla_fila'>
						<select id='sel1' name='sel1' style="width:150px" onChange='calcular_dependencia();'>
								<option value=''>(Seleccione...)</option>
								<option value='3|1' <?php if($sel[0]=='3|1') echo 'SELECTED'; ?>>* Usuario receptor de los cuidados básicos,requeridos 3 veces al día o más(con/sin participación de familia)</option>
								<option value='2|2' <?php if($sel[0]=='2|2') echo 'SELECTED'; ?>>* Usuario receptor de los cuidados básicos 2 veces al día (con/sin participación de familia)</option>
								<option value='1|3' <?php if($sel[0]=='1|3') echo 'SELECTED'; ?>>* Usuario y familia realizan estos cuidados con ayuda y supervisión,cualquiera sea la frecuencia</option>
								<option value='0|4' <?php if($sel[0]=='0|4') echo 'SELECTED'; ?>>* Usuario realiza solo el auto cuidado de cambio de ropa o cambio de pañal,toallas o apósitos higiénicos</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Movilización y transporte (levantada,deambulación,cambio posición)'>
						2.- Cuidados en confort y Bienestar (Movilizaci&oacute;n)</td>
					<td class='tabla_fila'>
						<select id='sel2' name='sel2' style="width:150px" onChange='calcular_dependencia();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[1]=='3|1') echo 'SELECTED'; ?>>* Usuario no se levanta y requiere de cambio de posición en cama,10 o más veces al día con/sin</option>
							<option value='2|2' <?php if($sel[1]=='2|2') echo 'SELECTED'; ?>>* Usuario es levantado a silla y requiere de cambio de posición, entre 4 a 9 veces al día con/sin partic. De familia</option>
							<option value='1|3' <?php if($sel[1]=='1|3') echo 'SELECTED'; ?>>* Usuario se levanta y deambula con ayuda y se cambia de posición en cama,solo o con ayuda de la familia</option>
							<option value='0|4' <?php if($sel[1]=='0|4') echo 'SELECTED'; ?>>* Usuario deambula sin ayuda y se moviliza solo en la cama</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Oral,Enteral o Parenteral'>
						3.- Cuidados de Alimentación</td>
					<td class='tabla_fila'>
						<select id='sel3' name='sel3' style="width:150px" onChange='calcular_dependencia();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[2]=='3|1') echo 'SELECTED'; ?>>* Usuario recibe alimentación y/o hidratación x vía parenteral total/parcial o requiere control por ayuno prolongado</option>
							<option value='3|2' <?php if($sel[2]=='3|2') echo 'SELECTED'; ?>>* Usuario recibe alimentación por vía enteral permanente o discontinua(con/sin participación de la familia)</option>
							<option value='2|3' <?php if($sel[2]=='2|3') echo 'SELECTED'; ?>>* Usuario recibe alimentación x vía oral,la que es administrada (con/sin participación de la familia)</option>
							<option value='1|4' <?php if($sel[2]=='1|4') echo 'SELECTED'; ?>>* Usuario se alimenta por vía oral o enteral, con ayuda y supervisión</option>
							<option value='0|5' <?php if($sel[2]=='0|5') echo 'SELECTED'; ?>>* Usuario se alimenta sin ayuda</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Orina, Deposiciones'>
						4.- Cuidados de Eliminación</td>
					<td class='tabla_fila'>
						<select id='sel4' name='sel4' style="width:150px" onChange='calcular_dependencia();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[3]=='3|1') echo 'SELECTED'; ?>>* Usuario elimina egreso por sonda, prótesis,procedim.dialiticos,colectores adhesivos o pañales</option>
							<option value='2|2' <?php if($sel[3]=='2|2') echo 'SELECTED'; ?>>* Usuario elimina egresos por vía natural y se le entregan o colocan al usuario los colectores(chata,pato)</option>
							<option value='1|3' <?php if($sel[3]=='1|3') echo 'SELECTED'; ?>>* Usuario y familia realizan recolección de egresos con ayuda y supervisión</option>
							<option value='0|4' <?php if($sel[3]=='0|4') echo 'SELECTED'; ?>>* Usuario usa colectores (chata,pato) sin ayuda y/o usa WC</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Usuario Receptivo,angustiado,Triste,Agresivo,Evasivo'>
						5.- Apoyo Psicosocial y Emocional</td>
					<td class='tabla_fila'>
						<select id='sel5' name='sel5' style="width:150px" onChange='calcular_dependencia();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[4]=='3|1') echo 'SELECTED'; ?>>* Usuario recibe más de 30 minutos de apoyo durante turno(conversar,acompañar,escuchar,tomar en brazos)</option>
							<option value='2|2' <?php if($sel[4]=='2|2') echo 'SELECTED'; ?>>* Usuario recibe entre 15 y 30 min. de apoyo durante turno(conversar,acompañar,escuchar,tomar en brazos)</option>
							<option value='1|3' <?php if($sel[4]=='1|3') echo 'SELECTED'; ?>>* Usuario recibe entre 5 y 14 min. de apoyo durante turno(conversar,acompañar,escuchar,tomar en brazos)</option>
							<option value='0|4' <?php if($sel[4]=='0|4') echo 'SELECTED'; ?>>* Usuario recibe menos de 5 min. de apoyo durante turno(conversar,acompañar,escuchar,tomar en brazos)</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Por alteración conciencia, riesgo caída o riesgo incidente(desplazamiento,retiro vías,sondas,tubos),Limitación física o por edad o de sentidos'>
						6.- Vigilancia</td>
					<td class='tabla_fila'>
						<select id='sel6' name='sel6' style="width:150px" onChange='calcular_dependencia();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[5]=='3|1') echo 'SELECTED'; ?>>* Usuario con alteración de conciencia y/o conducta insegura (desorientado,confuso,excitado,agresivo)</option>
							<option value='3|2' <?php if($sel[5]=='3|2') echo 'SELECTED'; ?>>* Usuario con con riesgo de caída o de incidentes(limitación física o cognoscitiva y/o con > 70 á y < 2á)</option>
							<option value='2|3' <?php if($sel[5]=='2|3') echo 'SELECTED'; ?>>* Usuario conciente pero intranquilo y c/ riesgo caída o incidente(bajo efectos de fármacos, con 1 o más)</option>
							<option value='1|4' <?php if($sel[5]=='1|4') echo 'SELECTED'; ?>>* Usuario conciente pero con inestabilidad de la marcha o no camina por reposo, por edad o alteración física y/o conducta insegura (desorientado,confuso,excitado,agresivo)</option>
							<option value='0|5' <?php if($sel[5]=='0|5') echo 'SELECTED'; ?>>* Usuario conciente ,orientado,autónomo</option>
						</select>
					</td>
				</tr>
				<tr class='tabla_fila'>
					<td style='text-align:right;'>Total Dependencia:</td>
					<td><input type='hidden' id='total_dep' name='total_dep' size=10>
					<b><label id='lbl_dep' name='lbl_dep'></label></b></td>
				</tr>
			</table>
					</div>
				</td><td>
					<div>
			<table width='450'>
				<tr>
					<td colspan=2 class='tabla_fila' style='text-align:center;'><b>Cuidados de Riesgo</b></td>
				</tr>
				<tr>
					<td class='tabla_fila2' width='65%'
					title='(2 o mas parámetros simultáneos): PA, Tº, Frec. Cardiaca,Frec. Cardiaca fetal,Frec. Respiratoria,nivel de dolor y otros.'>
						7.- Medición diaria de Signos Vitales</td>
					<td class='tabla_fila'>
						<select id='sel7' name='sel7' style="width:150px" onChange='calcular_riesgo();'>
								<option value=''>(Seleccione...)</option>
								<option value='3|1' <?php if($sel[6]=='3|1') echo 'SELECTED'; ?>>* Control por 8 veces y más(cada 3 horas o más frecuente * </option>
								<option value='2|2' <?php if($sel[6]=='2|2') echo 'SELECTED'; ?>>* Control por 4 a 7 veces (cada 4,5,6 o 7 horas)</option>
								<option value='1|3' <?php if($sel[6]=='1|3') echo 'SELECTED'; ?>>* Control por 2 a 3 veces (cada 8,9,10,11 o 12 horas)</option>
								<option value='0|4' <?php if($sel[6]=='0|4') echo 'SELECTED'; ?>>* Control por 1 vez (cada 13 a cada 24 horas)</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Medición de Ingresos y  Egresos realizado por profesionales'>
						8.- Balance Hídrico</td>
					<td class='tabla_fila'>
						<select id='sel8' name='sel8' style="width:150px" onChange='calcular_riesgo();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[7]=='3|1') echo 'SELECTED'; ?>>* Balance hídrico por 6 veces o más (cada 4 horas o más frecuente)</option>
							<option value='2|2' <?php if($sel[7]=='2|2') echo 'SELECTED'; ?>>* Balance hídrico por 2 a 5 veces (cada 12,8,6 o 5 horas)</option>
							<option value='1|3' <?php if($sel[7]=='1|3') echo 'SELECTED'; ?>>* Balance hídrico por 1 vez (cada 24 horas o menor  de cada 12 horas)</option>
							<option value='0|4' <?php if($sel[7]=='0|4') echo 'SELECTED'; ?>>* No requiere</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Por cánula de  traqueostomía,tubo endotraqueal,cámara,halo,máscara,sonda o bigotera'>
						9.- Cuidados en Oxigenoterapia</td>
					<td class='tabla_fila'>
						<select id='sel9' name='sel9' style="width:150px" onChange='calcular_riesgo();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[8]=='3|1') echo 'SELECTED'; ?>>* Administración de oxígeno por tubo y cánula endotraqueal y/o con VMI y VMNI permanente</option>
							<option value='2|2' <?php if($sel[8]=='2|2') echo 'SELECTED'; ?>>* Administración de oxígeno por halo,mascara,incubadora y/o con VMNI permanente</option>
							<option value='1|3' <?php if($sel[8]=='1|3') echo 'SELECTED'; ?>>* Administración de oxígeno con bigotera</option>
							<option value='0|4' <?php if($sel[8]=='0|4') echo 'SELECTED'; ?>>* Sin Oxigenoterapia</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Aspiración de Secreciones y Apoyo Kinésico requerido'>
						10.- Cuidados diarios de la Vía Aérea</td>
					<td class='tabla_fila'>
						<select id='sel10' name='sel10' style="width:150px" onChange='calcular_riesgo();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[9]=='3|1') echo 'SELECTED'; ?>>* Usuario con vía aérea artificial ( tubo o cánula endotraqueal )</option>
							<option value='3|2' <?php if($sel[9]=='3|2') echo 'SELECTED'; ?>>* Usuario con vía aérea artif.o natural con 4 o + aspiraciones secreciones traqueales y/o apoyo kinésico + de 4</option>
							<option value='2|3' <?php if($sel[9]=='2|3') echo 'SELECTED'; ?>>* Usuario respira por vía natural y requiere de 1 a 3 aspiración de secreciones y/o apoyo kinésico 2 o 3 veces al día</option>
							<option value='1|4' <?php if($sel[9]=='1|4') echo 'SELECTED'; ?>>* Usuario respira por vía natural,sin aspiración de secreciones y/o apoyo kinésico 1 vez al día</option>
							<option value='0|5' <?php if($sel[9]=='0|5') echo 'SELECTED'; ?>>* Usuario no requiere de apoyo ventilatorio adicional</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Intervenciones quirúrgicas y Procedimientos invasivos tales como punciones,toma de muestras, instalaciones de vías,sondas y tubos, etc'>
						11.- Intervenciones Profesionales</td>
					<td class='tabla_fila'>
						<select id='sel11' name='sel11' style="width:150px" onChange='calcular_riesgo();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[10]=='3|1') echo 'SELECTED'; ?>>* 1 o más procedimientos Invasivos realizados médicos en últimas 24 horas</option>
							<option value='3|2' <?php if($sel[10]=='3|2') echo 'SELECTED'; ?>>* 3 o más procedimientos Invasivos realizados por enfermera/matrona en últimas 24 horas</option>
							<option value='2|3' <?php if($sel[10]=='2|3') echo 'SELECTED'; ?>>* 1 o 2  procedimientos Invasivos realizados por enfermera/matrona en últimas 24 horas</option>
							<option value='2|4' <?php if($sel[10]=='2|4') echo 'SELECTED'; ?>>* 1 o más procedimientos Invasivos realizados por profesionales  en últimas 24 horas</option>
							<option value='0|5' <?php if($sel[10]=='0|5') echo 'SELECTED'; ?>>* No se realizan procedimientos Invasivos en 24 horas</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Prevención de lesiones de la piel y curaciones o refuerzo de apósitos'>
						12.- Cuidados de Piel y Curaciones</td>
					<td class='tabla_fila'>
						<select id='sel12' name='sel12' style="width:150px" onChange='calcular_riesgo();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[11]=='3|1') echo 'SELECTED'; ?>>* Curación o refuerzo de apósitos 3 o más veces en el día,independiente de la complejidad de la técnica empleada</option>
							<option value='2|2' <?php if($sel[11]=='2|2') echo 'SELECTED'; ?>>* Curación o refuerzo de apósitos 1 a 2 veces en el día,independiente de la complejidad de la técnica empleada</option>
							<option value='2|3' <?php if($sel[11]=='2|3') echo 'SELECTED'; ?>>* Prevención compleja de lesiones de la piel:uso de colchon antiescaras, piel de cordero u otro</option>
							<option value='1|4' <?php if($sel[11]=='1|4') echo 'SELECTED'; ?>>* Prevención corriente de lesiones : aseo,lubricación y proteccion de zonas propensas</option>
							<option value='0|5' <?php if($sel[11]=='0|5') echo 'SELECTED'; ?>>* No requiere</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Por vía inyectable EV,Intratecal y por vías tales como oral, ocular, aérea,rectal,vaginal,etc'>
						13.- Administración de Tratamiento Farmacológico</td>
					<td class='tabla_fila'>
						<select id='sel13' name='sel13' style="width:150px" onChange='calcular_riesgo();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[12]=='3|1') echo 'SELECTED'; ?>>* Tratamiento intratecal e inyectable endovenoso,directo o por fleboclisis</option>
							<option value='3|2' <?php if($sel[12]=='3|2') echo 'SELECTED'; ?>>* Tratamiento diario con 5 o más fármacos distintos,administrados por diferentes vías no inyectable</option>
							<option value='2|3' <?php if($sel[12]=='2|3') echo 'SELECTED'; ?>>* Tratamiento inyectable no endovenoso (IM,SC,ID)</option>
							<option value='2|4' <?php if($sel[12]=='2|4') echo 'SELECTED'; ?>>* Tratamiento diario con 2 a 4 fármacos ,administrados por diferentes vías no inyectable</option>
							<option value='1|5' <?php if($sel[12]=='2|5') echo 'SELECTED'; ?>>* Tratamiento con 1  fármaco ,administrado por diferentes vías no inyectable</option>
							<option value='0|6' <?php if($sel[12]=='0|6') echo 'SELECTED'; ?>>* Sin  tratamiento farmácológico</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tabla_fila2' 
					title='Cateteres y vías vasculares centrales, periféricos y arteriales. Manejo de sondas urinarias y digestivas a permanencia.Manejo de drenajes intracavitáreos o percutáneos'>
						14.- Presencia de elementos Invasivos</td>
					<td class='tabla_fila'>
						<select id='sel14' name='sel14' style="width:150px" onChange='calcular_riesgo();'>
							<option value=''>(Seleccione...)</option>
							<option value='3|1' <?php if($sel[13]=='3|1') echo 'SELECTED'; ?>>* Con 3 o más elementos invasivos (sondas,drenajes,cateteres o vías vasculares)</option>
							<option value='2|2' <?php if($sel[13]=='2|2') echo 'SELECTED'; ?>>* Con 1 a 2 elementos invasivos(sonda,drenaje,vía arterial,cateter o vía venosa central)</option>
							<option value='2|3' <?php if($sel[13]=='2|3') echo 'SELECTED'; ?>>* Con 2 o más vías venosas periféricas (mariposas,teflones,agujas)</option>
							<option value='1|4' <?php if($sel[13]=='1|4') echo 'SELECTED'; ?>>* Con 1 vía venosa periférica (mariposas,teflones,agujas)</option>
							<option value='0|5' <?php if($sel[13]=='0|5') echo 'SELECTED'; ?>>* Sin elementos invasivos</option>
						</select>
					</td>
				</tr>
				<tr class='tabla_fila'>
					<td style='text-align:right;'>Total Riesgo:</td>
					<td><input type='hidden' id='total_ries' name='total_ries' size=10 >
						<b><label id='lbl_ries' name='lbl_ries'></label></b></td></td>
				</tr>
			</table>
				</div>
				</td>
			</tr>
			</table>			
			</center>
		</div>
		<div class='sub-content'>
			<center>
				<table>
					<tr class='tabla_fila'>
						<td style='text-align:right;'>Resultado Riesgo-Dependencia (Categorizaci&oacute;n):</td>
						<td>[&nbsp;<input type='hidden' id='cat' name='cat' size=10>
							<b><label id='lbl_cat' name='lbl_cat'></label></b>&nbsp;]
						</td><td>
							<input type='button' value='Calcular' onClick='calcular_categorizacion();'></td>
					</tr>
					<tr>
<?php if($back!=''){ ?>
						<td colspan=3 style='text-align:center;'><input type='button' value='Volver' onClick='javascript:history.back()'></td>
<?php }else{ ?>
						<td colspan=3 style='text-align:center;'><input type='button' value='Categorizar Paciente' onClick='seleccionar_cat();'></td>
<?php } ?>
					</tr>
				</table>
			</center>
		</div>
	</div>
</form>
<?php if($hist==1) {?>
<script>
calcular_riesgo();
calcular_dependencia();
calcular_categorizacion();
</script>
<?php } ?>
</body>
</html>
