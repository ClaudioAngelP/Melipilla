<?php
    error_reporting(E_ALL);
    require_once('../conectar_db.php');
    $bod_id=24;
    $fi=explode("\n", utf8_decode(file_get_contents('inventario_farmacia_melipilla_07_01_2014.csv')));
    pg_query("START TRANSACTION;");
    pg_query("INSERT INTO logs VALUES (DEFAULT, 7, 20, '07/01/2014 14:24:00', 0, 0, 0, 'Carga inicial realizada masivamente por Sistemas Expertos.' );");
    for($i=1;$i<sizeof($fi);$i++)
    //for($i=1;$i<2;$i++)
    {
        $r=explode('|',$fi[$i]);
        //----------------------------------------------------------------------
        if(!isset($r[0]) OR trim($r[0])=='')
            continue;
        //----------------------------------------------------------------------
        $art_codigo=trim(strtoupper($r[0]));
        $nom=trim(strtoupper($r[1]));
	$ultimo_valor=(str_replace(",",".",($r[2]))*1);
	if(trim($r[4])=='')
        {
            $stock=0;
        }
	else
	{
            $stock=trim(str_replace('.','',$r[4]))*1;
        }
        ///ACTIVAR SI EN LISTADO ESTAN ESTOS DATOS
        ////////////////////////////////////////////////////////////////////////
        /*
	$clasificacion=trim(strtoupper($r[4]));
	$forma_glosa=trim(strtoupper($r[5]));
	$vence=trim(strtoupper($r[6]));
	$item_cod=trim(strtoupper($r[7]));
	$item_glosa=trim(strtoupper($r[8]));
 	$auge=trim(strtoupper($r[9]));
         * 
         */
        ////////////////////////////////////////////////////////////////////////
	//-----------------------------------------------------------------------------------------
        ///ACTIVAR SI EN LISTADO ESTAN ESTOS DATOS
        ////////////////////////////////////////////////////////////////////////
        /*
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
				print("Error al Ingresar Item Presupuestario Linea ".$i);
				print("<br>");
				
			}
		}
        }
         * 
         */
        ////////////////////////////////////////////////////////////////////////
        //-------------------------------------------------------------------------------------------
        ///ACTIVAR SI EN LISTADO ESTAN ESTOS DATOS
        ////////////////////////////////////////////////////////////////////////
        /*
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
               print("Error al ingresar nueva Forma Linea ".$i);
		 print("<br>");
               
            }
        }
         * 
         */
        ////////////////////////////////////////////////////////////////////////
        ///ACTIVAR SI EN LISTADO ESTAN ESTOS DATOS
        ////////////////////////////////////////////////////////////////////////
        //--------------------------------------------------------------------------------------
        /*
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
	              print("Error al ingresar nueva Clasificacion Linea ".$i);
			print("<br>");
		}
	 }
 	 //--------------------------------------------------------------------------------------
         * 
         */
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        $encontrado=true;
        $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
        if(!$art)
        {
            
            //print($fi[$i]);
            //print('<br>');
		/*
                if($vence=="SI")
		{
			$art_vence='1';
		}
		else
		{
			$art_vence='0';
		}
		if($auge=="S")
		{
			$art_auge='true';
		}
		else
		{
			$art_auge='false';
		}
                * 
                */
            
		//pg_query("INSERT INTO articulo VALUES (DEFAULT, '$art_codigo', '$art_vence', '$nom', '$nom' , $forma_id, $art_auge, $clasificcion_id, false, '$item_codigo', 0, 0, 0, $ultimo_valor, 1, true , 0, false);");
            	//$art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
                print('<br>');
                print("Articulo No Encontrado :|".$i."|".$art_codigo."|".$nom."|".$stock);
                print('<br>');
                $encontrado=false;
                
        }
        if($encontrado)
        {
            $art_id=$art['art_id']*1;
            if($stock==0)
                continue;
            if($art['art_vence']=='1')
                $lote="'28/01/2014'";
            else
                $lote='null';
         
            $total=($stock*$ultimo_valor);
            pg_query("INSERT INTO stock VALUES ( DEFAULT, $art_id, $bod_id, $stock, CURRVAL('logs_log_id_seq'), $lote, $total );");
            $bod_art=cargar_registro("SELECT * FROM articulo_bodega WHERE artb_art_id=$art_id and artb_bod_id=$bod_id");
            if(!$bod_art)
            {
                pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bod_id);");
            }
        }
    }
    $log=cargar_registro("SELECT * FROM logs WHERE log_id=CURRVAL('logs_log_id_seq');");
    echo "(".sizeof($fi).") OK LOG_ID: <B>".$log['log_id']."</B>";
    pg_query("COMMIT");
?>