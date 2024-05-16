<DOCTYPE html>
<html>
    <head>
        <title>Change Password</title>
        <link rel = "stylesheet" href = "signupstyle.css">
    </head>
    <body>
        <header>
          <h1>Fame Duel</h1>
        </header>

        <div class="container">
            <main>
            <?php if(isset($_GET['error'])){?>
                <p class = "error"><?php echo $_GET['error']; ?></p>
            <?php } ?>

                <form id = "" action = "passwordchange.php" method = "POST">
                    <?php
                        include 'connect.php';
                        include 'validate.php';
                        if(isset($_GET['key'])){
                            $key = $_GET['key'];
                        

                            $check = "SELECT Username FROM users WHERE Email = '$key' ";

                            $confirm = mysqli_query($connect, $check);

                            if (mysqli_num_rows($confirm) >= 1) {
                                $username = [];

                                while($row = mysqli_fetch_array($confirm)){
                                    $username[] = $row['Username'];
                                }

                                $buttons = "";

                                for($i = 0; $i < count($username); $i++){
                                    $buttons .= '<label><input type ="radio" name = "username" value = ' . $username[$i] . '>' . $username[$i] .'<br>   </label>';
                                }
                                echo $buttons;
                            }
                    ?>
                    <div class = "form-group">
                        <label>New Password</label><br>
                        <input type = "password" id = "nPass" name = "nPass"><br><br>
                    </div>

                    <div class = "form-group">
                        <label>Confirm Password</label><br>
                        <input type = "password" id = "cPass" name = "cPass"><br><br>
                    </div>

                    <button type ="submit">Change</button>
                </form>
            </main>
        </div>

        <footer>
            &copy; 2023 Fame Duel. All rights reserved.
        </footer>
    </body>
</html>
<?php 
    }else{
        header("Location: Login.php");
    } 
?>