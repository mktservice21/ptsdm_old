<html>
    <head>
        <title>GAMBAR</title>
        <meta http-equiv="Expires" content="Mon, 01 Sep 2018 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="../../images/icon.ico" />
    </head>
<body>
        
    
    <?PHP
    include "config/koneksimysqli_it.php";
    $query = "select * from dbimages.img_ca0 where nourut='$_GET[id]'";
    $tampil= mysqli_query($cnit, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        while ($i= mysqli_fetch_array($tampil)) {
            $idgam=$i['nourut'];
            $gambar=$i['gambar'];
            echo "<div class='col-sm-2'><div class='form-group'>";
            echo '<img class="imgzoomx" src="data:image/jpeg;base64,'.base64_encode( $gambar ).'" class="img-thumnail"/>';
            echo "</div></div>";
        }
    }
    ?>

</body>
</html>