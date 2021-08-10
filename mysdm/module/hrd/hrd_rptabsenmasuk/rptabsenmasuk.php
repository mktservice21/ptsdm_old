<?PHP
    include "config/cek_akses_modul.php";
    $aksi="module/hrd/hrd_rptabsenmasuk/aksi_rptabsenmasuk.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $fkaryawan = trim($_SESSION['IDCARD']);
    $fnmkaryawan=$_SESSION['NAMALENGKAP'];    
    $fgroupid = trim($_SESSION['GROUP']);
    
    $pmodule=$_GET['module'];
    
    $apvpilih="00";
    
    $pleader=false;
    $patasantanpaleader=false;
    $query = "select karyawanId, leader from dbmaster.t_karyawan_posisi WHERE karyawanId='$fkaryawan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $row= mysqli_fetch_array($tampil);
        $nldr_=$row['leader'];

        if ($nldr_=="Y") {
            $pleader=true;
            $patasantanpaleader=true;
        }else{
            $query_a = "select karyawanId FROM hrd.karyawan WHERE ( atasanId='$fkaryawan' OR atasanId2='$fkaryawan' ) "
                    . " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' ) "
                    . " AND IFNULL(aktif,'')<>'N'";
            $tampil_a= mysqli_query($cnmy, $query_a);
            $ketemu_a=mysqli_num_rows($tampil_a);
            if ((INT)$ketemu_a>0) {
                $patasantanpaleader=true;
            }
        }
    }

    $pbolehbukall=false;
    if ($fgroupid=="24" OR $fgroupid=="1" OR $fgroupid=="X57" OR $fgroupid=="47" OR $fgroupid=="29" OR $fgroupid=="46") {
        $pbolehbukall=true;
    }
        
?>

