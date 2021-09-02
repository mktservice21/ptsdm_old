<?PHP
$ptglnwoabsmsk=date("Y-m-d");
$pkaryawanabsmsk="";
$pkryjampulang="00:00";

if (isset($_SESSION['IDCARD']))     $pkaryawanabsmsk=$_SESSION['IDCARD'];
if (isset($_SESSION['J_PULANG']))   $pkryjampulang=$_SESSION['J_PULANG'];

$query = "select idabsen, jam FROM hrd.t_absen WHERE karyawanid='$pkaryawanabsmsk' AND tanggal='$ptglnwoabsmsk' AND kode_absen='2'";
$tampilabspln=mysqli_query($cnmy, $query);
$prow= mysqli_fetch_array($tampilabspln);
$pjmabsen=$prow['jam'];
$pidabsen=$prow['idabsen'];
$pjampulangabs="<div class='count'>".$pjmabsen."</div>";
if (empty($pjmabsen)) {
    $pjampulangabs="<div class='count' style='color:#C0C0C0'>$pkryjampulang</div>";
}

$pgambarabs="";
if (!empty($pidabsen)) {
    $query = "select nama FROM dbimages2.img_absen WHERE idabsen='$pidabsen' AND kode_absen='2'";
    $tampilabsimg=mysqli_query($cnmy, $query);
    $rimg= mysqli_fetch_array($tampilabsimg);
    $pgambarabs=$rimg['nama'];
}


$query = "select CURRENT_TIME() as jamserver";
$tampilabsjam=mysqli_query($cnmy, $query);
$jrow= mysqli_fetch_array($tampilabsjam);
$pjam_server=$jrow['jamserver'];


?>

<div class='modal fade' id='myModalAbsen' role='dialog' class='no-print'></div>
<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="tile-stats">

        <div class="icon">
            <?PHP
            $iconasli="<i class='glyphicon glyphicon-log-out'></i>";
            if (!empty($pgambarabs)) {
                $folderfotofileabs="images/foto_absen/".$pgambarabs;
                if (!file_exists($folderfotofileabs)) {
                    echo "Kosong";
                }else{
                    echo "<img src='$folderfotofileabs' width='50px' height='50px' />";
                }
            }else{
                echo $iconasli;
            }
            ?>
        </div>
        <?PHP echo $pjampulangabs; ?>
        <h3>
            <!--<button type='button' class='btn btn-default' id='ibuttonsave' data-toggle='modal' data-target='#myModalAbsen' onclick='ShowFormAbsen("2")'>Absen Masuk</button>-->
            <a href='?module=hrdabsenmasuk&act=absenpulang&idmenu=522&kriteria=Y' class='btn btn-default' id='ibuttonsave'>Absen Pulang</a>
        </h3>
        <?PHP
        if (empty($pgambarabs)) {
            echo "<p>Jam Server : $pjam_server</p>";
        }else{
            echo "<p>&nbsp;</p>";
        }
        ?>
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