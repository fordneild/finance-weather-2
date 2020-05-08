 <head>
	<title>Show Raw Scores For One Student</title>
 </head>
 <body>
 <?php


function outputResultsTableHeader() {
    echo "<tr>";
    echo "<th> SYMBOL </th>";
    echo "<th># DAYS</th>";
    echo "<th> PERCENT GAIN </th>";
    echo "</tr>";
}


// Open a database connection
// The call below relies on files named open.php and dbase-conf.php
// It initializes a variable named $mysqli, which we use below
include 'open.php';

// Configure error reporting settings
ini_set('error_reporting', E_ALL); // report errors of all types
ini_set('display_errors', true);   // report errors to screen (don't hide from user)

// Collect the data input posted here from the calling page
// The associative array called S_POST stores data using names as indices
$TEMP = $_POST['TEMP'];
if($_POST['RAIN']){
    $RAIN = "FORECAST.Precipitation > 0";
}else{
    $RAIN = "FORECAST.Precipitation = 0";
}

if($_POST['LIMIT']){
    $LIMIT = $_POST['LIMIT'];
}else{
    $LIMIT = 10;
}

// Call the stored procedure named ShowRawScores
// "multi_query" executes given (multiple-statement) MySQL query
// It returns true if first statement executed successfully; false otherwise.
// Results of first statement are retrieved via $mysqli->store_result()
// from which we can call ->fetch_row() to see successive rows
if ($mysqli->multi_query("SELECT TRADES.symbol, COUNT(TRADES.OpenPrice), AVG((100*(TRADES.ClosePrice - TRADES.OpenPrice))/TRADES.OpenPrice) AS percent_gain FROM FORECAST INNER JOIN TRADES ON TRADES.dateID = FORECAST.dateID WHERE ".$RAIN." AND FORECAST.MaxTemp > ".$TEMP." AND FORECAST.MinTemp < ".$TEMP." GROUP BY symbol ORDER BY percent_gain DESC;")) {


    // Check if a result was returned after the call
    if ($result = $mysqli->store_result()) {

        $numResults = mysqli_num_rows($result);
        if($numResults>0) {
        
            echo "<table border=\"1px solid black\">";
            $row = $result->fetch_row();

            // If the first row of result begins with 'ERROR: ', then our
            // stored procedure produced a relation that indicates error(s)
            if (strcmp($row[0], 'ERROR: ') == 0) {
                echo "<tr><th> Result </th></tr>";
                do {
                    echo "<tr><td>" ;
                    for($i = 0; $i < sizeof($row); $i++){
                        echo $row[$i];
                    }
                    echo "</td></tr>";
                } while ($row = $result->fetch_row());

            // Otherwise, we received real results, so output table
            } else {
                
                // Output appropriate table header row
                outputResultsTableHeader();
                $counter = 0;
                // Output each row of resulting relation
                do {
                    $counter = $counter + 1;
                    echo "<tr>";
                    for($i = 0; $i < sizeof($row); $i++){
                        echo "<td>" . $row[$i] . "</td>";
                    }
                    echo "</tr>";

                    if($counter>$LIMIT){
                        break;
                    }
                } while ($row = $result->fetch_row());
            }
            echo "</table>";
            $result->close();
        }else{
            echo "<h1>We do not have data for that date!</h1>";
        }
    }

// The "multi_query" call did not end successfully, so report the error
// This might indicate we've called a stored procedure that does not exist,
// or that database connection is broken
} else {
        printf("<br>Error: %s\n", $mysqli->error);
}

// Close the connection created above by including 'open.php' at top of this file
mysqli_close($mysqli);


 ?>
 </body>

