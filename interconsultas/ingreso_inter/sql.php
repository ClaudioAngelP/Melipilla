<?php    require_once('../../conectar_db.php');    if(!isset($_POST['nro_folio']))    {        $folio=-1;    }    else    {        $folio=$_POST['nro_folio']*1;        if($folio==0)            $folio=-1;    }    if(!isset($_POST['inst_id1']))    {        $inst_id1=3870;    }    else    {        $inst_id1=$_POST['inst_id1']*1;    }        if(!isset($_POST['inst_id2']))    {        $inst_id2=3870;    }    else    {        $inst_id2=$_POST['inst_id2']*1;    }            $fundamentos=iconv("UTF-8", "ISO-8859-1", pg_escape_string($_POST['inter_funda']));    if(!isset($_POST['inter_examen']))    {        $examenes="";    }    else    {        $examenes=iconv("UTF-8", "ISO-8859-1", pg_escape_string($_POST['inter_examen']));    }        $comentarios=iconv("UTF-8", "ISO-8859-1", pg_escape_string($_POST['inter_comenta']));    $diagnostico=iconv("UTF-8", "ISO-8859-1", pg_escape_string($_POST['diag_cod']));    $motivo=$_POST['motivo']*1;    $garantia_id=$_POST['pat_id'];    $patrama_id=$_POST['patrama_id']*1;    if(stristr($garantia_id, 'G'))    {        $garantia_id=substr($garantia_id, 1, strlen($garantia_id))*1; $pat_id='0';    }    else    {        $pat_id=substr($garantia_id, 1, strlen($garantia_id))*1; $garantia_id=0;    }    $id=($_POST['paciente_id']*1);    // Comprueba el Numero de Folio...    if($folio!=-1)    {        $comprobar = pg_query($conn,"SELECT * FROM interconsulta WHERE inter_folio=$folio;");        if(pg_num_rows($comprobar)>=1)        {            exit('N&uacute;mero de Folio "'.$folio.'" previamente ingresado al sistema.');        }    }        $prof_id=($_POST['prof_id']*1);    if(!isset($_POST['inter']))    {        if($prof_id==0)        {            pg_query("INSERT INTO profesionales_externos VALUES (            DEFAULT,            '".pg_escape_string($_POST['prof_paterno'])."',            '".pg_escape_string($_POST['prof_materno'])."',            '".pg_escape_string($_POST['prof_nombres'])."',            '".pg_escape_string($_POST['prof_rut'])."'            )");            $reg=cargar_registro("SELECT CURRVAL('profesionales_externos_prof_id_seq') AS id;");            $prof_id=$reg['id'];        }                    }    if(!isset($_POST['inter']))    {        $prioridad='DEFAULT';        $unidad_recep=0;        $especialidad=$_POST['esp_id']*1;        $inter_estado=0;    }    else    {        $prioridad=$_POST['priori_inter'];        $unidad_recep=$_POST['esp_id']*1;        $especialidad=$_POST['esp_orig']*1;        $inter_estado=1;            }    /*    print("INSERT INTO interconsulta VALUES(DEFAULT,$folio,$inst_id1,$especialidad,0,'$fundamentos','$examenes','$comentarios',$id,$inst_id2,    0, DEFAULT, DEFAULT, DEFAULT, DEFAULT, '$diagnostico', $motivo, $garantia_id, $pat_id, $prof_id, $patrama_id, now(), now());");    die();     *      */    // Ingreso de Interconsulta...    /*    pg_query($conn, "INSERT INTO interconsulta VALUES(DEFAULT,$folio,$inst_id1,$especialidad,0,'$fundamentos','$examenes','$comentarios',$id,$inst_id2,    0, DEFAULT, DEFAULT, DEFAULT, DEFAULT, '$diagnostico', $motivo, $garantia_id, $pat_id, $prof_id, $patrama_id, now(), now());");     *      */    if(isset($_POST['nomd_id_origen']))    {        $nomd_id_origen=$_POST['nomd_id_origen']*1;        pg_query($conn, "INSERT INTO interconsulta VALUES(DEFAULT,$folio,$inst_id1,$especialidad,$unidad_recep,$inter_estado,'$fundamentos','$examenes','$comentarios',$id,$inst_id2,        0, DEFAULT, DEFAULT, DEFAULT, $prioridad, '$diagnostico', $motivo,'',$garantia_id, 0, $prof_id, $patrama_id, now(), now(),DEFAULT,DEFAULT,".$_SESSION['sgh_usuario_id'].",        DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,now(),$pat_id,0,null,'',$nomd_id_origen);");    }    else    {        pg_query($conn, "INSERT INTO interconsulta VALUES(DEFAULT,$folio,$inst_id1,$especialidad,$unidad_recep,$inter_estado,'$fundamentos','$examenes','$comentarios',$id,$inst_id2,        0, DEFAULT, DEFAULT, DEFAULT, $prioridad, '$diagnostico', $motivo,'',$garantia_id, 0, $prof_id, $patrama_id, now(), now(),DEFAULT,DEFAULT,".$_SESSION['sgh_usuario_id'].",        DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,now(),$pat_id);");            }        $inter_id="";    $last_inter=cargar_registro("SELECT inter_id FROM interconsulta where inter_id=CURRVAL('interconsulta_inter_id_seq');");    if($last_inter)    {        $inter_id=$last_inter['inter_id'];    }    print("OK|".$inter_id);?>