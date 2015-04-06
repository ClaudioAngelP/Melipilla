<?php

  require_once('../../conectar_db.php');
  
  $bodega = $_GET['bodega'];
  $item = $_GET['item'];
  $clasifica = $_GET['clasifica'];
  
  if(isset($_GET['controlado'])) {
    $controlados = $_GET['controlado'];
  } else {
    $controlados = 0;
  }
  
  $orden = $_GET['orden'];
  $valorizar = $_GET['valorizar'];
  $valorizar_nro = ($_GET['valorizar']*1);
  
  if(isset($_GET['fecha_ref'])) {
    if(trim($_GET['fecha_ref'])!=date('d/m/Y')) {
      $fecha_ref=$_GET['fecha_ref'];
      $fref=true;
    } else {
      $fecha_ref=date('d/m/Y');
      $fref=false;
    }
  } else {
    $fecha_ref=date('d/m/Y');
    $fref=false;
  }
  
  $infobodega=cargar_registro('SELECT * FROM bodega WHERE bod_id='.$bodega);
  
  if(isset($_GET['xls'])) {
    header("Content-type: application/vnd.ms-excel");
    if(!$fref) {
      header("Content-Disposition: filename=\"InformeStock_".$infobodega['bod_glosa']."---".date('d-m-Y').".XLS\";");
    } else {
      header("Content-Disposition: filename=\"InformeStock_".$infobodega['bod_glosa']."---".str_replace('/','-',$fecha_ref).".XLS\";");
    }
  } else {
  
    print('<html><title>Listado de Saldos Valorizado</title>');
    
    cabecera_popup('../..');
    
    print('<body class="fuente_por_defecto popup_background">');
  
  }
  
  
  $largo_datos=2;
  
  if(isset($_GET['mostrar_item'])) {
    $mostrar_item=true; $largo_datos++;
  } else {
    $mostrar_item=false;
  }
  
  if(isset($_GET['mostrar_forma'])) {
    $mostrar_forma=true; $largo_datos++;
  } else {
    $mostrar_forma=false;
  }
  
  if(isset($_GET['mostrar_clasif'])) {
    $mostrar_clasif=true; $largo_datos++;
  } else {
    $mostrar_clasif=false;
  }
  
  if(isset($_GET['mostrar_lotes'])) {
    $mostrar_lotes=true; $largo_datos+=2;
  } else {
    $mostrar_lotes=false;
  }
  
  if(isset($_GET['mostrar_prioridad'])) {
    $mostrar_prioridad=true; $largo_datos++;
  } else {
    $mostrar_prioridad=false;
  }
  
  $cadena = pg_escape_string($_GET['buscar']);
  
  switch($valorizar) {
    case 0: $valorizar='art_val_min'; break;
    case 2: $valorizar='art_val_max'; break;
    case 3: $valorizar='art_val_ult'; break;
    case 10: $valorizar='0'; break;
    default: $valorizar='art_val_med'; break;
  }
  
  if($bodega!=0) {
    $bodega = ($bodega*1);
  } 
  
  $consulta='';
  $conector='';
  
  if($item!=0) {
    $consulta='WHERE ';
    $item = "(art_item='".$item."')";
    $conector = " AND ";
  } else {
    $item = "";
  }
  
  if($clasifica!=0) {
  $consulta='WHERE ';
  $clasifica = $conector."(art_clasifica_id=".$clasifica.")";
  $conector = " AND ";
  } else {
  $clasifica = "";
  }
  
  if($controlados!=0) {
  $consulta='WHERE ';
  $controlados = $conector."art_controlado";
  $conector = " AND ";
  } else {
  $controlados = "";
  }
  
	if(strlen($cadena)>0) {
  $consulta='WHERE ';
  $cadena = str_replace('*','%',$cadena);
  $cadena_w = $conector." art_codigo LIKE '".$cadena."'";
  $conector= " AND ";
  } else {
  $cadena_w='';
  }


  if(isset($_GET['mostrar_lotes_cero'])) {
    $lotes_en_cero='';
  } else {
    $lotes_en_cero='WHERE stock>0';
  }
  
  if(isset($_GET['mostrar_solo_lotes_cero'])) {
    $lotes_en_cero='WHERE stock=0';
  } 
  
  if(isset($_GET['ascendente'])) {
		$ascen = '';
	} else {
		$ascen='DESC';
	}
  
  switch ($orden) {
				case 0: $orden='articulo.art_codigo '.$ascen; break;
				case 1: $orden='articulo.art_glosa '.$ascen; break;
				case 2: $orden='articulo.art_nombre '.$ascen; break;
				case 3: $orden='articulo.clasifica_nombre '.$acen; break;
				case 4: $orden='articulo.item_glosa '.$ascen; break;
				case 5: 
          if($mostrar_lotes)
            $orden='art_prioridad_id '.$ascen.', articulo.art_codigo'; 
          else
            $orden='articulo.art_prioridad_id';
          break;
	}
  
	
	// Se crea una vista temporal de los datos para ser utilizada
	// por el proceso de mas abajo como tabla de stock.
	
	$q_view="
    
    CREATE TEMP VIEW stock_precalc_temp AS
    	 SELECT 
        stock_bod_id, stock_art_id, stock_vence, 
        SUM(stock_cant) AS stock_cant, 0 AS stock_cant_trans
	     FROM stock 
	     JOIN logs ON stock_log_id=log_id
	     LEFT JOIN pedido ON log_id_pedido=pedido_id
	     LEFT JOIN pedido_detalle 
		    ON pedido_detalle.pedido_id=pedido.pedido_id 
		    AND pedido_detalle.art_id=stock_art_id
	     WHERE (
	     ((pedido_estado=0 OR pedido_estado=1) OR 
       (COALESCE(pedidod_estado,false))
	     OR
	     (pedido_estado=2 OR pedido_estado IS NULL)))
	     AND date_trunc('day',log_fecha)<='".$fecha_ref."'
	     AND stock_bod_id=".$bodega."
	     GROUP BY stock_bod_id, stock_art_id, stock_vence;

  ";
    
	pg_query($conn, $q_view);
	
  $q_table = 'stock_precalc_temp';
	
	// Listado sin Lotes	
	
	if(!$mostrar_lotes) {
	
	 if($valorizar=='0') {
    $groupby = '';
   } else {
    $groupby = $valorizar.',';
   }
   
  $query="
		SELECT *, 
    (ss.stock*ss.unitario) AS total
		
		FROM
		
		(    
    
    SELECT 
    art_id,
		art_codigo,
		art_glosa,
		COALESCE(clasifica_nombre, '(No Asignado...)'),
		COALESCE(item_codigo, '(No Asignado...)'),
	  COALESCE(SUM(stock_cant),0) AS stock,
    $valorizar AS unitario,
    clasifica_nombre,
    item_glosa,
    COALESCE(forma_nombre, '(No Asignado...)'),
    art_prioridad_glosa
		
		FROM articulo
		
		LEFT JOIN $q_table ON stock_art_id=art_id AND stock_bod_id=$bodega
    LEFT JOIN bodega_clasificacion
		ON clasifica_id=art_clasifica_id
		LEFT JOIN item_presupuestario
		ON item_codigo=art_item
		LEFT JOIN bodega_forma
		ON art_forma=forma_id
		LEFT JOIN art_prioridad ON articulo.art_prioridad_id=art_prioridad.art_prioridad_id
		
		$consulta
		$item
		$clasifica
		$controlados
		$cadena_w
		
		GROUP BY 
    art_id, art_codigo, art_glosa, 
    clasifica_nombre, item_codigo, art_prioridad_glosa, 
    $groupby 
    clasifica_nombre, item_glosa, forma_nombre
		
    ) AS ss
		
		LEFT JOIN articulo ON ss.art_id=articulo.art_id
		
    $lotes_en_cero
		
    ORDER BY $orden
		
		";
   
  $listado = pg_query($conn, $query);
		
		} else {
  
  	// Listado con Lotes
		
		$query="
		SELECT 
      articulo.*, (articulo.stock*articulo.unitario) AS total
		
		FROM
		(
		SELECT 
    ss.stock_art_id AS art_id,
    art_codigo,
		art_glosa,
		COALESCE(clasifica_nombre, '(No Asignado...)') AS clasifica_nombre2,
		COALESCE(item_glosa, '(No Asignado...)') AS item_glosa2,
		".$q_table.".stock_cant AS stock,
    $valorizar AS unitario,
    clasifica_nombre,
    item_glosa,
    COALESCE(forma_nombre, '(No Asignado...)') AS forma_nombre,
    ss.stock_vence,
    ss.art_prioridad_id,art_prioridad_glosa
    
		FROM
		
		(    
		
    SELECT DISTINCT
    
    stock_art_id,
		stock_vence,
		art_codigo,art_glosa,art_nombre,
    art_clasifica_id,art_item,art_forma,art_prioridad_id,
    art_val_min, art_val_max, art_val_ult, art_val_med
		
		FROM stock
		JOIN articulo ON stock_art_id=articulo.art_id
		
		WHERE 
    stock_bod_id=$bodega
		
		) AS ss
		
		LEFT JOIN ".$q_table." ON 
        ".$q_table.".stock_art_id=ss.stock_art_id AND 
        ".$q_table.".stock_bod_id=$bodega AND
        ".$q_table.".stock_vence=ss.stock_vence
    LEFT JOIN bodega_clasificacion
		ON clasifica_id=ss.art_clasifica_id
		LEFT JOIN item_presupuestario
		ON item_codigo=ss.art_item
		LEFT JOIN bodega_forma
		ON ss.art_forma=forma_id
		LEFT JOIN art_prioridad 
    ON ss.art_prioridad_id=art_prioridad.art_prioridad_id
		
		$consulta
		$item
		$clasifica
		$controlados
		$cadena_w
		
		) AS articulo
  
    $lotes_en_cero
  
    ORDER BY $orden, stock_vence ASC
		  
    
    ";
    
    $listado = pg_query($conn, $query);
		
		}
		
		if(isset($_GET['xls'])) {
      $xls_table_style='border=1';
    } else {
      $xls_table_style='';
    }
		
    print("
    <table width=100% class='tabla_informe' $xls_table_style >
    ");
    
    if(isset($_GET['xls'])) {
    
      if(!$fref)
        print("<tr><td colspan=".($largo_datos+1)." style='text-align: right;'>
        Fecha de Referencia: <b>".date("d/m/Y")."</b></td></tr>");
      else
        print("<tr><td colspan=".($largo_datos+1)." style='text-align: right;'>
        Fecha de Referencia: <b>".$fecha_ref."</b></td></tr>");
        
    }
    
    print("
    <tr class='tabla_header' style='font-weight: bold;'>
    <td colspan=".$largo_datos.">Datos del Producto</td>
    <td rowspan=2>Stock</td>");
    
    if($valorizar!='0')
    print("<td colspan=2>Valor Neto ($)</td>");
    
    print("
    </tr>
    <tr class='tabla_header' style='font-weight: bold;'>
    <td>C&oacute;digo Int.</td>
    <td>Glosa</td>
    ");
    
    if($mostrar_clasif)
    print("<td>Clasificaci&oacute;n</td>");
    
    if($mostrar_forma)
    print("<td>Forma Farmac&eacute;utica</td>");
    
    if($mostrar_item)
    print("<td>Item Presupuestario</td>");
    
    if($mostrar_prioridad AND $mostrar_lotes)
    print("<td>Prioridad</td>");
    
    if($mostrar_lotes)
    print("<td>Fecha de Venc.</td><td>Stock</td>");
    
    if($mostrar_prioridad AND !$mostrar_lotes)
    print("<td>Prioridad</td>");
    
    
    if($valorizar!='0')
    print("
    <td>P. Unit.</td>
    <td>Subtotal</td>
    ");
    
    print("</tr>");
		
		$total=0;
		
    
    // Suma los lotes individuales para dar un stock general...
    
    if($mostrar_lotes) {

		$prev_cod=null;
		$contador=0;
		$contador_cant=0;
		$registro_inicial=0;
		
		for($u=0;$u<pg_num_rows($listado);$u++) {
    
      $fila_array[$u] = pg_fetch_row($listado, $u);
      
      if($fila_array[$u][0]==$prev_cod) {
        
        $contador+=$fila_array[$u][5];
        $contador_cant++;
      
      } else {
      
        $fila_array[$registro_inicial][15]=$contador;
        $fila_array[$registro_inicial][16]=$contador_cant;
        
        $contador=0;
        $contador_cant=0;

        $registro_inicial=$u;
        $prev_cod=$fila_array[$u][0];
        
        $contador+=$fila_array[$u][5];
        $contador_cant++;
        
        
      }
      
    }
    
    $fila_array[$registro_inicial][15]=$contador;
    $fila_array[$registro_inicial][16]=$contador_cant;
        
    
    }
		
		$clase='tabla_fila';
		
		if($mostrar_lotes) $j=0;
		
		for($i=0;$i<pg_num_rows($listado);$i++) {
      
      if(!$mostrar_lotes)
        $fila = pg_fetch_row($listado, $i);
      else
        $fila = $fila_array[$i];
     
      if(!$mostrar_lotes) {
        if($i%2==0)  
          $clase='tabla_fila';
        else
          $clase='tabla_fila2';
      } else {
        if($j%2==0)  
          $clase='tabla_fila';
        else
          $clase='tabla_fila2';
      
      } 
      
      $textocolor='';
      
      if(!$mostrar_lotes) {
        $subtotal=($fila[5]*$fila[6]);
        $total+=($fila[5]*$fila[6]);
      } else {
        if(isset($fila[15])) {
          $subtotal=($fila[15]*$fila[6]);
          $total+=($fila[15]*$fila[6]);
        }
      }
      
      print("
      <tr class='".$clase."' 
      style='
      $textocolor
      '
      >
      <td style='text-align: right;'><b><i>".$fila[1]."</i></b></td>
      <td><b>".htmlentities($fila[2])."</b></td>");
      
      if($mostrar_clasif) print("<td>".htmlentities($fila[3])."</td>");
      if($mostrar_forma) print("<td>".htmlentities($fila[9])."</td>");
      if($mostrar_item) print("<td>".htmlentities($fila[4])."</td>");
      if($mostrar_prioridad AND $mostrar_lotes)
        print('<td>'.htmlentities($fila[12]).'</td>');
        
      if($mostrar_lotes) 
      print("
      <td style='text-align: center; font-style: italic;'>
      ".htmlentities($fila[10])."
      </td>
      <td style='text-align: right;'>".number_formats($fila[5])."</td>
      ");
      
      if($mostrar_prioridad AND !$mostrar_lotes) 
        print('<td>'.htmlentities($fila[10]).'</td>');
      
        
      if($mostrar_lotes) {
        if(isset($fila[15])) {
          if($fila[15]>0) {
            
            print("
            <td style='text-align: right;' rowspan=".$fila[16].">
            ".number_formats($fila[15])."
            </td>
            ");
            
            if($valorizar!='0')
            print("
            <td style='text-align: right;' rowspan=".$fila[16].">
            \$".number_formats($fila[6]).".-</td>
            <td style='text-align: right;' rowspan=".$fila[16].">
            \$".number_formats($subtotal).".-</td>");
            
            print("</tr>");
            
            $cantidad_filas=$fila[16];
            
          }
          
          } else {
          
          $cantidad_filas=1;
          
          }
          
      } else {
        
        $cantidad_filas=1;
        
        print("
          <td style='text-align: right;'>
          ".number_formats($fila[5])."
          </td>
          ");
        
        if($valorizar!='0')
          print("
          <td style='text-align: right;'>
          \$".number_formats($fila[6]).".-</td>
          <td style='text-align: right;'>
          \$".number_formats($subtotal).".-</td>");
          
        print("</tr>"); 
      }    
      
    }
		
		
		if($valorizar!='0') {
		  
      $total_iva=floor(($total*1.19))-$total;
		  $total_total=floor(($total*1.19));
		
		
		  print("
      <tr><td rowspan=3 style='text-align: center;' class='tabla_header' 
      colspan=".($largo_datos+1)." width='75%'>
      <b>Totales Generales</b></td>	
			<td width=100 style='text-align: right;' class='tabla_header'>
      <b>Neto:</b></td>		
			<td width=100 style='text-align: right;' class='tabla_header'>
      <b>\$".number_formats($total).".-</b></td></tr>
			<tr><td style='text-align: right;' class='tabla_header'>
      <b>I.V.A.:</b></td>				
			<td style='text-align: right;' class='tabla_header'>
      <b>\$".number_formats($total_iva).".-</b></td></tr>
			<tr><td style='text-align: right;' class='tabla_header'>
      <b>Total:</b></td>					
			<td style='text-align: right;' class='tabla_header'>
      <b>\$".number_formats($total_total).".-</b></td></tr>");
    
    }
      
    print("</table>");
    
    if(!isset($_GET['xls'])) {
      print('</body></html>');
    }


?>
