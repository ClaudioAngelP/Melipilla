<?php
    require_once('conectar_db.php');
    if($_GET['tipo']=='diagnosticos') 
    {
?>
    <script>
        realizar_busqueda = function() {
            if(trim($('buscar_diag_txt').value)=='' || $('buscar_diag_txt').value.lenght<3) return;
            var myAjax = new Ajax.Updater('busqueda','buscadores_sql.php', 
            {
                method: 'get', 
		parameters: 'tipo=buscar_diag&buscar='+$('buscar_diag_txt').value,
		evalScripts: true
            });
        }
      
        sel_cod = function(codigo) {
            campo = $('busca_diag').objetivo_cod;
            $(campo).value=codigo;
            $('busca_diag').onCloseFunc();
            $('busca_diag').objeto.destroy();
        }
    </script>
    <div class='sub-content'>
        <img src='iconos/user_edit.png'>
        <b>Buscar Diagn&oacute;stico</b>
    </div>
    <div class='sub-content'>
        <center>
            Buscar: 
            <input type='text' name='buscar_diag_txt' id='buscar_diag_txt' size=20 onKeyUp='if(event.which==13) realizar_busqueda();'>
            <img src='iconos/zoom_in.png' style='cursor: pointer;' onClick='realizar_busqueda();'>
        </center>
    </div>
    <div class='sub-content2' id='busqueda' style='     height:190px; min-height:190px; overflow:auto;'>
    </div>
    <script> $('buscar_diag_txt').focus(); </script>
<?php
    }

    if($_GET['tipo']=='pacientes') 
    {
?>
        <html>
        <title>B&uacute;squeda de Pacientes</title>
        <?php cabecera_popup('.'); ?>
        <script>
            realizar_busqueda = function() {
                if($('buscar_pacs_txt').value.length<2) {
                    return;
                }
               
                $('__carga_busqueda').style.display='';
                
                var myAjax = new Ajax.Updater('busqueda','buscadores_sql.php',
                {
                    method: 'get',
                    evalScripts: true,
                    parameters: 'tipo=buscar_pacs&'+$('buscar_pacs_txt').serialize(),
                    onComplete: function() {
                        $('__carga_busqueda').style.display='none';
                    }
                });
            }
               
            sel_cod = function(codigo) {
                campo = window.objetivo_cod;
                window.opener.$(campo).value=codigo;
                window.onCloseFunc();
                window.close();
            }
        </script>
        <body class="fuente_por_defecto popup_background">
            <table width=100% height=100% cellpadding=0 cellspacing=0>
                <tr>
                    <td>
                        <div id='articulos' class='sub-content'>
                            <table width=100%>
                                <tr>
                                    <td width=20% style='text-align: right;'>Buscar:</td>
                                    <td width=80%>
                                        <input type='text' id='buscar_pacs_txt' name='buscar_pacs_txt' onKeyUp='if(event.which==13) realizar_busqueda();' style='width:80%;'>
                                        <img src='iconos/zoom_in.png' style='cursor: pointer;' onClick='realizar_busqueda();'>
                                    </td>
                                    <td style='width:80px;'>
                                        <img src='imagenes/ajax-loader3.gif' id='__carga_busqueda' style='display:none;'>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <i><u>NOTA:</u> para buscar por nombre, escriba su b&uacute;squeda en el siguiente orden "<b>apellido paterno materno y nombre(s)</b>", el campo apellido materno es opcional.</i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id='busqueda' class='sub-content3' style='height:240px;overflow: auto;'>
                            <center>(No se ha realizado a&uacute;n una b&uacute;squeda...)</center>
                        </div>
                    </td>
                </tr>
            </table>
            <script> $('buscar_pacs_txt').focus(); </script>
        </body>
        </html>
<?php
    }

