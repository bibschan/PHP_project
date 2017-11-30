<?php

session_start();

require ("includes/functions.php");

$message        = '';
$firstName      = '';
$lastName       = '';
$phoneNumber    = '';
$dob            = '';

if(isset($_COOKIE['firstName']))
{
    $firstName = $_COOKIE['firstName'];
}

if(isset($_COOKIE['lastName']))
{
    $lastName = $_COOKIE['lastName'];
}

if(isset($_COOKIE['phoneNumber']))
{
    $phoneNumber = $_COOKIE['phoneNumber'];
}

if(isset($_COOKIE['dob']))
{
    $dob = $_COOKIE['dob'];
}


if(count($_POST) > 0)
{
    //set cookies and session var 
    if(isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['phoneNumber']) && isset($_POST['dob']))
    {
        setcookie('firstName', $_POST['firstName'], time() + 60 * 60);
        $firstName = $_POST['firstName'];

        setcookie('lastName', $_POST['lastName'], time() + 60 * 60);
        $lastName = $_POST['lastName'];

        setcookie('phoneNumber', $_POST['phoneNumber'], time() + 60 * 60);
        $phoneNumber = $_POST['phoneNumber'];

        setcookie('dob', $_POST['dob'], time() + 60 * 60);
        $dob = $_POST['dob'];
    }


    //DATABASE CONNECT, INSERT AND ETC
    $servername = "127.0.0.1";
    $username   = "root";
    $password   = "";
    $dbname     = "php";

    $connection = mysqli_connect($servername, $username, $password, $dbname);

        // Check connection
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
        else {
        //query to insert posted data
            $sql = "INSERT INTO logins (phoneNumber, password, firstname, lastname, dob) VALUES (?, ?, ?, ?, ?)";
            $statement = mysqli_prepare($connection, $sql);

            //variables
            $postedNumber    = preg_replace('~\D~', '', $_POST['phoneNumber']);
            $postedPassword  = $_POST['password'];
            $postedFirstName = $_POST['firstName'];
            $postedLastName  = $_POST['lastName'];
            $postedDob       = $_POST['dob'];


            $statement -> bind_param("issss", $postedNumber, $postedPassword, $postedFirstName, $postedLastName, $postedDob);

            $statement -> execute();

                if ($statement) {
                    //session variables
                    $_SESSION['login']     = true;
                    $_SESSION['firstName'] = $postedFirstName;
                    $_SESSION['lastName']  = $postedLastName;

                    //redirect and exit
                    header('Location: index.php');
                    exit();
                } 

                else {
                    echo "Query was not succesful :(";
                }

        //close connection
        mysqli_close($connection);

        }
    

    $check = checkSignUp($_POST);

    if($check === true)
    {
        $message = '<div class="alert alert-success text-center">
                        Thank you for signing up!
                    </div>';
    }
    else
    {
        $message = '<div class="alert alert-danger text-center">
                        '.$check.' 
                    </div>';
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper">

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1 class="login-panel text-center text-muted">COMP 3015</h1>

                <?php echo $message; ?>

                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Create Account</h3>
                    </div>
                    <div class="panel-body">
                        <form name="signup" role="form" action="signup.php" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control"
                                           value="<?php echo $firstName;?>"
                                           name="firstName"
                                           placeholder="First Name"
                                           type="text"
                                           autofocus
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           value="<?php echo $lastName;?>"
                                           name="lastName"
                                            placeholder="Last Name"
                                            type="text"
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           name="password"
                                           placeholder="Password"
                                           type="password"
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           value="<?php echo $phoneNumber;?>"
                                           name="phoneNumber"
                                           placeholder="Phone Number"
                                           type="text"
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           value="<?php echo $dob;?>"
                                           name="dob"
                                           placeholder="Date of Birth"
                                           type="text"
                                    />
                                </div>
                                <input type="submit" class="btn btn-lg btn-info btn-block" value="Sign Up!"/>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <a class="btn btn-sm btn-default" href="login.php">Login</a>
            </div>
        </div>

    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
