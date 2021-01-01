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
		$no=1;
        while ($i= mysqli_fetch_array($tampil)) {
            $idgam=$i['nourut'];
            $gambar=$i['gambar'];
            
            $gambar2=$i['gambar2'];

            if (!empty($gambar2)) {
                $data="data:".$gambar2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju="img_".$no."".$idgam."IDRUTCAX_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
            }
            echo "<div class='col-sm-2'><div class='form-group'>";
            if (empty($gambar2))
                echo '<img class="imgzoomx" src="data:image/jpeg;base64,'.base64_encode( $gambar ).'" class="img-thumnail"/>';
            else
                echo "<img class='imgzoomx' src='images/tanda_tangan_base64/$namapengaju' class='img-thumnail'>";
            echo "</div></div>";
			$no++;
        }
    }
    ?>

</body>
</html>