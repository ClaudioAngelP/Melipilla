<?php
    require_once('../../conectar_db.php');
    require_once('../../conectores/fonasa/cargar_paciente_fonasa.php');
    $pac_id=$_GET['pac_id']*1;
    $nom_id=$_GET['nom_id']*1;
    $nomd_hora=pg_escape_string($_GET['nomd_hora']);
    $nomd_hora_extra=pg_escape_string($_GET['nomd_hora_extra']);
    $duracion='';
    if(isset($_GET['duracion']))
        $duracion=$_GET['duracion']*1;
    
    $consulta="SELECT * FROM nomina LEFT JOIN especialidades ON nom_esp_id=esp_id LEFT JOIN doctores ON nom_doc_id=doc_id WHERE nom_id=$nom_id";
    $n=cargar_registro($consulta);
    
    
    if(strstr($nomd_hora,'_'))
    {
        $cmp=explode('_',$nomd_hora);
        $nomina_detalle=true;
        //$consulta="select nomd_extra from nomina_detalle where nom_id=$nom_id and nomd_hora='$cmp[0]'";
    }
    else
    {
        $nomina_detalle=false;
        //$consulta="select nomd_extra from nomina_detalle where nom_id=$nom_id and nomd_hora='$nomd_hora'";
    }
    if($nomina_detalle)
    {
        $consulta="select nomd_id from nomina_detalle where nom_id=$nom_id and nomd_hora='$cmp[0]'";
        $reg_cupo=cargar_registro($consulta);
        if(!$reg_cupo)
        {
            print("Error al Encontrar Nomina_detalle bloque");
            die();
        }
        $nomd_id=$reg_cupo['nomd_id'];
    }
    /*
    $reg_cupo=cargar_registro($consulta);
    if(!$reg_cupo)
    {
        print("Error al Encontrar Nomina_detalle");
        die();
    }
    else
    {
        $extra=$reg_cupo['nomd_extra'];
    }
    */
    
    
    if($pac_id!="0" && $pac_id!="")
    {
        $consulta="SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_id=$pac_id";
        $pac=cargar_registro($consulta, true);
        $consulta="
        SELECT * FROM nomina_detalle 
        JOIN nomina USING (nom_id)
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE pac_id=$pac_id AND ((nom_fecha>=(CURRENT_DATE-('2 days'::interval)) AND nomd_diag_cod NOT IN ('B')) OR (nomd_diag_cod='X' AND nomd_estado IS NULL OR NOT nomd_estado='1'))
        ORDER BY nom_fecha, nomd_hora;
        ";
        $nd=cargar_registros_obj($consulta);
    }
    else
    {
        if(isset($_GET['pac_rut']))
        {
            if($_GET['pac_rut']!='')
            {
		  $pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_rut='".$_GET['pac_rut']."'", true);
		  if(!$pac)
		  {
			pac_fonasa($_GET['pac_rut'],0);
		  }
	         $pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_rut='".$_GET['pac_rut']."'", true);
		  if($pac)
		  {
			$pac_id=$pac['pac_id'];
			$nd=cargar_registros_obj("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) JOIN especialidades ON nom_esp_id=esp_id JOIN doctores ON nom_doc_id=doc_id WHERE pac_id=$pac_id AND ((nom_fecha>=(CURRENT_DATE-('2 days'::interval)) AND nomd_diag_cod NOT IN ('B')) OR (nomd_diag_cod='X' AND nomd_estado IS NULL OR NOT nomd_estado='1'))  ORDER BY nom_fecha, nomd_hora;");
	  	  }
                else
		  {
			echo 'Error al procesar paciente';
                	die();
		  }
	     }
            else
            {
                echo 'Error al procesar paciente';
                die();
            }
        }
        else
        {
            echo 'Error al procesar paciente';
            die();
        }
    }
