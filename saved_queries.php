<?php
  session_start();
  require_once('includes/config.php');
  include_layout_template('layouts/header.php');
        // echo "<a href='query.php'> &laquo Create another query </a>" ;


  if(isset($_GET['id'])) {
    /* show the results of the selected query */
    $id = $_GET['id'];
    $sql = "SELECT query, query_desc, pairs FROM queries WHERE id = '$id'";
    if($result = $db->query($sql)) {
      while($row = $result->fetch_row()) {
      $sql2 = $row[0];
      $desc = $row[1];
      $pairs = explode(";", rtrim($row[2],";"));
      }
    }

    if($result2 = $db->query($sql2)) {
      echo "<h1>Results for '" . $desc . "':</h1>";
      echo "<div class='result_table'>";
      echo "<table>";
        echo "<tr>";
        echo "<td>&nbsp;</td>";
        foreach($pairs as $pair) {
          echo "<td>" . $pair . "</td>";
        }
        echo "</tr>";

      while($row = $result2->fetch_row()) {
        echo "<tr>";
        echo "<td>" . (1 + $result->current_field++) . "</td>";
        foreach($row as $row) {
          if(is_numeric($row)) {
            echo "<td>" . number_format($row, 0) . "</td>";
          } else {
            echo "<td>" . $row . "</td>";
          }
        }
        echo "</tr>";
      }
      echo "</table>";
      echo "</div>";
    }
  } else {
    /* show all the saved queries */
      $table_name = $_SESSION['table_name'];
      $sql = "SELECT id, query, query_desc FROM queries WHERE table_name = '$table_name'";

      if($result = $db->query($sql)) {
        echo "<h1>Saved queries for table: {$table_name}</h1>";
        echo "<ul class='saved_list'>";
          while($row = $result->fetch_row()) {
            if(!empty($row)) {
              echo "<li><a href='saved_queries.php?id=$row[0]'>" . $row[2] . "</a></li>";
            }
          }
        echo "</ul>";
      }
    }


  include_layout_template('layouts/footer.php');

?>
