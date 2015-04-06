<?php
    require_once('../../conectar_db.php');
    $multa_id=$_GET['multa_id']*1;
    if(isset($_GET['adjunto_id']))
    {
        $adjunto_id=$_GET['adjunto_id']*1;
	pg_query("DELETE FROM multa_adjuntos WHERE mad_id=$adjunto_id and multa_id=$multa_id;");
    }
    $l=pg_query("SELECT * FROM multa_adjuntos WHERE multa_id=$multa_id");
    print("<center><table>");
    for($i=0;$i<pg_num_rows($l);$i++)
    {
        $adjunto = pg_fetch_assoc($l);
        list($nombre,$tipo,$peso,$md5)=explode('|',$adjunto['mad_adjunto']);
        print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\"' onMouseOut='this.className=\"$clase\"'>
        <table style='cursor:pointer;border:1px solid black;background-color:white;font-size:12px;'>
            <tr>
                <td><i>Archivo:</i></td>
		<td><img src='../../iconos/application_put.png'></td>
		<td onClick='window.open(\"descargar_adjunto_multa.php?adjunto_id=".$adjunto['mad_id']."\", \"_self\");'><b><u>".htmlentities($nombre)."</u></b></td>
		<td><i>(".number_format($peso/1024,1,',','.')." Kb)</i></td>
		<td>.</td>
                <td><img src='../../iconos/delete.png' Onclick='eliminar_adjunto(".$adjunto['mad_id'].",".$adjunto['multa_id'].");'></td>
            </tr>
	</table> 
	</td></tr>");
    }
    print("</table></center>");
?>
