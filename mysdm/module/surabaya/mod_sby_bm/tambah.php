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
</style>

<script>    

function ShowCOA(udiv, ucoa, ucoa2) {
    ShowCOA1(udiv, ucoa);
    ShowCOA2(udiv, ucoa2);
    ShowBiayaUntuk();
    ShowDataCabang();
}

function ShowCOA1(udiv, ucoa) {
    var icar = "";
    var idiv = document.getElementById(udiv).value;
    $.ajax({
        type:"post",
        url:"module/surabaya/mod_sby_bm/viewdata.php?module=viewcoadivisi",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
}

function ShowCOA2(udiv, ucoa) {
    var icar = "";
    var idiv = document.getElementById(udiv).value;
    $.ajax({
        type:"post",
        url:"module/surabaya/mod_sby_bm/viewdata.php?module=viewcoadivisi2",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
}

function ShowBiayaUntuk() {
    var icar = "";
    var idiv = document.getElementById('cb_divisi').value;
    $.ajax({
        type:"post",
        url:"module/surabaya/mod_sby_bm/viewdata.php?module=viwewbiayauntuk",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#cb_biayauntuk").html(data);
            ShowBiayaUntukKredit();
        }
    });
}

function ShowBiayaUntukKredit() {
    var icar = "";
    var idiv = document.getElementById('cb_divisi').value;
    $.ajax({
        type:"post",
        url:"module/surabaya/mod_sby_bm/viewdata.php?module=viwewbiayauntuk",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#cb_biayauntukk").html(data);
        }
    });
}

function ShowDataCabang() {
    var icar = "";
    var idiv = document.getElementById('cb_divisi').value;
    $.ajax({
        type:"post",
        url:"module/surabaya/mod_sby_bm/viewdata.php?module=viwewdatacabang",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#cb_cabang").html(data);
        }
    });
}

function disp_confirm(pText_,ket)  {
    
    var ecoa =document.getElementById('cb_coa').value;
    
    if (ecoa==""){
        alert("coa masih kosong....");
        return 0;
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
            document.getElementById("demo-form2").action = "module/surabaya/mod_sby_bm/aksi_bm.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}


</script>

<?PHP
$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));


                
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$keterangan="";
$divisi="";
$jumlahd="";
$jumlahk="";
$coa="";
$coa2="";
$nobbm="";
$nobbk="";
$pbiayauntuk="";
$pbiayauntuk_k="";
    
    $thurufbln=CariBulanHuruf(date('m', strtotime($hari_ini)));
    $tglnomor = $thurufbln."/".date('Y', strtotime($hari_ini));
    $pthnini = date('Y', strtotime($hari_ini));
    $tnobbm="1501";
    $tnobbk="1501";
    $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(NOBBM, '/', 1)),'BBM','')) as nobbm, LTRIM(REPLACE(MAX(SUBSTRING_INDEX(NOBBK, '/', 1)),'BBK','')) as nobbk FROM dbmaster.t_bm_sby 
        WHERE YEAR(TANGGAL)='$pthnini'";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        if (!empty($sh['nobbm'])) { $tnobbm=(INT)$sh['nobbm']; }
        if (!empty($sh['nobbk'])) { $tnobbk=(INT)$sh['nobbk']; }
    }
    
    $nobbm = "BBM".$tnobbm."/".$tglnomor;
    $nobbk = "BBK".$tnobbk."/".$tglnomor;
    
$picabangid="";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_bm_sby WHERE ID='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['ID'];
    $tglberlku = date('d/m/Y', strtotime($r['TANGGAL']));
    $tgl1 = date('d/m/Y', strtotime($r['TANGGAL']));
    $idajukan=$r['KARYAWANID']; 
    $keterangan=$r['KETERANGAN'];
    $jumlahd=$r['DEBIT'];
    $jumlahk=$r['KREDIT'];
    $divisi=$r['DIVISI'];
    $coa=$r['COA4'];
    $coa2=$r['COA4_K'];
    $nobbm=$r['NOBBM'];
    $nobbk=$r['NOBBK'];
    $pbiayauntuk=$r['BIAYA_UNTUK'];
    $pbiayauntuk_k=$r['BIAYA_UNTUK_K'];
    $picabangid=$r['ICABANGID'];
    
}
    
