<?php
    require_once('../conectar_db.php');
    if(isset($_GET['pac_id']))
    {
        $pac_id=$_GET['pac_id']*1;
    }
    else
    {
        $pac_id=false;
    }
    if(isset($_GET['bod_id']))
    {
        $bod_id=$_GET['bod_id']*1;
    }
    else
    {
        $bod_id=false;
    }
    
    
?>
<script>
    /*
    agregar_pat2 = function(autf_id,pac_id,bod_id)
    {
        var pat=autf_id
        if(pat=='')
            return;
        var val='';
        var param='pac_id='+encodeURIComponent(pac_id);
	param+='&accion=agregar&pacpat_desc='+encodeURIComponent(val);
        param+='&pat='+encodeURIComponent(pat);
	param+='&farma='+encodeURIComponent(bod_id);
        param+='&opcion_patologia=2';
        var myAjax=new Ajax.Updater('lista_patologias',
        'lista_patologias.php',
	{
            method: 'get',
            parameters: param,
            evalScripts: true,
            onComplete: function() {
                //window.close();
            }
	});
    }
    */
</script>
<html>
    <title>Conexi&oacute;n SIGGES</title>
    <?php cabecera_popup('..'); ?>
    <body class='fuente_por_defecto popup_background'>
    <?php
        ob_start();
	require_once('../conectores/sigges/descargar_paciente.php');
	$json=ob_get_contents();
	ob_end_clean();
	$datos=json_decode($json, true);
    ?>
    <table style='width:100%;'>
        <tr>
            <td colspan=2 class='sub-content' style='font-weight:bold;text-align:center;font-size:14px;'>Informaci&oacute;n de Registro SIGGES</td>
	</tr>
        <tr>
            <td style='text-align:right;width:30%;' class='tabla_fila2'>RUT:</td>
            <td style='font-size:16px;font-weight:bold;'><?php echo $datos['rut']; ?></td>
	</tr>
	<tr>
            <td style='text-align:right;width:30%;' class='tabla_fila2'>Nombre:</td>
            <td style='font-size:16px;font-weight:bold;'><?php echo $datos['nombre']; ?></td>
	</tr>
    </table>
    <table style='width:100%;font-size:14px;'>
        <tr class='tabla_header'>
            <td>#</td>
            <td>Problema de Salud GES</td>
	</tr>
        <?php 
        $casos=$datos['casos'];
        for($i=0;$i<sizeof($casos);$i++)
        {
            $consulta="SELECT * FROM autorizacion_farmacos where upper(autf_patologia_ges)=upper('".$casos[$i]['xproblema']."') ORDER BY autf_nombre";
            $reg_pst_autf = cargar_registros_obj($consulta,true);
            if($reg_pst_autf)
            {
                if(count($reg_pst_autf)>1)
                {
                   $multi_asig=true;
                }
                else
                {
                    $multi_asig=false;
                    $autf_id=$reg_pst_autf[0]['autf_id'];
                }
            }
            else
            {
                $reg_pst_autf=false;
            }
            
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase'>");
                print("<td style='text-align:right;font-weight:bold;font-size:24px;' rowspan=3>".($i+1)."</td>");
                if($reg_pst_autf)
                {
                    if($multi_asig)
                    {
                        print("<td style='font-size:16px;'><b>".$casos[$i]['xproblema']."</b>&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;<span id='span_cantidad_relaciones' class='texto_tooltip' Onclick=''><i>(Se encontraron multiples asignaciones)</i></span></td>");
                    }
                    else
                    {
                        if($casos[$i]['nombre']!="Caso Cerrado")
                        {
                            if($pac_id!=false)
                            {
                                print("<td style='font-size:16px;'><b>".$casos[$i]['xproblema']."</b>&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;<span id='span_cantidad_relaciones' class='texto_tooltip' Onclick='agregar_pat2($autf_id,$pac_id,$bod_id);'><i>(Asociar Patolog&iacute;a a Paciente)</i></span></td>");
                            }
                            else
                            {
                                print("<td style='font-size:16px;'><b>".$casos[$i]['xproblema']."</b></td>");
                            }
                            
                        }
                        else
                        {
                            print("<td style='font-size:16px;'><b>".$casos[$i]['xproblema']."</b></td>");
                        }
                    }
                }
                else
                {
                    print("<td style='font-size:16px;'><b>".$casos[$i]['xproblema']."</b></td>");
                }
            print("</tr>");
            print("<tr class='$clase'>");
                print("<td><b>".$casos[$i]['estado']."</b></td>");
            print("</tr>");
            print("<tr class='$clase'>");
                print("<td><i>".$casos[$i]['nombre']."</i></td>");
            print("</tr>");
	}
        ?>
    </table>
</body>
</html>