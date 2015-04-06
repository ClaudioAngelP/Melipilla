<?php

  require_once('../../conectar_db.php');

  $informes = file_get_contents('informes.txt');

  $informe=explode("\n", $informes);

  $u=0;

  $lista_informes='';

  for($i=0;$i<count($informe);$i++) {

    $inf = explode('|',$informe[$i]);

    if(count($inf)==2) {

    ($u%2==0) ?  $clase='tabla_fila' : $clase='tabla_fila2';

    $lista_informes.='
    <tr class="'.$clase.'" style="cursor:pointer;"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\'"
    onClick="seleccionar_inf('.$u.', \''.trim($inf[1]).'\');"
    >
    <td>'.htmlentities($inf[0]).'</td></tr>
    ';

    $u++;

    }

  }

?>

<script>

var informe_actual='';

seleccionar_inf = function(num, archivo) {

  $('inf_formulario').innerHTML='<br><br><br><br><br><br><br><br><br><br><br><br><img src="imagenes/ajax-loader3.gif"><br>Cargando Formulario...';

  var myAjax = new Ajax.Updater(
  'inf_formulario',
  'estadisticas/estadisticas_farmacia/informes/'+archivo,
  {
    method: 'get',
    evalScripts: true
  });

  informe_actual=archivo;

}

procesar_formulario = function() {

  var myfrm = $('genform');

  myfrm.target='win_informes';
  myfrm.method='post';
  myfrm.action='estadisticas/estadisticas_farmacia/informes/'+informe_actual;
  
  if(!validaciones()) return;

  if($('form_ver').value!=2) {

    top=Math.round(screen.height/2)-225;
    left=Math.round(screen.width/2)-350;

    new_win =
    window.open('estadisticas/cargando.html',
    'win_informes', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
    'top='+top+', left='+left);

    new_win.focus();

  }

  setTimeout('$("genform").submit();', 500);

}

</script>

<center>
<table style='width:720px;'>
<tr><td>

<div class='sub-content'>
<div class='sub-content'>
<img src='iconos/chart_bar.png'>
<b>Estad&iacute;sticas Farmacia</b>
</div>

<table style='width:100%;'>
<tr><td style='width:220px;'>

<div class='sub-content2' style='height:400px; overflow:auto;'>
<table style='width:100%'>
<?=$lista_informes?>
</table>
</div>

</td><td>

<div class='sub-content2' style='height:400px; overflow:auto;'
id='inf_formulario'>

</div>

</td></tr>
</table>

</div>

</td></tr>
</table>
</center>
