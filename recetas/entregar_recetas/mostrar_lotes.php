<?php

  require_once('../../conectar_db.php');
  
  $paciente = ($_GET['paciente_id']*1);
  $receta_id = ($_GET['receta_id']*1);
  $bodega_id = ($_GET['bodega_id']*1);
  $receta_detalle = ($_GET['receta_detalle_'.$receta_id]);
  $cheque=false;
  
  if(isset($_GET['directo'])) {
    $directo=true;
  } else {
    $directo=false;
  }
  
  // Despacha inmediatamente los medicamentos...
  // Simula haber seleccionado los artículos directamente.
  
  $cadena='';
  
  $nro = pg_query($conn,
  "SELECT receta_numero FROM receta WHERE receta_id=".$receta_id
  );
  
  $nro_rec = pg_fetch_row($nro);
  
  $nro_receta = $nro_rec[0];
  
  if($receta_detalle=='x') {
  
    $receta = pg_query($conn, 
    "SELECT * FROM recetas_detalle WHERE recetad_receta_id=".$receta_id);
    
    for($i=0;$i<pg_num_rows($receta);$i++) {
      
      $art = pg_fetch_row($receta);
      
      $cadena .= $art[0].'/'.$art[1].'!';
          
      $_GET['art_cant_'.$art[0]]=($art[4]*24)/($art[3])*($art[2]);
    
    }
  
    $receta_detalle=$cadena;
  
  }
   
?> 
<script>
      
      verifica_despacho = function() {
      
      var myAjax = new Ajax.Request(
			'recetas/entregar_recetas/sql.php', 
			{
				method: 'get', 
				parameters: $('receta_detalle').serialize(),
				onComplete: function(pedido_datos) {
				
				    if(pedido_datos.responseText=='OK') {
              // alert('Receta rebajada exitosamente.');
              
              try { mostrar_recetas(); }
              catch(err) {}
              
				      $("mostrar_lotes").win_obj.close();
               
      	    } else {
      
              alert('ERROR\n\n'+pedido_datos.responseText);
      
            }
					
				}
				
			}
			
			);
      
      }
      
      borrar_receta = function() {
      
      var myAjax = new Ajax.Request(
      'recetas/entregar_recetas/sql_borrar_receta.php',
      {
        method: 'get',
        parameters: 'receta_id=<?=$receta_id?>',
        onComplete: function() {
          $("mostrar_lotes").win_obj.close();
        }
      }
      );
      
      }
      
      </script>
      
      
<?php
  
    if($cheque) {
     
?>
     
      <div class='sub-content'>
      <center>
      N&uacute;mero de Receta: <b><?php echo $nro_receta; ?></b>
      </center>
      </div>
  
<?php  
      
    }

?> 
      <div class='sub-content3' style='height:305px; overflow: auto;'>
      <table width=100%>
      <tr class='tabla_header' style='font-weight: bold;'>
      <td>C&oacute;digo Int.</td>
      <td>Glosa</td>
      <td>Fecha Venc.</td>
      <td>Cantidad</td>
      <td>Stock</td>
      </tr>
      
<?php      
      
      $medica = split('!', $receta_detalle);
      
      $cadena='';
      $u=0;
      
      $stock_insuf=false;
      
      for($i=0;$i<count($medica)-1;$i++) {
      
        $art = split('/', $medica[$i]);
        
        if(!isset($_GET['art_cant_'.$art[0]])) {
          
          // Medicamento ya fue entregado y campo 
          // no fué pasado desde el navegador.
        
          continue;
        
        } else {
        
          if(($_GET['art_cant_'.$art[0]]*1)<=0) {
            
            // Cantidad pasada desde el formulario es menor o igual a cero.
            
            continue;
            
          }
        
        }
        
        $id_articulo=$art[1];
        
        $art_info = pg_query($conn,"
        SELECT
        art_codigo,
        art_glosa,
        (
          SELECT SUM(stock_cant) FROM stock_precalculado_trans
          WHERE stock_art_id=art_id AND stock_bod_id=$bodega_id 
        ) AS stock
        FROM articulo
        WHERE art_id=$id_articulo
        ");
        
        $lotes = pg_query($conn, "
				SELECT * FROM lotes_vigentes($id_articulo, $bodega_id);
        ");
				
			  $articulo=pg_fetch_row($art_info); 
			  
			  $cant=($_GET['art_cant_'.$art[0]]*1);
			  
			  $cb=0;
			  
			  $falta_stock = false;
			  
			  if($articulo[2]<$cant) {
        
          
         print("
         <tr class='mouse_over' style='color: red;'>
           <td style='text-align:right;'><b><i>".$articulo[0]."</i></b></td>
           <td><b>".$articulo[1]."</b></td>
           <td style='text-align:center;'>&nbsp;</td>
           <td style='text-align:right; color: red;'><b>(".$cant.")</b></td>
           <td style='text-align:right; color: red;'><b>".$articulo[2]."</b></td>
          </tr>");
           
          $cant=$articulo[2];
          $falta_stock = true;
          $stock_insuf = true;
        
        
        }
			  
			  while($cant>0) {
        
          $cb++;
          
          if($cb>300) die('Error Inesperado.');
        
          $u++;
          ($u%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
  
          $lote = pg_fetch_row($lotes);
          
          if($lote[0]<$cant) {
            $cnt=$lote[0];
          } else {
            $cnt=$cant;
          }
          
          if($falta_stock) {
          print("
	   		   <tr class='$clase'>
           <td style='text-align:right;'><b><i>".$articulo[0]."</i></b></td>
           <td><b>".$articulo[1]."</b></td>
           <td style='text-align:center;'>".$lote[1]."</td>
           <td style='text-align:right; color: red;'><b>".$cnt."</b></td>
           <td style='text-align:right; color: red;'><b>".$articulo[2]."</b></td>
           </tr>
          ");
          } else {
          print("
	   		   <tr class='$clase'>
           <td style='text-align:right;'><b><i>".$articulo[0]."</i></b></td>
           <td><b>".$articulo[1]."</b></td>
           <td style='text-align:center;'>".$lote[1]."</td>
           <td style='text-align:right;'><b>".$cnt."</b></td>
           <td style='text-align:right;'><b>".$articulo[2]."</b></td></tr>
          ");
          
          }
        
          $cant-=$cnt;
          
          $lote[1]=str_replace('/','$', $lote[1]);
          
          $cadena.=$art[0].'/'.$art[1].'/'.$lote[1].'/'.$cnt.'!';
        
        }
			
      }
      
      print("
      
      </table>
      </div>
      
      <form name='receta_detalle' id='receta_detalle'>
      <input type='hidden' id='bodega_id' name='bodega_id' 
      value='$bodega_id'>
      <input type='hidden' id='detalle_receta' name='detalle_receta' 
      value='$cadena'>
      <input type='hidden' id='receta_id' name='receta_id' 
      value='$receta_id'>
      </form>
      
      ");
  
  
if(!$stock_insuf) {      

?>

    
    
    <center><table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='verifica_despacho();'>Entregar Productos...</a>
		</td></tr></table>
		</div>

<?php 

} 

if($directo) {

?>
		
		
		
		<center><div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='borrar_receta();'>Cancelar Ingreso...</a>
		</td></tr></table>
		</div>
		</td></tr></table>
      
    </table>

<?php

}

?>
