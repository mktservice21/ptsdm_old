<?php
//include "config/koneksimysqli_it.php";
$cnit=$cnmy;

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 


$pidinput="";
$pkaryawanid="";
$pnmapotik="";
$palamat1="";
$palamat2="";
$pkota="";

$act="input";
if ($pidact=="editdata"){
    $act="update";
    $pidinput=$_GET['id'];
    
    $sql = "select idapotik as idapotik, srid as srid, aptId as aptid, apt_id as apt_id, aptType as apttype, nama as nama, "
            . " alamat1 as alamat1, alamat2 as alamat2, kota as kota, aktif as aktif, "
            . " user1 as user1, icabangid as icabangid, areaid as areaid FROM hrd.mr_apt WHERE idapotik='$pidinput'";
    $edit = mysqli_query($cnit, $sql);
    $r    = mysqli_fetch_array($edit);
    
    $pkaryawanid=$r['srid'];
    $pnmapotik=$r['nama'];
    $palamat1=$r['alamat1'];
    $palamat2=$r['alamat2'];
    $pkota=$r['kota'];
    
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
                                                            $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                            $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Apotik <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nmapotik' name='e_nmapotik' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmapotik; ?>' maxlength="40">
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
    var inama = document.getElementById('e_nmapotik').value;
    var ialamat1 = document.getElementById('e_alamat1').value;
    var ikota = document.getElementById('e_kota').value;
    
    if (ikry=="") {
        alert("karyawan masih kosong...");
        return false;
    }
    
    if (inama=="") {
        alert("nama apotik masih kosong...");
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

    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/ks_dataapotik/aksi_dataapotik.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
    
}


</script>