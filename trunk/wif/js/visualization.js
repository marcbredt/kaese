// instantiate the chart
function chart_instantiate(co,ivs) {
  // create a context
  var cctx = $(co).get(0).getContext("2d");
  //Chart.defaults.global.responsive = true;

  // TODO: probably adjust chart options

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

  samples : 50,

  chart : null,

  type : "Line",

  options : {
    animation : false,
    animationSteps: 60,
    animationEasing: "easeOutQuart",
    showScale: true,
    scaleOverride: false,
    scaleSteps: null,
    scaleStepWidth: null,
    scaleStartValue: null,
    scaleLineColor: "rgba(0,0,0,.1)",
    scaleLineWidth: 1,
    scaleShowLabels: true,
    scaleLabel: "<%=value%>",
    scaleIntegersOnly: true,
    scaleBeginAtZero: false,
    scaleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
    scaleFontSize: 12,
    scaleFontStyle: "normal",
    scaleFontColor: "#666",
    responsive: false,
    maintainAspectRatio: true,
    showTooltips: true,
    customTooltips: false,
    tooltipEvents: ["mousemove", "touchstart", "touchmove"],
    tooltipFillColor: "rgba(0,0,0,0.8)",
    tooltipFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
    tooltipFontSize: 14,
    tooltipFontStyle: "normal",
    tooltipFontColor: "#fff",
    tooltipTitleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
    tooltipTitleFontSize: 14,
    tooltipTitleFontStyle: "bold",
    tooltipTitleFontColor: "#fff",
    tooltipYPadding: 6,
    tooltipXPadding: 6,
    tooltipCaretSize: 8,
    tooltipCornerRadius: 6,
    tooltipXOffset: 10,
    tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
    multiTooltipTemplate: "<%= value %>",
    onAnimationProgress: function(){},
    onAnimationComplete: function(){},

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

