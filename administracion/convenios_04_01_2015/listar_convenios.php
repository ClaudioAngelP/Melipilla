<?php
    require_once('../../conectar_db.php');
    $sel_id_licitacion=pg_escape_string(trim(utf8_decode($_POST['sel_id_licitacion'])));
    $prov_id=$_POST['prov_id']*1;
    $art_id=$_POST['art_id2']*1;

    if($sel_id_licitacion!='') {
        $lic_w="trim(upper(convenio_licitacion)) ILIKE '%$sel_id_licitacion%'";
    } else {
        $lic_w='true';
    }
  
    if($prov_id!=0) {
        $prov_w="prov_id=$prov_id";
    } else {
        $prov_w='true';
    }

    if($art_id!=0) {
        $art_w="JOIN convenio_detalle AS c2 ON c2.convenio_id=c1.convenio_id AND art_id=$art_id";
    } else {
        $art_w='';
    }

    $estado=$_POST['filtro_estado']*1;

    if($estado==0) {
        $estado_w="true";
    } elseif($estado==1) {
        $estado_w="dias_vigencia>0";
    } elseif($estado==2) {
        $estado_w="dias_vigencia<=0";
    }
  
  
  
    $lista_convenios = pg_query($conn,"
    SELECT *, (SELECT COUNT(*) FROM convenio_detalle WHERE convenio_detalle.convenio_id=fooo.convenio_id) AS arts, (SELECT COUNT(*) FROM orden_compra WHERE orden_licitacion=convenio_licitacion AND fooo.prov_id=orden_prov_id) AS ocs FROM (SELECT DISTINCT c1.*, (convenio_fecha_final-CURRENT_DATE) AS dias_vigencia,prov_glosa,upper(prov_rut)as prov_rut FROM convenio AS c1
    JOIN proveedor USING (prov_id)
    $art_w
    WHERE $lic_w AND $prov_w
    ORDER BY c1.convenio_nombre) AS fooo WHERE $estado_w
    ORDER BY convenio_nombre
    ");
   
    print('<table style="width:100%;">');
        print('<tr class="tabla_header">');
            print('<td style="width:10%;"><b>ID Licitaci&oacute;n</b></td>');
            print('<td style="width:50%;"><b>Nombre del Convenio</b></td>');
           print('<td><b>Rut Proveedor</b></td>');
            print('<td><b>Proveedor</b></td>');
            print('<td>Art&iacute;iculos</td><td>Nro. OC</td>');
            print('<td colspan=5><b>Acciones</b></td>');
        print('</tr>');
  
    if(_cax(18)){
        print('<tr class="tabla_fila" id="convenio_nuevo_boton" onMouseOver="this.clase=this.className; this.className=\'mouse_over\';" onMouseOut="this.className=this.clase;">');
            print('<td colspan=6><i>Agregar Convenio Nuevo...</i></td>');
            print('<td colspan=4>');
                print('<center>');
                    print('<img src="iconos/database_add.png" style="cursor: pointer;" alt="Agregar Convenio..." title="Agregar Convenio..." onClick="abrir_convenio(0);">');
                print('</center>');
            print('</td>');
        print('</tr>');
        
        print('<tr class="tabla_fila" id="convenio_nuevo" style="display: none;" onMouseOver="this.clase=this.className; this.className=\'mouse_over\';" onMouseOut="this.className=this.clase;">');
            print('<td>');
                print('<input type="text" id="convenio_nombre" name="convenio_nombre" style="width:100%;">');
            print('</td>');
            print('<td>');
                print('<center>');
                    print('<img src="iconos/database_add.png" style="cursor: pointer;" alt="Agregar Convenio..." title="Agregar Convenio..." onClick="guardar_convenio();">');
                print('</center>');
            print('</td>');
            print('<td>');
                print('<center>');
                    print('<img src="iconos/database_delete.png" style="cursor: pointer;" alt="Cancelar..." title="Cancelar..." onClick="cancelar_convenio();">');
                print('</center>');
            print('</td>');
            print('<td>&nbsp;</td>');
        print('</tr>');
    }
    
    for($i=0;$i<pg_num_rows($lista_convenios);$i++) {
        $convenio = pg_fetch_assoc($lista_convenios);
        ($i%2==1)? $clase='tabla_fila': $clase='tabla_fila2';
        
        if($convenio['dias_vigencia']*1>=180) {
            $color='000000'; $deco='';
        } elseif($convenio['dias_vigencia']*1<180 AND $convenio['dias_vigencia']*1>90) {
            $color='999900'; $deco='';
        } elseif($convenio['dias_vigencia']*1<=90 AND $convenio['dias_vigencia']*1>5) {
            $color='FF0000'; $deco='';
        } else {
            $color='FF0000'; $deco='line-through';
        }
        
        print('<tr class="'.$clase.'" style="height:30px;" onMouseOver="this.clase=this.className; this.className=\'mouse_over\';" onMouseOut="this.className=this.clase;">');
            print('<td style="font-weight:bold;text-align:right;">'.htmlentities($convenio['convenio_licitacion']).'</td>');
            print('<td style="color:#'.$color.';text-decoration:'.$deco.'">'.htmlentities($convenio['convenio_nombre']).'</td>');
            print('<td>'.htmlentities($convenio['prov_rut']).'</td>');
            print('<td>'.htmlentities($convenio['prov_glosa']).'</td>');
            print('<td style="text-align:right;">'.$convenio['arts'].'</td>');
            print('<td style="text-align:right;font-weight:bold;">'.$convenio['ocs'].'</td>');
   
        if(_cax(43)){
            print('<td><center>');
            print('<img src="iconos/database_table.png" style="cursor: pointer;" alt="Ver Convenio..." title="Ver Convenio..." onClick="ver_convenio('.$convenio['convenio_id'].');">');
            print('</center></td>');
        }
	
        if(_cax(14)){
            print('<td><center>');
                print('<img src="iconos/database_link.png" style="cursor: pointer;" alt="Editar Convenio ..." title="Editar Convenio ..." onClick="abrir_convenio('.$convenio['convenio_id'].');">');
            print('</center></td>');	
        }
        
        
        if($convenio['convenio_id']!=1 AND _cax(47)) {
            print('<td>');
                print('<center>');
                    print('<img src="iconos/database_delete.png" style="cursor: pointer;" alt="Eliminar Convenio..." title="Eliminar Convenio..."onClick="eliminar_convenio('.$convenio['convenio_id'].');">');
                print('</center>');
            print('</td>');
        }
        else {
            
            //print('<td>&nbsp;</td>');
        }
        
        
        print('<td><center>
        <img src="iconos/page.png" style="cursor: pointer;" alt="Ver &Oacute;rdenes de Compra..." title="Ver &Oacute;rdenes de Compra..."onClick="ver_ordenes('.$convenio['convenio_id'].');">
        </center></td>');
    
        if(_cax(48)){
            print('<td><center>');
            print('<img src="iconos/book_add.png" style="cursor: pointer;" alt="Agregar Multa..." title="Agregar Multa..." onClick="agregar_multa('.$convenio['convenio_id'].',\''.$convenio['convenio_licitacion'].'\');">');
            print('</center></td>');
        }
        
        
        print('</tr>');
    }
    print('</table>');
?>