<?php
  
  require_once('../conectar_db.php');

  $tipo=($_GET['tipo']*1);
  $art_num=($_GET['art_num']*1);
  $nro_talonarios=($_GET['nro_talonarios']*1);
  $tipo_talonarios=($_GET['tipotalonario_id']*1);
  $cadena_actual=($_GET['cadena_actual']);

  $tals = explode('|', $cadena_actual);

  $talonarios = Array();

  for($i=0;$i<$nro_talonarios;$i++) {
    $talonarios[$i] = Array();
    for($c=0;$c<3;$c++)
      $talonarios[$i][$c]='';
  }

  for($i=0;$i<count($tals);$i++) {

    $tal=explode('-', $tals[$i]);

    if(count($tal)==3)
      $talonarios[$i] = $tal;

  }

  print("
  <html><title>Especificar Talonarios</title>
  ");

  cabecera_popup('..');

?>

  <script>

  var talcadena='';
  var campo_upd = <?php if($tipo==0) echo '12'; else echo '8'; ?>;

  comprobar_talonarios = function() {

    talcadena='';

    for(i=1;i<=<?php echo $nro_talonarios; ?>;i++) {

      //talnum = $('talonario_nro_'+i).value*1;
      taldes = $('talonario_desde_'+i).value*1;
      talhas = $('talonario_hasta_'+i).value*1;

      // Comprueba validez de los números ingresados.
         //talnum<=0||
      if(talhas<=0||taldes<=0||taldes>=talhas) {
        alert(('Descripci&oacute;n del talonario est&aacute; incorrecta.').unescapeHTML());
        return;
      }

      // Comprueba que los números ingresados no se sobrepongan a los
      // otros de la misma lista.

      for(u=1;u<=<?php echo $nro_talonarios; ?>;u++) {
        if(u!=i) {
        compr=true;
        //_talnum = $('talonario_nro_'+u).value*1;
        _taldes = $('talonario_desde_'+u).value*1;
        _talhas = $('talonario_hasta_'+u).value*1;

       // if(talnum==_talnum) compr=false;
        if(_taldes<=taldes && _talhas>=taldes) compr=false;
        if(_taldes>=taldes && _talhas<=talhas) compr=false;
        if(_taldes<=talhas && _talhas>=talhas) compr=false;
        if(_taldes<=taldes && _talhas>=talhas) compr=false;

        if(!compr) {
          alert(('La numeraci&oacute;n del talonario '+(i)+' se sobrepone a la del talonario '+(u)+'.').unescapeHTML());
          return;
        }
        }
      }

     // talcadena+=talnum+'-'+taldes+'-'+talhas;
      talcadena+=taldes+'-'+talhas;
      talcadena+='|';

    }

    var myAjax = new Ajax.Request(
    'comp_talonarios.php',
    {
      method: 'get',
      parameters: 'cadena_talonarios='+talcadena+
                  '&tipotalonario=<?php echo $tipo_talonarios; ?>'+'&num_talonario=<?php echo $nro_talonarios; ?>',
      onComplete: function(respuesta) {
        if(respuesta.responseText=='OK')
          guardar_talonarios();
        else
          alert(respuesta.responseText.unescapeHTML());
      }
    }
    );

  }

  guardar_talonarios = function () {

    window.opener.articulos[<?php echo $art_num; ?>][campo_upd]=talcadena;

    window.close();

  }

  </script>

  <body class='fuente_por_defecto popup_background'>

  <table style='width:100%'>
    <tr class='tabla_header' style='font-weight: bold;'>
    <td>#</td>

    <td>Nro. Inicial</td>
    <td>Nro. Final</td>
    </tr>

<?php
  //<td>Nro. Talonario</td>
  // <td><center>
  //  <input type='text' id='talonario_nro_$i' style='text-align: right'
  //  name='talonario_nro_$i' size=10 value='".$talonarios[$i-1][0]."'></td>


  for($i=1;$i<=$nro_talonarios;$i++) {

    (($i%2)==0) ? $clase='tabla_fila' : $clase='tabla_fila2';

    print("
    <tr class='$clase'>
    <td style='text-align: right;'>$i.-</td>
    <td><center>
    <input type='text' id='talonario_desde_$i' style='text-align: right'
    name='talonario_desde_$i' size=10 value='".$talonarios[$i-1][1]."'></td>
    <td><center>
    <input type='text' id='talonario_hasta_$i' style='text-align: right'
    name='talonario_hasta_$i' size=10 value='".$talonarios[$i-1][2]."'></td>
    </tr>
    ");

  }

?>

  <tr>
  <td colspan=4>
  <center>
	<table><tr><td>

		<div class='boton'>
		<table><tr><td>
		<img src='../iconos/accept.png'>
		</td><td>
		<a href='#' onClick='comprobar_talonarios();'>Ingresar Valores...</a>
		</td></tr></table>
		</div>

  </td></tr></table>
  </center>

  </td>
  </tr>
  </table>
  </body>
  <script>
   // $('talonario_nro_1').select();
   // $('talonario_nro_1').focus();
  </script>
  </html>
