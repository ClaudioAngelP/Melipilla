<?php 

require_once('../../config.php');
require_once('../sigh.php');
require_once('simplehtmldom/simple_html_dom.php');

?>

<table>

<?php

$e='               
               <option value="10016">Alimentación&nbsp13-01-00</option> 
             
               <option value="10022">Anatomía Patológica&nbsp13-04-00</option> 
             
               <option value="10030">Angiografía&nbsp13-05-05</option> 
             
               <option value="10054">Audiometría&nbsp13-14-09</option> 
             
               <option value="10021">Banco de Sangre&nbsp13-03-03</option> 
             
               <option value="10013">Bodega de Leche&nbsp13-26-01</option> 
             
               <option value="10036">Braquiterapia&nbsp13-06-02</option> 
             
               <option value="10003">Cámara Hiperbárica&nbsp13-17-00</option> 
             
               <option value="10024">Citología&nbsp13-04-02</option> 
             
               <option value="10006">Coordinación de Ambulancias&nbsp13-20-01</option> 
             
               <option value="10023">Depósito de Cadáveres(Necropsia)&nbsp13-04-01</option> 
             
               <option value="10041">Diálisis&nbsp13-09-00</option> 
             
               <option value="10032">Eco Dopler&nbsp13-05-07</option> 
             
               <option value="10034">Ecocardiografía&nbsp13-05-09</option> 
             
               <option value="10031">Ecografía&nbsp13-05-06</option> 
             
               <option value="10033">Ecotomografía&nbsp13-05-08</option> 
             
               <option value="10052">Electroencéfalograma(E.E.G)&nbsp13-14-07</option> 
             
               <option value="10048">Elwctrocardiograma(E.C.G)&nbsp13-14-02</option> 
             
               <option value="10049">Endoscopía&nbsp13-14-03</option> 
             
               <option value="10012">Entrega de Leche&nbsp13-26-00</option> 
             
               <option value="10053">Espirometría&nbsp13-14-08</option> 
             
               <option value="10011">Esterilización&nbsp13-25-00</option> 
             
               <option value="999999">FALTACODIGO&nbsp999-999</option> 
             
               <option value="10046">Farmacia&nbsp13-13-01</option> 
             
               <option value="10045">Fonoaudiología&nbsp13-12-01</option> 
             
               <option value="10020">Histología&nbsp13-02-02</option> 
             
               <option value="10025">Imagenología y Ultrasonografía&nbsp13-05-00</option> 
             
               <option value="10043">Kinesiología&nbsp13-10-00</option> 
             
               <option value="10018">Laboratorio Clínico&nbsp13-02-00</option> 
             
               <option value="10103">Mamografía&nbsp13-05-10</option> 
             
               <option value="10064">Medicina Nuclear&nbsp13-06-00</option> 
             
               <option value="10063">Medicina Preventiva&nbsp13-18_00</option> 
             
               <option value="10019">Microbiología&nbsp13-02-01</option> 
             
               <option value="10005">Movilización General&nbsp13-20-00</option> 
             
               <option value="10069">Pabellón de Cirugía Menor&nbsp13-15-08</option> 
             
               <option value="10000">Pabellón de Yeso&nbsp13-15-09</option> 
             
               <option value="10060">Pabellón Obstétrico de Urgencia&nbsp13-15-05</option> 
             
               <option value="10059">Pabellón Obstétrico Electivo&nbsp13-15-04</option> 
             
               <option value="10058">Pabellón Oftalmológico&nbsp13-15-03</option> 
             
               <option value="10068">Pabellón Quirúrgico de Cirugía de Urgencia&nbsp13-15-02</option> 
             
               <option value="10067">Pabellón Quirúrgico de Cirugía Electiva&nbsp13-15-01</option> 
             
               <option value="10066">Pabellón Quirúrgico Indiferenciado&nbsp13-15-00</option> 
             
               <option value="10042">Peritoneodiálisis&nbsp13-09-01</option> 
             
               <option value="10047">Procedimientos de Cardiología&nbsp13-14-01</option> 
             
               <option value="10051">Procedimientos Gastroenterológicos&nbsp13-14-06</option> 
             
               <option value="10056">Procedimientos Ginecológicos&nbsp13-14-11</option> 
             
               <option value="10057">Procedimientos Neurológicos&nbsp13-14-12</option> 
             
               <option value="10050">Procedimientos Ortopédicos&nbsp13-14-05</option> 
             
               <option value="10104">Psicología&nbsp13-28-01</option> 
             
               <option value="10040">Quimioterapia&nbsp13-08-00</option> 
             
               <option value="10027">Radiografías Complejas&nbsp13-05-02</option> 
             
               <option value="10028">Radiografías Dentales&nbsp13-05-03</option> 
             
               <option value="10026">Radiografías Simples&nbsp13-05-01</option> 
             
               <option value="10037">Radioterapia&nbsp13-07-00</option> 
             
               <option value="10038">Radioterapia con Acelerador Lineal&nbsp13-07-01</option> 
             
               <option value="10039">Radioterapia con Telecobaltoterapia&nbsp13-07-02</option> 
             
               <option value="10035">Resonancia Nuclear Magnética&nbsp13-06-01</option> 
             
               <option value="10010">Sala Curación y Tratamientos&nbsp13-24-00</option> 
             
               <option value="10014">Sala de Educación&nbsp13-27-00</option> 
             
               <option value="10061">Sala de Partos&nbsp13-15-06</option> 
             
               <option value="10065">Sala de Procedimientos Indiferenciados&nbsp13-14-00</option> 
             
               <option value="10015">Sala de Psicoterapia&nbsp13-28-00</option> 
             
               <option value="10062">Sala de Recuperación&nbsp13-15-07</option> 
             
               <option value="10002">Sala Enfermedades Respiratorias Agudas(E.R.A)&nbsp13-16-01</option> 
             
               <option value="10001">Sala Infecciones Respiratoria Agudas(I.R.A)&nbsp13-16-00</option> 
             
               <option value="10004">Salud Ocupacional&nbsp13-19-00</option> 
             
               <option value="10007">S.A.M.U&nbsp13-21-00</option> 
             
               <option value="10017">Sedile&nbsp13-01-01</option> 
             
               <option value="10008">Servicios Generales&nbsp13-22-00</option> 
             
               <option value="10044">Terapia Ocupacional&nbsp13-11-00</option> 
             
               <option value="10055">Test de Esfuerzo&nbsp13-14-10</option> 
             
               <option value="10029" selected="selected">Tomografía Axial Computarizada&nbsp13-05-04</option> 
             
               <option value="10009">Vacunatorio&nbsp13-23-00</option>';
                      
