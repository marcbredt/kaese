// dump obj information
function dump(o) {
  var s = "[ ";
  for(var oi in o){ 
    //if(typeof o[oi] == "object") s += oi + ": " + dump(o[oi]) + ", ";
    //else s += oi + ": " + o[oi] + ", "; 
    s += oi + ": " + o[oi] + ", "; 
  }
  if(s!="[ ") s = s.slice(0,-2);
  s += " ]";
  return s;
}

function get_array_index(array,iname) {
  return $.map(array,function(item,i){
                       //console.log("item="+item+", ditem="+dump(item)+
                       //            ", typeof="+(typeof item)+", i="+i);
                       if(item.name==iname) return i;
                     })[0];

}

// mouse handler
function mouse_handler(e,p){ 

  /*
  console.log("mouse_event=" + dump(e) + 
              "\n\nwith=" + dump(p) + 
              "\n\na=" + $(this).attr("id") + " fired.\n");
  */

  // avoid redirections
  e.preventDefault();
  p.preventDefault();

  switch(p.type) {

    case "click": 

        // run watch 
        if($(this).attr("id")=="stats_form_submit_run"){
          console.log("click at stats_form_submit_run");

          var rd = $("#stats_form").serialize();
          var rda = $("#stats_form").serializeArray();
          var od;
          //console.log("rd=" + rd + ", rda=" + dump(rda));
          
          // get the command type
          var aix = undefined;
          if((aix=get_array_index(rda,"stats_type"))!="undefined" 
             && rda[aix].value=="command") {
            var od = document.getElementById("contents_command");
          } else if((aix=get_array_index(rda,"stats_type"))!="undefined"
                    && rda[aix].value=="logfile") {
            var od = document.getElementById("contents_logfile");
          } else {
            console.log("E: Unknown " + rda[0].name + "='" + rda[0].value + "'");
          }

          // put the filter
          var fox = get_array_index(rda,"stats_filter"); 
          var fix = get_array_index(rda,"stats_filter_value");
          //console.log("filter fox=" + fox + ", fix=" + fix);
          if(rda[fox]!==undefined && rda[fox].value==="on" && rda[fix].value!=="") {
            var fa = rda[fix].value.split(";");
            //console.log("fa="+dump(fa));
            $(fa).each(function(faix) { 
              //console.log("fa[faix]="+fa[faix]+
              //              ", faix="+faix+
              //              ", this.[faix]="+dump($(this)[faix])); 
              stats.setf(fa[faix].split(":")[0],fa[faix].split(":")[1]);
            });
          }

          // set the status
          stats.sets(true); 

          // send the xhr 
          od.onclick = send_xhr_request(od, "GET", 
                         "php/response.php?action=read&"+rd, "run", rda[aix].value);

          // start the timer
          timer_run(stats.geti());

        // stop watch
        } else if($(this).attr("id")=="stats_form_submit_stop") {

          var od = document.getElementById("contents_logfile");
          od.onclick = send_xhr_request(od, "GET", 
                         "php/response.php?action=halt", "stop");

          stats.init(); // unset global stats
          ptr = 0; // reset pointer for next passthru run

          timer_stop();
        }

      break;
    
    default: break;
  }

  return false;
}

// set up listeners, trigger function mouse_handler on click events
function listen(o) {
  $(o).on("mouseevent", mouse_handler); 
  $(o).click("mouseevent", function(event){$(o).trigger("mouseevent",event)});
}

// reverse concatenation of array contents
function rconcat(a,offset) {
  var rc = "";
  var l = a.length;
  for(var i=l-1-offset; i<l-1; i++) rc = rc + a[i] + "<br>"; 
  return rc; 
}

// apply the filter for a single command
function filter_matches(command,cline,key,value){
  switch(command) {
    case "logfile" : 
        var vs = value.split(/,/g);
        var va = false;
        switch(key) {
          // NOTE: difficulty here is applying 'chain' element on logfile entries
          case "chain" : 
            // TODO: 'kaese's rule attrs need to be mapped to logfile abr's
            break;
          default : 
              $.each(vs, function(k,v){
                           va = va || (cline.match(
                                         new RegExp(key.toUpperCase()+"="+
                                                    v.toUpperCase(),"g"))!==null);
                         }); 
            break;
        } 
        return va;
      break;
    
    case "command" : break;
    default: break;
  }
} 

// apply the filter
function filter_applied(command,cline) {
  var filter = stats.filter;
  var fm = true;

  if(filter.length>0) {
    switch(command) {
      case "logfile" : 
          var fm = true;
          $.each(filter,function(key,value){ 
                          //console.log("f="+dump(filter)+", k="+key+", v="+value); 
                           fm &= filter_matches(command,cline,key,value);
                        }); 
        break;
      case "command" : 
        break; 
      default : break;
    }
  }

  return false || fm;
}

