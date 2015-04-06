<?php
    require_once('../../conectar_db.php');
    require_once('../../fpdf/fpdf.php');
    set_time_limit(0);
    class PDF extends FPDF
    {
        //Columna actual
        var $col=0;
        //Ordenada de comienzo de la columna
        var $y0;
        function SetCol($col)
        {
            //Establecer la posición de una columna dada
            $this->col=$col;
            $x=5+$col*93;
            $this->SetLeftMargin($x);
            $this->SetX($x);
        }
    }

    $receta_id=$_GET['receta_id']*1;
    $consulta="
    SELECT 
    receta_id,
    doc_rut,
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS doc_nombre,
    date_trunc('second', receta_fecha_emision) AS receta_emision,
    receta_comentarios,
    receta_diag_cod,
    diag_desc,
    COALESCE(receta_cronica, false) AS receta_cronica,
    tipotalonario_nombre,
    receta_numero,
    receta_tipotalonario_id,
    extract(month from receta_fecha_emision) AS mes,
    extract(year from receta_fecha_emision) AS anio,
    extract(day from receta_fecha_emision) AS dia,
    pac_rut, pac_appat || ' ' || pac_apmat || ' ' || pac_nombres AS pac_nombre
    FROM receta
    LEFT JOIN pacientes ON receta_paciente_id=pac_id
    LEFT JOIN doctores ON receta_doc_id=doc_id
    LEFT JOIN diagnosticos ON receta_diag_cod=diag_cod
    LEFT JOIN receta_tipo_talonario 
    ON receta_tipotalonario_id=tipotalonario_id
    WHERE receta_id=$receta_id
    ";
    
    
    $receta=cargar_registro($consulta);
    
    $detalle=cargar_registros_obj("SELECT * FROM recetas_detalle JOIN articulo ON recetad_art_id=art_id WHERE recetad_receta_id=$receta_id");
        
    $detalle_mov=cargar_registros_obj("SELECT *, date_trunc('second', log_fecha) AS log_fecha FROM logs 
    LEFT JOIN stock ON stock_log_id=log_id
    WHERE log_recetad_id IN (SELECT recetad_id FROM recetas_detalle WHERE recetad_receta_id=$receta_id)
    ");
    
    
    for($i=0;$i<count($detalle);$i++)
    {
        $arts[$detalle[$i]['art_id']]['art_id']=$detalle[$i]['art_id'];
        $arts[$detalle[$i]['art_id']]['art_codigo']=$detalle[$i]['art_codigo'];
        $arts[$detalle[$i]['art_id']]['art_glosa']=$detalle[$i]['art_glosa'];
        $arts[$detalle[$i]['art_id']]['recetad_cant']=$detalle[$i]['recetad_cant'];
        $arts[$detalle[$i]['art_id']]['recetad_horas']=$detalle[$i]['recetad_horas'];
        $arts[$detalle[$i]['art_id']]['recetad_dias']=$detalle[$i]['recetad_dias'];
        $arts[$detalle[$i]['art_id']]['saldo']=$detalle[$i]['recetad_dias']*24/$detalle[$i]['recetad_horas']*$detalle[$i]['recetad_cant'];      
    }
    
    
    if($detalle_mov)
    {
        for($i=0;$i<count($detalle_mov);$i++)
        {
            $det=$detalle_mov[$i];
            $art=$arts[$det['stock_art_id']];
            $arts[$det['stock_art_id']]['saldo']+=$det['stock_cant'];
            $cantidad=$art['recetad_dias']*24/$art['recetad_horas']*$art['recetad_cant'];
        }
    }
    
   
    $iniciar=99999;
    $terminar=0;
    
    foreach($arts as $art)
    {
        $cantidad=($art['recetad_dias']*24/$art['recetad_horas'])*$art['recetad_cant'];
        $cuota=floor($cantidad/($art['recetad_dias']/30));
        $c=floor(($cantidad-$art['saldo'])/$cuota);
        $t=floor($cantidad/$cuota);
        if($c<$iniciar)
            $iniciar=$c;
        if($t>$terminar)
            $terminar=$t;
        $arts[$art['art_id']]['cantidad']=$cantidad;
        $arts[$art['art_id']]['cuota']=$cuota;
        $arts[$art['art_id']]['inicio']=$c;
        $arts[$art['art_id']]['final']=$t;
    }
        
    
    $c=0;
    //print('iniciar en '.$iniciar.' y terminar en '.$terminar);
    
    $mes=$receta['mes'];
    $anio=$receta['anio'];
    $dia=$receta['dia'];
    
    $pdf = new PDF('L', 'mm', 'Letter');
    $pdf->SetFont('Arial','B',16);
    
    $w=array(53,9,10,10);
    $header=array('Glosa', 'Cant.', '', 'Cuota');

    for($i=$iniciar+1;$i<=$terminar;$i++,$c++)
    {
        if($c==0 or $c==3)
        {
            if($c==3)
                $c=0;
            $pdf->AddPage();
        } 
        $pdf->SetCol($c);
        $pdf->SetY(15);
        $fecha=date('d/m/Y', mktime(0, 0, 0, $mes+($i-1), $dia, $anio));
        $pdf->SetFont('Arial','B',16);
        $pdf->SetFillColor(255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(128,0,0);
        $pdf->SetLineWidth(.1);
        $pdf->SetFont('','B', 12);
        $pdf->SetFillColor(224,235,255);
    
        $pdf->Multicell( 82, 5, "Ministerio de Salud Publica\nServicio de Salud\nMetropolitano Occidente - Melipilla\nHospital San José de Melipilla",1,'C', true);
    
        $pdf->SetFont('', 'BU', 14);
    
        $pdf->Cell( 82, 10, "Talonario de Recetas Crónicas",1,0,'C');    
        $pdf->Ln();

        $pdf->SetFont('', '', 8);
    
        $pdf->Cell(25, 7, "Médico:",1,0,'R', true);
        $pdf->Cell(57, 7, $receta['doc_nombre'],1,0,'L');
        $pdf->Ln();
    
        $pdf->Cell(25, 7, "R.U.T. Paciente:",1,0,'R', true);
        $pdf->Cell(57, 7, $receta['pac_rut'],1,0,'L');
        $pdf->Ln();
    
        $pdf->Cell(25, 7, "Paciente:",1,0,'R', true);
        $pdf->Cell(57, 7, $receta['pac_nombre'],1,0,'L');
        $pdf->Ln();
    
        $pdf->Cell(25, 7, "Fecha:",1,0,'R',true);
        $pdf->SetFont('', '', 11);
        $pdf->Cell(57, 7,"___/___/______",1,0,'L');
        $pdf->SetFont('', '', 8);
        $pdf->Ln();
    
    
        $pdf->SetFillColor(255,0,0);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(128,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('','B', 6);

        for($k=0;$k<count($header);$k++)
            $pdf->Cell($w[$k],7,$header[$k],1,0,'C',1);
    
        $pdf->Ln();
    
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('','',6);
        //Datos
        $fill=false;
        foreach($arts as $art)
        {
            if($art['inicio']<$i AND $art['final']>=$i) 
            {
                //$pdf->Cell($w[1],7,substr($art['art_glosa'],0,24),'LR',0,'L',$fill);
                $x=$pdf->GetX();
                $y=$pdf->GetY();
                $pdf->SetFont('','',8);
                $pdf->SetX($x);
                $pdf->Multicell($w[0], 4, $art['art_glosa'], 1, 'LR', $fill);
                $ysize=$pdf->GetY()-$y;
                //$pdf->SetFont('','',6);
                //$pdf->SetXY($x, $y);        
                //$pdf->Cell($w[0],$ysize,$art['art_codigo'],'LR',0,'R',$fill);
                $pdf->SetFont('','',9);
                $pdf->SetXY($x+$w[0], $y);
                $pdf->Cell($w[1],$ysize,number_format($art['cuota'],1,',','.'),'LR',0,'R',$fill);
                $pdf->Cell($w[2],$ysize,"_____",'LR',0,'C',$fill);
                $pdf->Cell($w[3],$ysize,$i."/".number_format($art['final']),'LR',0,'C',$fill);
                $pdf->Ln();
                $pdf->SetFont('','',6);
                $fill=!$fill;
            }
        }
        $pdf->Cell(array_sum($w),0,'','T');
        $pdf->SetY(180);
        $pdf->SetFont('','',12);
        $pdf->MultiCell(82, 7, "____________________\nFirma Funcionario",1,'C',true);
    }
    $pdf->Output();
?>