<?php 

       session_start();
       
       session_destroy();

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="favicon.ico" rel="shortcut icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>::: Consulta Paciente :::</title>
<style type="text/css">
<!--
body {
	background-image: url(login_ima.png);
}
.Estilo3 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10px;}
.Estilo4 {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
}
a:link {
	color: #FFFFFF;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FFFFFF;
}
a:hover {
	text-decoration: none;
	color: #FFFFFF;
}
a:active {
	text-decoration: none;
	color: #FFFFFF;
}
.Estilo6 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; }
.Estilo8 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; }
.Estilo9 {color: #FFFFFF;font-size:10px;}
.Estilo10 {font-family: Verdana, Arial, Helvetica, sans-serif; color: #FFFFFF;font-size:10px; }
.Estilo12 {color: #FFFFFF; font-weight: bold; }
.Estilo13 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FFFFFF;
	font-weight: bold;
}
.Estilo14 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 24px;
	font-weight: bold;
	color: #FFFFFF;
}
.Estilo15 {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 20px;
}
.Estilo16 {font-size: 9}
-->
</style></head>

<body>
<p>&nbsp;</p>
<table width="777" border="0" align="center">
  <tr>
    <td width="198" rowspan="3"><div align="center"><img src="logo_min.png" width="188" height="109" /></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><span class="Estilo14">Hospital Dr. Gustavo Fricke - Vi&ntilde;a del Mar</span></div></td>
  </tr>
  <tr>
    <td><div align="center" class="Estilo15">Bienvenido al Sistema de Consulta de Horas en L&iacute;nea.</div></td>
  </tr>
</table>
<p>&nbsp;</p>

<table width="975" border="0" align="center">
   <tr>
     <td width="377">&nbsp;</td>
     <td width="107">&nbsp;</td>
     <td width="472">&nbsp;</td>
     <td width="1">&nbsp;</td>
   </tr>
   <tr>
     <td colspan="4"><div align="center"><span class="Estilo8">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OBTENCION DE CLAVE</span></div></td>
   </tr>
   <tr>
     <td><div class='sub-content'>
      
      <form id='login' name='login' action='consulta.php' method='post'>
        <table width="267" align="right">
          <tr>
      <td width=16><img src="group_go.png" width="16" height="16" /></td>
      <td width="59"><div align="right"><span class="Estilo3">RUT:</span></div></td>
     
       <td width="144"><input type='text' id='rut' name='rut' title="Ingrese su RUT" ondblclick='this.value="";' /></td>
       <td width="12">&nbsp;</td>
      </tr>
      <tr>
      <td width=16><img src="lock.png" width="16" height="16" /></td>
      <td><div align="right"><span class="Estilo3">CLAVE:</span></div></td>
      <td><input type='password' id='clave' name='clave' title="Ingrese su contraseña" ondblclick='this.value="";' /></td>
      <td>&nbsp;</td>
      </tr>
      <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><div align="right">
        <input name="submit" type='submit' value='Consultar' />
      </div></td>
      <td>&nbsp;</td>
      </tr>
      </table>
	   </form>
      </div></td>
     <td colspan="3"><div align="justify" class="Estilo3"><font size="2" color="black"><span style="text-align:justify"><strong>L</strong>a clave de acceso se entregar&aacute; a los pacientes que la soliciten directamente en el Consultorio de Especialidades del Hospital (CAE), o podr&aacute; solicitarla llenando el formulario de Solicitud de Clave, aceptando la cl&aacute;usula de responsabilidad.</span></font></div></td>
   </tr>
   <tr>
     <td colspan="3" style='padding-top:90px;'><div align="center">
       <p align="left" class="Estilo13" >CL&Aacute;USULA DE RESPONSABILIDAD</p>
       <div align="justify" class="Estilo3"><span class="Estilo12"><font size="2">P</font></span><font size="2"><span style="text-align:justify"><span class="Estilo9">ara garantizar la identidad del paciente que accede al sitio Web y posibilitar el uso de la consulta de Citaciones M&eacute;dicas en Internet, existe el uso clave de acceso, personal e intransferible.</span></span></font></div>
       <div align="justify"><span class="Estilo10">El uso de la informaci&oacute;n obtenida a trav&eacute;s de Citaciones M&eacute;dicas del sitio Web del hospital, es de exclusiva responsabilidad del Paciente que solicita la clave, y su mal uso no es imputable al hospital.</span></div>
     </div></td>
     <td>&nbsp;</td>
   </tr>
   
 </table>
 
<p align="center" class="Estilo4"><a href="http://www.sistemasexpertos.cl" target="_blank" class="Estilo16" title="Sistemas Expertos e Ingener&iacute;a de Software LTDA."><img src="logo_seis.png" width="125" height="30"  style='border:0px;' /></a>
</body>
</html>

<script>
document.login.rut.focus();
</script>
