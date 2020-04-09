<?php
  $f = fopen("uas_register_27775_counts.csv", "r");
  $values = array();

  while(!feof($f))
  {
      $line = fgets($f);
      if(strpos($line, "Current") !== FALSE )
      {
          $columns = explode(";", $line);
      }
      else
      {
          $tempValues = explode(";", $line);
          for($i = 2; $i < sizeof($tempValues)-1; $i += 1)
          {
              $values[$i] += $tempValues[$i];
          }
      }
  }
  fclose($f);
  for($i = 2; $i < sizeof($values)-1; $i += 1)
  {
      echo $columns[$i].": ".$values[$i]."<br>";
  }



  
  $f = fopen("uas_register_3404_.csv", "r");
  $values = array();

  while(!feof($f))
  {
      $line = fgets($f);
      if(strpos($line, "Current") !== FALSE )
      {
          $columns = explode(";", $line);
      }
      else
      {
          $tempValues = explode(";", $line);
          for($i = 8; $i < 18; $i += 1)
          {
              $values[$i] += $tempValues[$i];
          }
      }
  }
  fclose($f);
  for($i = 8; $i < 18; $i += 1)
  {
      if($columns[$i] === "TotalCallCreated")
      {
          echo "Total Calls Created: ".$values[$i]."<br>";
      }
      else if($columns[$i] === "SuccessfulCall(C)")
      {
          echo "Successful Calls: ".$values[$i]."<br>";
      }
      else if($columns[$i] === "FailedCall(C)")
      {
          echo "Failed Calls: ".$values[$i]."<br>";
      }
  }
?>