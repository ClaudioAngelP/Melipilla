<?php 

	require_once('../../conectar_db.php');
	
	$fap_id=$_POST['fap_id']*1;
		
	function trunc($str, $len) {
		if(strlen($str)>$len)
			return substr($str,0,$len).'...';
		else
			return $str;
	}

	$fs=cargar_registros_obj("SELECT * FROM fap_series WHERE fap_id=$fap_id;");
	
	$tmin=0; $hmin='00:00';
	$tmax=0; $hmax='00:00';
	$init_t=false;
		
	for($i=0;$i<sizeof($fs);$i++) {

		if(trim($fs[$i]['fs_datos'])=='') continue;	
		
		$data=explode("\n",$fs[$i]['fs_datos']);
		
		$ymin=0; $ymax=50;
		
		$max=$fs[$i]['fs_val_max']*1; 
		$min=$fs[$i]['fs_val_min']*1;

		$data2=array();
		$data3=array();
		$data4=array();
		
		for($n=0;$n<sizeof($data);$n++) {
		
			if(trim($data[$n])=='') continue;
			
			$tmp=explode(' ', trim($data[$n]));
			
			$val=$tmp[1]*1;
			
			//if($n==0) { $min=$val; $max=$val; }
			
			//$stock+=$val;
			//$data2[$n]=$stock;
			$data2[$n]=$val*1;
			list($hr,$mins)=explode(':',$tmp[0]);
			$data3[$n]=mktime($hr*1,$mins*1,0);
			$data4[$n]=date('H:i',$data3[$n]);
			
			//if($stock<$min) $min=$stock;
			//if($stock>$max) $max=$stock;
			//if($val<$min) $min=$val;
			//if($val>$max) $max=$val;
			
			if(!$init_t) {
				$init_t=true;
				$tmin=$data3[$n];
				$tmax=$data3[$n];
				$hmin=$data4[$n];
				$hmax=$data4[$n];
			}
			
			if($data3[$n]<$tmin) { $tmin=$data3[$n]; $hmin=$data4[$n]; }
			if($data3[$n]>$tmax) { $tmax=$data3[$n]; $hmax=$data4[$n]; }
			
		}		
				
		//$tmpdif=($max-$min)/10;
		$smin=number_format($min,0,',','.');
		$smax=number_format($max,0,',','.');
	
		//$max=$max+$tmpdif;
		//$min=$min-$tmpdif;
			
		$dif=$max-$min;
				
		$tsdif=($data3[sizeof($data3)-1]*1)-($data3[0]*1);
		
		$fs[$i]['data2']=$data2;
		$fs[$i]['data3']=$data3;
		$fs[$i]['data4']=$data4;
		
		$fs[$i]['max']=$max;
		$fs[$i]['min']=$min;
		$fs[$i]['smax']=$smax;
		$fs[$i]['smin']=$smin;
		$fs[$i]['tsdif']=$tsdif;
		$fs[$i]['dif']=$dif;
		
		}
		
		$_tsdif=$tmax-$tmin;
		
		$svg='';
		
		$svg.='<svg width="660px" height="280px">';
		
		$offsetx=5;
		$offsety=15;

		
		$cnt=sizeof($data);
		
			$svg.="<text x='5' y='22' font-weight='bold' 
			font-family='Verdana' font-size='14' fill='black' >
				$codigo
			</text><text x='5' y='38' font-weight='bold'
			font-family='Verdana' font-size='16' fill='green' >
				$glosa
			</text>
			
			<text x='5' y='".($offsety+255)."' 
			font-family='Verdana' font-size='16' fill='black' >
				".$hmin."
			</text>
			
			<text x='610' y='".($offsety+255)."' text-anchor='right'
			font-family='Verdana' font-size='16' fill='black' >
				".$hmax."
			</text>
						
			";
			
