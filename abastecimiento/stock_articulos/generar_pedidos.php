<?php
require_once('../../conectar_db.php');
?>

<html><title>Generaci&oacute;n de Pedidos</title>

<?php cabecera_popup('../..'); ?>

<script>

generar_pedidos = function() {

  datos_pedidos=$('pedidos').serialize();

  for(i=0;i<$('pedido_cant').value;i++) {
    $('nro_pedido_'+i).innerHTML=
    '<img src="../../imagenes/ajax-loader1.gif"> Generando...';
  }

  var myAjax = new Ajax.Request(
  'guardar_pedidos.php',
  {
    method:'post',
    parameters: datos_pedidos,
    onComplete: function(respuesta) {
      try {
        datos = respuesta.responseText.evalJSON(true);
      } catch(err) {
      
        for(i=0;i<=$('pedido_cant').value;i++) {
          $('nro_pedido_'+i).innerHTML='&nbsp;&nbsp;?????';
        }

        alert('ERROR:\n\n'+respuesta.responseText);

      }

      try {

      for(i=0;i<datos.length;i++) {
        $('nro_pedido_'+i).innerHTML='&nbsp;&nbsp;'+datos[i].pedido_nro;
        $('fecha_pedido_'+i).innerHTML='&nbsp;&nbsp;'+datos[i].pedido_fecha;
      }

      $('aceptar_pedidos_div').style.display='none';
      $('imprimir_pedidos_div').style.display='';

      cargar_listado();

      } catch(err) {
        alert(err);
      }

    }
  });

  }

  wo=window.opener;
  imprimefunc=wo.imprimirHTML.bind(wo);

  cargar_listado = function() {

    if((wo.$('buscar').value.length>0 || wo.$('item').value!=-1)
        && wo.seltab==0) {

		wo.$('imagen_carga').style.display='';

		var myAjax = new Ajax.Updater(
			wo.$('resultado'),
			'abastecimiento/stock_articulos/listado_criticos.php',
			{
				method: 'get',
				parameters: wo.$('bodega').serialize()+'&'+wo.$('buscar').serialize()+
      '&'+wo.$('item').serialize(),
				evalScripts: true,
		    onComplete: function() {
          wo.$('imagen_carga').style.display='none';
	       }
			}

			);

			}

			if(wo.seltab==1) cargar_punto_pedido();
			if(wo.seltab==2) cargar_punto_critico();

	}

		cargar_punto_pedido = function() {

    id_bodega = wo.$('bodega').value;
    wo.$('imagen_carga').style.display='';

    var myAjax = new Ajax.Updater(
    wo.$('tab_pedido_listado'),
    'punto_pedido.php',
    {
      method: 'get',
      parameters: wo.$('bodega').serialize()+'&'+wo.$('buscar').serialize()+
      '&'+wo.$('item').serialize()+'&'+wo.$('convenio').serialize(),
      onComplete: function () {
        wo.$('imagen_carga').style.display='none';
      }
    });
  }

  cargar_punto_critico = function() {

    id_bodega = wo.$('bodega').value;
    wo.$('imagen_carga').style.display='';

    var myAjax = new Ajax.Updater(
    wo.$('tab_critico_listado'),
    'punto_critico.php',
    {
      method: 'get',
      parameters: wo.$('bodega').serialize()+'&'+wo.$('buscar').serialize()+
      '&'+wo.$('item').serialize()+'&'+wo.$('convenio').serialize(),
      onComplete: function () {
        wo.$('imagen_carga').style.display='none';
      }
    });
  }

  imprimir_pedidos = function() {

    imprimefunc($('pedidos').innerHTML);

  }



</script>

<body class='fuente_por_defecto popup_background'>

<?php

if(isset($_POST['pedido_bodega']))
  $bodega = $_POST['pedido_bodega']*1;
else
  $bodega = $_POST['manual_bodega']*1;

if(isset($_POST['pedido_nro_arts']))
  $num_arts = $_POST['pedido_nro_arts']*1;
else
  $num_arts = $_POST['manual_nro_arts']*1;


$limite=2800000;

