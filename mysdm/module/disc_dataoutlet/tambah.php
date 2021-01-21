<?PHP
include "config/koneksimysqli_ms.php";

$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$fgroupidcard=$_SESSION['GROUP'];
$fjbtid=$_SESSION['JABATANID'];
$pidcard=$_SESSION['IDCARD'];

$psudahtampil="";
$pidoutlet="";
$psektorid="";
$pnmoutlet="";
$palamat="";
$pprovinsi="";
$pkota="";
$pkdpos="";
$ptelp="";
$ppersonkontak="";
$pnotes="";
$pstsaktif="Y";
$pidcabang="";
$pidarea="";
$pidcust="";
$pdistcount="";
$pbonus="";
$pnodpl="";
$psdmdisc="";

$pigroupcust="";
$fcustidpilih="";

$pact=$_GET['act'];
$act="input";
if ($pact=="editdata"){
    $act="update";
    
    $pidoutlet=$_GET['id'];
    
    $query = "SELECT * FROM dbdiscount.t_outlet_dpl WHERE idoutlet_dpl='$pidoutlet'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pigroupcust=$row['igroup'];
    
    $psektorid=$row['isektorid'];
    $pnmoutlet=$row['nama_outlet'];
    $palamat=$row['alamat'];
    $pprovinsi=$row['provinsi'];
    $pkota=$row['kota'];
    $pkdpos=$row['kodepos'];
    $ptelp=$row['telp'];
    $ppersonkontak=$row['kontakperson'];
    $pnotes=$row['notes'];
    $pstsaktif=$row['aktif'];
    
    $pidcabang=$row['icabangid'];
    $pidarea=$row['areaid'];
    $pidcust=$row['icustid'];
    $pnodpl=$row['nodpl'];
    
    $pdistcount= $row['discount'];
    $pbonus= $row['bonus'];
    $psdmdisc= getfield("select disc as lcfields from dbdiscount.t_outlet_dpl_d WHERE idoutlet_dpl='$pidoutlet' AND distid='0000000000' LIMIT 1");
    
    if ($pigroupcust=="0") $pigroupcust="";
    
    $fcustidpilih="0000000XXX, ";
    $query = "SELECT icustid as icustid FROM dbdiscount.t_outlet_dpl WHERE IFNULL(igroup,'0')='$pigroupcust' AND IFNULL(igroup,'')<>''";
    $tampil2= mysqli_query($cnmy, $query);
    while ($row2= mysqli_fetch_array($tampil2)) {
        $fcustid=$row2['icustid'];
        $fcustidpilih .="".$fcustid.", ";
    }
    if (!empty($fcustidpilih)) $fcustidpilih=substr($fcustidpilih, 0, -2);
    
}



