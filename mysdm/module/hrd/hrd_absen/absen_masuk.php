<?PHP
$ptglnwoabsmsk=date("Y-m-d");
$pkaryawanabsmsk="";
$pkryjammasuk="00:00";

if (isset($_SESSION['IDCARD']))     $pkaryawanabsmsk=$_SESSION['IDCARD'];
if (isset($_SESSION['J_MASUK']))    $pkryjammasuk=$_SESSION['J_MASUK'];

$query = "select idabsen, jam, l_status FROM hrd.t_absen WHERE karyawanid='$pkaryawanabsmsk' AND tanggal='$ptglnwoabsmsk' AND kode_absen='1'";
$tampilabsmsk=mysqli_query($cnmy, $query);
$mrow= mysqli_fetch_array($tampilabsmsk);
$pjmabsen=$mrow['jam'];
$pidabsen=$mrow['idabsen'];
$ntempatabsen=$mrow['l_status'];
$pjammasukabs="<div class='count'>".$pjmabsen."</div>";
if (empty($pjmabsen)) {
    $pjammasukabs="<div class='count' style='color:#C0C0C0'>$pkryjammasuk</div>";
}

$pgambarabs="";
if (!empty($pidabsen)) {
    $query = "select nama FROM dbimages2.img_absen WHERE idabsen='$pidabsen' AND kode_absen='1'";
    $tampilabsimg=mysqli_query($cnmy, $query);
    $rimg= mysqli_fetch_array($tampilabsimg);
    $pgambarabs=$rimg['nama'];
}
?>
<div class='modal fade' id='myModalAbsen' role='dialog' class='no-print'></div>
<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="tile-stats">

        <div class="icon">
            <?PHP
            $iconasli="<i class='glyphicon glyphicon-check'></i>";
            if (!empty($pgambarabs)) {
                $folderfotofileabs="images/foto_absen/".$pgambarabs;
                if (!file_exists($folderfotofileabs)) {
                    echo "$ntempatabsen";
                }else{
                    echo "<img src='$folderfotofileabs' width='50px' height='50px' />";
					echo "<span><i style='font-size:15px; font-wight:bold;'>$ntempatabsen</i></span>";
                }
            }else{
                echo $iconasli;
            }
            ?>
        </div>
        <?PHP echo $pjammasukabs; ?>
        <h3>
            <!--<button type='button' class='btn btn-default' id='ibuttonsave' data-toggle='modal' data-target='#myModalAbsen' onclick='ShowFormAbsen("1")'>Absen Masuk</button>-->
            <a href='?module=hrdabsenmasuk&act=absenmasuk&idmenu=522&kriteria=Y' class='btn btn-default' id='ibuttonsave'>Absen Masuk</a>
        </h3>
        <p>&nbsp;</p>
    </div>
</div>

<script>
    
    function ShowFormAbsen(sKey) {
        $.ajax({
            type:"post",
            url:"module/hrd/hrd_absen/upload_webcam.php?module=uploadwebcame",
            data:"ukey="+sKey,
            success:function(data){
                $("#myModalAbsen").html(data);
            }
        });
    }
</script>