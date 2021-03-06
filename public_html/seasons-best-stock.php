<head>
	<title>Show Prices</title>
</head>
<body>
<?php
function outputResultsTableHeader() {
    echo "<tr>";
    echo "<th> Company Name </th>";
    echo "<th> Symbol </th>";
    echo "<th> Avg Gain </th>";
    echo "<th> Volume Traded in Season </th>";
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
$Seasons = $_POST['Seasons'];
$rSeason = "initialized";
$Year = $_POST['Year'];

//echo "<p>".$Seasons."</p>";
// Call the stored procedure named ShowRawScores
// "multi_query" executes given (multiple-statement) MySQL query
// It returns true if first statement executed successfully; false otherwise.
// Results of first statement are retrieved via $mysqli->store_result()
// from which we can call ->fetch_row() to see successive rows
//Multiple queries to be called.
if ($Seasons == "Winter") {
    $rSeason = "SELECT ID AS DateID FROM DATES WHERE MM = 12 OR MM = 1 OR MM = 2 AND YY = ".$Year."";
}
if ($Seasons == "Summer") {
    $rSeason = "SELECT ID AS DateID FROM DATES WHERE MM = 6 OR MM = 7 OR MM = 8 AND YY = ".$Year."";
    
}
if ($Seasons == "Spring") {
    $rSeason = "SELECT ID AS DateID FROM DATES WHERE MM = 3 OR MM = 4 OR MM = 5 AND YY = ".$Year."";
}
if ($Seasons == "Fall") {
    $rSeason = "SELECT ID AS DateID FROM DATES WHERE MM = 9 OR MM = 10 OR MM = 11 AND YY = ".$Year."";
}






//second query is to find all the stock prices for those dates and avergae them.
$sql= "SELECT CompanyName, SECURITIES.Symbol, Gain, Volumes FROM SECURITIES JOIN(SELECT Symbol, AVG(100*(TRADES.ClosePrice - TRADES.OpenPrice)/TRADES.OpenPrice) AS Gain, Sum(Volume) AS Volumes FROM TRADES WHERE DateID IN (".$rSeason.") GROUP BY Symbol)AS Calc on SECURITIES.Symbol = Calc.Symbol ORDER BY Gain DESC;";
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