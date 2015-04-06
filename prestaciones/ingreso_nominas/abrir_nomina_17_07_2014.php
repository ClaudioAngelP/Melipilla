<?php 
    require_once('../../conectar_db.php');
    $ord=$_POST['orden']*1;
    $orden="nomd_hora,nomd_extra DESC,nomd_folio,pac_appat,pac_apmat,pac_nombres";
    $n=false;
    $cant_sobrecupos=0;
    $sobrecupos_nomina=0;
    if(isset($_POST['nom_id']))
    {
        if( isset($_POST['folios_nominas']) AND $_POST['folios_nominas']*1!=-1)
        {
            $nom_id = pg_escape_string($_POST['folios_nominas']*1);
        }
        else
        { 
            $nom_id = pg_escape_string($_POST['nom_id']*1);
        }
        $n=cargar_registro("SELECT *, nom_fecha::date FROM nomina LEFT JOIN doctores ON nom_doc_id=doc_id LEFT JOIN especialidades ON nom_esp_id=esp_id WHERE nom_id=$nom_id");
        $nom_id=$n['nom_id'];
        $esp_id=$n['nom_esp_id'];
        $doc_id=$n['nom_doc_id'];
        $fecha=$n['nom_fecha'];
        
        $reg_cupos_atencion=cargar_registro("select sum(cupos_cantidad_c)as cantidad from cupos_atencion where nom_id=$nom_id group by nom_id");
        if($reg_cupos_atencion)
        {
            $cant_sobrecupos=$reg_cupos_atencion['cantidad']*1;
        }
        else
        {
            $cant_sobrecupos=0;
        }
    }
    else
    {
        $nom_folio = pg_escape_string($_POST['nom_folio']);
        $consulta="SELECT *, nom_fecha::date FROM nomina 
        LEFT JOIN doctores ON nom_doc_id=doc_id
  	LEFT JOIN especialidades ON nom_esp_id=esp_id
	WHERE nom_folio='$nom_folio';";
        $chk=cargar_registro($consulta);
        if($chk)
        {
            $nom_id=$chk['nom_id']; 
            $esp_id=$chk['nom_esp_id'];
            $doc_id=$chk['nom_doc_id'];
            $fecha=$chk['nom_fecha'];
            $nom_motivo=$chk['nom_motivo'];
            $consulta="SELECT *, nom_fecha::date FROM nomina
            LEFT JOIN doctores ON nom_doc_id=doc_id
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            WHERE
            nom_id=$nom_id ORDER BY nom_folio, nom_id";
            $n=cargar_registro($consulta);
            $nom_id=$n['nom_id'];
            $reg_cupos_atencion=cargar_registro("select sum(cupos_cantidad_c)as cantidad from cupos_atencion where nom_id=$nom_id group by nom_id");
            if($reg_cupos_atencion)
            {
                $cant_sobrecupos=$reg_cupos_atencion['cantidad']*1;
            }
            else
            {
                $cant_sobrecupos=0;
            }
  	}
        /*
        else
        {
            $n=cargar_registro("SELECT * FROM nomina 
            LEFT JOIN doctores ON nom_doc_id=doc_id
            LEFT JOIN especialidades ON nom_esp_id=esp_id		
            WHERE nom_folio='$nom_folio'", true);  	
            $nom_id=$n['nom_id'];  	
            $esp_id=$n['nom_esp_id'];
            $doc_id=$n['nom_doc_id'];
            $fecha=$n['nom_fecha'];
  	}*/
    }
    if(!$n)
        exit();
    
    $consulta="SELECT DISTINCT nom_id, nom_folio,
    (SELECT COUNT(*) FROM nomina_detalle AS foo WHERE foo.nom_id=nomina.nom_id) AS cantidad
    FROM nomina 
    WHERE  nom_id=$nom_id	
    ORDER BY nom_folio";
    
    $lnom=cargar_registros_obj($consulta);
  
    
    if($lnom AND sizeof($lnom)>1)
    {
        $htmlnom='Adjuntas ('.sizeof($lnom).') : 
	<select id="folios_nominas" name="folios_nominas" style="font-size:10px;" onChange="abrir_nomina($(\'nom_id\').value*1, 0);">
        <option value="-1" SELECTED>(Todas...)</option>';  
        for($i=0;$i<sizeof($lnom);$i++)
        {
            $htmlnom.='<option value="'.$lnom[$i]['nom_id'].'">'.$lnom[$i]['nom_folio'].' ('.$lnom[$i]['cantidad'].')</option>';  
        }
        $htmlnom.='</select>';
    }
    else
    {
        $htmlnom='<i>(No hay n&oacute;minas adjuntas.)</i>';
    }
    if( isset($_POST['folios_nominas']) AND $_POST['folios_nominas']*1!=-1)
    {
        Print("Aqui");
        die();
        $lista = cargar_registros_obj("
        SELECT 
	pacientes.*, nomina_detalle.*, diag_desc, nom_motivo, esp_recurso,
	date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
	nom_esp_id, cancela_desc ,prev_desc
	FROM nomina_detalle
	JOIN nomina USING (nom_id)
	JOIN especialidades ON nom_esp_id=esp_id
	LEFT JOIN pacientes USING (pac_id)
	LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
        LEFT JOIN prevision on pacientes.prev_id=prevision.prev_id
	WHERE nom_id=$nom_id OR nomd_nom_id=$nom_id AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL)
	ORDER BY $orden
	");
	//'X',
	  
	$nom_id=$lista[0]['nom_id']*1;
	$esp_id=$lista[0]['nom_esp_id']*1;
	$nom_recurso=($lista[0]['esp_recurso']=='t');
    }
    else
    {
        $consulta="select count(nomd_hora)as cantidad,nomd_hora from nomina_detalle
        join nomina USING(nom_id)
        where nom_id=$nom_id AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL)
        group by nomd_hora";
       
        
        $grupo_hrs = cargar_registros_obj($consulta);
        
        $lote=false;
        if($grupo_hrs)
        {
            //print_r($grupo_hrs);
            //die();
            if(count($grupo_hrs)==1)
            {
                if(($grupo_hrs[0]['cantidad']*1)>1)
                {
                    $lote=true;
                    $orden="nomd_id,nomd_hora,nomd_extra DESC,nomd_folio,pac_appat,pac_apmat,pac_nombres";
                }
            }
        }
        
        $consulta="
        SELECT 
        pacientes.*, nomina_detalle.*, diag_desc,nom_motivo,esp_recurso, 
        date_part('year',age(pac_fc_nac)) as edad, cancela_desc,nom_esp_id,prev_desc
        FROM nomina_detalle
        JOIN nomina USING (nom_id)
        JOIN especialidades ON nom_esp_id=esp_id
        LEFT JOIN pacientes USING (pac_id)
        LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
        LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
        LEFT JOIN prevision on pacientes.prev_id=prevision.prev_id
        WHERE
        nom_id=$nom_id AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL)
        ORDER BY $orden
        ";
        
        $lista = cargar_registros_obj($consulta);
        
        // 'X',

	$nom_id=$lista[0]['nom_id']*1;
        $esp_id=$lista[0]['nom_esp_id']*1;
        $nom_recurso=($lista[0]['esp_recurso']=='t');
    }
    print("<input type='hidden' id='nom_id' name='nom_id' value='$nom_id' />");
    print("<input type='hidden' id='esp_id' name='esp_id' value='$esp_id' />");
    // <input type='hidden' id='esp_id' name='esp_id' value='$esp_id' />
    $ficha_clinica=false;
    //$esp_aut=_cav(313);
    //if($esp_aut!="")
    //{
        //$proc=cargar_registro("SELECT * FROM especialidades WHERE esp_id in ("._cav(313).") and esp_id=".$esp_id);
        $proc=cargar_registro("SELECT * FROM especialidades WHERE esp_id=".$esp_id);
        if($proc)
        {
            if($proc['esp_ficha']=="t")
            {
                
                $ficha_clinica=true;
                
            }
        }
    //}
    
    
    $informe=false;
    $proc=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=".$esp_id);
    if($proc)
    {
        print("<input type='hidden' id='proc' name='proc' value='1' />");
        if($proc['esp_informe']=='t')
        {
            //$informe=true;
        }
        //require_once('abrir_nomina_proc.php');
	//exit(0);
    }
    //$esp_aut_agenda=_cav(311);
    //$arr_esp=explode(',',$esp_aut_agenda);
    //$disabled_input="disabled";
    //$display_input="display:none;";
    //for($i=0;$i<count($arr_esp);$i++)
    //{
        //if($arr_esp[$i]==$esp_id)
        //{
            $disabled_input="";
            $display_input="display:block;";
            //break;
        //}
        
    //}
