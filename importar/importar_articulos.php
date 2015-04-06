<?php 
    require_once('../conectar_db.php');
    $fa=explode("\n",utf8_decode(file_get_contents('Codigo Bodega Farmacia 1.csv')));
    pg_query("START TRANSACTION;");
    for($i=1;$i<sizeof($fa);$i++)
    {
        print("<br>");
        print("LINEA $i");
        print("<br>");
        $r=explode(';',pg_escape_string(($fa[$i])));
	$cod=trim(strtoupper($r[0]));
	$nom=trim(strtoupper($r[1]));
	$forma=trim(strtoupper($r[2]));
	$clase='';
        $item_cod=trim(strtoupper($r[4]));
        $desc_item=trim(strtoupper($r[5]));
        $vence=trim($r[3]);
        $auge=trim($r[6]*1);
        if($auge==0)
        {
            $auge='false';
        }
        else
        {
            $auge='true';
        }
        $controlado=trim($r[7]);
        //----------------------------------------------------------------------
	$f=cargar_registro("SELECT * FROM bodega_forma WHERE forma_nombre='$forma'");
        if($f)
        {
            $forma_id=$f['forma_id'];
	}
        else
        {
            print("<br>");
            print("INSERT INTO bodega_forma VALUES (DEFAULT, '$forma');");
            print("<br>");
            pg_query("INSERT INTO bodega_forma VALUES (DEFAULT, '$forma');");
            $forma_id="CURRVAL('bodega_forma_forma_id_seq')";
	}
        //----------------------------------------------------------------------
        $c=cargar_registro("SELECT * FROM bodega_clasificacion WHERE clasifica_nombre='$clase'");
        if($c)
        {
            $clase_id=$c['clasifica_id'];
	}
        else
        {
            print("<br>");
            print("INSERT INTO bodega_clasificacion VALUES (DEFAULT, '$clase', '');");
            print("<br>");
            pg_query("INSERT INTO bodega_clasificacion VALUES (DEFAULT, '$clase', '');");
            $clase_id="CURRVAL('bodega_clasificacion_clasifica_id_seq')";
	}
        //----------------------------------------------------------------------
        print("<br>");
        print("codigo='$cod' Nombre='$nom'");
        print("<br>");
        print("select * from item_presupuestario where item_codigo='$item_cod'");
        print("<br>");
        
        $c=cargar_registro("select * from item_presupuestario where item_codigo='$item_cod'");
        if(!$c)
        {
            print("<br>");
            print("INSERT INTO item_presupuestario VALUES ($item_cod, '$desc_item', null);");
            print("<br>");
            pg_query("INSERT INTO item_presupuestario VALUES ($item_cod, '$desc_item', null);");
	}
        //----------------------------------------------------------------------
	$chk=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");
        if(!$chk)
        {
            print("<br>");
            print("INSERT INTO articulo VALUES (DEFAULT,'$cod','$vence','$nom', '$nom', $forma_id, $auge, $clase_id, false, '$item_cod', 0, 0, 0, 0, 1,true, $controlado, false);");
            print("<br>");
            pg_query("INSERT INTO articulo VALUES (DEFAULT,'$cod','$vence','$nom', '$nom', $forma_id, $auge, $clase_id, false, '$item_cod', 0, 0, 0, 0, 1,true, $controlado, false);");
        }
        else
        {
            print("<br>");
            print("Articulo encontrado: $cod");
            print("<br>");
        }
    }
    pg_query("COMMIT;");
?>