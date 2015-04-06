<?php
    set_time_limit(0);
    require_once("../../conectar_db.php");
    $codigo = pg_escape_string($_GET['codigo']);
	$bodega = pg_escape_string($_GET['bodega']);
    $bodega_des=pg_escape_string($_GET['bodega_dest']);
	if(isset($_GET['fecha1'])) $fecha1 = pg_escape_string($_GET['fecha1']);
    if(isset($_GET['fecha2'])) $fecha2 = pg_escape_string($_GET['fecha2']);

    // Carga modificadores para calcular stock sugerido...
    $modglobal = ($_GET['modglobal']*1);
    $mods = $_GET['cant']*1;
    for($i=0;$i<($mods-1);$i++)
    {
        $modifica[$i][0]=$_GET['mod'.$i.'_sel'];
        $modifica[$i][1]=$_GET['mod'.$i.'_cant']*1;
    }


    //calcular_stock(art_id, $bodega) AS stock, funcion lanza negativo
    if(isset($fecha1) and isset($fecha2))
    {
        if(strstr($bodega,'.'))
        {
            $gasto_query="0";
        }
        else
        {
            $gasto_query="calcular_gasto(art_id, $bodega, '$fecha1', '$fecha2')";
        }
    }
    else
    {
        $gasto_query='0';
    }
      
    if(strstr($bodega,'.'))
    {
        if($bodega!='.subdireccinmdicoasistencial.laboratorioybcosangre.bancodesangre' and $bodega!='.subdireccinmdicoasistencial.laboratorioybcosangre.laboratorio')
        {
            $tstock='stock_precalculado2';
            $bod_comp="stock_centro_ruta='$bodega'";
            $critico='0';
            $pedido='0';
            $join_critico='';
        }
        else
        {
            if($bodega_des==6)
            {
                $tstock='stock_precalculado';
                $bod_comp="stock_bod_id=6";
                $critico='critico_critico';
                $pedido='critico_pedido';
                $join_critico="LEFT JOIN stock_critico ON
                art_id=critico_art_id AND critico_bod_id=6";
            }
            else
            {
                $tstock='stock_precalculado2';
                $bod_comp="stock_centro_ruta='$bodega'";
                $critico='0';
                $pedido='0';
                $join_critico='';
            }
        }
    }
    else
    {
        $tstock='stock_precalculado';
        $bod_comp="stock_bod_id=$bodega";
        $critico='critico_critico';
        $pedido='critico_pedido';
        $join_critico="LEFT JOIN stock_critico ON
        art_id=critico_art_id AND critico_bod_id=$bodega";
    }
    
    $articulo = pg_query($conn, "
	SELECT *, (-(stock_gasto)+COALESCE($pedido,0)) FROM
    (
    SELECT
    art_id,
    art_codigo,
    art_glosa,
    (select sum(stock_cant) from $tstock 
    where $bod_comp and stock_art_id=art_id) AS stock,
    $gasto_query AS stock_gasto,
    $critico,
    $pedido,
    art_clasifica_id,
    art_val_ult,
    art_prioridad_id
    FROM
    articulo
    $join_critico
    WHERE art_codigo='$codigo'
    ) AS ss
    ");

    $datos = pg_fetch_row($articulo);
	for($i=0;$i<count($datos);$i++)
    {
        $datos[$i]=htmlentities($datos[$i]);
	}
    
    // Agrega modificadores globales a la cantidad sugerida...
      
     $modif=$modglobal;
   
    for($u=0;$u<($mods-1);$u++)
    {
      if($datos[7]==$modifica[$u][0])
      $modif+=$modifica[$u][1];
    }

    if($bodega!='.subdireccinmdicoasistencial.laboratorioybcosangre.bancodesangre' and $bodega!='.subdireccinmdicoasistencial.laboratorioybcosangre.laboratorio')
    {
        $sugerido=(($datos[10]*1)-($datos[3]*1))+(($datos[10]*1)-($datos[3]*1))/100*$modif;
    }
    else
    {
        $sugerido=0;
    }
    if($sugerido<0) $sugerido=0;
    $datos[count($datos)]=$sugerido;
	if (count($datos) > 1)
    {
        print(json_encode($datos));
	}

?>
