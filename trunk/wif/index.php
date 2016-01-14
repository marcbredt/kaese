<?php session_start(); ?>
<!DOCTYPE html >
<html>

<head>
  <!-- libs -->
  <script src="lib/js/jquery/2.1.4/jquery-2.1.4.min.js"></script>
  <!-- scripts -->
  <script src="js/navigation.js"></script>
  <!--<script src="js/timer.js"></script>-->
  <!--<script src="js/update.js"></script>-->
  <!--<script src="js/graph.js"></script>-->
</head>

<body style="margin:0px;font-family:Verdana,Helvetica;font-size:12px;">

  <div name="navigation" id="nav" 
       style="width:100%;margin:0px 0px 0px 0px;padding:0px 0px 0px 0px;
                 border:1px solid black;">
    <table style="margin:0px 0px 0px 0px;">
      <tr>
        <td><a id="a_home" href="home">home</a></td>
        <td><a id="a_config" href="config">config</a></td>
        <td><a id="a_stats" href="stats">stats</a></td>
        <td><a id="a_about" href="about">about</a></td>
        <td><a id="a_license" href="license">license</a></td>
      </tr>
    </table>
  </div>

  <div name="contents" id="contents" 
       style="width:100%;margin:0px 0px 0px 0px;padding:0px 0px 0px 0px;
              border:1px solid blue;"> 
  </div>

</body>

</html>