?>
<html>
    <title>Actualizar Datos del Paciente</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        function imprimir_citacion(nomd_id)
        {
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-340;
            new_win = window.open('citaciones2.php?nomd_id='+nomd_id,
            '_self', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
            'top='+top+', left='+left);
            new_win.focus();
        }
        
        function guardar_cupo()
        {
            var myAjax=new Ajax.Request('sql_tomar_cupo.php',
            {
                method:'post',
                parameters:$('datos_pac').serialize(),
                onComplete:function(resp2)
                {
                    if(resp2.responseText!="X")
                    {
                        var fn2=window.opener.abrir_nomina.bind(window.opener);
                        fn2(window.opener.$("folio_nomina").value, 1);
                        imprimir_citacion(resp2.responseText*1);
                    }
                    else
                    {
                        alert("LA HORA SELECCIONADA YA HA SIDO ASIGNADA");
                        return;
                    }
                    //window.close();
                }
            });		
        }
    
        function init()
        {
            var myAjax=new Ajax.Request('certificar_paciente.php',
            {
                method:'post', parameters:'pac_rut=<?php echo $pac['pac_rut']; ?>',
		onComplete:function(r)
                {
                    //alert(r.responseText);
                    try
                    {
                        var datos=r.responseText.evalJSON(true);
			$('prev_id').value=datos.prev_id;
			$('ult_act').innerHTML=datos.fecha_fonasa.substr(0,19);
			$('cargar_fonasa').hide();
                    }
                    catch(err)
                    {
                        alert(r.responseText);
                    }
		}
            });
        }
	
    </script>
    <body class='fuente_por_defecto popup_background' onLoad='init();'>
        <div class='sub-content'>
            <img src='../../iconos/user_go.png' />
            <b>Actualizar Datos de Paciente</b>
        </div>
        <form id='datos_pac' name='datos_pac' onSubmit='return false;'>
            <input type='hidden' id='pac_id' name='pac_id' value='<?php echo $pac_id; ?>' />
            <input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />
            <input type='hidden' id='nomd_hora' name='nomd_hora' value='<?php echo $nomd_hora; ?>' />
            <input type='hidden' id='nomd_hora_extra' name='nomd_hora_extra' value='<?php echo $nomd_hora_extra; ?>' />
            <input type='hidden' id='hora_extra' name='hora_extra' value='<?php echo $extra; ?>' />
            <input type='hidden' id='duracion' name='duracion' value='<?php echo $duracion; ?>' />
            <?php if($nd) { ?>
                <table style='width:100%;font-size:11px;'>
                    <tr class='tabla_header'><td colspan=5><u>Alerta de Citaciones Recientes</u></td></tr>
                    <tr class='tabla_header'>
                        <td>Fecha</td>
                        <td>Hora</td>
                        <td>Especialidad</td>
                        <td>Profesional/Recurso</td>
                        <td>Asignaci&oacute;n</td>
                    </tr>
            <?php 
                for($i=0;$i<sizeof($nd);$i++)
                {
                    $clase=$i%2==0?'tabla_fila':'tabla_fila2';
                    $color=($nd[$i]['esp_id']*1==$n['nom_esp_id']*1)?'red':'black';
                    if($nd[$i]['nomd_diag_cod']=='X')
                    {
                        $bg_color='background-color:yellow';
                    }
                    else
                    {
                        if($nd[$i]['nomd_diag_cod']=='T')
                            $bg_color='background-color:pink';
                        else
                            $bg_color='';
                    }
                    print("
                    <tr class='$clase' style='color:$color;$bg_color'>
                        <td style='text-align:center;'>".substr($nd[$i]['nom_fecha'],0,10)."</td>
                        <td style='text-align:center;'>".substr($nd[$i]['nomd_hora'],0,5)."</td>
                        <td style='text-align:left;font-weight:bold;'>".$nd[$i]['esp_desc']."</td>
                        <td style='text-align:left;'>".$nd[$i]['doc_paterno']." ".$nd[$i]['doc_materno']." ".$nd[$i]['doc_nombres']."</td>
                        <td style='text-align:center;'>".substr($nd[$i]['nomd_fecha_asigna'],0,16)."</td>
                    </tr>");
                }
            ?>
                </table>
        <?php } ?>
            <div class='sub-content'>
                <table style='width:100%;'>
                    <tr>
                        <td style='text-align:right;width:25%;' class='tabla_fila2'>R.U.N.:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_rut' name='pac_rut' value='<?php echo $pac['pac_rut']; ?>' style='font-size:14px;font-weight:bold;' onBlur='validacion_rut(this);' readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Pasaporte/ID:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_pasaporte' name='pac_pasaporte' value='<?php echo $pac['pac_pasaporte']; ?>' style='font-size:14px;font-weight:bold;' />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>N&uacute;mero Ficha:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_ficha' name='pac_ficha' value='<?php echo $pac['pac_ficha']; ?>' style='font-size:14px;font-weight:bold;' readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Nombres:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_nombres' name='pac_nombres' value='<?php echo $pac['pac_nombres']; ?>' style='font-size:12px;font-weight:bold;' size=30 readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Apellido Paterno:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_appat' name='pac_appat' value='<?php echo $pac['pac_appat']; ?>' style='font-size:12px;font-weight:bold;' size=30 readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Apellido Materno:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_apmat' name='pac_apmat' value='<?php echo $pac['pac_apmat']; ?>' style='font-size:12px;font-weight:bold;' size=30 readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Fecha de Nacimiento:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_fc_nac' name='pac_fc_nac' value='<?php echo $pac['pac_fc_nac']; ?>' style='text-align:center;' size=10 readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Sexo:</td>
                        <td style='tabla_fila'>
                            <select id='sex_id' onMouseOver="" onMouseOut="">
                            <?php 
                                $sexs=cargar_registros_obj("SELECT * FROM sexo ORDER BY sex_id;", true);
                                for($i=0;$i<sizeof($sexs);$i++)
                                {
                                    if($pac['sex_id']==$sexs[$i]['sex_id'])
                                        $sel='SELECTED';
                                    else
                                        $sel='';
                                    print("<option value='".$sexs[$i]['sex_id']."' $sel >".$sexs[$i]['sex_desc']."</option>");
                                }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Direcci&oacute;n:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_direccion' name='pac_direccion' value='<?php echo $pac['pac_direccion']; ?>' size=40 />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Comuna:</td>
                        <td style='tabla_fila'>
                            <select id='ciud_id' name='ciud_id' style='font-size:14px;'>
                            <?php 
                                $coms=cargar_registros_obj("SELECT * FROM comunas ORDER BY ciud_desc;", true);
                                for($i=0;$i<sizeof($coms);$i++)
                                {
                                    if($pac['ciud_id']==$coms[$i]['ciud_id'])
                                        $sel='SELECTED';
                                    else
                                        $sel='';
                                    print("<option value='".$coms[$i]['ciud_id']."' $sel >".$coms[$i]['ciud_desc']."</option>");
                                }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Tel&eacute;fono:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_fono' name='pac_fono' value='<?php echo $pac['pac_fono']; ?>' size=20 />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Celular:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_celular' name='pac_celular' value='<?php echo $pac['pac_celular']; ?>' size=20 />
                        </td>
                    </tr>
		    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Recados:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_recados' name='pac_recados' value='<?php echo $pac['pac_recados']; ?>' size=20 />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>e-mail:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_mail' name='pac_mail' value='<?php echo $pac['pac_mail']; ?>' size=30 />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Ocupaci&oacute;n:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_ocupacion' name='pac_ocupacion' value='<?php echo $pac['pac_ocupacion']; ?>' style='font-size:14px;font-weight:bold;' />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Representante:</td>
                        <td style='tabla_fila'>
                            <input type='text' id='pac_padre' name='pac_padre' value='<?php echo $pac['pac_padre']; ?>' style='font-size:14px;font-weight:bold;' />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Previsi&oacute;n:</td>
                        <td style='tabla_fila'>
                            <select id='prev_id' name='prev_id' style='font-size:14px;' DISABLED>
                            <?php 
                                $prvs=cargar_registros_obj("SELECT * FROM prevision ORDER BY prev_id;", true);
                                for($i=0;$i<sizeof($prvs);$i++)
                                {
                                    if($pac['prev_id']==$prvs[$i]['prev_id'])
                                        $sel='SELECTED';
                                    else
                                        $sel='';
                                    print("<option value='".$prvs[$i]['prev_id']."' $sel >".$prvs[$i]['prev_desc']."</option>");
                                }
                            ?>
                            </select>
                            <i>&Uacute;ltima Actualizaci&oacute;n: <span id='ult_act' style='font-weight:bold;'><?php echo substr($pac['fecha_fonasa'],0,16); ?></span></i> <img src='../../imagenes/ajax-loader1.gif' id='cargar_fonasa' />
                        </td>
                    </tr>
                </table>
                <div class='sub-content'>
                    <img src='../../iconos/table.png'>
                    <b>Prestaciones</b>
                </div>
                <div class='sub-content2' id='lista_presta'>
                    <table style='width:100%;font-size:16px;'>
                        <tr class='tabla_header'>
                            <td>&nbsp;</td>
                            <td>C&oacute;digo</td>
                            <td>Descripci&oacute;n</td>
                        </tr>
                        <?php 
                            $esp_desc=$n['esp_desc'];
                            $doc_nombres=$n['doc_nombres'];
                            $doc_id=$n['doc_id'];
                            $nom_motivo=$n['nom_motivo'];
                            //$consulta="SELECT DISTINCT presta_codigo, glosa FROM prestaciones_tipo_atencion LEFT JOIN codigos_prestacion ON presta_codigo=codigo WHERE esp_desc ILIKE '$esp_desc' AND doc_nombres ILIKE '$doc_nombres' AND nom_motivo ILIKE '$nom_motivo';";
                            $consulta="SELECT DISTINCT presta_codigo, COALESCE(glosa,presta_desc)as glosa FROM prestaciones_tipo_atencion LEFT JOIN codigos_prestacion ON presta_codigo=codigo WHERE esp_desc ILIKE '$esp_desc' AND nom_motivo ILIKE '$nom_motivo' AND doc_id=$doc_id;";
                            
                            $p=cargar_registros_obj($consulta, true);
                            $cods='';
                            if($p)
                            {
                                for($i=0;$i<sizeof($p);$i++)
                                {
                                    $clase=($i%2==0?'tabla_fila':'tabla_fila2');
                                    print("
                                    <tr class='$clase'>
                                        <td>
                                            <center>
                                                <input type='checkbox' id='presta_".$p[$i]['presta_codigo']."' name='presta_".$p[$i]['presta_codigo']."' value='1' CHECKED />
                                            </center>
                                        </td>
                                        <td style='font-weight:bold;text-align:center;'>".$p[$i]['presta_codigo']."</td>
                                        <td>".$p[$i]['glosa']."</td>
                                    </tr>");
                                    $cods.=$p[$i]['presta_codigo']."|";
                                }
                            }
                            $cods=trim($cods,'|');
                        ?>
                    </table>
                </div>
                <center>
                    <input type='button' id='guarda' name='guarda' value='--[[ Guardar Datos de Paciente... ]]--' onClick='guardar_cupo();' style='font-size:18px;margin:5px;' />
                </center>
            </div>
            <input type='hidden' id='codigos' name='codigos' value='<?php echo $cods; ?>' />
        </form>
    </body>
</html>