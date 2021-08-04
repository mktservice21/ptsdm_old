<?PHP
$ptglnwoabsmsk=date("Y-m-d");
$pkaryawanabsmsk="";
$pkryjampulang="00:00";

if (isset($_SESSION['IDCARD']))     $pkaryawanabsmsk=$_SESSION['IDCARD'];
if (isset($_SESSION['J_PULANG']))   $pkryjampulang=$_SESSION['J_PULANG'];

$query = "select jam FROM hrd.t_absen WHERE karyawanid='$pkaryawanabsmsk' AND tanggal='$ptglnwoabsmsk' AND kode_absen='2'";
$tampilabspln=mysqli_query($cnmy, $query);
$prow= mysqli_fetch_array($tampilabspln);
$pjmabsen=$prow['jam'];
$pjampulangabs="<div class='count'>".$pjmabsen."</div>";
if (empty($pjmabsen)) {
    $pjampulangabs="<div class='count' style='color:#C0C0C0'>$pkryjampulang</div>";
}
?>

<div class='modal fade' id='myModalAbsen' role='dialog' class='no-print'></div>
<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="tile-stats">

        <div class="icon">
            <i class="fa fa-check-square-o"></i>

        </div>
        <?PHP echo $pjampulangabs; ?>
        <h3>
            <!--<button type='button' class='btn btn-default' id='ibuttonsave' data-toggle='modal' data-target='#myModalAbsen' onclick='ShowFormAbsen("2")'>Absen Masuk</button>-->
            <a href='?module=hrdabsenmasuk&act=absenpulang&idmenu=522&kriteria=Y' class='btn btn-default' id='ibuttonsave'>Absen Pulang</a>
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