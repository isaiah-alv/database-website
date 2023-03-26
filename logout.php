<?php

    setcookie("customer_id", "", time() - 84000, "/");
    setcookie("customer_name", "", time() - 84000, "/");
    setcookie("login", "", time()-84000,"/");
    echo "You have successfully logged out";
    echo "<br><a href=\"index.html\">Back To Home</a>";
?>