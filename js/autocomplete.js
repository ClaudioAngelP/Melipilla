// Prototype Light Autocomplete Class v0.3b
// Rodrigo Carvajal J.
// rcarvajal@scv.cl

  var AutoComplete = Class.create();
  
  AutoComplete.prototype = {
  
    initialize: function(object, ajax_url, ajax_options_func, className, width, height, wait_time, first_col, last_col, callback) {
  
    this.object=object;
    this.object_handler=$(object);
    
    this.width=width;
    this.height=height;
    this.className=className;
    this.wait_time=wait_time;
    this.first_col=first_col;
    this.last_col=last_col;
    
    this.ajax_url = ajax_url;
    
    this.ajax_options = ajax_options_func;
    
    this.ajax_request = new Object;
    this.ajax_observer = new Object;
    
    this.callback=callback;
    
    this.selected_option=0;
    this.options_number=0;
    this.wait_response=false;
    this.select_on_response=false;
    this.visible = false;
    this.focus = false;
    
    this.keyTimer=0;
    
    
    this.object_handler.className=this.className;
    
    this.options_div = document.createElement('div');
    
    var thtml='<div style="width:'+this.width+'px;max-height:'+this.height+'px;overflow-x:hidden;overflow-y:auto;" ';
    thtml+='id="__autocompleter_'+object+'"><div ';
    thtml+='style="width:'+this.width+'px;max-height:'+this.height+'px;"';
    thtml+='id="__autocompleter_content_'+object+'"></div>';
    thtml+='</div>';
    
    this.options_div.innerHTML = thtml;
    
    Element.hide(this.options_div);
    
    this.object_handler.parentNode.appendChild(this.options_div);
    
    this.container = document.getElementById('__autocompleter_'+object);
    this.content = document.getElementById('__autocompleter_content_'+object);

    this.container.setStyle({
        border:'1px solid black;',
        width:this.width+'px;',
        'max-height':this.height+'px;',
        overflow:'auto;'
        }
    );
    
    this.options_div.style.position='absolute';
    
    // Handlers for events...
    
    this.eventKEYUP = this.onKeyUp.bindAsEventListener(this);
    this.eventBLUR = this.onBlur.bindAsEventListener(this);
    this.eventKEYDOWN = this.onKeyDown.bindAsEventListener(this);
    this.eventFOCUS = this.onFocus.bindAsEventListener(this);
    this.eventTICK = this.onTick.bindAsEventListener(this);
    
    this.eventMOUSEOVER = this.onMouseOver.bindAsEventListener(this);
    this.eventMOUSEOUT = this.onMouseOut.bindAsEventListener(this);
    this.eventMOUSECLICK = this.onMouseClick.bindAsEventListener(this);
    
    
    Event.observe(this.object_handler, 'keydown', this.eventKEYDOWN);
    Event.observe(this.object_handler, 'keyup', this.eventKEYUP);
    Event.observe(this.object_handler, 'focus', this.eventFOCUS);
    Event.observe(this.object_handler, 'blur', this.eventBLUR);
    
    Event.observe(this.content, 'mousedown', this.eventMOUSECLICK);
    
    return this;
    
    },
    
    onTick: function() {
    
      if(!this.focus && this.visible) this.hide_options();
      
    },
    
    onFocus: function() {
    
      this.focus=true;
    
    },
    
    
    onKeyUp: function(__e) {
      
      var c = (__e.charCode)?__e.charCode:__e.keyCode;
      
      
      switch(c) {
      case Event.KEY_DOWN:
        this.selectNEXTOPT();
      break;
      case Event.KEY_UP:
        this.selectPREVOPT();
      break;
      case Event.KEY_RETURN:
        this.selectTHISOPT();
      break;
      case Event.KEY_RIGHT: case Event.KEY_LEFT: break;
      default:
      
        clearTimeout(this.keyTimer);
        
        this.keyTimer=setTimeout(
          this.send_request.bind(this), 
          this.wait_time
        );
      
      break;
      }
   
    },
    
    onKeyDown: function(__e) {
    
      var c = (__e.charCode)?__e.charCode:__e.keyCode;
   
      if(c==Event.KEY_TAB) {
        if(this.visible) {
          this.hide_options();
          this.object_handler.focus();
        }
      }
    
    },
    
    onMouseOver: function(__e) {
		
		var el = Event.element(__e);
		
		//console.log('over '+this.object);
		
		var val=el.id.split('_');
		
		this.selected_option=val[val.length-1]*1;
		this.redraw();
		//if(!this.visible) this.show_options(); else 
		this.scrollOption();
		
	},

    onMouseOut: function(__e) {

		var el = Event.element(__e);
		
		//console.log('out'+this.object);

		var val=el.id.split('_');
		
		this.selected_option=val[val.length-1]*1;
		this.redraw();
		//if(!this.visible) this.show_options(); else 
		this.scrollOption();
		
	},

    onMouseClick: function() {
      
      // If is a request pending for new options the
      // return key does not select the highlighted option...
      
      // This performs on mousedown event for mouse selection...
      
      //console.log('mousedown '+this.object);
      
      if(!this.wait_response) {
		  
        this.object_handler.value=this.options[this.selected_option][0];
      
        if(typeof(this.callback)=='function') {
          this.callback(this.options[this.selected_option], this.object);
        }
      
        if(this.visible) this.hide_options();
        
      } else {
		  
        this.select_on_response=true;
        
      }
      
    },
    
    selectFIRSTOPT: function() {
    
      this.selected_option=0;
      this.redraw();
      this.content.scrollTop=0;
      if(!this.visible && this.focus) this.show_options();
      
    },
    
    selectNEXTOPT: function() {
      
      if(this.selected_option<(this.options_number-1)) this.selected_option++;
      this.redraw();
      if(!this.visible) this.show_options(); else this.scrollOption();
      
    },
    
    selectPREVOPT: function() {
      
      if(this.selected_option>0) this.selected_option--;
      this.redraw();
      if(!this.visible) this.show_options(); else this.scrollOption();
      
    },
    
    selectANYOPT: function(opt_number) {
      this.selected_option=opt_number;
      this.redraw();
      if(!this.visible) this.show_options(); else this.scrollOption();
    },
    
    selectTHISOPT: function() {
      
      // If is a request pending for new options the
      // return key does not select the highlighted option...
      
      if(!this.wait_response) {
		  
        this.object_handler.value=this.options[this.selected_option][0];
      
        if(typeof(this.callback)=='function') {
          this.callback(this.options[this.selected_option], this.object);
        }
      
        if(this.visible) this.hide_options();
        
      } else {
		  
        this.select_on_response=true;
        
      }
      
    },
    
    scrollOption: function() {
    
      rows = this.content.getElementsByTagName('tr');
      
      first_row = rows[0];
      row = rows[this.selected_option];
      
      //console.log('scrolling '+this.object);
      
      obj_position = Position.positionedOffset(row);
      first_obj_position = Position.positionedOffset(first_row);
      
      obj_position[0]=obj_position[0]-first_obj_position[0];
      obj_position[1]=obj_position[1]-first_obj_position[1];
      
      obj_height=Element.getHeight(row);
      
      // Is higher than the region displayed...
      
      if(this.content.scrollTop>obj_position[1]) {
        this.content.scrollTop=obj_position[1];
      }
        
      // Is below the display...
      
      if(this.content.scrollTop<(obj_position[1]-this.height)+obj_height) {
        this.content.scrollTop=(obj_position[1]-this.height)+obj_height;
      }
    
    },
    
    show_options: function() {
      
      try {
      
      // this.position_container();
      
      this.send_request();
      
      } catch(err) {
      
        alert(err);
      
      }
      
    },
    
    position_container: function() {
    
      obj_pos = Position.cumulativeOffset(this.object_handler);
      
      obj_height = this.object_handler.getHeight();
      
      this.options_div.setStyle(
          {
            left:obj_pos[0]+'px;', 
            top:obj_pos[1]+obj_height+'px;'
          }
      );
      
    },
    
    send_request: function() {
    
      __ajax_options = this.ajax_options();
      
      if(!__ajax_options) {
        if(this.visible) this.hide_options();
        return;
      }
      
      this.object_handler.className=this.className+'_charge';
      
      this.wait_response=true;
      
      this.ajax_observer = this.refresh_options.bindAsEventListener(this);
      
      var current_time = new Date();
      
      timestamp = current_time.getTime();
      
      __ajax_options.onComplete = this.ajax_observer;
      __ajax_options.timestamp = timestamp;
      
      this.ajax_request = new Ajax.Request(this.ajax_url, __ajax_options);
      
    },
    
    refresh_options: function(request) {
      
      try {
        
      this.options = request.responseText.evalJSON(true);
      
      this.options_number=this.options.length;
      
      if(this.options.length==0) {
        
        this.object_handler.className=this.className+'_empty';
        
        if(this.visible)
        this.hide_options();
        return;
      
      }
      
      new_options_html='<table width=100% cellpadding=0 cellspacing=0 class="'+this.className+'_container">';
      
      var u=0;
      
      for(i=0;i<this.options.length;i++) {
		  
        new_options_html+='<tr id="__autocompleter_row_'+this.object+'_'+i+'" style="cursor:pointer;"><td width=2% id="__autocompleter_spacer_'+u+'_'+this.object+'_'+i+'">&nbsp;</td>';
        
        for(u=this.first_col;u<=this.last_col;u++) {
          
          if(this.options[i][u]==null) {
            new_options_html+='<td id="__autocompleter_row_'+u+'_'+this.object+'_'+i+'">&nbsp;</td>';
          } else {
            new_options_html+='<td id="__autocompleter_row_'+u+'_'+this.object+'_'+i+'">'+this.options[i][u]+'</td>';
          }
          
          if(u<this.last_col) new_options_html+='<td width=5% id="__autocompleter_spacer_'+u+'_'+this.object+'_'+i+'">&nbsp;</td>';
          
        }
        
        new_options_html+='<td width=2% id="__autocompleter_spacer_'+u+'_'+this.object+'_'+i+'">&nbsp;</td></tr>';
      }
      
      new_options_html+='</table>';
      
      this.content.innerHTML=new_options_html;
      
      var rows = this.content.getElementsByTagName('tr');
      
      for(i=0;i<rows.length;i++) {
		//console.log('row '+i+' es '+rows[i]);
        Event.observe(rows[i], 'mouseover', this.eventMOUSEOVER);
        Event.observe(rows[i], 'mouseout', this.eventMOUSEOUT);
      }
      
      this.object_handler.className=this.className;
      
      if(this.focus && !this.visible && !this.select_on_response) {
        Element.show(this.options_div);
        this.timer = setInterval(this.eventTICK,100);
        this.options_pos=Position.cumulativeOffset(this.options_div);
        this.visible=true;
      }
      
      this.selectFIRSTOPT();
      
      } catch(err) {
      
        alert(err);
      
      }
      
      this.wait_response=false;
      
      if(this.select_on_response) {
        this.selectTHISOPT();
        this.select_on_response=false;  
      }
      
      
    },
    
    onBlur: function() {
    
      if(this.visible) {
        this.object_handler.focus();
        this.hide_options();
      }
      
      this.focus=false;
    
    },
    
    hide_options: function() {
    
      this.visible=false;
      Element.hide(this.options_div);
      clearInterval(this.timer);
    
    },
    
    redraw: function () {
      
      try {

      //console.log('redrawing '+this.object+' on item '+this.selected_option);
      
      rows = this.content.getElementsByTagName('tr');
      
      for(i=0;i<rows.length;i++) {
        rows[i].className=(i%2==0)?this.className+'_row_1':this.className+'_row_2';
        if(i==this.selected_option)
          rows[i].className=this.className+'_row_s';
    
      }
      
      } catch (err) {
        
        alert(err);
      
      }
      
    }
    
    
  }