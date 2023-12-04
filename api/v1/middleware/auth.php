<?php

function checkUser($conn, string $uname)
{
    $query = "SELECT uname, user_role from `users` where uname = `$uname`";

    if (mysqli_num_rows(mysqli_query($conn, $query)) !== 0) {

    }
}

function registerTemp($conn, string $tempUname)
{
    $sql = "INSERT INTO `users` (`uname`, `password`, 'user_role') VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password, $param_role);

        // Set parameters
        $param_username = $tempUname;
        $param_password = password_hash('tempPass', PASSWORD_DEFAULT); // Creates a password hash
        $param_role = 2;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'success' => true
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}