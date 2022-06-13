<?php
// sets up the database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'exam_portal';

$conn = mysqli_connect($db_host, $db_user, $db_pass,$db_name);

if($conn === false){
    die("Error connecting to database: " . mysqli_connect_error());
}

// function that uses prepared statements to run sql query 
// returns the stmt object if successful else
// returns false 
function prep_sql_execute($conn, $sql, $para_array, $str_list){
    // create the prepared statement
    $stmt = mysqli_stmt_init($conn);
    if($stmt === false || !mysqli_stmt_prepare($stmt, $sql)){
        return false;
    }
    
    // bind the parameter to sql query and execute the query
    if(mysqli_stmt_bind_param($stmt, $str_list, ...$para_array ) === false || mysqli_stmt_execute($stmt) === false){
        return false;
    }
    
    return $stmt;
}
