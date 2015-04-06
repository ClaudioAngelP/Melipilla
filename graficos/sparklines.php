<?php

  define('TEXT_TOP',    1);
  define('TEXT_RIGHT',  2);
  define('TEXT_BOTTOM', 3);
  define('TEXT_LEFT',   4);
    
  define('FONT_1', 1);
  define('FONT_2', 2);
  define('FONT_3', 3);
  define('FONT_4', 4);
  define('FONT_5', 5);


  class Sparkline {
  
    // Constructor
  
    function Sparkline($w=200,$h=100) {
      $this->w=$w; $this->h=$h;
      $this->dataSet=Array();
      $this->featureSet=Array();
      $this->minX=0; $this->minY=0; 
      $this->maxX=0; $this->maxY=0;
      $this->padding=Array(0,0,0,0);
      $this->lineColor=Array(0,0,0);
      $this->lineSize=1;  
      $this->backgroundColor=Array(255,255,255);
    }
    
    function setSize($w,$h) {
      $this->w=$w; $this->h=$h;
    }
    
    function setBackgroundColor($r=255, $g=255, $b=255) {
      $this->backgroundColor=Array($r,$g,$b);
    }

    function setLineColor($r=255, $g=255, $b=255) {
      $this->lineColor=Array($r,$g,$b);
    }
    
    function setLineSize($lw) {
      $this->lineSize=$lw;
    }
    
    function setPadding($l=0, $t=NULL, $b=NULL, $r=NULL) {
      if($t==NULL AND $b==NULL AND $r==NULL)
        $this->padding=Array($l,$l,$l,$l);
      else
        $this->padding=Array($l,$t,$b,$r);
    }
    
    function addDataPoint($dx, $dy, $s=1) {
      
      $n=sizeOf($this->dataSet);
      
      if($n==0) {
        $this->minX=$dx; $this->maxX=$dx;
        $this->minY=$dy; $this->maxY=$dy;
      }
      
      $this->dataSet[$n]=Array($dx, $dy);
      if($this->minX>$dx) $this->minX=$dx;
      if($this->maxX<$dx) $this->maxX=$dx;
      if($this->minY>$dy) $this->minY=$dy;
      if($this->maxY<$dy) $this->maxY=$dy;
      
    }
    
    function setFeaturePoint($dx, $dy, $color, $text, $align, $font, $r, $s=1) {
      $n=sizeOf($this->featureSet);
      $this->featureSet[$n]=Array($dx, $dy, $color, $text, $align, $font, $r);
    }

    
    function processData() {
      
      $px=$this->padding[0] + $this->padding[3];
      $py=$this->padding[1] + $this->padding[2];
      
      $this->paddingX=$px; $this->paddingY=$py;
      
      $rX=($this->maxX - $this->minX);
      $rY=($this->maxY - $this->minY);
      
      if($rX==0) $rX=1; 
      if($rY==0) $rY=1; 
      
      for($i=0;$i<sizeof($this->dataSet);$i++) {
        $this->readySet[$i][0]=
        round(($this->dataSet[$i][0] - $this->minX) / $rX * ($this->w-$px));
        
        $this->readySet[$i][1]=
        round(($this->dataSet[$i][1] - $this->minY) / $rY * ($this->h-$py));
      }
      
      for($i=0;$i<sizeof($this->featureSet);$i++) {
        $this->featureSet[$i][0]=
        round(($this->featureSet[$i][0] - $this->minX) / $rX * ($this->w-$px));
        
        $this->featureSet[$i][1]=
        round(($this->featureSet[$i][1] - $this->minY) / $rY * ($this->h-$py));
      }
              
    }
    
    // Renderizador

    function Render() {
    
      $this->readySet=Array();
    
      $this->imageHandler=imagecreatetruecolor($this->w, $this->h);
      $this->processData();

      $_bgColor=imagecolorallocate($this->imageHandler,
                      $this->backgroundColor[0],
                      $this->backgroundColor[1],
                      $this->backgroundColor[2]
                  );
    
      $_lineColor=imagecolorallocate($this->imageHandler,
                      $this->lineColor[0],
                      $this->lineColor[1],
                      $this->lineColor[2]
                  );

      imagefilledrectangle($this->imageHandler, 
                            0, 0, $this->w, $this->h, 
                            $_bgColor);
      
      // Dibuja líneas del gráfico
      
      imagesetthickness($this->imageHandler, $this->lineSize);
      
      $graphH=$this->h - $this->paddingY;
      
      for($i=1;$i<sizeOf($this->readySet);$i++) {
        
        $d=$this->readySet[$i];
        $dt=$this->readySet[$i-1];
        
        imageline($this->imageHandler,
                  $dt[0] + $this->padding[0],
                  ($graphH - $dt[1]) + $this->padding[1],
                  $d[0] + $this->padding[0],
                  ($graphH - $d[1]) + $this->padding[1], 
                  $_lineColor);
                  
      }
      
      imagesetthickness($this->imageHandler, 1);
      
      for($i=0;$i<sizeof($this->featureSet);$i++) {
        
        $d=$this->featureSet[$i];
        
        $pcolor=imagecolorallocate($this->imageHandler, 
                                    $d[2][0], $d[2][1], $d[2][2]);
        
        imagefilledellipse($this->imageHandler, 
                            $d[0]+$this->padding[0], 
                            $graphH - $d[1] + $this->padding[1], 
                            $d[6], $d[6], $pcolor);
                            
        $tx=($d[0]+$this->padding[0]);
        $ty=($graphH - $d[1] + $this->padding[1])-
            (imagefontheight($d[5])/2);
        
        if($d[4]==TEXT_RIGHT) 
          $tx+=5;
        else if($d[4]==TEXT_LEFT) 
          $tx-=(imagefontwidth($d[5])*strlen($d[3]))+5;
        else {
          $tx-=(imagefontwidth($d[5])*strlen($d[3]))/2;
          if($d[4]==TEXT_TOP)
            $ty-=(imagefontheight($d[5]));
          else
            $ty+=(imagefontheight($d[5]));
        }
        
        
        imagestring($this->imageHandler,$d[5],$tx,$ty,$d[3],$pcolor);
        
      }
    
    }
    
    // Envía imagen al browser...
    
    function Inspect() {
    
      print_r($this->dataSet);
      print_r($this->readySet);

      $this->processData();
      
      $graphH=$this->h - $this->paddingY;
      
      echo '\n\nminX '.$this->minX."\n";
      echo 'maxX '.$this->maxX."\n";
      echo 'minY '.$this->minY."\n";
      echo 'maxY '.$this->maxY."\n\n";
      
      
      for($i=1;$i<sizeOf($this->readySet);$i++) {
        
        $d=$this->readySet[$i];
        $dt=$this->readySet[$i-1];
        
        echo " L($i) = ( ".
                  ($dt[0] + $this->padding[0]).", ".
                  (($graphH - $dt[1]) + $this->padding[1]).", ".
                  ($d[0] + $this->padding[0]).", ".
                  (($graphH - $d[1]) + $this->padding[1])." ) \n";
                  
      }

    }
    
    function Output() {
      header('Content-type: image/png');
  
      imagepng($this->imageHandler, NULL, 5);
      imagedestroy($this->imageHandler);

    }
  
  }

?>
