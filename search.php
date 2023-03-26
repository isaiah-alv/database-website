<?php
define("IN_CODE", 1);
include "dbconfig.php";
echo "<a href=\"logout.php\">Logout</a>";
$con = mysqli_connect($host, $username, $password, $dbname)
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
$name = $_COOKIE["customer_name"];
$id = $_COOKIE["customer_id"];

if(!isset($_COOKIE["customer_id"])){
    echo "Please Login First!";
    die;
}else{
    if(!isset($_GET["keyword"])){
        echo "<br> Please enter a keyword";
    }else{
        $keyword = $_GET["keyword"];
        echo "<html>\n<body style=\"font-family: 'Trebuchet MS', sans-serif;\">";
        echo "<p>The transactions in the customer <strong>$name</strong> records matched keyword: <strong>$keyword</strong> are" ;
        if($keyword == "*"){
            $query = "SELECT mid AS ID, code AS Code, type AS Type, amount AS Amount, Sources.name AS Source, mydatetime AS DateTime, note AS Note FROM CPS3740_2022F.Money_alviolai inner join CPS3740.Sources ON Money_alviolai.sid = Sources.id where cid = '$id'";
        }
        else{
            $query ="SELECT mid AS ID, code AS Code, type AS Type, amount AS Amount, Sources.name AS Source, mydatetime AS DateTime, note AS Note FROM CPS3740_2022F.Money_alviolai inner join CPS3740.Sources ON Money_alviolai.sid = Sources.id where note like '%$keyword%' and cid = '$id'";
        }
        $result = mysqli_query($con, $query);
        $num_search = mysqli_num_rows($result);
        $total_balance = 0;
        if($num_search > 0){
            echo "<TABLE border = 1>";
            echo "<TR><TH>ID<TH>CODE<TH>Type<TH>Amount<TH>Source<TH>Date & Time<TH>Note";
            while ($row = mysqli_fetch_array($result)) {
                $m_id = $row["ID"];
                $transaction_code = $row["Code"];
                $transaction_type = $row["Type"];
                $transaction_amount = $row["Amount"];
                $transation_source = $row["Source"];
                $transaction_timestamp = $row["DateTime"];
                $note = $row["Note"];


                if ($transaction_type == "W") {
                    $transaction_amount = 0 - $transaction_amount;
                }
                if ($transaction_amount < 0) {
                    $color = "red";
                } else {
                    $color = "blue";
                }
                $total_balance = $total_balance + $transaction_amount;


                echo "<TR><TD>$m_id<TD>$transaction_code<TD>$transaction_type<TD><font color = '$color'>$transaction_amount<TD>$transation_source<TD>$transaction_timestamp<TD>$note";
            }
            echo "</table>";
            if ($total_balance < 0) {
                $color = "red";
            } else {
                $color = "blue";
            }
            echo "Total Balance:<font color = '$color'>$total_balance";
        }else{
            echo "<br>No Transactions With Keyword: <strong>$keyword</strong>";
        }

    }
echo "</body>\n</html>";
mysqli_free_result($result);
}
mysqli_close($con);
?>