$esp=str_get_html($e);

$r=$esp->find('option');

for($i=0;$i<sizeof($r);$i++) {

	$texto=$r[$i]->find('text');
	$texto=$texto[0];
	
	$partes=explode('&nbsp',$texto);

	print('<tr>
	<td>'.$r[$i]->attr['value'].'</td>
	<td>'.$partes[0].'</td>
	<td>\''.$partes[1].'</td>	
	</tr>');
	
	pg_query("INSERT INTO especialidades VALUES (
		DEFAULT,
		'".$partes[0]."',
		".$r[$i]->attr['value'].",
		-1,
		'".$partes[1]."', ''	
	);");
	
}

exit();

?>

</table>

<table>

<?php 

$e='<select name="campo_5204" disabled="disabled" class="htmltextColeccion"><option value="" class="htmltextColeccion">(Seleccione un Establecimiento)</option>           
           <option value="415" class="htmltextColeccion">Alicahue, Posta</option>  <option value="416" class="htmltextColeccion">Artificio (Cabildo), Posta</option>  <option value="446" class="htmltextColeccion">Artificio, Consultorio</option>  <option value="417" class="htmltextColeccion">Aviador  Acevedo, Posta</option>  <option value="447" class="htmltextColeccion">Boco, Consultorio</option>  <option value="418" class="htmltextColeccion">Catapilco, Posta</option>  <option value="2281" class="htmltextColeccion">Centro Comunitario de Salud Familiar Cardenal Raúl Silva Henriquez</option>  <option value="2749" class="htmltextColeccion">Centro Comunitario de Salud Familiar Ex Asentamiento El Melón</option>  <option value="2282" class="htmltextColeccion">Centro Comunitario de Salud Familiar Villa Valparaíso</option>  <option value="449" class="htmltextColeccion">Centro de Salud Cardenal Raúl Silva Henríquez de Quillota</option>  <option value="2596" class="htmltextColeccion">Centro de Salud Familiar Alcalde Iván Manríquez</option>  <option value="2597" class="htmltextColeccion">Centro de Salud Familiar Juan Bautista Bravo Vega</option>  <option value="2598" class="htmltextColeccion">Centro de Salud Familiar Reñaca Alto Dr. Jorge Kaplan</option>  <option value="1000002203" class="htmltextColeccion">Centro de Salud Mental y Psiquiatría Comunitaria Concón</option>  <option value="2147" class="htmltextColeccion">Chincolco, Consultorio</option>  <option value="451" class="htmltextColeccion">Cienfuegos, Consultorio   Miguel Concha Dr., Consultorio</option>  <option value="2614" class="htmltextColeccion">Clínica Dental Móvil, Vina del Mar Quillota</option>  <option value="419" class="htmltextColeccion">Colliguay, Posta</option>  <option value="452" class="htmltextColeccion">Concón, Consultorio  de (con SAPU)</option>  <option value="457" class="htmltextColeccion">Consultorio Brígida Zavala</option>  <option value="461" class="htmltextColeccion">Consultorio Dr. J.C. Baeza</option>  <option value="471" class="htmltextColeccion">Consultorio Dr. Miguel Concha</option>  <option value="469" class="htmltextColeccion">Consultorio Marco Maldonado</option>  <option value="483" class="htmltextColeccion">Consultorio Santa Julia Posta Urbana</option>  <option value="453" selected="selected" class="htmltextColeccion">Dr. Gustavo Fricke de  Viña del Mar, Hospital</option>  <option value="462" class="htmltextColeccion">Dr. Mario Sánchez Vergara de La Calera, Hospital</option>  <option value="448" class="htmltextColeccion">Dr. Víctor Hugo Moll de Cabildo, Hospital</option>  <option value="454" class="htmltextColeccion">Eduardo Frei, Consultorio (con Sapu)</option>  <option value="455" class="htmltextColeccion">El Belloto, Consultorio</option>  <option value="456" class="htmltextColeccion">El Melón, Consultorio</option>  <option value="458" class="htmltextColeccion">Gómez Carreño, Consultorio</option>  <option value="459" class="htmltextColeccion">H. Centro Geriátrico Paz De La Tarde</option>  <option value="420" class="htmltextColeccion">Hierro Viejo, Posta</option>  <option value="460" class="htmltextColeccion">Hijuelas, Consultorio de</option>  <option value="2148" class="htmltextColeccion">Horcón, Posta (Horcón)</option>  <option value="481" class="htmltextColeccion">Hospital Adriana Cousiño</option>  <option value="475" class="htmltextColeccion">Hospital Juana Ross de Edwards (Peñablanca)</option>  <option value="464" class="htmltextColeccion">Hospital San Agustín</option>  <option value="467" class="htmltextColeccion">Hospital Santo Tomás</option>  <option value="421" class="htmltextColeccion">Huaquén, Posta</option>  <option value="422" class="htmltextColeccion">La Canela, Posta</option>  <option value="463" class="htmltextColeccion">La Cruz, Consultorio de</option>  <option value="465" class="htmltextColeccion">La Palma, Consultorio</option>  <option value="423" class="htmltextColeccion">La Vega, Posta</option>  <option value="427" class="htmltextColeccion">Las Parcelas, Posta</option>  <option value="466" class="htmltextColeccion">Las Torres, Consultorio</option>  <option value="428" class="htmltextColeccion">Los Molles, Posta</option>  <option value="468" class="htmltextColeccion">Lusitania, Consultorio</option>  <option value="429" class="htmltextColeccion">Maitencillo, Posta</option>  <option value="430" class="htmltextColeccion">Manzanar, Posta</option>  <option value="470" class="htmltextColeccion">Miraflores, Consultorio  (con SAPU)</option>  <option value="2128" class="htmltextColeccion">Modulo Odontologico Simón Bolivar</option>  <option value="472" class="htmltextColeccion">Nogales, Consultorio de</option>  <option value="473" class="htmltextColeccion">Nueva Aurora, Consultorio ( con SAPU)</option>  <option value="474" class="htmltextColeccion">Olmué, Consultorio de</option>  <option value="431" class="htmltextColeccion">Pachacama, Posta</option>  <option value="433" class="htmltextColeccion">Papudo, Posta</option>  <option value="434" class="htmltextColeccion">Pedegua, Posta</option>  <option value="476" class="htmltextColeccion">Petorca,  Hospital de</option>  <option value="435" class="htmltextColeccion">Pichicuy, Posta</option>  <option value="436" class="htmltextColeccion">Pompeya, Posta</option>  <option value="424" class="htmltextColeccion">Posta de Salud Rural La Viña</option>  <option value="425" class="htmltextColeccion">Posta de Salud Rural Las Palmas</option>  <option value="426" class="htmltextColeccion">Posta de Salud Rural Las Puertas</option>  <option value="432" class="htmltextColeccion">Posta de Salud Rural Pachacamita</option>  <option value="444" class="htmltextColeccion">Posta de Salud Rural Villa Prat</option>  <option value="477" class="htmltextColeccion">Puchuncaví, Consultorio de</option>  <option value="437" class="htmltextColeccion">Pueblo de Roco, Posta</option>  <option value="438" class="htmltextColeccion">Pueblo de Varas, Posta</option>  <option value="439" class="htmltextColeccion">Pullally, Posta</option>  <option value="440" class="htmltextColeccion">Quebrada Alvarado, Posta</option>  <option value="479" class="htmltextColeccion">Quilpué, Consultorio</option>  <option value="480" class="htmltextColeccion">Quilpué, Hospital de</option>  <option value="2149" class="htmltextColeccion">Reñaca, Posta</option>  <option value="441" class="htmltextColeccion">Romeral, Posta</option>  <option value="478" class="htmltextColeccion">San Martín de Quillota, Hospital</option>  <option value="482" class="htmltextColeccion">San Pedro, Consultorio</option>  <option value="442" class="htmltextColeccion">Santa Marta, Posta</option>  <option value="2288" class="htmltextColeccion">SAPU-Artificio</option>  <option value="2285" class="htmltextColeccion">SAPU-Concón</option>  <option value="2289" class="htmltextColeccion">SAPU-Eduardo Frei</option>  <option value="2286" class="htmltextColeccion">SAPU-El Belloto</option>  <option value="2284" class="htmltextColeccion">SAPU-Miraflores</option>  <option value="2283" class="htmltextColeccion">SAPU-Nueva Aurora</option>  <option value="2287" class="htmltextColeccion">SAPU-Ventanas</option>  <option value="443" class="htmltextColeccion">Trapiche, Posta</option>  <option value="484" class="htmltextColeccion">Ventanas, Consultorio</option>  <option value="485" class="htmltextColeccion">Villa Alemana, Consultorio</option>  <option value="445" class="htmltextColeccion">Zapallar, Posta</option></select>';

$est=str_get_html($e);

$r=$est->find('option');

pg_query('truncate table instituciones;');

for($i=1;$i<sizeof($r);$i++) {

	$texto=$r[$i]->find('text');
	$texto=$texto[0];
	
	print('<tr>
	<td>'.$r[$i]->attr['value'].'</td>
	<td>'.$texto.'</td>
	</tr>');
	
	pg_query("insert into instituciones values (
			$i, '$texto', 0, '', '".($r[$i]->attr['value'])."' 	
	);");
	
}


?>

</table>

<table>

<?php 

$c='
				<option value="737">Alto Hospicio</option>
				<option value="386">Arica</option>
	         <option value="387">Camarones</option>
	         <option value="381">Camiña</option>
	         <option value="382">Colchane</option>
	         <option value="723">Desconocida</option>
	         <option value="389">General Lagos</option>
	         <option value="383">Huara</option>
	         <option value="380">Iquique</option>
	         <option value="384">Pica</option>
	         <option value="385">Pozo Almonte</option>
	         <option value="388">Putre</option>     
	         <option value="390">Antofagasta</option>
	         <option value="394">Calama</option>
	         <option value="724">Desconocida</option>
	         <option value="398">María Elena</option>
	         <option value="391">Mejillones</option>
	         <option value="395">Ollagüe</option>
	         <option value="396">San Pedro de Atacama</option>
	         <option value="392">Sierra Gorda</option>
	         <option value="393">Taltal</option>
	         <option value="397">Tocopilla</option>
  		       <option value="405">Alto del Carmen</option>
	         <option value="400">Caldera</option>
	         <option value="402">Chañaral</option>
	         <option value="399">Copiapó</option>
	         <option value="725">Desconocida</option>
	         <option value="403">Diego de Almagro</option>
	         <option value="406">Freirina</option>
	         <option value="407">Huasco</option>
	         <option value="401">Tierra Amarilla</option>
	         <option value="404">Vallenar</option>
    	     <option value="410">Andacollo</option>
	         <option value="415">Canela</option>
	         <option value="419">Combarbalá</option>
	         <option value="409">Coquimbo</option>
	         <option value="726">Desconocida</option>
	         <option value="414">Illapel</option>
	         <option value="411">La Higuera</option>
	         <option value="408">La Serena</option>
	         <option value="416">Los Vilos</option>
	         <option value="420">Monte Patria</option>
	         <option value="418">Ovalle</option>
	         <option value="412">Paiguano</option>
	         <option value="421">Punitaqui</option>
	         <option value="422">Río Hurtado</option>
	         <option value="417">Salamanca</option>
	         <option value="413">Vicuña</option>
         <option value="450">Algarrobo</option>
         <option value="438">Cabildo</option>
         <option value="443">Calera</option>
         <option value="434">Calle Larga</option>
         <option value="451">Cartagena</option>
         <option value="424">Casablanca</option>
         <option value="456">Catemu</option>
         <option value="425">Concón</option>
         <option value="727">Desconocida</option>
         <option value="452">El Quisco</option>
         <option value="453">El Tabo</option>
         <option value="444">Hijuelas</option>
         <option value="432">Isla  de Pascua</option>
         <option value="426">Juan Fernández</option>
 	         <option value="445">La Cruz</option>
	         <option value="437">La Ligua</option>
	         <option value="446">Limache</option>
	         <option value="457">Llay-Llay</option>
	         <option value="433">Los Andes</option>
	         <option value="447">Nogales</option>
	         <option value="448">Olmué</option>
	         <option value="458">Panquehue</option>
	         <option value="439">Papudo</option>
	         <option value="440">Petorca</option>
	         <option value="427">Puchuncaví</option>
	         <option value="459">Putaendo</option>
	         <option value="442">Quillota</option>
	         <option value="428">Quilpué</option>
	         <option value="429">Quintero</option>
	         <option value="435">Rinconada</option>
	         <option value="449">San Antonio</option>
	         <option value="436">San Esteban</option>
	         <option value="455">San Felipe</option>
	         <option value="460">Santa María</option>
	         <option value="454">Santo Domingo</option>
	         <option value="423">Valparaíso</option>
	         <option value="430">Villa Alemana</option>
	         <option value="431">Viña del Mar</option>
	         <option value="441">Zapallar</option>

         <option value="485">Chépica</option>
         <option value="486">Chimbarongo</option>
         <option value="462">Codegua</option>
         <option value="463">Coinco</option>
         <option value="464">Coltauco</option>
         <option value="728">Desconocida</option>
         <option value="465">Doñihue</option>
         <option value="466">Graneros</option>
         <option value="479">La Estrella</option>
         <option value="467">Las Cabras</option>
         <option value="480">Litueche</option>
         <option value="487">Lolol</option>
         <option value="468">Machalí</option>
         <option value="469">Malloa</option>
	         <option value="481">Marchihue</option>
	         <option value="470">Mostazal</option>
	         <option value="488">Nancagua</option>
	         <option value="482">Navidad</option>
	         <option value="471">Olivar</option>
	         <option value="489">Palmilla</option>
	         <option value="483">Paredones</option>
	         <option value="490">Peralillo</option>
	         <option value="472">Peumo</option>
	         <option value="473">Pichidegua</option>
	         <option value="478">Pichilemu</option>
	         <option value="491">Placilla</option>
	         <option value="492">Pumanque</option>
	         <option value="474">Quinta de Tilcoco</option>
	         <option value="461">Rancagua</option>
	         <option value="475">Rengo</option>
	         <option value="476">Requínoa</option>
	         <option value="484">San Fernando</option>
	         <option value="477">San Vicente</option>
	         <option value="493">Santa Cruz</option>

         <option value="504">Cauquenes</option>
	         <option value="505">Chanco</option>
	         <option value="517">Colbún</option>
	         <option value="495">Constitución</option>
	         <option value="496">Curepto</option>
	         <option value="507">Curicó</option>
	         <option value="729">Desconocida</option>
	         <option value="497">Empedrado</option>
	         <option value="508">Hualañé</option>
	         <option value="509">Licantén</option>
	         <option value="516">Linares</option>
	         <option value="518">Longaví</option>
	         <option value="498">Maule</option>
	         <option value="510">Molina</option>
	         <option value="519">Parral</option>
	         <option value="499">Pelarco</option>
	         <option value="506">Pelluhue</option>
	         <option value="500">Pencahue</option>
	         <option value="511">Rauco</option>
	         <option value="520">Retiro</option>
	         <option value="501">Río Claro</option>
	         <option value="512">Romeral</option>
	         <option value="513">Sagrada Familia</option>
	         <option value="502">San Clemente</option>
	         <option value="521">San Javier</option>
	         <option value="503">San Rafael</option>
	         <option value="494">Talca</option>
	         <option value="514">Teno</option>
	         <option value="515">Vichuquén</option>
	         <option value="522">Villa Alegre</option>
	         <option value="523">Yerbas Buenas</option>

         <option value="738">Alto Biobío</option>
         <option value="543">Antuco</option>
         <option value="536">Arauco</option>
         <option value="556">Bulnes</option>
         <option value="544">Cabrero</option>
         <option value="537">Cañete</option>
         <option value="527">Chiguayante</option>
         <option value="555">Chillán</option>
         <option value="560">Chillán Viejo</option>
         <option value="557">Cobquecura</option>
         <option value="558">Coelemu</option>
         <option value="559">Coihueco</option>
         <option value="524">Concepción</option>
         <option value="538">Contulmo</option>
	         <option value="525">Coronel</option>
         <option value="539">Curanilahue</option>
         <option value="730">Desconocida</option>
         <option value="561">El Carmen</option>
         <option value="528">Florida</option>
         <option value="736">Hualpén</option>
         <option value="529">Hualqui</option>
         <option value="545">Laja</option>
         <option value="535">Lebu</option>
         <option value="540">Los Alamos</option>
         <option value="542">Los Angeles</option>
         <option value="530">Lota</option>
         <option value="546">Mulchén</option>
         <option value="547">Nacimiento</option>
         <option value="548">Negrete</option>
         <option value="562">Ninhue</option>
         <option value="563">Ñiquén</option>
         <option value="564">Pemuco</option>
         <option value="531">Penco</option>
         <option value="565">Pinto</option>
         <option value="566">Portezuelo</option>
         <option value="549">Quilaco</option>
         <option value="550">Quilleco</option>
         <option value="567">Quillón</option>
         <option value="568">Quirihue</option>
	         <option value="569">Ránquil</option>
	         <option value="570">San Carlos</option>
	         <option value="571">San Fabián</option>
	         <option value="572">San Ignacio</option>
	         <option value="573">San Nicolás</option>
	         <option value="526">San Pedro de la Paz</option>
	         <option value="551">San Rosendo</option>
	         <option value="552">Santa Bárbara</option>
	         <option value="532">Santa Juana</option>
	         <option value="533">Talcahuano</option>
	         <option value="541">Tirúa</option>
	         <option value="534">Tomé</option>
	         <option value="574">Treguaco</option>
	         <option value="553">Tucapel</option>
	         <option value="554">Yumbel</option>
	         <option value="575">Yungay</option>

         <option value="596">Angol</option>
         <option value="577">Carahue</option>
         <option value="739">Cholchol</option>
         <option value="597">Collipulli</option>
         <option value="578">Cunco</option>
         <option value="598">Curacautín</option>
         <option value="579">Curarrehue</option>
         <option value="731">Desconocida</option>
         <option value="599">Ercilla</option>
         <option value="580">Freire</option>
         <option value="581">Galvarino</option>
         <option value="582">Gorbea</option>
         <option value="583">Lautaro</option>
         <option value="584">Loncoche</option>
	         <option value="600">Lonquimay</option>
	         <option value="601">Los Sauces</option>
	         <option value="602">Lumaco</option>
	         <option value="585">Melipeuco</option>
	         <option value="586">Nueva Imperial</option>
	         <option value="587">Padre Las Casas</option>
	         <option value="588">Perquenco</option>
	         <option value="589">Pitrufquén</option>
	         <option value="590">Pucón</option>
	         <option value="603">Purén</option>
	         <option value="604">Renaico</option>
	         <option value="591">Saavedra</option>
	         <option value="576">Temuco</option>
	         <option value="592">Teodoro Schmidt</option>
	         <option value="593">Toltén</option>
	         <option value="605">Traiguén</option>
	         <option value="606">Victoria</option>
	         <option value="594">Vilcún</option>
	         <option value="595">Villarrica</option>


         <option value="617">Ancud</option>
         <option value="608">Calbuco</option>
         <option value="616">Castro</option>
         <option value="633">Chaitén</option>
         <option value="618">Chonchi</option>
         <option value="609">Cochamó</option>
         <option value="638">Corral</option>
         <option value="619">Curaco de Vélez</option>
         <option value="620">Dalcahue</option>
         <option value="732">Desconocida</option>
         <option value="610">Fresia</option>
         <option value="611">Frutillar</option>
         <option value="634">Futaleufú</option>
         <option value="639">Futrono</option>
	         <option value="635">Huailaihué</option>
	         <option value="640">La Unión</option>
	         <option value="641">Lago Ranco</option>
	         <option value="642">Lanco</option>
	         <option value="613">Llanquihue</option>
	         <option value="643">Los Lagos</option>
	         <option value="612">Los Muermos</option>
	         <option value="644">Máfil</option>
	         <option value="645">Mariquina</option>
	         <option value="614">Maullín</option>
	         <option value="626">Osorno</option>
	         <option value="646">Paillaco</option>
	         <option value="636">Palena</option>
	         <option value="647">Panguipulli</option>
	         <option value="607">Puerto Montt</option>
	         <option value="627">Puerto Octay</option>
	         <option value="615">Puerto Varas</option>
	         <option value="621">Puqueldón</option>
	         <option value="628">Purranque</option>
	         <option value="629">Puyehue</option>
	         <option value="622">Queilén</option>
	         <option value="623">Quellón</option>
	         <option value="624">Quemchi</option>
	         <option value="625">Quinchao</option>
	         <option value="648">Río Bueno</option>
	         <option value="630">Río Negro</option>
	         <option value="631">San Juan de la Costa</option>
	         <option value="632">San Pablo</option>
	         <option value="637">Valdivia</option>


	         <option value="651">Aisén</option>
	         <option value="657">Chile Chico</option>
	         <option value="654">Cochrane</option>
	         <option value="649">Coyhaique</option>
	         <option value="733">Desconocida</option>
	         <option value="653">Guaitecas</option>
	         <option value="650">Lago Verde</option>
	         <option value="655">OHiggins</option>
	         <option value="652">Puerto Cisnes</option>
	         <option value="658">Río Ibáñez</option>
	         <option value="656">Tortel</option>

        <option value="664">Antártica</option>
	         <option value="663">Cabo de Hornos</option>
	         <option value="734">Desconocida</option>
	         <option value="660">Laguna Blanca</option>
	         <option value="665">Porvenir</option>
	         <option value="666">Primavera</option>
	         <option value="668">Puerto Natales</option>
	         <option value="659">Punta Arenas</option>
	         <option value="661">Río Verde</option>
	         <option value="662">San Gregorio</option>
	         <option value="667">Timaukel</option>
	         <option value="669">Torres del Paine</option>

       <option value="713">Alhué</option>
         <option value="709">Buin</option>
         <option value="710">Calera de Tango</option>
         <option value="671">Cerrillos</option>
         <option value="672">Cerro Navia</option>
         <option value="705">Colina</option>
         <option value="673">Conchalí</option>
         <option value="714">Curacaví</option>
         <option value="735">Desconocida</option>
         <option value="674">El Bosque</option>
         <option value="718">El Monte</option>
         <option value="675">Estación Central</option>
         <option value="676">Huechuraba</option>
         <option value="677">Independencia</option>
	         <option value="719">Isla de Maipo</option>
	         <option value="678">La Cisterna</option>
	         <option value="679">La Florida</option>
	         <option value="680">La Granja</option>
	         <option value="681">La Pintana</option>
	         <option value="682">La Reina</option>
	         <option value="706">Lampa</option>
	         <option value="683">Las Condes</option>
	         <option value="684">Lo Barnechea</option>
	         <option value="685">Lo Espejo</option>
	         <option value="686">Lo Prado</option>
	         <option value="687">Macul</option>
	         <option value="688">Maipú</option>
	         <option value="715">María Pinto</option>
	         <option value="712">Melipilla</option>
	         <option value="689">Ñuñoa</option>
	         <option value="720">Padre Hurtado</option>
	         <option value="711">Paine</option>
	         <option value="690">Pedro  Aguirre Cerda</option>
	         <option value="721">Peñaflor</option>
	         <option value="691">Peñalolén</option>
	         <option value="703">Pirque</option>
	         <option value="692">Providencia</option>
	         <option value="693">Pudahuel</option>
	         <option value="702">Puente Alto</option>
	         <option value="694">Quilicura</option>
	         <option value="695">Quinta Normal</option>
	         <option value="696">Recoleta</option>
	         <option value="697">Renca</option>
	         <option value="708">San Bernardo</option>
	         <option value="698">San Joaquín</option>
	         <option value="704">San José de Maipo</option>
	         <option value="699">San Miguel</option>
	         <option value="716">San Pedro</option>
	         <option value="700">San Ramón</option>
	         <option value="670">Santiago</option>
	         <option value="717">Talagante</option>
	         <option value="707">Tiltil</option>
         <option value="701">Vitacura</option>

';

$ciud=str_get_html($c);

$r=$ciud->find('option');

for($i=0;$i<sizeof($r);$i++) {

	$texto=$r[$i]->find('text');
	$texto=$texto[0];
	
	print('<tr>
	<td>'.$r[$i]->attr['value'].'</td>
	<td>'.$texto.'</td>
	</tr>');
		
}


?>

</table>