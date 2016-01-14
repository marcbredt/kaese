<!DOCTYPE html>
<html>
<head>
  <script src="js/communication.js"></script>
</head>
<body>

<div id="stats_usage_div"
     style="width:100%;margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;border:1px solid black;">
usage: <br>
  # filter - "FKEY:FVAL[;FKEY:FVAL]*" <br> 
  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
  where FKEY is one of { proto; port; ifin; ifout; src; dst; chain } <br>
  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
  and FVAL is a corresponding value, e.g { tcp; 80,443; eth0; eth1; 0.0.0.0/0; 0.0.0.0/0; OUTPUT }
</div>

<div id="stats_form_div"
     style="width:100%;margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;border:1px solid black;">
  <form name="stats_form" id="stats_form">
    <table style="margin:0px 0px 0px 0px;">
      <tr>
        <td>type:</td>
        <td>
          <select name="stats_type">
            <option>command</option>
            <option>logfile</option>
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
            <option>packets</option>
            <option>bytes</option> <!-- LEN -->
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

<div id="contents_refresh"
     style="width:100%;margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;border:1px solid black;">
  running = <span id="span_stats_status"></span> <br>
  interval = <span id="span_stats_interval"></span> <br>
  filter = <span id="span_stats_filter"></span> <br>
  packets/total = <span id="span_stats_num_packets_in_total"></span> <br>
  bytes/total = <span id="span_stats_num_bytes_in_total"></span> <br>
  packets/interval = <span id="span_stats_num_packets_per_interval"></span> <br>
  bytes/interval = <span id="span_stats_num_bytes_per_interval"></span>
</div>

<div id="contents_graph"
     style="width:100%;margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;border:1px solid black;">
  here the graph <br>
</div>

<div id="contents_command"
     style="width:100%;height:100px;margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;border:1px solid black;
            overflow:scroll;"></div>

<div id="contents_logfile"
     style="width:100%;height:100px;margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;overflow:scroll;
            border:1px solid black;"></div>

</body>
</html>
