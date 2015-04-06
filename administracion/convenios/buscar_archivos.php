<?php
require_once('../../conectar_db.php');
  $convenio_id = $_GET['convenio_id']*1;
  
  $sql = "SELECT * FROM convenio_adjuntos WHERE convenio_id = $convenio_id";
  $result = pg_query($conn, $sql) or die(pg_last_error());
  
  $contador = 0;  
  while($row = pg_fetch_array($result)){
  	
  	$cad_id[$contador] = $row['cad_id'];  
	$cad_adjunto[$contador] = $row['cad_adjunto'];  	
	$cad_ruta[$contador]   = $row['ruta'];
	
	$contador++;
  }
  
  if($contador > 0){
  	
  echo '<table cellpadding="0" cellspacing="2"  style="width:100%;">';
	  
  for($i = 0 ;$i<$contador; $i++){
		
	list($nombre,$tipo,$peso,$md5) = explode('|',$cad_adjunto[$i]);
	?>
	<tr>
		<td style="text-align: right;">
			<a  style="font-weight: bold;" href="<? echo $cad_ruta[$i]."".$md5?>" 
				download="<?=strtoupper($nombre);?>">
				<?= strtoupper($nombre);?>
			</a>			
		</td>
		<td style="text-align: right;">
			<span onclick="eliminar_adjunto(<?= $cad_id[$i] ?>,<?= $convenio_id?>);" style="cursor: pointer;"> 
			<img src="iconos/application_delete.png" />
		</span>	
		</td>
	</tr>	
	<?
  }
?>
</table>
<?php } ?>