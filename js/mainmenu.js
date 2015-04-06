// Javascript
// Menú Principal de la Aplicación
// Rodrigo Carvajal J. (rcarvajal@scv.cl)

function createjsDOMenu() {
  menusesion = new jsDOMenu(160, "fixed");
  with (menusesion) {
    addMenuItem(new menuItem("Cerrar Sesión...", "", "blank.htm"));
  }
  

  menufarmaciabod = new jsDOMenu(210, "fixed");
  with (menufarmaciabod) {
    addMenuItem(new menuItem("Ingreso/Edición de Artículos", "", "javascript:cambiar_pagina('abastecimiento/ingreso_articulos/form.php');"));
    addMenuItem(new menuItem("Recepción de Artículos", "", "javascript:cambiar_pagina('abastecimiento/recepcion_articulos/form.php');"));
    addMenuItem(new menuItem("Movimiento de Artículos", "", "javascript:cambiar_pagina('abastecimiento/movimiento_articulos/form.php');"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Bincard de Artículos", "", "javascript:cambiar_pagina('abastecimiento/bincard_articulos/form.php');"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Stock Crítico/Pedido", "", "javascript:cambiar_pagina('abastecimiento/stock_articulos/form.php');"));
    addMenuItem(new menuItem("Valorización de Artículos", "", "javascript:cambiar_pagina('abastecimiento/valorizacion_articulos/form.php');"));
   // addMenuItem(new menuItem("Libro de Controlados", "", "javascript:cambiar_pagina('abastecimiento/libro_controlados/form.php');"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Pedido de Artículos", "pedidoarts"));
  }

  pedidoarts_sub = new jsDOMenu(180, "fixed");
  with (pedidoarts_sub) {
    addMenuItem(new menuItem("Lista de Pedido", "", "javascript:cambiar_pagina('abastecimiento/pedido_articulos/form.php');"));
    addMenuItem(new menuItem("Órdenes de Compra", "", "javascript:cambiar_pagina('abastecimiento/orden_compra/form.php');"));
    addMenuItem(new menuItem("Recepción de Artículos", "", "javascript:cambiar_pagina('abastecimiento/recepcion_pedido/form.php');"));
  }

  menurecetas = new jsDOMenu(200, "fixed");
  with (menurecetas) {
    addMenuItem(new menuItem("Entrega de Medicamentos", "", "javascript:cambiar_pagina('recetas/entregar_recetas/form.php');"));
  }
  
  menuficha = new jsDOMenu(180, "fixed");
  with (menuficha) {
    addMenuItem(new menuItem("Ficha Básica", "", "javascript:cambiar_pagina('ficha_clinica/ficha_basica/form.php');"));
    //addMenuItem(new menuItem("Ficha Clínica", "", "blank.htm"));
  }

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
    addMenuItem(new menuItem("Remoción de Pacientes", "", "blank.htm"));
    addMenuItem(new menuItem("Traslados", "", "blank.htm"));
    addMenuItem(new menuItem("Listar por Especialidad", "", "blank.htm"));
  
  }
  
  menuinter = new jsDOMenu(185, "fixed");
  with (menuinter) {
    addMenuItem(new menuItem("Ingresar Paciente", "", "blank.htm"));
    addMenuItem(new menuItem("Estado de Interconsultas", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Evaluación de Solicitudes", "", "blank.htm"));
  
  }
  */
  
  menuadmin = new jsDOMenu(180, "fixed");
  with (menuadmin) {
    addMenuItem(new menuItem("Ubicaciones", "", "javascript:cambiar_pagina('administracion/ubicaciones/form.php');"));
    // addMenuItem(new menuItem("Sitios de Atención", "", "blank.htm"));
    addMenuItem(new menuItem("Items Presupuestarios", "", "javascript:cambiar_pagina('administracion/items_presupuestarios/form.php');"));
    addMenuItem(new menuItem("Departamentos", "", "javascript:cambiar_pagina('administracion/departamentos/principal.php');"));
    addMenuItem(new menuItem("Centros de Responsabilidad", "", "javascript:cambiar_pagina('administracion/centros_responsabilidad/form.php');"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Proveedores", "", "javascript:cambiar_pagina('administracion/proveedores/form.php');"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Funcionarios", "", "javascript:cambiar_pagina('administracion/funcionarios/form.php');"));

  }

  menuestad = new jsDOMenu(180, "fixed");
  with (menuestad) {
    addMenuItem(new menuItem("Informes Abastecimiento", "", "javascript:cambiar_pagina('estadisticas/estadisticas_abastecimiento/form.php');"));
    addMenuItem(new menuItem("Informes Farmacia", "", "javascript:cambiar_pagina('estadisticas/estadisticas_farmacia/form.php');"));
    addMenuItem(new menuItem("Informes Generales", "", "javascript:cambiar_pagina('estadisticas/estadisticas_generales/form.php');"));


  }

  menuayuda = new jsDOMenu(130, "fixed");
  with (menuayuda) {
    addMenuItem(new menuItem("Ayuda del Sistema...", "", "blank.htm"));
  }  
  
 menufarmaciabod.items.pedidoarts.setSubMenu(pedidoarts_sub);
 //menuagenda.items.listaespera.setSubMenu(listaespera_sub);
  
  menusys = new jsDOMenuBar("fixed", "menusys", false, "jsdomenubardiv");
  menusys.hide();
  with (menusys) {
    moveTo(0, 50);
    addMenuBarItem(new menuBarItem("Sesión de Usuario", menusesion, "Musuarios"));
    addMenuBarItem(new menuBarItem("Farmacia/Bodega", menufarmaciabod));
    addMenuBarItem(new menuBarItem("Recetas", menurecetas));
    addMenuBarItem(new menuBarItem("Ficha Clínica", menuficha));
    //addMenuBarItem(new menuBarItem("Agenda Médica", menuagenda));
    //addMenuBarItem(new menuBarItem("Interconsultas", menuinter));
    addMenuBarItem(new menuBarItem("Administración", menuadmin, "Madmin"));
    addMenuBarItem(new menuBarItem("Estadísticas", menuestad));
    addMenuBarItem(new menuBarItem("Ayuda", menuayuda, "Mayuda"));
    show();
  }

}

function resize_menu() {
  menusys.width=screen.availWidth-22;
}

