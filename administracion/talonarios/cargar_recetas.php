<?php

  require_once('../../conectar_db.php');
  
  $id_talonario = ($_GET['id_talonario']*1);
  
  function comprobar_receta_existe($nro_receta) {
  
    GLOBAL $recetas, $recetas_anuladas;
    
    for($u=0;$u<count($recetas);$u++) {
      if($recetas[$u][1]==$nro_receta) 
      //return 1;
      return Array(1,'');
    }

    for($u=0;$u<count($recetas_anuladas);$u++) {
      if($recetas_anuladas[$u][1]==$nro_receta) 
      //return 2; 
      return Array(2,$recetas_anuladas[$u][2]);
    }
	return Array(0,'');
   //return 0;
  
  }
  
  $talonario = cargar_registro('
  SELECT * FROM talonario WHERE talonario_id='.$id_talonario.'
  ');
  
  $recetas = cargar_registros('
  SELECT receta_tipotalonario_id, receta_numero FROM receta WHERE 
  receta_tipotalonario_id='.$talonario['talonario_tipotalonario_id'].' AND
  receta_numero>='.$talonario['talonario_inicio'].' AND 
  receta_numero<='.$talonario['talonario_final'].'
  ', true);
  
  $recetas_anuladas = cargar_registros('
  SELECT * FROM receta_anulada WHERE 
  receta_tipotalonario_id='.$talonario['talonario_tipotalonario_id'].' AND
  receta_numero>='.$talonario['talonario_inicio'].' AND 
  receta_numero<='.$talonario['talonario_final'].'
  ', true);
  
  print('
  <table style="width:100%;font-size:10px;" cellpadding=0 cellspacing=0>
  <tr class="tabla_header">
  <td style="width:120px;">N&uacute;mero de Receta</td>
  <td>Estado</td>
  <td style="width:60px;">Validez</td>
  <td>Causal</td>
  </tr>');
  
  for($i=$talonario['talonario_inicio'];$i<=$talonario['talonario_final'];$i++) 
  {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    $rec=comprobar_receta_existe($i);
    $estadorec=$rec[0];
    $causalrec=$rec[1];
    
    if($causalrec=='Extravio') $causal1='SELECTED'; else $causal1='';
    if($causalrec=='Robo') $causal2='SELECTED'; else $causal2='';
    if($causalrec=='Devolucion') $causal3='SELECTED'; else $causal3='';
    
    if($estadorec==1) {
    
      $activado = 'disabled';
      $chequeado = 'checked';
      $estado = 'Emitida';
      $causal = 'DISABLED';
    
    } else {
    
      $activado = '';
      
      if($estadorec==0) {
        $chequeado = 'checked';
        $estado = 'Activa';
        $causal = 'DISABLED';
      } elseif($estadorec==2) {
        $chequeado = '';
        $estado = 'Inactiva';
        $causal='';
        
      }
      
    }
    
    print('
    <tr class="'.$clase.'"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\';"
    >
    <td style="text-align:center;font-weight:bold;">'.$i.'</td>
    <td>'.$estado.'</td>
    <td>
    <center>
    <input type="checkbox" '.$activado.' '.$chequeado.' 
    onChange="if(this.checked) $(\'causal_receta_'.$i.'\').disabled=true; else $(\'causal_receta_'.$i.'\').disabled=false;"	
    id="check_receta_'.$i.'" name="check_receta_'.$i.'">
    </center>
    </td>
    <td>
		<center>
			<select id="causal_receta_'.$i.'" name="causal_receta_'.$i.'" '.$causal.'>
			<option value="">Seleccione</option>
			<option value="EXTRAVIO" '.$causal1.' >Extrav&iacute;o</option>
			<option value="ROBO" '.$causal2.' >Robo</option>
			<option value="DEVOLUCION" '.$causal3.' >Devoluci&oacute;n</option>
			</select>
		</center>
	</td>
    </tr>');
    
  }
  
  print('
  </table>
  ');

?>
