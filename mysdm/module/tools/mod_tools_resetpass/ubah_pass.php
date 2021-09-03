<?PHP
    
    $pmodule=""; $pact=""; $pidmenu="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pilih = date('d F Y', strtotime($hari_ini));
    
    $pperiode_ = date('Ym', strtotime($hari_ini));
    
    $fnmkaryawan=$_SESSION['NAMALENGKAP'];
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    
    $perror="";
    $pketeksekusi="";
    if (isset($_GET['iderror'])) $perror=$_GET['iderror'];
    if (isset($_GET['keteks'])) $pketeksekusi=$_GET['keteks'];
    
    
?>




<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Data Karyawan (Ubah PIN/Password)";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Ubah PIN/Password";
                else
                    echo "$judul";
                ?>
            </h3>
            anda harus ubah password agar bisa masuk ke menu yang lain.
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/purchasing/pch_barang/laporan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
                
                <?PHP
                    $aksi="";
                    $act="lokupdatekrypass";
                    $pidkaryawan = $fkaryawan;

                    $query = "select nama, pin from hrd.karyawan WHERE karyawanId='$pidkaryawan'";
                    $tampil= mysqli_query($cnmy, $query);
                    $row=mysqli_fetch_array($tampil);
                    $pnamakaryawan=$row['nama'];
                    $ppin_pass=$row['pin'];
                    $ppin_pass="";

                    if (empty($pnamakaryawan)) {
                        $query = "select nama, pin from dbmaster.t_karyawan_khusus WHERE karyawanId='$pidkaryawan'";
                        $tampil= mysqli_query($cnmy, $query);
                        $row=mysqli_fetch_array($tampil);
                        $pnamakaryawan=$row['nama'];
                    }


                    $perror="";
                    $pketeksekusi="";
                    if (isset($_GET['iderror'])) $perror=$_GET['iderror'];
                    if (isset($_GET['keteks'])) $pketeksekusi=$_GET['keteks'];

                ?>

                <script> window.onload = function() { document.getElementById("txt_pin").focus(); } </script>

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">

                        <div class="x_panel">
                            <?PHP

                            if ($perror=="error" OR $perror=="berhasil") {

                                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                                    echo "<div class='x_panel'>";

                                        echo "<div class='x_title'>";
                                            if ($perror=="error") {
                                                echo "<h2 style='color:red;'>Gagal ubah password</h2>";
                                                echo "<div class='clearfix'></div>";
                                                echo "<div>($pketeksekusi)</div>";
                                            }elseif ($perror=="berhasil") {
                                                echo "<h2 style='color:blue;'>password berhasil diubah, silakan logout dan login ulang...</h2>";
                                            }
                                            echo "<ul class='nav navbar-right panel_toolbox'><li><a class='close-link'><i class='fa fa-close'></i></a></li></ul>";
                                            echo "<div class='clearfix'></div>";

                                        echo "</div>";

                                    echo "</div>";

                                echo "</div>";

                            }
                            ?>

                            <div class="x_content">
                                <br />

                                <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
                                      id='d-form3' name='form1' data-parsley-validate 
                                      class='form-horizontal form-label-left'>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">ID <span class="required">*</span></label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" id="txt_idkaryawan" name="txt_idkaryawan" required="required" class="form-control col-md-7 col-xs-12" value="<?PHP echo $pidkaryawan; ?>" Readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nama Karyawan <span class="required">*</span></label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" id="txt_karyawan" name="txt_karyawan" required="required" class="form-control col-md-7 col-xs-12" value="<?PHP echo $pnamakaryawan; ?>" Readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">New Password <span class="required">*</span></label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="password" autocomplete="off" id="txt_pin" name="txt_pin" required="required" class="form-control col-md-7 col-xs-12" value="<?PHP echo $ppin_pass; ?>" maxlength="6" >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Ulangi Password <span class="required">*</span></label>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="password" autocomplete="off" id="txt_pin2" name="txt_pin2" required="required" class="form-control col-md-7 col-xs-12" value="<?PHP echo $ppin_pass; ?>" maxlength="6" >
                                            <b style="color:red;">*) panjang Password harus 6 karakter terdiri dari huruf dan angka, tanpa Space.</b>
                                        </div>
                                    </div>



                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <button type='button' id="btn_simpan" class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Simpan</button>
                                        </div>
                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>
                </div>


                <script>

                    function disp_confirm(pText_, ket) {
                        var ikryid = document.getElementById('txt_idkaryawan').value;
                        var ikrynm = document.getElementById('txt_karyawan').value;
                        var ipin1 = document.getElementById('txt_pin').value;
                        var ipin2 = document.getElementById('txt_pin2').value;

                        var myInputPwd = document.getElementById("txt_pin");

                        // Validate lowercase letters
                        var lowerCaseLetters = /[a-z]/g;
                        var ilowcar=false;
                        if(myInputPwd.value.match(lowerCaseLetters)) {  
                            //alert("valid"); return false;
                            ilowcar=true;
                        } else {
                            //alert("invalid"); return false;
                            ilowcar=false;
                        }

                        // Validate capital letters
                        var upperCaseLetters = /[A-Z]/g;
                        var iupper=false;
                        if(myInputPwd.value.match(upperCaseLetters)) {  
                            //alert("valid"); return false;
                            var iupper=true;
                        } else {
                            //alert("invalid"); return false;
                            var iupper=false;
                        }


                        // Validate numbers
                        var numbers = /[0-9]/g;
                        var number=false;
                        if(myInputPwd.value.match(numbers)) {  
                            //alert("valid"); return false;
                            number=true;
                        } else {
                            //alert("invalid"); return false;
                            number=false;
                        }

                        // Validate length
                        var lengthoke=false;
                        if(myInputPwd.value.length >= 6) {
                            //alert("valid"); return false;
                            lengthoke=true;
                        } else {
                            //alert("invalid"); return false;
                            lengthoke=false;
                        }

                        if (lengthoke==false) {
                            alert("panjang password harus 6 karakter.\n\
                perpaduan huruf dan angka"); return false;
                        }

                        if (ilowcar==false && iupper==false) {
                            alert("harus perpaduan huruf dan angka"); return false;
                        }

                        if (number==false) {
                            alert("harus perpaduan huruf dan angka"); return false;
                        }


                        if (ikryid=="") {
                            alert("Karyawan Kosong"); return false;
                        }

                        if (ikrynm=="") {
                            alert("Karyawan Kosong"); return false;
                        }

                        if (ipin1=="") {
                            alert("Password tidak boleh kosong..."); document.getElementById('txt_pin').focus(); return false;
                        }

                        if (ipin1==ipin2) {
                        }else{
                            alert("Password tidak sama..."); document.getElementById('txt_pin2').focus(); return false;
                        }

                        var r=confirm(pText_)
                        if (r==false) {
                            return false;
                        }



                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        //document.write("You pressed OK!")
                        document.getElementById("d-form3").action = "module/tools/mod_tools_resetpass/aksi_resetpass.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                        document.getElementById("d-form3").submit();
                        return 1;

                    }
                </script>                
                

                <?PHP

            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>