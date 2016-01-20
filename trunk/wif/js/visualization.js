// instantiate the chart
function chart_instantiate(co,iv) {
  // create a context
  var cctx = $(co).get(0).getContext("2d");
  //Chart.defaults.global.responsive = true;

  // TODO: adjust chart options

  // setup labels for interval
  for(var i=0; i<c.samples; i++) 
    c.data.labels[i] = (i*iv).toString() + "s";

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
function append_label_data() {
  var queue = c.data.labels;
  var last = queue[queue.length-1];
  if(last===null||last===undefined) last = 0;
  //console.log("len="+c.data.datasets[0].data.length);
  if(c.data.datasets[0].data.length == c.samples) {
    queue.shift();
    queue[queue.length] = 
      (parseInt(queue[queue.length-1].split("s")[0])+stats.geti())
        .toString()+"s";
  }
}

// chart literal
var c = {

  samples : 30,

  chart : null,

  type : "Line",

  options : {
    scaleShowGridLines : true,
    scaleGridLineColor : "rgba(0,0,0,.05)",
    scaleGridLineWidth : 1,
    scaleShowHorizontalLines: true,
    scaleShowVerticalLines: true,
    bezierCurve : false,
    bezierCurveTension : 0.4,
    pointDot : true,
    pointDotRadius : 4,
    pointDotStrokeWidth : 1,
    pointHitDetectionRadius : 20,
    datasetStroke : true,
    datasetStrokeWidth : 2,
    datasetFill : true,
    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
    multiTooltipTemplate: "<%= value %>",
    onAnimationProgress: function(){},
    onAnimationComplete: function(){}
  },

  data : {
    labels : [],
    datasets : [
      {
        label: "My First dataset",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "rgba(220,220,220,1)",
        pointColor: "rgba(220,220,220,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: [ ] //[65, 59, 80, 81, 56, 55, 40]
      }/*,
      {
        label: "My Second dataset",
        fillColor: "rgba(151,187,205,0.2)",
        strokeColor: "rgba(151,187,205,1)",
        pointColor: "rgba(151,187,205,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(151,187,205,1)",
        data: [28, 48, 40, 19, 86, 27, 90]
      }*/
    ]
  }

}

chart_instantiate("#contents_graph_chart",stats.geti());
var cr = setInterval(function() {
                       append_label_data(); 
                       append_trial_data(0,rand()); 
                       chart_refresh(); 
                     }, 1000); 

