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
  
  if(isset($_GET['mostrar_lotes_cero'])) {
    $lotes_en_cero='';
  } else {
    if($mostrar_lotes)    $lotes_en_cero='stock>0 AND';
    else                  $lotes_en_cero='WHERE stock>0';
  }
  
  
  
  
  $cadena = pg_escape_string($_GET['buscar']);
  
  switch($valorizar) {
    case 0: $valorizar='articulo.art_val_min'; break;
    case 2: $valorizar='articulo.art_val_max'; break;
    case 3: $valorizar='articulo.art_val_ult'; break;
    default: $valorizar='articulo.art_val_med'; break;
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
  $cadena_w = $conector."art_codigo LIKE '".$cadena."'";
  } else {
  $cadena_w='';
  }
  
  
  
  switch ($orden) {
				case 0: $orden='articulo.art_codigo'; break;
				case 1: $orden='articulo.art_glosa'; break;
				case 2: $orden='articulo.art_nombre'; break;
				case 3: $orden='clasifica_nombre'; break;
				case 4: $orden='item_glosa'; break;
	}
  
  if(isset($_GET['ascendente'])) {
		$ascen = '';
	} else {
		$ascen='DESC';
	}
		
	// Listado sin Lotes	
	
	if(!$mostrar_lotes) {
	
  $listado = pg_query($conn, "
		SELECT *, 
    (ss.stock*ss.unitario) AS total
		
		FROM
		
		(    
    
    SELECT 
    art_id,
		art_codigo,
		art_glosa,
		COALESCE(clasifica_nombre, '(No Asignado...)'),
		COALESCE(item_glosa, '(No Asignado...)'),
		calcular_stock(art_id, $bodega) AS stock,
    $valorizar AS unitario,
    clasifica_nombre,
    item_glosa,
    COALESCE(forma_nombre, '(No Asignado...)')
		
		FROM articulo
		
		LEFT JOIN bodega_clasificacion
		ON clasifica_id=art_clasifica_id
		LEFT JOIN item_presupuestario
		ON item_codigo=art_item
		LEFT JOIN bodega_forma
		ON art_forma=forma_id
		
		$consulta
		$item
		$clasifica
		$controlados
		$cadena_w
		
    ) AS ss
		
		LEFT JOIN articulo ON ss.art_id=articulo.art_id
		
    $lotes_en_cero
		
    ORDER BY $orden $ascen
		
		");
		
		} else {
		
		// Listado con Lotes
		
		$listado = pg_query($conn, "
		SELECT *, 
    (ss.stock*ss.unitario) AS total
		
		FROM
		
		(    
    
    SELECT DISTINCT
    art_id,
		art_codigo,
		art_glosa,
		COALESCE(clasifica_nombre, '(No Asignado...)'),
		COALESCE(item_glosa, '(No Asignado...)'),
		calcular_stock(art_id, $bodega, stock_vence) AS stock,
    $valorizar AS unitario,
    clasifica_nombre,
    item_glosa,
    COALESCE(forma_nombre, '(No Asignado...)'),
    stock_vence
		
		FROM stock AS v1
		
		RIGHT JOIN articulo ON
		stock_art_id=art_id
		LEFT JOIN bodega_clasificacion
		ON clasifica_id=art_clasifica_id
		LEFT JOIN item_presupuestario
		ON item_codigo=art_item
		LEFT JOIN bodega_forma
		ON art_forma=forma_id
		
		$consulta
		$item
		$clasifica
		$controlados
		$cadena_w
		
    ) AS ss
		
		LEFT JOIN articulo ON ss.art_id=articulo.art_id
		
		WHERE
    $lotes_en_cero
    ((stock_vence is not null and not stock=0)
    OR 
    (stock_vence is null and (stock=0 or stock is null)))
    
    
    ORDER BY $orden $ascen, stock_vence ASC
		
    ");
		
		}
		
    print("
    <table width=100% class='tabla_informe'>
    <tr class='tabla_header' style='font-weight: bold;'>
    <td colspan=".$largo_datos.">Datos del Producto</td>
    <td rowspan=2>Stock</td>
    <td colspan=2>Valor Neto ($)</td>
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
    
    if($mostrar_lotes)
    print("<td>Fecha de Venc.</td><td>Stock</td>");
    
    
    print("
    <td>P. Unit.</td>
    <td>Subtotal</td>
    </tr>
    ");
		
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
          
        $cantidad_filas=1;
        
      } else {
        
        if(isset($fila[15])) {
          if($fila[15]>0)
            $cantidad_filas=$fila[16];
            if($clase=='tabla_fila' or $clase=='') {
              $clase='tabla_fila2';
            } else {
              $clase='tabla_fila';
            }
        }
        
      }
      
      $textocolor='';
      
      if(!$mostrar_lotes) {
        $subtotal=($fila[5]*$fila[6]);
        $total+=($fila[5]*$fila[6]);
      
      } else {
        $subtotal=($fila[15]*$fila[6]);
        $total+=($fila[15]*$fila[6]);
      
      }
      
      
      if(!$mostrar_lotes) {
      
        print("
        <tr class='".$clase."' 
        style='
        $textocolor
        '>
        <td style='text-align: right;'>
        <b><i>".$fila[1]."</i></b></td>
        <td><b>".htmlentities($fila[2])."</b></td>");
      
        if($mostrar_clasif) 
        print("<td>".htmlentities($fila[3])."</td>");
        if($mostrar_forma) 
        print("<td>".htmlentities($fila[9])."</td>");
        if($mostrar_item) 
        print("<td>".htmlentities($fila[4])."</td>");
      
      } else {
      
        if(isset($fila[15])) {
          if($fila[15]>0) {
        
          print("
          <tr class='".$clase."' 
          style='
          $textocolor
          '>
          <td style='text-align: right;' rowspan=".$cantidad_filas.">
          <b><i>".$fila[1]."</i></b></td>
          <td rowspan=".$cantidad_filas."><b>
          ".htmlentities($fila[2])."</b></td>");
      
          if($mostrar_clasif) 
          print("<td rowspan=".$cantidad_filas.">
          ".htmlentities($fila[3])."</td>");
          
          if($mostrar_forma) 
          print("<td rowspan=".$cantidad_filas.">
          ".htmlentities($fila[9])."</td>");
          
          if($mostrar_item) 
          print("<td rowspan=".$cantidad_filas.">
          ".htmlentities($fila[4])."</td>");
          
          } else {
            print("
            <tr class='".$clase."' 
            style='
            $textocolor
            '>");
          } 
          
        } else {
          
          print("
          <tr class='".$clase."' 
          style='
          $textocolor
          '>");
          
        }
      }
      
      if($mostrar_lotes) 
      print("
      <td style='text-align: center; font-style: italic;'>
      ".htmlentities($fila[10])."
      </td>
      <td style='text-align: right;'>".number_formats($fila[5])."</td>
      ");
      
      if($mostrar_lotes) {
        if(isset($fila[15])) {
          if($fila[15]>0) {
            
            print("
            <td style='text-align: right;' rowspan=".$fila[16].">
            ".number_formats($fila[15])."
            </td>
            <td style='text-align: right;' rowspan=".$fila[16].">
            \$".number_formats($fila[6]).".-</td>
            <td style='text-align: right;' rowspan=".$fila[16].">
            \$".number_formats($subtotal).".-</td>
            </tr>");
           
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
          <td style='text-align: right;'>
          \$".number_formats($fila[6]).".-</td>
          <td style='text-align: right;'>
          \$".number_formats($subtotal).".-</td>
          </tr>
          "); 
      }    
      
    }
		
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
      <b>\$".number_formats($total_total).".-</b></td></tr>
			
		</table>");


?>
