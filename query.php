<?php
  session_start();
  require_once('includes/config.php');
$num = 1;

  if(isset($_POST['submit']) || (!isset($_SESSION['table_name']))) {
    $target_file = $_POST['target_file'];
    $_SESSION['table_name'] = $_POST['table_name'];
    $table_name = $_SESSION['table_name'];
    $_SESSION['attribute_names'] = $_POST;

    $sql = "CREATE TABLE " . $table_name . " ( ";
      $i = 0;
      while ($i !== ((count($_POST)-3)/2)) {
        if(!empty($_POST['field_'.$i])) {
          $str .= $_POST['field_'.$i] . " ";
        }
        $i++;
      }
      $fields = explode(" ", rtrim($str));
      $j = 0;
      while ($j !== ((count($_POST)-3)/2)) {
        if(isset($_POST['type_'.$j])) {
          $str2 .= $_POST['type_'.$j] . " ";
        }
        $j++;
      }
      $types = explode(" ", rtrim($str2));
      for($k=0; $k <= $j; $k++) {
        if($k < $j-1) {
          $sql .= $fields[$k] . " " . $types[$k] . ", ";
        } else {
          $sql .= $fields[$k] . " " . $types[$k] . " ";
        }
      }
    $sql .= "); ";
    $load = "LOAD DATA LOCAL INFILE '" . $target_file . "' INTO TABLE " . $table_name . " FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 ROWS;";
    // echo $sql;
    // echo $load . "<br />";

    $result = $db->query($sql);
    $result .= $db->query($load);

    /* preliminary insights */

    $attrs=array();
    for($k=0; $k <= count($_POST)/2; $k++) {
      if($types[$k] == 'DECIMAL(12,2)' || $types[$k] == 'FLOAT' || $types[$k] == 'INTEGER(11)' || $types[$k] == 'SMALLINT') {
        $arr = array($fields[$k] => $types[$k]);
          //Pushing all relevant attributes into array
         array_push($attrs,$fields[$k]);
        if(!empty($fields[$k])) {
          $array .= $fields[$k] . ",";
        }
        $psql .= "SELECT ";
        foreach($arr as $field => $type) {
          $psql .= "AVG($field), MIN($field), MAX($field), STD($field) ";
        }
        $psql .= "FROM $table_name;";
      }
    }
    //$attrs = explode(",", rtrim($array, ","));
    $pqrys = explode(";", rtrim($psql, ";"));

      //Create these sessions
    $_SESSION['array_attrs']=$attrs;
    $_SESSION['psql'] = $pqrys;

    // echo "<br> Table: ". $table_name. " has been created with values from ". $target_file;
  } else {
    // echo "Session: ".$_SESSION['table_name']. " has not been destroyed";
  }
?>

<?php include_layout_template('layouts/header.php'); ?>

  <form action="result.php" method="post">
    <h1>3. Enter your query:</h1>
    <div class='query_table'>
      <table>
        <tr>
          <td><h2>1. Select dimensions to display:</h2></td>
          <td>
            <p id="skyblue"><a href="#" onclick="toggle_visibility('prelim')" style="float: right">PRELIMINARY INSIGHTS</a></p>
          </td>
        </tr>
        <tr>
          <td>
            <table id='dimensions'>
              <tr>
                <td>Dimension</td>
                <td>Attribute</td>
                <td>Function</td>
              </tr>
              <?php
                for($x=1; $x<=$num; $x++) {
                  echo "<tr>";
                    echo "<td>" . $x . "</td>";
                    echo "<td><select name='attribute_{$x}'>"; attrs(); echo "</select></td>";
                    echo "<td><select name='function_{$x}'>"; ftns(); echo "</select></td>";
                    echo "<td id='td_plus' style='border: 0 none;'>";
                ?>
                  <div id="a_circle"><a id="plus" href='#' onclick='my_function()'>+</a></div>
                <?php
                  echo "</td>";
                  echo "</tr>";
                }
              ?>
            </table>

              <!-- JS for additonal attribute  -->
            <script type ="text/javascript">
              var rowCount = <?php echo $num; ?>;
              function my_function() {
                  var table = document.getElementById("dimensions");
                  var row = table.insertRow(rowCount+1);
                  var data1 = row.insertCell(0);
                  var data2 = row.insertCell(1);
                  var data3 = row.insertCell(2);
                  data1.innerHTML = rowCount+1;
                  data2.innerHTML = "<select name='attributes[]'> <?php attrs(); ?></select>";
                  data3.innerHTML = "<select name='functions[]'> <?php ftns(); ?></select>";
                  rowCount++;
              }
            </script>


          </td>
          <td>
            <div id="prelim" style="display: none">
              <table>
                <tr>
                  <td>&nbsp;</td>
                  <td>Average</td>
                  <td>Minimum</td>
                  <td>Maximum</td>
                  <td>Std. Dev.</td>
                </tr>
                <?php

                $pqrys=$_SESSION['psql'];
                $attrs=$_SESSION['array_attrs'];
                  $e = 0;
                  while($e < count($attrs)) {
                    echo "<tr>";
                      echo "<td>" . $attrs[$e] . "</td>";
                      if($res = $db->query($pqrys[$e])) {
                        foreach($res->fetch_row() as $val) {
                          echo "<td>" . number_format($val, 0) . "</td>";
                        }
                      }
                    echo "</tr>";
                    $e++;
                  }
                ?>
              </table>
            </div>
          </td>
        </tr>
        <tr>
          <td><h2>2. Enter any conditionals to concentrate the results:</h2></td><td></td>
        </tr>
        <tr>
            <td>
            <table id='conditions'>
                <tr>
                    <td>Where:</td>
                    <td><select name='where_select'><?php attrs(); ?></select></td>
                    <td><input type='text' name='where_text' value=''/></td>
                </tr>
                <tr>
                    <td>Grouped by:</td>
                    <td><input type='text' name='group_by'/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Having:</td>
                    <td><input type='text' name='having'/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Ordered by:</td>
                    <td><input type='text' name='order_by'/></td>
                    <td><input type='checkbox' name='desc' value='DESC'/> Descending</td>
                </tr>
                <tr>
                    <td>Top:</td>
                    <td><input type='text' name='limit'/></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
          </td>
          <td></td>
        </tr>
        <tr>
          <td colspan='2'><h2>3. Describe your query:</h2></td>
        </tr>
        <tr>
          <td style='left: 1.5%'><input type='text' style='width: 400px;' autocomplete='off' name='query_desc' value=''/></td>
          <td></td>
        </tr>
        <tr>
          <td><input type='reset' name='reset' value='Reset'/></td>
          <td><input type='submit' name='submit' value='Submit'/></td>
          <input type='hidden' name='num' value='<?php echo $num; ?>'/>
          <input type='hidden' name='table_name' value='<?php echo $table_name; ?>'/>
        </tr>
      </table>
    </div>
  </form>
<?php include_layout_template('layouts/footer.php');?>
