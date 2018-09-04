<?php
  session_start();
  session_destroy();
  require_once('includes/config.php');
  include_layout_template('layouts/header.php');

  if(isset($_POST['submit'])) {
    $tmp_file = $_FILES['file_upload']['tmp_name'];
    $target_file = basename($_FILES['file_upload']['name']);
    $upload_dir = "uploads";
    $types = array(
      'Small Integer' => 'SMALLINT',
      'Integer' => 'INTEGER(11)',
      'Float' => 'FLOAT',
      'Decimal' => 'DECIMAL(12,2)',
      'Timestamp' => 'TIMESTAMP',
      'Time' => 'TIME',
      'Year' => 'YEAR',
      'Date' => 'DATE',
      'Character' => 'CHARACTER(10)',
      'Variable Character' => 'VARCHAR(255)'
    );

      //Example array
 $examples = array(
        'Small Integer' => '1',
        'Integer' => '42868892',
        'Float' => '50.12345',
        'Decimal' => '50.56',
        'Timestamp' => 'Tue 01-01-2009 6:00',
        'Time' => '15:00',
        'Year' => '2015',
        'Date' => '01/01/2000',
        'Character' => 'abcd',
        'Variable Character' => '26 Smith Rd, Sydney'
    );

?>

<!-- Bo appears when clicked -->

<div id="pop_background"> </div>

<div id="pop_box">
    <!-- <span id="close">&times;</span> -->

    <!-- Enter text here for popup box: -->
    <center>
    <?php
        echo "<table style='left:25px'>";
        echo "<tr>";
            echo "<td style='font-family: Helvetica Neue; font-size: 15px; font-weight: 300'>Data Type:</td>";
            echo "<td style='font-family: Helvetica Neue; font-size: 15px; font-weight: 300'>Example:</td>";
        echo "</tr>";
        foreach($examples as $x => $x_value) {
            echo "<tr>";
                echo "<td style='white-space: nowrap;'>";
                    echo $x;
                echo "</td>";
                echo "<td style='white-space: nowrap;'>";
                    echo $x_value;
                echo "</td>";
            echo "</td>";
        }
    echo "</table>";

?>
    </center>
</div>


<?php
    if(move_uploaded_file($tmp_file, $upload_dir."/".$target_file)) {
      if($handle = fopen($target_file, 'r')) {
        // echo $target_file;
?>
        <h1>2. Create your table:</h1>
        <div class='create_form'>
        <form action='query.php' class='' method='post' name='form'>
          <table>
            <tr>
              <th style='padding-top: 7px; padding-bottom: 7px'>Attribute</th>
              <th style='padding-top: 7px; padding-bottom: 7px'>Data Type</th>
            </tr>
          <?php
            while($data = fgetcsv($handle, 1000, ",")) {
              $num = count($data);
              for($i=0; $i < $num; $i++) {
                echo "<tr>";
                  echo "<td><input type='text' name='field_".$i."' value='".$data[$i]."'/></td>";
                  echo "<td>";
                    echo "<select name='type_".$i."'>";
                    foreach($types as $key => $value) {
                      echo "<option value='".$value."' selected>" .$key. "</option>";
                    }
                    echo "</select>";
                  echo "</td>";
                echo "</tr>";
              }
              break;
            }
          ?>
          </table>
          <br />
          <table class='choose_data'>
            <tr id='table_name'>
              <td><a href="#" id="open">What data type should I choose?</a></td>
              <td>Table name:&nbsp;&nbsp;<input type='text' name='table_name' value=''/></td>
            </tr>
          </table>
          <div class='upload_bottom'>
            <!-- <a href=''>What data type should I choose?</a> -->
            <input type='submit' name='submit' id='modify' value='Submit'/>
          </div>
          <input type='hidden' name='target_file' value='<?php echo $target_file; ?>'/>
      </form>
      </div>
<?php
        fclose($handle);
      }
    }
  } else {
?>
  <h1>1. Upload your file:</h1>
  <div class="box">
    <div class="h_center">
      <div class="h_big_logo"><p>h</p></div>
      <h1 id='h_word'>Helios</h1>
      <div class="upload_box">
        <form action="file_upload.php" enctype="multipart/form-data" method="post">
          <input class="file" type="file" name="file_upload"/><input class="file" type="submit" name="submit" value="Upload"/>
        </form>
      </div>
    </div>
  </div>
<?php
  }
  include_layout_template('layouts/footer.php');
?>
