<?PHP
$ptglnwoabsmsk=date("Y-m-d");
$pkaryawanabsmsk="";
if (isset($_SESSION['IDCARD'])) $pkaryawanabsmsk=$_SESSION['IDCARD'];

$query = "select jam FROM hrd.t_absen WHERE karyawanid='$pkaryawanabsmsk' AND tanggal='$ptglnwoabsmsk' AND kode_absen='3'";
$tampilabsist=mysqli_query($cnmy, $query);
$irow= mysqli_fetch_array($tampilabsist);
$pjamistabs="<div class='count'>".$irow['jam']."</div>";
if (empty($pjamistabs)) {
    $pjamistabs="<div class='count'>12:00</div>";
}

$query = "select jam FROM hrd.t_absen WHERE karyawanid='$pkaryawanabsmsk' AND tanggal='$ptglnwoabsmsk' AND kode_absen='4'";
$tampilabsist_msk=mysqli_query($cnmy, $query);
$irow_m= mysqli_fetch_array($tampilabsist_msk);
$pjamistabs_msk="<div class='count'>".$irow_m['jam']."</div>";
if (empty($pjamistabs_msk)) {
    $pjamistabs_msk="<div class='count'>13:00</div>";
}

$platitude_home="";
$plongitude_home="";
?>


<div>
    <input type='hidden' id='e_latitude_home' name='e_latitude_home' class='form-control col-md-7 col-xs-12' value='<?PHP echo $platitude_home; ?>' Readonly>
    <input type='hidden' id='e_longitude_home' name='e_longitude_home' class='form-control col-md-7 col-xs-12' value='<?PHP echo $plongitude_home; ?>' Readonly>
</div>

<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="tile-stats">
        <div class="icon"><i class="fa fa-comments-o"></i></div>
        <?PHP echo $pjamistabs; ?>
        <h3><button type='button' class='btn btn-default' id="ibuttonsave" onclick='SimpanAbsensiHome("3")'>Absen Istirahat</button></h3>
        <p>&nbsp;</p>
    </div>
</div>


<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="tile-stats">
        <div class="icon"><i class="fa fa-sort-amount-desc"></i></div>
        <?PHP echo $pjamistabs_msk; ?>
        <h3><button type='button' class='btn btn-default' id="ibuttonsave" onclick='SimpanAbsensiHome("4")'>Selesai Istirahat</button></h3>
        <p>&nbsp;</p>
    </div>
</div>


<script>
    $(document).ready(function() {
        //getLocation();
    } );
    
    var x = document.getElementById("d_lokasi");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        document.getElementById("e_latitude_home").value=position.coords.latitude;
        document.getElementById("e_longitude_home").value=position.coords.longitude;
    }
    
    
    function SimpanAbsensiHome(sKey)  {
        getLocation();
        setTimeout(function () {
            disp_confirm_absensi(sKey)
        }, 200);
        
    }
    
    function disp_confirm_absensi(sKey) {
        var ilat = document.getElementById("e_latitude_home").value;
        var ilong = document.getElementById("e_longitude_home").value;
        
        if (ilat=="" || ilong=="") {
            alert("Tidak bisa absen, karena lokasi kosong...");
            return false;
        }
        
        var pText_="";
        if (sKey=="1") {
            pText_="Apakah akan melakukan absen masuk...?";
        }else if (sKey=="2") {
            pText_="Apakah akan melakukan absen pulang...?";
        }else if (sKey=="3") {
            pText_="Apakah akan melakukan absen istirahat...?";
        }else if (sKey=="4") {
            pText_="Apakah akan melakukan absen masuk dari istirahat...?";
        }
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                
                $.ajax({
                    type:"post",
                    url:"module/hrd/hrd_absen/simpanabsenistirahat.php?module="+module+"&act=simpandataabsen&idmenu="+idmenu,
                    data:"ukey="+sKey+"&ulat="+ilat+"&ulong="+ilong,
                    success:function(data){
                        alert(data);
                    }
                });
                
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
</script>