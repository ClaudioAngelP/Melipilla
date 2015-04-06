<?php
    error_reporting(E_ALL);
    //require_once('../conectar_db.php');
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    require_once('../conectores/fonasa/cargar_paciente_fonasa.php');
    set_time_limit(0);
    //-----------------------------------------------------------------------
    $l=explode("\n",file_get_contents("agenda_ant/Agenda 2014.csv"));
    //pg_query("START TRANSACTION;");
    //for($i=138425;$i<sizeof($l);$i++)
    for($i=1;$i<sizeof($l);$i++)
    {
        print('\n');
        print('Linea '.$i);
	print('\n');
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        if(trim($l[$i])=='')
            continue;
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
	print('\n');
        print($l[$i]);
	print('\n');

        $r=explode(';',$l[$i]);
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        if(trim($r[11])=='')
	{
            print("\n");
            print("Linea Falta Ficha: ".$i );
            print("\n");
            continue;
	}
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        if(trim($r[14])=='')
        {
            print("\n");
            print("Linea Falta Rut: ".$i );
            print("\n");
            continue;
	}
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
	$pac_ficha=trim($r[11]);
        $direccion= str_replace("'"," ",$r[16]);
        $pac_rut=strtoupper(str_replace('.','',trim($r[14])));
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
	print("\n");
        print("SELECT * FROM pacientes WHERE upper(pac_rut)=upper('$pac_rut')");
        print("\n");
        $pac=cargar_registro("SELECT * FROM pacientes WHERE upper(pac_rut)=upper('$pac_rut')");
        if($pac)
        {
            print("\n");
            print("Paciente encontrado: ".$pac_rut ." pac_ficha: ".$pac_ficha);
            print("\n");
            $pac_id=$pac['pac_id']*1;
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            $nombres=pg_escape_string(trim($r[13]));
            if(strstr($nombres,','))
            {
                $name=explode(',',$nombres);
                $paciente_nombre=$name[1];
                $name=explode(' ',$name[0]);
                $paciente_appat=$name[0];
                $paciente_apmat=$name[1];
            }
            else
            {
                $name=explode(' ',$nombres);
                $paciente_appat=$name[0];
                $paciente_apmat=$name[1];
                $paciente_nombre=$name[2];
            
                if(isset($name[3]))
                    $paciente_nombre.=' '.$name[3];
                if(isset($name[4]))
                    $paciente_nombre.=' '.$name[4];
            
            }
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            pg_query("update pacientes set pac_ficha='$pac_ficha',pac_direccion='$direccion',pac_nombres='$paciente_nombre',pac_appat='$paciente_appat',pac_apmat='$paciente_apmat' where pac_id=$pac_id;");
        }
        else
        {
            print("\n");
            print("Paciente No encontrado: ".$pac_rut);
            print("\n");
            print("\n");
            //------------------------------------------------------------------
            $nombres=pg_escape_string(trim($r[13]));
            if(strstr($nombres,','))
            {
                $name=explode(',',$nombres);
                $paciente_nombre=$name[1];
                $name=explode(' ',$name[0]);
                $paciente_appat=$name[0];
                $paciente_apmat=$name[1];
            }
            else
            {
                $name=explode(' ',$nombres);
                $paciente_appat=$name[0];
                $paciente_apmat=$name[1];
                $paciente_nombre=$name[2];
            
                if(isset($name[3]))
                    $paciente_nombre.=' '.$name[3];
                if(isset($name[4]))
                    $paciente_nombre.=' '.$name[4];
            
            }
            //------------------------------------------------------------------
            $paciente_fc_nac=trim($r[15]);
            if($paciente_fc_nac!='')
            {
                $paciente_fc_nac="'".strtoupper(str_replace('-','/',trim($paciente_fc_nac)))."'";
            }
            else
            {
                $paciente_fc_nac='null';
            }
            
            //------------------------------------------------------------------
            $paciente_sex_id=trim($r[21]);
            if(trim($paciente_sex_id)=="M")
                $paciente_sex_id=0;
            elseif(trim($paciente_sex_id)=="F")
                $paciente_sex_id=1;
            else
                $paciente_sex_id=8;
            //------------------------------------------------------------------
            $paciente_prevision=trim($r[18]);
            //------------------------------------------------------------------
            $tramo_fonasa="";
            //------------------------------------------------------------------
            if($paciente_prevision=="FONASA")
            {
                $paciente_grupo=trim($r[19]);
                if($paciente_grupo=="Grupo A")
                {
                    $prevision=1;
                    $tramo_fonasa="A";
                }
                else if($paciente_grupo=="Grupo B")
                {
                    $prevision=2;
                    $tramo_fonasa="B";
                }
                else if($paciente_grupo=="Grupo C")
                {
                    $prevision=3;
                    $tramo_fonasa="C";
                }
                else if($paciente_grupo=="Grupo D")
                {
                    $prevision=4;
                    $tramo_fonasa="D";
                }
                else 
                {
                    $prevision=8;
                }
            }
            else if($paciente_prevision=="ISAPRE")
            {
                $prevision=5;
            }
            else if($paciente_prevision=="Particular")
            {
                $prevision=6;
            }
            else if($paciente_prevision=="Prevision Provisoria")
            {
                $prevision=10;
            }
            else if($paciente_prevision=="Programa Social")
            {
                $prevision=11;
            }
            else if($paciente_prevision=="")
            {
                $prevision=8;
            }
            //------------------------------------------------------------------
            $comuna=trim($r[17]);
            $ciud_id = cargar_registro("SELECT ciud_id FROM comunas WHERE ciud_desc='".$comuna."'", true);
            if($ciud_id)
            {
                $ciud_id = $ciud_id['ciud_id'];
            }
            else
            {
                $ciud_id =-1;
            }
            $paciente_ciud_id=$ciud_id;
            //------------------------------------------------------------------
            $paciente_direccion= str_replace("'"," ",trim($r[16]));
            //------------------------------------------------------------------
            $paciente_fono=str_replace("'"," ",trim($r[12]));
            //------------------------------------------------------------------
            
            $agregar_paciente = "INSERT INTO pacientes VALUES
            (DEFAULT,
            upper('$pac_rut'),
            '$paciente_nombre',
            '$paciente_appat',
            '$paciente_apmat',
            $paciente_fc_nac,
            $paciente_sex_id,
            $prevision,
            null,
            -1,
            -1,
            '$paciente_direccion',
            $paciente_ciud_id,
            -1,
            null,
            '$paciente_fono',
            null,
            null,
            '$tramo_fonasa',
            null,
            '$pac_ficha',
            null,
            null,
            null,
            null,
            null,
            null,
            null
            );";
            pg_query($agregar_paciente);
        }
        flush();
    }
    //pg_query("ROLLBACK;");
?>