// evaluate filter and other stuff to modify the stats liberal
function evaluate(command,key,line) {

  if(line.trim()!=""&&line.trim()!="\r"
     &&line.trim()!="\n"&&line.trim()!="\r\n") {

    switch(command) {

      case "logfile" :  
          //console.log("c="+command+", k="+key+", l="+line);
          if(filter_applied(command,line)) {
            //console.log("l='"+line+"' matches filter "+dump(stats.filter));
            // add packet and bytes count to the stats literal
            var matches = line.match(/LEN=[1-9][0-9]*/);
            if(matches!==null) {
              var bytes = parseInt(matches.toString().split("=")[1]);
              stats.setp(1);
              stats.setb(bytes);
              stats.setppi(1);
              stats.setbpi(bytes);
            } else {
              console.log("E: 0 matches in line '" + line + "'"); }
          }
        break;

      case "command" :
        break;

      default: break;

    }

  }
}

// calculate stats for values submitted
function modify_stats(command,chunk) {

  // distinct the commands
  switch(command) {

    // extract statistics directly from command output chunk
    case "command" : 
        // NOTES: main chains -> first rule = max packets/bytes
        //        sub chains -> sum of accept targets or first rule
        //          if non standard/another chain target
        //
        // COUNT: accept targets + first chain rule
        evaluate(command,k,chunk); 
      break;

    // extract statistics from logfile output 
    case "logfile" : 
        // evaluate each line of the chunk
        $.each(chunk.split(/[\r\n]/g), function(k,l) { 
                                        evaluate(command,k,l); 
                                      });
      break;

    default : break;
  }

}

