<?php
    require_once('../../conectar_db.php');
?>
<script>
    buscar=function()
    {
        if($('tipo').value==0)
        {
            $('busca').value=$('busca').value.toUpperCase();
            if(!comprobar_rut($('busca').value))
            {
                $('busca').style.background='red';
		alert('R.U.T. incorrecto.');
		$('busca').select(); $('busca').focus();
		return;				
            } 
	}
		
	$('busca').style.background='';
        var params='';
        <?php
            if(isset($_GET['pac_ficha']))
            {
        ?>
                var url='listar_prestaciones.php'
                params='&ficha=1';
        <?php
            }
            else
            {
        ?>
                var url='prestaciones/consultar_paciente/listar_prestaciones.php'
        <?php
            }
        ?>
	var myAjax=new Ajax.Updater('lista_presta',url,
        {
            method:'post',
            evalScripts:true,
            parameters:$('tipo').serialize()+'&'+$('busca').serialize()+params
	});
    }

    imprimir_citacion=function(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = window.open('prestaciones/ingreso_nominas/citaciones.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    imprimir_citacion2=function(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = window.open('prestaciones/ingreso_nominas/citaciones2.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
    
    abrir_ficha = function(id)
    {
        inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
	'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
	inter_ficha.focus();
    }
    
    print_inter_ficha=function(id)
    {
        inter_ficha_pdf = window.open('interconsultas/inter_ficha_pdf.php?tipo=inter_ficha&inter_id='+id,
        'inter_ficha_pdf', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
	inter_ficha_pdf.focus();
    }
</script>
<?php 
    if(isset($_GET['pac_ficha']))
        cabecera_popup('../..'); 
?>
<center>
    <div class='sub-content' style='width:1100px; height: 600px;'>
        <div class='sub-content'>
            <?php
            if(isset($_GET['pac_ficha']))
            {
            ?>
            <input type='hidden' id='ficha' name='ficha' size=40 value="<?php echo $_GET['pac_ficha'];?>"/>
                <img src='../../iconos/user.png' />
            <?php
            }
            else
            {
            ?>
                <img src='iconos/user.png' />
                <input type='hidden' id='ficha' name='ficha' size=40 value=""/>
            <?php
            }
            ?>
            <b>Consultas por Paciente</b>
        </div>
        <div class='sub-content'>
            <table style='width:100%;'>
                <tr>
                    <td style='text-align:right;'>Buscar:</td>
                    <td>
                        <center>
                            <select id='tipo' name='tipo'>
                                <option value='1' SELECTED>Nro. de Ficha</option>
                                <option value='0'>RUT</option>
                                <option value='2'>Nro. de N&oacute;mina</option>
                                <option value='3'>Pasaporte</option>
                                <option value='4'>Codigo Interno</option>
                            </select>
                        </center>
                    </td>
                    <td>
                        <input type='text' id='busca' name='busca' size=40 />
                    </td>
                    <td style='width:40%;'>
                        <input type='button' value='Realizar B&uacute;squeda...' onClick='buscar();' />
                    </td>
                </tr>
            </table>
        </div>
        <div class='sub-content2' id='lista_presta' style='height:500px;overflow:auto;'>
            
        </div>
            
    </div>
</center>
<script>
    <?php
    if(isset($_GET['pac_ficha']))
    {
    ?>
        $('busca').value=$('ficha').value;
        buscar();
    <?php
    }
    ?>
</script>