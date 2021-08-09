<?PHP 
    date_default_timezone_set('Asia/Jakarta');
    //include "config/cek_akses_modul.php"; 
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    
    $pkeypilih="";
    $psudahabsen=false;
    $plangitude="";//-6.1910063
    $plongitude="";//106.8500943    -6.190690628090547, 106.85030691323897
    
    $pket_absen="";
    
    $judul="";
    if ($pact=="absenmasuk") {
        $judul = "Absen Masuk";
        $pkeypilih="1";
    }elseif ($pact=="absenpulang"){
        $judul = "Absen Pulang";
        $pkeypilih="2";
    }else{
        
    }
    
?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">
    
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                echo "$judul";
                ?>
            </h3>
            
        </div>
        
    </div>
    <div class="clearfix"></div>
    
    <!--row-->
    <div class="row">
        

        <div class="">


            <!--row-->
            <div class="row">

                <div class='col-md-12 col-sm-12 col-xs-12'>
                    
                    <div class='x_panel'>

                        <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                              id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>

                            
                                <div class='x_content'>


                                    <div class='col-md-6 col-xs-12'>
                                        
                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <input type='text' id='e_key' name='e_key' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkeypilih; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Latitude <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <input type='text' id='e_lat' name='e_lat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $plangitude; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Longitude <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <input type='text' id='e_long' name='e_long' class='form-control col-md-7 col-xs-12' value='<?PHP echo $plongitude; ?>' Readonly>
                                            </div>
                                        </div>
                                        

                                    </div>
                                    
                                    

                                </div>
                            
                                <div class='x_content'>
                                    <div id="my_camera"></div>
                                </div>
                                <div class='clearfix'></div>
                            
                                <div class='x_content'>


                                    <div class='col-md-6 col-xs-12'>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <textarea class='form-control' id="e_ketabsen" name='e_ketabsen' maxlength='300'><?PHP echo $pket_absen; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <a class='btn btn-default' id='btn_home' href="<?PHP echo "?module=home"; ?>">Home</a> &nbsp; &nbsp; &nbsp;
                                                <?PHP
                                                if ($psudahabsen==true) {
                                                    echo "";
                                                }else{
                                                    if ($pkeypilih=="1") {
                                                        echo "<button type='submit' class='tombol-simpan btn btn-info' id='ibuttonsave'>Absen Masuk</button>";
                                                    }elseif ($pkeypilih=="2") {
                                                        echo "<button type='submit' class='tombol-simpan btn btn-info' id='ibuttonsave'>Absen Pulang</button>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        

                                    </div>
                                    
                                    

                                </div>
                            
                            
                            

                        </form>
                        
                        <div id='div_map'>

                        </div>

                    </div>
                    
                    
                </div>
                
            </div>
            
        </div>
        
        
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

<!-- jquery  -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
    integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous">
</script>
<!-- bootstrap js  -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
    integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous">
</script>
<!-- webcamjs  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.js"></script>
<script language="JavaScript">
    // menampilkan kamera dengan menentukan ukuran, format dan kualitas 
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 100
    });

    //menampilkan webcam di dalam file html dengan id my_camera
    Webcam.attach('#my_camera');

</script>