?>
<table style='width:100%;font-size:11px;' class='lista_small celdas' cellspacing=0>
    <tr class='tabla_header'>
        <td>#</td>
        <td>Hora</td>
        <td>RUT/Ficha</td>
        <td>Paciente</td>
        <td>S</td>
        <td>E</td>
        <td>Prev</td>
        <td>Sobrecupo</td>
        <td>Diagn&oacute;stico</td>
        <!--<td>S/Ficha</td>-->
        <td>Estado</td>
        <td>Pertinente<br />Prot/Tiempo</td>
        <td>Procedencia</td>
        <td>G.E.S.</td>
        <?php
        if($ficha_clinica)
        {
            $cols=1;
        ?>
            <td>Registro</td>
        <?php
        }
        ?>
        <?php
        if($informe)
        {
            $cols=2;
        ?>
            <td>Informe</td>
        <?php
        }
        ?>
        <td>&nbsp;</td>
    </tr>
<?php
    if(!$nom_recurso)
    {
        //$horas_html="<select id='nomd_hora' name='nomd_hora' onChange='if(this.value==\\\"00:00\\\"){ $(\\\"td_horas_extra\\\").show();} else{ $(\\\"td_horas_extra\\\").hide();}'>";
        $horas_html="<select id='nomd_hora' name='nomd_hora' onChange='verificar_sobrecupos();'>";
    }
    else
    {
        $horas_html="<select id='nomd_hora' name='nomd_hora'>";
    }
   
    $nombrecupo=$nom_recurso?'BLOQUE':'CUPO';

    $cc=0;
    if($lista){
        $completo=false;
        for($i=0;$i<count($lista);$i++)
        {
            ($cc%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            $cc++;
            //------------------------------------------------------------------
            if($lista[$i]['sex_id']==0)
                $sexo='M';
            elseif($lista[$i]['sex_id']==1)
                $sexo='F';
            else
                $sexo='I';
            //------------------------------------------------------------------
            if($lista[$i]['nomd_diag_cod']=='B')
            {
                ($cc%2==0) ? $color='#AAAAAA' : $color='#BBBBBB';
                $texto='&nbsp;';
                if($lista[$i+1]['pac_id']!=$lista[$i]['pac_id'])
                    $texto='<i>ASEO DE PABELL&Oacute;N</i>';

                print("
                <tr style='height:30px;background-color:$color;' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";' onClick=''>
                    <td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($i+1)."</td>
                    <td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
                    <td style='text-align:center;font-weight:bold;font-size:16px;' colspan=12>$texto</td>
                    <td>
                        <center>
                            <img src='iconos/delete.png'  style='cursor:pointer;' onClick='eliminar(".($lista[$i]['nomd_id']).")' />
                        </center>
                    </td>
                </tr>");	
                continue;
            }
            //------------------------------------------------------------------
            if($lista[$i]['pac_id']==0)
            {
                //$completo=false;
                ($cc%2==0) ? $color='#BBDDBB' : $color='#BBEEBB';
                if($lista[$i]['nomd_diag_cod']=='X')
                {
                    ($cc%2==0) ? $color='#ff8888' : $color='#ee8888';
                    $cestado='BLOQUEADO ('.$lista[$i]['cancela_desc'].')';
                    $boton1='';
                    
                }
                else
                {
                    
                    $string_extra="";
                    if($lista[$i]['nomd_extra']=="S")
                    {
                        $sobrecupos_nomina=$sobrecupos_nomina+1;
                        $color='#ff9933';
                        //$string_extra="EXTRA";
                    }
                    
                    $ntipo=$lista[$i]['nom_motivo'];
                    //----------------------------------------------------------
                    if($ntipo!='')
                        $cestado='DISPONIBLE ('.$ntipo.')';
                    else
                        $cestado='DISPONIBLE';
                    //----------------------------------------------------------
                    if($lote)
                    {
                        $horas_html.="<option value='".substr($lista[$i]['nomd_hora'],0,5)."_".$lista[$i]['nomd_id']."'>".substr($lista[$i]['nomd_hora'],0,5)."</option>";
                    }
                    else
                    {
                        $horas_html.="<option value='".substr($lista[$i]['nomd_hora'],0,5)."_".$lista[$i]['nomd_id']."'>".substr($lista[$i]['nomd_hora'],0,5)."</option>";
                    }
                    //----------------------------------------------------------
                    //$boton1="<img src='iconos/pencil.png'  style='cursor:pointer;' onClick='asignar(".($lista[$i]['nomd_id']).");' />";
                    $boton1="<img src='iconos/pencil.png'  style='cursor:pointer;' onClick='' />";
		}
                $hora_arr=str_replace(":",".",substr($lista[$i]['nomd_hora'],0,5));
                //$hora_arr=explode(":",$hora_arr);
                if($lote)
                {
                    //print("<tr style='height:30px;background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";' onClick=verificar_cupo(".$i.",'".substr($lista[$i]['nomd_hora'],0,5)."_".$lista[$i]['nomd_id']."');>");
                    print("<tr style='height:30px;background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";' onClick=''>");
                }
                else
                {
                    print("<tr style='height:30px;background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";' onClick=''>");
                }
                $colspan=11+$cols;
                print("
		<td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($i+1)."</td>
		<td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
		<td style='text-align:center;font-weight:bold;font-size:16px;' colspan=$colspan><i>$nombrecupo $string_extra $cestado</i></td>
                <td>
                <center>
                    $boton1
		</center>
                </td>
		</tr>
                ");
                //<img src='iconos/delete.png'  style='cursor:pointer;'
                //onClick='eliminar(".($lista[$i]['nomd_id']).")' />
                continue;
            }
            //------------------------------------------------------------------
            print("
            <input type='hidden' id='nomd_codigo_susp_".$lista[$i]['nomd_id']."' name='nomd_codigo_susp_".$lista[$i]['nomd_id']."' value='".$lista[$i]['nomd_codigo_cancela']."' />
            <input type='hidden' id='nomd_codigo_no_atiende_".$lista[$i]['nomd_id']."' name='nomd_codigo_no_atiende_".$lista[$i]['nomd_id']."' value='".$lista[$i]['nomd_codigo_no_atiende']."' />
            <input type='hidden' id='nomd_institucion_".$lista[$i]['nomd_id']."' name='nomd_institucion_".$lista[$i]['nomd_id']."' value='".$lista[$i]['inst_id']."' />
            <input type='hidden' id='nomd_tipo_".$lista[$i]['nomd_id']."' name='nomd_tipo_".$lista[$i]['nomd_id']."' value='".$lista[$i]['nomd_tipo']."' />
            <input type='hidden' id='nomd_extra_".$lista[$i]['nomd_id']."' name='nomd_extra_".$lista[$i]['nomd_id']."' value='".$lista[$i]['nomd_extra']."' />
            ");
            
            if($lista[$i]['nomd_diag_cod']!='X' AND $lista[$i]['nomd_diag_cod']!='T')
            {
                if($lista[$i]['nomd_extra']=="S")
                {
                    $sobrecupos_nomina=$sobrecupos_nomina+1;
                    $color='#ff9933';
                }
                else
                {
                    $color='';
                }
                print("<tr class='$clase' style='height:30px;background-color:$color' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";' onClick=''>");
            }
            else
            {
                ($cc%2==0) ? $color='#ff8888' : $color='#ee8888';
                print("<tr style='height:30px;background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";'>");
            }
            //------------------------------------------------------------------
            if($lista[$i]['nomd_diag_cod']=='X' OR $lista[$i]['nomd_diag_cod']=='T' OR $lista[$i]['nomd_diag_cod']=='N')
                $motivo_enabled='';
            else
		$motivo_enabled='DISABLED';
            //------------------------------------------------------------------
            if($lista[$i]['nomd_origen']=='A')
                $origen_enabled='';
            else
                $origen_enabled='DISABLED';
            //------------------------------------------------------------------
            print("
            <td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($i+1)."</td>
            <td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
            <td style='text-align:center;font-weight:bold;'>".($lista[$i]['pac_rut']!=''?$lista[$i]['pac_rut']:$lista[$i]['pac_ficha'])."</td>
            ");
            //------------------------------------------------------------------
            if($ord!=2)
                print("<td>".htmlentities(strtoupper($lista[$i]['pac_nombres'].' '.$lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat']))."</td>");
            else
                print("<td>".htmlentities(strtoupper($lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat'].' '.$lista[$i]['pac_nombres']))."</td>");   
            //------------------------------------------------------------------
            print("
            <td style='text-align:center;font-weight:bold;' id='nomd_sexo_".$lista[$i]['nomd_id']."'>".$sexo."</td>
            <td style='text-align:center;font-weight:bold;' id='nomd_edad_".$lista[$i]['nomd_id']."'>".$lista[$i]['edad']."</td>
            ");
            //------------------------------------------------------------------
            /*
            print("<td><center><select onChange='calcular_totales();' id='nomd_tipo_".$lista[$i]['nomd_id']."' name='nomd_tipo_".$lista[$i]['nomd_id']."'>
            <option value='N' ".($lista[$i]['nomd_tipo']=='N'?'SELECTED':'').">N</option>
            <option value='C' ".($lista[$i]['nomd_tipo']=='C'?'SELECTED':'').">C</option>
            <option value='P' ".($lista[$i]['nomd_tipo']=='P'?'SELECTED':'').">P</option>
	    <option value='R' ".($lista[$i]['nomd_tipo']=='R'?'SELECTED':'').">R</option>
            </select></center></td>    
            <td><center><select onChange='calcular_totales();' id='nomd_extra_".$lista[$i]['nomd_id']."' name='nomd_extra_".$lista[$i]['nomd_id']."'>
            <option value='S' ".($lista[$i]['nomd_extra']=='S'?'SELECTED':'').">Si</option>
            <option value='N' ".($lista[$i]['nomd_extra']!='S'?'SELECTED':'').">No</option>
            </select></center></td>");
            */
            print("<td style='text-align:center;font-weight:bold;font-size:16px;'>".str_replace("FONASA","",$lista[$i]['prev_desc'])."</td>");
            
            print("<td style='text-align:center;font-weight:bold;font-size:16px;'>".$lista[$i]['nomd_extra']."</td>");
            print("
            <td style='text-align:left;'>");
            $estado_cupo=true;
            if(strstr($lista[$i]['nomd_diag'],'|'))
            {
                $diagnosticos=explode("|",$lista[$i]['nomd_diag']);
                print("<b>".htmlentities($diagnosticos[0]).": </b>".htmlentities($diagnosticos[1])."");
                
            }
            else
            {
                $escrito=false;
                if($lista[$i]['nomd_diag_cod']!="OK")
                {
                    if($lista[$i]['nomd_diag_cod']!="ALTA")
                    {
                        if($lista[$i]['nomd_diag_cod']!="N")
                        {
                            if($lista[$i]['nomd_diag_cod']!="X")
                            {
                                if($lista[$i]['nomd_diag_cod']!="T")
                                {
                                    if($lista[$i]['nomd_diag_cod']!="")
                                    {
                                        //$estado_cupo=false;
                                        $escrito=true;
                                        print("<b>".htmlentities($lista[$i]['nomd_diag_cod']).": </b>".htmlentities($lista[$i]['nomd_diag'])."");
                                    }
                                }
                            }
                        }
                    }
                }
                if(!$escrito)
                    print("".htmlentities($lista[$i]['nomd_diag'])."");
            }
            /*
            if($lista[$i]['nomd_diag_cod']!="OK")
            {
                if($lista[$i]['nomd_diag_cod']!="ALTA")
                {
                    if($lista[$i]['nomd_diag_cod']!="N")
                    {
                        if($lista[$i]['nomd_diag_cod']!="X")
                        {
                            if($lista[$i]['nomd_diag_cod']!="T")
                            {
                                if($lista[$i]['nomd_diag_cod']!="")
                                {
                                    $estado_cupo=false;
                                    print("<b>".htmlentities($lista[$i]['nomd_diag_cod']).": </b>".htmlentities($lista[$i]['nomd_diag'])."");
                                }
                            }
                        }
                    }
                }
            }
            if($estado_cupo)
                print("".htmlentities($lista[$i]['nomd_diag'])."");
             * 
             */
            
            print("
                    <!--
                    <input type='text' id='nomd_diag_".$lista[$i]['nomd_id']."' name='nomd_diag_".$lista[$i]['nomd_id']."' style='width:80%;' value='".htmlentities($lista[$i]['nomd_diag'])."' $disabled_input/>
                    -->
            </td>
	     <!--
            <td>
                <center>
                    <select onChange='calcular_totales($i);' id='nomd_sficha_".$lista[$i]['nomd_id']."' name='nomd_sficha_".$lista[$i]['nomd_id']."' $disabled_input>
                        <option value='S' ".($lista[$i]['nomd_sficha']=='S'?'SELECTED':'').">S</option>
                        <option value='N' ".($lista[$i]['nomd_sficha']!='S'?'SELECTED':'').">N</option>
                    </select>
                </center>
            </td>
	     -->	
            <td style='white-space:nowrap;'>
                <center>");
                    if($estado_cupo)
                    {
                        if($lista[$i]['nomd_diag_cod']=="OK" or $lista[$i]['nomd_diag_cod']=="ALTA" or $lista[$i]['nomd_diag_cod']=="N")
                        {
                            print("<select onChange='' style='width:100px;' id='nomd_diag_cod_".$lista[$i]['nomd_id']."' name='nomd_diag_cod_".$lista[$i]['nomd_id']."' $disabled_input>");
                        }
                        else
                        {
                            if($lista[$i]['nomd_diag_cod']!='' and $lista[$i]['nomd_diag_cod']!='X' and $lista[$i]['nomd_diag_cod']!='T' )
                            {
                                print("<select onChange='' style='width:100px;' id='nomd_diag_cod_".$lista[$i]['nomd_id']."' name='nomd_diag_cod_".$lista[$i]['nomd_id']."' $disabled_input>");
                            }
                            else
                            {
                                print("<select onChange='calcular_totales($i);' style='width:100px;' id='nomd_diag_cod_".$lista[$i]['nomd_id']."' name='nomd_diag_cod_".$lista[$i]['nomd_id']."' $disabled_input>");
                            }
                        }
                    }
                    else
                    {
                        print("<select onChange='calcular_totales($i);' style='width:100px;' id='nomd_diag_cod_".$lista[$i]['nomd_id']."' name='nomd_diag_cod_".$lista[$i]['nomd_id']."' $disabled_input>");
                    }
                    
                    if($estado_cupo)
                    {
                        if($lista[$i]['nomd_diag_cod']=="OK")
                        {
                            print("<option value='OK' ".($lista[$i]['nomd_diag_cod']=='OK'?'SELECTED':'').">ATENDIDO</option>");
                            
                        }
                        else
                        {
                            if($lista[$i]['nomd_diag_cod']=="ALTA")
                            {
                                print("<option value='ALTA' ".($lista[$i]['nomd_diag_cod']=='ALTA'?'SELECTED':'').">ALTA DE ESPECIALIDAD</option>");
                            }
                            else
                            {
                                if($lista[$i]['nomd_diag_cod']=="N")
                                {
                                    print("<option value='N' ".($lista[$i]['nomd_diag_cod']=='N'?'SELECTED':'').">NO ATENDIDO</option>");
                                }
                                else
                                {
                                    if($lista[$i]['nomd_diag_cod']!='' and $lista[$i]['nomd_diag_cod']!='X' and $lista[$i]['nomd_diag_cod']!='T')
                                    {
                                        print("<option value='OK' ".($lista[$i]['nomd_diag_cod']=='OK'?'SELECTED':'').">ATENDIDO</option>");
                                    }
                                    else
                                    {
                                        print("<option value='' ".($lista[$i]['nomd_diag_cod']==''?'SELECTED':'').">AGENDADO</option>");
                                        print("<!----<option value='X' ".($lista[$i]['nomd_diag_cod']=='X'?'SELECTED':'').">BLOQUEADO</option>--->");
                                        print("<option value='T' ".($lista[$i]['nomd_diag_cod']=='T'?'SELECTED':'').">SUSPENDIDO</option>");
                                    }
                                }
                            }
                        }
                        
                        /*
                        print("<option value='OK' ".($lista[$i]['nomd_diag_cod']=='OK'?'SELECTED':'').">ATENDIDO</option>");
                        print("<option value='ALTA' ".($lista[$i]['nomd_diag_cod']=='ALTA'?'SELECTED':'').">ALTA DE ESPECIALIDAD</option>");
                        print("<option value='N' ".($lista[$i]['nomd_diag_cod']=='N'?'SELECTED':'').">NO ATENDIDO</option>");
                        print("<!----<option value='X' ".($lista[$i]['nomd_diag_cod']=='X'?'SELECTED':'').">BLOQUEADO</option>--->");
                        print("<option value='T' ".($lista[$i]['nomd_diag_cod']=='T'?'SELECTED':'').">SUSPENDIDO</option>");
                         * 
                         */
                    }
                    else
                    {

                        print("<option value=''>AGENDADO</option>");
                        //print("<option value='OK' SELECTED>ATENDIDO</option>");
                        //print("<option value='ALTA' >ALTA DE ESPECIALIDAD</option>");
                        //print("<option value='N' >NO ATENDIDO</option>");
                        print("<!----<option value='X' >BLOQUEADO</option>--->");
                        print("<option value='T'>SUSPENDIDO</option>");

                    }



			/*
                    print("
                    <select onChange='calcular_totales($i);' style='width:100px;' id='nomd_diag_cod_".$lista[$i]['nomd_id']."' name='nomd_diag_cod_".$lista[$i]['nomd_id']."' $disabled_input>
                    ");
                    if($estado_cupo)
                    {
                        print("
                        <option value='' ".($lista[$i]['nomd_diag_cod']==''?'SELECTED':'').">AGENDADO</option>
                        <option value='OK' ".($lista[$i]['nomd_diag_cod']=='OK'?'SELECTED':'').">ATENDIDO</option>
                        <option value='ALTA' ".($lista[$i]['nomd_diag_cod']=='ALTA'?'SELECTED':'').">ALTA DE ESPECIALIDAD</option>
                        <option value='N' ".($lista[$i]['nomd_diag_cod']=='N'?'SELECTED':'').">NO ATENDIDO</option>
                        <!----<option value='X' ".($lista[$i]['nomd_diag_cod']=='X'?'SELECTED':'').">BLOQUEADO</option>--->
                        <option value='T' ".($lista[$i]['nomd_diag_cod']=='T'?'SELECTED':'').">SUSPENDIDO</option>
                        ");
                    }
                    else
                    {

                        print("
                        <option value=''>AGENDADO</option>
                        <option value='OK' SELECTED>ATENDIDO</option>
                        <option value='ALTA' >ALTA DE ESPECIALIDAD</option>
                        <option value='N' >NO ATENDIDO</option>
                        <!----<option value='X' >BLOQUEADO</option>--->
                        <option value='T'>SUSPENDIDO</option>
                        ");

                    }
		      */
                    print("
                    </select>
                    <input type='button' $motivo_enabled id='motivo_".$lista[$i]['nomd_id']."' name='motivo_".$lista[$i]['nomd_id']."' value='[S]' onClick='sel_motivo(".$lista[$i]['nomd_id'].",$i);' style='border:0px;margin:0px;'  />
                </center>
            </td>
            <td style='white-space:nowrap'>
                <center>");
		      $palabra = 'Nueva';
                    $encontrada = strrpos($lista[$i]['nom_motivo'], $palabra);
                    if($encontrada)
                    {
                    	  if($lista[$i]['nomd_motivo']=="")
                       { 
                          print("
                            <select onChange='calcular_totales($i);' id='nomd_motivo_".$lista[$i]['nomd_id']."' name='nomd_motivo_".$lista[$i]['nomd_id']."' $disabled_input>
                              <option value='S'>S</option>
                              <option value='N' SELECTED>N</option>
                            </select>");
                        
                            print("<select onChange='calcular_totales($i);' id='nomd_motivo2_".$lista[$i]['nomd_id']."' name='nomd_motivo2_".$lista[$i]['nomd_id']."' $disabled_input>
                              <option value='S'>S</option>
                              <option value='N' SELECTED>N</option>
                            </select>");
                        
                        }
                       else
                       {
                            print("
                             <select onChange='calcular_totales($i);' id='nomd_motivo_".$lista[$i]['nomd_id']."' name='nomd_motivo_".$lista[$i]['nomd_id']."' $disabled_input>
                               <option value='S' ".($lista[$i]['nomd_motivo'][0]=='S'?'SELECTED':'').">S</option>
                               <option value='N' ".($lista[$i]['nomd_motivo'][0]=='N'?'SELECTED':'').">N</option>
                             </select>");
                    
                            print("<select onChange='calcular_totales($i);' id='nomd_motivo2_".$lista[$i]['nomd_id']."' name='nomd_motivo2_".$lista[$i]['nomd_id']."' $disabled_input>
                               <option value='S' ".($lista[$i]['nomd_motivo'][1]=='S'?'SELECTED':'').">S</option>
                               <option value='N' ".($lista[$i]['nomd_motivo'][1]=='N'?'SELECTED':'').">N</option>
                            </select>");
                       }
                    }
			else
			{
				print("&nbsp;");
			}             
                    print("
                </center>
            </td>
            <td style='white-space:nowrap;'>
                <center>
                    <select style='width:100px;' onChange='calcular_totales($i);' id='nomd_origen_".$lista[$i]['nomd_id']."' name='nomd_origen_".$lista[$i]['nomd_id']."' $disabled_input>
                        <option value='' ".($lista[$i]['nomd_origen']==''?'SELECTED':'').">(Sin Info)</option>
                        <option value='A' ".($lista[$i]['nomd_origen']=='A'?'SELECTED':'').">APS</option>
                        <option value='U' ".($lista[$i]['nomd_origen']=='U'?'SELECTED':'').">URGENCIA</option>
                        <option value='C' ".($lista[$i]['nomd_origen']=='C'?'SELECTED':'').">CAE</option>
                    </select>
                    <input type='button' $origen_enabled id='origen_".$lista[$i]['nomd_id']."' name='origen_".$lista[$i]['nomd_id']."' value='[S]' onClick='sel_origen(".$lista[$i]['nomd_id'].",$i);' style='border:0px;margin:0px;'  />
                </center>
            </td>
            <td>
                <center>
                    <select onChange='calcular_totales($i);' id='nomd_auge_".$lista[$i]['nomd_id']."' name='nomd_auge_".$lista[$i]['nomd_id']."' $disabled_input>
                        <option value='S' ".($lista[$i]['nomd_auge']=='S'?'SELECTED':'').">S</option>
                        <option value='N' ".($lista[$i]['nomd_auge']!='S'?'SELECTED':'').">N</option>
                    </select>
                </center>
            </td>");
            if($ficha_clinica)
            {
                print("<td>");
                    print("<center>");
                        if(_cax(313))
                        {
                            $esp_permiso=_cav(313);
                            $esp_permiso=explode(",",$esp_permiso);
                            $encontrado=false;
                            for($x=0;$x<count($esp_permiso);$x++)
                            {
                                if($esp_permiso[$x]==$esp_id)
                                {
                                    $encontrado=true;
                                    break;
                                }
                            }
                            if($encontrado)
                            {
                                print("<img src='iconos/table_edit.png'  style='cursor:pointer;' onClick='registrar(".($lista[$i]['nomd_id']).");' />");
                            }
                            else
                            {
                                print("&nbsp;");
                            }
                        }
                        else
                        {
                            print("&nbsp;");
                        }
                    print("</center>");
                print("</td>");
            }
            if($informe)
            {
                print("
                <td>
                    <center>
                        <img src='iconos/script_edit.png'  style='cursor:pointer;' onClick='informe(".($lista[$i]['nomd_id']).");' />
                    </center>
                </td>
                ");
            }
            if($lista[$i]['nomd_via_ingreso']!='A')
		$icono='user';
            else
		$icono='calendar';
            
            print("<td style='white-space:nowrap;'>
            <center>");
            if($disabled_input=="")
            {
                print("<img src='iconos/phone.png'  style='cursor:pointer;' alt='Gestiones Citaci&oacute;n' title='Gestiones Citaci&oacute;n' onClick='gestiones_citacion(".$lista[$i]['nomd_id'].");' />");
            }
            else
            {
                print("<img src='iconos/phone.png'  style='cursor:pointer;' alt='Gestiones Citaci&oacute;n' title='Gestiones Citaci&oacute;n' onClick='' />");
            }
            print("
                <img src='iconos/printer.png'  style='cursor:pointer;' alt='Imprimir Citaci&oacute;n' title='Imprimir Citaci&oacute;n' onClick='imprimir_citacion(".$lista[$i]['nomd_id'].");' />
                <img src='iconos/layout.png'  style='cursor:pointer;' alt='Imprimir Hoja AT.' title='Imprimir Hoja AT.' onClick='imprimir_citacion2(".$lista[$i]['nomd_id'].");' />
		<!--- <img src='iconos/$icono.png'  />	---->
            ");
            /*
            if($lista[$i]['id_sidra']=='')
                print("<img src='iconos/delete.png'  style='cursor:pointer;' onClick='eliminar(".($lista[$i]['nomd_id']).")' />");
            else
		print("<img src='iconos/stop.png'  style='cursor:pointer;' alt='SIDRA' title='SIDRA' />");
            */
            print("</center></td></tr>");
        }
    }
        
        $horas_html.="<option value='00:00'>EXTRA</option></select>";
        $horas_html.="</select>";
	$horas_extra_html="<select id='nomd_hora_extra' name='nomd_hora_extra'>";
        $consulta="SELECT DISTINCT nomd_hora FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora IS NOT NULL AND NOT nomd_hora='00:00:00' ORDER BY nomd_hora";
        //$consulta="SELECT DISTINCT nomd_hora,nomd_id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora IS NOT NULL AND nomd_extra='S' ORDER BY nomd_hora";
        $hs=cargar_registros_obj($consulta);
        if($hs)
            for($k=0;$k<sizeof($hs);$k++)
            {
                //if($lote)
                //{
                //    $horas_extra_html.="<option value='".substr($hs[$k]['nomd_hora'],0,5)."_".$hs[$k]['nomd_id']."'>".substr($hs[$k]['nomd_hora'],0,5)."</option>";
                //}
                //else
                //{
                    $horas_extra_html.="<option value='".substr($hs[$k]['nomd_hora'],0,5)."'>".substr($hs[$k]['nomd_hora'],0,5)."</option>";
                //}
            }
	$horas_extra_html.='</select>';
?>
</table>
<script>
    verificar_sobrecupos=function()
    {
        if($('nomd_hora').value=="00:00")
        {
            if(($('total_sobrecupos').value*1)==0)
            {
                alert("La nomina de atenci&oacute;n Actual no permite ingresar SOBRECUPOS".unescapeHTML());
                $('btn_agregar').disabled=true;
                return;
            }
            if(($('total_sobrecupos').value*1)!=($('nomina_sobrecupos').value*1))
            {
                $('btn_agregar').disabled=false;
                $("td_horas_extra").show();
            }
            else
            {
                alert("Se han completados la cantidad de SOBRECUPOS PARA ESTA NOMINA DE ATENCI&Oacute;N".unescapeHTML());
                $('btn_agregar').disabled=true;
                return;
            }
        }
        else
        {
            $('btn_agregar').disabled=false;
            $("td_horas_extra").hide();
        }
        
        
    }
    //$horas_html="<select id='nomd_hora' name='nomd_hora' onChange='if(this.value==\\\"00:00\\\"){ $(\\\"td_horas_extra\\\").show();} else{ $(\\\"td_horas_extra\\\").hide();}'>";
    //$horas_html="<select id='nomd_hora' name='nomd_hora' onChange='verificar_sobrecupos();'>";
    $('total_sobrecupos').value="<?php echo $cant_sobrecupos; ?>";
    $('nomina_sobrecupos').value="<?php echo $sobrecupos_nomina; ?>";
    $('td_horas').innerHTML="<?php echo $horas_html; ?>";
    $('td_horas_extra').innerHTML="<?php echo $horas_extra_html; ?>";
    <?php if($completo){?>
        $('btn_agregar').disabled=true;
    <?php }else { ?>
        $('btn_agregar').disabled=false;
    <?php } ?>
    if($('nomd_hora').value=='00:00')
    {
        $('td_horas_extra').show();
    }
    else
    {
        $('td_horas_extra').hide();
    }
    <?php
    if($nom_recurso)
    {
    ?>
        $('td_duracion').show();
        $('duracion').value='1';
        $('duracion').disabled=false;
    <?php
    }
    else
    {
    ?>
        $('td_duracion').hide();
        $('duracion').disabled=true;
    <?php
    }
    ?>
    try {
    <?php   
        if(!(isset($_POST['folios_nominas'])))
        {
    ?>
            $('folio_nomina').value='<?php echo $n['nom_folio']; ?>';
            $('nro_nomina').innerHTML='<?php echo $n['nom_folio']; ?>';
            $('fecha_nomina').innerHTML='<?php echo $n['nom_fecha']; ?>';
            $('medico_nomina').innerHTML='<?php echo htmlentities($n['doc_nombres'].' '.$n['doc_paterno'].' '.$n['doc_materno']); ?>';
            $('esp_nomina').innerHTML='<?php echo htmlentities($n['esp_desc']) .' - ('.htmlentities($n['nom_motivo']).')' ?>';
            $('estado_nomina').value='<?php echo $n['nom_estado_digitacion']*1; ?>';
            
            
            $('extras_disponibles').innerHTML=($('total_sobrecupos').value*1)-($('nomina_sobrecupos').value*1);
            //$('select_nominas').innerHTML=<?php //echo json_encode($htmlnom); ?>;
            //$('select_nominas').style.display='';
            lnomina=<?php echo json_encode($lnom); ?>;	
    <?php  
        }
    ?>	
    dnomina=<?php echo json_encode($lista); ?>;
    if($('folio_nomina').value.substr(0,3)=='SN-')
        $('eliminar_nominas').style.display='';
    else
        $('eliminar_nominas').style.display='none';
	//calcular_totales();
    }
    catch(err)
    {
        alert(err);
    }
</script>
