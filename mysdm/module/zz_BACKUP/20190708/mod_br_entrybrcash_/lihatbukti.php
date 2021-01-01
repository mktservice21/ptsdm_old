<?PHP
    $printdate= date("d_m_Y");
    $jamnow=date("H_i_s");
    $kodepilih=3;
    
    include "config/koneksimysqli.php";
    include_once("config/common.php");
    $judulnya="DATA BUKTI CASH ADVANCE";
?>
<html>
<head>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
    <script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/jquery/dist/jquery.min.js"></script>
    <link href="nanogallery2-2.3.0/dist/css/nanogallery2.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/nanogallery2-2.3.0/dist/jquery.nanogallery2.min.js"></script>
</head>
<body>
    <center><h2><u><?PHP echo "$judulnya"; ?>, ID : <b><?PHP echo "$_GET[brid]"; ?></b></u></h2></center>
    <div ID="ngy2p" data-nanogallery2='{
        "itemsBaseURL": "http://ms.sdm-mkt.com/mysdm/images/tanda_tangan_base64/",
        "thumbnailWidth": "200",
        "thumbnailAlignment": "center"
        }'>
    <?PHP
    $query = "select * from dbimages.img_ca0 where idca='$_GET[brid]'";
    $tampil= mysqli_query($cnmy, $query);
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
                $namapengaju="img_".$no."".$idgam."IDRUTXB_.jpg";
                
                $namapengajupng="img_".$no."".$idgam."IDRUTXB_.png";
                $namapengajujpg="img_".$no."".$idgam."IDRUTXB_.jpg";
                
                //=====
                //file_put_contents('images/tanda_tangan_base64/'.$namapengajupng, $data);
                //png2jpg('images/tanda_tangan_base64/'.$namapengajupng,'images/tanda_tangan_base64/'.$namapengajujpg, 100);
                //unlink('images/tanda_tangan_base64/'.$namapengajupng);
                //=====
                
                $image = $gambar2;
                $image = imagecreatefrompng($image);
                imagejpeg($image, 'images/tanda_tangan_base64/'.$namapengaju, 100);
                imagedestroy($image);
            }else{
                
            }
            
            ?>
            <?PHP
            echo "<a href='../../images/tanda_tangan_base64/$namapengaju' data-ngthumb='$namapengaju' data-ngdesc=''>$_GET[brid]</a>";
            /*
            if (empty($gambar2))
                echo '<img class="small-preview" src="data:image/jpeg;base64,'.base64_encode( $gambar ).'" class="img-thumnail"/><br/>&nbsp;<br/>&nbsp;';
            else
                echo "<img class='small-preview' src='images/tanda_tangan_base64/$namapengaju' class='img-thumnail'>";
                */
            
            $no++;
        }
    }
    
    function png2jpg($originalFile, $outputFile, $quality) {
        $image = imagecreatefrompng($originalFile);
        imagejpeg($image, $outputFile, $quality);
        imagedestroy($image);
    }
    ?>
    </div>
</body>
</html>