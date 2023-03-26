<?php
define("IN_CODE", 1);
include "dbconfig.php";

if (!isset($_COOKIE["customer_id"])) {
    echo "Please Login First";
    echo "<br><a href=\"index.html\">Back To Home</a>";
    die;
} else { 
    echo "<a href=\"logout.php\">Logout</a>";

    $con = mysqli_connect($host, $username, $password, $dbname)
        or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
    if (!isset($_POST['total_balance']) || !isset($_POST['code']) || !isset($_POST['type']) || !isset($_POST['amount']) || !isset($_POST['source_id']) || !isset($_POST['note'])) {
        echo "<br>Please Check Form For Completion";
    } else {
        $total_balance = (int)$_POST['total_balance'];
        $id = $_COOKIE['customer_id'];
        $transaction_code = $_POST['code'];
        $transaction_type = $_POST['type'];
        $transaction_amount = (int)$_POST['amount'];
        $transaction_source = $_POST['source_id'];
        $transaction_timestamp = 'CURRENT_TIMESTAMP';
        $note = $_POST['note'];
        $insufficient_indicator =  $total_balance - $transaction_amount; //variable indicates if a withdraw would become negative
    

    if ($transaction_amount <= 0 OR ($transaction_type == "D" AND $transaction_amount <= 0)) {
        echo "<br>Negative Values Are Not Permitted, Please Try Entering A Non Negative Number And Checking 'DEPOSIT' For Negative Values";
        die;
    } else {
        
        $insertion = "INSERT INTO CPS3740_2022F.Money_alviolai VALUES (NULL, '$transaction_code', $id, '$transaction_type', $transaction_amount, $transaction_timestamp, '$note', $transaction_source)";

        if($transaction_type=="W" AND  $insufficient_indicator <= 0){
            echo "<br>Unable To Withdraw, Insefficient Funds";
            die;
        }
        else{
            $result = mysqli_query($con, $insertion);
            // Perform a query, check for duplicate code
            if (mysqli_errno($con) == 1062) {
                echo ("<br>Error description: A Code already exists with that name." );
            }else{
                echo "<br>Successfully Added!";
                if($transaction_type == "W"){
                    $new_balance = $total_balance - $transaction_amount;
                    echo "<br> New Balance: $new_balance";
                }elseif($transaction_type == "D"){
                    $new_balance = $total_balance + $transaction_amount;
                    echo "<br> New Balance: $new_balance";
                }
            }
            
        
        }
    }
}
}
mysqli_close($con);
?>