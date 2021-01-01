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
    include "config/koneksimysqli.php";
    $query = "select * from dbimages.img_br_cab1 where nourut='$_GET[id]'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $no=1;
        while ($i= mysqli_fetch_array($tampil)) {
            $idgam=$i['nourut'];
            $gambar=$i['gambar'];

            if (!empty($gambar)) {
                $data="data:".$gambar;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju="img_".$no."".$idgam."IDGBCABX_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
            }
            echo "<div class='col-sm-2'><div class='form-group'>";
            echo "<img class='imgzoomx' src='images/tanda_tangan_base64/$namapengaju' class='img-thumnail'>";
            echo "</div></div>";
            $no++;
        }
    }
    
    mysqli_close($cnmy);
    ?>

</body>
</html>