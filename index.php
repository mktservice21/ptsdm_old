<!DOCTYPE html>
<html >
<head>
    <meta charset="UTF-8">
    <title>Marketing Service</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="mysdm/images/icon.ico" />
</head>

<body>
    <script> window.onload = function() { document.getElementById("t_email").focus(); } </script>
    <div class="form">


        <div class="tab-content">

            <div id="login">
                <h1>Selamat Datang</h1>
                <form action="cek_login.php" method="post">
                    <div class="field-wrap">
                        <label>Userid<span class="req">*</span></label>
                        <input type="text" required name="t_email" id="t_email" /><!--0000001854-->
                    </div>

                    <div class="field-wrap">
                        <label>Password<span class="req">*</span></label>
                        <input type="password"required autocomplete="off" name="t_pass" /><!--1605-->
                    </div>
                    <button class="button button-block"/>Log In</button>
                </form>
            </div>
            
            <div id="signup">
                

            </div>

        </div>

    </div>

    <script src='js/jquery.min.js'></script>
    <script src="js/index.js"></script>
</body>
</html>
