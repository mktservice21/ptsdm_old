<?PHP
session_start();
$aksi="";
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];
$fgroupid=$_SESSION['GROUP'];
$fstsadmin=$_SESSION['STSADMIN'];
$flvlposisi=$_SESSION['LVLPOSISI'];
$fdivisi=$_SESSION['DIVISI'];
$psudahabsen=false;

?>


<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Absensi - Upload Foto</h4>
        </div>
        <br/>
        <div class="">
            
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="x_panel">
                        
                        <div class="x_content">
                            <div class="dashboard-widget-content">
                                <form id="form">
                                    <div id="my_camera"></div>
                                    <br/>
                                    <hr/>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-8'>
                                            <?PHP
                                            if ($psudahabsen==true) {
                                                echo "";
                                            }else{
                                            ?>
                                                <button type='button' class='btn btn-info' id="ibuttonsave" onclick='disp_confirm_absen("")'>Absen Masuk</button>

                                            <?PHP
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
</div>

<!-- jquery  -->
<script src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
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
    // saat dokumen selesai dibuat jalankan fungsi update
    $(document).ready(function () {
        //update();
    });

    // jalankan aksi saat tombol register disubmit
    $(".tombol-simpan").click(function () {
        event.preventDefault();

        // membuat variabel image
        var image = '';

        //mengambil data uername dari form di atas dengan id name
        var name = $('#name').val();

        //mengambil data email dari form di atas dengan id email
        var email = $('#email').val();

        //memasukkan data gambar ke dalam variabel image
        Webcam.snap(function (data_uri) {
            image = data_uri;
        });

        //mengirimkan data ke file action.php dengan teknik ajax
        $.ajax({
            url: 'action.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                image: image
            },
            success: function () {
                alert('input data berhasil');
                // menjalankan fungsi update setelah kirim data selesai dilakukan 
                update()
            }
        })
    });


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