// create some random values for testing/demo purposes
function rand(min,max) {
  //console.log("rand types = " + (typeof min) + ", " + (typeof max));
  if(typeof min == "undefined" || typeof min != "number") min = 0;
  if(typeof max == "undefined" || typeof max != "number") max = 100;
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

// manage queue operations on a fifo queue on the client side
function queue_manage(response,output,command) {

  //console.log("filling queue with " + response);
  /*
  console.log("rlen=" + response.length + 
              ", type=" + (typeof output) + 
              ", command=" + command);
  */

  // NOTE: as passthru just passes the output it just grows on the 
  //        client side so there is probably a use of newline pointers
  //       for passthru its a bit more complex as multiple/broken
  //        lines can be passed

  if(response.length>0) {

    // get the next response chunk which was not processed yet
    var rm = response.substring(rptr).match(/[\r\n]/g);
    if(rm!==null) {
      // if the trimmed chunks last char is not an [\r\n] 
      // the chunk is broken and needs to be concatenated
      // with the next chunk
      var li = response.substring(rptr).lastIndexOf(rm[rm.length-1]);
      var response_chunk = response.substring(rptr, rptr+li); 
      // store last command output length to know where we have been yet
      rptr = rptr + li;
      //console.log("li="+li+", rp="+rptr+", r="+response_chunk);

      // and dump the chunk to the output container 
      $(output).empty(); // clear the output container first
      $(output).html(response_chunk.replace(/[\r\n]/g,"<br>"));
      // TODO: for logfile responses probably create a queue

      // then get statistics for the chunk,
      modify_stats(command,response_chunk);
    }

  }

}

// shift queue and modify stats
function queue_shift() {

}

// some timer functions to run
function timer_run(seconds) {
  $('#span_stats_status').text(stats.gets()); 
  $('#span_stats_revtimer').text(parseInt(stats.geti())); 
  // TODO: update revtimer each second
  rtimer = setInterval(function(){
                         $('#span_stats_revtimer').
                           text(parseInt($('#span_stats_revtimer').text())-1); 
                       }, 1000);
  $('#span_stats_interval').text(stats.geti() + " seconds"); 
  $('#span_stats_filter').text(dump(stats.getf())); 
  timer = setInterval(function(){
                        // queue_read simulation
                        // update stats, for testing purposes some random ones
                        /*
                        var p = rand(1,5);
                        var b = rand(5,10);
                        console.log("rands=[" + p + ", " + b + "]");
                        stats.setp(p); stats.setb(b);
                        stats.setppi(p); stats.setbpi(b);
                        */

                        // update stats fields
                        $('#span_stats_revtimer').text(stats.geti()); 
                        $('#span_stats_duration').
                           text(parseInt($('#span_stats_duration').text())
                                  + stats.geti()); 
                        $('#span_stats_num_packets_in_total').text(stats.getp()); 
                        $('#span_stats_num_bytes_in_total').text(stats.getb()); 
                        $('#span_stats_num_packets_per_interval').text(stats.getppi()); 
                        $('#span_stats_num_bytes_per_interval').text(stats.getbpi());
                      }, seconds*1000);
}

// and to stop a timer
function timer_stop() {
  $('#span_stats_status').text(stats.gets()); 
  clearInterval(rtimer);
  clearInterval(timer);
}

// send a XMLHttpRequest
function send_xhr_request(output,method,query,action,command) {
      
  console.log("send_xhr_request: " + action);

  if(undefined === xhr && action=="run") {

    xhr = new XMLHttpRequest();
    // NOTE: <br> tag depends on the doctype, see XHTML's <br/>
    xhr.onprogress = function(e) {
      var rt = e.currentTarget.responseText; // response text
      //console.log("readystate=" + xhr.readyState);
      queue_manage(rt,output,command);
    }
    xhr.onreadystatechange = function(e) {
      if(xhr.readyState == 4) console.log("xhr request finished");
    }
    xhr.open(method, query, true); // true represents async com
    xhr.send(null);

    console.log("launching xhr=" + dump(xhr));

  } else if(undefined !== xhr && action=="stop") {
   
    console.log("aborting xhr request, xhr=" + dump(xhr));

    // abort running/open header as xhr is globally declared
    xhr.abort(); 

    // afterwards send the cleaning xhr request to clean up server side 
    xhr.open(method, query, false); // and wait for it to finish
    xhr.send(null);
 
    // unset xhr so it can be launched/stopped again
    delete xhr; xhr = undefined; // avoid reference errors too

  } else if(xhr || undefined !== xhr) {
    
    console.log("xhr already sent/running");   

  }

}

// main
var xhr; // declared outiside to abort xhr request, otherwise it would be pending
var rtimer, timer;
var queue; // fifo queue for logfile content passed thru
var rptr = 0; // response pointer that stores the last position in the response 
              // text when data passed thru
 
// statistics object/literal, contains 
var stats = { 

  // "private" object variables
  
  status : false,
  
  packets : 0, // initial total number of packets captured

  packetspi : 0, // initial packets per interval captured

  bytes : 0, // initial total number of bytes captured

  bytespi : 0, // initial bytes per interval captured

  interval : 10, // default interval in seconds when refreshing values

  filter : { // initial filter, contains nothing
           }, 

  // stat functions
 
  // get stats for a logfile line
  gets : function(line) {
         },

  // getters, setters, helpers

  // get current packets captured
  gets : function() {
           return this.status;
         },
  // set packets captured
  sets : function(state) {
           if(typeof state == "boolean") this.status = state;
         },
  // get current packets captured
  getp : function() {
           return this.packets;
         },
  // set packets captured
  setp : function(amount) {
           this.packets += amount;
         },
  // get current packets captured during interval
  getppi : function() {
           var ppi = this.packetspi;
           this.packetspi = 0;
           return ppi;
         },
  // set packets captured during interval
  setppi : function(amount) {
           this.packetspi += amount;
         },
  // get current bytes captured
  getb : function() {
           return this.bytes;
         },
  // set bytes captured
  setb : function(amount) {
           this.bytes += amount;
         },
  // get current bytes captured during interval
  getbpi : function() {
           var bpi = this.bytespi;
           this.bytespi = 0;
           return bpi;
         },
  // set bytes captured during interval
  setbpi : function(amount) {
           this.bytespi += amount;
         },
  // get current interval set
  geti : function() {
           return this.interval;
         },
  // set interval to refresh
  seti : function(seconds) {
           this.interval = seconds;
         },
  // get the current filter 
  getf : function() {
           return this.filter;
         },
  // set a filter key k with value v
  setf : function(k,v) {
           eval("this.filter." + k + " = v;");
         },
  // initialize this object
  init : function init() {
           this.status = false;
           this.packets = 0;
           this.packetspi = 0;
           this.bytes = 0;
           this.bytespi = 0;
           this.interval = this.geti();
           this.filter = {};
         }
}

// document callbacks
var callbacks = {
  onready : function() {
              // set up default values
              $('#stats_type').val("logfile");
              $('#stats_filter').prop("checked",true);
              $('#stats_filter_value').val("proto:tcp");
              $('#span_stats_status').text(stats.gets());
              $('#span_stats_revtimer').text(stats.geti());
              $('#stats_interval').val(stats.geti());
              $('#span_stats_interval').text(stats.geti() + " seconds"); 
              $('#span_stats_duration').text(0);
              $('#span_stats_filter').text(dump(stats.getf())); 
              $('#span_stats_num_packets_in_total').text(stats.getp()); 
              $('#span_stats_num_bytes_in_total').text(stats.getb()); 
              $('#span_stats_num_packets_per_interval').text(stats.getppi()); 
              $('#span_stats_num_bytes_per_interval').text(stats.getbpi());
              // set up button listeners
              listen('#stats_form_submit_run'); 
              listen('#stats_form_submit_stop'); 
            } 
}
// set focus and capture key events
$(document).ready(callbacks.onready);

// some simple stat obj tests
/*
console.log("stats=" + dump(stats));

stats.setp(621);
console.log("packets=" + stats.getp());

stats.setppi(217);
console.log("packetspi=" + stats.getppi());

stats.setb(234);
console.log("bytes=" + stats.getb());

stats.setbpi(983);
console.log("bytespi=" + stats.getbpi());

stats.seti(20);
console.log("interval=" + stats.geti());

stats.setf("protocol","tcp");
console.log("filter=" + dump(stats.getf()));

console.log("stats=" + dump(stats));
stats.init();
console.log("stats=" + dump(stats));
*/

