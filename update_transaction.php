<?php
define("IN_CODE", 1);
include "dbconfig.php";
if(!isset($_COOKIE["customer_id"])){
    echo "Please Login First";
    echo "<br><a href=\"index.html\">Back To Home</a>";
    die;
} else {
echo "<a href=\"logout.php\">Logout</a>";

$con = mysqli_connect($host, $username, $password, $dbname)
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
echo "<html>\n<body style=\"font-family: 'Trebuchet MS', sans-serif;\">";

$num_transactions = $_POST['num_transactions'];
$count_update = 0;
$count_delete = 0;

//update all changed fields at once
for ($i = 0; $i < count($_POST['update']); $i++) {
    //assigns variables from the form in order to update
    $transaction_timestamp[$i] = 'CURRENT_TIMESTAMP';
    $note[$i] = $_POST['note'][$i];
    $code[$i] = $_POST['code'][$i];
    $note_update[$i] = $_POST['update'][$i];
 
    //checks if note has been changed
    if ($note[$i] != $note_update[$i]) {
        $update_query = "UPDATE CPS3740_2022F.Money_alviolai 
                    SET note = '$note_update[$i]', mydatetime = $transaction_timestamp[$i]
                    WHERE code = '$code[$i]'";
        $update_note = mysqli_query($con, $update_query);
        $count_update++; //increments for proper count if it meets that condition
        //handles error, if none will output success
        echo "<br>The note for code $code[$i] has been updated from the database.";
        if (!$update_note) {
            echo ("Error description: " . mysqli_error($con));
        }
    } else {
        continue;
    }
}
//deletes only checked records
for ($i = 0; $i <= $num_transactions; $i++) {
    if (isset($_POST['delete'][$i])) {
        $deleted_transaction[$i] = $_POST['delete'][$i];
        $delete_query = "DELETE FROM CPS3740_2022F.Money_alviolai 
                                    WHERE code = '$deleted_transaction[$i]'";
        $delete_record = mysqli_query($con, $delete_query);
        $count_delete++;
        echo "<br>The transaction with code $deleted_transaction[$i] has been deleted from the database.";
        //handles error, if none will output success
        if (!$delete_record) {
            echo ("Error description: " . mysqli_error($con));
        }
    } else {
        $deleted_transaction[$i] = null;
        }
    continue;
}


echo "<br>Successfully updated $count_update transactions.";
echo "<br>Successfully deleted $count_delete transactions.";
echo "</body>\n</html>";

}
mysqli_close($con);
?>