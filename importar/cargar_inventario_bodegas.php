<?php
    error_reporting(E_ALL);
    require_once('../conectar_db.php');
    $bod_id=35;
    $fi=explode("\n", file_get_contents('stock_farmacia_24.csv'));
    pg_query("START TRANSACTION;");
    pg_query("INSERT INTO logs VALUES (DEFAULT, 7, 20, now(), 0, 0, 0, 'Carga inicial realizada masivamente por Sistemas Expertos en farmacia 24.' );");
    for($i=1;$i<sizeof($fi);$i++)
    //for($i=1;$i<2;$i++)
    {
        print("<br>");
        print("LINEA: ".$i);
        print("<br>");
        
        $r=explode(';',$fi[$i]);
        //----------------------------------------------------------------------
        if(!isset($r[0]) OR trim($r[0])=='')
        {
            print("<br>");
            print("No tiene Codigo");
            print("<br>");
            continue;
        }
        //----------------------------------------------------------------------
        $art_codigo=trim(strtoupper($r[0]));
        $nom=trim(strtoupper($r[1]));
        $ultimo_valor=0;
	if(trim($r[3])=='' || trim($r[3])=='0')
        {
            $stock=0;
        }
	else
	{
            $stock=trim(str_replace('.','',$r[3]))*1;
        }
        
        if(trim($r[2])=='NO' || trim($r[2])=='')
        {
            $vence='null';
        }
        else
        {
            $vence=trim(str_replace('-','/',$r[2]));
        }
        //----------------------------------------------------------------------
        
        /*
        if(trim($r[5])=='')
        {
            $lote='';
        }
        else
        {
            $lote=trim($r[5]);
        }
         * 
         */
        //----------------------------------------------------------------------
        /*
        if(trim(str_replace('.','',$r[7]))=="")
        {
            $punto_pedido=0;
        }
        else
        {
            $punto_pedido=trim(str_replace('.','',$r[7]))*1;
        }
        
        if(trim(str_replace('.','',$r[6]))=="")
        {
            $punto_critico=0;
        }
        else
        {
            $punto_critico=trim(str_replace('.','',$r[6]))*1;
        }
        
        
        if(trim(str_replace('.','',$r[8]))=="")
        {
            $gasto_mensual=0;
        }
        else
        {
            $gasto_mensual=trim(str_replace('.','',$r[8]))*1;
        }
        */
        
        
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
        $art=cargar_registro("SELECT * FROM articulo WHERE upper(art_codigo)=upper('$art_codigo')");
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
            /*
            print("<br>");
            print("Codigo= ". $art_codigo);
            print("<br>");
            print("update articulo set art_control=$controlado where art_id=$art_id;");
            print("<br>");
            print("<br>");
            pg_query("update articulo set art_control=$controlado where art_id=$art_id;");
             * 
             */
            
            if($stock==0)
                continue;
            /*
            if($art['art_vence']=='1')
                $lote="'31/12/2014'";
            else
                $lote='null';
             * 
             */
            
            
            $total=($stock*$ultimo_valor);
            print("<br>");
            print("<br>");
            print("INSERT INTO stock VALUES ( DEFAULT, $art_id, $bod_id, $stock, CURRVAL('logs_log_id_seq'), $vence, $total );");
            print("<br>");
            print("<br>");
            if($vence!="null")
                pg_query("INSERT INTO stock VALUES ( DEFAULT, $art_id, $bod_id, $stock, CURRVAL('logs_log_id_seq'), '$vence', $total );");
            else
                pg_query("INSERT INTO stock VALUES ( DEFAULT, $art_id, $bod_id, $stock, CURRVAL('logs_log_id_seq'), $vence, $total );");
            
            print("<br>");
            print("<br>");
            /*
            if($lote!="")
            {
                print("INSERT INTO stock_refpartida VALUES ( DEFAULT, CURRVAL('stock_stock_id_seq'), '$lote');");
                pg_query("INSERT INTO stock_refpartida VALUES ( DEFAULT, CURRVAL('stock_stock_id_seq'), '$lote');");
            }
             * 
             */
            print("<br>");
            print("<br>");
            
            $bod_art=cargar_registro("SELECT * FROM articulo_bodega WHERE artb_art_id=$art_id and artb_bod_id=$bod_id");
            if(!$bod_art)
            {
                print("<br>");
                print("<br>");
                print("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bod_id);");
                pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bod_id);");
                print("<br>");
                print("<br>");
            }
            /*
            if($gasto_mensual!=0)
            {
                $grabar=true;
                if($grabar)
                {
                    $chk=cargar_registro("SELECT * FROM stock_critico WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
                    if(!$chk)
                        pg_query("INSERT INTO stock_critico VALUES ($art_id, $punto_pedido, $punto_critico, $bod_id, $gasto_mensual);");
                    else
                        pg_query("UPDATE stock_critico SET critico_pedido=$punto_pedido, critico_critico=$punto_critico, critico_gasto=$gasto_mensual WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
                }
                else
                {
                    print("<br>");
                    print("No se ha grabado el articulo : ".$art_codigo);
                    print("<br>");
                }
            }
             * 
             */
            
        }
    }
    $log=cargar_registro("SELECT * FROM logs WHERE log_id=CURRVAL('logs_log_id_seq');");
    echo "(".sizeof($fi).") OK LOG_ID: <B>".$log['log_id']."</B>";
    pg_query("COMMIT");
?>