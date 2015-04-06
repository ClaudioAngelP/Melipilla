<?php 

	require_once('../fpdf/fpdf.php');

	function carta_header($pdf) {
		
			$pdf->SetFont('Arial','B', 10);

			$pdf->Image('../imagenes/logotipo_grande.jpg',17,10,30,25);
			
			$pdf->Ln(25);
			$pdf->Cell(45,5,utf8_decode('Gobierno de Chile'),0,0,'C');	
			$pdf->Ln();
			$pdf->Cell(45,5,utf8_decode('Ministerio de Salud'),0,0,'C');	
			$pdf->Ln();
			$pdf->Cell(45,5,utf8_decode('Hospital Dr. Gustavo Fricke'),0,0,'C');
			$pdf->Ln();
			$pdf->Cell(45,5,utf8_decode('Coordinación AUGE'),0,0,'C');
			$pdf->Ln();
		
			$pdf->SetFontSize(10);		
			$pdf->SetY(75);	

		
	}


	function excepcion_header($pdf) {
		
			$pdf->SetFont('Arial','B', 8);

			$pdf->Ln(15);
			$pdf->Cell(45,5,utf8_decode('Gobierno de Chile'),0,0,'C');	
			$pdf->Ln();
			$pdf->Cell(45,5,utf8_decode('Ministerio de Salud'),0,0,'C');	
			$pdf->Ln();
			$pdf->Cell(45,5,utf8_decode('DEPARTAMENTO DE ESTADÍSTICAS E'),0,0,'C');
			$pdf->Ln();
			$pdf->Cell(45,5,utf8_decode('INFORMACIÓN DE SALUD'),0,0,'C');
			$pdf->Ln();
		
			$pdf->SetFontSize(10);		
			$pdf->SetY(45);	

		
	}


	$vmes[1]='Enero';
	$vmes[2]='Febrero';
	$vmes[3]='Marzo';
	$vmes[4]='Abril';
	$vmes[5]='Mayo';
	$vmes[6]='Junio';
	$vmes[7]='Julio';
	$vmes[8]='Agosto';
	$vmes[9]='Septiembre';
	$vmes[10]='Octubre';
	$vmes[11]='Noviembre';
	$vmes[12]='Diciembre';


	class PDF extends FPDF {
		function header() {

		
		}

		function footer() {

			$this->SetY(335);
			$this->SetFont('','',10);
			//$this->Cell(200,6,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'C');
			
		}

	}	
	
	$pdf=new PDF('P','mm','Legal');
	$pdf->AliasNbPages();
	
	$pdf->SetAutoPageBreak(true,20);
	
	
	for($j=0;$j<2;$j++) {

		// Nóminas...

		$pdf->AddPage('L');

		carta_header($pdf);

		$pdf->SetFont('Arial','', 9);

		$pdf->Cell(330,5,utf8_decode('Nómina de Correos a Enviar'),1,1,'C');
		
		$pdf->Cell(20,5,'FICHA',1,0,'C');
		$pdf->Cell(20,5,'RUT',1,0,'C');
		$pdf->Cell(110,5,'Nombre Completo',1,0,'C');
		$pdf->Cell(110,5,utf8_decode('Dirección'),1,0,'C');
		$pdf->Cell(30,5,utf8_decode('Ciudad'),1,0,'C');
		$pdf->Cell(40,5,'Problema Salud',1,1,'C');

		for($i=0;$i<sizeof($lista);$i++) {

			$tmp=explode('-',$lista[$i]['mon_rut']);
			
			$rut=''.number_format($tmp[0],0,',','.').'-'.$tmp[1];
			
			$pdf->SetFont('Arial','', 8);
			$pdf->Cell(20,5,$lista[$i]['pac_ficha'],1,0,'C');
			$pdf->Cell(20,5,$rut,1,0,'R');
			$pdf->Cell(110,5,$lista[$i]['mon_nombre'],1,0,'L');
			$pdf->SetFont('Arial','', 6);
			$pdf->Cell(110,5,$lista[$i]['pac_direccion'],1,0,'L');
			$pdf->Cell(30,5,$lista[$i]['ciud_desc'],1,0,'L');
			$pdf->Cell(40,5,$lista[$i]['pst_patologia_interna'],1,1,'L');
	

		}
		
		$pdf->SetFont('Arial','', 12);
		
		// CARTAS y EXCEPCIONES
		
		for($i=0;$i<sizeof($lista);$i++) {
		
			$tmp=explode('-',$lista[$i]['mon_rut']);
			
			$rut=''.number_format($tmp[0],0,',','.').'-'.$tmp[1];
			
			
			
			// CREACIÓN DE DOS CARTAS, UNA PARA EL PACIENTE Y OTRA PARA SU FICHA...
				
				$pdf->AddPage();

				carta_header($pdf);
				
				$pdf->SetFillColor(200,200,200);	

				$pdf->SetFont('Arial','', 12);
				
				$pdf->SetXY(150,200);
				
				$pdf->Cell(40,5,'Ficha: '.$lista[$i]['pac_ficha'],0,0,'R');
				$pdf->Ln();

				$pdf->SetFont('Arial','B', 12);
				
				$pdf->SetXY(60,20);
				
				$pdf->Cell(90,5,'Sr(a): '.$lista[$i]['mon_nombre'],0,0,'L');
				$pdf->Ln();
				
				$pdf->SetFont('Arial','B', 10);
				
				$pdf->SetX(60);
				$pdf->Cell(90,5,utf8_decode("Dirección: ").$lista[$i]['pac_direccion'],0,0,'L');
				$pdf->Ln();
				$pdf->SetX(60);
				$pdf->Cell(90,5,utf8_decode("Comuna: ").$lista[$i]['ciud_desc'],0,0,'L');
				$pdf->Ln();

				$pdf->SetY(75);	

				$pdf->SetFont('Arial','B', 12);
				
				$pdf->Cell(90,10,'Sr(a): '.$lista[$i]['mon_nombre'],0,0,'L');
				$pdf->Ln();
				$pdf->Cell(90,10,'R.U.T.: '.$rut,0,0,'L');
				$pdf->Ln();

				
				$pdf->SetFont('Arial','', 12);
				
				if($lista[$i]['id_condicion']!='48') {
				
					$pdf->Image('carta_ges_firma.jpg',70,140,110,45);
					
					$pdf->Multicell(200,5,str_replace('<br>',"\n",str_replace("\n",' ',utf8_decode('
					Junto con saludarle, informamos a usted que según nuestros registros se encuentra con una
					atención de salud pendiente en el marco de las Garantías Explícitas de Salud (AUGE).<br><br>
					Debido a que no hemos podido ubicarlo telefónicamente, solicitamos a usted acercarse a Secretaría AUGE, ubicada
					en Coordinación GES(al costado del Consultorio de Especialidades) de nuestro establecimiento, en un plazo no mayor a 20 días a contar de la fecha de emisión de esta carta,
					en horario de 8:30 hrs. a 12:00 hrs.; para poder entregar la atención de salud requerida.<br><br>
					Ante cualquier duda, contáctese al teléfono (32) 2577770 en horario de 12:00 hrs. a 16:00 hrs.<br><br>
					Saluda atentamente a ud;<br><br>
					'))));
					
				} else {
					
					$pdf->Image('carta_ges_firma.jpg',70,130,110,45);

					$pdf->Multicell(200,5,str_replace('<br>',"\n",str_replace("\n",' ',utf8_decode('
					Junto con saludarle, informamos a usted que según registros de FONASA se encuentra con su 
					CERTIFICACIÓN PREVISIONAL BLOQUEADA.<br><br>
					Para poder seguir entregando a usted las atenciones médicas requeridas, solicitamos regularizar
					a la brevedad posible esta situación en su consultorio más cercano o en las oficinas de FONASA.<br><br>
					Ante cualquier duda, contáctese al teléfono (32) 2577770.<br><br>
					Saluda atentamente a ud;<br><br>
					'))));
				}

				$pdf->Ln(30);
				
					
				$pdf->Cell(90,10,utf8_decode('Viña del Mar, '.date('d').' de '.$vmes[date('m')*1].' de '.date('Y')),0,0,'L');	
			
			
			
			// CREACIÓN DE LA EXCEPCIÓN DE GARANTÍA...
			
			if($j==1) {
			
				$pdf->AddPage();
				
				excepcion_header($pdf);
				
				$pdf->SetFillColor(200,200,200);	

				$pdf->SetFont('Arial','', 10);
				
				$pdf->Cell(200,7,utf8_decode('Justificación de la no realización o postergación de una prestación'),'LRT',0,'C');
				$pdf->Ln();
				$pdf->Cell(200,7,utf8_decode('(Excepción de Garantía)'),'LR',0,'C');
				$pdf->Ln();
				
				$pdf->Cell(50,7,utf8_decode('Folio Nº ___________'),'LB',0,'C');

				$f1=explode('/',substr($lista[$i]['monr_fecha_evento'],0,10));
				$ff1=($f1[2].''.$f1[1].''.$f1[0])*1;
				$ff2=date('Ymd')*1;

				if($ff1>$ff2)
					$pdf->Cell(100,7,utf8_decode('FECHA: '.(substr($lista[$i]['monr_fecha'],0,10))),'B',0,'C');
				else
					$pdf->Cell(100,7,utf8_decode('FECHA: '.($lista[$i]['monr_fecha_evento']==''?substr($lista[$i]['monr_fecha'],0,10):substr($lista[$i]['monr_fecha_evento'],0,10))),'B',0,'C');


				$pdf->Cell(50,7,utf8_decode('Hora: 10:00'),'RB',0,'C');
				$pdf->Ln();

				$pdf->Cell(75,7,utf8_decode('1. Servicio de Salud'),1,0,'L');
				$pdf->Cell(125,7,utf8_decode('2. Establecimiento'),1,0,'L');
				$pdf->Ln();
				$pdf->Cell(75,7,utf8_decode('Servicio de Salud Viña del Mar - Quillota'),1,0,'L');
				$pdf->Cell(125,7,utf8_decode('Hospital Dr. Gustavo Fricke'),1,0,'L');
				$pdf->Ln();

				$pdf->Cell(200,7,utf8_decode('3. Especialidad'),'LRT',0,'L');
				$pdf->Ln();
				$pdf->Cell(200,7,utf8_decode(''),'LRB',0,'L');
				$pdf->Ln();


				$pdf->Cell(75,7,utf8_decode('DATOS DEL (DE LA) PACIENTE'),'LRB',0,'L');
				$pdf->Cell(125,7,utf8_decode('4. Historia Clínica: '.$lista[$i]['pac_ficha']),'R',0,'C');
				$pdf->Ln();

				$pdf->Cell(50,7,utf8_decode('5. Nombre'),'L',0,'L');
				if($lista[$i]['pac_appat']!='') {
					$pdf->Cell(50,7,($lista[$i]['pac_appat']),'B',0,'C');
					$pdf->Cell(50,7,($lista[$i]['pac_apmat']),'B',0,'C');
					$pdf->Cell(50,7,($lista[$i]['pac_nombres']),'BR',0,'C');
				} else {
					list($apellidos,$nombre)=explode(',',$lista[$i]['mon_nombre']);
					$pdf->Cell(100,7,trim($apellidos),'B',0,'C');
                                        $pdf->Cell(50,7,trim($nombre),'BR',0,'C');
				}

				$pdf->Ln();

				$pdf->Cell(50,7,utf8_decode(''),'L',0,'L');
				$pdf->Cell(50,7,"Apellido Paterno",0,0,'C');
				$pdf->Cell(50,7,"Apellido Materno",0,0,'C');
				$pdf->Cell(50,7,"Nombres",'R',0,'C');
				$pdf->Ln();

				$pdf->Cell(50,7,utf8_decode('6. RUT: ').$rut,'LB',0,'L');
				$pdf->Cell(50,7,"7. Sexo(Marcar con X): ".$lista[$i]['sex_id'],'B',0,'C');
				$pdf->Cell(100,7,utf8_decode("8. Teléfono: ".$lista[$i]['pac_fono']),'BR',0,'C');
				$pdf->Ln();

				$pdf->SetFont('Arial','', 8);
				
				$pdf->Cell(100,7,utf8_decode('9. Problema de Salud AUGE'),'LBR',0,'L');
				$pdf->Cell(100,7,utf8_decode("10. Diagnóstico(anote el(los) diagnostico(s) con letra legible y sin siglas):"),'LBR',0,'C');
				$pdf->Ln();
			
				$pdf->Cell(100,7,$lista[$i]['pst_patologia_interna'],'LR',0,'L');
				$pdf->Cell(100,7,utf8_decode("__________________________________________________"),'LR',0,'C');
				$pdf->Ln();
				
				$pdf->Cell(100,7,'Subproblema',1,0,'L');
				$pdf->Cell(100,7,utf8_decode("__________________________________________________"),'LR',0,'C');
				$pdf->Ln();

				$pdf->Cell(100,7,$lista[$i]['mon_garantia'],'LBR',0,'L');
				$pdf->Cell(100,7,utf8_decode("__________________________________________________"),'LBR',0,'C');
				$pdf->Ln();

				$pdf->SetFont('Arial','', 10);
				
				$pdf->Cell(200,7,utf8_decode('11. CAUSAL DE LA EXCEPCIÓN'),'LR',0,'L');
				$pdf->Ln();
				$pdf->Cell(200,7,utf8_decode('(Marcar causa con X)'),'LR',0,'L');
				$pdf->Ln();
				$_x=$pdf->GetX();
				$_y=$pdf->GetY();
				
				
				$x=array('','','','','','');
				$dias_nsp='';
				$fecha_nsp='';
				$causa='';
				
				switch($lista[$i]['id_condicion']) {
					case '8': $x[2]='X'; $dias_nsp='1'; $fecha_nsp=$lista[$i]['monr_fecha_evento']; break;
					case '9': $x[2]='X'; $dias_nsp='2'; $fecha_nsp=$lista[$i]['monr_fecha_evento']; break;
					case '34': $x[4]='X'; break;
					case '35': $x[4]='X'; break;
					default: $x[5]='X'; $causa=utf8_decode($lista[$i]['nombre_condicion']); break;
				}
				
				
				$pdf->SetXY(15,165);
				$pdf->Cell(15,5,utf8_decode('11.1 Decisión del Profesional Tratante:'),0,0,'L');
				
				$pdf->SetXY(30,170);
				$pdf->Cell(15,5,'11.1.1',0,0,'C');
				$pdf->Cell(5,5,$x[0],1,0,'C');
				$pdf->Cell(50,5,utf8_decode('Criterios de Exclusión(según protocolos).'),0,0,'L');
				
				$pdf->SetXY(30,175);
				$pdf->Cell(15,5,'11.1.2',0,0,'C');
				$pdf->Cell(5,5,$x[1],1,0,'C');
				$pdf->Cell(50,5,utf8_decode('Indicación Médica.'),0,0,'L');
				
				$pdf->SetXY(15,180);
				$pdf->Cell(15,5,'11.2 Causas atribuibles al paciente o sus representantes:',0,0,'L');
				
				$pdf->SetXY(30,185);
				$pdf->Cell(15,5,'11.2.1',0,0,'C');
				$pdf->Cell(5,5,$x[2],1,0,'C');
				$pdf->Cell(50,5,utf8_decode('Inasistencia.'),0,0,'L');
				
				$pdf->SetXY(60,190);
				$pdf->Cell(50,5,utf8_decode('Días de Inasistencia'),0,0,'L');
				$pdf->Cell(15,5,utf8_decode($dias_nsp),1,0,'C');
				$pdf->Cell(50,5,utf8_decode('Fecha Inasistencia'),0,0,'L');
				$pdf->Cell(30,5,utf8_decode($fecha_nsp),1,0,'C');
				
				$pdf->SetXY(30,195);
				$pdf->Cell(15,5,'11.2.2',0,0,'C');
				$pdf->Cell(5,5,$x[3],1,0,'C');
				$pdf->Cell(50,5,utf8_decode('Rechazo del prestador designado.'),0,0,'L');
				
				$pdf->SetXY(30,200);
				$pdf->Cell(15,5,'11.2.3',0,0,'C');
				$pdf->Cell(5,5,$x[4],1,0,'C');
				$pdf->Cell(50,5,utf8_decode('Rechazo de la atención o procedimiento garantizado.'),0,0,'L');
				
				$pdf->SetXY(60,205);
				$pdf->Cell(20,5,utf8_decode('Código'),0,0,'C');
				$pdf->Cell(20,5,utf8_decode(''),1,0,'L');
				$pdf->Cell(25,5,utf8_decode('Descripción'),0,0,'C');
				$pdf->Cell(70,5,utf8_decode(''),1,0,'L');
				
				$pdf->SetXY(30,210);
				$pdf->Cell(15,5,'11.2.4',0,0,'C');
				$pdf->Cell(5,5,$x[5],1,0,'C');
				$pdf->Cell(50,5,utf8_decode('Otra causa definida por el paciente: (explicitar).'),0,0,'L');
				$pdf->SetXY(60,215);
				$pdf->Cell(140,5,$causa,'B',0,'L');
				
				$pdf->SetXY($_x,$_y);
				$pdf->Cell(200,80,'',1,1,'L');
				
				
				$pdf->Cell(200,7,utf8_decode('12. OBSERVACIONES: (complementar selección anterior)'),1,1,'L');
				
				$_y=$pdf->GetY();

				if($lista[$i]['observa']=='') {
					if(trim($lista[$i]['pac_fono'].' '.$lista[$i]['pac_celular'])!='')
						$lista[$i]['observa']='SE LLAMA AL PACIENTE EL '.substr($lista[$i]['monr_fecha'],0,16).' HRS. A LOS FONOS '.trim($lista[$i]['pac_fono'].' '.$lista[$i]['pac_celular']).' RESULTANDO  '.strtoupper($lista[$i]['nombre_condicion']).'.'; 
					else
						$lista[$i]['observa']='SE INTENTA LLAMAR AL PACIENTE EL '.substr($lista[$i]['monr_fecha'],0,16).' HRS. SIN FONOS DISPONIBLES EN SISTEMAS, RESULTANDO  '.strtoupper($lista[$i]['nombre_condicion']).'.';
				}

				$pdf->Multicell(200,5,$lista[$i]['observa'],1,'J');

				$pdf->SetY($_y);

				$pdf->Cell(200,5,'',1,1,'L');
				$pdf->Cell(200,5,'',1,1,'L');
				$pdf->Cell(200,5,'',1,1,'L');
				$pdf->Cell(200,5,'',1,1,'L');
				$pdf->Cell(200,5,'',1,1,'L');
				$pdf->Cell(200,5,'',1,1,'L');
				$pdf->Cell(200,5,'',1,1,'L');


				$pdf->Image('carta_ges_firma_excepcion.jpg',150,300,50,30);

				$pdf->Cell(200,7,utf8_decode('DATOS DEL RESPONSABLE: (profesional tratante; paciente o su representante.)'),1,0,'L');
				$pdf->Ln();

				$pdf->Cell(50,7,utf8_decode('13. Nombre'),'L',0,'L');
				$pdf->Cell(50,7,'FUENZALIDA',0,0,'C');
				$pdf->Cell(50,7,'PACHECO',0,0,'C');
				$pdf->Cell(50,7,utf8_decode('PILAR ANGÉLICA'),'R',0,'C');
				$pdf->Ln();

				$pdf->Cell(50,7,utf8_decode(''),'L',0,'L');
				$pdf->Cell(50,7,"Apellido Paterno",'T',0,'C');
				$pdf->Cell(50,7,"Apellido Materno",'T',0,'C');
				$pdf->Cell(50,7,"Nombres",'TR',0,'C');
				$pdf->Ln();

				$pdf->Cell(50,7,utf8_decode('14. RUT: 7.557.069-4'),'L',0,'L');
				$pdf->Cell(50,7,"",0,0,'C');
				$pdf->Cell(50,7,"Firma:",0,0,'C');
				$pdf->Cell(50,7,"",'R',0,'C');
				$pdf->Ln();

				$pdf->Cell(200,7,utf8_decode('ESTE DOCUMENTO SE DEBE ANEXAR A LA HISTORIA CLÍNICA'),1,0,'L');
				$pdf->Ln();
			
			}
		
		}
	
	}
	

	$pdf->Output('/tmp/cartas_y_excepciones.pdf','F');

?>
