<?php 
    require_once('../../conectar_db.php');
    $nomd_id=$_GET['nomd_id']*1;
    $tipo=$_GET['tipo']*1;
    $valor=$_GET['valor']*1;
    if(isset($_GET['llamada']))
    {
        if($tipo==1)
        {
            $llamada=pg_escape_string($_GET['llamada']);
            if($valor=="")
            {   
                $nd=cargar_registro("SELECT nomd_codigo_no_atiende FROM nomina_detalle WHERE nomd_id=$nomd_id", true);
                if($nd)
                    $valor=$nd['nomd_codigo_no_atiende']*1;
            }
        }
        if($tipo==2)
        {
            $llamada=pg_escape_string($_GET['llamada']);
            if($valor=="")
            {   
                $nd=cargar_registro("SELECT inst_id FROM nomina_detalle WHERE nomd_id=$nomd_id", true);
                if($nd)
                    $valor=$nd['inst_id']*1;
            }
        }
    }
    else
    {
        if($tipo==1)
        {
            $llamada="";
            if($valor=="")
            {
                $nd=cargar_registro("SELECT nomd_codigo_no_atiende FROM nomina_detalle WHERE nomd_id=$nomd_id", true);
                if($nd)
                    $valor=$nd['nomd_codigo_no_atiende']*1;
            }
        }
    }
    switch($tipo)
    {
        case 0:
            $query="SELECT * FROM nomina_codigo_suspende ORDER BY susp_id";
            $titulo='Motivos de Suspensi&oacute;n';
            $campo1='susp_id';
            $campo2='susp_desc';
            $nombre_campo='nomd_codigo_susp_'.$nomd_id;
            break;
        case 1:
            $query="SELECT * FROM nomina_codigo_no_atiende ORDER BY noat_id";
            $titulo='Motivos de No Atenci&oacute;n';
            $campo1='noat_id';
            $campo2='noat_desc';
            $nombre_campo='nomd_codigo_no_atiende_'.$nomd_id;
            break;
	case 2:
            if($llamada=="ficha")
                $query="SELECT * FROM instituciones WHERE inst_codigo_ifl is not null and not inst_codigo_ifl='' and inst_id=$valor ORDER BY inst_nombre";
            else
                $query="SELECT * FROM instituciones WHERE inst_codigo_ifl is not null and not inst_codigo_ifl='' ORDER BY inst_nombre";
            $titulo='Instituciones de Or&iacute;gen';
            $campo1='inst_id';
            $campo2='inst_nombre';
            $nombre_campo='nomd_institucion_'.$nomd_id;
            break;
    }
    $l=cargar_registros_obj($query, true);
    $nd=cargar_registro("SELECT * FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN especialidades ON nom_esp_id=esp_id
    JOIN doctores ON nom_doc_id=doc_id
    JOIN pacientes USING (pac_id)
    WHERE nomd_id=$nomd_id
    ", true);

?>
<html>
    <title><?php echo $titulo; ?></title>
    <?php cabecera_popup('../..'); ?>
    <script>
    sel_codigo=function(cod)
    {
        <?php 
        if(!isset($_GET['consulta']))
        {
        ?>
            if($('llamada').value=="ficha")
            {
            
                $j('#nomd_codigo_no_atiende').val(cod);
                $("motivo_nomina_form").win_obj.close();
                //window.opener.parent.abrir_nomina(<?php echo $nd['nom_id']*1;?>, 0);
            }
            else
            {
                <?php
                if(isset($_GET['index']))
                {
                ?>
                    window.opener.$('<?php echo $nombre_campo; ?>').value=cod;
                    var fn=window.opener.calcular_totales.bind(window.opener);
                    fn(<?php echo ($_GET['index']*1);?>);
                    window.close();
                <?php
                }
                ?>
            }
        <?php
        }
        else
        {
        ?>
            
            var myAjax=new Ajax.Request('../consultar_paciente/sql_suspension.php',
            {
                method:'post',parameters:'nomd_id=<?php echo $nomd_id; ?>&codigo_suspension='+encodeURIComponent(cod),
                onComplete:function(r)
                {
                    var fn=window.opener.buscar.bind(window.opener);
                    fn();
                    window.close();
                }
            });
        <?php
        }
        ?>
    }
    </script>
    <body style='fuente_por_defecto popup_background' topmargin=0>
        <input type='hidden' id='llamada' name='llamada' value='<?php echo $llamada; ?>' />
        <center>
            <table style='width:75%;font-size:11px;'>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>Fecha/Hora:</td>
                    <td class='tabla_fila' style='font-size:16px;'>
                        <?php echo substr($nd['nom_fecha'],0,10).' '.substr($nd['nomd_hora'],0,5); ?>
                    </td>
                </tr>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>Especialidad:</td>
                    <td class='tabla_fila'><?php echo $nd['esp_desc']; ?></td>
                </tr>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>Profesional/Servicio:</td>
                    <td class='tabla_fila'><?php echo $nd['doc_paterno'].' '.$nd['doc_materno'].' '.$nd['doc_nombres']; ?></td>
                </tr>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>R.U.N.:</td>
                    <td class='tabla_fila'><b><?php echo $nd['pac_rut'].'</b> Ficha: <b>'.$nd['pac_ficha'].'</b>'; ?></td>
                </tr>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>Nombre Completo:</td>
                    <td class='tabla_fila'><?php echo $nd['pac_appat'].' '.$nd['pac_apmat'].' '.$nd['pac_nombres']; ?></td>
                </tr>
            </table>
            <h2><u><?php echo $titulo; ?></u></h2><br/>
            <table style='width:100%;'>
                <tr class='tabla_header'>
                    <td>C&oacute;digo</td>
                    <td>Descripci&oacute;n</td>
                </tr>
                <?php 
                for($i=0;$i<sizeof($l);$i++)
                {
                    $clase=($i%2==0?'tabla_fila':'tabla_fila2');
                    if($l[$i][$campo1]==$valor)
                    {
                        $style='background-color:black;color:white;';
                    }
                    else
                    {
                        $style='';
                    }
                    if($tipo==2 and $llamada=="ficha")
                    {
                        print("<tr class='$clase' style='cursor:pointer;$style'
                        onMouseOver='this.className=\"mouse_over\";'
                        onMouseOut='this.className=\"$clase\";'
                        onClick=''>
                        <td style='text-align:right;font-weight:bold;'>".$l[$i][$campo1]."</td>
                        <td>".$l[$i][$campo2]."</td>
                        </tr>");
                    }
                    else 
                    {
                        print("<tr class='$clase' style='cursor:pointer;$style'
                        onMouseOver='this.className=\"mouse_over\";'
                        onMouseOut='this.className=\"$clase\";'
                        onClick='sel_codigo(\"".$l[$i][$campo1]."\");'>
                        <td style='text-align:right;font-weight:bold;'>".$l[$i][$campo1]."</td>
                        <td>".$l[$i][$campo2]."</td>
                        </tr>");
                    }
                }
                ?>
            </table>
        </center>
    
    </body>
</html>