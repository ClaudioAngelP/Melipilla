<?php
    require_once('../../conectar_db.php');
?>
<script>
    tr_traslado = function() {
        var val=$('motivo').value*1;
	if(val==0) {
            $('tr1').show();
            $('tr2').show();
            $('tr3').hide();
            $('tr4').hide();
            $('tr5').hide();
            $('tr6').hide();
	} else if(val==1) {
            $('tr1').hide();
            $('tr2').hide();
            $('tr3').show();
            $('tr4').hide();
            $('tr5').hide();
            $('tr6').hide();			
        } else if(val==2) {
            $('tr1').hide();
            $('tr2').hide();
            $('tr3').show();
            $('tr4').hide();
            $('tr5').hide();
            $('tr6').hide();						
        } else if(val==3) {
            $('tr1').hide();
            $('tr2').hide();
            $('tr3').hide();
            $('tr4').show();
            $('tr5').show();
            $('tr6').hide();			
        } else if(val==4) {
            $('tr1').hide();
            $('tr2').hide();
            $('tr3').hide();
            $('tr4').hide();
            $('tr5').hide();
            $('tr6').show();			
        }
    }
</script>
<form id='solicitud' name='solicitud' onSubmit='return false;'>
    <input type='hidden' id='pac_id' name='pac_id' value='' />
    <center>
        <div class='sub-content' style='width:850px;'>
            <div class='sub-content'>
                <img src='iconos/building_go.png' /> <b>Solicitud de Traslado en Ambulancia</b> 
            </div>
            <table style='width:100%;'>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>Paciente:</td>
                    <td colspan=3>
			<input type='text' size=45 id='pac_rut' name='pac_rut' value='' />
                    </td>
                </tr>
                <tr>
                    <td class='tabla_fila2'  style='text-align:right;'>Ficha Cl&iacute;nica:</td>
                    <td class='tabla_fila' style='text-align:left;font-weight:bold;' id='pac_ficha' colspan=3></td>
                </tr>
                <tr>
                    <td class='tabla_fila2'  style='text-align:right;'>Nombre:</td>
                    <td class='tabla_fila' colspan=3 style='font-weight:bold;' id='pac_nombre'></td>
                </tr>
                <tr>
                    <td class='tabla_fila2'  style='text-align:right;'>Fecha de Nac.:</td>
                    <td class='tabla_fila' id='pac_fc_nac'></td>
                    <td class='tabla_fila2' colspan=2 style='text-align:center;width:40%;' id='pac_edad'>
                        Edad:<b>?</b>
                    </td>
                </tr>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>Motivo del Traslado:</td>
                    <td class='tabla_fila' colspan=3>
                        <select id='motivo' name='motivo' onChange='tr_traslado();'>
                            <option value='0'>Alta a Domicilio</option>
                            <option value='1'>Rescate a Domicilio</option>
                            <option value='2'>Rescate a Cl&iacute;nica/Hospital</option>
                            <option value='3'>Procedimiento</option>
                            <option value='4'>Traslado a la Red</option>
                        </select>
                    </td>
                </tr>
                <tr id='tr1'>
                    <td  class='tabla_fila2' style='text-align:right;'>Direcci&oacute;n:</td>
                    <td class='tabla_fila'  colspan=3>
                        <input type='text' id='direccion' name='direccion' size=40 />
                    </td>
                </tr>
                <tr id='tr2'>
                    <td  class='tabla_fila2' style='text-align:right;'>Tel&eacute;fono:</td>
                    <td class='tabla_fila'  colspan=3>
			<input type='text' id='fono' name='fono' />
                    </td>
                </tr>
                <tr id='tr3' style='display:none;'>
                    <td  class='tabla_fila2' style='text-align:right;'>Desde/Hacia:</td>
                    <td class='tabla_fila'  colspan=3>
			<input type='text' id='desde' name='desde' />
			<input type='text' id='hacia' name='hacia' />
                    </td>
                </tr>
                <tr id='tr4' style='display:none;'>
                    <td  class='tabla_fila2' style='text-align:right;'>Procedimiento:</td>
                    <td class='tabla_fila'  colspan=3>
			<input type='text' id='proc' name='proc' size=40 />
                    </td>
                </tr>
                <tr id='tr5' style='display:none;'>
                    <td  class='tabla_fila2' style='text-align:right;'>Lugar/Hora Citaci&oacute;n:</td>
                    <td class='tabla_fila'  colspan=3>
			<input type='text' id='lugar' name='lugar' size=30 />
			<input type='text' id='hora' name='hora' size=10 />
                    </td>
                </tr>
                <tr id='tr6' style='display:none;'>
                    <td  class='tabla_fila2' style='text-align:right;'>Desde/Hacia:</td>
                    <td class='tabla_fila' colspan=3 >
			<input type='text' id='desde2' name='desde2' />
			<input type='text' id='hacia2' name='hacia2' />
                    </td>
                </tr>
                <tr>
                    <td  class='tabla_fila2' style='text-align:right;'>Nombre de Contacto:</td>
                    <td class='tabla_fila' colspan=3>
			<input type='text' id='contacto' name='contacto' size=40 />
                    </td>
                </tr>
            </table>
            <table style='width:100%;'>
                <tr>
                    <td colspan=8 class='tabla_fila2'><b>Condiciones del Paciente</b></td>
                </tr>
                <tr>
                    <td><input type='checkbox' id='cond1' name='cond1' value='Grave' /></td>
                    <td style='width:15%;'>Grave</td>
                    <td><input type='checkbox' id='cond2' name='cond2' value='Postrado' /></td>
                    <td style='width:15%;'>Postrado</td>
                    <td><input type='checkbox' id='cond3' name='cond3' value='Desmovilizado' /></td>
                    <td style='width:15%;'>Desmovilizado</td>
                    <td><input type='checkbox' id='cond4' name='cond4' value='Autovalente' /></td>
                    <td style='width:15%;'>Autovalente</td>
                </tr>
                <tr>
                    <td colspan=8 class='tabla_fila2'><b>Requisitos del Traslado</b></td>
                </tr>
                <tr>
                    <td style='width:5%;'><input type='checkbox' id='req1' name='req1' value='Sentado' /></td>
                    <td>Sentado</td>
                    <td style='width:5%;'><input type='checkbox' id='req2' name='req2' value='Bomba de Infusi&oacute;n' /></td>
                    <td>Bomba de Infusi&oacute;n</td>
                    <td style='width:5%;'><input type='checkbox' id='req3' name='req3' value='Sujeciones' /></td>
                    <td>Sujeciones</td>
                    <td style='width:5%;'><input type='checkbox' id='req4' name='req4' value='Monitores' /></td>
                    <td>Monitores</td>
                </tr>
                <tr>
                    <td style='width:5%;'><input type='checkbox' id='req5' name='req5' value='Sentado' /></td>
                    <td>V. Mec&aacute;nica</td>
                    <td style='width:5%;'><input type='checkbox' id='req6' name='req6' value='Bomba de Infusi&oacute;n' /></td>
                    <td>Incubadora</td>
                    <td style='width:5%;'><input type='checkbox' id='req7' name='req7' value='Sujeciones' /></td>
                    <td>Camilla</td>
                    <td style='width:5%;'><input type='checkbox' id='req8' name='req8' value='Monitores' /></td>
                    <td>V. No Invasiva</td>
                </tr>
                <tr>
                    <td style='width:5%;'><input type='checkbox' id='req9' name='req9' value='Ox&iacute;geno' /></td>
                    <td>Ox&iacute;geno</td>
                    <!--
                    <td colspan=2>
                        <input type='text' id='req9_txt' name='req9_txt' value='' />
                    </td>
                    -->
                    <td style='width:5%;'>
                        <input type='checkbox' id='req10' name='req10' value='Otros' />
                    </td>
                    <td>Otros</td>
                    <!--
                    <td colspan=2>
                        <input type='text' id='req10_txt' name='req10_txt' value='' />
                    </td>
                    -->
                </tr>
            </table>
            <br /><br />
        <center>
            <input type='button' id='' name='' value='Guardar Traslado...' onClick='guardar_solicitud();' />
        </center>
    </div>
</center>
</form>
<script>

	guardar_solicitud=function() {
		
		var myAjax=new Ajax.Request(
			'prestaciones/solicitud_ambulancia/sql.php',
			{
				method:'post',
				parameters:$('solicitud').serialize(),
				onComplete:function(r) {
					alert('Solicitud ingresada exitosamente.');
				}
				
			}
		);
		
	}

    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('pac_nombre').innerHTML=d[2];
		$('pac_id').value=d[4];
		$('pac_ficha').innerHTML=d[3];
		$('pac_fc_nac').innerHTML=d[7];
		$('pac_edad').innerHTML='Edad: <b>'+d[11]+'</b>';    
    	
    }

    autocompletar_pacientes = new AutoComplete(
      'pac_rut', 
      'autocompletar_sql.php',
      function() {
        if($('pac_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);



</script>
