<?php 

	require_once('../../conectar_db.php');

	$id=$_GET['id']*1;
        $fecha1=pg_escape_string($_GET['fecha1']);
        $fecha2=pg_escape_string($_GET['fecha2']);
	
	function trunc($str, $len) {
		if(strlen($str)>$len)
			return substr($str,0,$len).'...';
		else
			return $str;
	}
	
	function graficar2($tabla, $limit) {

		$colores=Array(
			'#00A779',
			'#1F7D63',
			'#006D4F',
			'#35D3A7',
			'#5FD3B3',
			'#0E53A7',
			'#274E7D',
			'#04346C',
			'#4284D3',
			'#6899D3',
			'#FF9C00',
			'#BF8830',
			'#A66500',
			'#FFB540',
			'#FFC973',
			'#FF5C00',
			'#BF6430',
			'#A63C00',
			'#FF8540',
			'#FFA573',
			'#DDDDDD', //'#3515B0'
			'#3F2D84',
			'#1D0772',
			'#6749D7',
			'#856FD7',
			'#6C0AAB',
			'#5D2680',
			'#45036F',
			'#9B3FD5',
			'#AA67D5',
			'#FFFC00',
			'#BFBD30',
			'#A6A400',
			'#FFFD40',
			'#FFFD73',
			'#FFD100',
			'#BFA530',
			'#A68800',
			'#FFDC40',
			'#FFE573',
			'#00A779',
			'#1F7D63',
			'#006D4F',
			'#35D3A7',
			'#5FD3B3',
			'#0E53A7',
			'#274E7D',
			'#04346C',
			'#4284D3',
			'#6899D3',
			'#FF9C00',
			'#BF8830',
			'#A66500',
			'#FFB540',
			'#FFC973',
			'#FF5C00',
			'#BF6430',
			'#A63C00',
			'#FF8540',
			'#FFA573',
			'#DDDDDD', //'#3515B0'
			'#3F2D84',
			'#1D0772',
			'#6749D7',
			'#856FD7',
			'#6C0AAB',
			'#5D2680',
			'#45036F',
			'#9B3FD5',
			'#AA67D5',
			'#FFFC00',
			'#BFBD30',
			'#A6A400',
			'#FFFD40',
			'#FFFD73',
			'#FFD100',
			'#BFA530',
			'#A68800',
			'#FFDC40',
			'#FFE573',
			'#00A779',
			'#1F7D63',
			'#006D4F',
			'#35D3A7',
			'#5FD3B3',
			'#0E53A7',
			'#274E7D',
			'#04346C',
			'#4284D3',
			'#6899D3',
			'#FF9C00',
			'#BF8830',
			'#A66500',
			'#FFB540',
			'#FFC973',
			'#FF5C00',
			'#BF6430',
			'#A63C00',
			'#FF8540',
			'#FFA573',
			'#DDDDDD', //'#3515B0'
			'#3F2D84',
			'#1D0772',
			'#6749D7',
			'#856FD7',
			'#6C0AAB',
			'#5D2680',
			'#45036F',
			'#9B3FD5',
			'#AA67D5',
			'#FFFC00',
			'#BFBD30',
			'#A6A400',
			'#FFFD40',
			'#FFFD73',
			'#FFD100',
			'#BFA530',
			'#A68800',
			'#FFDC40',
			'#FFE573'			
		);	
	
		$total=0;
		
		$tabla2=Array();
		
		if($tabla)
		for($i=0;$i<sizeof($tabla);$i++) {
			$total+=$tabla[$i]['cant']*1;
			if($i<$limit) $tabla2[$i]=$tabla[$i];
			else {
				if(!isset($tabla2[$limit])) {
					$tabla2[$limit]['ruta']='(Otros...)';
					$tabla2[$limit]['cant']=0;
				}
				$tabla2[$limit]['cant']+=$tabla[$i]['cant']*1;
			}
		}
		
		$tabla=$tabla2;
	
		if($total>0) {
			for($i=0;$i<sizeof($tabla);$i++) {
				$tabla[$i]['angle']=($tabla[$i]['cant']*360/$total);
				$tabla[$i]['pcnt']=($tabla[$i]['cant']*100/$total);
			}
		} else {
			$svg='<svg width="1100px" height="250px">';
			$svg.="<circle cx='125' cy='125' r='120' style='stroke:black; stroke-width: 1; fill:gray;' />";
			$svg.='</svg>';
			
			return $svg;
		}
		
		$svg='';
	
		$svg.='<svg width="1100px" height="250px">';
		
		$angulo=180;
		$x=250;
				
		for($i=0;$i<sizeof($tabla);$i++) {
			
			$d_angulo=$tabla[$i]['angle']*1;
			$color=$colores[$i];
			
			$x1=125+(cos(deg2rad($angulo))*120);
			$y1=125+(sin(deg2rad($angulo))*120);
			
			$x2=125+(cos(deg2rad($angulo+$d_angulo))*120);
			$y2=125+(sin(deg2rad($angulo+$d_angulo))*120);
			
			if($d_angulo>180) $la='1'; else $la='0';
			
			if($d_angulo<360) {
				$svg.="<path d='M125,125 L$x1,$y1 A 120,120 0 $la,1 $x2,$y2 z' style='stroke:black; stroke-width: 0.5; fill:$color;' />";
			} else {
				$svg.="<circle cx='120' cy='125' r='125' style='stroke:black; stroke-width: 1; fill:$color;' />";
			}
			
			
			$angulo+=$d_angulo;
			
			$codigo=$tabla[$i]['art_codigo'];
			$glosa=trunc($tabla[$i]['ruta'],30);
			$pcnt=number_format($tabla[$i]['pcnt'],1,',','.').'%';
			$y=15+(($i%12)*20);
			$x=300+((floor($i/12))*400);
			
			$svg.="<rect x='$x' y='$y' width=24 height=12 style='stroke:black; stroke_width: 0.5;' fill='$color' />
			
			<text x='".($x+30)."' y='".($y+10)."' font-weight='bold'
			font-family='Verdana' font-size='14' fill='black' >
				$pcnt
			</text>

			<text x='".($x+90)."' y='".($y+10)."' font-weight='bold'
			font-family='Verdana' font-size='14' fill='green' >
				$glosa
			</text>";
			
			
			
		}
		
		$svg.="<text x='900' y='225' text-anchor='middle'
			font-family='Verdana' font-size='20' fill='black' >
				Cantidad de Acci&oacute;nes:".$total.".-
			</text></svg>";
				
		return $svg;

	
	}
	
	function graficar($codigo, $glosa, $bodega, $forma, $saldos, $precio, $precios) {
		
		$data=explode('|',$saldos);		
		$ymin=0; $ymax=50;
		
		$max=0; $min=0;
		$stock=0;
		
		$data2=array();
		$data3=array();
		$data4=array();
		
		$total=0;
		
		for($n=0;$n<sizeof($data);$n++) {
			
			
			$tmp=explode('#', $data[$n]);
			
			$val=$tmp[0]*1;
			
			if($n==0) { $min=$val; $max=$val; }
			
			$total+=$val;
			
			//$stock+=$val;
			//$data2[$n]=$stock;
			$data2[$n]=$val;
			$data3[$n]=$tmp[1]*1;
			$data4[$n]=date('d/m/Y',$tmp[1]*1);
			
			//if($stock<$min) $min=$stock;
			//if($stock>$max) $max=$stock;
			if($val<$min) $min=$val;
			if($val>$max) $max=$val;
			
		}		
		
		$tmpdif=($max-$min)/10;
		$smin=number_format($min,0,',','.');
		$smax=number_format($max,0,',','.');
	
		$max=$max+$tmpdif;
		$min=$min-$tmpdif;
			
		$dif=$max-$min;
		
		$tsdif=($data3[sizeof($data3)-1]*1)-($data3[0]*1);

		




		
		$datav=explode('|',$precios);
		
		$yminv=0; $ymaxv=50;
		
		$maxv=0; $minv=0;
		$stockv=0;
		
		$datav2=array();
		$datav3=array();
		$datav4=array();
		
		$totalv=0;
		
		for($n=0;$n<sizeof($datav);$n++) {
			
			
			$tmp=explode('#', $datav[$n]);
			
			$val=$tmp[0]*1;
			
			if($n==0) { $minv=$val; $maxv=$val; }
			
			$totalv+=$val;
			
			//$stock+=$val;
			//$data2[$n]=$stock;
			$datav2[$n]=$val;
			$datav3[$n]=$tmp[1]*1;
			$datav4[$n]=date('d/m/Y',$tmp[1]*1);
			
			//if($stock<$min) $min=$stock;
			//if($stock>$max) $max=$stock;
			if($val<$minv) $minv=$val;
			if($val>$maxv) $maxv=$val;
			
		}		
		
		$tmpdifv=($maxv-$minv)/10;
		$sminv=number_format($minv,0,',','.');
		$smaxv=number_format($maxv,0,',','.');
	
		$maxv=$maxv+$tmpdifv;
		$minv=$minv-$tmpdifv;
			
		$difv=$maxv-$minv;
		
		$tsdifv=($datav3[sizeof($datav3)-1]*1)-($datav3[0]*1);
		
		
		
		
		
		
		
		
		
		
		
		
		$svg='';
		
		$svg.='<svg width="700px" height="180px">';
		
		$offsetx=5;
		$offsety=45;

		
		$cnt=sizeof($data);
		
			$svg.="<text x='5' y='22' font-weight='bold' 
			font-family='Verdana' font-size='14' fill='black' >
				$codigo
			</text><text x='5' y='38' font-weight='bold'
			font-family='Verdana' font-size='16' fill='green' >
				$glosa
			</text>
			
			<text x='5' y='".($offsety+135)."' 
			font-family='Verdana' font-size='14' fill='black' >
				".$data4[0]."
			</text>
			
			<text x='370' y='".($offsety+135)."' text-anchor='right'
			font-family='Verdana' font-size='14' fill='black' >
				".$data4[sizeof($data4)-1]."
			</text>
			
			<text x='465' y='".($offsety+115)."' text-anchor='left' font-weight='bold'
			font-family='Verdana' font-size='10' fill='gray' >
				$smin
			</text>

			<text x='465' y='".($offsety+10)."' text-anchor='left' font-weight='bold'
			font-family='Verdana' font-size='10' fill='gray' >
				$smax
			</text>

			
			<text x='600' y='67' text-anchor='middle'  font-weight='bold'
			font-family='Verdana' font-size='16' fill='black' >
				".number_format($total,0,',','.')." ".$forma."
			</text>			
			
			
			
			";

			
		$cnt=0;	
		for($xx=0;$xx<=450;$xx+=22.5) {
			
			if($cnt++%2==0) $color='#BBBBBB'; else $color='#EEEEEE';
			
			$xxx=$xx+$offsetx;
			
			$svg.="<line x1='$xxx' y1='$offsety' x2='$xxx' y2='".($offsety+120)."' style='stroke-width: 0.5; stroke: ".$color.";'/>";
			
		}

		$cnt=0;	
		for($yy=0;$yy<=120;$yy+=12) {
			
			if($cnt++%2==0) $color='#BBBBBB'; else $color='#EEEEEE';
			
			$yyy=$yy+$offsety;
			
			$svg.="<line x1='$offsetx' y1='$yyy' x2='".($offsetx+450)."' y2='".($yyy)."' style='stroke-width: 0.5; stroke: ".$color.";'/>";
			
		}
		
		
		if($cnt>1) {			
			
			$rd=1;
			
			for($i=1;$i<sizeof($data2);$i++) {
			
				$color='blue';
				
				//$x1=$offsetx+((450/($cnt-1))*($i-1));
				//$x2=$offsetx+((450/($cnt-1))*$i);
				if($tsdif>0) {
					$x1=$offsetx+(($data3[$i-1]-$data3[0])*450)/$tsdif;
					$x2=$offsetx+(($data3[$i]-$data3[0])*450)/$tsdif;
				} else {
					$x1=$offsetx+(450/2);
					$x2=$x1;
				}
				
				if($dif>0) {
					$y1=number_format($offsety+(120-((($data2[$i-1]-$min)*120)/$dif)),4,'.','');
					$y2=number_format($offsety+(120-((($data2[$i]-$min)*120)/$dif)),4,'.','');
				} else {
					$y1=number_format($offsety+(120-((($data2[$i-1]-$min)*120))),4,'.','');
					$y2=number_format($offsety+(120-((($data2[$i]-$min)*120))),4,'.','');				
				}
				
				if($data2[$i-1]<0)
					$color='red';

				
				// LINEA RECTA
				//$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y1' style='stroke-width: 2; stroke: ".$color.";'/>";	
				//$svg.="<line x1='$x2' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 2; stroke: ".$color.";'/>";	
				
				// DIAGONAL
				$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 2; stroke: ".$color.";'/>";	
				
				if($data2[$i]<0)
					$color='red';

				//if($i==sizeof($data2)-1) 
					//$rd+=1;
	
				//$svg.="<circle cx='$x2' cy='$y2' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";

				if($i==sizeof($data2)-1) {
					$rd+=1;
	
					$svg.="<circle cx='$x2' cy='$y2' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";
				}

			}

			for($i=1;$i<sizeof($datav2);$i++) {
			
				$color='green';
				
				//$x1=$offsetx+((450/($cnt-1))*($i-1));
				//$x2=$offsetx+((450/($cnt-1))*$i);
				if($tsdif>0) {
					$x1=$offsetx+(($datav3[$i-1]-$data3[0])*450)/$tsdif;
					$x2=$offsetx+(($datav3[$i]-$data3[0])*450)/$tsdif;
				} else {
					$x1=$offsetx+(450/2);
					$x2=$x1;
				}
				
				if($x1>($offsetx+450)) $x1=$offsetx+450;
				if($x2>($offsetx+450)) $x2=$offsetx+450;
				
				
				if($difv>0) {
					$y1=number_format($offsety+(120-((($datav2[$i-1]-$minv)*120)/$difv)),4,'.','');
					$y2=number_format($offsety+(120-((($datav2[$i]-$minv)*120)/$difv)),4,'.','');
				} else {
					$y1=number_format($offsety+(120-((($datav2[$i-1]-$minv)*120))),4,'.','');
					$y2=number_format($offsety+(120-((($datav2[$i]-$minv)*120))),4,'.','');				
				}
				
				if($datav2[$i-1]<0)
					$color='yellowgreen';

				
				// LINEA RECTA
				//$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y1' style='stroke-width: 2; stroke: ".$color.";'/>";	
				//$svg.="<line x1='$x2' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 2; stroke: ".$color.";'/>";	
				
				// DIAGONAL
				$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 2; stroke: ".$color.";'/>";	
				
				//if($datav2[$i]<0)
					//$color='red';

				//if($i==sizeof($data2)-1) 
					//$rd+=1;
	
				if($i==1) {
					$color2="yellowgreen";
					$svg.="<line x1='$offsetx' y1='$y1' x2='$x1' y2='$y1' style='stroke-width: 2; stroke: ".$color2.";'/>";	
					$svg.="<circle cx='$x1' cy='$y1' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";
				}

				$svg.="<circle cx='$x2' cy='$y2' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";

				if($i==sizeof($datav2)-1) {
					$rd+=1;
					$color2="yellowgreen";
					$svg.="<line x1='$x2' y1='$y2' x2='".($offsetx+450)."' y2='$y2' style='stroke-width: 2; stroke: ".$color2.";'/>";		
					$svg.="<circle cx='$x2' cy='$y2' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";
				}

			}

			
		} else {
			
			$x1=250;
			$x2=420;
			$y1=45;
			$y2=45;
			$color='blue';
			
			$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 5; stroke: ".$color.";'/>";	
			$svg.="<circle cx='$x2' cy='$y2' r='10' style='stroke: ".$color."; fill: ".$color.";'/>";
			
		}
		
		$svg.='</svg>';
		
		return $svg;
		
		
	}

	if($bod_id==-2) {
                $bod_id='2,3,4,50,23,16,10';
        }



	$abc=cargar_registros_obj("
		
		SELECT * FROM (
		select COALESCE((la_glosa),'Otros')as ruta,COUNT(*) AS cant ,func_nombre
		FROM logs_acceso
		LEFT JOIN logs_acceso_rutas on la_ruta=ruta 
		JOIN funcionario USING (func_id) 
		WHERE ruta!='/produccion/chat_status.php' AND ruta!='/produccion/autocompletar_sql.php' AND
(CASE  WHEN ruta='/produccion/chat_mensajes.php'  THEN (CASE WHEN http_post !='' THEN TRUE ELSE FALSE END) ELSE TRUE END)
		AND (fecha::date BETWEEN '$fecha1' AND '$fecha2') AND func_id=$id
		GROUP BY  la_glosa,func_nombre) AS foo ORDER BY cant DESC 

	");
	/*
	$query=cargar_registros_obj("

select *,( cant||'#'||date_part('epoch',tiempo)) as saldos from(
		SELECT * FROM (
		select ruta as art_codigo,COALESCE((la_glosa||' / '||la_glosa2),ruta)as ruta,func_nombre as bod_glosa, 'Accion(es)' as forma_nombre,COUNT(*) AS cant,date_trunc('hour', fecha)as tiempo 
		FROM logs_acceso	

		LEFT JOIN logs_acceso_rutas on la_ruta=ruta 
		JOIN funcionario USING (func_id) 
		WHERE ruta!='/produccion/chat_status.php' AND ruta!='/produccion/autocompletar_sql.php' AND
(CASE  WHEN ruta='/produccion/chat_mensajes.php'  THEN (CASE WHEN http_post !='' THEN TRUE ELSE FALSE END) ELSE TRUE END)
		AND (fecha::date BETWEEN '$fecha1' AND '$fecha2') AND func_id=7 
		GROUP BY date_trunc('hour', fecha), func_nombre, art_codigo,ruta,ruta,bod_glosa,forma_nombre) AS foo order by tiempo desc limit 60) as foto
	
	");
	 * */
	 
	
	print("<table style='width:100%;font-family:Verdana;'>");
	print("<tr><td style='text-align:center;'><h2><u>Histograma de Acci&oacute;nes ($fecha1 - $fecha2)</u></h2><h3>".htmlentities($sghinstitucion)."</h3></td></tr>"); //<td>".$query[$i]['saldos']."</td>
	print("<tr><td style='text-align:center;'><h2>".htmlentities($abc[0]['func_nombre'])."</h2></td></tr>"); //<td>".$query[$i]['saldos']."</td>
		
	print("<tr><td><center>".graficar2($abc,20)."</center></td></tr>"); //<td>".$query[$i]['saldos']."</td>
	
	
	
	print("</table>");

?>
