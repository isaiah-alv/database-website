<?php
define("IN_CODE", 1);
include "dbconfig.php";
echo "<a href=\"logout.php\">Logout</a>";

$con = mysqli_connect($host, $username, $password, $dbname)
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());


if (!isset($_COOKIE["customer_id"])) {
    echo "Please Login First";
    echo "<br><a href=\"index.html\">Back To Home</a>";
    die;
} else {
    if(!isset($_POST['total_balance'])){
        echo "Total Balance Not Set";
    }else{
        $total_balance = $_POST['total_balance'];
    }
    $name = $_COOKIE["customer_name"];
    
    $query = "SELECT name from CPS3740.Sources";
    $result = mysqli_query($con, $query);
    
    // html form 
    echo "<html>\n<body style=\"font-family: 'Trebuchet MS', sans-serif;\">";
    echo "<form name='input' action='insert_transaction.php' method='post' required='required'>";
    echo "<input type='hidden' name='customer_name' value='$name'>";
    echo "<p> <strong>$name</strong> current balance is <strong>$total_balance</strong></p>";
    echo "Transaction code: <input type='text' name='code' required='required'>";
    echo "<br><input type='radio' name='type' value='D'>Deposit";
    echo "<input type='radio' name='type' value='W'3>Withdraw";
    echo "<br> Amount: <input type='number' name='amount' required='required'>";
    echo "<br>Select a Source: <SELECT name='source_id'>";
    echo "<option value = ''> </option>";
    //while loop that iterates 'option_val' based on the number of rows in that table
    $option_val = 1;
    while ($row = mysqli_fetch_array($result)) {
        $transaction_source = $row["name"];
        echo "<option value = $option_val> $transaction_source </option>";
        $option_val++;
    }
    echo "</select>";
    echo "<BR>Note: <input type='text' name='note'>";
    echo "<input type='hidden'name='total_balance' value='$total_balance'/>";
    echo "<br><input type='submit' value='Submit'>";
    echo "</form>";   
}
echo "</body>\n</html>";
mysqli_free_result($result);
mysqli_close($con);
?>