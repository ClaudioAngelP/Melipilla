<?php
    require_once('../../conectar_db.php');
    $convenio_id=$_GET['convenio_id']*1;
    if(isset($_GET['adjunto_id']))
    {
        $adjunto_id=$_GET['adjunto_id']*1;
	pg_query("DELETE FROM convenio_adjuntos WHERE cad_id=$adjunto_id and convenio_id=$convenio_id;");
    }
    $l=pg_query("SELECT * FROM convenio_adjuntos WHERE convenio_id=$convenio_id");
    print("<table>");
    for($i=0;$i<pg_num_rows($l);$i++)
    {
        $adjunto = pg_fetch_assoc($l);
        list($nombre,$tipo,$peso,$md5)=explode('|',$adjunto['cad_adjunto']);
        print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\"' onMouseOut='this.className=\"$clase\"'>
        <table style='cursor:pointer;border:1px solid black;background-color:white;font-size:12px;'>
            <tr>
                <td><i>Archivo:</i></td>
		<td><img src='iconos/application_put.png'></td>
		<td onClick='window.open(\"administracion/convenios/descargar_adjunto.php?adjunto_id=".$adjunto['cad_id']."\", \"_self\");'><b><u>".$nombre."</u></b></td>
		<td><i>(".number_format($peso/1024,1,',','.')." Kb)</i></td>
		<td>.</td>
                <td><img src='iconos/delete.png' Onclick='eliminar_adjunto(".$adjunto['cad_id'].",".$adjunto['convenio_id'].");'></td>
            </tr>
	</table> 
	</td></tr>");
    }
    print("</table>");
?>