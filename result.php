<?php
  session_start();


//Defining attributes
$attributes_array=array();
$functions_array=array();
$num=$_POST['num'];
$number=$num+1;

if(isset($_POST['attributes'])) {
    for($i=0;$i < count($_POST['attributes']);$i++){
        $attributes_array['attribute_' . $number] = $_POST['attributes'][$i];
        $function_array['function_'. $number]=$_POST['functions'][$i];
        $number++;
    }
    $combined=array_merge($_POST,$attributes_array,$function_array);
    $row_numbers=$num+count($_POST['attributes']);
} else {
    $combined=$_POST;
    $row_numbers=$num;
}

//NOTE: Change '$num' to '$row_numbers' below
//NOTE: Change "$_POST['attribute_'.$i]" and "$_POST['function_'.$i]" to "$combined['attribute_'.$i]" and "$combined['function_'.$i]" below.

  require_once('includes/config.php');
  if(isset($_POST['submit'])) {
    $table_name = $_SESSION['table_name'];
    $query_desc = $_POST['query_desc'];
    $sql = "SELECT ";
      for($i=1; $i<=$row_numbers; $i++) {
        $sql .= $combined['function_'.$i] . "(";
        $sql .= $combined['attribute_'.$i];
        if($i<$row_numbers && isset($_POST['plus_'.$i])) {
          $sql .= ") + ";
        }
        elseif($i<$row_numbers && !isset($_POST['plus_'.$i])) {
          $sql .= "), ";
        }
        else {
          $sql .= ") ";
        }
      }
    $sql .= "FROM {$table_name}";
      if(!empty($_POST['where_select'] && $_POST['where_text'])) {
        $sql .= " WHERE " . $_POST['where_select'] . " " . $_POST['where_text'];
      }
      if(!empty($_POST['group_by'])) {
        $sql .= " GROUP BY " . $_POST['group_by'];
      }
      if(!empty($_POST['having'])) {
        $sql .= " HAVING " . $_POST['having'];
      }
      if(!empty($_POST['order_by'])) {
        $sql .= " ORDER BY " . $_POST['order_by'];
        if(isset($_POST['desc'])) {
          $sql .= " DESC";
        }
      }
      if(!empty($_POST['limit'])) {
        $sql .= " LIMIT " . $_POST['limit'];
      }
    $sql .= ";";

    $e = 0;
    while($e <= $row_numbers) {
      if(!empty($combined['attribute_'.$e])) {
        $arr .= $combined['attribute_'.$e] . ":";
        if(!empty($combined['function_'.$e])) {
          $arr .= $combined['function_'.$e] . ";";
        } else {
          $arr .= ";";
        }
      }
      $e++;
    }
      // echo $sql . "<br />";
      // echo $table_name . "<br />";
      // echo $query_desc . "<br />";
      // echo $arr;
  include_layout_template('layouts/header.php');
  echo "<h1>4. View your results:</h1>";

  if($result = $db->query($sql)) {
    if($query = $db->query("INSERT INTO queries (query, table_name, owner, query_desc, pairs) VALUES ('$sql','$table_name', 'admin', '$query_desc', '$arr')")) {
      echo "<h3>Your query: <strong>" . $query_desc . "</strong> was successfully added to table: <strong>" . $table_name . "</strong></h3>";
    }
?>


    <div class='result_table'>
      <table>
        <tr>
          <td>&nbsp;</td>
<?php
        // echo "<a href='query.php'> &laquo Create another query </a>" ;
          for($i=1; $i<=$row_numbers; $i++) {
            echo "<td>" . $combined['function_' . $i] . "(" . $combined['attribute_' . $i] . ")" . "</td>";
          }
        echo "</tr>";
        while($row = $result->fetch_row()) {
          echo "<tr>";
            echo "<td>" . (1 + $result->current_field++) . "</td>";
            for($i=0; $i<$row_numbers; $i++) {
              if(is_numeric($row[$i]) && strlen($row[$i]) > 4) {
                echo "<td>" . number_format($row[$i], 0) . "</td>";
              } else {
                echo "<td>" . $row[$i] . "</td>";
              }
            }
          echo "</tr>";
        }
        echo "</table>";
      // echo "</div>";
    }
    include_layout_template('layouts/footer.php');
  }
?>
