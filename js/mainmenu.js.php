<?php

  require_once('../conectar_db.php');

?>

// Javascript
// Men� Principal de la Aplicaci�n
// Rodrigo Carvajal J. (rcarvajal@scv.cl)

function createjsDOMenu() {

  menusesion = new jsDOMenu(200, "fixed");

  with (menusesion) {

    addMenuItem(new menuItem("Cambiar Clave de Acceso", "", "javascript:cambiar_pagina('administracion/cambiar_clave/form.php');"));

    addMenuItem(new menuItem("-"));

    addMenuItem(new menuItem("Cerrar Sesi�n...", "", "login.php"));

  }

  

  

    

<?php 

  $menu_principal='';
  if( _cax(1)   OR _cax(2)  OR _cax(23) OR _cax(3)  OR _cax(4)  OR 

      _cax(5)   OR _cax(6)  OR _cax(7)  OR _cax(23)  OR _cax(24) OR _cax(25) OR _cax(26) OR 

      _cax(27)  OR _cax(28) OR _cax(8)  OR _cax(9) OR _cax(41) OR

      _cax(60) OR _cax(61) OR _cax(62) OR _cax(70) OR _cax(31) OR _cax(20000)) {

      

  print('

  menufarmaciabod = new jsDOMenu(250, "fixed");

  with (menufarmaciabod) {

  ');

  $menu_principal.='addMenuBarItem(new menuBarItem("Farmacia/Bodega", menufarmaciabod));';

  }



  if(_cax(1)) {

  print('

  addMenuItem(new menuItem("Ingreso/Edici�n de Art�culos", "", "javascript:cambiar_pagina(\'abastecimiento/ingreso_articulos/form.php\');"));

  ');

  }

  if(_cax(31)) {

  print('

  addMenuItem(new menuItem("�rdenes de Compra", "", "javascript:cambiar_pagina(\'abastecimiento/orden_compra/form.php\');"));

  addMenuItem(new menuItem("Cuentas por Pagar", "", "javascript:cambiar_pagina(\'abastecimiento/pago_cuentas/form.php\');"));


  ');

  }

  if(_cax(20000)) {

  print('

  addMenuItem(new menuItem("Codificaci�n Masiva O.C.", "", "javascript:cambiar_pagina(\'abastecimiento/codificacion_masiva/form.php\');"));

 ');

  }

  

  if(_cax(2)) {

  print('

  addMenuItem(new menuItem("Recepci�n de Art�culos", "", "javascript:cambiar_pagina(\'abastecimiento/recepcion_articulos/form.php\');"));

  ');

  }



  if(_cax(26)) {

  print('

  addMenuItem(new menuItem("Recepci�n de Consumo Inmediato", "", "javascript:cambiar_pagina(\'abastecimiento/recepcion_gastos/form.php\');"));
  addMenuItem(new menuItem("Recepci�n de Facturas", "", "javascript:cambiar_pagina(\'abastecimiento/recepcion_facturas/form.php\');"));

  ');

  }

  

  if(_cax(23)) {

  print('

  addMenuItem(new menuItem("Historial de Recepci�n", "", "javascript:cambiar_pagina(\'abastecimiento/historial_recepcion/form.php\');"));

  ');

  }

  

  if(_cax(3)) {

  print('

  addMenuItem(new menuItem("Movimiento de Art�culos", "", "javascript:cambiar_pagina(\'abastecimiento/movimiento_articulos/form.php\');"));

  ');

  }



  if(_cax(24)) {

  print('

  addMenuItem(new menuItem("Ajustes de Stock", "", "javascript:cambiar_pagina(\'abastecimiento/ajustar_stock/form.php\');"));

  ');

  }

  if(_cax(25)) {

  print('

  addMenuItem(new menuItem("Realizar Preparaciones", "", "javascript:cambiar_pagina(\'abastecimiento/preparaciones/form.php\');"));
  addMenuItem(new menuItem("Recetario NPT", "", "javascript:cambiar_pagina(\'recetas/receta_npt/form.php\');"));
  addMenuItem(new menuItem("Consultas Receta NPT", "", "javascript:cambiar_pagina(\'recetas/receta_npt/form_consultar.php\');"));

  ');

  }
if(_cax(41)) {

  print('
  
  addMenuItem(new menuItem("Recepci�n Recetas NPT", "", "javascript:cambiar_pagina(\'recetas/receta_npt/form_consultar.php\');"));

  ');

  }


  if((_cax(1) OR _cax(2) OR _cax(23) OR _cax(3)) AND _cax(4)) {

    print('

      addMenuItem(new menuItem("-"));

    ');

  }

  

  if(_cax(4)) {

  print('

  addMenuItem(new menuItem("Bincard de Art�culos", "", "javascript:cambiar_pagina(\'abastecimiento/bincard_articulos/form.php\');"));

  addMenuItem(new menuItem("Indice de Rotaci�n de Art�culos", "", "javascript:cambiar_pagina(\'abastecimiento/informe_rotacion/form.php\');"));

  addMenuItem(new menuItem("Total de Recetas y Preescripciones", "", "javascript:cambiar_pagina(\'abastecimiento/informe_recetas/form.php\');"));

  addMenuItem(new menuItem("Listado Conteo Selectivo", "", "javascript:cambiar_pagina(\'abastecimiento/listado_selectivo/form.php\');"));

  ');

  }

  

  if(_cax(28)) {

  print('



  addMenuItem(new menuItem("Informe General de Gastos", "", "javascript:cambiar_pagina(\'abastecimiento/informe_gastos/form.php\');"));

  ');

  }







  if((_cax(1) OR _cax(2) OR _cax(23) OR _cax(3) OR _cax(4)) 

  AND (_cax(5) OR _cax(6) OR _cax(7)))  {

    print('

      addMenuItem(new menuItem("-"));

    ');

  }





  if(_cax(5)) {

  print('

  addMenuItem(new menuItem("Stock Cr�tico/Pedido", "", "javascript:cambiar_pagina(\'abastecimiento/stock_articulos/form.php\');"));

  ');

  }

  

  if(_cax(6)) {

  print('

  addMenuItem(new menuItem("Valorizaci�n de Art�culos", "", "javascript:cambiar_pagina(\'abastecimiento/valorizacion_articulos/form.php\');"));

  ');

  }

  

  /*if(_cax(7)) {

  print('

  addMenuItem(new menuItem("Libro de Controlados", "", "javascript:cambiar_pagina(\'abastecimiento/libro_controlados/form.php\');"));

  ');

  } */

  

  if((_cax(1) OR _cax(2) OR _cax(23) OR _cax(3) OR _cax(4) 

  OR _cax(5) OR _cax(6) OR _cax(7)) AND (_cax(8) OR _cax(9)

  OR _cax(60) OR _cax(61) OR _cax(62))) {

    print('

      addMenuItem(new menuItem("-"));

    ');

  }

  

  if(_cax(60)) {

  print('

  addMenuItem(new menuItem("Crear Solicitudes de Compra", "", "javascript:cambiar_pagina(\'abastecimiento/solicitudes_compra/form.php\');"));

  ');

  }

  

  if(_cax(61) OR _cax(62)) {

  print('

  addMenuItem(new menuItem("Revisi�n Solicitudes de Compra", "", "javascript:cambiar_pagina(\'abastecimiento/revision_solicitudes/form.php\');"));

  ');

  }

  

  

  if(_cax(8) OR _cax(9) OR _cax(22) OR _cax(27)) {

  print('

  addMenuItem(new menuItem("Pedido de Art�culos", "pedidoarts"));

  ');

  }

  

  if(_cax(70)) {

  print('

  addMenuItem(new menuItem("Hoja de Cargo por Paciente", "", "javascript:cambiar_pagina(\'abastecimiento/hoja_cargo/form.php\');"));

  ');

  }

  

  if(_cax(1) OR _cax(2) OR _cax(23) OR _cax(24) OR _cax(25) OR _cax(3) OR _cax(4) 

             OR _cax(5) OR _cax(6) OR _cax(7) OR _cax(41)

             OR _cax(8) OR _cax(9) OR _cax(22) OR _cax(27) OR _cax(28) 

             OR _cax(60) OR _cax(61) OR _cax(62) OR _cax(70) OR _cax(31) OR _cax(20000)) {

  print('

  }

  ');

  }



  if(_cax(8) OR _cax(9) OR _cax(22) OR _cax(27)) {

  print('

  pedidoarts_sub = new jsDOMenu(180, "fixed");

  with (pedidoarts_sub) {

  ');

  }



  if(_cax(8)) {

  print('

  addMenuItem(new menuItem("Lista de Pedido", "", "javascript:cambiar_pagina(\'abastecimiento/pedido_articulos/form.php\');"));');

  }



  if(_cax(27)) {

  print('

  addMenuItem(new menuItem("Listado de Reposici�n", "", "javascript:cambiar_pagina(\'abastecimiento/listado_reposicion/form.php\');"));');

  }





  if(_cax(9)) {

  print('

  addMenuItem(new menuItem("Recepci�n de Art�culos", "", "javascript:cambiar_pagina(\'abastecimiento/recepcion_pedido/form.php\');"));

  ');

  }



  if(_cax(22)) {

  print('

  addMenuItem(new menuItem("Historial de Pedidos", "", "javascript:cambiar_pagina(\'abastecimiento/historial_pedido/form.php\');"));

  ');

  }



  if(_cax(8) OR _cax(9) OR _cax(22) OR _cax(27)) {

  print('

  }



  menufarmaciabod.items.pedidoarts.setSubMenu(pedidoarts_sub);

  ');

  }





  if(_cax(10) or _cax(19) OR _cax(502) OR _cax(503) OR _cax(504)) {



  $menu_principal.='addMenuBarItem(new menuBarItem("Recetas", menurecetas));';



  print('

  menurecetas = new jsDOMenu(200, "fixed");

  with (menurecetas) {

  ');



  }



  if(_cax(19)) {

   print('

    addMenuItem(new menuItem("B�squeda de Recetas", "", "javascript:cambiar_pagina(\'recetas/ver_recetas/form.php\');"));

  ');

  }



  if(_cax(10)) {

  print('

    addMenuItem(new menuItem("Entrega de Medicamentos", "", "javascript:cambiar_pagina(\'ficha_clinica/ficha_basica/form.php\');"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Reporte F�rmacos Restringidos/GES/MAC", "", "javascript:cambiar_pagina(\'recetas/reporte_farmacos/form.php\');"));


  ');

  }
  
     
  if(_cax(502) OR _cax(503)){
  
   print('
    addMenuItem(new menuItem("Autorizaci�n de F�rmacos", "", "javascript:cambiar_pagina(\'ficha_clinica/autorizar_farmacos/form.php\');"));

  ');

  
  
  }

  if(_cax(504)){
  
   print('
    addMenuItem(new menuItem("Autorizaci�n de Antimicrobianos", "", "javascript:cambiar_pagina(\'ficha_clinica/autorizar_antimicrobianos/form.php\');"));

  ');

  
  
  }




  if(_cax(10) OR _cax(19)  OR _cax(502) OR _cax(503) OR _cax(504)) {



  print('

  }

  ');



  }







  if(_cax(38) OR _cax(39) OR _cax(40)) {

  

  $menu_principal.='addMenuBarItem(new menuBarItem("Listas de Espera", menuagenda));';

  

  print('

  menuagenda = new jsDOMenu(200, "fixed");

  with (menuagenda) {

  ');

  

  }


  if(_cax(39)) {

  	print('

  		addMenuItem(new menuItem("Adm. Listas de Espera", "", "javascript:cambiar_pagina(\'agenda_medica/lista_espera/form.php\');"));
		addMenuItem(new menuItem("Consolidado LEA y LEQ (FOTO)", "", "javascript:cambiar_pagina(\'agenda_medica/lista_espera/lista1.php\');"));

  	');

  } elseif(_cax(38)) {

  	print('

  		addMenuItem(new menuItem("Visualizar Listas de Espera", "", "javascript:cambiar_pagina(\'agenda_medica/lista_espera/form.php\');"));

  	');  	
  
  }

  if(_cax(38)) {

  print('

	  addMenuItem(new menuItem("Adm. Listas de Espera", "", "javascript:cambiar_pagina(\'agenda_medica/lista_espera/form.php?notificar=1\');"));
	  addMenuItem(new menuItem("Consolidado LEA y LEQ (FOTO)", "", "javascript:cambiar_pagina(\'agenda_medica/lista_espera/lista1.php\');"));

  ');

  }



  if(_cax(38) OR _cax(39) OR _cax(40)) {

  print('

  }

  ');

  }  



  

  if(_cax(36) OR _cax(35) OR _cax(34) OR _cax(51)) {

  $menu_principal.='addMenuBarItem(new menuBarItem("Interconsultas", menuinter));';

  print('
  menuinter = new jsDOMenu(260, "fixed");
  with (menuinter) {
  ');

  }



  if(_cax(34)) {

  print('

  addMenuItem(new menuItem("Descarga de Interconsultas", "", "javascript:cambiar_pagina(\'interconsultas/ingreso_inter_auto/form.php\');"));
  addMenuItem(new menuItem("Ingresar Interconsultas", "", "javascript:cambiar_pagina(\'interconsultas/ingreso_inter/form.php\');"));

  ');

  }

  if(_cax(51)) {

  print('

  addMenuItem(new menuItem("Ingresar O.A. Controles", "", "javascript:cambiar_pagina(\'prestaciones/orden_atencion/form.php?tipo=1\');"));
  addMenuItem(new menuItem("Ingresar O.A. Hospitalizaci�n", "", "javascript:cambiar_pagina(\'prestaciones/orden_hospitalizacion/form.php?tipo=2\');"));

  ');

  }


  if(_cax(35)) {

  print('

  addMenuItem(new menuItem("Revisi�n de Solicitudes", "", "javascript:cambiar_pagina(\'interconsultas/revision_inter/form.php\');"));

  ');

  }

  if(_cax(36)) {

  print('

  addMenuItem(new menuItem("Estado de Solicitudes", "", "javascript:cambiar_pagina(\'interconsultas/estado_inter/form.php\');"));

  ');

  }


  if(_cax(36) OR _cax(35) OR _cax(34) OR _cax(51)) {
  print(' 
		} 
	');
  }
	

  if(_cax(250) OR _cax(251) OR _cax(252) OR _cax(253) OR _cax(254) OR _cax(255) OR _cax(256) OR _cax(257)) {

  $menu_principal.='addMenuBarItem(new menuBarItem("Gesti�n de Camas", menucamas));';

  print('

  menucamas = new jsDOMenu(240, "fixed");

  with (menucamas) {

	');
   }
  
  if( _cax(250) )
  print('
      addMenuItem(new menuItem("Ingreso Hospitalario", "", "javascript:cambiar_pagina(\'prestaciones/ingreso_egreso_hospital/solicitud_hospitalizacion.php\');"));
      addMenuItem(new menuItem("-"));
	');
	 
  if( _cax(250) OR _cax(251))
  print('
      addMenuItem(new menuItem("Asignaci�n de Camas", "", "javascript:cambiar_pagina(\'prestaciones/asignar_camas/form.php\');"));
     ');

  if( _cax(253) AND !_cax(250) AND !_cax(251))
  print('
      addMenuItem(new menuItem("Visualizar Gesti�n Central de Camas", "", "javascript:cambiar_pagina(\'prestaciones/asignar_camas/form.php\');"));
     ');

  if( _cax(252) ){
	print('
      addMenuItem(new menuItem("Censo Diario de Pacientes", "", "javascript:cambiar_pagina(\'prestaciones/censo_pacientes/form.php\');"));
     ');
   print('
      addMenuItem(new menuItem("Informes Gesti�n de Camas", "", "javascript:cambiar_pagina(\'prestaciones/informes_camas/form.php\');"));
     ');
   }

   if(_cax(255))
	print('
      addMenuItem(new menuItem("Consulta O.I.R.S.", "", "javascript:cambiar_pagina(\'prestaciones/informes_camas/form_oirs.php\');"));
     ');
     
   
  if( _cax(254))
  print('
      addMenuItem(new menuItem("-"));
      addMenuItem(new menuItem("Administrar Bloqueo de Camas", "", "javascript:cambiar_pagina(\'prestaciones/bloqueo_camas/form.php\');"));
     ');

  if( _cax(256))
  print('
      addMenuItem(new menuItem("Solicitud Traslado Ambulancia", "", "javascript:cambiar_pagina(\'prestaciones/solicitud_ambulancia/form.php\');"));
     ');

  if( _cax(257))
  print('
      addMenuItem(new menuItem("Validar Traslados Ambulancia", "", "javascript:cambiar_pagina(\'prestaciones/solicitud_ambulancia/form2.php\');"));
     ');
     

  if(_cax(250) OR _cax(251) OR _cax(252) OR _cax(253) OR _cax(254) OR _cax(255) OR _cax(256) OR _cax(257)) 
  print('}');  

	

  if(_cax(49) OR _cax(50) OR _cax(200) OR _cax(201) OR _cax(202) OR _cax(300) OR _cax(203) OR
  		_cax(205) OR _cax(206) OR _cax(207) OR _cax(208) OR _cax(209) OR _cax(301) OR _cax(302)
  		OR _cax(52)) {

  $menu_principal.='addMenuBarItem(new menuBarItem("Prestaciones", menupresta));';

  print('

  menupresta = new jsDOMenu(240, "fixed");

  with (menupresta) {

  ');

  }

  if( _cax(52) )
  print('
    addMenuItem(new menuItem("Consulta Datos del Pacientes", "", "javascript:cambiar_pagina(\'ficha_clinica/ficha_consulta/form.php\');"));
  ');



  if(_cax(200))
  	print('
  		addMenuItem(new menuItem("Consulta por Paciente", "", "javascript:cambiar_pagina(\'prestaciones/consultar_paciente/form.php\');"));
  	');


  // addMenuItem(new menuItem("Informe Estad�stico de N�minas", "", "javascript:cambiar_pagina(\'prestaciones/consultar_nominas/form.php\');"));

	/*
  if(_cax(201))
	print('
		addMenuItem(new menuItem("Planilla de Prestaciones Diarias", "", "javascript:cambiar_pagina(\'prestaciones/ingreso_prestaciones/form.php\');"));	
	');*/

  if(_cax(202) OR _cax(300))
	print('
      addMenuItem(new menuItem("N�minas de Atenci�n", "", "javascript:cambiar_pagina(\'prestaciones/ingreso_nominas/form.php\');"));
	addMenuItem(new menuItem("N�minas de Reagendamiento", "", "javascript:cambiar_pagina(\'prestaciones/ingreso_nominas/form_reagendar.php\');"));
      addMenuItem(new menuItem("Informe Estad�stico de N�minas", "", "javascript:cambiar_pagina(\'prestaciones/consultar_nominas/form.php\');"));
	');
  
  // addMenuItem(new menuItem("Informe de Producci�n", "", "javascript:cambiar_pagina(\'prestaciones/informe_nominas/form.php\');"));

  if(_cax(205) OR _cax(206) OR _cax(207) OR _cax(208) OR _cax(209))
	print('
      addMenuItem(new menuItem("Registro de FAP", "", "javascript:cambiar_pagina(\'prestaciones/ingreso_fap/form.php\');"));
	');

  if(_cax(205) OR _cax(206) OR _cax(207) OR _cax(208))
      print('addMenuItem(new menuItem("Informes Estad�sticos de FAP", "", "javascript:cambiar_pagina(\'prestaciones/consultar_fap/form.php\');"));');
	
  if(_cax(50)) {

  print('

      addMenuItem(new menuItem("-"));

      addMenuItem(new menuItem("Ficha Cl�nica de Pacientes", "", "javascript:cambiar_pagina(\'prestaciones/episodios_clinicos/form.php\');"));
      addMenuItem(new menuItem("Monitoreo GES", "", "javascript:cambiar_pagina(\'prestaciones/monitoreo_ges/form.php\');"));
      addMenuItem(new menuItem("Reportes GES", "", "javascript:cambiar_pagina(\'prestaciones/reportes_ges/form.php\');"));

  ');

  }

  if(_cax(49))
	print('addMenuItem(new menuItem("Bandejas Proceso de Monitoreo", "", "javascript:cambiar_pagina(\'listas_dinamicas/form.php\');"));');  
  
if(_cax(301))
      print('
                addMenuItem(new menuItem("Recaudaci�n de Prestaciones", "", "javascript:cambiar_pagina(\'creditos/ingreso/form.php\');"));
                addMenuItem(new menuItem("Informes de Caja", "", "javascript:cambiar_pagina(\'ingresos/cierre_caja/form.php\');"));
                addMenuItem(new menuItem("Registro de Cheques", "", "javascript:cambiar_pagina(\'ingresos/cierre_caja/form_cheques.php\');"));
                addMenuItem(new menuItem("-"));
                addMenuItem(new menuItem("B�squeda de Pacientes/Cr�ditos", "", "javascript:cambiar_pagina(\'creditos/busqueda/form.php\');"));
                addMenuItem(new menuItem("Listado de Pacientes/Cr�ditos Morosos", "", "javascript:cambiar_pagina(\'creditos/busqueda/form.php?morosidad=1\');"));
        ');

  if(_cax(49) OR _cax(50) OR _cax(200) OR _cax(201) OR _cax(202) OR _cax(300) OR _cax(203) OR
  		_cax(205) OR _cax(206) OR _cax(207) OR _cax(208) OR _cax(209) OR _cax(301) OR _cax(302)
  		OR _cax(52)) {

  print('

  }

  ');

  }  



  if(_cax(42) OR _cax(43) OR _cax(44) OR _cax(45) OR _cax(46) OR _cax(48) OR _cax(204)) {

  

  $menu_principal.='addMenuBarItem(new menuBarItem("Archivo", menuarchivo));';

  

  print('

  menuarchivo = new jsDOMenu(250, "fixed");

  with (menuarchivo) {

  ');

  

  }


	if(_cax(48)) {

  print('

  addMenuItem(new menuItem("Picking Ficha", "", "javascript:cambiar_pagina(\'prestaciones/archivo_fichas/form.php\');"));

  ');

  }


  if(_cax(42)) {

  print('

  addMenuItem(new menuItem("Crear Solicitudes de Fichas", "", "javascript:cambiar_pagina(\'archivo/solicitud/form.php\');"));

  ');

  }

  

  



  if(_cax(43)) {

  print('

  addMenuItem(new menuItem("Salida de Fichas", "", "javascript:cambiar_pagina(\'archivo/movimiento/form.php\');"));

  ');

  }

  if(_cax(44)) {

  print('

  addMenuItem(new menuItem("Recepci�n de Fichas", "", "javascript:cambiar_pagina(\'archivo/entradas/form.php\');"));

  ');

  }

  if(_cax(45)) {

  print('

  addMenuItem(new menuItem("Salidas Especiales desde Archivo", "", "javascript:cambiar_pagina(\'archivo/pedidos/form.php\');"));

  ');

  }



  if(_cax(46) OR _cax(47)) {

  print('

  addMenuItem(new menuItem("Priorizaci�n y Autorizaci�n de Solicitudes", "", "javascript:cambiar_pagina(\'archivo/estado/form.php\');"));

  ');

  }

  if(_cax(204)) {
  print('
  addMenuItem(new menuItem("Ingreso de Pacientes Extra", "", "javascript:cambiar_pagina(\'prestaciones/ingreso_nomina_extra/form.php\');"));
  ');
  }




  if(_cax(42) OR _cax(43) OR _cax(44) OR _cax(45) OR _cax(46) OR _cax(48) OR _cax(204)) {

  print('

  }

  ');

  }  

  
  
  if(_cax(100) OR _cax(102) OR _cax(103) OR _cax(104) or _cax(105) OR _cax(106)) {   

  

  print('

  menuequipos = new jsDOMenu(320, "fixed");

  with (menuequipos) {

  ');

  $menu_principal.='addMenuBarItem(new menuBarItem("Activo Fijo", menuequipos, "Mequipos"));';

  

  }



  if(_cax(100)) {

  print('

  addMenuItem(new menuItem("Listado de Inventario Asignado", "", "javascript:cambiar_pagina(\'equipos/listado_usuario/form.php\');"));

  ');

  }



  if(_cax(102)) {

  print('

  addMenuItem(new menuItem("Inventario Asignado Pendiente", "", "javascript:cambiar_pagina(\'equipos/listado_mantencion/form.php\');"));

  ');

  }

  

  if(_cax(103)) {

  print('

  addMenuItem(new menuItem("Asignaci�n de Inventario en Mantenci�n", "", "javascript:cambiar_pagina(\'equipos/asignar_equipos/form.php\');"));

  ');

  }

  

  if(_cax(104)) {

  print('

  addMenuItem(new menuItem("Ingreso de Nuevo Inventario", "", "javascript:cambiar_pagina(\'equipos/ingreso_equipos/form.php\');"));

  ');

  }

 

 if(_cax(105)) {

  print('

  addMenuItem(new menuItem("Inventario de Activo Fijo", "", "javascript:cambiar_pagina(\'equipos/inventario_equipos/form.php\');"));

  ');

  }

   if(_cax(106)) {

  print('

  addMenuItem(new menuItem("Administrar Documentaci�n de Ordenes", "", "javascript:cambiar_pagina(\'equipos/listado_mantencion/form.php?modo=admin\');"));

  ');

  }

 

 if(_cax(100) OR _cax(102) OR _cax(103) OR _cax(104) OR _cax(105) OR _cax(106)) {   

 print('}');

 } 



  if(_cax(12) OR _cax(13) OR _cax(14) OR _cax(15) OR _cax(16) OR _cax(75) 

      OR _cax(17) OR _cax(20) OR _cax(39) OR _cax(37) OR _cax(40) OR _cax(110) 
      
      OR _cax(303) OR _cax(304) OR _cax(502) OR _cax(503)) {

  print('

  menuadmin = new jsDOMenu(230, "fixed");

  with (menuadmin) {

  ');

  $menu_principal.='addMenuBarItem(new menuBarItem("Administraci�n", menuadmin, "Madmin"));';



  }



  if(_cax(12)) {

  print('

  addMenuItem(new menuItem("Ubicaciones", "", "javascript:cambiar_pagina(\'administracion/ubicaciones/form.php\');"));

  ');

  }
  
  if(_cax(10000) AND _cax(10001)) {

  print('

  addMenuItem(new menuItem("Control de Tareas", "", "javascript:cambiar_pagina(\'administracion/control_tareas/form.php\');"));

  ');

  }



  if(_cax(13)) {

  print('

  addMenuItem(new menuItem("Items Presupuestarios", "", "javascript:cambiar_pagina(\'administracion/items_presupuestarios/form.php\');"));

  ');

  }



  if(_cax(14)) {

  print('

  addMenuItem(new menuItem("Turnos", "", "javascript:cambiar_pagina(\'administracion/turnos/form.php\');"));

  ');

  }



  if(_cax(15)) {

  print('

  addMenuItem(new menuItem("Centros de Responsabilidad", "", "javascript:cambiar_pagina(\'administracion/centros_responsabilidad/form.php\');"));

  ');

  }



  if((_cax(16) OR _cax(18)) AND

  (_cax(12) OR _cax(13) OR _cax(14) OR _cax(15))) {

    print('

      addMenuItem(new menuItem("-"));

    ');

  }



  if(_cax(16)) {

  print('

  addMenuItem(new menuItem("Proveedores", "", "javascript:cambiar_pagina(\'administracion/proveedores/form.php\');"));

  ');

  }



  if(_cax(18)) {

  print('

  addMenuItem(new menuItem("Convenios", "", "javascript:cambiar_pagina(\'administracion/convenios/form.php\');"));

  ');

  }
  
  if(_cax(502) OR _cax(503)){
  
   print('
    addMenuItem(new menuItem("Autorizaciones de F�rmacos", "", "javascript:cambiar_pagina(\'administracion/autorizacion_farmacos/form.php\');"));

  ');
  
  }


  if((_cax(17) OR _cax(20)) AND (_cax(16) OR _cax(18) OR _cax(12)

  OR _cax(13) OR _cax(14) OR _cax(15) OR _cax(39) OR _cax(40))) {

    print('

      addMenuItem(new menuItem("-"));

    ');

  }



  if(_cax(20)) {

  print('

  addMenuItem(new menuItem("Talonarios de Recetas", "", "javascript:cambiar_pagina(\'administracion/talonarios/form.php\');"));

  ');

  }



  if(_cax(17)) {

  print('

  addMenuItem(new menuItem("Funcionarios", "", "javascript:cambiar_pagina(\'administracion/funcionarios/form.php\');"));

  ');

  }

  

  if(_cax(39)) {

  print('

  addMenuItem(new menuItem("Especialidades", "", "javascript:cambiar_pagina(\'administracion/especialidades/form.php\');"));

  ');

  }



  if(_cax(40)) {

  print('

  addMenuItem(new menuItem("Profesionales", "", "javascript:cambiar_pagina(\'administracion/medicos/form.php\');"));

  ');

  }



  if(_cax(37)) {

  print('

  addMenuItem(new menuItem("Patolog�as AUGE", "", "javascript:cambiar_pagina(\'administracion/patologias_auge/form.php\');"));

  ');

  }



  if(_cax(75)) {

  print('

  addMenuItem(new menuItem("Garant�as de Atenci�n", "", "javascript:cambiar_pagina(\'administracion/garantias_atencion/form.php\');"));

  ');

  }



  if(_cax(110)) {

  print('

  addMenuItem(new menuItem("T�cnicos", "", "javascript:cambiar_pagina(\'administracion/tecnicos/form.php\');"));

  addMenuItem(new menuItem("Equipos Electrom�dicos", "", "javascript:cambiar_pagina(\'administracion/clasificacion_equipos/form.php\');"));

  ');

  }


  if(_cax(303)) {

  print('

  addMenuItem(new menuItem("Mantenci�n Mod. Pabell�n", "", "javascript:cambiar_pagina(\'administracion/mantenedor_pabellon/form.php\');"));

  ');

  }
  if(_cax(304)) {

  print('

  addMenuItem(new menuItem("Mantenci�n Espec./Servicios", "", "javascript:cambiar_pagina(\'administracion/mantenedor_especialidades/form.php\');"));

  ');

  }


  if(_cax(12) OR _cax(13) OR _cax(14) OR _cax(15) OR _cax(16) OR _cax(75)

   OR _cax(17) OR _cax(20) OR _cax(37) OR _cax(39) OR _cax(40) OR _cax(110) 
   
   OR _cax(303) OR _cax(304) OR _cax(502) OR _cax(503)) {

  print('

  }

  ');

  }





?>









  /*



  menuagenda = new jsDOMenu(180, "fixed");

  with (menuagenda) {

    addMenuItem(new menuItem("Asignar Horas", "", "blank.htm"));

    addMenuItem(new menuItem("Agendar Horas", "", "blank.htm"));

    addMenuItem(new menuItem("-"));

    addMenuItem(new menuItem("Listas de Espera","listaespera"));



  }



  listaespera_sub =  new jsDOMenu(180, "fixed");

  with (listaespera_sub) {

    addMenuItem(new menuItem("Ingreso de Pacientes", "", "blank.htm"));

    addMenuItem(new menuItem("Remoci�n de Pacientes", "", "blank.htm"));

    addMenuItem(new menuItem("Traslados", "", "blank.htm"));

    addMenuItem(new menuItem("Listar por Especialidad", "", "blank.htm"));



  }



  menuinter = new jsDOMenu(185, "fixed");

  with (menuinter) {

    addMenuItem(new menuItem("Ingresar Paciente", "", "blank.htm"));

    addMenuItem(new menuItem("Estado de Interconsultas", "", "blank.htm"));

    addMenuItem(new menuItem("-"));

    addMenuItem(new menuItem("Evaluaci�n de Solicitudes", "", "blank.htm"));



  }

  */



  menuayuda = new jsDOMenu(130, "fixed");

  with (menuayuda) {

    addMenuItem(new menuItem("Ayuda del Sistema...", "", "blank.htm"));

  }



<?php



 /* if(_cax(30)) {

    print('

      menuestadistica = new jsDOMenu(180, "fixed");

      with (menuestadistica) {

        addMenuItem(new menuItem("Informes Estad�sticos...", "", "javascript:cambiar_pagina(\"estadisticas/form.php\");"));

      }

    ');

  }*/



 //***********************************************************************************************



  if(_cax(30) OR _cax(32) OR _cax(33)) {



  $menu_principal.='addMenuBarItem(new menuBarItem("Estad�sticas", menuestad));';



  print('

  menuestad = new jsDOMenu(190, "fixed");

  with (menuestad) {

  ');



  }



  if(_cax(30)) {

  print('

  addMenuItem(new menuItem("Informes Abastecimiento", "", "javascript:cambiar_pagina(\"estadisticas/estadisticas_abastecimiento/form.php\");"));

  ');

  }



  if(_cax(32)) {

  print('

   addMenuItem(new menuItem("Informes Farmacia", "", "javascript:cambiar_pagina(\"estadisticas/estadisticas_farmacia/form.php\");"));

  ');

  }



  if(_cax(33)) {

  print('

        addMenuItem(new menuItem("Informes Generales", "", "javascript:cambiar_pagina(\"estadisticas/estadisticas_generales/form.php\");"));

  ');

  }



   if(_cax(30) or _cax(32) or _cax(33)) {



  print('

  }

  ');



  }





 //***********************************************************************************************



  /* if(_cax(30)) {

    print('

      menuestadistica = new jsDOMenu(190, "fixed");

      with (menuestadistica) {

        addMenuItem(new menuItem("Informes Abastecimiento", "", "javascript:cambiar_pagina(\"estadisticas/estadisticas_abastecimiento/form.php\");"));

        addMenuItem(new menuItem("Informes Farmacia", "", "javascript:cambiar_pagina(\"estadisticas/estadisticas_farmacia/form.php\");"));

        addMenuItem(new menuItem("Informes Generales", "", "javascript:cambiar_pagina(\"estadisticas/estadisticas_generales/form.php\");"));

      }

    ');

  } */



?>



 //menuagenda.items.listaespera.setSubMenu(listaespera_sub);



  menusys = new jsDOMenuBar("fixed", "menusys", false, "jsdomenubardiv");

  menusys.hide();

  with (menusys) {

    moveTo(0, 50);

    addMenuBarItem(new menuBarItem("Sesi�n de Usuario", menusesion, "Musuarios"));

    

    <?php print($menu_principal); ?>



    addMenuBarItem(new menuBarItem("Ayuda", menuayuda, "Mayuda"));

    show();

  }



}



function resize_menu() {

  menusys.width=screen.availWidth-22;

}


