<?php
    error_reporting(E_ALL);
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    pg_query("START TRANSACTION;");
    $fi=explode("\n", file_get_contents('especialidad_prestacion_adr.csv'));
    for($i=1;$i<sizeof($fi);$i++)
    {
        print("<br>");
        print("<br>");
        print("Linea - ".$i);
        print("<br>");
        print("<br>");
        
        if(trim($fi[$i])=='')
            continue;
           
        $r=explode(';',$fi[$i]);
        
        //print("<br>");
        //print("<br>");
        //print_r($r);
        print("<br>");
        print("<br>");
        $esp_id=trim(strtoupper($r[0]));
        $esp_desc=trim(strtoupper($r[1]));
        print("<br>");
        print("Especialidades id:".$esp_id." -- Descripción: ".$esp_desc);
        print("<br>");
        
        $rut_doc=trim(strtoupper($r[2]));
        $nom_doc=trim(strtoupper($r[3]));
        $tipo_atencion=trim(strtoupper($r[4]));
        $prestacion_fonasa=trim(strtoupper($r[5]));
        $codigo_presta=trim(strtoupper($r[6]));
        $rut=strtoupper(str_replace('.','',trim($rut_doc)));
        $codigo_presta=strtoupper(str_replace('-','',trim($codigo_presta)));
        if($rut!='')
        {
            if(!strstr($rut, '-'))
            {
                print("<br>");
                print("<br>");
                print("Rut Sin Guion: ".$rut);
                print("<br>");
                print("<br>");
                die();
            }
        }
        
        //$r=explode(';',$fi[$i]);
        
        $reg_esp=cargar_registro("SELECT * FROM especialidades where upper(esp_desc)=upper('$esp_desc')");
        if(!$reg_esp)
        {
            print("<br>");
            print("<br>");
            print("Especialidad No Encontrada: ".$esp_desc);
            print("<br>");
            print("<br>");
        }
        $reg_motivo=cargar_registro("select * from (select distinct nom_motivo from nomina order by nom_motivo)as foo where upper(nom_motivo)=upper('$tipo_atencion')");
        if(!$reg_motivo)
        {
            print("<br>");
            print("<br>");
            print("Motivo No Encontrado: ".$tipo_atencion);
            print("<br>");
            print("<br>");
            
        }
        $esp_id=$reg_esp['esp_id']*1;
        if($rut!='')
        {
            $reg_doc=cargar_registro("SELECT * FROM doctores where upper(doc_rut)=upper('$rut')");
            if(!$reg_doc)
            {
                print("<br>");
                print("<br>");
                print("Doctor No Encontrado: ".$esp_desc."-".$rut."-".$nom_doc);
                print("<br>");
                print("<br>");
                die();
            }
            else
            {
                $doc_id=$reg_doc['doc_id']*1;
                $doc_nombres=$reg_doc['doc_nombres']." ".$reg_doc['doc_paterno']." ".$reg_doc['doc_materno'];
                print("<br>");
                print("<br>");
                print("select * from prestaciones_tipo_atencion where esp_id=$esp_id and doc_id=$doc_id and presta_codigo='".trim($codigo_presta)."' and upper(nom_motivo)='".trim($tipo_atencion)."'");
                print("<br>");
                print("<br>");
                
                $reg_presta_atencion=cargar_registro("select * from prestaciones_tipo_atencion where esp_id=$esp_id and doc_id=$doc_id and presta_codigo='".trim($codigo_presta)."' and upper(nom_motivo)='".trim($tipo_atencion)."'");
                if(!$reg_presta_atencion)
                {
                    print("<br>");
                    print("<br>");
                    print("INSERT INTO prestaciones_tipo_atencion VALUES (upper('".trim($esp_desc)."'), upper('".trim($doc_nombres)."'), upper('".trim($tipo_atencion)."'), lower('".trim($prestacion_fonasa)."'), upper('".trim($codigo_presta)."'),$esp_id,$doc_id);");
                    print("<br>");
                    print("<br>");
                    pg_query("INSERT INTO prestaciones_tipo_atencion VALUES (upper('".trim($esp_desc)."'), upper('".trim($doc_nombres)."'), upper('".trim($tipo_atencion)."'), lower('".trim($prestacion_fonasa)."'), upper('".trim($codigo_presta)."'),$esp_id,$doc_id);");
                }
            }
        }
        else
        {
            $reg_doc=cargar_registro("SELECT * FROM doctores where upper(doc_nombres)=upper('$nom_doc')");
            if(!$reg_doc)
            {
                print("<br>");
                print("<br>");
                print("Doctor No Encontrado por Nombre: ".$esp_desc."-".$nom_doc);
                print("<br>");
                print("<br>");
                die();
            }
            $doc_id=$reg_doc['doc_id']*1;
            $doc_nombres=$reg_doc['doc_nombres'];
            $reg_presta_atencion=cargar_registro("select * from prestaciones_tipo_atencion where esp_id=$esp_id and doc_id=$doc_id and presta_codigo='".trim($codigo_presta)."' and nom_motivo='".trim($tipo_atencion)."'");
            if(!$reg_presta_atencion)
            {
                print("<br>");
                print("2do Insert");
                print("<br>");
                print("INSERT INTO prestaciones_tipo_atencion VALUES (upper('".trim($esp_desc)."'), upper('".trim($doc_nombres)."'), upper('".trim($tipo_atencion)."'), lower('".trim($prestacion_fonasa)."'), upper('".trim($codigo_presta)."'),$esp_id,$doc_id);");
                print("<br>");
                print("<br>");
                pg_query("INSERT INTO prestaciones_tipo_atencion VALUES (upper('".trim($esp_desc)."'), upper('".trim($doc_nombres)."'), upper('".trim($tipo_atencion)."'), lower('".trim($prestacion_fonasa)."'), upper('$codigo_presta'),$esp_id,$doc_id);");
            }
        }
    }
    pg_query("COMMIT");
?>