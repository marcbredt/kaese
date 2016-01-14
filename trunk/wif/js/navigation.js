function refresh(o, t) {
  $(o).text(t);
}

function dump(o) {
  var s = '[ ';
  for(var oi in o){ 
    //if(typeof o[oi] == "object") s += oi + ": " + dump(o[oi]) + ", ";
    //else 
    s += oi + ": " + o[oi] + ", "; 
  }
  if(s!="[ ") s = s.slice(0,-2) + ' ]';
  return s;
}

function mouse_handler(e,p) {
  console.log("\n\nmouse_event=" + dump(e) + 
              "\n\nwith=" + dump(p) + 
              "\n\no=" + $(this).attr("href") + " fired.\n");
  p.preventDefault(); // prevent from redirect

  // if we are at stats and xhr state is not DONE (4) yet 
  // run the stop cmd first
  var nloc = $(this).attr("href");
  if(typeof xhr != "undefined" && nloc != "stats" && xhr.readyState < 4) {
    console.log("calling stop xhr request before leaving");
    $("#stats_form_submit_stop").click();
  }
  
  // then load the contents
  $("#contents").load("html/" + nloc + ".html.php");
  return false;
}

function listen(o) {
  $(o).on("mouseevent", mouse_handler);
  $(o).click("mouseevent", function(event){$(o).trigger("mouseevent",event)});
}

// main
var callbacks = {
  onready : function() { 
              listen("#a_home"); 
              listen("#a_config"); 
              listen("#a_stats"); 
              listen("#a_graphs"); 
              listen("#a_about"); 
              listen("#a_license"); 
              $("#contents").load("html/home.html.php")
            }
}
$(document).ready(callbacks.onready);
