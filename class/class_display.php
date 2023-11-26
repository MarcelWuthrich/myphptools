<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>my PHP Tools</title>
    <link rel="stylesheet" href="../style/style.css" type="text/css">
</head>

<body >

<?php

class cl_display

{

    public function display_tracking_user_day($myarray) {
        
        try {

            echo '<BR><BR>';

            $myarray_count = count($myarray);

            echo '<BR><BR>';
            echo '<table border="1" cellspacing="0" cellpading="3">';
            echo '<tr>';
            
            echo '<th>trk_id</th>';
            echo '<th>trk_booking_date_time</th>';
            echo '<th>trk_action</th>';
            echo '<th>trk_created_by</th>';
            echo '<th>trk_created_date</th>';
            echo '<th>trk_modified_by</th>';
            echo '<th>trk_modified_date</th>';
            echo '</tr>';


            for ($i = 0; $i < $myarray_count; $i++) {
                echo '<tr>';
                echo '<td>' . $myarray[$i]['trk_id'] . '</td>';
                echo '<td>' . $myarray[$i]['trk_booking_date_time'] . '</td>';
                switch($myarray[$i]['trk_action']) {

                    case 210:
                        echo '<td>Stop Activity</td>';
                        break;
            
                        case 240:
                            echo '<td>Start Activity</td>';
                            break;
                
                            case 254:
                                echo '<td>Time out</td>';
                                break;
                    
                                case 255:
                                    echo '<td>Time in</td>';
                                    break;
                        
                                                
                }
                echo '<td>' . $myarray[$i]['trk_created_by'] . '</td>';
                echo '<td>' . $myarray[$i]['trk_created_date'] . '</td>';
                echo '<td>' . $myarray[$i]['trk_modified_by'] . '</td>';
                echo '<td>' . $myarray[$i]['trk_modified_date'] . '</td>';
                echo '</tr>';    
            }
            echo '</table>';

        }
        catch (exception $e) {
            echo 'Error' .  $e->getMessage() . '<BR>';
        }
        return $myarray;
    }
    public function displayFromArray($myarray) {
    
        
        try {
            echo '<BR><BR>';

            //print_r($myarray);

            foreach ($myarray as $toto) {
                foreach ($toto as $titi) {
                    $titi=$titi;
                }
                
            }
            /*
            foreach ($myarray as $key => $value) {
                echo $key;
                echo $value;
            }
            */
            var_dump($myarray);

            echo '<BR><BR>';
            echo '<table>';
            echo '<tr>';
            
            echo '<th>columns1</th>';
            echo '<th>columns2</th>';
            echo '<th>columns3</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>value 1</td>';
            echo '<td>value 2</td>';
            echo '<td>value 3</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>value a</td>';
            echo '<td>value b</td>';
            echo '<td>value c</td>';
            echo '</tr>';
            echo '</table>';
            $myarray=$myarray;
            //$sth = null;
            //$dbh = nulll;
        }
        catch (exception $e) {
            echo 'Error' .  $e->getMessage() . '<BR>';
        }
        
        return $rows;
    }

    

        


}
    

?>

</body>
</html>