<?php
include "config/koneksimysqli_ms.php";

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP'];


$pidinput="";
$pidcabang="";
$pnamadokt="";
$pgelar="";
$pnohp="";
$pidspesial="";
$pprofesi="Lain-Lain";

$preadnm="";
$act="input";
if ($pidact=="editdata"){
    $act="update";

    include "config/fungsi_ubahget_id.php";
    
    $pidinput_ec=$_GET['id'];
    $pidinput = decodeString($pidinput_ec);
    
    $preadnm=" readonly ";
    
    $sql = "select * from dr.masterdokter WHERE id='$pidinput'";
    $edit = mysqli_query($cnmy, $sql);
    $r    = mysqli_fetch_array($edit);
    
    $pidcabang=$r['icabangid'];
    $pnamadokt=$r['namalengkap'];
    $pprofesi=$r['profesi'];
    //$pgelar=$r['gelar'];
    $pnohp=$r['nohp'];
    $pidspesial=$r['spesialis'];
    
}

?>



<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="" data-live-search="true">
                                            <?PHP 
                                                if ($pidgroup=="1" OR $pidgroup=="24") {
                                                    $query = "select iCabangId as icabangid, nama as nama_cabang from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
                                                    $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                                    $query .=" order by nama, iCabangId";
                                                }else{
                                                    if ($pidjbt=="10" OR $pidjbt=="18") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.ispv0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }elseif ($pidjbt=="08") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.idm0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }elseif ($pidjbt=="20") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.ism0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }else{
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.imr0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }
                                                }
                                                $tampilket= mysqli_query($cnmy, $query);
                                                $ketemu=mysqli_num_rows($tampilket);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
                                                while ($du= mysqli_fetch_array($tampilket)) {
                                                    $nidcab=$du['icabangid'];
                                                    $nnmcab=$du['nama_cabang'];
                                                    $nidcab_=(INT)$nidcab;
    
                                                    if ($nidcab==$pidcabang)
                                                        echo "<option value='$nidcab' selected>$nnmcab ($nidcab_)</option>";
                                                    else
                                                    echo "<option value='$nidcab'>$nnmcab ($nidcab_)</option>";
    
    
                                                    $cno++;
                                                }
                                            ?>
                                          </select>
                                    </div>
                                </div>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Gelar <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_gelar' name='cb_gelar' onchange="" data-live-search="true">
                                            <?PHP 
                                                //echo "<option value='dr' selected>dr</option>";
                                                echo "<option value='' selected></option>";
                                            ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Profesi <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_profesi' name='cb_profesi' onchange="ShowDataSpesial()" data-live-search="true">
                                            <?PHP
                                            if ($pidact=="editdata"){
                                                $query = "select `id`, `profesi` from dr.profesi_dokter WHERE profesi='$pprofesi'";
                                            }else{
                                                $query = "select `id`, `profesi` from dr.profesi_dokter WHERE IFNULL(aktif,'')<>'N' Order By profesi";
                                            }
                                            $tampil=mysqli_query($cnms, $query);
                                            while ($row=mysqli_fetch_array($tampil)) {
                                                $pnidprof=$row['id'];
                                                $pnnmprof=$row['profesi'];
                                                if ($pnnmprof==$pprofesi)
                                                    echo "<option value='$pnnmprof' selected>$pnnmprof</option>";
                                                else
                                                    echo "<option value='$pnnmprof' >$pnnmprof</option>";
                                            }
                                            ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Lengkap <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_namadokt' name='e_namadokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadokt; ?>' maxlength="40" <?PHP echo $preadnm; ?>>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesialis <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_spesial' name='cb_spesial' onchange="" data-live-search="true">
                                            <?PHP
                                            echo "<option value='' selected></option>";
                                            if ($pprofesi == "Dokter" OR $pprofesi == "Profesor") {
                                                
                                                $query = "select `id`, `nama` as spesialis from ms2.`lookup` WHERE IFNULL(`type`,'')='spesialis' Order By nama";
                                                $tampil=mysqli_query($cnms, $query);
                                                while ($row=mysqli_fetch_array($tampil)) {
                                                    $pnidsp=$row['id'];
                                                    $pnnmsp=$row['spesialis'];
                                                    if ($pnnmsp==$pidspesial)
                                                        echo "<option value='$pnnmsp' selected>$pnnmsp</option>";
                                                    else
                                                        echo "<option value='$pnnmsp' >$pnnmsp</option>";
                                                }
                                            }
                                            ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Hp. <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nohp' name='e_nohp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnohp; ?>' maxlength="20">
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
function ShowDataSpesial() {
    var iprofesi = document.getElementById('cb_profesi').value;
    $.ajax({
        type:"post",
        url:"module/dkd/viewdatadkd.php?module=viewdataspesial",
        data:"uprofesi="+iprofesi,
        success:function(data){
            $("#cb_spesial").html(data);
        }
    });
    
}

function disp_confirm(pText_,ket)  {
    //ShowDataAtasan();
    //ShowDataJumlah();
    
    var iid = document.getElementById('e_id').value;
    var icabang = document.getElementById('cb_cabang').value;
    var igelar = document.getElementById('cb_gelar').value;
    var inama = document.getElementById('e_namadokt').value;
    var inohp = document.getElementById('e_nohp').value;
    var ispesial = document.getElementById('cb_spesial').value;
    var iprofesi = document.getElementById('cb_profesi').value;
    
    if (icabang=="") {
        alert("cabang masih kosong...");
        return false;
    }
    
    if (inama=="") {
        alert("nama masih kosong...");
        return false;
    }
    
    if (iprofesi=="Dokter") {
        if (ispesial=="") {
            alert("spesialis masih kosong...");
            return false;
        }
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
            document.getElementById("d-form2").action = "module/dkd/dkd_masterdokter/aksi_masterdokterdkd.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("d-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
    
}


</script>