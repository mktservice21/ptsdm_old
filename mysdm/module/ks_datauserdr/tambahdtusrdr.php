<?php
include "config/koneksimysqli_it.php";
//$cnit=$cnmy;

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD']; 

$hari_ini = date("Y-m-d");
$ptglcn = date('d/m/Y', strtotime($hari_ini));


$pidinput="";
$pkaryawanid="";
$pnmuserdr="";
$palamat1="";
$palamat2="";
$pkota="";
$pidspesial="";
$pbagian="";
$ptelp1="";
$ptelp2="";
$php="";
$pcn="";
$prpsaldo="";

$phiidendiv="";

$act="input";
if ($pidact=="editdata" OR $pidact=="editdatacn"){
    if ($pidact=="editdatacn") {
        $act="updatecn";
    }else{
        $phiidendiv="hidden";
        $act="update";
    }
    
    $pidinput=$_GET['id'];
    $pkaryawanid=$_GET['ikar'];
    
    $sql = "select a.dokterId as dokterid, a.nama as nama, a.spid as spid, a.bagian as bagian, "
            . " a.alamat1 as alamat1, a.alamat2 as alamat2, a.kota as kota, a.aktif as aktif, "
            . " a.user1 as user1, a.icabangid as icabangid, a.areaid as areaid,"
            . " a.telp as telp, a.telp2 as telp2, a.hp as hp, b.karyawanid as karyawanid, b.cn as cn, b.tgl as tgl, b.awal as awal "
            . " FROM hrd.dokter as a JOIN hrd.mr_dokt as b on a.dokterid=b.dokterid "
            . " WHERE a.dokterId='$pidinput' AND b.karyawanid='$pkaryawanid'";
    $edit = mysqli_query($cnit, $sql);
    $r    = mysqli_fetch_array($edit);
    
    
    $pnmuserdr=$r['nama'];
    $pidspesial=$r['spid'];
    $pbagian=$r['bagian'];
    $palamat1=$r['alamat1'];
    $palamat2=$r['alamat2'];
    $pkota=$r['kota'];
    $ptelp1=$r['telp'];
    $ptelp2=$r['telp2'];
    $php=$r['hp'];
    $pcn=$r['cn'];
    $ptglcn=$r['tgl'];
    
    $prpsaldo=$r['awal'];
    
    if ($ptglcn=="0000-00-00") $ptglcn="";
    if (!empty($ptglcn)) $ptglcn = date('d/m/Y', strtotime($ptglcn));
    
}

?>



<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="" data-live-search="true">
                                              
                                              <?PHP 
                                                    echo "<option value='' selected>--Pilihan--</option>";
                                                    $query = "select karyawanId, nama From hrd.karyawan
                                                        WHERE 1=1 ";
                                                    if ($pidact=="editdata"){
                                                        $query .= " AND karyawanid ='$pkaryawanid'";
                                                    }else{
                                                        if (!empty($pfilterkaryawan)) {
                                                            $query .= " AND karyawanid IN $pfilterkaryawan ";
                                                        }else{
                                                            /*
                                                            $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                            
                                                            $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                             * 
                                                             */
                                                            $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                            $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                        }
                                                    }
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkaryid=$z['karyawanId'];
                                                        $pkarynm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;
                                                        if ($z['karyawanId']==$pkaryawanid)
                                                            echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                        else
                                                            echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nmuserdr' name='e_nmuserdr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmuserdr; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesialis <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_spesialis' name='cb_spesialis' onchange="" data-live-search="true">
                                              
                                              <?PHP 
                                                    //echo "<option value='' selected>--Pilihan--</option>";
                                                    $query = "select spId as spid, nama as nama From hrd.spesial
                                                        WHERE IFNULL(aktif,'')='Y' ";
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pspid=$z['spid'];
                                                        $pspnm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;
                                                        if ($pspid==$pidspesial)
                                                            echo "<option value='$pspid' selected>$pspnm</option>";
                                                        else
                                                            echo "<option value='$pspid'>$pspnm</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bagian / RS <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_bagian' name='e_bagian' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbagian; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_alamat1' name='e_alamat1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat1; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_alamat2' name='e_alamat2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat2; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_kota' name='e_kota' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>' maxlength="30">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Telepon <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_telp1' name='e_telp1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptelp1; ?>' maxlength="30">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Telepon 2 <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_telp2' name='e_telp2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptelp2; ?>' maxlength="30">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>HP <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_hp' name='e_hp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $php; ?>' maxlength="30">
                                    </div>
                                </div>
                                
                                <div <?PHP echo $phiidendiv; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CN (%) <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_cn' name='e_cn' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pcn; ?>'>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $phiidendiv; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal CN </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglcn' name='e_tglcn' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglcn; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $phiidendiv; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo Awal <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_saldorp' name='e_saldorp' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpsaldo; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
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

<script>
function disp_confirm(pText_,ket)  {
    //ShowDataAtasan();
    //ShowDataJumlah();
    
    var iid = document.getElementById('e_id').value;
    var ikry = document.getElementById('cb_karyawan').value;
    var inama = document.getElementById('e_nmuserdr').value;
    var ibagian = document.getElementById('e_bagian').value;
    var ialamat1 = document.getElementById('e_alamat1').value;
    var ikota = document.getElementById('e_kota').value;
    var itlp1 = document.getElementById('e_telp1').value;
    
    if (ikry=="") {
        alert("karyawan masih kosong...");
        return false;
    }
    
    if (inama=="") {
        alert("nama masih kosong...");
        return false;
    }
    
    if (ibagian=="") {
        alert("bagian / rs masih kosong...");
        return false;
    }
    
    if (ialamat1=="") {
        alert("alamat masih kosong...");
        return false;
    }
    
    if (ikota=="") {
        alert("kota masih kosong...");
        return false;
    }
    
    if (itlp1=="") {
        alert("telepon masih kosong...");
        return false;
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
            document.getElementById("demo-form2").action = "module/ks_datauserdr/aksi_datauserdr.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
    
}


</script>