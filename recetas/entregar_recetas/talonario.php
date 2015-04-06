<?php
    require_once('../../conectar_db.php');
    require('fpdf_alpha.php'); 
    //require_once('../../fpdf/fpdf.php');
    set_time_limit(0);
    $margin=5; $bodega='BODEGA';  $ratio=0.7;
    $log_fecha=isset($_GET['log_fecha'])?($_GET['log_fecha']):'0';
    if($log_fecha!='')
    {
        if($log_fecha!='0')
            $log_fecha_q="AND log_fecha>='$log_fecha'";
        else
            $log_fecha_q="";
    }
    else
    {
        $log_fecha_q="";
    }
    
    class PDF extends PDF_ImageAlpha {
    //Columna actual
    var $col=0;
    //Ordenada de comienzo de la columna
    var $y0;

    function SetCol($col)
    {
		GLOBAL $margin;
		
        //Establecer la posición de una columna dada
        $this->col=$col;
        $x=($margin+10)+$col*93;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }
    
    function Header()
    {
		GLOBAL $receta, $bodega, $mes, $dia, $anio, $fill, $margin, $fecha, $ratio, $log_fecha, $esp_receta;
		

		$this->SetCol(0);
		$this->SetY(5);
		
		//$fecha=date('d/m/Y', mktime(0, 0, 0, $mes+($i-1), $dia, $anio));

		$this->SetFont('Arial','B',16*$ratio);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		//$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(0);
		$this->SetFont('','B', 14*$ratio);
		$this->SetX($margin+(80*$ratio));
		$this->Multicell(100*$ratio, 5, "Servicio Salud Metropolitano Occidente\nHospital de San José de Melipilla",0,'L', false);
		$this->Image('../../imagenes/logo.png',($margin+(30*$ratio)),5,30,15);
		$this->SetFont('','', 15*$ratio);
		
		$this->Ln(3);
		$this->SetFont('', 'BU', 15*$ratio);
		$this->Cell(280*$ratio, 6, "DISPENSACION DE MEDICAMENTOS $bodega",0,0,'C');    
		$this->SetTextColor(0);
		$this->SetFillColor(255,255,255);
		$this->Ln();
		
		$this->SetFont('', '', 10*$ratio);
		$this->Cell(35*$ratio, 4, "N° Ficha:",1,0,'L', true);
		
		$this->Cell(100*$ratio, 4, $receta['pac_ficha'],1,0,'L');
		
		$this->Cell(35*$ratio, 4, "N° Receta:",1,0,'L', true);
		
		$this->Cell(100*$ratio, 4, $receta['receta_numero'],1,0,'L');
		$this->Ln();
		
		$this->Cell(35*$ratio, 4, "R.U.T. Paciente:",1,0,'L', true);
		
		$this->Cell(100*$ratio, 4, $receta['pac_rut'],1,0,'L');
		
		$this->Cell(35*$ratio, 4, "Despachado en:",1,0,'L', true);
		
		$this->Cell(100*$ratio, 4, $bodega,1,0,'L');
		$this->Ln();
		
		$this->Cell(35*$ratio, 4, "Nombres:",1,0,'L', true);
		
		$this->Cell(100*$ratio, 4, $receta['pac_nombres'],1,0,'L');
		$this->Cell(35*$ratio, 4, "Digitado Por:",1,0,'L', true);
		$this->Cell(100*$ratio, 4, $receta['func'],1,0,'L');
		$this->Ln();
		$this->Cell(35*$ratio, 4, "Apellidos:",1,0,'L', true);
		
		$this->Cell(100*$ratio, 4, $receta['pac_apellidos'],1,0,'L');
		$this->Cell(35*$ratio, 4, "Profesional:",1,0,'L', true);
		$this->SetFont('', '', 9*$ratio);
		$this->Cell(100*$ratio, 4,  'DR(a). '.$receta['doc_nombre'],1,0,'L');
		
        
		$this->Ln();
		$this->Cell(35*$ratio, 4, "Prevision:",1,0,'L', true);
		
		$this->Cell(100*$ratio, 4, $receta['prev_desc'],1,0,'L');
		$this->SetFont('', '', 9*$ratio);
		$this->Cell(35*$ratio, 4, "R.U.T. Profesional:",1,0,'L', true);
		
		$this->SetFont('', '', 10*$ratio);
		$this->Cell(100*$ratio, 4,  $receta['doc_rut'],1,0,'L');
		$this->Ln();
		
		$this->Cell(35*$ratio, 4, "Diagnóstico:",1,0,'L', true);
		$this->SetFont('', '', 9*$ratio);
		if(strlen($receta['receta_diagnostico'])>45) $diag_m=substr($receta['receta_diagnostico'],0,45).'...'; else $diag_m=$receta['receta_diagnostico'];
		$this->Cell(100*$ratio, 4, $diag_m,1,0,'L');
		$this->SetFont('', '', 9*$ratio);
		$this->Cell(35*$ratio, 4, "Estamento:",1,0,'L', true);
		$this->SetFont('', '', 10*$ratio);
		$this->Cell(100*$ratio, 4, $receta['doc_estamento'],1,0,'L');
		$this->Ln();
		
		$this->Cell(35*$ratio, 4, "Fecha despacho:",1,0,'L', true);
		$this->SetFont('', '', 10*$ratio);
		$this->Cell(100*$ratio, 4, $receta['fecha'],1,0,'L');
		$this->SetFont('', '', 9*$ratio);
		$this->Cell(35*$ratio, 4, "Especialidad:",1,0,'L', true);
		$this->SetFont('', '', 10*$ratio);
		$this->Cell(100*$ratio, 4, $esp_receta,1,0,'L');
		$this->Ln();
		
		$this->Cell(35*$ratio, 4, "Despacho:",1,0,'L', true);
		$this->SetFont('', '', 10*$ratio);

		if($receta['vigencia']==1)
                {
                    $vigencia_txt='1 de 1';		
		}
                else
                {
                    $vigencia = cargar_registros_obj("
                    SELECT DISTINCT date_trunc('MINUTE', (log_fecha))::date AS fecha
                    FROM logs 
                    WHERE log_recetad_id IN
                    (SELECT recetad_id FROM recetas_detalle WHERE recetad_receta_id=".$receta['receta_id'].")
                    ORDER BY fecha ASC");
                    for($o=0;$o<sizeof($vigencia);$o++)
                    {
                        if($vigencia[$o]['fecha']==$receta['fecha'])
                        {
                            $vigencia_txt=($o+1)." de ".$receta['vigencia'];
			}
                    }
		}
		
		$this->Cell(100*$ratio, 4,$vigencia_txt,1,0,'L');
		$this->SetFont('', '', 9*$ratio);
		$this->Cell(35*$ratio, 4, "Policlínico:",1,0,'L', true);
		$this->SetFont('', '', 10*$ratio);
		$this->Cell(100*$ratio, 4, $receta['centro_nombre'],1,0,'L');
		$this->Ln();
		
		$diag=$receta['receta_diagnostico'];
		if(strlen($diag)>35) {
			$diag=substr($diag,0,35).'...';
		}
		
		if($receta['receta_diag_cod']!='')$diag_cod='['.$receta['receta_diag_cod'].']';
		else $diag_cod='';
		
		$this->SetTextColor(0,0,0);
		$this->SetFont('','B', 13*$ratio);
		$this->Cell(270*$ratio,5,'Detalle de los Medicamentos Dispensados',1,0,'C',1);
		$this->Ln();
		
		$this->SetFont('','B', 13*$ratio);
		$this->Cell(100*$ratio,8,'Descripción Medicamento',1,0,'C',1);
		$this->Cell(20*$ratio,8,'Unidad',1,0,'C',1);
		$this->SetFont('','B', 8*$ratio);
		
		$this->SetFont('','B', 10*$ratio);
		$this->Cell(90*$ratio,8,'Dosis',1,0,'C',1);					
		$this->Cell(30*$ratio,8,'Desp.',1,0,'C',1);
		$this->Cell(30*$ratio,8,'Pendientes',1,0,'C',1);		
		
		$this->Ln();
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0);
		$this->SetFont('','',6*$ratio);
		
	}
	
	function Footer() {

		GLOBAL $receta, $bodega, $mes, $dia, $anio, $fill, $margin, $desp;
		
		$this->SetFont('', 'I', 8*$ratio);
		$this->Ln();
		$this->Cell( 123*$ratio, 4, "Despachado por: ".$desp[(sizeof($desp)-1)]['func'],0,0,'L');    
		$this->Ln();
		$this->Cell( 123*$ratio, 4, "Observaciones: ".$receta['receta_comentarios'],0,0,'L');    
		
		// Muestra Número de Página
		//$this->SetY(-18.5);
		//$this->SetFont('', 'B', 10);
		//$this->Cell(123,5,'Página '.$this->PageNo().' de {nb}','T',1,'C');		
		
		//$this->SetFont('', '', 10*$ratio);
		
		//$this->Cell(220,5,'NOTA: Si la fecha de entrega corresponde a día no hábil; podrá retirar el día hábil anterior.','T',0,'C');

		// Firma del Médico (SOLO AUTORIZADOS)
		$mrut=str_replace('-','',trim($receta['doc_rut']));
		$fname='../firma_medicos/'.$mrut.'.png';
		if(file_exists($fname)) {
			$this->Image($fname,175,150,100,45);
		}
		
	}
	
	function CheckText($string, $___width, $___height) {
			
			$number_of_lines = ceil( $this->GetStringWidth($string) / ($___width - 1) );
			$_height=$___height*$number_of_lines;
			
			$space_left = (($this->h-150) - $this->GetY()); // space left on page
			$space_left -= $_height; // less the bottom margin
			$space_left -= $this->__bottom_margin; // less the bottom margin

			if($space_left<0) {
				//print('<b>CREAR PAGINA!</b><br>');
				$this->AddPage();
			} else {
				//print("NO CREAR len=".strlen($string)." ___width=$___width __bottom_margin=".($this->__bottom_margin)." ___height=$___height number_of_lines=$number_of_lines _height=$_height this->h=".$this->h." space_left=$space_left !<br>");
			}
			
			return $space_left;
			
	}

    }
    
    $todas=false;
    $esp_receta="";
    if(isset($_GET['receta_numero']))    
    {
        $receta_num=$_GET['receta_numero']*1;
        if($receta_num!=0)
        {
            $reg_receta=cargar_registro("SELECT * from receta where receta_numero=$receta_num");
            if($reg_receta)
            $receta_id=$reg_receta['receta_id'];
            else
            {
                print("Error al rescatar receta");
                exit();
            }
        }
        else {
            $todas=true;
        }
       
    }
    else {
        $receta_id=$_GET['receta_id']*1;
    }
    if($todas)
    {
        if(isset($_GET['gestion_cama']))    
        {
            $fecha1=pg_escape_string($_GET['fecha1']);
            $fecha2=pg_escape_string($_GET['fecha2']);
            $h1=pg_escape_string($_GET['hora1']);
            $h2=pg_escape_string($_GET['hora2']);
            $serv_hosp=pg_escape_string($_GET['centro_ruta0']);
            $doc_id=$_GET['doc_id']*1;
            $pac_id=$_GET['pac_id']*1;
            
            if($doc_id!="")
            {
                $w_doc="receta_doc_id=$doc_id";
            }
            else
            {
                $w_doc="true";
            }
            
            if($pac_id!="" and $pac_id!="0"){
				$w_pac=" receta_paciente_id=".$pac_id."";
			} else {
				$w_pac=" true";
			}
            
            
            if($serv_hosp!="")
            {
                $reg_servicio=cargar_registro("select * from clasifica_camas where tcama_id=$serv_hosp");
                if($reg_servicio)
                {
                    $w_centro_ruta=" receta_centro_ruta='".$reg_servicio['tcama_centro_ruta']."'";
                }
                else
                {
                    $w_centro_ruta=" true";
                }
            }
            else
            {
                $w_centro_ruta="true";
            }
            
            
            
            if($h1!='')
                $h1l_w=$h1;
            else
                $h1l_w='00:00:00';

            if($h2!='')
                $h2l_w=$h2;
            else
                $h2l_w='23:59:59';
            
            /*
            print("SELECT receta_id 
            FROM receta where receta_bod_id=35 and receta_fecha_emision between '$fecha1 $h1l_w' and '$fecha2 $h2l_w'
            and $w_centro_ruta");
            * 
            */
            
            $consulta="SELECT receta_id 
            FROM receta where receta_bod_id=35 and receta_fecha_emision between '".$fecha1." ".$h1l_w."' and '".$fecha2." ".$h2l_w."'
            and ".$w_centro_ruta." and ".$w_doc." and ".$w_pac."";
            
            //print($consulta);
            
            $reg_recetas=cargar_registros_obj($consulta);
            if(!$reg_recetas)
            {
                print("No se han encontrado recetas segun parametros de busqueda");
                exit();
            }
                
        }
      
    }
    
    if(!$todas)
    {
        $consulta="SELECT pac_ficha,pac_rut,pac_nombres,pac_appat||' '||pac_apmat AS pac_apellidos,prev_desc,prev_id,
        doc_rut,doc_nombres||' '||doc_paterno||' '||doc_materno AS doc_nombre,
	diag_desc AS receta_diagnostico,receta_id,receta_diag_cod,diag_desc,
        date_trunc('second', receta_fecha_emision) AS receta_emision, 
        (receta_fecha_emision::date+(((SELECT max(recetad_dias) FROM recetas_detalle WHERE recetad_receta_id=receta_id) || ' days')::interval))::date AS vigente,
        receta_comentarios,COALESCE(receta_cronica, false) AS receta_cronica,tipotalonario_nombre,
	COALESCE(CASE WHEN receta_numero=0 OR receta_numero is null THEN '0' ELSE receta_numero END,receta_id)AS receta_numero,
	receta_tipotalonario_id,centro_nombre,func_nombre AS func,receta_bod_id,
	(SELECT esp_desc FROM nomina_detalle join nomina using (nom_id) join especialidades on esp_id=nom_esp_id
	WHERE nomina_detalle.pac_id=pacientes.pac_id AND nom_doc_id=receta_doc_id 
	ORDER BY nom_fecha DESC,nomd_hora DESC LIMIT 1)AS esp_desc, doc_estamento,
        CASE WHEN (SELECT max(recetad_dias) FROM recetas_detalle WHERE recetad_receta_id=receta_id)>30 
        THEN (SELECT max(recetad_dias) FROM recetas_detalle WHERE recetad_receta_id=receta_id)/30 
        ELSE 1 END as vigencia,
	COALESCE((SELECT max(log_fecha) FROM logs left join recetas_detalle 
	on recetad_id=log_recetad_id WHERE recetad_receta_id=receta_id),receta_fecha_emision)::date AS fecha,
        receta_hosp_id,
	receta_nomd_id,
        receta_doc_id
	FROM receta
	LEFT JOIN pacientes ON receta_paciente_id=pac_id
	LEFT JOIN prevision USING (prev_id)
	LEFT JOIN doctores ON receta_doc_id=doc_id
	LEFT JOIN diagnosticos ON receta_diag_cod=diag_cod
	LEFT JOIN receta_tipo_talonario ON receta_tipotalonario_id=tipotalonario_id
	LEFT JOIN centro_costo ON centro_ruta=receta_centro_ruta
	LEFT JOIN funcionario ON func_id=receta_func_id
        WHERE receta_id=$receta_id
        group by receta_id,doc_rut,doc_nombres,doc_paterno,doc_materno,receta_fecha_emision,receta_comentarios,receta_diag_cod,
	diag_desc,receta_cronica,tipotalonario_nombre,receta_numero,receta_tipotalonario_id,pac_fc_nac,pac_ficha,pac_rut,pac_nombres,pac_appat,
	pac_apmat,receta_diagnostico,prev_desc,prev_id,centro_nombre,func_nombre,receta_bod_id,pacientes.pac_id,receta.receta_doc_id, doc_estamento";
        
        /*
        if(($_SESSION['sgh_usuario_id']*1)==7)
        {
            print($consulta);
            die();
        
        }
        */
        
        $receta=cargar_registro($consulta);
        if($receta)
        {
            if(($receta['receta_hosp_id']*1)!=0)
            {
                
            }
            else {
                if(($receta['receta_nomd_id']*1)!=0)
                {
                    
                    $consulta="SELECT nom_esp_id,nom_doc_id,esp_desc FROM nomina
                    LEFT JOIN nomina_detalle using (nom_id)
                    LEFT JOIN especialidades on esp_id=nom_esp_id
                    WHERE nomd_id=".($receta['receta_nomd_id']*1)." and nom_doc_id=".($receta['receta_doc_id']*1)."";
                    $receta_esp=cargar_registro($consulta);
                    if($receta_esp)
                    {
                        $esp_receta=$receta_esp['esp_desc'];
                    }
                }
            }
        }
        
        
    
        $consulta="
        SELECT *,
        upper( COALESCE(art_unidad_adm, forma_nombre) ) AS art_unidad_administracion,
        COALESCE( art_unidad_cantidad, 1 ) AS art_unidad_cantidad_adm,recetad_indicaciones,
        ar_fonasa_a,ar_fonasa_b,ar_fonasa_c,ar_fonasa_d,ar_particular
        FROM recetas_detalle 
        JOIN articulo ON recetad_art_id=art_id
        LEFT JOIN bodega_forma ON art_forma=forma_id
	LEFT JOIN bodega ON bod_id=".$receta['receta_bod_id']."
        LEFT JOIN aranceles ON ar_codigo=art_codigo AND bod_glosa ilike '%'||ar_tipo||'%'
        WHERE recetad_receta_id=$receta_id
        ";
        
        /*
        if(($_SESSION['sgh_usuario_id']*1)==7)
        {
            print($consulta);
            die();
        
        }
        
         * 
         */
        $detalle=cargar_registros_obj($consulta);
        
        $mes=$receta['mes'];
        $anio=$receta['anio'];
        $dia=$receta['dia'];
    
        $pdf = new PDF('P', 'mm', 'Letter');
        //$pdf = new PDF('P','mm',array(311,396));
        $pdf->SetFont('Arial','B',16*$ratio);
    
        $c=0; $i=0;
        $bod=cargar_registros_obj("SELECT -stock_cant AS stock_cant,
        UPPER(COALESCE(f2.func_nombre,f1.func_nombre))AS func,
	COALESCE(b2.bod_glosa,b1.bod_glosa) AS bod_glosa,
	MAX(log_fecha::date)AS fecha,log_fecha::date AS log_fecha, COALESCE(b2.bod_id,b1.bod_id) AS bod_id
	FROM recetas_detalle 
	LEFT JOIN receta ON receta_id=recetad_receta_id
	LEFT JOIN logs ON log_recetad_id=recetad_id
	LEFT JOIN stock ON stock_log_id=log_id
	LEFT JOIN bodega AS b2 ON b2.bod_id=stock_bod_id
	LEFT JOIN bodega AS b1 ON b1.bod_id=receta_bod_id
	LEFT JOIN funcionario AS f2 ON f2.func_id=log_func_if
	LEFT JOIN funcionario AS f1 ON f1.func_id=receta_func_id
	WHERE recetad_id=".$detalle[0]['recetad_id']." $log_fecha_q
	GROUP BY stock_cant,b1.bod_glosa,b2.bod_glosa,f1.func_nombre,f2.func_nombre,log_fecha, b1.bod_id, b2.bod_id
	ORDER BY log_fecha ,log_fecha::time ASC");
			
        $bodega=$bod[(sizeof($bod)-1)]['bod_glosa'];
        $bod_id=$bod[(sizeof($bod)-1)]['bod_id']*1;
        if($bod_id==3){
            $pdf->SetAutoPageBreak(true, 100);
        }else{
            $pdf->SetAutoPageBreak(true, 150);
        }
        $pdf->AliasNbPages();
    
        if($c==0 or $c==3)
        {
            if($c==3)
                $c=0;
            $pdf->AddPage();
        }
    
        $fill=false;
    
        $precios=Array();
        $copagos=Array();
    
    
        foreach($detalle as $art)
        {
            /*
            if(($_SESSION['sgh_usuario_id']*1)==7)
            {
                print("SELECT COALESCE(SUM(-stock_cant),0) AS stock_cant,
            UPPER(COALESCE(f2.func_nombre,f1.func_nombre))AS func,
            COALESCE(b2.bod_glosa,b1.bod_glosa) AS bod_glosa,
            MAX(log_fecha::date)AS fecha,log_fecha::date AS log_fecha
            FROM recetas_detalle 
            LEFT JOIN receta ON receta_id=recetad_receta_id
            LEFT JOIN logs ON log_recetad_id=recetad_id
            LEFT JOIN stock ON stock_log_id=log_id
            LEFT JOIN bodega AS b2 ON b2.bod_id=stock_bod_id
            LEFT JOIN bodega AS b1 ON b1.bod_id=receta_bod_id
            LEFT JOIN funcionario AS f2 ON f2.func_id=log_func_if
            LEFT JOIN funcionario AS f1 ON f1.func_id=receta_func_id
            WHERE recetad_id=".$art['recetad_id']." AND log_fecha::date='".$receta['fecha']."'
            GROUP BY b1.bod_glosa,b2.bod_glosa,f1.func_nombre,f2.func_nombre,log_fecha
            ORDER BY log_fecha DESC");
                
                print("<br /><br /><br /><br /><br />");
                
        
            }
            */
            $desp=cargar_registros_obj("SELECT COALESCE(SUM(-stock_cant),0) AS stock_cant,
            UPPER(COALESCE(f2.func_nombre,f1.func_nombre))AS func,
            COALESCE(b2.bod_glosa,b1.bod_glosa) AS bod_glosa,
            MAX(log_fecha::date)AS fecha,log_fecha::date AS log_fecha
            FROM recetas_detalle 
            LEFT JOIN receta ON receta_id=recetad_receta_id
            LEFT JOIN logs ON log_recetad_id=recetad_id
            LEFT JOIN stock ON stock_log_id=log_id
            LEFT JOIN bodega AS b2 ON b2.bod_id=stock_bod_id
            LEFT JOIN bodega AS b1 ON b1.bod_id=receta_bod_id
            LEFT JOIN funcionario AS f2 ON f2.func_id=log_func_if
            LEFT JOIN funcionario AS f1 ON f1.func_id=receta_func_id
            WHERE recetad_id=".$art['recetad_id']." AND log_fecha::date='".$receta['fecha']."'
            GROUP BY b1.bod_glosa,b2.bod_glosa,f1.func_nombre,f2.func_nombre,log_fecha
            ORDER BY log_fecha DESC");
            
            $pdf->SetFillColor(255,255,255);
            $pdf->SetTextColor(0);
            $pdf->CheckText($art['art_glosa'],100*$ratio,6);
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->SetFont('','B',10*$ratio);
            //$pdf->SetX($x);
            $pdf->Multicell(100*$ratio, 6, $art['art_glosa'], 1, 'LR', false);

            $ysize=($pdf->GetY()-$y)/2;
            $pdf->SetXY($x+(100*$ratio),$y);
            $pdf->Cell(20*$ratio, $ysize, $art['forma_nombre'],1,0,'C');
            $indica=explode('|',html_entity_decode(strip_tags($art['recetad_indicaciones'])));		
            $i1=$indica[0];
            $i2=$indica[1];
            $i3=$indica[2];

            if($art['recetad_horas']*1<=24)
            {
                $div_h=1;
                $txt_horas='horas';
            }
            else
            {
                if(($art['recetad_horas']%24)==0)
                {
                    $div_h=24;
                    $txt_horas='día(s)';
                }
                else
                {
                    $div_h=1;
                    $txt_horas='horas';
                }
            }
            if($art['recetad_dias']*1<=30)
            {
                $div_d=1;
                $txt_dias='día(s).';
            }
            else
            {
                if(($art['recetad_dias']%30)==0)
                {
                    $div_d=30;
                    $txt_dias='mes(es).';
                }
                else
                {
                    $div_d=1;
                    $txt_dias='día(s).';
                }
            }

            if( $art['recetad_cant'] >= 1 )
            {
                $recetad_cant =  $art['recetad_cant'];
                $recetad_cant = number_format($recetad_cant, 1,',','.');
            }
            else
            {
                if( $art['recetad_cant'] == 0.25 )
                    $recetad_cant='1/4';  
                else if ( $art['recetad_cant'] == 0.5 )
                    $recetad_cant='1/2';
                else if ( $art['recetad_cant'] == 0.75 )
                    $recetad_cant='3/4';
                else{
                    $recetad_cant = $art['recetad_cant'];
                    $recetad_cant = number_format($recetad_cant, 1,',','.');
                }
            }

            if($art['indicacion']!='')
            {
                    $txt_dosis=$art['indicacion'];
            }
            else
            {
                $txt_dosis=$recetad_cant." ".$art['art_unidad_administracion']." cada ".($art['recetad_horas']/$div_h)." ".$txt_horas." por ".($art['recetad_dias']/$div_d)." ".$txt_dias.".";
            }

            $pdf->Cell(90*$ratio,$ysize,$txt_dosis,1,0,'C');

            //$cantidad=ceil(((($art['recetad_cant']*24)/$art['recetad_horas'])*$art['recetad_dias']));///$art['art_unidad_cantidad']);
            //valor = Math.ceil((((dias_q*24))/horas_q*(cantidad))/($('relacion_ua').value*1));

            $cantidad=ceil(((($art['recetad_dias']*24)/$art['recetad_horas']*$art['recetad_cant']))/$art['art_unidad_cantidad_adm']*1);
            if($receta['receta_cronica']=='t' )
            {
                $meses=($art['recetad_dias']*1/30);
                $cant_mes=ceil($cantidad/$meses);
                $resto_desp=($cantidad-$total_desp);
                if($resto_desp<$cant_mes)
                {
                    $cant_mes=$resto_dep;

                }
                //$pdf->Cell(30*$ratio,$ysize,$cantidad,1,0,'C');		
            }
            else
            {
                //$pdf->Cell(30*$ratio,$ysize,$cantidad,1,0,'C');		
                $cant_mes=$cantidad;
            }
            if($receta['receta_cronica']=='t')
            {
                for($k=0;$k<sizeof($desp);$k++)
                {
                    $total_desp+=$desp[$k]['stock_cant'];
                }
                //*******************************************
                $ff=explode('/',$receta['receta_fecha']);
                $dia=$ff[0]*1;
                $mes_inicial=$ff[1]*1;
                $anio=$ff[2]*1;
                $meses=($art['recetad_dias']*1/30);
                $cant_desp=$desp[(sizeof($desp)-1)]['stock_cant'];
                $cant_dia=($art['recetad_cant']*(24/$art['recetad_horas']));
                $dias=CEIL($cant_desp/$cant_dia);
                $cant_mes=ceil($cantidad/$meses);
                $resto_desp=($cantidad-$total_desp);
                $txt_fechas='';
                if($resto_desp<$cant_mes)
                {
                    $cant_mes=$resto_desp;
                }	
                $txt_fechas=date('d/m/Y',mktime(0,0,0,$mes_inicial,($dia+$dias),$anio));
                //****************************************************
            }
            $pdf->Cell(30*$ratio,$ysize,($desp[0]['stock_cant']*1),1,0,'C',false);
            $pendiente=($cant_mes-$desp[0]['stock_cant']);
            if($pendiente<0)
                $pendiente=0;

            $pdf->Cell(30*$ratio,$ysize,$pendiente*1,1,0,'C');


                    $observacion = $art['recetad_observacion'];
                    $pdf->Ln();		
                    $pdf->SetX($pdf->GetX()+(100*$ratio));	
                    $pdf->Cell(170*$ratio,$ysize,'Administración: '.$observacion,1,0,'L');		
                    $pdf->Ln();		

            $fill=!$fill;
        }
    
        $pdf->Output();
    }
    else
    {
        
        $pdf = new PDF('P', 'mm', 'Letter');
        for($kk=0;$kk<count($reg_recetas);$kk++)
        {
            
            $receta_id=$reg_recetas[$kk]['receta_id']*1;
            
            
            $receta=cargar_registro("SELECT pac_ficha,pac_rut,pac_nombres,pac_appat||' '||pac_apmat AS pac_apellidos,prev_desc,prev_id,
		doc_rut,doc_nombres||' '||doc_paterno||' '||doc_materno AS doc_nombre,
		diag_desc AS receta_diagnostico,receta_id,receta_diag_cod,diag_desc,
		date_trunc('second', receta_fecha_emision) AS receta_emision, 
		(receta_fecha_emision::date+(((SELECT max(recetad_dias) FROM recetas_detalle WHERE recetad_receta_id=receta_id) || ' days')::interval))::date AS vigente,
		receta_comentarios,COALESCE(receta_cronica, false) AS receta_cronica,tipotalonario_nombre,
		COALESCE(CASE WHEN receta_numero=0 OR receta_numero is null THEN '0' ELSE receta_numero END,receta_id)AS receta_numero,
		receta_tipotalonario_id,centro_nombre,func_nombre AS func,receta_bod_id,
		(SELECT esp_desc FROM nomina_detalle join nomina using (nom_id) join especialidades on esp_id=nom_esp_id
		WHERE nomina_detalle.pac_id=pacientes.pac_id AND nom_doc_id=receta_doc_id 
		ORDER BY nom_fecha DESC,nomd_hora DESC LIMIT 1)AS esp_desc, doc_estamento,e1.esp_desc as esp_doctor,
	CASE WHEN (SELECT max(recetad_dias) FROM recetas_detalle WHERE recetad_receta_id=receta_id)>30 
	THEN (SELECT max(recetad_dias) FROM recetas_detalle WHERE recetad_receta_id=receta_id)/30 
	ELSE 1 END as vigencia,
		COALESCE((SELECT max(log_fecha) FROM logs left join recetas_detalle 
		on recetad_id=log_recetad_id WHERE recetad_receta_id=receta_id),receta_fecha_emision)::date AS fecha
		FROM receta
		LEFT JOIN pacientes ON receta_paciente_id=pac_id
		LEFT JOIN prevision USING (prev_id)
		LEFT JOIN doctores ON receta_doc_id=doc_id
		LEFT JOIN diagnosticos ON receta_diag_cod=diag_cod
		LEFT JOIN receta_tipo_talonario ON receta_tipotalonario_id=tipotalonario_id
		LEFT JOIN centro_costo ON centro_ruta=receta_centro_ruta
		LEFT JOIN funcionario ON func_id=receta_func_id
		LEFT JOIN especialidad_doctor AS d1 USING(doc_id)
		LEFT JOIN especialidades AS e1 USING(esp_id)
		WHERE receta_id=$receta_id
		group by receta_id,doc_rut,doc_nombres,doc_paterno,doc_materno,receta_fecha_emision,receta_comentarios,receta_diag_cod,
		diag_desc,receta_cronica,tipotalonario_nombre,receta_numero,receta_tipotalonario_id,pac_fc_nac,pac_ficha,pac_rut,pac_nombres,pac_appat,
		pac_apmat,receta_diagnostico,prev_desc,prev_id,centro_nombre,func_nombre,receta_bod_id,pacientes.pac_id,receta.receta_doc_id, doc_estamento, esp_doctor
    ");
    
    $consulta="
    SELECT *,
      upper( COALESCE(art_unidad_adm, forma_nombre) ) AS art_unidad_administracion,
	  COALESCE( art_unidad_cantidad, 1 ) AS art_unidad_cantidad_adm,recetad_indicaciones,
	  ar_fonasa_a,ar_fonasa_b,ar_fonasa_c,ar_fonasa_d,ar_particular
	  FROM recetas_detalle 
      JOIN articulo ON recetad_art_id=art_id
      LEFT JOIN bodega_forma ON art_forma=forma_id
	LEFT JOIN bodega ON bod_id=".$receta['receta_bod_id']."
      LEFT JOIN aranceles ON ar_codigo=art_codigo AND bod_glosa ilike '%'||ar_tipo||'%'
      WHERE recetad_receta_id=$receta_id
    ";
    
    
    
    $detalle=cargar_registros_obj($consulta);
    
    
    $mes=$receta['mes'];
    $anio=$receta['anio'];
    $dia=$receta['dia'];
    
    
    //$pdf = new PDF('P','mm',array(311,396));
    
    $pdf->SetFont('Arial','B',16*$ratio);
    
    $c=0; $i=0;
    $bod=cargar_registros_obj("SELECT -stock_cant AS stock_cant,
					UPPER(COALESCE(f2.func_nombre,f1.func_nombre))AS func,
					COALESCE(b2.bod_glosa,b1.bod_glosa) AS bod_glosa,
					MAX(log_fecha::date)AS fecha,log_fecha::date AS log_fecha, COALESCE(b2.bod_id,b1.bod_id) AS bod_id
					FROM recetas_detalle 
					LEFT JOIN receta ON receta_id=recetad_receta_id
					LEFT JOIN logs ON log_recetad_id=recetad_id
					LEFT JOIN stock ON stock_log_id=log_id
					LEFT JOIN bodega AS b2 ON b2.bod_id=stock_bod_id
					LEFT JOIN bodega AS b1 ON b1.bod_id=receta_bod_id
					LEFT JOIN funcionario AS f2 ON f2.func_id=log_func_if
					LEFT JOIN funcionario AS f1 ON f1.func_id=receta_func_id
					WHERE recetad_id=".$detalle[0]['recetad_id']." $log_fecha_q
					GROUP BY stock_cant,b1.bod_glosa,b2.bod_glosa,f1.func_nombre,f2.func_nombre,log_fecha, b1.bod_id, b2.bod_id
					ORDER BY log_fecha ,log_fecha::time ASC");
			
    $bodega=$bod[(sizeof($bod)-1)]['bod_glosa'];
    $bod_id=$bod[(sizeof($bod)-1)]['bod_id']*1;
    if($bod_id==3){
        $pdf->SetAutoPageBreak(true, 100);
    }else{
        $pdf->SetAutoPageBreak(true, 150);
    }
    $pdf->AliasNbPages();
    
    $pdf->AddPage();
    /*
    if($c==0 or $c==3)
    {
      if($c==3)
          $c=0;
      $pdf->AddPage();
    } 
    */
    $fill=false;
    
    $precios=Array();
    $copagos=Array();
    
    
    foreach($detalle as $art)
    {
        $consulta="SELECT COALESCE(SUM(-stock_cant),0) AS stock_cant,
	UPPER(COALESCE(f2.func_nombre,f1.func_nombre))AS func,
	COALESCE(b2.bod_glosa,b1.bod_glosa) AS bod_glosa,
	MAX(log_fecha::date)AS fecha,log_fecha::date AS log_fecha
	FROM recetas_detalle 
	LEFT JOIN receta ON receta_id=recetad_receta_id
	LEFT JOIN logs ON log_recetad_id=recetad_id
	LEFT JOIN stock ON stock_log_id=log_id
	LEFT JOIN bodega AS b2 ON b2.bod_id=stock_bod_id
	LEFT JOIN bodega AS b1 ON b1.bod_id=receta_bod_id
	LEFT JOIN funcionario AS f2 ON f2.func_id=log_func_if
	LEFT JOIN funcionario AS f1 ON f1.func_id=receta_func_id
	WHERE recetad_id=".$art['recetad_id']." AND log_fecha::date='".$receta['fecha']."'
	GROUP BY b1.bod_glosa,b2.bod_glosa,f1.func_nombre,f2.func_nombre,log_fecha
	ORDER BY log_fecha DESC";
        
        
        
        
        
        
        $desp=cargar_registros_obj($consulta);
        $pdf->SetFillColor(255,255,255);
	$pdf->SetTextColor(0);
	$pdf->CheckText($art['art_glosa'],100*$ratio,6);
        $x=$pdf->GetX();
	$y=$pdf->GetY();
        $pdf->SetFont('','B',10*$ratio);
        //$pdf->SetX($x);
        $pdf->Multicell(100*$ratio, 6, $art['art_glosa'], 1, 'LR', false);
        
        $ysize=($pdf->GetY()-$y)/2;
        $pdf->SetXY($x+(100*$ratio),$y);
	$pdf->Cell(20*$ratio, $ysize, $art['forma_nombre'],1,0,'C');
	$indica=explode('|',html_entity_decode(strip_tags($art['recetad_indicaciones'])));		
	$i1=$indica[0];
	$i2=$indica[1];
	$i3=$indica[2];
		
	if($art['recetad_horas']*1<=24)
        {
            $div_h=1;
            $txt_horas='horas';
	}
        else
        {
            if(($art['recetad_horas']%24)==0)
            {
                $div_h=24;
		$txt_horas='día(s)';
            }
            else
            {
                $div_h=1;
		$txt_horas='horas';
            }
	}
	if($art['recetad_dias']*1<=30)
        {
            $div_d=1;
            $txt_dias='día(s).';
	}
        else
        {
            if(($art['recetad_dias']%30)==0)
            {
                $div_d=30;
		$txt_dias='mes(es).';
            }
            else
            {
                $div_d=1;
		$txt_dias='día(s).';
            }
	}
		
	if( $art['recetad_cant'] >= 1 )
        {
            $recetad_cant =  $art['recetad_cant'];
            $recetad_cant = number_format($recetad_cant, 1,',','.');
	}
        else
        {
            if( $art['recetad_cant'] == 0.25 )
                $recetad_cant='1/4';  
            else if ( $art['recetad_cant'] == 0.5 )
                $recetad_cant='1/2';
            else if ( $art['recetad_cant'] == 0.75 )
                $recetad_cant='3/4';
            else {
                $recetad_cant = $art['recetad_cant'];
                $recetad_cant = number_format($recetad_cant, 1,',','.');
            }
	}

	if($art['indicacion']!='')
        {
		$txt_dosis=$art['indicacion'];
	}
        else
        {
		$txt_dosis=$recetad_cant." ".$art['art_unidad_administracion']." cada ".($art['recetad_horas']/$div_h)." ".$txt_horas." por ".($art['recetad_dias']/$div_d)." ".$txt_dias.".";
	}
	
        $pdf->Cell(90*$ratio,$ysize,$txt_dosis,1,0,'C');
        
        //$cantidad=ceil(((($art['recetad_cant']*24)/$art['recetad_horas'])*$art['recetad_dias']));///$art['art_unidad_cantidad']);
        //valor = Math.ceil((((dias_q*24))/horas_q*(cantidad))/($('relacion_ua').value*1));
        
        $cantidad=ceil(((($art['recetad_dias']*24)/$art['recetad_horas']*$art['recetad_cant']))/$art['art_unidad_cantidad_adm']*1);
	if($receta['receta_cronica']=='t' )
        {
            $meses=($art['recetad_dias']*1/30);
            $cant_mes=ceil($cantidad/$meses);
            $resto_desp=($cantidad-$total_desp);
            if($resto_desp<$cant_mes)
            {
                $cant_mes=$resto_dep;
                
            }
            //$pdf->Cell(30*$ratio,$ysize,$cantidad,1,0,'C');		
        }
        else
        {
            //$pdf->Cell(30*$ratio,$ysize,$cantidad,1,0,'C');		
            $cant_mes=$cantidad;
	}
        if($receta['receta_cronica']=='t')
        {
            for($k=0;$k<sizeof($desp);$k++)
            {
                $total_desp+=$desp[$k]['stock_cant'];
            }
            //*******************************************
            $ff=explode('/',$receta['receta_fecha']);
            $dia=$ff[0]*1;
            $mes_inicial=$ff[1]*1;
            $anio=$ff[2]*1;
            $meses=($art['recetad_dias']*1/30);
            $cant_desp=$desp[(sizeof($desp)-1)]['stock_cant'];
            $cant_dia=($art['recetad_cant']*(24/$art['recetad_horas']));
            $dias=CEIL($cant_desp/$cant_dia);
            $cant_mes=ceil($cantidad/$meses);
            $resto_desp=($cantidad-$total_desp);
            $txt_fechas='';
            if($resto_desp<$cant_mes)
            {
                $cant_mes=$resto_desp;
            }	
            $txt_fechas=date('d/m/Y',mktime(0,0,0,$mes_inicial,($dia+$dias),$anio));
            //****************************************************
	}
	$pdf->Cell(30*$ratio,$ysize,($desp[0]['stock_cant']*1),1,0,'C',false);
	$pendiente=($cant_mes-$desp[0]['stock_cant']);
	if($pendiente<0)
            $pendiente=0;
	
        $pdf->Cell(30*$ratio,$ysize,$pendiente*1,1,0,'C');
		
		
		$observacion = $art['recetad_observacion'];
		$pdf->Ln();		
		$pdf->SetX($pdf->GetX()+(100*$ratio));	
		$pdf->Cell(170*$ratio,$ysize,'Administración: '.$observacion,1,0,'L');		
		$pdf->Ln();		
		
        $fill=!$fill;
    }
            
        }
        $pdf->Output('recetas_'.date("Ymd").'.pdf','I');
        
        
        
        
        
        
    }
    
?>
