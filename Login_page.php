<?php
    session_start();

    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
        header("location: main.php");
        exit;
    }

    require_once "config.php";

    $username = $password = "";
    $username_err = $password_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["username"]))){
            $username_err = "Enter the username";
        } else{
            $username = trim($_POST["username"]);
        }

        if(empty(trim($_POST["password"]))){
            $password_err = "Enter the password";
        } else{
            $password = trim($_POST["password"]);
        }

        if(empty($username_err) && empty($password_err)){
            $query = "select * from users where username = ?";

            if($statement = mysqli_prepare($conn, $query)){
                mysqli_stmt_bind_param($statement, "s", $param_username);

                $param_username = $username;

                if(mysqli_stmt_execute($statement)){
                    mysqli_stmt_store_result($statement);

                    if(mysqli_stmt_num_rows($statement) == 1){
                        mysqli_stmt_bind_result($statement, $username, $hashed_password);

                        if(mysqli_stmt_fetch($statement)){
                            if(password_verify($password, $hashed_password)){
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["username"] = $username;
                                $_SESSION["search_name"] = "";
                                header("location: main.php");
                            } else{
                                $password_err = "password is wrong";
                            }
                        }
                    } else{
                        $username_err = "username is wrong";
                    }
                } else{
                    echo "<script>alert('Something went wrong. Try again later')</script>";
                }
                mysqli_stmt_close($statement);
            }
        }
        mysqli_close($conn);
        /*if(!empty($username_err)){
            echo "<script>alert('".$username_err."');</script>";
        }
        if(!empty($password_err)){
            echo "<script>alert('".$password_err."');</script>";
        }*/
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="CSS/login.css">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>
        <div class="background" style="overflow:hidden">
            <nav>
                <div class="navigation">
                    <a class="title" href="Login_page.php">Chefsite</a>
                    <a class="login" href="signup.php">Signup</a>
                    <a class="login" href="Login_page.php">Login</a>
                </div>
            </nav>
            <header>
                <div class="container">
                    <div class="inner">
                        <h3 class="login">Welcome to Chefsite</h3>
                        <p class="login">Login to your account</p>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <div class="error">
                                <p>
                                    <?php
                                        if(!empty($username_err)){
                                            echo "".$username_err;

                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="login">
                                <input type="text" name="username" id="username" placeholder="Username">
                            </div>
                            <div class="error">
                                <p style="height:15px;font-size:15px">
                                    <?php
                                        if(!empty($password_err)){
                                            echo "".$password_err;
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="login">
                                <input type="password" name="password" id="password" placeholder="Password">
                            </div>
                            <div class="login">
                                <input type="submit" name="submit" id="submit" value="Login">
                            </div>
                            <p class="login" style="font-weight: unset;">Dont have an account? <a class="signup" href="signup.php">Sign up</a></p>
                        </form>
                    </div>
                </div>
            </header>
        </div>
    </body>
</html>