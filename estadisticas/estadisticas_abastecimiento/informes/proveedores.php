<?php

      /*
   Nombre Informe: Proveedores Asociados HSMQ
   Lista todos los proveedores asociados al hospital, entregando datos
   como rut, nombre,dirección y correo electronico
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */

   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

    $query=
    "
      SELECT count(*) as conta,
             prov_rut,
             prov_glosa,
             prov_direccion,
             prov_fono,
             prov_mail
      FROM proveedor
      GROUP BY prov_rut,prov_glosa,prov_direccion,prov_fono,prov_mail
      ORDER BY prov_glosa;
      ";

    $formato=Array(
                Array('prov_rut',        'RUT Prov.',        0, 'left'),
                Array('prov_glosa',      'Nombre Prov.',     0, 'left'),
                Array('prov_direccion',  'Direcci&oacute;n', 0, 'left'),
                Array('prov_fono',       'Telefono',         0, 'left'),
                Array('prov_mail',       'Email',            0, 'right'),
              );

     ejecutar_consulta();
      $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=4>Total De Proveedores:</td>
      <td>'.number_format(contador('conta'),0,',','.').'</td>
      </tr>
    ';

   procesar_formulario('Provedores Asociados HSMQ');

?>
