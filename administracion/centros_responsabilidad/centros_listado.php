<?php

  require_once('../../conectar_db.php');

$centros = pg_query($conn,"
	SELECT
	centro_ruta,
	centro_nombre,
	length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel,
	(
	SELECT COUNT(*)
	FROM centro_costo AS cnt
	WHERE cnt.centro_ruta ~ ('^'||centro_costo.centro_ruta||'\\\.')
	)
	AS centro_contiene,
	centro_medica,
	centro_gasto,
        centro_winsig
	FROM
	centro_costo
	ORDER BY
	centro_ruta
	");
	
?>

<table width=100%>
    <tr class='tabla_header'>
        <td rowspan=2>
            <b>Estructura de la Instituci&oacute;n</b>
        </td>
        <td colspan=5>
            <b>Acciones</b>
        </td>
    </tr>
    <tr class='tabla_header'>
        <td>
            <img src='iconos/coins.png'>
        </td>
        <td>
            <img src='iconos/pill.png'>
        </td>
        <td>
            <img src='iconos/add.png'>
        </td>
        <td>
            <img src='iconos/delete.png'>
        </td>
        <td>
            <img src='iconos/pencil.png'>
        </td>
    </tr>
    <tr id='ruta_' class='tabla_fila' onMouseOver='this.className="mouse_over";' onMouseOut='this.className="tabla_fila";' style='text-align: left;'>
        <td>
            <img src='iconos/bullet_green.png'><b>Carpeta Ra&iacute;z</b>
        </td>
	<td colspan=2>&nbsp;</td>
        <td>
            <center>
                <img src='iconos/group_add.png' onClick='agregar_centro("");' alt='Agregar en este nivel...' title='Agregar en este nivel...'>
            </center>
        </td>
        <td colspan=2>&nbsp;</td>
    </tr>
<?php	
    for($i=0;$i<pg_num_rows($centros);$i++)
    {
        $datos=pg_fetch_row($centros);
	if(($datos[2])==1)
        {
            $clase='tabla_fila';
            $estilofuente = "<b>";
        }
        else
        {
            $clase='tabla_fila2';
            $estilofuente = "";
        }
	($datos[3]>0)?$bullet='bullet_toggle_plus.png':$bullet='bullet_orange.png';
        ($datos[4]=='t')?$valor_check='checked':$valor_check='';
        ($datos[5]=='t')?$valor_check2='checked':$valor_check2='';
	$espaciado = str_repeat("<img src='iconos/blank.gif'>", $datos[2]);
	print("
            <tr class='$clase' id='ruta_".$datos[0]."' onMouseOver='this.className=\"mouse_over\"' onMouseOut='this.className=\"$clase\"'>
                <td><table border=0 width='100%'><tr><td>
                $espaciado<img src='iconos/$bullet'>$estilofuente
                    <span id='texto_".$datos[0]."'>".htmlentities($datos[1])."</span>
                        </td>
                        <td style='text-align:right;font-size:9px;width=50%;'>
                        <input type='text' size='40' style='font-size:10px;text-align:left;' id='alias_".$datos[0]."' name='alias_".$datos[0]."' value='".$datos[6]."' onBlur='' />
                        </td>
                        <td>
                            <center>
                                <img style='cursor:pointer;' src='iconos/disk.png' onClick='guardar_alias(\"".$datos[0]."\");' alt='Guardar Alias...' title='Guardar Alias...'>
                            </center>
                        </td>
                        </tr>
                        </table>
		</td>
        ");
        if($datos[2]==2)
            print("<td>
            <center>
                <input type='checkbox' onClick='guardar_gasto(\"".$datos[0]."\");' id='gasto_".$datos[0]."' name='gasto_".$datos[0]."' ".$valor_check2.">
                <img src='imagenes/ajax-loader1.gif' id='cargag_".$datos[0]."' style='display:none;'>
            </center>
            </td>");
        else
            print('<td>&nbsp;</td>');
        
        print("
        <td>
            <center>
                <input type='checkbox' onClick='guardar_pildora(\"".$datos[0]."\");' id='medica_".$datos[0]."' name='medica_".$datos[0]."' ".$valor_check.">
                <img src='imagenes/ajax-loader1.gif' id='cargam_".$datos[0]."' style='display:none;'>
            </center>
        </td>
	<td>
            <center>
                <img src='iconos/group_add.png' onClick='agregar_centro(\"".$datos[0]."\");' alt='Agregar en este nivel...' title='Agregar en este nivel...'>
            </center>
        </td>
        <td>
            <center>
                <img src='iconos/group_delete.png' onClick='borrar_centro(\"".$datos[0]."\", ".$datos[3].");' alt='Borrar este nivel...' title='Borrar este nivel...'>
            </center>
        </td>
        <td>
            <center>
                <img src='iconos/group_edit.png' onClick='editar_centro(\"".$datos[0]."\");' alt='Editar este nivel...' title='Editar este nivel...'>
            </center>
        </td>
        </tr>");
        }
?>
</table>