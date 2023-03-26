<?php
define("IN_CODE", 1);
include "dbconfig.php";

echo "<html>\n<body style= 'font-family: 'Trebuchet MS', sans-serif;'>";



if((!isset($_POST['username']) || !isset($_POST['password']))){
    echo"Please Login First";
    echo "<br><a href=\"index.html\">Back To Home</a>";
} else {

    $con = mysqli_connect($host, $username, $password, $dbname)
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());

    $cookie_id = "customer_id";
    $cookie_name = "customer_name";
    $login = mysqli_real_escape_string($con, $_POST['username']);
    $bpassword = mysqli_real_escape_string($con, $_POST['password']);

    $check_login = "SELECT login, password FROM CPS3740.Customers WHERE login ='$login'";
    $login_result = mysqli_query($con, $check_login);
    $num = mysqli_num_rows($login_result);
    if (!mysqli_query($con, $check_login)) {
        echo ("Error description: " . mysqli_error($con));
    }


    //LOGOUT.PHP LINK
    echo "<a href='logout.php'>Logout</a>";

    //GETS THE IP ADDRESS & PRINTS ONTO PAGE
    $ip = $_SERVER['REMOTE_ADDR'];
    echo "<br> Your IP: $ip\n";

    //CHECKS FOR KEAN UNIVERSITY'S IP ADDRESS
    $IPV4 = explode(".", $ip);
    if (($IPV4[0] == "131" && $IPV4[1] == "125") || $IPV4[0] == "10") {
        echo "<br>You are at Kean University. \n";
    } else {
        echo "<br>You are not at Kean University. \n";
    }

    //GETS OPERATING SYSTEM AND BROWSER OF OS
    echo "<br>Your Browser and OS: ";
    echo $_SERVER['HTTP_USER_AGENT'];
    echo "\n";
    echo "<hr>";

    //if num == 0 then login is not in the database
    if ($num == 0) {
        echo "<br>'$login' does not exist in the database.";
    }
    else{ // start of User Homepage
        $check_passwordMatch = "SELECT * FROM CPS3740.Customers WHERE login = '$login' AND password = '$bpassword'";
        $password_result = mysqli_query($con, $check_passwordMatch);
        $num_auth = mysqli_num_rows($password_result);
        if (!mysqli_query($con, $check_passwordMatch)) {
            echo ("Error description: " . mysqli_error($con));
        }
        if ($num_auth == 0) {

            echo "<br>login:'$login' exists, but password are not a match.";
        }
        else{ // num_rows != 0 therefore there exist a password and a login that is a match
            while ($row = mysqli_fetch_array($password_result)) {
                $id = $row["id"];
                $name = $row["name"];
                $gender = $row["gender"];
                $image = $row["img"];
                $dob = $row["DOB"];
                $street = $row["street"];
                $city = $row["city"];
                $state = $row["state"];
                $zipcode = $row["zipcode"];
            }
            setcookie($cookie_id, $id, time() + 84000, "/");
            setcookie($cookie_name, $name, time() + 84000, "/");
            //GETS NAME OF USER
            echo "Welcome Customer: <strong>$name</strong>\n";

            //GETS AGE OF USER
            $today = date("Y-m-d");
            $diff = date_diff(date_create($dob),date_create($today));
            echo "<br>Your Age:" .$diff -> format('%y');
            
            //ADDRESS OF USER
            echo "<br>Your Address Is: $street, $city, $zipcode\n";
            
            //ACCESS IMAGE OF MYSQL IN PHP
            echo "<br><img src=' data:image/jpeg;base64,".base64_encode( $image ) . "'>\n";
            
            echo "<hr>";
            $transaction_table_query = "SELECT mid AS ID, code AS Code, type AS Type, amount AS Amount, Sources.name AS Source, mydatetime AS DateTime, note AS Note FROM CPS3740_2022F.Money_alviolai inner join CPS3740.Sources ON Money_alviolai.sid = Sources.id where cid = '$id' ORDER BY mid ASC";
            $transaction_table_result = mysqli_query($con, $transaction_table_query); 
            $num_transactions = mysqli_num_rows($transaction_table_result);

            echo "There are <strong>$num_transactions</strong> transactions for <strong>$name</strong>.";
            
            $total_balance = 0;
            if ($num_transactions > 0){
                echo "<TABLE border = 1>";
                echo "<TR><TH>ID<TH>CODE<TH>Type<TH>Amount<TH>Source<TH>Date & Time<TH>Note";   
                while ($row = mysqli_fetch_array($transaction_table_result)){
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

                
            }
            else{
                echo "<br>No Transactions Made: Not Updatable";
            }
            echo "
                    <form action = 'add_transaction.php' method='post'>
                        <input type='hidden'  name='total_balance' value='$total_balance'/>
                        <input type= 'submit' value= 'Add Transaction'>
                        &emsp;<a href=\"display_transaction.php\">Display and update transaction</a>
                        &emsp;<a href=\"display_stores.php\">Display stores</a>
                    </form>
                ";
            echo "<form action=\"search.php\" method=\"get\" style =\"color:black;\">Keyword: <input type=\"text\" name=\"keyword\"><button type=\"submit\" action=\"search_transaction.php\">Search Transaction</button></form>";
            echo "</body>\n</html>";
            mysqli_free_result($transaction_table_result);
        }
    }
mysqli_close($con);
}
?>