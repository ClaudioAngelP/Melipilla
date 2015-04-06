<?php

  require_once('../../conectar_db.php');
  
  $func_id = $_POST['func_id'];
  
  $accesos = pg_query($conn,
  "SELECT permiso_id, permiso_tipo FROM func_permiso"
  );
  
  $bod_ids_ = pg_query('SELECT bod_id FROM bodega ORDER BY bod_id');
  for($u=0;$u<pg_num_rows($bod_ids_);$u++) {
    $tmp_id=pg_fetch_row($bod_ids_);
    $bod_ids[$u] = $tmp_id[0]; 
  }

  $centro_ids_ = pg_query("
    SELECT centro_ruta FROM centro_costo
    WHERE length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica 
    ORDER BY centro_ruta
    ");
    
  for($u=0;$u<pg_num_rows($centro_ids_);$u++) {
    $tmp_id=pg_fetch_row($centro_ids_);
    $centro_rutas[$u] = $tmp_id[0]; 
  }
      
  
  for($i=0;$i<pg_num_rows($accesos);$i++) {
  
    $acceso = pg_fetch_row($accesos);
    
    $existe = pg_query($conn, 
    "SELECT * FROM func_acceso
    WHERE permiso_id=".($acceso[0]*1)." 
    AND func_id=".($func_id*1)." AND NOT acceso_ruta"
    );

    $existe2 = pg_query($conn, 
    "SELECT * FROM func_acceso
    WHERE permiso_id=".($acceso[0]*1)." 
    AND func_id=".($func_id*1)." AND acceso_ruta"
    );

      
    if($acceso[1]==0) {
      
      if(isset($_POST['acceso_check_'.$acceso[0]])) {
        
        if(pg_num_rows($existe)==0) {
        
          pg_query($conn, 
          'INSERT INTO func_acceso VALUES 
          (DEFAULT,
          '.($acceso[0]*1).', 
          '.($func_id*1).',
          0,
          \'1\');');
          
        } 
        
      } else {
      
        pg_query("DELETE FROM func_acceso WHERE
        permiso_id=".($acceso[0]*1)." AND func_id=".($func_id*1));
      
      } 
      
    } else {
    
    
      $c=0;
      $valor='';
      
      if($acceso[1]!=8) {
        foreach($bod_ids AS $bod_id) {
          if(isset($_POST['acceso_check_'.$acceso[0].'_'.$bod_id])) {
          $valor .= 
          pg_escape_string($_POST['acceso_check_'.$acceso[0].'_'.$bod_id]).',';
          $c++;
          }
        }
      }
      
      if($acceso[1]==5 OR $acceso[1]==8) {
        $valor2='';
        foreach($centro_rutas AS $centro_ruta) {
          $centro=str_replace('.','_',$centro_ruta);
          if(isset($_POST['acceso_check_'.$acceso[0].'_'.$centro])) {
          $valor2 .= pg_escape_string(
          $_POST['acceso_check_'.$acceso[0].'_'.$centro]).",";
          $c++;
          }
        }  
      }
      
      $valor = substr($valor, 0, strlen($valor)-1);
      
      if($c>0) {
      
        // Confirma existencia del permiso...
        
        if(pg_num_rows($existe)==0) {
        
        pg_query($conn, 
        'INSERT INTO func_acceso VALUES 
        (DEFAULT,
        '.($acceso[0]*1).', 
        '.($func_id*1).',
        0,
        \''.$valor.'\', false);');

        
        } else {
      
        pg_query($conn, 
        'UPDATE func_acceso SET 
        valor=\''.$valor.'\' WHERE
        permiso_id='.($acceso[0]*1).' AND 
        func_id='.($func_id*1).' AND NOT acceso_ruta');
                
        }
        
        if($acceso[1]==5 OR $acceso[1]==8) {
        
          if(pg_num_rows($existe2)==0) {
            pg_query($conn, 
            'INSERT INTO func_acceso VALUES 
            (DEFAULT,
            '.($acceso[0]*1).', 
            '.($func_id*1).',
            0,
            \''.$valor2.'\', true);');        
          } else {
            pg_query($conn, 
            'UPDATE func_acceso SET 
            valor=\''.$valor2.'\' WHERE
            permiso_id='.($acceso[0]*1).' AND 
            func_id='.($func_id*1).' AND acceso_ruta');
          }
        }
      
      
      } else {
   
        pg_query("DELETE FROM func_acceso WHERE
        permiso_id=".($acceso[0]*1)." AND func_id=".($func_id*1));

      
      }
    
    }
  
  }
  
  print('OK');

?>