<script type="text/javascript">
    $(document).ready(function() {
        getLocationPlaceholder();
        setTimeout(function () {
            ShowIframeMapsAbsen('0');
        }, 1000);
        //update();
    } );
    
    var x = document.getElementById("d_lokasi");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("Geolocation tidak support pada browser ini.");
            //x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        document.getElementById("e_lat").value=position.coords.latitude;
        document.getElementById("e_long").value=position.coords.longitude;
    }
    
    
    function getLocationPlaceholder() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPositionPlaceholder, showError);
        } else {
            alert("Geolocation tidak support pada browser ini.");
            //x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }
    
    function showPositionPlaceholder(position) {
        document.getElementById("e_lat").placeholder=position.coords.latitude;
        document.getElementById("e_long").placeholder=position.coords.longitude;
    }
    
    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("Anda Memblokir Lokasi Untuk Situs MS");
                //x.innerHTML = "User denied the request for Geolocation."
            break;
            case error.POSITION_UNAVAILABLE:
                alert("Informasi Lokasi Tidak Tersedia.");
                //x.innerHTML = "Location information is unavailable."
            break;
            case error.TIMEOUT:
                alert("Time Out - Permintaan Untuk Mendapatkan Lokasi.");
                //x.innerHTML = "The request to get user location timed out."
            break;
            case error.UNKNOWN_ERROR:
                alert("Terjadi Kesalahan Yang Tidak Diketahui.");
                //x.innerHTML = "An unknown error occurred."
            break;
        }
    }

    // jalankan aksi saat tombol register disubmit
    $(".tombol-simpan").click(function () {
        var sKeyPilih = $('#e_key').val();
        event.preventDefault();
        
        getLocation();
        setTimeout(function () {
            
            var pText_="";
            if (sKeyPilih=="1") {
                pText_="Apakah akan melakukan absen masuk...?";
            }else if (sKeyPilih=="2") {
                pText_="Apakah akan melakukan absen pulang...?";
            }else if (sKeyPilih=="3") {
                pText_="Apakah akan melakukan absen istirahat...?";
            }else if (sKeyPilih=="4") {
                pText_="Apakah akan melakukan absen masuk dari istirahat...?";
            }
            
            var r=confirm(pText_)
            if (r==true) {
            }else{
                return false;
            }
            
            // membuat variabel image
            var image = '';

            var nlat = $('#e_lat').val();
            var nlong = $('#e_long').val();
            var nketerangan = $('#e_ketabsen').val();
            
            if (nlat=="" || nlong=="") {
                alert("Lokasi Kosong"); return false;
            }
            //memasukkan data gambar ke dalam variabel image
            Webcam.snap(function (data_uri) {
                image = data_uri;
            });
            
            if (image=="") {
                alert("Foto masih kosong..."); return false;
            }
            
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var act = urlku.searchParams.get("act");
            var idmenu = urlku.searchParams.get("idmenu");
            //mengirimkan data ke file action.php dengan teknik ajax
            $.ajax({
                url: 'module/hrd/hrd_absen/simpanabsen.php?module='+module+'&act='+act+'&idmenu='+idmenu,
                type: 'POST',
                data: {
                    ukey: sKeyPilih,
                    ulatitude: nlat,
                    ulongitude: nlong,
                    uketerangan: nketerangan,
                    image: image
                },
                success: function (data) {
                    var tdata = myTrim(data);
                    var ists = tdata.substring(0, 8);
                    alert(data);
                    if (ists=="berhasil") {
                        document.getElementById('btn_home').click();
                    }
                    
                    // menjalankan fungsi update setelah kirim data selesai dilakukan 
                    //update()
                }
            })            
            
        }, 500);

    });

    
    function myTrim(x) {
        return x.replace(/^\s+|\s+$/gm,'');
    }
    
    
    function ShowIframeMapsAbsen(sKey) {
        if (sKey=="0") {
            var slatitude=document.getElementById('e_lat').placeholder;
            var slongitude=document.getElementById('e_long').placeholder;
        }else{
            var slatitude=document.getElementById('e_lat').value;
            var slongitude=document.getElementById('e_long').value;
        }
        
        if (slatitude=="") {
            $("#div_map").html("");
            return false;
        }
        
        $.ajax({
            url: 'module/hrd/hrd_absen/peta_lokasiabsen.php?module=showiframemaps',
            type: 'POST',
            data: {
                ulat: slatitude,
                ulong: slongitude,
            },
            success: function (data) {
                $("#div_map").html(data);
            }
        })   
    }
    
    
    //fungsi update untuk menampilkan data
    function update() {
        $.ajax({
            url: 'data.php',
            type: 'get',
            success: function (data) {
                $('#data').html(data);
            }
        });
    }



</script>