if($_GET['tipo']=='funcionarios' or $_GET['tipo']=='medicos') {

  print("<html><title>");
  
  if($_GET['tipo']=='funcionarios') {
    print('Buscar Funcionario');
    $bus = 'buscar_funcs';
  } else {
    print('Buscar M&eacute;dico');
    $bus = 'buscar_docs';
  }
  
  print("</title>");
  

  cabecera_popup('.');

?>

    <script>
    
    realizar_busqueda = function() {
			
			if($('buscar_funcs_txt').value.length<2) {
				return;
			}
		
			var myAjax = new Ajax.Updater(
			'busqueda', 
			'buscadores_sql.php', 
			{
				method: 'get', 
				evalScripts: true,
				parameters: 'tipo=<?php echo $bus?>&'+$('buscar_funcs_txt').serialize()
				
      }
			
			);
		
		}
		
		sel_cod = function(codigo) {
      
        objetivo_cod.value=codigo;
        
        onCloseFunc2=onCloseFunc.bind(window.opener);
        onCloseFunc2();
        
        setTimeout('window.close();', 500);
      
    }
    
    </script>
    
  <body class='fuente_por_defecto popup_background'>

  <table width=100% height=100% cellpadding=0 cellspacing=0><tr><td>
  <div id='articulos' class='sub-content'>
	<center>
	<table width=100%>
  <tr><td width=20% style='text-align: right;'>
  Buscar:
	</td><td width=80%>
	<input type='text' id='buscar_funcs_txt' name='buscar_funcs_txt' onKeyUp='
	if(event.which==13) realizar_busqueda();
	' style='width:80%;'>
	<img src='iconos/zoom_in.png' style='cursor: pointer;' 
  onClick='realizar_busqueda();'>
	</td><td>
	</td></tr></table>
	</center>
	</div>
	
	<div id='busqueda' class='sub-content3' style='
	height:230px; font-weight: 10px;
  overflow: auto;
  '>
		<center>
		(No se ha realizado a&uacute;n una b&uacute;squeda...)
		</center>
	</div>
	</td></tr></table>
	
	<script> $('buscar_funcs_txt').focus(); </script>
	
	</body></html>

<?php

}


if($_GET['tipo']=='medicamentos' OR $_GET['tipo']=='medicamentos_controlados' OR $_GET['tipo']=='articulos') {

  if($_GET['tipo']=='medicamentos') {
          $pagina='buscar_meds';
          $id='busca_meds';
  } else if ($_GET['tipo']=='medicamentos_controlados') {
          $pagina='buscar_meds_controlados';
          $id='busca_meds';
  } else {
          $pagina='buscar_arts';
          $id='busca_arts';
  }

?>

	<script>
	
		realizar_busqueda = function() {
			
			if($('buscar_meds_txt').value.length<2) {
				return;
			}
		
			var myAjax = new Ajax.Updater(
			'busqueda', 
			'buscadores_sql.php', 
			{
				method: 'get', 
				evalScripts: true,
				parameters: 'tipo=<?php echo $pagina; ?>&'+$('buscar_meds_txt').serialize()
				
      }
			
			);
		
		}
		
		sel_cod = function(codigo) {
      
        campo = $('<?php echo $id; ?>').objetivo_cod;
        
        $(campo).value=codigo;
      
        $('<?php echo $id; ?>').onCloseFunc();
      
        $('<?php echo $id; ?>').objeto.destroy();
      
    }

	</script>
		
	<table width=100% height=100% cellpadding=0 cellspacing=0><tr><td>
  <div id='articulos' class='sub-content'>
	<center>
	<table width=100%>
  <tr><td width=20% style='text-align: right;'>
  Buscar:
	</td><td width=80%>
	<input type='text' id='buscar_meds_txt' name='buscar_meds_txt' onKeyUp='
	if(event.which==13) realizar_busqueda();
	' style='width:80%;'>
	<img src='iconos/zoom_in.png' style='cursor: pointer;' 
  onClick='realizar_busqueda();'>
	</td><td>
	</td></tr></table>
	</center>
	</div>
	
	<div id='busqueda' class='sub-content2' style='
	height:190px;
  overflow: auto;
  '>
		<center>
		(No se ha realizado a&uacute;n una b&uacute;squeda...)
		</center>
	</div>
	</td></tr></table>
	
	<script> $('buscar_meds_txt').focus(); </script>

<?php

}

?>
