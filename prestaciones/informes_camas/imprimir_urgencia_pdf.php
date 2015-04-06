<?php

	require_once('../../fpdf/fpdf.php');
	require_once('../../conectar_db.php');
	
	$medico=$_GET['nombre_medico'];
	$id_medico=$_GET['doc_id'];
	$medico1=$_GET['nombre_medico1'];
	$id_medico1=$_GET['doc_id1'];
	$funcionarios=$_GET['funcionarios'];
			
		$funcionario_query=cargar_registro("SELECT func_nombre FROM funcionario WHERE func_id=$funcionarios;");
		$func_nombre=$funcionario_query['func_nombre'];
		
			
			
			function trunc($str, $len) {
		if(strlen($str)>$len)
			return substr($str,0,$len).'...';
		else
			return $str;
	}
		
		
		
		function dinero($num) {
		GLOBAL $xls;
		if(!$xls) return (number_format($num,0,',','.').'');
		else			return floor($num*1);
	}	
	
	function numero($num) {
		GLOBAL $xls;
		if(!$xls) return (number_format($num,0,',','.'));
		else			return floor($num*1);
	}
	
	//$fecha_hora=("SELECT EXTRACT(YEAR FROM TIMESTAMP 'now') as anio");
	
		
	$q=cargar_registros_obj("
			SELECT * FROM (
			SELECT *,
			pac_nombres || ' ' || pac_appat || ' ' || pac_apmat AS nombre_completo,
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			--cama_id,
			(CURRENT_DATE-COALESCE(hosp_fecha_ing,hosp_fecha_hospitalizacion)::date) AS dias_espera,
			EXTRACT(HOUR FROM (CURRENT_DATE-COALESCE(hosp_fecha_ing,hosp_fecha_hospitalizacion)::time)) AS horas_espera,
			t1.tcama_tipo AS tcama_tipo, t1.tcama_num_ini AS tcama_num_ini,
			t2.tcama_tipo AS servicio,hosp_id,
			date_part('year',age( pac_fc_nac ))AS edad_paciente,
			cama_num_ini , cama_num_fin,hosp_condicion_egr,
			COALESCE(diag_desc, hosp_diagnostico) as diag_desc,
				(SELECT hcon_nombre 
				FROM hospitalizacion_registro 
				LEFT JOIN hospitalizacion_condicion using (hcon_id) 
				WHERE hosp_id=h1.hosp_id 
				ORDER BY hreg_fecha desc limit 1) as hcon_nombre1
				FROM hospitalizacion h1
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod
			LEFT JOIN tipo_camas ON
			cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas AS t1 ON 
			t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas AS t2 ON 
			t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
			--LEFT JOIN hospitalizacion_registro USING (hosp_id)
			--LEFT JOIN hospitalizacion_condicion USING (hcon_id)
			WHERE hosp_anulado!=1 AND t1.tcama_id=77 AND hosp_numero_cama>0 AND hosp_fecha_egr IS NULL
		) AS foo ORDER BY hosp_numero_cama" );
	
	function dibujar_encabezado_tabla($pdf,$query,$q,$id_medico,$medico,$id_medico1,$medico1,$func_nombre){

		$pdf->SetFont('Arial','B',7);
		$pdf->Ln(5);
		$pdf->Cell(15, 10, utf8_decode('Cta. Cte'),1,0,'C');
		$pdf->Cell(20, 10, utf8_decode('RUT'),1,0,'C');
		$pdf->Cell(35, 10, utf8_decode('Nombre'),1,0,'C');
		$pdf->Cell(10, 10, utf8_decode('Edad'),1,0,'C');
		$pdf->Cell(35, 10, utf8_decode('Servicio / Sala'),1,0,'C');
		$pdf->Cell(8, 10, utf8_decode('Cama'),1,0,'C');
		$pdf->Cell(20, 10, utf8_decode('Estado'),1,0,'C');
		$pdf->Cell(75, 10, utf8_decode('Diagnóstico'),1,0,'C');
		$pdf->Cell(25, 10, utf8_decode('Dias / Hrs. Hosp.'),1,0,'C');
		$pdf->Cell(25, 10, utf8_decode('Especialidad'),1,0,'C');
		$pdf->Ln();
		imprimir_urgencia($pdf,$query,$q,$medico,$id_medico,$id_medico1,$medico1,$func_nombre);
		
		
		
		
		
	}
	
	function imprimir_urgencia($pdf,$query,$q,$medico,$id_medico,$id_medico1,$medico1,$func_nombre)
	{
		//if($q)
		$t=0;$pasillo=0;$hidra=0;$sala=0;
		
		for($i=0;$i<sizeof($q);$i++) {
					
			if($t==12){
				$pdf->addPage('L', 'letter');
				$t=0;
			}
			$t++;
		//------------------------------------------------------------------------------
		//BUSQUEDA DE NUMERO DE CAMAS    
                $j=1;
                for($n=$q[$i]['cama_num_ini']*1;$n<=$q[$i]['cama_num_fin']*1;$n++) {                                
                      if($q[$i]['hosp_numero_cama']*1==$n){
                             $nn=$j;
                      }
                            $j++;
                }
		//CIERRE BUSQUEDA DE NUM DE CAMAS
		//------------------------------------------------------------------------------
			$hosp_id=$q[$i]['hosp_id'];
			$pac_rut=$q[$i]['pac_rut'];
			$pac_nombre_completo=htmlentities($q[$i]['nombre_completo']);
			$edad_paciente=$q[$i]['edad_paciente'];
			$servicio=utf8_decode($q[$i]['tcama_tipo']).' '.utf8_decode($q[$i]['cama_tipo']);
			$cama=$nn;
			$estado=utf8_decode($q[$i]['hcon_nombre1']);
			$diagnostico=htmlspecialchars_decode($q[$i]['hosp_diag_cod'].' '.$q[$i]['diag_desc']);
			$dias_hosp=$q[$i]['dias_espera'].' D - '.$q[$i]['horas_espera'].'Hrs.';
			$especialidad=strtoupper($q[$i]['esp_desc']);
	
			$alineamiento = array('C', 'C', 'L', 'C', 'C', 'C', 'C', 'L', 'C', 'C');
			$medidas = array(15,20,35,10,35,8,20,75,25,25);
			//$fila = array($i+1, $hosp_id, $pac_rut, $pac_nombre_completo,$edad_paciente); //$i sirve para enumerar las filas
			$fila = array($hosp_id, $pac_rut, $pac_nombre_completo,$edad_paciente,$servicio,$cama,$estado,$diagnostico,
						  $dias_hosp,$especialidad);
			$pdf->setAligns($alineamiento);
			$pdf->setWidths($medidas);
			$pdf->Row($fila);
				
	
	if ($q[$i]['cama_id']==458){
			$pasillo++;
		}
	if ($q[$i]['cama_id']==492){
			$hidra++;
		}
	
	if ($q[$i]['cama_id']==457){
			$sala++;
		}
		
}//CIERRA FOR
	
	if($t>6){
		$pdf->addPage('L', 'letter');
	}
	
	$pdf->Ln();
	$pdf->SetFont('Arial','BU',15);
	$pdf->Cell(268, 10, utf8_decode('Profesionales de Turno'),0,0,'C');
	$pdf->Ln(15);
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(50, 5, utf8_decode('Médico:'),1,0,'R');
	$pdf->Cell(75, 5,$id_medico,1,0,'C');
	$pdf->Cell(83, 5, utf8_decode(''),0,0,'R');
	$pdf->Cell(35, 5, utf8_decode('Camillas Pediátricas:'),1,0,'R');
	$pdf->Cell(25, 5,$sala,1,0,'C');
	$pdf->Ln();
	$pdf->Cell(50, 5, utf8_decode('Traumatólogo:'),1,0,'R');
	$pdf->Cell(75, 5,$medico1,1,0,'C');
	$pdf->Cell(83, 5, utf8_decode(''),0,0,'R');
	$pdf->Cell(35, 5, utf8_decode('Camillas Adultos:'),1,0,'R');
	$pdf->Cell(25, 5,$pasillo,1,0,'C');
	$pdf->Ln();
	$pdf->Cell(50, 5, utf8_decode('Enfermera(o):'),1,0,'R');
	$pdf->Cell(75, 5, $func_nombre,1,0,'C');
	$pdf->Cell(83, 5, utf8_decode(''),0,0,'R');
	$pdf->Cell(35, 5, utf8_decode('Box:'),1,0,'R');
	$pdf->Cell(25, 5, $hidra,1,0,'C');
	$pdf->Ln();
	
	$pdf->Cell(208, 5, utf8_decode(''),0,0,'R');
	$pdf->Cell(35, 5, utf8_decode('Total Pacientes:'),1,0,'R');
	$pdf->Cell(25, 5, utf8_decode(sizeof($q)),1,0,'C');
	$pdf->Ln();
	
}//	CIERRA FUNCION
	
	

class PDF extends FPDF
{
	
	function Header()
	{
		
		GLOBAL $fecha1, $fecha2, $func_nombre, $ac, $medico,$id_medico,$id_medico1,$medico1;
		$this->Image('../../imagenes/logohpm_small.jpg',10,8,33);
		$this->SetFont('Arial','B',20);
		//$this->Cell(100);
		$this->Cell(0,10,'Informe de Urgencia',0,0,'C');
		$this->Ln(30);		
		$this->SetFont('Arial','B',9);
		$this->Cell(30,5,utf8_decode('Fecha de Emisión: '),0,0);
		$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		//echo $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
		$this->Cell(55,5, utf8_decode($dias[date('w')])." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y')." a las ". date ('H:i:s'),0,0,'L') ;
		//$this->Cell(18,5,"a las ". date ('H:i:s'),0,0,'L');
		
		
		$this->SetFont('Arial','B',12);
		//$this->Cell(0,10,'Fecha Inicio: '.$fecha1,0,1,'R');
		//$this->Cell(0,10,'Funcionario: '.$func_nombre,0,0,'L');
		//$this->Cell(0,10,'Fecha Fin: '.$fecha2,0,1,'R');
		$this->Ln(8);
			
		
	
	}
		
		
	function Footer(){

		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
		//$this->Cell(30,5,"cant".sizeof($q),0,0,'R');
	}
	
	var $widths;
	var $aligns;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,html_entity_decode($data[$i]),0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage('L','letter');
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}

	
	
	
	
	$pdf = new PDF();
	$pdf->SetMargins(5, 5 , 5); 
	$pdf->AliasNbPages();
	$pdf->AddPage('L', 'letter');
	dibujar_encabezado_tabla($pdf,$query,$q,$medico,$id_medico,$id_medico1,$medico1,$func_nombre);
	$pdf->Output();

	
?>
