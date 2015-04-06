<?php 

	require_once("../../conectar_db.php");

	if(isset($_POST['eliminar'])) {

		unlink('../../tmp/'.$_POST['eliminar']);

	}

	$f=scandir("../../tmp");

	function fecha($str) {
		return $str[6].$str[7].'/'.$str[4].$str[5].'/'.substr($str,0,4).' '.substr($str,8,2).':'.substr($str,10,2);
	}

	print("<center><table style='font-size:10px;' cellpadding=0 cellspacing=0><tr>");

	$c=0; $foto_src='';

	for($i=sizeof($f)-1;$i>1;$i--) {

		if(!strstr($f[$i], 'jpg') AND !strstr($f[$i], 'mp3')) continue;

		$clase=$c++%2==0?'tabla_fila':'tabla_fila2';
		

		if(strstr($f[$i], 'jpg')) {
			print("<td class='$clase'><center><img src='../../tmp/".$f[$i]."' style='width:85px;height:65px;cursor:pointer;border:1px solid black;' onClick='ver_archivo(\"".$f[$i]."\");' /><br/>".fecha($f[$i])."</center></td><td class='$clase'><center><img src='../../iconos/delete.png' style='cursor:pointer;margin:2px;' onClick='eliminar_archivo(\"".$f[$i]."\");' /></center></td>");
			if($foto_src=='')
				$foto_src="tmp/".$f[$i];
		}

		if(strstr($f[$i], 'mp3'))
                        print("<td class='$clase'><center><img src='audio_icon.png' style='width:85px;height:65px;cursor:pointer;' onClick='ver_archivo(\"".$f[$i]."\");' /><br/>".fecha($f[$i])."</center></td><td class='$clase'><center><img src='../../iconos/delete.png' style='cursor:pointer;margin:2px;' onClick='eliminar_archivo(\"".$f[$i]."\");' /></center></td>");

		if($c%4==0)
			print("</tr><tr>");
		
	}
	
	if($c==0) {
		print("<td>(Sin contenido asociado...)</td>");
	}

	print("</tr></table></center>");
	
?>
