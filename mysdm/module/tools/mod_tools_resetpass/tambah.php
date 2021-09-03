<?PHP
    $dari_loginawal=false;
    if (isset($_GET['sloginawal'])) {
        if ($_GET['sloginawal']=="awal") $dari_loginawal=true;
    }
    
    include "config/fungsi_ubahget_id.php";
    
    $aksi="";
    $act="update";
    $pidbr_ec=$_GET['id'];
    $pidkaryawan = decodeString($pidbr_ec);
    
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
    
    $act="updatepass";
    if ($dari_loginawal==true) $act="updatepassawal";
    
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
            if ($dari_loginawal==false) {
                echo "<div class='x_title'>";
                    echo "<h2><a class='btn btn-default' href=\"?module=$pmodule&act=homeback&idmenu=$pidmenu&nmun=$pidmenu\">Back</a> </h2>";
                    echo "<div class='clearfix'></div>";
                echo "</div>";
            }
            
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
                      id='demo-form2' name='form1' data-parsley-validate 
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
                            <!-- data-inputmask="'mask' : '_____'" -->
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

<style>
/*
input#txt_pin {
  display: inline-block;
  
  padding: 0;
  background: repeating-linear-gradient(90deg, dimgrey 0, dimgrey 1ch, transparent 0, transparent 1.5ch) 0 100%/ 10ch 2px no-repeat;
  
}
*/
</style>
<script>
    /*
    var myInput = document.getElementById("txt_pin");
    myInput.onkeyup = function() {
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if(myInput.value.match(lowerCaseLetters)) {  
            alert("valid"); return false;
        } else {
            alert("invalid"); return false;
        }
    }
    */
    
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
        document.getElementById("demo-form2").action = "module/tools/mod_tools_resetpass/aksi_resetpass.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
        document.getElementById("demo-form2").submit();
        return 1;
        
    }
</script>