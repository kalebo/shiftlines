<?php
// Set the desired constants for when the office opens
// and closes and how many periods should be shown per hour  (PPH).
define('PPH', 1);
define('START', 8); 
define('END', 17);

$usernames = array(); // simple array of all usernames found in dir (lexical file order)
$usershifts = array(); // array[name] -> array[day] -> periods
$shifttables = array(); // array[name] -> array[day] -> htmltable

/**
 * @param array $hours representing the shift schedule of a worker
 * @param int $pph periods per hour
 * @param int $start start of workday
 * @param int $end end of the workday
 * @return an array of 1s and 0s matching the number of periods a worker will working during the day
*/
function maphours($hours, $pph, $start, $end){
    $buffer = array_fill(0, $end - $start, 0);
    foreach($hours as $period){
        if ($period[0] < $start) $period[0] = $start;
        if ($period[1] > $end) $period[1] = $end;
        for ($i = $period[0] * $pph; $i < $period[1] * $pph; $i++){
            for ($j = 0; $j < $pph; $j++){
                $buffer[$i - $start * $pph] = 1;
            }
        }
    }
    return $buffer;
}
/**
 * @param array $input generated by maphours
 * @param int $id determines the order workers and their colors
 * @return string with html tables
*/
function array2table($input, $id){
    $output = "";
    $output = $output . "<table class=\"shift\" >";
    foreach($input as $moment){
        if ($moment == 1){
            $output = $output . "<tr><td id=\"worker$id\"/></tr>";
        }
        else {
            $output = $output . "<tr><td/></tr>";
        }
    }
    $output = $output . "</table>";
    return $output;
}

$shiftfiles = scandir("shifts/");
$shiftfiles = array_diff($shiftfiles, array('.', '..')); // filters out relative unix dirs
chdir("shifts/");
foreach($shiftfiles as $file){
    $rawfile = file_get_contents($file);
    $jsonobj = json_decode($rawfile, true);
    $usernames[] = $jsonobj["Name"];
    $usershifts[$jsonobj["Name"]] = $jsonobj["Shift"];
}

$id = 1;
foreach($usershifts as $username => $days){
    foreach($days as $day => $hours){
        $shifttables[$username][$day] = array2table(maphours($hours, PPH, START, END), $id );
    }
    $id++;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>CSR Shift Scheduals</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <link rel="stylesheet" href="css/custom.css">

  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="favicon.png">

</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
    <div class="container" style="margin-top: 15%">
        <h4>Shift Schedules</h4>
        <section class="matrix">
        <div class="row">
                <div class="one columns">
                <div class="header" style="background:none" ></br></div>
                    <table>
                      <?php
                         for ($hour = START; $hour <END; $hour+= 1/PPH){
                            if (fmod($hour, 1) == 0 ){
                                echo "<tr><td>{$hour}h00</td></tr>";
                            }
                            else {
                                echo "<tr><td></td></tr>";
                            }
                         }

                         ?>
                    </table>
                </div>

                <div class="two columns">
                <div class="header" >Monday</div>
                    <div class="container daily" style="text-transform:none"> 
                    <?php
                        foreach($shifttables as $user){
                            echo $user["Monday"];
                        }
                    ?>
                    </div>
                </div>

                <div class="two columns">
                <div class="header" >Tuesday</div>
                    <div class="container daily" style="text-transform:none">
                    <?php
                        foreach($shifttables as $user){
                            echo $user["Tuesday"];
                        }
                    ?>
                    </div>
                </div>

                <div class="two columns">
                <div class="header" >Wednesday</div>
                    <div class="container daily" style="text-transform:none">
                    <?php
                        foreach($shifttables as $user){
                            echo $user["Wednesday"];
                        }
                    ?>
                    </div>
                </div>
                <div class="two columns">
                <div class="header" >Thursday</div>
                    <div class="container daily" style="text-transform:none">
                    <?php
                        foreach($shifttables as $user){
                            echo $user["Thursday"];
                        }
                    ?>
                    </div>
                </div>
                <div class="two columns">
                <div class="header" >Friday</div>
                    <div class="container daily" style="text-transform:none">
                    <?php
                        foreach($shifttables as $user){
                            echo $user["Friday"];
                        }
                    ?>
                    </div>
                </div>
            </div>
        </section>
      </div>
        <div class="row" id="status">
                                 <div class="offset-by-two eight columns ">
<?php
                $id = 1;
                foreach($usernames as $user){
                    if ($id % 3 == 1){echo "<div class='row'>";}
                    echo "<div class='one-third column'><table style='width:100%'><tr><td id='worker$id' style='width:4%'/><td class='crop name'>$user</td></tr></table></div>";
                    if ($id % 3 == 0){echo "</div>"; }
                    $id++;
                }
if (($id - 1) % 3 !=  0){echo "</div>";}
?>
                                 </div>

        </div>

    </div>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>
