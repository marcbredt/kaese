<!DOCTYPE html>
<html>
<head>
  <!-- libraries -->
  <script src="lib/chartjs/1.0.2/Chart.js"></script>
  <!-- package scripts -->
  <script src="js/visualization.js"></script>
  <script src="js/communication.js"></script>
</head>
<body>

<table style="width:100%;height:100%;border:0px solid red;"
       cellpadding="0" cellspacing="0">
<tr style="background-color:#0707f2;">
<td valign="top" style="color:#ffffff;padding:13px;
                        background-color:#010195;
                        border-right:1px solid black;">usage:</td>
<td style="padding:13px;">
<div id="stats_usage_div"
     style="width:100%;color:#ffffff;
            color:#ffffff;
            border:0px solid black;">
  # filter - "FKEY:FVAL[,FVAL]*[;FKEY:FVAL[,FVAL]*]*" <br> 
  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
  where FKEY is one of { proto; dpt; spt; in; out; src; dst; chain } <br>
  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
  and FVAL is a corresponding value, e.g { tcp,udp; 80; 443; eth0; eth1; 127.0.0.1; 127.0.1.1; OUTPUT } <br>
  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
  comma separated FVALs will be ORed but all FKEYs results will be ANDed
</div>
</td></tr>
<tr style="background-color:#0202b8;">
<td valign="top" style="color:#ffffff;padding:13px;
                        background-color:#010195;
                        border-right:1px solid black;">setup:</td>
<td style="padding:13px;">
<div id="stats_form_div"
     style="width:100%;color:#ffffff;
            border:0px solid black;">
  <form name="stats_form" id="stats_form">
    <table style="">
      <tr>
        <td>type:</td>
        <td>
          <select name="stats_type" id="stats_type">
            <option value="command">command</option>
            <option value="logfile">logfile</option>
          </select>
        </td>
        <td>
          <select name="stats_command_prefix">
            <option>ipt-filter</option>
            <option>ipt-nat</option>
          </select>
        </td>
        <td>data:</td>
        <td>
          <select name="stats_data">
            <option value="p">packets</option>
            <option value="b">bytes</option> <!-- LEN -->
          </select>
        </td>
        <td><input type="checkbox" name="stats_filter" id="stats_filter">filter:</td>
        <td><input type="text" name="stats_filter_value" id="stats_filter_value"></td>
        <td>interval:</td>
        <td>
          <input name="stats_interval" id="stats_interval" type="number" 
                 style="text-align:right;" step="1" min="0" max="3600" />s
        </td>
        <td>
          <input id="stats_form_submit_run" name="stats_form_submit_run" 
                 type="submit" value="run" />
          <input id="stats_form_submit_stop" name="stats_form_submit_stop" 
                 type="submit" value="stop" />
        </td>  
      </tr>
    </table>
  </form>
</div>
</td></tr>
<tr style="background-color:#0707f2;">
<td valign="top" style="color:#ffffff;padding:13px;
                        background-color:#010195;
                        border-right:1px solid black;">summary:</td>
<td style="padding:13px;">
<div id="contents_refresh"
     style="width:100%;color:#ffffff;
            border:0px solid black;">
  running = <span id="span_stats_status"></span> (<span id="span_stats_revtimer"></span>) <br>
  interval = <span id="span_stats_interval"></span> &nbsp;
  duration = <span id="span_stats_duration"></span> seconds &nbsp;
  filter = <span id="span_stats_filter"></span> <br>

  channel 0/seen : 
  packets/total = <span id="span_stats_num_packets_in_total_0"></span> &nbsp;
  bytes/total = <span id="span_stats_num_bytes_in_total_0"></span> &nbsp;
  packets/interval = <span id="span_stats_num_packets_per_interval_0"></span> &nbsp;
  bytes/interval = <span id="span_stats_num_bytes_per_interval_0"></span> <br>

  channel 1/allowed : 
  packets/total = <span id="span_stats_num_packets_in_total_1"></span> &nbsp;
  bytes/total = <span id="span_stats_num_bytes_in_total_1"></span> &nbsp;
  packets/interval = <span id="span_stats_num_packets_per_interval_1"></span> &nbsp;
  bytes/interval = <span id="span_stats_num_bytes_per_interval_1"></span> <br>

  channel 2/denied : 
  packets/total = <span id="span_stats_num_packets_in_total_2"></span> &nbsp;
  bytes/total = <span id="span_stats_num_bytes_in_total_2"></span> &nbsp;
  packets/interval = <span id="span_stats_num_packets_per_interval_2"></span> &nbsp;
  bytes/interval = <span id="span_stats_num_bytes_per_interval_2"></span> <br>
</div>
</td></tr>
<tr style="background-color:#0202b8;">
<td valign="top" style="color:#ffffff;padding:13px;
                        background-color:#010195;
                        border-right:1px solid black;">visual:</td>
<td style="padding:13px;">
<div id="contents_graph"
     style="width:100%;
            color:#ffffff;
            border:0px solid black;text-align:center;">
  <canvas id="contents_graph_chart" style="width:100%;height:200px;"></canvas>
</div>
</td></tr>
<tr style="background-color:#0707f2;">
<td valign="top" style="color:#ffffff;padding:13px;
                        background-color:#010195;
                        border-right:1px solid black;">collection:</td>
<td style="padding:13px;">
<div id="contents_summary"
     style="width:100%;
            color:#ffffff;
            border:0px solid black;">
  here the summary (IPs seen, Ports messed, ...) <br>
</div>
</td></tr><tr style="background-color:#0202b8;">
<td valign="top" style="color:#ffffff;padding:13px;
                        background-color:#010195;
                        border-right:1px solid black;">response:</td>
<td style="padding:13px;">
<div id="contents_response"
     style="width:100%;height:100px;
            border:0px solid black;
            background-color:#ffffff;overflow:scroll;"></div>
</td></tr>
<tr style="background-color:#0202b8;height:100%;">
<td valign="top" style="color:#ffffff;padding:13px;
                        background-color:#010195;height:100%;
                        border-right:1px solid black;"></td>
<td style="padding:13px;"></td>
</tr></table>
</body>
</html>