$funcionario = cargar_registro("SELECT func_nombre FROM funcionario
        WHERE func_id=".($_SESSION['sgh_usuario_id']*1));

//************************************************************
   $bodega_reg = pg_query($conn, "
      SELECT bod_glosa FROM bodega WHERE bod_id=".$bodega."
      ");

      $bodega_row = pg_fetch_row($bodega_reg);

      $bodega_nombre = $bodega_row[0];

//************************************************************


$c=0;
$arts='';

for($i=0;$i<$num_arts;$i++) {

  if(($_POST['sug_'.$i]*1)>0) {
    $artx[$c]=$_POST['art_'.$i];
    $codx[$c]=$_POST['cod_'.$i];
    $nomx[$c]=$_POST['nom_'.$i];
    $forx[$c]=$_POST['for_'.$i];
    $sugx[$c]=$_POST['sug_'.$i];
    $valx[$c]=$_POST['ult_'.$i];
    $usax[$c]=0;
    $arts.=$_POST['art_'.$i].',';
    $c++;
  }

}

if($c==0) {

?>

<br><br><br><br>
<center><b>ERROR:</b>
<br><br>No hay art&iacute;culos seleccionados.
</center>

</body></html>

<?php

exit();

}

$num_arts=count($artx);

$arts=substr($arts, 0, strlen($arts)-1);

$convenios=cargar_registros_obj("
  SELECT * FROM convenio_detalle WHERE art_id IN ($arts)
");


for($i=0;$i<$num_arts;$i++) {
  $fnd=false;
  for($j=0;$j<count($convenios);$j++) {
    if($artx[$i]==$convenios[$j]['art_id']) {
      $conx[$i]=$convenios[$j]['convenio_id'];
      $fnd=true;
      break;
    }
  }
  if(!$fnd) $conx[$i]=-1;
}

$convs=array_unique($conx);

$num_total_arts=$num_arts;
$pedido_nro=0;



?>


<div id='todos_pedidos' name='todos_pedidos'>
    <form id='pedidos' name='pedidos' method='post' action='guardar_pedidos.php'>
    <input type='hidden' id='pedido_bodega' name='pedido_bodega' value='<?php echo htmlentities($bodega); ?>'>
    <table style='width:100%;font-size:11px;'>
        <?php
        foreach($convs AS $conv_id)
        {
            if($conv_id!=-1)
            {
                $conv_det = cargar_registro("SELECT * FROM convenio WHERE convenio_id=".($conv_id*1)."");
                $convenio_nombre=$conv_det['convenio_nombre'];
            }
            else
            {
                $convenio_nombre='<i>Art&iacute;culos sin Convenio Asociado</i>';
            }
            $c=0;
            for($i=0;$i<$num_total_arts;$i++)
            {
                if($conx[$i]==$conv_id)
                {
                    $art[$c]=$artx[$i];
                    $cod[$c]=$codx[$i];
                    $nom[$c]=$nomx[$i];
                    $for[$c]=$forx[$i];
                    $sug[$c]=$sugx[$i];
                    $val[$c]=$valx[$i];
                    $usa[$c]=0;
                    $c++;
                }
            }
            $num_arts=$c;
            $lim=$limite;
            $terminado=false;
            $pedido=true;
            $sumatoria=0;
            $i=0;
            $f=0;
            $pedido_detalle='';
            $arts_terminados=0;
            $imprime_boton=false;
            while (!$terminado)
            {
                if($pedido)
                {
                    print('
                        <table border=2 style="width:100%;font-size:11px;">
                        <tr  class="tabla_header">
                        <td style="text-align:right;">Bodega:</td>
                        <td colspan=5 style="font-weight:bold;text-align:left;">&nbsp;'.$bodega_nombre.'</td>
                    </tr>
                    <tr class="tabla_header">
                        <td style="text-align:right;">Pedido Nro:</td>
                        <td colspan=5 style="font-weight:bold;text-align:left;"id="nro_pedido_'.$pedido_nro.'">&nbsp;&nbsp;?????</td>
                    </tr>
                    <tr class="tabla_header">
                        <td style="text-align:right;">Fecha/Hora:</td>
                        <td colspan=5 style="font-weight:bold;text-align:left;"id="fecha_pedido_'.$pedido_nro.'">&nbsp;&nbsp;?????</td>
                    </tr>
                    <tr class="tabla_header">
                        <td style="text-align:right;">Convenio:</td>
                        <td colspan=5 style="font-weight:bold;text-align:left;">&nbsp;'.$convenio_nombre.'</td>
                    </tr>
                    <tr class="tabla_header">
                        <td style="text-align:right;">Funcionario:</td>
                        <td colspan=5 style="font-weight:bold;text-align:left;">&nbsp;&nbsp;'.$funcionario['func_nombre'].'</td>
                    </tr>
                    <tr class="tabla_header">
                        <td style="text-align:center;">C&oacute;digo Int.</td>
                        <td style="text-align:center;">Glosa</td>
                        <td style="text-align:center;">Cant.</td>
                        <td style="text-align:center;">Unidad</td>
                        <td style="text-align:center;">P. Unit.</td>
                        <td style="text-align:center;">Subtotal</td>
                    </tr>
                    ');
                    $lim=$limite;
                    $pedido=false;
                    $lim_arts=0;
                }
                if(($sug[$i]-$usa[$i])>0)
                {
                    if($lim<($sug[$i]-$usa[$i])*$val[$i])
                    {
                        if($val[$i]<=$lim AND ($val[$i]*($sug[$i]-$usa[$i]))>=$limite)
                        {
                            $cantidad=floor($lim/$val[$i]);
                        }
                        else
                        {
                            $testear=false;
                            for($a=0;$a<$num_arts;$a++)
                            {
                                if($lim>=($val[$a]*($sug[$a]-$usa[$a])) AND ($sug[$a]-$usa[$a])>0 AND $val[$a]>0)
                                { $i=$a; $testear=true; break; }
                            }
                            if($testear) { continue; }
                            $i=0;
                            $cantidad=0;
                            $pedido=true;
                        }

                    }
                    else
                    {
                        $cantidad=($sug[$i]-$usa[$i]);
                    }
                    // Marca usados del artículo igual a la cantidad sugerida
                    // para no volver a ser usado...
                    if($cantidad!=0)
                    {
                        $usa[$i]+=$cantidad;
                        $f++;
                        ($f%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
                        print('
                        <tr class="'.$clase.'">
                            <td style="text-align:right;">'.htmlentities($cod[$i]).'</td>
                            <td>'.htmlentities($nom[$i]).'</td>
                            <td style="text-align:right;">'.number_formats($cantidad).'</td>
                            <td>'.htmlentities($for[$i]).'</td>
                            <td style="text-align:right;">$'.number_formats($val[$i]).'.-</td>
                            <td style="text-align:right;">$'.number_formats($cantidad*$val[$i]).'.-</td>
                        </tr>');
                        $pedido_detalle.=$art[$i].'=='.$cantidad.'!';
  
                        $sumatoria+=$cantidad*$val[$i];
                        $lim-=$cantidad*$val[$i];
                        $arts_terminados=0;
                        $imprime_boton=true;
                        $lim_arts++;
  
                        if($lim_arts>20) { $pedido=true; $pedidopor='este es por limite de 20'; }
  
                    }
  
                }
  
                $i++;
  
                if($i>=$num_arts) $i=0;
    
                $prosigue=false;
                for($j=0;$j<$num_arts;$j++)
                {
                    if($sug[$j]-$usa[$j]) { $prosigue=true; break;  }
                }
      
                if($pedido OR !$prosigue)
                {
                    print('
                        <tr class="tabla_header">
                            <td style="text-align:right;" colspan=5>Total:</td>
                            <td style="text-align:right;">$'.number_formats($sumatoria).'.-</td>
                        </tr>
                    </table>
                    <input type="hidden" type="pedido_'.$pedido_nro.'" name="pedido_'.$pedido_nro.'" value="'.$pedido_detalle.'">
                    <hr style="page-break-after: always;">
                    ');
                    $sumatoria=0;
                    // Inicia variables para nuevo pedido...
                    $pedido_nro++;
                    $pedido_detalle='';

                }
      
                if(!$prosigue)
                {
                    break;
                }
            }
        }
        ?>
    </table>
    <input type='hidden' id='pedido_cant' name='pedido_cant' value='<?php echo $pedido_nro; ?>'>
    </form>
</div>


<?php if($imprime_boton) { ?>
<center>

<div class='boton' id='aceptar_pedidos_div'>
	<table><tr><td>
	<img src='../../iconos/accept.png'>
	</td><td>
	<a href='#' onClick='generar_pedidos();'><span id='texto_boton'>Generar estos Pedidos...</span></a>
	</td></tr></table>
</div>

<div class='boton' id='imprimir_pedidos_div' style='display:none;'>
	<table><tr><td>
	<img src='../../iconos/printer.png'>
	</td><td>
	<a href='#' onClick='imprimir_pedidos();'><span id='texto_boton'>Imprimir Pedidos...</span></a>
	</td></tr></table>
</div>

</center>

<?php } ?>

</body>
