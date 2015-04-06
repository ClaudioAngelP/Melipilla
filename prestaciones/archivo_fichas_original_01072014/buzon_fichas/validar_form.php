<?php
    require_once("../../../conectar_db.php");
    $fichas_futuras = pg_escape_string($_POST['fichas_futuras']);
    $fichas_futuras=json_decode($fichas_futuras);
    $ubicacion = pg_escape_string($_POST['ubicacion']);
    $fecha=pg_escape_string($_POST['fecha1']);
    $ficha=pg_escape_string($_POST['ficha']);

    if(strstr($ubicacion,'.'))
    {
        $consulta="";
        $registros=false;
    }
    else
    {
        if(isset($_POST['option']))
        {
            if($_POST['option']=="2")
            {
                $ficha_find=" and pac_ficha='$ficha' ";
                $fecha=explode(" ",$fecha);
                $fecha=$fecha[0];
                $esp="true";
                $doc="true";
                $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
                pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
                date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,
                especialidades.esp_id,doctores.doc_id,
                COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
                COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                COALESCE((
                select date(nom_fecha) from nomina where nom_id=(
                select nom_id from nomina_detalle where nomd_id=
                (
                    SELECT COALESCE(nomd_id,0) FROM archivo_movimientos 
                    WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha  
                and am_final
                ORDER BY am_fecha DESC LIMIT 1
                ))),null)as fecha_solicitud_ficha,
                COALESCE((
                select nomd_hora from nomina_detalle where nomd_id=(
                SELECT COALESCE(nomd_id,0) FROM archivo_movimientos 
                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha  
                and am_final
                ORDER BY am_fecha DESC LIMIT 1)
                ),null)as hora_solicitud_ficha,
                COALESCE((
                select upper(func_nombre) from funcionario where func_id=(
                select nomd_func_id from nomina_detalle where nomd_id=(
                SELECT COALESCE(nomd_id,0) FROM archivo_movimientos 
                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha  
                and am_final
                ORDER BY am_fecha DESC LIMIT 1)
                )),null)as funcionario_solicitud_ficha,
                upper(func_nombre)as func_nombre_actual,
                COALESCE((SELECT COALESCE(origen_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha and am_final ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_anterior,
                COALESCE((SELECT COALESCE(am_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha and am_final ORDER BY am_fecha DESC LIMIT 1),0) as am_id_anterior,
                COALESCE((SELECT COALESCE(esp_id,0) FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_anterior,
                COALESCE((SELECT COALESCE(esp_id,0) FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual
                FROM nomina
                LEFT JOIN nomina_detalle USING (nom_id)
                LEFT JOIN especialidades ON nom_esp_id=esp_id
                LEFT JOIN doctores ON nom_doc_id=doc_id
                JOIN pacientes USING (pac_id)
                LEFT JOIN funcionario on nomina_detalle.nomd_func_id=func_id
                WHERE nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' $ficha_find
                AND nomd_diag_cod NOT IN ('X','T','B')
                AND $esp AND $doc
                ORDER BY nom_fecha,esp_desc,doc_nombre,pac_ficha";
            }
            else
            {
                $consulta="";
            }
            //AND COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0)=0
            $registros = cargar_registros_obj($consulta);
            if(!$registros)
            {
                $registros=false;
            }
        }
        
        
    }
    
    
    
    
    
    
?>
<script type="text/javascript" >
    cancelar = function()
    {
        $("validar_ficha").win_obj.close();
    }
    //--------------------------------------------------------------------------
    cambio_ficha = function (pos_index,pac_id,nomd_id,nom_id)
    {
        
        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/sql_mover_ficha.php',
        {
            method:'post',
            
            parameters:'&llamada=buzon_enviar&tipo_inf=1&pac_id='+pac_id+'&nomd_id='+nomd_id+'&nom_id='+nom_id,
            onComplete:function(r)
            {
                try
                {
                    var datos=r.responseText.evalJSON(true);
                    //var limpiar=false;
                    if(datos)
                    {
                        /*
                        $('barras').style.background='yellowgreen';						
                        $('texto_barras').style.background='black';
                        $('texto_barras').style.color='white';
                        $('texto_barras').value=datos[0].pac_rut+' - '+datos[0].pac_nombres+' '+datos[0].pac_appat+' '+datos[0].pac_apmat;
                        */
                        //limpiar=true;
                    }
                    else
                    {
                        alert("Error al mover la ficha de unidad");
                        return;
                    }
                    $("validar_ficha").win_obj.close();
                    //listar_nominas();
                    //$('barras_unidad').value='';
                    //$('barras_unidad').select();
                    //$('barras_unidad').focus();
                }
                catch(err)
                {
                    alert(err);
                }
            }
        });
    }
    //--------------------------------------------------------------------------
</script>
<div class="sub-content">
    <table style="font-size:12px;width: 100%;">
        <tr>
            <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Nro de Ficha</td>
            <td class='tabla_fila' style="font-weight: bold"><?php echo $ficha;?></td>

        </tr>
    </table>
</div>
<div id="list_fichas_futuras" name="list_fichas_futuras" class="sub-content2" style="height:500px;overflow:auto;">
    <?php
        if($registros!=false)
        {
            $doc_ant="";
            $esp_ant="";
            for($i=0;$i<count($registros);$i++){
                ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                if($doc_ant!=$registros[$i]['doc_id'] OR $esp_ant!=$registros[$i]['esp_id'])
                {
                    $doc_ant=$registros[$i]['doc_id'];
                    $esp_ant=$registros[$i]['esp_id'];
                    $cont=1;
                    print("<table style='width:100%;' class='lista_small'>");
                        print("<tr class='tabla_header'>");
                            print("<td style='text-align:left;font-size:16px;' colspan=11>Programa: <b>".htmlentities($registros[$i]['esp'])."</b><br/>Profesional/Servicio: <b>".htmlentities($registros[$i]['doc_nombre'])."</b></td>");
                        print("</tr>");
                        print("<tr class='tabla_header'>");
                            print("<td style='width:3%;'>#</td>");
                            print("<td style='width:15%;'>Solicitado</td>");
                            print("<td style='width:12%;'>Ficha</td>");
                            print("<td style='width:12%;'>RUN</td>");
                            print("<td style='width:40%;'>Nombre Completo</td>");
                            print("<td style='width:40%;'>Seleccionar</td>");
                            
                            //print("<td>Estado</td>");
                            //print("<td>Etiqueta</td>");
                            //print("<td>Historial</td>");
			print("</tr>");
                }
                print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
                    print("<td style='text-align:center;'>".$cont."</td>");
                    print("<td style='text-align:center;'>".$registros[$i]['fecha_asigna']."</td>");
                    print("<td style='text-align:right;font-size:14px;font-weight:bold;'>".$registros[$i]['pac_ficha']."</td>");
                    print("<td style='text-align:right;'>".$registros[$i]['pac_rut']."</td>");
                    print("<td style='text-align:left;'>".htmlentities($registros[$i]['pac_nombre'])."</td>");
                    print('<td><center><input type="checkbox" id="chk_ficha_'.$i.'" name="chk_ficha_'.$i.'" value="1" onChange="cambio_ficha('.$i.','.$registros[$i]['pac_id'].','.$registros[$i]['nomd_id'].','.$registros[$i]['nom_id'].')"></center></td>');
		print("</tr>");
                $cont++;
            }
        print("</center>");
        print("</div>");
        }
        else
        {
            
            
        }
    
    ?>
    <div>
        <center>
            <table>
                <tr>
                    <td>
                        <div class='boton' style="min-width: 100px;">
                            <table>
                                <tr>
                                    <td>
                                        <img src='iconos/cancel.png' />
                                    </td>
                                    <td>
                                        <a href='#' onClick='cancelar();'>Cancelar</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </center>
    </div>
</div>