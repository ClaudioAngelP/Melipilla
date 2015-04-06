<?php

require_once('../../conectar_db.php');
require_once('../../fpdf/fpdf.php');

set_time_limit(0);
  
class PDF extends FPDF {

    //Columna actual
    var $row=0;
    //Ordenada de comienzo de la columna
    var $y0;

    function SetRow($row)
    {
        //Establecer la posición de una columna dada
        $this->row=$row;
        $y=15+$row*125;
        $this->SetTopMargin($y);
        $this->SetY($y);
    }

}

  function recortar($str, $l) {
    if(strlen($str)>$l)
      return substr($str,0,$l);
    else
      return $str;
  }

  $esp_id=$_POST['esp_id']*1;
  $fecha1=pg_escape_string($_POST['fecha1']);
  $fecha2=pg_escape_string($_POST['fecha2']);

  if($esp_id!=-1) {
    $espq="cupos_esp_id=$esp_id";
  } else {
    $espq="true";
  }

  $planillas=cargar_registros_obj("
  
    SELECT * FROM (
    SELECT DISTINCT 
      cupos_esp_id AS esp_id, cupos_doc_id AS doc_id, cupos_fecha::date AS cupos_fecha
    FROM cupos_atencion 
    WHERE 
    cupos_fecha BETWEEN '$fecha1' AND '$fecha2'
    AND $espq
    ) AS foo 
    JOIN doctores USING (doc_id)
    JOIN especialidades USING (esp_id)
    ORDER BY esp_id, cupos_fecha;
     
  ");

    $pdf = new PDF('L', 'mm', 'Legal');
    
    for($i=0;$i<count($planillas);$i++) {
    
    $cupos = cargar_registros_obj(
    "SELECT
    pacientes.*,
    cupos_atencion.*,
    cupos_fecha::date AS cupos_fecha, 
    asigna_hora, asigna_id, control_id 
    FROM cupos_asigna 
    JOIN interconsulta ON cupos_asigna.inter_id=interconsulta.inter_id 
    JOIN pacientes ON pacientes.pac_id=interconsulta.inter_pac_id
    JOIN cupos_atencion USING (cupos_id)
    WHERE 
    (date_trunc('day', cupos_atencion.cupos_fecha)='".$planillas[$i]['cupos_fecha']."')
    AND cupos_doc_id=".$planillas[$i]['doc_id']."
    AND $espq
    ORDER BY cupos_atencion.cupos_fecha, asigna_hora
    ");

    if($cupos) {
      $pdf->AddPage();    
    } else {
      continue;
    }

    $pdf->SetFont('Arial','B',16);
    
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(128,0,0);
    $pdf->SetLineWidth(.1);
    $pdf->SetFont('','B', 12);
    $pdf->SetFillColor(224,235,255);
    
    $pdf->Multicell( 330, 7, "Ministerio de Salud Pública\nServicio de Salud Viña del Mar - Quillota\nHospital San Martín de Quillota",1,'C', true);
    
    $pdf->SetFont('', 'BU', 14);
    
    $pdf->Cell( 330, 10, "Planilla de Atención Abierta",1,0,'C');    
    $pdf->Ln();

    $pdf->SetFont('', '', 12);
    
    $pdf->Cell(60, 7, "Especialidad:",1,0,'R', true);
    $pdf->Cell(270, 7, $planillas[$i]['esp_desc'],1,0,'L');
    $pdf->Ln();
    
    $pdf->Cell(60, 7, "Fecha:",1,0,'R',true);
    $pdf->Cell(270, 7, $planillas[$i]['cupos_fecha'] ,1,0,'L');
    $pdf->Ln();

    $pdf->Cell(60, 7, "Nombre Médico:",1,0,'R', true);
    $pdf->Cell(270, 7, $planillas[$i]['doc_nombres'].' '.$planillas[$i]['doc_paterno'].' '.$planillas[$i]['doc_materno'],1,0,'L');
    $pdf->Ln();
    
    
    $pdf->SetFillColor(224,235,255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('Courier','',8);
    
    $h=array('Hora','RUT','Ficha','Paterno','Materno','Nombre','T','Asiste','Diag.','Destino');
    $w=array(20,30,30,40,40,40,5,15,80,30);
    
    for($k=0;$k<count($w);$k++) {
      $pdf->Cell($w[$k],7,$h[$k], 1,0,'C', true);
    }
    
    $pdf->Ln();
    
    for($j=0;$j<count($cupos);$j++) {
    
      $c=$cupos[$j];
    
      $pdf->Cell($w[0], 7, $c['asigna_hora'], 1, 0, 'C');
      $pdf->SetFont('Courier','B',10);
      $pdf->Cell($w[1], 7, $c['pac_rut'], 1, 0, 'R');
      $pdf->Cell($w[2], 7, $c['pac_ficha'], 1, 0, 'C');
      $pdf->SetFont('Courier','',8);
      $pdf->Cell($w[3], 7, recortar($c['pac_appat'],20) , 1, 0, 'L');
      $pdf->Cell($w[4], 7, recortar($c['pac_apmat'],20) , 1, 0, 'L');
      $pdf->Cell($w[5], 7, recortar($c['pac_nombres'],20) , 1, 0, 'L');
      $pdf->Cell($w[6], 7, $c['control_id']!=0?'C':'N' , 1, 0, 'C');
      $pdf->Cell($w[7], 7, '___', 1, 0, 'C');
      $pdf->Cell($w[8], 7, ' ', 1, 0, 'C');
      $pdf->Cell($w[9], 7, ' ', 1, 0, 'C');
      $pdf->Ln();
    
    }
    
  }
  
  $fecha1=str_replace('/','-',$fecha1);
  $fecha2=str_replace('/','-',$fecha2);
  
  if($fecha1!=$fecha2) 
    $sufix=$fecha1."_".$fecha2;
  else
    $sufix=$fecha1;
  
  $pdf->Output("planillas_$sufix.pdf",'I');
  
?>