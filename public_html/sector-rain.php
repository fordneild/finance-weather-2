<head>
	<title>Show Prices</title>
</head>
<body>
<?php
function outputResultsTableHeader() {
    echo "<tr>";
    echo "<th> Rainfall (inches) </th>";
    echo "<th> AVG Gain </th>";
    echo "<th> Total Volume Traded</th>";
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
$Sector = $_POST['Sector'];
if(isset($_POST['RAIN'])){
    $RAIN = "FORECAST.Precipitation > 0";
}else{
    $RAIN = "FORECAST.Precipitation = 0";
}


$sql = "SELECT RAIN, AVG((100*(PRICES.ClosePrice - PRICES.OpenPrice)/PRICES.OpenPrice)), SUM(Volume) FROM SECURITIES JOIN (SELECT Symbol, OpenPrice, ClosePrice, Volume, RAIN FROM TRADES JOIN (SELECT DateID, Precipitation AS RAIN FROM FORECAST WHERE ".$RAIN.")AS WEATHER ON (TRADES.DateID = WEATHER.DateID)) AS PRICES ON (SECURITIES.Symbol = PRICES.Symbol) WHERE SECURITIES.SectorID in (SELECT ID AS SectorID FROM SECTOR WHERE SectorName = '".$Sector."') GROUP BY RAIN ORDER BY RAIN ASC;";


//$sql = "SELECT RAIN, AVG(100*Final.ClosePrice-Final.OpenPrice)/Final.OpenPrice), Sum(Volume) FROM SECTOR JOIN (SELECT * FROM SECURITIES JOIN (SELECT Symbol, OpenPrice, ClosePrice, Volume, RAIN FROM TRADES JOIN (SELECT DateID, Precipitation AS RAIN FROM FORECAST WHERE ".$RAIN.")AS WEATHER ON (TRADES.DateID = WEATHER.DateID)) AS Prices ON (SECURITIES.Symbol = Prices.Symbol)) AS Final ON (SECTOR.ID = Final.SectorID) WHERE SECTOR.SectorName = '"$.Sector."' GROUP BY RAIN ORDER BY RAIN ASC;";



//$sql= "SELECT RAIN, AVG(100*(Final.ClosePrice-Final.OpenPrice)/Final.OpenPrice), SUM(Volume) FROM (SELECT Symbol, OpenPrice, ClosePrice, Volume, SectorID, RAIN FROM (((TRADES JOIN SECURITIES ON (TRADES.Symbol = SECURITIES.Symbol)) AS Prices JOIN (SELECT DateID, Precipitation AS RAIN FROM FORECAST  WHERE ".$RAIN.") AS WEATHER ON (Prices.DateID = WEATHER.DateID)) AS PW JOIN SECTOR ON (PW.SectorID = SECTOR.ID) WHERE SECTOR.SectorName = "'.$Sector.'")) AS Final GROUP BY RAIN ORDER BY RAIN ASC;";


if ($mysqli->multi_query($sql)) {
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
                // Output each row of resulting relation
                do {
                    echo "<tr>";
                    for($i = 0; $i < sizeof($row); $i++){
                        echo "<td>".$row[$i]."</td>";
                    }
                    echo "</tr>";
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