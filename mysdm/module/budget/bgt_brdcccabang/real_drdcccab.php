<?php

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$sudahapv="";

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];

$act="realisasiupdate";

include "config/fungsi_ubahget_id.php";

$pidinput_ec=$_GET['id'];
$pidinput = decodeString($pidinput_ec);

$edit = mysqli_query($cnmy, "SELECT * FROM ms2.br WHERE id='$pidinput'");
$jmlrw0=mysqli_num_rows($edit);
$row= mysqli_fetch_array($edit);

$pidpengaju=$row['createdby'];
$ptglinput=$row['tanggal'];

$pjumlah=$row['jumlah'];
$pjmlreal=$row['jumlah1'];
$pketerangan=$row['keterangan'];
$ptglajukan = date('d/m/Y', strtotime($ptglinput));



$query = "select nama from hrd.karyawan where karyawanId='$pidpengaju'";
$tampil=mysqli_query($cnmy, $query);
$row1= mysqli_fetch_array($tampil);
$pnmpengaju=$row1['nama'];
    
    
?>

<script> window.onload = function() { document.getElementById("e_jmlreal").focus(); } </script>

<div class="">

    
    <!--row-->
    <div class="row">


        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>

                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                        id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>


                    <!--kiri-->
                    <div class='col-md-12 col-xs-12'>
                        
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <div class='input-group date' id='mytgl01_'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglajukan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <input type='hidden' id='e_idkaryawan' name='e_idkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidpengaju; ?>' Readonly>
                                        <input type='text' id='e_namakaryawan' name='e_namakaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmpengaju; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class=''></span></label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>' readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Realisasi <span class=''></span></label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <input type='text' id='e_jmlreal' name='e_jmlreal' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlreal; ?>' >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class=''></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea class='form-control' id='e_keterangan' name='e_keterangan' readonly><?PHP echo $pketerangan; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class=''></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                    </div>
                                </div>
                                
                                
                                
                                
                            </div>
                        </div>
                        
                    </div>
                    
                    
                    
                </form>
                
            </div>
            
        </div>
        
        
    </div>
    
    
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

<script>
    function disp_confirm(pText_, ket, data_img) {
        var iid = document.getElementById('e_id').value;
        var ikry = document.getElementById('e_idcarduser').value;
        
        if (iid=="") {
            alert("ID masih kosong...");
            return false;
        }
        
        if (ikry=="") {
            alert("Pembuat masih kosong...");
            return false;
        }
        
        var iket_save="Apakah akan melakukan simpan data realisasi...?";
        
        var cmt = confirm(iket_save);
        if (cmt == false) {
            return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");


        document.getElementById("form_data1").action = "module/budget/bgt_brdcccabang/aksi_brdcccabang.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
        document.getElementById("form_data1").submit();

        return false;
        
        
    }
</script>
