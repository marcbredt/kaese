// instantiate the chart
function chart_instantiate(co,ivs) {

  // create a context
  var cctx = $(co).get(0).getContext("2d");

  // adjust chart options
  /*
  Chart.defaults.global.responsive = true;
  Chart.defaults.global.animation = false;
  Chart.defaults.global.bezierCurve = false;
  */

  // setup labels for interval
  for(var i=0; i<c.samples; i++) 
    c.data.labels[i] = ((i+1)*ivs).toString() + "s";

  // initial drawings
  c.chart = new Chart(cctx);
  chart_refresh();

}

// refresh chart data
function chart_refresh() {
  c.chart.Line(c.data,c.options).update();
}

// append a single value to a specific trial in c.datasets
function append_trial_data(tix,tdat) {
  var queue = c.data.datasets[tix].data;
  if(queue.length > c.samples) queue.shift();
  queue[queue.length] = parseInt(tdat);
}

// append a single value to a specific trial in c.datasets
function append_label_data(ivs) {
  var queue = c.data.labels;
  var dlen = c.data.datasets[0].data.length;
  if(dlen===null||dlen===undefined) dlen = 0;
  //console.log("len="+dlen+", cs="+c.samples);
  if(dlen>=c.samples) {
    queue.shift();
    queue[queue.length] = 
      (parseInt(queue[queue.length-1].split("s")[0])+ivs)
        .toString()+"s";
  }
}

// chart literal
var c = {

  samples : 96,

  chart : null,

  type : "Line",

  options : {
    responsive : true, 
    animation : false,
    bezierCurve : false
  },

  data : {
    labels : [],
    datasets : [
      {
        label: "packets seen",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "rgba(220,220,220,1)",
        pointColor: "rgba(220,220,220,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: [ ] 
      },
      {
        label: "packets passed",
        fillColor: "rgba(0,220,0,0.2)",
        strokeColor: "rgba(0,220,0,1)",
        pointColor: "rgba(0,220,0,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(0,220,0,1)",
        data: [ ] 
      },
      {
        label: "packets denied",
        fillColor: "rgba(220,0,0,0.2)",
        strokeColor: "rgba(220,0,0,1)",
        pointColor: "rgba(220,0,0,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,0,0,1)",
        data: [ ] 
      }
    ]
  }

}

/* some simple visualization test
chart_instantiate("#contents_graph_chart",stats.geti());
var cr = setInterval(function() {
                       append_label_data(stats.geti()); 
                       append_trial_data(0,rand()); 
                       chart_refresh(); 
                     }, 1000); 
*/

