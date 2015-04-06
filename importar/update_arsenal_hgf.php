<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  
  $fi=explode("\n", utf8_decode(file_get_contents('arsenal_octubre.csv')));
  
  pg_query("START TRANSACTION;");
  $updates='';
  $up=0;
  $ignores='';
  $ig=0;
  $inact='';
  $in=0;
  $lost='';
  $lst=0;
  
 for($i=1;$i<sizeof($fi);$i++) {
      
	$r=explode('|',$fi[$i]);
     
     //if(!isset($r[0]) OR trim($r[0])=='') continue;
      
	$art_codigo=trim($r[1]);
	$art_glosa=trim(strtoupper($r[2]));
	$art_forma=trim(strtoupper($r[3]));
      		
	$art=cargar_registro("SELECT art_id,art_activado FROM articulo WHERE art_codigo='$art_codigo';");
	
	if($art){
		pg_query("UPDATE articulo SET art_arsenal=true WHERE art_id=".$art['art_id'].";");
		$arsenal=cargar_registro("SELECT art_arsenal,art_activado FROM articulo WHERE art_codigo='$art_codigo';");
		if($arsenal['art_activado']=='t' AND $arsenal['art_arsenal']=='t'){
			$updates.='['.$i.'] ['.$art_codigo.' '.$art_glosa.' '.$art_forma.'] '.$arsenal['art_arsenal'].'/'.$arsenal['art_activado'].'<br>';
			$up++;
		}else if($arsenal['art_activado']=='f'){
			$inact.='['.$i.'] ['.$art_codigo.' '.$art_glosa.' '.$art_forma.'] '.$arsenal['art_arsenal'].'/'.$arsenal['art_activado'].'<br>';
			$in++;
		}else{
			$lost.='['.$i.'] ['.$art_codigo.' '.$art_glosa.' '.$art_forma.'] '.$arsenal['art_arsenal'].'/'.$arsenal['art_activado'].'<br>';
			$lst++;
		}	

	}else{
		$ignores.='['.$i.'] ['.$art_codigo.' '.$art_glosa.' '.$art_forma.']<br>';
		$ig++;
	}
		
	


}
print('UPDATES='.$up.'<br>');
print($updates.'<br><br>');
print('INACTIVOS='.$in.'<br>');
print($inact.'<br><br>');
print('IGNORES='.$ig.'<br>');
print($ignores);
print('PERDIDOS='.$lst.'<br>');
print($lost);
 	 pg_query("COMMIT");
?>
