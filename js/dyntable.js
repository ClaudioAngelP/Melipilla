// DynTable


console.log('creating dyntable class prototype...');

var DynTable = Class.create();

DynTable.prototype = {
  
    initialize: function(div, config) {
  
    console.log('dyntable initializing...');
  
    this.container = $(div);
    this.config_file = config;
    
    this.descriptor = new Object();
    
    // DynTable actions...
    
    this.list=new Object();
    this.submit=new Object();
    
    this.list.action='';
    this.list.method='post';
    this.list.parameters='';

    this.submit.action='';
    this.submit.method='post';
    this.submit.parameters='';
    
    // DynTable field and data containers...
    
    this.fields = new Array();
    this.data = new Object();
    
    // html container...
    
    this.html='';
    this.eventCLICK=this.click.bindAsEventListener(this);
    
    // DynTable styles...
    
    this.rowh='';
    this.row1=''; this.row2=''; this.row_mouse_over='';
    
    this.currentCell=null;
    
    console.log('dyntable created...');
    
    try{
    
    var loadconfig = new Ajax.Request(
    this.config_file, 
    {
      onComplete: this.setConfig.bind(this)  
    });  
    
    } catch(err) { console.error(err); }
    
    return this;
  
  },

  getConfig: function(tag, num) {
  
    var tmp=this.descriptor.getElementsByTagName(tag);
    
    return tmp[num].textContent;
    
  },

  setConfig: function(conf) {
    
    console.log('dyntable refreshing data...');

    try {
        
      this.descriptor=conf.responseXML.documentElement;
      console.log(this.descriptor);
          
      this.list.action=this.getConfig('action', 0);
      this.submit.action=this.getConfig('action', 1);
         
      this.list.method=this.getConfig('method', 0);
      this.submit.method=this.getConfig('method', 1);

      this.list.parameters=this.getConfig('parameters', 0);
      this.submit.parameters=this.getConfig('parameters', 1);
      
      this.rowh=this.getConfig('rowh', 0);
      this.row1=this.getConfig('row1', 0);
      this.row2=this.getConfig('row2', 0);
      this.row_mouse_over=this.getConfig('row_mouse_over', 0);
          
      console.log('action:'+this.list.action);
      console.log('method:'+this.list.method);
      console.log('parameters:'+this.list.parameters);
          
      fields=this.descriptor.getElementsByTagName('field');
          
      for(var n=0;n<fields.length;n++) {
            
        this.fields[n] = new Object();
        this.fields[n].id=fields[n].attributes.id.value;
        this.fields[n].name=fields[n].attributes.name.value;
        this.fields[n].style=fields[n].attributes.style.value;  
        this.fields[n].align=fields[n].attributes.align.value;  
            
        console.log('field '+this.fields[n].id+' is '+this.fields[n].name);
          
      }

      console.log(this.fields.length+' field(s)...');

    } catch(err) {
        
      console.error(err);
        
    }
        
    var loaddata = new Ajax.Request(
      this.list.action,
      {
        method: this.list.method,
        parameters: this.list.parameters,
        onComplete: this.setData.bind(this)
      }
    );
        
  },
  
  setData: function(data) {
  
    console.log('dyntable parsing JSON data...');
  
    this.data=data.responseText.evalJSON(true);
    
    this.redraw();
  
  }, 
    
  redraw: function() {
  
    console.log('dyntable redrawing...');
  
    var html=this.html;
  
    html='<table style="width:100%;"><tr class="'+this.rowh+'">';
    
    var num=this.fields.length;
    var dnum=this.data.length;
    
    console.log('dyntable displaying ['+dnum+'] row(s).');
    
    // Print column headers...
    
    for(var n=0;n<num;n++) {
      html+='<td>'+this.fields[n].name+'</td>';
    }
    
    html+='</tr>';
    
    // Print rows of data...
    
    for(var j=0;j<dnum;j++) {
      
      var rclass=(j%2==0)?this.row1:this.row2;
      
      html+='<tr class="'+rclass+'" ';
      html+='onMouseOver="this.className=\''+this.row_mouse_over+'\';" ';
      html+='onMouseOut="this.className=\''+rclass+'\'" >';
      
      // Print each column with formatting options...
      
      for(n=0;n<num;n++) {
        html+='<td id="'+j+'_'+n+'" style="'+this.fields[n].style+'">';
        html+=this.data[j][this.fields[n].id]+'</td>';
      }
      
      html+='</tr>';
    
    }
    
    // Fill container with new html...
    
    html+='</table>';
    
    this.container.innerHTML=html;
    
    // Listen to click events...
    
    Event.observe(this.container, 'click', this.eventCLICK);
  
  },
  
  click: function(e) {

    var td=Event.findElement(e,'td');
    
    // Check the cell to be inside the container object...
    
    if(!td.descendantOf(this.container)) return;
    
    if(this.currentCell!=null) {
      var i=this.currentCell.getElementsByTagName('input');
      var v=i[0].value;
      this.currentCell.innerHTML=v;
    }
    
    this.currentCell=td;
    
    var pos=td.id.split('_');
    
    var v=this.data[(pos[0]*1)][this.fields[(pos[1]*1)].id]; 
    
    var thtml='<input type="text" style="width:100%;';
    thtml+='text-align:'+this.fields[(pos[1]*1)].align+';" value="'+v+'">';
    
    console.log(thtml);
    
    td.innerHTML=thtml;
    
    var i = td.getElementsByTagName('input');
    
    i[0].select(); i[0].focus();

  }


}



/*


*/