/*

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
			<text x='600' y='85' text-anchor='middle'  font-weight='bold'
			font-family='Verdana' font-size='14' fill='gray' >
				PPM:$".number_format($precio,0,',','.').".-
			</text><text x='600' y='135' text-anchor='middle'
			font-family='Verdana' font-size='18' fill='black' >
				Gasto:$".number_format($total*$precio,0,',','.').".-
			</text>



*/

			
		$cnt=0;	
		for($xx=0;$xx<=650;$xx+=32.5) {
			
			if($cnt++%2==0) $color='#BBBBBB'; else $color='#EEEEEE';
			
			$xxx=$xx+$offsetx;
			
			$svg.="<line x1='$xxx' y1='$offsety' x2='$xxx' y2='".($offsety+240)."' style='stroke-width: 0.5; stroke: ".$color.";'/>";
			
		}

		$cnt=0;	
		for($yy=0;$yy<=240;$yy+=24) {
			
			if($cnt++%2==0) $color='#BBBBBB'; else $color='#EEEEEE';
			
			$yyy=$yy+$offsety;
			
			$svg.="<line x1='$offsetx' y1='$yyy' x2='".($offsetx+650)."' y2='".($yyy)."' style='stroke-width: 0.5; stroke: ".$color.";'/>";
			
		}
		
		
		if($cnt>1) {			

			for($ss=0;$ss<sizeof($fs);$ss++) {

			if(trim($fs[$ss]['fs_datos'])=='') continue;

			$data2=$fs[$ss]['data2'];
			$data3=$fs[$ss]['data3'];
			$data4=$fs[$ss]['data4'];
			
			$max=$fs[$ss]['max'];
			$min=$fs[$ss]['min'];
			$smax=$fs[$ss]['smax'];
			$smin=$fs[$ss]['smin'];
			$tsdif=$fs[$ss]['tsdif'];
			$dif=$fs[$ss]['dif'];
			
			$rd=3;
			
			for($i=1;$i<sizeof($data2);$i++) {
			
				$color=$fs[$ss]['fs_color'];
				
				//$x1=$offsetx+((450/($cnt-1))*($i-1));
				//$x2=$offsetx+((450/($cnt-1))*$i);
				$x1=$offsetx+((($data3[$i-1]-$tmin)*650)/$_tsdif);
				$x2=$offsetx+((($data3[$i]-$tmin)*650)/$_tsdif);
				
				
				if($dif>0) {
					$y1=number_format($offsety+(240-((($data2[$i-1]-$min)*240)/$dif)),4,'.','');
					$y2=number_format($offsety+(240-((($data2[$i]-$min)*240)/$dif)),4,'.','');
				} else {
					$y1=number_format($offsety+(240-((($data2[$i-1]-$min)*240))),4,'.','');
					$y2=number_format($offsety+(240-((($data2[$i]-$min)*240))),4,'.','');				
				}
				
				// LINEA RECTA
				//$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y1' style='stroke-width: 2; stroke: ".$color.";'/>";	
				//$svg.="<line x1='$x2' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 2; stroke: ".$color.";'/>";	
				
				// DIAGONAL
				$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 3; stroke: ".$color.";'/>";	
				
				if($data2[$i]<0)
					$color='red';

				//if($i==sizeof($data2)-1) 
					//$rd+=1;
	
				//$svg.="<circle cx='$x2' cy='$y2' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";

				//if($i==sizeof($data2)-1) {
					//$rd+=1;

					if($i==1)
					$svg.="<circle cx='$x1' cy='$y1' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";

					
					$svg.="<circle cx='$x2' cy='$y2' r='$rd' style='stroke: ".$color."; fill: ".$color.";'/>";
				//}

			}
			
			}

			
		} else {
			
			$x1=250;
			$x2=420;
			$y1=45;
			$y2=45;
			$color=$fs[$ss]['fs_color'];
			
			$svg.="<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' style='stroke-width: 5; stroke: ".$color.";'/>";	
			$svg.="<circle cx='$x2' cy='$y2' r='10' style='stroke: ".$color."; fill: ".$color.";'/>";
			
		}
		
		$svg.='</svg>';
		

	print($svg);
		
?>
