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

  if(!isset($_GET['asigna_id'])) {
  
    $esp_id=$_POST['esp_id']*1;
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
    
    if($esp_id!=-1) 
      $espq="cupos_esp_id=$esp_id";
    else
      $espq="true";
      
    if(isset($_POST['excluir']))
      $excluye="NOT asigna_citacion AND";
    else
      $excluye='';
    
    $where=" WHERE $excluye
    (date_trunc('day', cupos_atencion.cupos_fecha) BETWEEN '$fecha1' AND '$fecha2') 
    AND $espq
    ORDER BY esp_id, doc_paterno, doc_materno, doc_nombres, 
              cupos_atencion.cupos_fecha, asigna_hora ";
    
    $asigna_id=0;
    
  } else {
  
    $asigna_id=$_GET['asigna_id']*1;
    $where=" WHERE asigna_id=$asigna_id ";
    
  }

  $cupos = cargar_registros_obj(
    "SELECT
    pacientes.*,
    doctores.*, 
    cupos_atencion.*, especialidades.*,
    cupos_fecha::date AS cupos_fecha, 
    asigna_hora, asigna_id, control_id , ciud_desc, prov_desc, reg_desc
    FROM cupos_asigna 
    JOIN interconsulta ON cupos_asigna.inter_id=interconsulta.inter_id 
    JOIN pacientes ON pacientes.pac_id=interconsulta.inter_pac_id
    LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id
		LEFT JOIN provincias ON comunas.prov_id=provincias.prov_id
		LEFT JOIN regiones ON provincias.reg_id=regiones.reg_id
    JOIN cupos_atencion USING (cupos_id)
    JOIN especialidades ON cupos_esp_id=esp_id
    JOIN doctores ON doctores.doc_id=cupos_doc_id
    $where
    "
  );

  //die(print_r($cupos));
    
    $pdf = new PDF('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->SetRow(0);
    
    $pp=0;
    
    if($cupos)
    for($i=0;$i<count($cupos);$i++) {
    
    $c=$cupos[$i];
    
    pg_query("UPDATE cupos_asigna SET asigna_citacion=true 
              WHERE asigna_id=".$c['asigna_id']);
    
    $pdf->SetFont('Arial','B',16);
    
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(128,0,0);
    $pdf->SetLineWidth(.1);
    $pdf->SetFont('','B', 12);
    $pdf->SetFillColor(224,235,255);
    
    $pdf->Multicell( 190, 7, "Ministerio de Salud Pública\nServicio de Salud Viña del Mar - Quillota\nHospital San Martín de Quillota",1,'C', true);
    
    $pdf->SetFont('', 'BU', 14);
    
    $pdf->Cell( 190, 10, "Citación de Atención Médica",1,0,'C');    
    $pdf->Ln();

    $pdf->SetFont('', '', 10);
    
    $pdf->Cell(45, 7, "Especialidad:",1,0,'R', true);
    $pdf->Cell(145, 7, $c['esp_desc'],1,0,'L');
    $pdf->Ln();
    
    $pdf->Cell(45, 7, "Tipo:",1,0,'R', true);
    $pdf->Cell(145, 7, $c['control_id']!=0?'Control':'Primera Atención' ,1,0,'L');
    $pdf->Ln();
    
    $pdf->Cell(45, 7, "Nombre Médico:",1,0,'R', true);
    $pdf->Cell(145, 7, $c['doc_nombres'].' '.$c['doc_paterno'].' '.$c['doc_materno'],1,0,'L');
    $pdf->Ln();
    
    $pdf->Cell(45, 7, "R.U.T. Paciente:",1,0, 'R', true);
    $pdf->Cell(50, 7, $c['pac_rut'],1,0,'L');
    $pdf->Cell(45, 7, "Ficha Paciente:",1,0, 'R', true);
    $pdf->Cell(50, 7, $c['pac_ficha'],1,0,'L');
    
    $pdf->Ln();
    
    $pdf->Cell(45, 7, "Nombre Paciente:",1,0,'R', true);
    $pdf->Cell(145, 7, $c['pac_nombres'].' '.$c['pac_appat'].' '.$c['pac_apmat'],1,0,'L');
    $pdf->Ln();
    
    $pdf->Cell(45, 7, "Domicilio:",1,0,'R',true);
    $pdf->Cell(145, 7, trim($c['pac_direccion']).(($c['sector_nombre']!='')?', '.trim($c['sector_nombre']):'').', '.$c['ciud_desc'].'.' ,1,0,'L');
    $pdf->Ln();
    $pdf->Cell(45, 7, " ",1,0,'R',true);
    $pdf->Cell(145, 7, 'Provincia de '.$c['prov_desc'].', '.$c['reg_desc'].'.' ,1,0,'L');
    $pdf->Ln();
    
    $pdf->Cell(45, 7, "Teléfono:",1,0,'R',true);
    $pdf->Cell(145, 7, (($c['pac_fono']!='')?$c['pac_fono']:'(No hay registro)') ,1,0,'L');
    $pdf->Ln();
    
    $pdf->Cell(45, 7, "Fecha:",1,0,'R',true);
    $pdf->Cell(145, 7, $c['cupos_fecha'] ,1,0,'L');
    $pdf->Ln();

    $pdf->Cell(45, 7, "Hora:",1,0,'R',true);
    $pdf->Cell(145, 7, $c['asigna_hora'] ,1,0,'L');
    $pdf->Ln();
    
    
    $pdf->SetFillColor(224,235,255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('','',6);
    
    $pp++;
    
    if($pp==1) $pdf->SetRow(1);
    elseif($pp==2 AND $i<count($cupos)-1) { $pdf->AddPage(); $pp=0; $pdf->SetRow(0); }

    
  }
  
  if($asigna_id==0) {

    $fecha1=str_replace('/','-',$fecha1);
    $fecha2=str_replace('/','-',$fecha2);
  
    if($fecha1!=$fecha2) 
      $sufix=$fecha1."_".$fecha2;
    else
      $sufix=$fecha1;

  } else 

    $sufix=$asigna_id;
  
  if($cupos) {
    $pdf->Output("citaciones_$sufix.pdf",'I');
    exit(0);
  }
    
?>

<html>
<title>Sistema Integral de Gesti&oacute;n Hospitalaria (SiGH)</title>

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>
<center>
<br /><br /><br /><br /><br />
<b>No hay citaciones pendientes por imprimir.</b>

</center>
</body>
</html>