?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowCOA('cb_divisi', 'cb_coa', 'cb_coa2');">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER') ";
                                            if ($_SESSION['DIVISI']=="OTC") {
                                                $query .=" AND DivProdId = 'OTC' ";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                if ($z['DivProdId']==$divisi)
                                                    echo "<option value='$z[DivProdId]' selected>$z[DivProdId]</option>";
                                                else
                                                    echo "<option value='$z[DivProdId]'>$z[DivProdId]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select karyawanId as karyawanid, nama as nama_karyawan From hrd.karyawan WHERE 1=1 ";
                                            $query .= " AND ( ";
                                                $query .= " ( ";
                                                    $query .= " (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                            . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                            . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                            . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                            . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                    $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                    $query .= " AND karyawanid NOT IN ('0000002200', '0000002083') ";
                                                    //$query .= " AND divisiId NOT IN ('OTC', 'CHC') ";
                                                $query .= " ) ";
                                            $query .= " OR karyawanId='$idajukan' ) ";
                                            $query .= " ORDER BY nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pkaryid=$z['karyawanid'];
                                                $pkarynm=$z['nama_karyawan'];
                                                $pkryid=(INT)$pkaryid;
                                                
                                                if ($pkaryid==$idajukan)
                                                    echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                else
                                                    echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="">
                                            <?PHP
                                            if ($divisi=="OTC") {
                                                $query = "SELECT * FROM (select icabangid_o as icabangid, nama as nama_cabang FROM MKT.icabang_o WHERE aktif<>'N' ";
                                                $query .= "UNION ";
                                                $query .= "select cabangid_ho as icabangid, nama as nama_cabang FROM dbmaster.cabang_otc WHERE aktif<>'N') as tcab ";
                                                $query .= " ORDER BY nama_cabang";
                                                
                                                echo "<option value='' selected>-- All CHC --</option>";
                                            }else{
                                                $query = "select iCabangId as icabangid, nama as nama_cabang FROM MKT.icabang WHERE aktif<>'N' ";
                                                $query .= " AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -') ";
                                                $query .= " ORDER BY nama";
                                                
                                                echo "<option value='' selected>-- All Ethical --</option>";
                                            }
                                            
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $nnicabid=$z['icabangid'];
                                                $nnicabnm=$z['nama_cabang'];
                                                
                                                if ($nnicabid==$picabangid)
                                                    echo "<option value='$nnicabid' selected>$nnicabnm - $nnicabid</option>";
                                                else
                                                    echo "<option value='$nnicabid'>$nnicabnm - $nnicabid</option>";
                                            }
                                            
                                            if ($divisi=="OTC") {
                                            }else{
                                                $query = "select iCabangId as icabangid, nama as nama_cabang FROM MKT.icabang WHERE aktif<>'Y' ";
                                                $query .= " AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -') ";
                                                $query .= " ORDER BY nama";
                                                
                                                echo "<option value='' >-- Non Aktif --</option>";
                                                
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $nnicabid=$z['icabangid'];
                                                    $nnicabnm=$z['nama_cabang'];

                                                    if ($nnicabid==$picabangid)
                                                        echo "<option value='$nnicabid' selected>$nnicabnm - $nnicabid</option>";
                                                    else
                                                        echo "<option value='$nnicabid'>$nnicabnm - $nnicabid</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. BBM <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' id='e_nobbm' name='e_nobbm' class='form-control col-md-7 col-xs-12' placeholder="BBM1500/<?PHP echo $tglnomor; ?>" value='<?PHP echo $nobbm; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. BBK <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' id='e_nobbk' name='e_nobbk' class='form-control col-md-7 col-xs-12' placeholder="BBK1500/<?PHP echo $tglnomor; ?>" value='<?PHP echo $nobbk; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Biaya Untuk (Debit) <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_biayauntuk' name='cb_biayauntuk' onchange="">
                                            <option value='' selected>-- All --</option>
                                            <?PHP
                                            if ($divisi=="OTC") {
                                                $query = "select gkode, nama_group FROM hrd.brkd_otc_group 
                                                    WHERE 1=1 ";
                                                $query .= " ORDER BY gkode";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['gkode']==$pbiayauntuk)
                                                        echo "<option value='$z[gkode]' selected>$z[gkode] - $z[nama_group]</option>";
                                                    else
                                                        echo "<option value='$z[gkode]'>$z[gkode] - $z[nama_group]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>COA / Posting Debit <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_coa' name='cb_coa' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $fil = " AND (c.DIVISI2 = '$divisi' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', ''))";
                                                    if (empty($divisi)) $fil = " AND RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '')";
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
                                                        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
                                                        WHERE 1=1 $fil ";
                                                    $query .= " ORDER BY a.COA4";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        if ($z['COA4']==$coa)
                                                            echo "<option value='$z[COA4]' selected>$z[COA4] - $z[NAMA4]</option>";
                                                        else
                                                            echo "<option value='$z[COA4]'>$z[COA4] - $z[NAMA4]</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Debit
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmldebit' name='e_jmldebit' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlahd; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Biaya Untuk (Kredit) <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_biayauntukk' name='cb_biayauntukk' onchange="">
                                            <option value='' selected>-- All --</option>
                                            <?PHP
                                            if ($divisi=="OTC") {
                                                $query = "select gkode, nama_group FROM hrd.brkd_otc_group 
                                                    WHERE 1=1 ";
                                                $query .= " ORDER BY gkode";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['gkode']==$pbiayauntuk_k)
                                                        echo "<option value='$z[gkode]' selected>$z[gkode] - $z[nama_group]</option>";
                                                    else
                                                        echo "<option value='$z[gkode]'>$z[gkode] - $z[nama_group]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>COA / Posting Kredit <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_coa2' name='cb_coa2' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $fil2 = " AND (c.DIVISI2 = '$divisi' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', ''))";
                                                    if (empty($divisi)) $fil2 = " AND RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '')";
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
                                                        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
                                                        WHERE 1=1 $fil2 ";
                                                    $query .= " ORDER BY a.COA4";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        if ($z['COA4']==$coa2)
                                                            echo "<option value='$z[COA4]' selected>$z[COA4] - $z[NAMA4]</option>";
                                                        else
                                                            echo "<option value='$z[COA4]'>$z[COA4] - $z[NAMA4]</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Kredit
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlkredit' name='e_jmlkredit' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlahk; ?>'>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $keterangan; ?>'>
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
                    

                </div>
            </div>
            

        </form>
        
    </div>
    <!--end row-->
</div>


    <script type="text/javascript">
        $(function() {
            $('#tglfrom').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var min = $(this).datepicker('getDate');
                    $('#tglto').datepicker('option', 'minDate', min || '0');
                    datepicked();
                } 
            });
            $('#tglto').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                minDate: '0',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var max = $(this).datepicker('getDate');
                    $('#tglfrom').datepicker('option', 'maxDate', max || '+2Y');
                    datepicked();
                } 
            });
        });
        var datepicked = function() {
            var from = $('#from');
            var to = $('#to');
            var nights = $('#nights');
            var fromDate = from.datepicker('getDate')
            var toDate = to.datepicker('getDate')
            if (toDate && fromDate) {
                var difference = 0;
                var oneDay = 1000 * 60 * 60 * 24;
                var difference = Math.ceil((toDate.getTime() - fromDate.getTime()) / oneDay);
                nights.val(difference);
            }
        }
    </script>
    