?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
    
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <!--kiri-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_id' name='e_id' placeholder="AUTO" class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidoutlet; ?>' Readonly>
                                    <input type='hidden' id='e_userinput' name='e_userinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    <input type='hidden' id='e_sdhtmpl' name='e_sdhtmpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahtampil; ?>' Readonly>
                                    <input type='hidden' id='e_grpid' name='e_grpid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pigroupcust; ?>' Readonly>
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='soflow' name='cb_semester' id='cb_semester' onchange="">
                                        <?php
                                        echo "<option value='1' selected>Semester 1 - 2021</option>";
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. DPL <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nodpl' name='e_nodpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodpl; ?>' required maxlength="50">
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sektor <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='soflow' name='cb_sektorid' id='cb_sektorid' onchange="">
                                        <?php
                                        echo "<option value='' selected>--Pilih--</option>";
                                        $query = "select iSektorId as isektorid, nama as nama from MKT.isektor WHERE "
                                                . " ISektorId IN ('02', '03', '08', '06', '09', '10', '14', '19', '22', '27', '23', '28', '29', '13', '99') "
                                                . " order by 1,2";
                                        $tampiledu= mysqli_query($cnmy, $query);
                                        while ($du= mysqli_fetch_array($tampiledu)) {
                                            $nidsektro=$du['isektorid'];
                                            $nnmsektro=$du['nama'];

                                            if ($nidsektro==$psektorid) 
                                                echo "<option value='$nidsektro' selected>$nidsektro - $nnmsektro</option>";
                                            else
                                                echo "<option value='$nidsektro'>$nidsektro - $nnmsektro</option>";

                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Outlet <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nmoutlet' name='e_nmoutlet' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmoutlet; ?>' required >
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_alamat' name='e_alamat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat; ?>' maxlength="200">
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Provinsi <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_provinsi' name='e_provinsi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pprovinsi; ?>' onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_kota' name='e_kota' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>' required onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Pos <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_kdpos' name='e_kdpos' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdpos; ?>' required >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Telp. / Hp. <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_telp' name='e_telp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptelp; ?>' maxlength="20">
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kontak Person <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_kontakperson' name='e_kontakperson' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppersonkontak; ?>' maxlength="100">
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                    
                    
                </div>
                <!--end kiri-->
                

                <!--kanan-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='soflow' name='cb_custpilih' id='cb_custpilih' onchange="DataPilihanSingleMulti()">
                                        <?php
                                        if ($pact=="editdata"){
                                            if (empty($pigroupcust)) {
                                                echo "<option value='S' selected>Single Customer</option>";
                                                echo "<option value='M' >Multi Customer</option>";
                                            }else{
                                                echo "<option value='M' selected>Multi Customer</option>";
                                            }
                                        }else{
                                            if (empty($pigroupcust)) {
                                                echo "<option value='S' selected>Single Customer</option>";
                                                echo "<option value='M' >Multi Customer</option>";
                                            }else{
                                                echo "<option value='S'>Single Customer</option>";
                                                echo "<option value='M' selected>Multi Customer</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='soflow' name='cb_cabangid' id='cb_cabangid' onchange="DataCabang()">
                                        <?php
                                        echo "<option value='' selected>--Pilih--</option>";
                                        if ($fjbtid=="38") {
                                            $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                                    . " JOIN hrd.rsm_auth as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                            $query .=" order by a.nama";
                                        }elseif ($fjbtid=="10" OR $fjbtid=="18") {
                                            $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                                    . " JOIN MKT.ispv0 as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                            $query .=" order by a.nama";
                                        }elseif ($fjbtid=="15") {
                                            $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                                    . " JOIN MKT.imr0 as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                            $query .=" order by a.nama";
                                        }else{
                                            $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE 1=1 ";
                                            $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -') ";
                                            $query .=" AND IFNULL(aktif,'')<>'N' ";
                                            $query .=" order by nama";
                                        }
                                        $tampiledu= mysqli_query($cnmy, $query);
                                        while ($du= mysqli_fetch_array($tampiledu)) {
                                            $nidcab=$du['icabangid'];
                                            $nnmcab=$du['nama'];

                                            if ($nidcab==$pidcabang) 
                                                echo "<option value='$nidcab' selected>$nnmcab</option>";
                                            else
                                                echo "<option value='$nidcab'>$nnmcab</option>";

                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='soflow' name='cb_areaid' id='cb_areaid' onchange="DataArea()">
                                        <?php
                                        echo "<option value='' selected>--Pilih--</option>";
                                        if (!empty($pidcabang)) {
                                            $query = "select icabangid as icabangid, areaid as areaid, nama as nama from MKT.iarea WHERE icabangid='$pidcabang' ";
                                            $query .=" AND IFNULL(aktif,'')<>'N' ";
                                            $query .=" order by nama";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nidarea=$du['areaid'];
                                                $nnmarea=$du['nama'];

                                                if ($nidarea==$pidarea) 
                                                    echo "<option value='$nidarea' selected>$nnmarea</option>";
                                                else
                                                    echo "<option value='$nidarea'>$nnmarea</option>";

                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            <div id="div_custsingle">
                            
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Customer <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_custid' id='cb_custid' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if (!empty($pidcabang) AND !empty($pidarea) AND empty($pigroupcust)) {
                                                $query = "select icabangid as icabangid, areaid as areaid, icustid as icustid, nama as nama from MKT.icust WHERE "
                                                        . " icabangid='$pidcabang' AND areaid='$pidarea' ";
                                                $query .=" AND IFNULL(aktif,'')<>'N' ";
                                                $query .=" order by CASE WHEN IFNULL(nama,'')='' then 'zzzz' else LTRIM(nama) end";
                                                $tampila= mysqli_query($cnms, $query);
                                                $ketemua= mysqli_num_rows($tampila);
                                                if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
                                                while ($arow= mysqli_fetch_array($tampila)) {
                                                    $nidcust=$arow['icustid'];
                                                    $nnmcust=$arow['nama'];
                                                    
                                                    if ($nidcust==$pidcust) 
                                                        echo "<option value='$nidcust' selected>$nnmcust ($nidcust)</option>";
                                                    else
                                                        echo "<option value='$nidcust'>$nnmcust ($nidcust)</option>";

                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            
                            </div>
                            
                            <div id="div_custmulti">
                                <!--<input type="checkbox" id="chkbtncust" value="select" onClick="SelAllCheckBox('chkbtncust', 'chkbox_custid[]')" />-->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12'>Customer &nbsp;<span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <div id="kotak-multi" class="jarak">
                                        <?PHP
                                            if (!empty($pidcabang) AND !empty($pidarea) AND !empty($pigroupcust)) {
                                                
                                                echo "<table id='tblcustomer'>";
                                                echo "<tbody>";
                                                
                                                $query = "select icabangid as icabangid, areaid as areaid, icustid as icustid, nama as nama from MKT.icust WHERE "
                                                        . " icabangid='$pidcabang' ";//AND areaid='$pidarea'
                                                $query .=" AND IFNULL(aktif,'')<>'N' ";
                                                $query .=" order by CASE WHEN IFNULL(nama,'')='' then 'zzzz' else LTRIM(nama) end ";
                                                $tampila= mysqli_query($cnms, $query);
                                                $ketemua= mysqli_num_rows($tampila);
                                                echo "<option value='' selected>--Pilih--</option>";
                                                while ($arow= mysqli_fetch_array($tampila)) {
                                                    $nidcust=$arow['icustid'];
                                                    $nnmcust=$arow['nama'];
                                                    
                                                    $nidcabang=$arow['icabangid'];
                                                    $nidarea=$arow['areaid'];
                                                    
                                                    $pidareaidcust=$nidcabang."|".$nidarea."|".$nidcust;
                                                    
                                                    $pchkpilihcust="";
                                                    if (strpos($fcustidpilih, $nidcust)==true) {
                                                        $pchkpilihcust="checked";
                                                    }
                                                    //echo "$fcustidpilih, $pchkpilihcust, $nidcust";exit;
                                                    //echo "<input type=checkbox value='$nidcust' name='chkbox_custid[]' $pchkpilihcust> $nnmcust ($nidcust)<br/>";
                                                    
                                                    $pchkcust="<input type=checkbox value='$pidareaidcust' name='chkbox_custid[]' $pchkpilihcust> $nnmcust ($nidcust)";
                                                    
                                                    echo "<tr>";
                                                    echo "<td class='divnone'>$nidcabang</td>";
                                                    echo "<td class='divnone'>$nidarea</td>";
                                                    echo "<td nowrap>$pchkcust</td>";
                                                    echo "</tr>";

                                                }
                                                
                                                echo "</tbody>";
                                                echo "</table>";
                                                
                                            }
                                        ?>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>All Discount Dist.<br/>
                                    <span style="color:blue;">*) masing-masing Dist. bisa diedit</span> <span class='required'></span></label>
                                <div class='col-xs-6'>
                                    <input type='text' id='e_discount' name='e_discount' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="IsiSemuaDataDist()" value='<?PHP echo $pdistcount; ?>' >
                                </div>
                            </div>
                            
                            <?PHP
                            $query = "select a.urutan, a.distid as distid, b.nama as nama, b.initial from dbdiscount.t_dist_pilih as a "
                                    . " LEFT JOIN MKT.distrib0 as b "
                                    . " on a.distid=b.Distid WHERE 1=1 ";
                            $query .= " order by a.urutan, b.nama, a.distid";
                            $tampil= mysqli_query($cnmy, $query);
                            while ($row= mysqli_fetch_array($tampil)) {
                                $niddist=$row['distid'];
                                $nnmdist=$row['nama'];
                                $nnminitialdist=$row['initial'];
                                //if ($niddist=="0000000000") $nnmdist="PT SDM";
                                $njmldistdist= getfield("select disc as lcfields from dbdiscount.t_outlet_dpl_d WHERE idoutlet_dpl='$pidoutlet' AND distid='$niddist'");
                                $pchkbox = "<span hidden><input type='checkbox' id='chk_kodeid[$niddist]' name='chk_kodeid[]' value='$niddist' checked><span>";
                                
                                echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>$nnminitialdist $pchkbox <span class='required'></span></label>";
                                    echo "<div class='col-xs-6'>";
                                        echo "<input type='text' id='e_jmldisc[$niddist]' name='e_jmldisc[$niddist]' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur='' value='$njmldistdist' >";
                                    echo "</div>";
                                echo "</div>";
                                
                            }
                            ?>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SDM <span class='required'></span></label>
                                <div class='col-xs-6'>
                                    <input type='text' id='e_sdmdiscount' name='e_sdmdiscount' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="" value='<?PHP echo $psdmdisc; ?>' >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bonus SDM <span class='required'></span></label>
                                <div class='col-xs-6'>
                                    <input type='text' id='e_sdmbonus' name='e_sdmbonus' class='form-control col-md-7 col-xs-12' onblur="" value='<?PHP echo $pbonus; ?>' >
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <textarea class='form-control' id='e_notes' name='e_notes' rows='3' placeholder='keterangan'><?PHP echo $pnotes; ?></textarea>
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div class="checkbox">
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <!--end kanan-->
                    
                
                
            </div>
            
        </form>
        
    </div>
    <!--end row-->
    
</div>

<!--<script src="module/mod_br_entrydcc/mytransaksi.js"></script>-->
<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
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
    #kotak-multi {
        resize: both;
        overflow: auto;
    }
    .divnone {
        display: none;
    }
</style>



<script>
    $(document).ready(function() {
        ShowBukaCustomer();
    } );
    
    function ShowBukaCustomer(){
        var ipilih=document.getElementById('cb_custpilih').value;
        
        if (ipilih=="M") {
            div_custsingle.style.display = 'none';
            div_custmulti.style.display = 'block';
        }else{
            div_custsingle.style.display = 'block';
            div_custmulti.style.display = 'none';
        }
    }
    
    
    function DataCabang() {
        ShowDataCabangArea();
    }
    
    function DataArea() {
        var ipilih=document.getElementById('cb_custpilih').value;
        if (ipilih=="M") {
            UrutkanTabelCustomer();
        }else{
            ShowDataAreaCustomer();
            $("#kotak-multi").html("");
        }
    }
    
    function DataPilihan() {
        var ipilih=document.getElementById('cb_custpilih').value;
        if (ipilih=="M") {
            $("#cb_custid").html("<option value=''>--Pilihan--</option>");
            ShowDataMultiCustomer();
            
        }else{
            ShowDataAreaCustomer();
            $("#kotak-multi").html("");
        }
    }
    
    function DataPilihanSingleMulti() {
        ShowBukaCustomer();
        DataPilihan();
    }
    

    function ShowDataCabangArea() {
        var idcab=document.getElementById('cb_cabangid').value;
        var ipilih=document.getElementById('cb_custpilih').value;
        $.ajax({
            type:"post",
            url:"module/disc_dataoutlet/viewdatadpl.php?module=viewdataareacabang",
            data:"udcab="+idcab+"&upilih="+ipilih,
            success:function(data){
                $("#cb_areaid").html(data);
                DataPilihan();
            }
        });
    }
    
    
    function ShowDataAreaCustomer() {
        var idcab=document.getElementById('cb_cabangid').value;
        var idarea=document.getElementById('cb_areaid').value;
        
        $.ajax({
            type:"post",
            url:"module/disc_dataoutlet/viewdatadpl.php?module=viewdatacustomer",
            data:"udcab="+idcab+"&udarea="+idarea,
            success:function(data){
                $("#cb_custid").html(data);
            }
        });
    }
    
    
    function ShowDataMultiCustomer() {
        var idcab=document.getElementById('cb_cabangid').value;
        var idarea=document.getElementById('cb_areaid').value;
        $.ajax({
            type:"post",
            url:"module/disc_dataoutlet/viewdatadpl.php?module=viewdatamulticustomer",
            data:"udcab="+idcab+"&udarea="+idarea,
            success:function(data){
                $("#kotak-multi").html(data);
                UrutkanTabelCustomer();
            }
        });
    }
    
    
    function UrutkanTabelCustomer() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("cb_areaid");
        filter = input.value.toUpperCase();
        table = document.getElementById("tblcustomer");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
    
    
    function IsiSemuaDataDist() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        
        var idisc=document.getElementById('e_discount').value;
        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_dist="e_jmldisc["+fields[0]+"]";
                
                document.getElementById(anm_dist).value=idisc;
                
            }
        }
    }
    
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }

    }
                    
    function disp_confirm(pText_,ket)  {
        var eidinput =document.getElementById('e_id').value;
        var enama =document.getElementById('e_nmoutlet').value;
        var ealamat=document.getElementById('e_alamat').value;
        
        var idcab=document.getElementById('cb_cabangid').value;
        var idarea=document.getElementById('cb_areaid').value;
        var idcust=document.getElementById('cb_custid').value;
        
        var ipilih=document.getElementById('cb_custpilih').value;
        
        if (enama=="") {
            alert("nama masih kosong....");
            return false;
        }
        
        if (ealamat=="") {
            //alert("alamat masih kosong....");
            //return false;
        }
        
        if (idcab=="") {
            alert("cabang masih kosong....");
            return false;
        }
        
        
        if (ipilih=="M") {
            
        }else{
            
            if (idarea=="") {
                alert("area masih kosong....");
                return false;
            }
        
            if (idcust=="") {
                alert("customer masih kosong....");
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
                document.getElementById("form_data01").action = "module/disc_dataoutlet/aksi_dataoutletdpl.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_data01").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    
    }
</script>
