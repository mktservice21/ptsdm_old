<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />


<?php
//kodeperiode dijadikan id karyawan kontrak

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];

$hari_ini = date("Y-m-d");
$mytglini="";
$mytglini = getfield("select CURRENT_DATE as lcfields");
if ($mytglini==0) $mytglini="";
if (!empty($mytglini)) $hari_ini = date('Y-m-d', strtotime($mytglini));
$iniharinya=date('d', strtotime($mytglini));

$tglberlku = date('F Y', strtotime($hari_ini));
$ptgl1 = date('01/m/Y', strtotime($hari_ini));
$ptgl2 = date('t/m/Y', strtotime($hari_ini));


$idrutin="";

$idajukan=$_SESSION['IDCARD'];

$pidjabatan="";
$pidcabang="";
$pidarea="";
$pnm_krynonelama_="";
$pkd_krynone_="";
$pnm_krynonebaru_="";
$pkrnonereadonly="";

$patasan1="";
$patasan2="";
$patasan3="";
$patasan4="";

$psudahapv="";

$pketerangan="";
$ptotalsemua=0;

if ($pidact=="editdata") {
    $idrutin=$_GET['id'];
    $editdt= mysqli_query($cnmy, "select * from dbmaster.t_ca0 where idca='$idrutin'");
    $row= mysqli_fetch_array($editdt);
    
    $idajukan=$row['karyawanid'];
    $ptgl1=$row['periode'];
    $tglberlku = date('F Y', strtotime($ptgl1));
    
    $pketerangan=$row['keterangan'];
    $ptotalsemua=$row['jumlah'];
    
    $psudahapv=$row['stsnonaktif'];
    if ($psudahapv=="Y") $psudahapv="reject";
    
    $pidjabatan=$row['jabatanid'];
    $pidcabang=$row['icabangid_o'];
    $pidarea=$row['areaid_o'];
    
    $patasan1=$row['atasan1'];
    $patasan2=$row['atasan2'];
    $patasan3=$row['atasan3'];
    $patasan4=$row['atasan4'];
    
    
    if ($patasan1==$idajukan) $patasan1="";
    if ($patasan2==$idajukan) $patasan2="";
    if ($patasan3==$idajukan) $patasan3="";
    if ($patasan4==$idajukan) $patasan4="";
    
    //kodeperiode dijadikan id karyawan kontrak
    $pnm_krynonelama_=$row['nama_karyawan'];
    $pkd_krynone_=$row['kodeperiode'];
    $pnm_krynonebaru_=$row['nama_karyawan'];
    
    
    
    if ($idajukan=="0000002200" || $idajukan=="0000002083" || (DOUBLE)$idajukan==2200 || (DOUBLE)$idajukan==2083) {
        $pkrnonereadonly=" Readonly ";
        if ($pidjabatan=="37" OR (DOUBLE)$pidjabatan==37) $pidjabatan="";
        if (empty($pkd_krynone_)) {
            $query = "select `id` as idkry from dbmaster.t_karyawan_kontrak WHERE divisi='OTC' AND nama='$pnm_krynonebaru_' AND icabangid_o='$pidcabang' AND areaid_o='$pidarea' AND atasan1='$patasan1' AND atasan2='$patasan2' AND atasan4='$patasan4' ORDER BY `id`";
            $tampil1_=mysqli_query($cnmy, $query);
            $nr= mysqli_fetch_array($tampil1_);
            $pkd_krynone_=$nr['idkry'];
        }
    }

    
    
}
?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                
                
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
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idrutin; ?>' Readonly>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="showDataUtkKaryawan()">
                                            <option value='' selected>--Pilihan--</option>
                                            <?PHP
                                            $query = "select b.karyawanid, b.nama from hrd.karyawan b WHERE b.aktif='Y' and (IFNULL(b.tglkeluar,'')='' OR IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00') ";
											//$query .= " AND IFNULL(b.jabatanid,'') NOT IN ('35') ";
                                            $query .= " AND b.divisiId='OTC' ";
                                            $query .= " AND b.karyawanid NOT IN ('0000001272', '0000000432', '0000000992') ";
                                            $query .=" AND b.karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                            $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ')  "
                                                    . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                                    . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
                                                    . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
                                            
                                            $query .= " OR b.karyawanid='$idajukan' ";
                                            $query .= " order by b.nama";
                                            $tampil=mysqli_query($cnmy, $query);
                                            while ($rt= mysqli_fetch_array($tampil)) {
                                                $pkryid=$rt['karyawanid'];
                                                $pnmkry=$rt['nama'];
                                                
                                                if ($pkryid==$idajukan)
                                                    echo "<option value='$pkryid' selected>$pnmkry</option>";
                                                else
                                                    echo "<option value='$pkryid'>$pnmkry</option>";
                                            }
                                            ?>
                                        </select>
                                        
                                        
                                        <div id="kry_none_" style="display:none;">
                                        
                                            <input type='hidden' class='form-control' id='e_nmkrynone2' name='e_nmkrynone2' autocomplete="off" value='<?PHP echo $pnm_krynonelama_; ?>'>
                                            <input type='hidden' id='e_kdkrynone' name='e_kdkrynone' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkd_krynone_; ?>'>
                                            <?PHP
                                            if ($pidact=="editdata") {
                                            }else{
                                                echo "*) <span style='color:blue;'><b>jika diklik Close / Esc atau Nama dirubah satu hurup saja, maka status karyawan akan menjadi karyawan baru...</b></span>";
                                            }
                                            ?>
                                            <input type="text" class='form-control' id="e_nmkrynone" name="e_nmkrynone" size="50px" placeholder="cari data..."
                                                   onkeyup="cariFormData(this.id, 'e_kdkrynone', 'myDivSearching2', 'carikaryawankontrak')" 
                                                   onkeydown="checkkey()" 
                                                   autocomplete="off" value="<?PHP echo $pnm_krynonebaru_; ?>" <?PHP echo $pkrnonereadonly; ?>/>
                                            
                                            <div id="myDivSearching2"></div>
                                            
                                        </div>
										
										
                                    </div>
                                </div>
                                
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status CA Untuk <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_jenisca' name='e_jenisca'>
                                            <?PHP
                                            if ($jenisca=="br") {
                                                echo "<option value='lk'>Biaya Luar Kota</option>";
                                                //echo "<option value='br' selected>Biaya Rutin</option>";
                                            }else{
                                                echo "<option value='lk' selected>Biaya Luar Kota</option>";
                                                //echo "<option value='br'>Biaya Rutin</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cbln01'>Periode </label>
                                    <div class='col-md-6'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_tgl' name='e_tgl' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tglberlku; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea class='form-control' id='e_ket' name='e_ket' rows='3' placeholder='Aktivitas'><?PHP echo $pketerangan; ?></textarea>
                                    </div><!--disabled='disabled'-->
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="button" class='btn btn-warning btn-xs'  name="btn_refresh" id="btn_refresh" onclick="CariAtasanKaryawan('2')" value="Refresh Atasan.."><!--refresh_atasan()-->
                                    </div>
                                </div>


                                <div id="div_atasan">
                                    
                                    <div id="div_disable_non" class='disabledDiv'>

                                        <div id="div_cabang">

                                            
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jabatan <span class='required'></span></label>
                                                <div class='col-xs-5'>
                                                    <select class='form-control input-sm' id='cb_idjabatan' name='cb_idjabatan'>
                                                        <option value='' selected>_blank</option>
                                                        <?PHP
                                                        $query = "select jabatanid, nama from hrd.jabatan ";
                                                        $query .= " order by jabatanid";
                                                        $tampil=mysqli_query($cnmy, $query);
                                                        while ($rt= mysqli_fetch_array($tampil)) {
                                                            $pjabid=$rt['jabatanid'];
                                                            $pnmjab=$rt['nama'];

                                                            if ($pjabid==$pidjabatan)
                                                                echo "<option value='$pjabid' selected>$pjabid - $pnmjab</option>";
                                                            else
                                                                echo "<option value='$pjabid'>$pjabid - $pnmjab</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            
                                            
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                                <div class='col-xs-5'>
                                                    <select class='form-control input-sm' id='cb_idcabang' name='cb_idcabang' onclick="CariDataArea()">
                                                        <option value='' selected>_blank</option>
                                                        <?PHP
                                                        if ($pidact=="editdata") {
                                                            $query = "select icabangid_o, nama from MKT.icabang_o where IFNULL(aktif,'')='Y' ";
                                                            $query .= " order by nama";
                                                            $tampil=mysqli_query($cnmy, $query);
                                                            while ($rt= mysqli_fetch_array($tampil)) {
                                                                $pcabid=$rt['icabangid_o'];
                                                                $pnmcab=$rt['nama'];

                                                                if ($pcabid==$pidcabang)
                                                                    echo "<option value='$pcabid' selected>$pnmcab</option>";
                                                                else
                                                                    echo "<option value='$pcabid'>$pnmcab</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                                <div class='col-xs-5'>
                                                    <select class='form-control input-sm' id='cb_idarea' name='cb_idarea'>
                                                        <option value='' selected>_blank</option>
                                                        <?PHP
                                                        if ($pidact=="editdata") {
                                                            $query = "select areaid_o, nama from MKT.iarea_o where icabangid_o='$pidcabang' AND IFNULL(aktif,'')='Y' ";
                                                            $query .= " order by nama";
                                                            $tampil=mysqli_query($cnmy, $query);
                                                            while ($rt= mysqli_fetch_array($tampil)) {
                                                                $pareaid=$rt['areaid_o'];
                                                                $pnmarea=$rt['nama'];

                                                                if ($pareaid==$pidarea)
                                                                    echo "<option value='$pareaid' selected>$pnmarea</option>";
                                                                else
                                                                    echo "<option value='$pareaid'>$pnmarea</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV <span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                <select class='form-control input-sm' id='cb_idspv' name='cb_idspv' onchange="ShowDataDMotc()">
                                                    <option value='' selected>_blank</option>
                                                    <?PHP
                                                    if ($pidact=="editdata") {
                                                        $query ="select karyawanid, nama from hrd.karyawan where 1=1 "
                                                                . " AND (aktif='Y' OR karyawanid='$patasan1' ) ";
                                                            $query .=" AND divisiid ='OTC' ";
                                                            $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                                        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                                                        $query .=" ORDER BY nama";
                                                        $sql=mysqli_query($cnmy, $query);
                                                        while ($Xt=mysqli_fetch_array($sql)){
                                                            $xid=$Xt['karyawanid'];
                                                            $xnama=$Xt['nama'];

                                                            if ($xid==$patasan1)
                                                                echo "<option value='$xid' selected>$xnama</option>";
                                                            else
                                                                echo "<option value='$xid'>$xnama</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>AM <span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                <select class='form-control input-sm' id='cb_idam' name='cb_idam' onchange="ShowDataGSMotc()">
                                                    <option value='' selected>_blank</option>
                                                    <?PHP
                                                    if ($pidact=="editdata") {
                                                        $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$patasan2') ";                                            
                                                            $query .=" AND divisiid ='OTC' ";
                                                            $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                                        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                                                        $query .=" ORDER BY nama";

                                                        $sql=mysqli_query($cnmy, $query);
                                                        while ($Xt=mysqli_fetch_array($sql)){
                                                            $xid=$Xt['karyawanid'];
                                                            $xnama=$Xt['nama'];

                                                            if ($xid==$patasan2)
                                                                echo "<option value='$xid' selected>$xnama</option>";
                                                            else
                                                                echo "<option value='$xid'>$xnama</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>HOS <span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                <select class='form-control input-sm' id='cb_idhos' name='cb_idhos'>
                                                    <option value='' selected>_blank</option>
                                                    <?PHP
                                                    if ($pidact=="editdata") {
                                                        $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$patasan4')";
                                                            $query .=" AND divisiid ='OTC' ";
                                                            $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                                            $query .=" And jabatanId in (select distinct jabatanId from hrd.jabatan WHERE rank='02')";
                                                        $query .=" ORDER BY nama";

                                                        $sql=mysqli_query($cnmy, $query);
                                                        while ($Xt=mysqli_fetch_array($sql)){
                                                            $xid=$Xt['karyawanid'];
                                                            $xnama=$Xt['nama'];

                                                            if ($xid==$patasan4)
                                                                echo "<option value='$xid' selected>$xnama</option>";
                                                            else
                                                                echo "<option value='$xid'>$xnama</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>



                                    </div>
                                    
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' readonly>
                                    </div>
                                </div>
                                                                
                                
                                
                                
                                
                            </div>
                            
                            
                            <?PHP if ($pstsmobile=="Y") { ?>
                                <br/>&nbsp;<div style="overflow-x:auto;">
                                    <?PHP
                                        include "module/mod_br_entrybrcashotcho/inputdetailmobileotcho.php";
                                    ?>
                                </div>
                            <?PHP }else{
                                include "module/mod_br_entrybrcashotcho/inputdetailotcho.php";
                            }
                            ?>
                            
                            
                        </div>
                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <?PHP
                        if ($psudahapv=="reject") {
                            echo "<h2>Data sudah direject</h2>";
                        }elseif ($psudahapv=="approve") {
                            echo "<h2>Data sudah diapprove</h2>";
                        }else{
                            
                            if ($pidact=="editdata") {
                                echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm_update('Simpan', '$pidact')\">Update</button>";
                            }else{
                                echo "<div class='col-sm-5'>";
                                    include "module/mod_br_entrybrcashotcho/ttd_ca.php";
                                echo "</div>";
                            }
                            
                        }
                        ?>
                        <div class='clearfix'></div>
                    </div>
                
                </form>
                
                
                
            </div>
        </div>
        
    </div>
    
</div>

<script>
    $(document).ready(function() {
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nmodule = urlku.searchParams.get("module");
        var nidmenu = urlku.searchParams.get("idmenu");
        var nact = urlku.searchParams.get("act");
        if (nact=="editdata") {    
            var icar = document.getElementById('e_idkaryawan').value;
            if (icar=="0000002200" || icar=="0000002083" || icar=="2200" || icar=="2083") {
                kry_none_.style.display = 'block';
            }
        }
        
        
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
    });
    
    
    function hit_total(pNilai_,pQty_,pTotal_) {
        
        nilai = document.getElementById(pNilai_).value;  
        qty = document.getElementById(pQty_).value;

        var newchar = '';
        var mynilai = nilai;  
        mynilai = mynilai.split(',').join(newchar);
        var myqty = qty;  
        myqty = myqty.split(',').join(newchar);
        
        total_ = mynilai * myqty;
        document.getElementById(pTotal_).value = total_;
        findTotal()
        
    }
    
    function findTotal(){
        var newchar = '';
        var a1 = document.getElementById('e_total1').value;
        var a2 = document.getElementById('e_total2').value;
        var a3 = document.getElementById('e_total3').value;
        var a4 = document.getElementById('e_total4').value;
        var a5 = document.getElementById('e_total5').value;
        var a6 = document.getElementById('e_total6').value;
        var a7 = document.getElementById('e_total7').value;
        var a8 = document.getElementById('e_total8').value;
        
        a1 = a1.split(',').join(newchar);
        a2 = a2.split(',').join(newchar);
        a3 = a3.split(',').join(newchar);
        a4 = a4.split(',').join(newchar);
        a5 = a5.split(',').join(newchar);
        a6 = a6.split(',').join(newchar);
        a7 = a7.split(',').join(newchar);
        a8 = a8.split(',').join(newchar);
        if (a1 === "") a1=0; if (a2 === "") a2=0; if (a3 === "") a3=0; if (a4 === "") a4=0;
        if (a5 === "") a5=0; if (a6 === "") a6=0; if (a7 === "") a7=0; if (a8 === "") a8=0;
        
        tot =parseInt(a1)+parseInt(a2)+parseInt(a3)+parseInt(a4)+parseInt(a5)+parseInt(a6)
            +parseInt(a7)+parseInt(a8);
        document.getElementById('e_totalsemua').value = tot;
    }
    
    
    function showDataUtkKaryawan() {
        showDataOther();
        KosongkanKaryawanNone();
        showDataAtasan();
        CariAtasanKaryawan('1');
    }
    
    function showDataOther() {
        var icar = document.getElementById('e_idkaryawan').value;
        if (icar=="0000002200" || icar=="0000002083" || icar=="2200" || icar=="2083") {
            kry_none_.style.display = 'block';
        }else{
            kry_none_.style.display = 'none';
        }
    }
    
    function showDataAtasan() {
        var istskry="inormal";
        var icar = document.getElementById('e_idkaryawan').value;
        if (icar=="0000002200" || icar=="0000002083" || icar=="2200" || icar=="2083") {
            istskry="inone";
            //div_cabang.style.display = 'block';
        }else{
            //div_cabang.style.display = 'none';
        }
        
        
    }
    
    function KosongkanKaryawanNone() {
        document.getElementById('e_nmkrynone2').value="";
        document.getElementById('e_kdkrynone').value="";
        document.getElementById('e_nmkrynone').value="";
    }
    
    
    function CariAtasanKaryawan(ipilih) {
        var istskry="inormal";
        var icar = document.getElementById('e_idkaryawan').value;
        if (icar=="0000002200" || icar=="0000002083" || icar=="2200" || icar=="2083") {
            istskry="inone";
            //div_cabang.style.display = 'block';
        }else{
            //div_cabang.style.display = 'none';
        }
        
        var sid = document.getElementById('e_kdkrynone').value;
        var snmlama = document.getElementById('e_nmkrynone2').value;
        var snmbaru = document.getElementById('e_nmkrynone').value;
        //alert(sid+", "+snmlama+", "+snmbaru); return false;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcashotcho/cariatasan.php?module=cariatasankaryawan",
            data:"uidkontrak="+sid+"&unmlama="+snmlama+"&unmbaru="+snmbaru+"&ucar="+icar+"&ustskry="+istskry+"&upilih="+ipilih,
            success:function(data){
                $("#div_atasan").html(data);
            }
        });
    }
    
    function CariDataArea() {
        var eidcab = document.getElementById('cb_idcabang').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcashotcho/viewdata.php?module=caridataarea",
            data:"uidcab="+eidcab,
            success:function(data){
                $("#cb_idarea").html(data);
            }
        });
    }
    
    
    function ShowDataDMotc() {
    
        var ispv = document.getElementById('cb_idspv').value;
        if (ispv=="") {
        }else{
            $.ajax({
                type:"post",
                url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatadmnyaotc",
                data:"uspv="+ispv,
                success:function(data){
                    $("#cb_idam").html(data);
                    ShowDataSMotc();
                }
            });
        }
    }
    
    function ShowDataSMotc() {
        var ispv = document.getElementById('cb_idspv').value;
        var idm = document.getElementById('cb_idam').value;
        if (idm=="" && ispv=="") {
        }else{
            $.ajax({
                type:"post",
                url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatasmnyaotc",
                data:"udm="+idm+"&uspv="+ispv,
                success:function(data){
                    $("#cb_idhos").html(data);
                    ShowDataGSMotc();
                }
            });
        }
    }
    
    function ShowDataGSMotc() {
        var ispv = document.getElementById('cb_idspv').value;
        var idm = document.getElementById('cb_idam').value;
        var ism = "";
        if (ism=="" && idm=="" && ispv=="") {
        }else{
            $.ajax({
                type:"post",
                url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatagsmnyaotc",
                data:"usm="+ism+"&udm="+idm+"&uspv="+ispv,
                success:function(data){
                    $("#cb_idhos").html(data);
                }
            });
        }
    }
    
    
    function disp_confirm_update(pText_, ket) {
        var icar=document.getElementById('e_idkaryawan').value;

        if (icar=="") {
            alert("Yang membuat masih kosong...");
            return false;
        }
        
        
        if (icar=="0000002200" || icar=="0000002083" || icar=="2200" || icar=="2083") {
            var sid = document.getElementById('e_kdkrynone').value;
            var snmlama = document.getElementById('e_nmkrynone2').value;
            var snmbaru = document.getElementById('e_nmkrynone').value;

            if (snmbaru=="") {
                alert("nama masih kosong...");
                return false;
            }

            var icnmlama=snmlama.toUpperCase();
            var icnmbaru=snmbaru.toUpperCase();

            if (icnmlama != icnmbaru) {
                document.getElementById('e_kdkrynone').value="";
                sid="";
            }

            if (sid=="") {
                var cidcab = document.getElementById('cb_idcabang').value;
                var cidarea = document.getElementById('cb_idarea').value;
                var cidatasan1 = document.getElementById('cb_idspv').value;
                var cidatasan2 = document.getElementById('cb_idam').value;
                var cidatasan4 = document.getElementById('cb_idhos').value;

                if (cidcab=="") {
                    alert("cabang masih kosong...");
                    return false;
                }

                if (cidarea=="") {
                    alert("area masih kosong...");
                    return false;
                }

                if (cidatasan1=="" && cidatasan2=="" && cidatasan4=="") {
                    alert("atasan masih kosong...");
                    return false;
                }


            }
        }else{

        }
         
         
        var etotsem =document.getElementById('e_totalsemua').value;
        if (etotsem === "") etotsem=0;
        if (parseInt(etotsem)==0) {
            alert("Total Rupiah Masih Kosong....");
            return 0;
        }
         
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");

        //simpan data ke DB
        var cmt = confirm('Apakah akan melakukan edit data...???');
        if (cmt == false) {
            return false;
        }else{
            
            document.getElementById("demo-form2").action = "module/mod_br_entrybrcashotcho/aksi_entrybrcashotcho.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            
        }
            
            
            
    }
</script>


<script>
    function cariFormData(str, idnya, myDivForm, cModule){
        $("#"+str).keyup(function(){
            $.ajax({
            type: "POST",
            url: "js/formpencarian/formsearch_eth.php?module="+cModule+"&myidform="+str+"&idnya="+idnya+"&myDivForm="+myDivForm,
            data:'keyword='+$(this).val(),
            beforeSend: function(){
                    $("#"+str).css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data){
                    $("#"+myDivForm).show();
                    $("#"+myDivForm).html(data);
                    $("#"+str).css("background","#FFF");
            }
            });
        });
    }
    function selectDataFormSearch(val) {
        var nmid = val.split("|");
        $("#"+nmid[2]).hide();
        $("#e_kdkrynone").val(nmid[3]);
        $("#e_nmkrynone").val(nmid[4]);
        $("#e_nmkrynone2").val(nmid[4]);
        //alert(nmid[3]);
        CariAtasanKaryawan('1');
    }

    function HideDataFormSearch(val) {
        var nmid = val.split("|");
        $("#"+nmid[1]).val(nmid[4]);
        $("#"+nmid[2]).hide();
        CariAtasanKaryawan('1');
    }

    function checkkey(){
        if(event.keyCode==27){
            //put what you want here...
            $("#myDivSearching2").hide();
            //window.alert("Escape key pressed!");
        }
    }
    
</script>


<style>
    .infoCari{padding:5px;margin-bottom: 5px; cursor: pointer;}
    .infoCari b{color:#555555;}

    #myDivSearching, #myDivSearching1, #myDivSearching2, #myDivSearching3, #myDivSearching4, #myDivSearching5, #myDivSearching6, #myDivSearching7, #myDivSearching8, #myDivSearching9, #myDivSearching10,
    #myDivSearching11, #myDivSearching12, #myDivSearching13, #myDivSearching14, #myDivSearching15,
        #myDivSearchingObt1, #myDivSearchingObt2, #myDivSearchingObt3, #myDivSearchingObt4, #myDivSearchingObt5,
        #myDivSearchingObt6, #myDivSearchingObt7, #myDivSearchingObt8, #myDivSearchingObt9, #myDivSearchingObt10 {
        position: absolute;background: #fff;box-shadow: 0px 3px 5px #555555; z-index:100; color:#000;
        width: 350px; padding-left: 0px;
    }

    #search-form{list-style:none;margin-left:-30px;}
    #search-form li{padding: 5px 10px 5px 0px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid; padding-left: 5px;}

    #search-form li:hover{background:#ece3d2;cursor: pointer;}
</style>


<style>
    .form-group, .input-group, .control-label {
        margin-bottom:2px;
    }
    .control-label {
        font-size:11px;
    }
    #datatable input[type=text], #tabelnobr input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:11px;
        height: 25px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }

    table.datatable, table.tabelnobr {
        color: #000;
        font-family: Helvetica, Arial, sans-serif;
        width: 100%;
        border-collapse:
        collapse; border-spacing: 0;
        font-size: 11px;
        border: 0px solid #000;
    }

    table.datatable td, table.tabelnobr td {
        border: 1px solid #000; /* No more visible border */
        height: 10px;
        transition: all 0.1s;  /* Simple transition for hover effect */
    }

    table.datatable th, table.tabelnobr th {
        background: #DFDFDF;  /* Darken header a bit */
        font-weight: bold;
    }

    table.datatable td, table.tabelnobr td {
        background: #FAFAFA;
    }

    /* Cells in even rows (2,4,6...) are one color */
    tr:nth-child(even) td { background: #F1F1F1; }

    /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
    tr:nth-child(odd) td { background: #FEFEFE; }

    tr td:hover.biasa { background: #666; color: #FFF; }
    tr td:hover.left { background: #ccccff; color: #000; }

    tr td.center1, td.center2 { text-align: center; }

    tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
    tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
    /* Hover cell effect! */
    tr td {
        padding: -10px;
    }

</style>


<style>
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>


<style>
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>