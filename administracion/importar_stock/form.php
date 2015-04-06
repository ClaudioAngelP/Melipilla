<?php
    require_once('../../conectar_db.php');
    //require_once('../../config.ph');
    //require_once('../../../conectores/sigh.php');
    $opt_bodegas=desplegar_opciones_sql("SELECT bod_id, bod_glosa FROM bodega ORDER BY bod_glosa", NULL, '', '');
    if($_FILES['file']['name'] != '')
    {
        //require_once('../../config.ph');
        //require_once('../../conectores/sigh.php');
        $bodega_destino=$_POST['bodega_destino']*1;
        if($bodega_destino==-1)
        {
            print("Debe Seleccionar Bodega Destino");
            die();
        }
        require_once 'reader/Classes/PHPExcel/IOFactory.php';
        function get_cell($cell, $objPHPExcel)
        {
            //select one cell
            $objCell = ($objPHPExcel->getActiveSheet()->getCell($cell));
            //get cell value
            return $objCell->getvalue();
        }
        function pp(&$var)
        {
            $var = chr(ord($var)+1);
            return true;
        }
        $name  = $_FILES['file']['name'];
        $tname = $_FILES['file']['tmp_name'];
        $type  = $_FILES['file']['type'];
        pg_query("START TRANSACTION;");
        pg_query("INSERT INTO logs VALUES (DEFAULT, 7, 20, '03/02/2014 00:00:00', 0, 0, 0, 'Carga inicial realizada masivamente por Sistemas Expertos Bodega Farmacia CAE.' );");
        print("<div class='sub-content2' style='overflow:auto;' id='listado_stock'>");
        if($type == 'application/vnd.ms-excel')
        {
            // Extension excel 97
            $ext = 'xls';
	}
	else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
	{
            // Extension excel 2007 y 2010
            $ext = 'xlsx';
	}
        else
        {
            // Extension no valida
            echo -1;
            exit();
	}
        $xlsx = 'Excel2007';
	$xls  = 'Excel5';
        //creando el lector
	$objReader = PHPExcel_IOFactory::createReader($$ext);
        //cargamos el archivo
	$objPHPExcel = $objReader->load($tname);
        $dim = $objPHPExcel->getActiveSheet()->calculateWorksheetDimension();
        // list coloca en array $start y $end
	list($start, $end) = explode(':', $dim);
        if(!preg_match('#([A-Z]+)([0-9]+)#', $start, $rslt))
        {
            return false;
	}
	list($start, $start_h, $start_v) = $rslt;
	if(!preg_match('#([A-Z]+)([0-9]+)#', $end, $rslt))
        {
            return false;
	}
	list($end, $end_h, $end_v) = $rslt;
        //empieza  lectura vertical
	
	for($v=$start_v; $v<=$end_v; $v++)
        {
            //empieza lectura horizontal
            if($v==1)
                continue;
            for($h=$start_h; ord($h)<=ord($end_h); pp($h))
            {
                $cellValue = get_cell($h.$v, $objPHPExcel);
                if($h=="A")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $art_codigo="";
                    }
                    else
                    {
                        $art_codigo=trim(strtoupper(htmlentities($cellValue)));
                    }
                }
                if($h=="B")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $nom="";
                    }
                    else
                    {
                        $nom=trim(strtoupper(htmlentities($cellValue)));
                        $nom=trim(str_replace(',','.',$nom));
                    }
                }
                if($h=="C")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $ultimo_valor="";
                    }
                    else
                    {
                        $ultimo_valor=trim(str_replace(",",".",($cellValue))*1);
                    }
                }
                if($h=="D")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $stock=0;
                    }
                    else
                    {
                        $stock=trim(str_replace('.','',$cellValue))*1;
                    }
                }
                if($h=="E")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $forma_glosa="";
                    }
                    else
                    {
                        $forma_glosa=trim(strtoupper(htmlentities($cellValue)));
                    }
                }
                if($h=="F")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $clasificacion="";
                    }
                    else
                    {
                        $clasificacion=trim(strtoupper(htmlentities($cellValue)));
                    }
                    
                }
                if($h=="G")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $vence="";
                    }
                    else
                    {
                        $vence=trim(strtoupper(htmlentities($cellValue)));
                    }
                    
                }
                if($h=="H")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $item_cod="";
                    }
                    else
                    {
                        $item_cod=trim(strtoupper($cellValue));
                    }
                }
                if($h=="I")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $item_glosa="";
                    }
                    else
                    {
                        $item_glosa=trim(strtoupper(htmlentities($cellValue)));
                    }
                }
                if($h=="J")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $auge="";
                    }
                    else
                    {
                        $auge=trim(strtoupper($cellValue));
                    }
                }
                if($h=="K")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $controlado="";
                    }
                    else
                    {
                        $controlado=trim(strtoupper($cellValue));
                    }
                }
                if($h=="L")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $tipo_controlado="";
                    }
                    else
                    {
                        $tipo_controlado=trim(strtoupper(htmlentities($cellValue)));
                    }
                }
                if($h=="M")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $fecha_vencimiento="";
                    }
                    else
                    {
                        $fecha_vencimiento=trim($cellValue);
                    }
                }
                if($h=="N")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $lote_serie="";
                    }
                    else
                    {
                        $lote_serie=trim(strtoupper(htmlentities($cellValue)));
                    }
                }
                if($h=="O")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $stock_critico=0;
                    }
                    else
                    {
                        $stock_critico=trim(str_replace('.','',$cellValue))*1;
                    }
                }
                if($h=="P")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $punto_pedido=0;
                    }
                    else
                    {
                        $punto_pedido=trim(str_replace('.','',$cellValue))*1;
                    }
                }
                if($h=="Q")
                {
                    if($cellValue == null Or $cellValue == '')
                    {
                        $consumo_mensual=0;
                    }
                    else
                    {
                       $consumo_mensual=trim(str_replace('.','',$cellValue))*1;
                    }
                }
            }
            if(!isset($art_codigo) OR trim($art_codigo)=='')
                continue;
            
            //-----------------------------------------------------------------------------------------
            $item_reg=cargar_registro("select * from item_presupuestario where item_codigo='$item_cod'");
            if($item_reg)
            {
                $item_codigo=$item_reg['item_codigo'];
            }
            else
            {
                $item_reg=cargar_registro("select * from item_presupuestario where item_glosa='$item_glosa'");
	 	if($item_reg)
        	{
		   $item_codigo=$item_reg['item_codigo'];
		}
		else
		{
                    pg_query("INSERT INTO item_presupuestario VALUES (DEFAULT, '$item_glosa',null);");
                    $item_reg=cargar_registro("select * from item_presupuestario where item_glosa='$item_glosa'");
                    if($item_reg)
                    {
                        $item_codigo=$item_reg['item_codigo'];
                    }
                    else
                    {
                        print("<br>");
			print("Error al Ingresar Item Presupuestario Linea ".$v);
			print("<br>");
                    }
		}
            }
            //-------------------------------------------------------------------------------------------
            $f=cargar_registro("SELECT * FROM bodega_forma WHERE forma_nombre='$forma_glosa';");
            if($f)
            {
                $forma_id=$f['forma_id'];
            }
            else
            {
                pg_query("INSERT INTO bodega_forma VALUES (DEFAULT, '$forma_glosa');");
                $f=cargar_registro("SELECT * FROM bodega_forma WHERE forma_nombre='$forma_glosa';");
                if($f)
                {
                    $forma_id=$f['forma_id'];
                }
                else
                {
                    print("<br>");
                    print("Error al ingresar nueva Forma Linea ".$v);
                    print("<br>");
                }
            }
            //-------------------------------------------------------------------------------------------
            $clasif=cargar_registro("SELECT * FROM bodega_clasificacion WHERE clasifica_nombre='$clasificacion';");
            if($clasif)
            {
                $clasificcion_id=$clasif['clasifica_id'];
            }
            else
            {
                pg_query("INSERT INTO  bodega_clasificacion VALUES (DEFAULT, '$clasificacion');");
		$clasif=cargar_registro("SELECT * FROM bodega_clasificacion WHERE clasifica_nombre='$clasificacion';");
        	if($clasif)
        	{
                    $clasificcion_id=$clasif['clasifica_id'];
	 	}
		else
		{
                    print("<br>");
	            print("Error al ingresar nueva Clasificacion Linea ".$v);
                    print("<br>");
		}
            }
            //-------------------------------------------------------------------------------------------
            $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
            if(!$art)
            {
                if($vence=="SI")
		{
			$art_vence='1';
		}
		else
		{
			$art_vence='0';
		}
		if($auge=="SI")
		{
			$art_auge='true';
		}
		else
		{
			$art_auge='false';
		}
                if($controlado=="SI")
		{
                    $control="1";
                    if($tipo_controlado=="Estupefaciente")
                    {
                        $control="1";
                    }
                    else
                    {
                        $control="2";
                    }
                }
                else
                {
                    $control="0";
                }
                pg_query("INSERT INTO articulo(
                art_id,
                art_codigo,
                art_vence,
                art_glosa,
                art_nombre,
                art_forma,
                art_auge,
                art_clasifica_id,
                art_reposicion,
                art_item,
                art_val_min,
                art_val_med,
                art_val_max,
                art_val_ult,
                art_prioridad_id,
                art_activado, 
                art_control,
                art_arsenal,
                art_unidad_adm,
                art_unidad_cantidad, 
                art_tipo_adm,
                art_via)
                VALUES (
                DEFAULT,
                '$art_codigo',
                '$art_vence',
                '$nom', 
                '$nom',
                $forma_id, 
                $art_auge,
                $clasificcion_id,
                false,
                '$item_codigo',
                0, 
                0,
                0,
                $ultimo_valor,
                1,
                true, 
                $control, 
                false, 
                null,
                null, 
                null,
                null);");
                $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
            }
            $art_id=$art['art_id']*1;
            
            if($stock==0)
                continue;
            if($art['art_vence']=='1')
                $lote="'$fecha_vencimiento'";
            else
                $lote='null';
            
            
            
            $total=($stock*$ultimo_valor);
            print("<br>");
            print("Linea ".$v);
            print("<br>");
            print("INSERT INTO stock VALUES ( DEFAULT, $art_id, $bodega_destino, $stock, CURRVAL('logs_log_id_seq'), $lote, $total );");
            print("<br>");
            pg_query("INSERT INTO stock VALUES ( DEFAULT, $art_id, $bodega_destino, $stock, CURRVAL('logs_log_id_seq'), $lote, $total );");
            
            if($lote_serie!="")
            {
                pg_query("INSERT INTO stock_refserie VALUES (default, CURRVAL('stock_stock_id_seq'), '$lote_serie');");
            }
            
            $bod=cargar_registro("SELECT * FROM articulo_bodega WHERE artb_art_id=$art_id and artb_bod_id=$bodega_destino");
            if(!$bod)
            { 
                pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bodega_destino);");
            }
          
            $critico=$stock_critico*1;
            $pedido=$punto_pedido*1;
            $gasto=$critico+$pedido;
            //$gasto=$consumo_mensual*1;
            if($gasto!=0)
            {
                $chk=cargar_registro("SELECT * FROM stock_critico WHERE critico_bod_id=$bodega_destino AND critico_art_id=$art_id");
                if(!$chk)
                    pg_query("INSERT INTO stock_critico VALUES ($art_id, $pedido, $critico, $bodega_destino, $gasto);");
                else
                    pg_query("UPDATE stock_critico SET critico_pedido=$pedido, critico_critico=$critico, critico_gasto=$gasto WHERE critico_bod_id=$bodega_destino AND critico_art_id=$art_id");
            }
	}
     print("</div>");
     pg_query("COMMIT");
}
else
{
 ?>
<center>
<div class='sub-content' style='width:880px;'>
<form name="frmload" method="post" action="administracion/importar_stock/form.php" enctype="multipart/form-data">
    <div class="sub-content"><img src="iconos/wand.png"> 
        <b>Importar Inventario y Stock</b>
    </div>
    <div class="sub-content">
        <table>
            <tr>
                <td>
                    Bodega Destino:
                </td>
                <td>
                    <select name='bodega_destino' id='bodega_destino'>
                        <option value=-1 selected>(Seleccione Bodega...)</option>
                        <?php echo $opt_bodegas; ?>
                    </select>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <input type="file" name="file" />
                        <!--<input type='button' id='btn_cargar' name='btn_cargar' value='-- Cargar --' onClick='cargar_document();' />-->
                    <input type="submit" value="----- IMPORTAR -----" />
                </td>
            </tr>
        </table>
        
    </div>
</form>
</div>
</center>
<?php
 }
 ?>
<!--
<h3>Seleccionar archivo Excel</h3>
<form name="frmload" method="post" action="index.php" enctype="multipart/form-data">
    <input type="file" name="file" />       <input type="submit" value="----- IMPORTAR -----" />
</form>
-->