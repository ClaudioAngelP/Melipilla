<?php
    require_once('../../conectar_db.php');
    error_reporting(E_ALL);
    if(isset($_POST['servicio']))
    {
        if(($_POST['servicio']*1)==1)
        {
            $servicio=true;
            $centro_ruta=pg_escape_string($_POST['centro_ruta']);
        }
        else
        {
            $servicio=false;
            
        }
    }
    else
    {
        $servicio=false;
        
    }
    $func_id=$_SESSION['sgh_usuario_id']*1;
    if(isset($_POST['check_lista']))
    {
        $lista=true;
        $lista_fichas=json_decode($_POST['list_fichas'],true);
    }
    else
    {
        $lista=false;
    }
    $fichas_encontradas = array();
    if(!$lista)
    {
        if(!$servicio)
        {
            $pid=$_POST['pac_id']*1;
            $did=$_POST['doc_id']*1;
            $eid=$_POST['esp_id']*1;
            //$motivo=$_POST['motivo'];
            $aid=$_POST['amp_id']*1;
            $w_origen="and esp_id=$eid";
            
            $registro=cargar_registro("select * from ficha_espontanea where pac_id=$pid and fesp_fecha::date=current_timestamp::date $w_origen;");
            if(!$registro)
            {
                $p=cargar_registro("SELECT pac_ficha FROM pacientes WHERE pac_id=$pid;");
                pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,$eid,$did,$pid,'".$p['pac_ficha']."',current_timestamp,0,null,$aid,null,$func_id)");
            }
            else
            {
                $fichas_encontradas[]=$registro['pac_id'];
            }
            if(count($fichas_encontradas)>0)
            {
                echo json_encode(array($fichas_encontradas));
            }
            else
            {
                echo json_encode(array(false));
            }
        }
        else
        {
            $pid=$_POST['pac_id']*1;
            $aid=$_POST['amp_id']*1;
            $w_origen="and fesp_centro_ruta='$centro_ruta'";
            $registro=cargar_registro("select * from ficha_espontanea where pac_id=$pid and fesp_fecha::date=current_timestamp::date $w_origen;");
            if(!$registro)
            {
                $p=cargar_registro("SELECT pac_ficha FROM pacientes WHERE pac_id=$pid;");
                pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,0,0,$pid,'".$p['pac_ficha']."',current_timestamp,0,null,$aid,'$centro_ruta',$func_id)");
            }
            else
            {
                $fichas_encontradas[]=$registro['pac_id'];
            }
            if(count($fichas_encontradas)>0)
            {
                echo json_encode(array($fichas_encontradas));
            }
            else
            {
                echo json_encode(array(false));
            }
        }
    }
    else
    {
        if(!$servicio)
        {
            $did=$_POST['doc_id']*1;
            $eid=$_POST['esp_id']*1;
        }
        $aid=$_POST['amp_id']*1;
        for($i=0;$i<count($lista_fichas);$i++)
        {
            $pid=$lista_fichas[$i]['pac_id'];
            $pac_ficha=$lista_fichas[$i]['pac_ficha'];
            if(!$servicio)
            {
                $w_origen="and esp_id=$eid";
                $registro=cargar_registro("select * from ficha_espontanea where pac_id=$pid and fesp_fecha::date=current_timestamp::date $w_origen;");
                if(!$registro)
                    pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,$eid,$did,$pid,'".$pac_ficha."',current_timestamp,0,null,$aid,null,$func_id)");
                else
                    $fichas_encontradas[]=$registro['pac_id'];
                    
            }
            else
            {
                $w_origen="and fesp_centro_ruta='$centro_ruta'";
                $registro=cargar_registro("select * from ficha_espontanea where pac_id=$pid and fesp_fecha::date=current_timestamp::date $w_origen;");
                if(!$registro)
                    pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,0,0,$pid,'".$pac_ficha."',current_timestamp,0,null,$aid,'$centro_ruta',$func_id)");
                else
                    $fichas_encontradas[]=$registro['pac_id'];
            }
        }
        if(count($fichas_encontradas)>0)
        {
            echo json_encode(array($fichas_encontradas));
        }
        else
        {
            echo json_encode(array(false));
        }
    }
?>