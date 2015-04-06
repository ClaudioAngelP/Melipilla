<?php
    require_once('../../conectar_db.php');
?> 
<script>
      
     
</script>
<br>
    
    <div class='sub-content2'>
                <table width=100%>
                    <tr class='tabla_header' style='font-weight: bold;'>
                        <td>Medicamento</td>
                        <td>D&oacute;sis</td>
                        <td>Total</td>
                    </tr>
                    <?php
                    $meds=json_decode($_POST['meds']);
                    for($i=0;$i<sizeof($meds);$i++)
                    {
                        $art_id=pg_escape_string($meds[$i][0]*1);
			$cod_presta=pg_escape_string($meds[$i][1]);
			$glosa=pg_escape_string($meds[$i][2]);
			$total=round(1*(($meds[$i][5]*24))/($meds[$i][4])*($meds[$i][3]));
			$cant=$meds[$i][3];
			$horas=$meds[$i][4];
			$dias=$meds[$i][5];
			$indica=pg_escape_string($meds[$i][6]);
                        if($horas*1<24)
                        {
                            $div_h=1;
                            $txt_horas='horas';
			}
                        else
                        {
                            if(($horas%24)==0)
                            {
                                $div_h=24;
                                $txt_horas='d&iacute;a(s)';
                            }
                            else
                            {
                                $div_h=1;
                                $txt_horas='horas';
                            }
			}
                        if($dias*1<=30)
                        {
                            $div_d=1;
                            $txt_dias='d&iacute;a(s)';
			}
                        else
                        {
                            if($dias%30==0)
                            {
                                $div_d=30;
                                $txt_dias='mes(es)';
                            }
                            else
                            {
                                $div_d=1;
                                $txt_dias='d&iacute;a(s)';
                            }
			}
                        $txt_dosis="<b>".$cant." ".$meds[$i][7]."</b> cada ".($horas/$div_h)." ".$txt_horas." por ".($dias/$div_d)." ".$txt_dias.".";
                        ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
			print("
                            <tr class='$clase'>
                                <td style='color: red;'>$glosa</td><td style='color: red;'>$txt_dosis</td><td rowspan=2 style='text-align: center; color: red;'>$total</td>
                            </tr>
                            <tr class='$clase'><td colspan=2>Indicaciones: $indica</td></tr>");
                    }
                    ?>
                </table>
            </div>
            <br><br>
            <center>
                <table>
                    <tr>
                        <td colspan=3>
                            <center>
                                <input type='submit' value='[ CERTIFICAR ]' onClick='guardar_prestacion();'>
                            </center>
                        </td>
                    </tr>
		</table>
            </center>
    
    <script>
        //$("___pass").focus();
    </script>
