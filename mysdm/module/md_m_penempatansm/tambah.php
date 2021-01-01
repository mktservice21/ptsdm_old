<script>
function getDataCabang(data1, data2){
    $.ajax({
        type:"post",
        url:"config/viewdata_ms.php?module=viewdatacabang&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalCabang(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function getDataKaryawanJbt(data1, data2, kdjbt){
    
    $.ajax({
        type:"post",
        url:"config/viewdata_ms.php?module=viewkaryawan&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}


function disp_confirm(pText_)  {
    var ecab =document.getElementById('e_idcabang').value;
    var ebuat =document.getElementById('e_idkaryawan').value;
    
    
    if (ecab==""){
        alert("cabang masih kosong....");
        return 0;
    }
    if (ebuat==""){
        alert("karyawan masih kosong....");
        return 0;
    }

    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/md_m_penempatansm/aksi_penempatansm.php";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        return 0;
    }
}

</script>


<?PHP
$act="input";
$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));

$idcab="";
$nmcab="";
$idajukan=""; 
$nmajukan=""; 
$stsaktif1="checked";
$stsaktif2="";
$stsaktif="N";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_penempatansm WHERE icabangid='$_GET[idcab]' "
            . "and karyawanid='$_GET[id]' and Date_format(awal,'%Y%m%d')='$_GET[tgl]'");
    $r    = mysqli_fetch_array($edit);
    $tglinput = date('d F Y', strtotime($r['awal']));
    $idcab=$r['icabangid'];
    $nmcab=$r['nama_cabang'];
    $idajukan=$r['karyawanid']; 
    $nmajukan=$r['nama'];
    if ($r['aktif']=="Y") {
        $stsaktif="Y";
    }else{
        $stsaktif1="";
        $stsaktif2="checked";
    }
    
}
    
?>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class='col-md-12 col-sm-12 col-xs-12'>
    <div class='x_panel'>
        <div class='x_title'>
            <h2>
                <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
                <?PHP if ($_GET['act']=="tambahbaru"){ ?>
                    <small>tambah data</small>
                <?PHP } else { ?>
                    <small>edit data</small>
                <?PHP } ?>
            </h2>
            <div class='clearfix'></div>
        </div>
        
        <div class='x_content'>
            <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                  id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>
                
                <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
                <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
                <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
                
                <?PHP if ($_GET['act']=="editdata"){ ?>
                    <input type='hidden' class='form-control' id='l_idcabang' name='l_idcabang' value='<?PHP echo $idcab; ?>' Readonly>
                    <input type='hidden' class='form-control' id='l_idkaryawan' name='l_idkaryawan' value='<?PHP echo $idajukan; ?>' Readonly>
                    <input type='hidden' class='form-control' id='l_tglinput' name='l_tglinput'  value='<?PHP echo $tglinput; ?>' Readonly>
                    <input type='hidden' class='form-control' id='l_aktif' name='l_aktif'  value='<?PHP echo $stsaktif; ?>' Readonly>
                <?PHP } ?>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_cabang'>Cabang <span class='required'></span></label>
                    <div class='col-md-6 col-sm-6 col-xs-12'>
                        <div class='input-group '>
                        <span class='input-group-btn'>
                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataCabang('e_idcabang', 'e_cabang')">Go!</button>
                        </span>
                        <input type='hidden' class='form-control' id='e_idcabang' name='e_idcabang' value='<?PHP echo $idcab; ?>' Readonly>
                        <input type='text' class='form-control' id='e_cabang' name='e_cabang' value='<?PHP echo $nmcab; ?>' Readonly>
                        </div>
                    </div>
                </div>
                
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Karyawan <span class='required'></span></label>
                    <div class='col-md-6 col-sm-6 col-xs-12'>
                        <div class='input-group '>
                        <span class='input-group-btn'>
                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataKaryawanJbt('e_idkaryawan', 'e_karyawan', '20')">Go!</button>
                        </span>
                        <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $idajukan; ?>' Readonly>
                        <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='<?PHP echo $nmajukan; ?>' Readonly>
                        </div>
                    </div>
                </div>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tanggal </label>
                    <div class='col-md-6 col-sm-6 col-xs-12'>
                        <div class='input-group date' id='tgl01'>
                            <input type='text' id='e_tglinput' name='e_tglinput' required='required' class='form-control col-md-7 col-xs-12' placeholder='tanggal input' value='<?PHP echo $tglinput; ?>' placeholder='dd mmm yyyy' Readonly>
                            <span class='input-group-addon'>
                                <span class='glyphicon glyphicon-calendar'></span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Status <span class='required'></span></label>
                    <div class='col-md-6 col-sm-6 col-xs-12'>
                        <div class='btn-group' data-toggle='buttons'>
                            <label class='btn btn-default'><input type='radio' class='flat' name='rb_status' id='rb_status1' value='Y' <?PHP echo $stsaktif1; ?>> Aktif </label>
                            <label class='btn btn-default'><input type='radio' class='flat' name='rb_status' id='rb_status2' value='N' <?PHP echo $stsaktif2; ?>> Non Aktif </label>
                        </div>
                    </div>
                </div>
                
                
            </form>
        </div>
    </div>
</div>