<script>
    $(document).ready(function() {
        $('#cbln01').on('change dp.change', function(e){
            KosongkanData();
        });
    
        var eapvpilih=document.getElementById('e_apvpilih').value;
        //pilihData(eapvpilih);
    } );
    
    function pilihData(ket){
        var istatus=document.getElementById('txt_hiden').value;
        if (istatus=="tutup") {
            document.getElementById('btnlink_hidden').click();
            document.getElementById('txt_hiden').value='buka';
        }
                                    
        var etgl1=document.getElementById('tgl1').value;
        var ekaryawan=document.getElementById('cb_karyawan').value;
        
        document.getElementById('e_apvpilih').value=ket;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        
        //alert(ket);
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/hrd/hrd_rptabsenmasuk/aksi_rptabsenmasuk.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"eket="+ket+"&uperiode1="+etgl1+"&ukaryawan="+ekaryawan+"&uketapv="+ket,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
    
    
    function KosongkanData() {
        $("#c-data").html("");
    }
    
    function ShowDataKaryawanByAtasan() {
        var eidatasan =document.getElementById('cb_atasan').value;
        $.ajax({
            type:"post",
            url:"module/hrd/viewdatahrd.php?module=carikaryawanbyatasan",
            data:"uidatasan="+eidatasan,
            success:function(data){
                $("#cb_karyawan").html(data);
                KosongkanData();
            }
        });
    }
            
</script>

<div class='modal fade' id='myModalImages' role='dialog' class='no-print'></div>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                echo "Report Visualisasi Absensi";
                ?>
                
            </h3>
        </div></div><div class="clearfix">
    </div>
    
    <!--row-->
    <div class="row">
        
        <form name='form1' id='form1' method='POST' action='' enctype='multipart/form-data'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                     <div hidden class='col-sm-3'>
                        <small>notes</small>
                        <div class="form-group">
                            <div class='input-group date'>
                                <input type='text' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class='col-sm-3'>
                        Bulan
                        <div class="form-group">
                            <div class='input-group date' id='cbln01'>
                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class='col-sm-3'>
                        Atasan
                        <div class="form-group">
                            <select class='form-control input-sm' id='cb_atasan' name='cb_atasan' onchange="ShowDataKaryawanByAtasan()" data-live-search="true">
                                <?PHP
                                    $query = "select a.karyawanId as karyawanid, a.nama as nama_karyawan From hrd.karyawan as a "
                                            . " JOIN dbmaster.t_karyawan_posisi as b on a.karyawanId=b.karyawanId "
                                            . " WHERE 1=1 ";
                                    $query .= " AND ( IFNULL(a.tglkeluar,'')='' OR IFNULL(a.tglkeluar,'0000-00-00')='0000-00-00' ) ";
                                    $query .= " AND IFNULL(b.`leader`,'')='Y' ";
                                    if ($pbolehbukall==true) {
                                    }else{
                                        $query .= " AND a.karyawanId='$fkaryawan'";
                                    }
                                    $query .= " ORDER BY a.nama";

                                    $tampil = mysqli_query($cnmy, $query);
                                    $ketemu= mysqli_num_rows($tampil);
                                    if ((INT)$ketemu<=0) {
                                        if ($pleader==false AND $patasantanpaleader==true)
                                            echo "<option value='$fkaryawan' selected>$fnmkaryawan</option>";
                                        else
                                            echo "<option value='' selected></option>";
                                    }else
                                        echo "<option value='' selected>-- All --</option>";
                                    while ($z= mysqli_fetch_array($tampil)) {
                                        $pkaryid=$z['karyawanid'];
                                        $pkarynm=$z['nama_karyawan'];
                                        $pkryid=(INT)$pkaryid;
                                        if ((INT)$ketemu==1)
                                            echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                        else
                                            echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class='col-sm-3'>
                        Karyawan
                        <div class="form-group">
                            <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="KosongkanData()" data-live-search="true">
                                <?PHP 
                                $query = "select a.karyawanId as karyawanid, a.nama as nama_karyawan From hrd.karyawan as a "
                                        . " JOIN dbmaster.t_karyawan_posisi as b on a.karyawanId=b.karyawanId "
                                        . " WHERE 1=1 ";
                                $query .= " AND ( IFNULL(a.tglkeluar,'')='' OR IFNULL(a.tglkeluar,'0000-00-00')='0000-00-00' ) ";
                                $query .= " AND IFNULL(b.`ho`,'')='Y' ";
                                if ($pbolehbukall==true) {
                                }else{
                                    if ($pleader==true OR $patasantanpaleader==true) {
                                        $query .= " AND (a.karyawanId='$fkaryawan' OR a.atasanId='$fkaryawan' OR a.atasanId2='$fkaryawan' ) ";
                                    }else{
                                        $query .= " AND a.karyawanId='$fkaryawan'";
                                    }
                                }
                                $query .= " ORDER BY a.nama";


                                $tampil = mysqli_query($cnmy, $query);

                                $ketemu= mysqli_num_rows($tampil);

                                while ($z= mysqli_fetch_array($tampil)) {
                                    $pkaryid=$z['karyawanid'];
                                    $pkarynm=$z['nama_karyawan'];
                                    $pkryid=(INT)$pkaryid;
                                    if ($pkaryid==$fkaryawan)
                                        echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                    else
                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class='col-sm-3'>
                        &nbsp;
                        <div class="form-group">
                            <input onclick="pilihData('00')" class='btn btn-success btn-sm' type='button' name='buttonview1' value='List Absensi'>
                            <!--<input onclick="pilihData('1')" class='btn btn-success btn-sm' type='button' name='buttonview1' value='Absen Masuk'>
                            <input onclick="pilihData('2')" class='btn btn-info btn-sm' type='button' name='buttonview1' value='Absen Pulang'>-->
                        </div>
                    </div>
                    
                </div>
            </div>
            
            
            
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Peta <small>&nbsp;</small></h2>
                            <input type='hidden' id='txt_hiden' name='txt_hiden' value=''>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link" id='btnlink_hidden' name='btnlink_hidden' onclick="showTombolHiden('btnlink_hidden')" ><i class="fa fa-chevron-up"></i></a></li>
                                <li class="dropdown">
                                    &nbsp;
                                    
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>

                        
                        <div class="x_content">
                            <br />
                            
                            
                            
                            <style type="text/css">
                                #map { width: 650px; height: 500px; border: 1px; padding: 0px; }
                            </style>
                            
                            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyClBImVGtLSNzJiU7ceMdSIUbnTAFJhaio&callback=myMap"></script>
                            <script type="text/javascript">
                                //Sample code written by August Li
                                var icon = new google.maps.MarkerImage("http://maps.google.com/mapfiles/ms/micons/blue.png",
                                new google.maps.Size(32, 32), new google.maps.Point(0, 0),
                                new google.maps.Point(16, 32));
                                var center = null;
                                var map = null;
                                var currentPopup;
                                var bounds = new google.maps.LatLngBounds();
                                function addMarker(lat, lng, info) {
                                    var pt = new google.maps.LatLng(lat, lng);
                                    bounds.extend(pt);
                                    var marker = new google.maps.Marker({
                                        position: pt,
                                        icon: icon,
                                        map: map
                                    });
                                    var popup = new google.maps.InfoWindow({
                                        content: info,
                                        maxWidth: 300
                                    });
                                    google.maps.event.addListener(marker, "click", function() {
                                        if (currentPopup != null) {
                                            currentPopup.close();
                                            currentPopup = null;
                                        }
                                        popup.open(map, marker);
                                        currentPopup = popup;
                                    });
                                    google.maps.event.addListener(popup, "closeclick", function() {
                                        map.panTo(center);
                                        currentPopup = null;
                                    });
                                    
                                }
                                
                                function initMap(slatitude, slongitude, snama_karyawan) {
                                    var istatus=document.getElementById('txt_hiden').value;
                                    if (istatus=="buka") {
                                        document.getElementById('btnlink_hidden').click();
                                        document.getElementById('txt_hiden').value='tutup';
                                    }
                                    
                                    map = new google.maps.Map(document.getElementById("map"), {
                                        center: new google.maps.LatLng(0, 0),
                                        zoom: 14,
                                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                                        mapTypeControl: false,
                                        mapTypeControlOptions: {
                                            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                                        },
                                        navigationControl: true,
                                        navigationControlOptions: {
                                            style: google.maps.NavigationControlStyle.SMALL
                                        }
                                    });
                                    <?PHP
                                        $lat="-6.2721541";
                                        $lon="106.9379772";
                                        $name="";
                                        $desc="";
                                        //echo ("addMarker($lat, $lon,'<b>$name</b><br/>$desc');\n");
                                    ?>
                                    addMarker(slatitude, slongitude, snama_karyawan);
                                    center = bounds.getCenter();
                                    map.fitBounds(bounds);

                                }
                                
                                $(document).ready(function() {
                                    //initMap('-6.1912629', '106.8503225', 'nama');
                                } );
                                
                                function ShowIframeMaps(slatitude, slongitude, snama_karyawan) {
                                    var istatus=document.getElementById('txt_hiden').value;
                                    if (istatus=="buka") {
                                        document.getElementById('btnlink_hidden').click();
                                        document.getElementById('txt_hiden').value='tutup';
                                    }
                                    $.ajax({
                                        url: 'module/hrd/hrd_rptabsenmasuk/hrd_peta_absen.php?module=showiframemaps',
                                        type: 'POST',
                                        data: {
                                            ulat: slatitude,
                                            ulong: slongitude,
                                            unamakry: snama_karyawan
                                        },
                                        success: function (data) {
                                            $("#div_map").html(data);
                                        }
                                    })   
                                }
                            </script>
        
                            
                            <!--<div id="map"></div>-->
                            <div id='div_map'>
                            
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </div>
            </div>
            
            
            
            <div id='loading'></div>
            <div id='c-data'>
                <div class='x_content'>

                    <table id='datatable' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th width='7px'>No</th>
                                <th width='10px'>
                                    <input type="checkbox" id="chkbtnbr" value="select" 
                                    onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>
                    
        </form>
        
    </div>
</div>


<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>
        
<script>
    // SCROLL
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
        } else {
            document.getElementById("myBtn").style.display = "none";
        }
    }
    
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    // END SCROLL

</script>

<script type="text/javascript">
    
    window.onload = function(){
      document.getElementById('btnlink_hidden').click();
    }

    function showTombolHiden(inama) {
        var istatus=document.getElementById('txt_hiden').value;
        if (istatus=="") {
            istatus="tutup";
        }
        
        if (istatus=="buka") {
            document.getElementById('txt_hiden').value='tutup';
        }else{
            document.getElementById('txt_hiden').value='buka';
        }
    }
</script>