<?php

  require_once('../conectar_db.php');

   pg_query($conn, "START TRANSACTION;");

  $infotalonario = $_GET['cadena_talonarios'];
  $tipo_talonario = ($_GET['tipotalonario']*1);
  $nro_talonarios = ($_GET['num_talonario']*1);  //cantidad de talonarios



  $tals = explode('|', $infotalonario);


  for($i=0;$i<count($tals);$i++) {

   $tal=explode('-', $tals[$i]);
  //   die($tal[1]);
   if(count($tal)==2) {
   //   if(count($tal)==3) {

    // list($estado, $error) = validez_talonario(tipo_talonario($tipo_talonario),
    //                                          $tal[0],$tal[1], $tal[2]);
     list($estado, $error) = validez_talonario(tipo_talonario($tipo_talonario),$tal[0], $tal[1]);

     if(!$estado) die('Error en Talonario Nro. '.($i+1).': '.$error);

    }

  }

  die('OK');


?>
