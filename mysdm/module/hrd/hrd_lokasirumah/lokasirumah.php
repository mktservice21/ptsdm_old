<?PHP 
    date_default_timezone_set('Asia/Jakarta');
    //include "config/cek_akses_modul.php"; 
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    
    $pkaryawanid=$_SESSION['IDCARD'];
    $pkaryawannm=$_SESSION['NAMALENGKAP'];
    $psudahinput=false;
    $plangitude="";//-6.1910063
    $plongitude="";//106.8500943    -6.190690628090547, 106.85030691323897
    $pradius=0.10;
    $pidstatus="HO1";
    $phidenpeta="hidden";
    
    $query = "select * from hrd.karyawan_absen WHERE karyawanid='$pkaryawanid'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $psudahinput=true;
        $row= mysqli_fetch_array($tampil);
        $plangitude=$row['a_latitude'];
        $plongitude=$row['a_longitude'];
        $pradius=$row['a_radius'];
        $pidstatus=$row['id_status'];
        $phidenpeta="";
    }
    
    
    $judul="Lokasi WFH";
    
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
                
                
                <?PHP
                $pketeksekusi="";
                if (isset($_GET['iderror'])) $pketeksekusi=$_GET['iderror'];
                if ($pact=="error" OR $pact=="berhasil") {
                
                    echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                        echo "<div class='x_panel'>";

                            echo "<div class='x_title'>";
                                if ($pact=="error") {
                                    echo "<h2 style='color:red;'>Gagal simpan data</h2>";
                                    echo "<div class='clearfix'></div>";
                                    echo "<div>($pketeksekusi)</div>";
                                }elseif ($pact=="berhasil") {
                                    echo "<h2 style='color:blue;'>Data berhasil disimpan</h2>";
                                }
                                echo "<ul class='nav navbar-right panel_toolbox'><li><a class='close-link'><i class='fa fa-close'></i></a></li></ul>";
                                echo "<div class='clearfix'></div>";

                            echo "</div>";

                        echo "</div>";

                    echo "</div>";
                
                }
                ?>

                <div class='col-md-12 col-sm-12 col-xs-12'>
                    
                    <div class='x_panel'>

                        <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                              id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>

                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a> &nbsp; &nbsp; &nbsp;
                                </h2>
                                <div class='clearfix'></div>
                            </div>
                            
                            <div class='x_content'>
                            <div id="my_camera"></div>
                            </div>
                            <div class='clearfix'></div>
                            
                                <div class='x_content'>


                                    <div class='col-md-12 col-sm-12 col-xs-12'>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='hidden' id='e_idkaryawan' name='e_idkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkaryawanid; ?>' Readonly>
                                                <input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkaryawannm; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Lokasi (Status) <span class='required'></span></label>
                                            <div class='col-xs-4'>
                                                  <select class='form-control input-sm' id='cb_lokasists' name='cb_lokasists' onchange="" data-live-search="true">
                                                    <?PHP
                                                        //$pidstatus
                                                        echo "<option value='HO1' selected>HO1</option>";
                                                    ?>
                                                  </select>
                                            </div>
                                        </div>
                                        
                                        <?PHP
                                        if ($psudahinput==false) {
                                        ?>
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <button type='button' class='tombol-simpan btn-xs btn-dark' id='ibuttontampil' onclick="getLocation()">Tampilkan Lokasi</button>
                                                </div>
                                            </div>
                                        <?PHP
                                        }
                                        ?>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Latitude <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_lat' name='e_lat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $plangitude; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Longitude <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_long' name='e_long' class='form-control col-md-7 col-xs-12' value='<?PHP echo $plongitude; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div id="div_petalokasi" <?PHP echo $phidenpeta;?> >
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <button type='button' class='tombol-simpan btn-xs btn-success' id='ibuttontampil' onclick="ShowIframeMapsPerson()">Lihat Peta Lokasi</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Radius <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_radius' name='e_radius' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pradius; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <?PHP
                                                if ($psudahinput==true) {
                                                    echo "";
                                                }else{
                                                    echo "<button type='button' class='tombol-simpan btn btn-info' id='ibuttonsave' onclick=\"disp_confirm_lokasi()\">Simpan</button>";
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




<script type="text/javascript">

    $(document).ready(function() {
        //getLocation();
    } );
    
    var x = document.getElementById("d_lokasi");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
            div_petalokasi.style.display='block';
        } else {
            alert("Geolocation tidak support pada browser ini.");
            //x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        document.getElementById("e_lat").value=position.coords.latitude;
        document.getElementById("e_long").value=position.coords.longitude;
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



    function disp_confirm_lokasi()  {
        
        getLocation();
        
        setTimeout(function () {
            disp_confirm_ext()
        }, 500);
        
    }
    
    function disp_confirm_ext()  {
        
        var pText_="Apakah akan melakukan simpan...?\n\
Data yang sudah disimpan tidak bisa diubah lagi...";
        var r=confirm(pText_)
        if (r==true) {
        }else{
            return false;
        }
        
        var eidkry=document.getElementById('e_idkaryawan').value;
        var nlat=document.getElementById('e_lat').value;
        var nlong=document.getElementById('e_long').value;
        
        if (eidkry==""){
            alert("karyawan kosong....");
            return 0;
        }
        
        if (nlat=="" || nlong=="") {
            alert("Lokasi Kosong"); return false;
        }
        
        var ket="simpanlokasi";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        //document.write("You pressed OK!")
        document.getElementById("d-form2").action = "module/hrd/hrd_lokasirumah/aksi_lokasirumah.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
        document.getElementById("d-form2").submit();
        return 1;
        
    }
    

    function ShowIframeMapsPerson() {
        var slatitude=document.getElementById('e_lat').value;
        var slongitude=document.getElementById('e_long').value;
        
        $.ajax({
            url: 'module/hrd/hrd_lokasirumah/peta_lokasiwfh.php?module=showiframemaps',
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
                                
</script>


