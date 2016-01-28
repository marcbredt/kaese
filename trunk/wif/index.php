<?php session_start(); ?>
<!DOCTYPE html >
<html>

<head>
  <!-- libs -->
  <script src="lib/js/jquery/2.1.4/jquery-2.1.4.min.js"></script>
  <!-- scripts -->
  <script src="js/navigation.js"></script>
</head>

<body style="margin:0px;padding:0px;font-family:Verdana,Helvetica;
             font-size:12px;height:100%">

  <div name="main" id="main" 
       style="width:100%;height:100vh;margin:0px;padding:0px;border:0px solid orange;"> 

  <div name="navigation" id="nav" 
       style="width:100%;height:40px;margin:0px 0px 0px 0px;padding:0px 0px 0px 0px;
              border-bottom:1px solid black;background-color:#010195;">
    <table style="margin:0px;padding:10px;height:100%;border:0px solid green;">
      <tr>
        <td><a id="a_home" style="color:#ffffff;font-weight:bold;" href="home">home</a></td>
        <td><a id="a_config" style="color:#ffffff;font-weight:bold;" href="config">config</a></td>
        <td><a id="a_stats" style="color:#ffffff;font-weight:bold;" href="stats">stats</a></td>
        <td><a id="a_about" style="color:#ffffff;font-weight:bold;" href="about">about</a></td>
      </tr>
    </table>
  </div>

  <div name="contents" id="contents" 
       style="width:100%;height:100%;margin:0px;padding:0px;border:0px solid yellow;"> 
  </div>

  </div>

</body>

</html>
