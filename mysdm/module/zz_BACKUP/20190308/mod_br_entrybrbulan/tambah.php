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
    function showCabangMR(ucar, ecabang) {
        var icar = document.getElementById(ucar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrbulan/viewdata.php?module=viewdatacabangkaryawan",
            data:"umr="+icar,
            success:function(data){
                $("#"+ecabang).html(data);
                ShowDivisi(ucar, 'cb_divisi');
                ShowCOA(ucar, 'cb_divisi', 'cb_coa');
            }
        });
    }

function ShowDivisi(ucar, udiv) {
    var icar = document.getElementById(ucar).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrybrbulan/viewdata.php?module=viewdivisimr",
        data:"umr="+icar,
        success:function(data){
            $("#"+udiv).html(data);
        }
    });
}

function ShowCOA(ucar, udiv, ucoa) {
    var icar = document.getElementById(ucar).value;
    var idiv = document.getElementById(udiv).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrybrbulan/viewdata.php?module=viewcoadivisi",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
            ShowCabangDivisi();
        }
    });
}
function ShowCabangDivisi() {
    var idiv = document.getElementById('cb_divisi').value;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrybrbulan/viewdata.php?module=viewareadivisi",
        data:"udivi="+idiv,
        success:function(data){
            $("#e_idcabang").html(data);
        }
    });
}
function disp_confirm(pText_,ket)  {
    
    var ecab =document.getElementById('e_idcabang').value;
    var ecoa =document.getElementById('cb_coa').value;
    /*
    var ekar =document.getElementById('e_idkaryawan').value;
    if (ekar==""){
        alert("yang membuat masih kosong....");
        return 0;
    }
    */
    if (ecab==""){
        alert("cabang masih kosong....");
        return 0;
    }
    
    if (ecoa==""){
        alert("COA / Posting masih kosong....");
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
            document.getElementById("demo-form2").action = "module/mod_br_entrybrbulan/aksi_entrybrbulan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
include "config/koneksimysqli_it.php";
$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$tgl1 = date('01/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$idcabang="";
$nmcabang="";
$keterangan="";
$divisi="";
$jumlah="";
$coa="";
$ca="";
$tahap="2";
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_t_br_bulan WHERE idbr='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idbr'];
    $tglberlku = date('m/Y', strtotime($r['periode']));
    $idajukan=$r['karyawanid']; 
    $nmajukan=$r['nama']; 
    $idcabang=$r['icabangid']; 
    $nmcabang=$r['nama_cabang'];
    $keterangan=$r['keterangan'];
    $jumlah=$r['jumlah'];
    $coa=$r['COA4'];
    $divisi=$r['divisi'];
    
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

                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="showCabangMR('e_idkaryawan', 'e_idcabang')">
                                            <?PHP
                                            //comboKaryawanAll("", "pilihan", $idajukan, $_SESSION['STSADMIN'], $_SESSION['LVLPOSISI'], $_SESSION['DIVISI']);
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowCOA('e_idkaryawan', 'cb_divisi', 'cb_coa');">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            if ($_SESSION['ADMINKHUSUS']=="Y") {
                                                //if (!empty($_SESSION['KHUSUSSEL'])) $query .=" AND DivProdId in $_SESSION[KHUSUSSEL]";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnit, $query);
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcabang'>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='e_idcabang' name='e_idcabang'>
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                if ($divisi=="OTC") {
                                                    $query = "select icabangid_o iCabangId, nama from dbmaster.v_icabang_o where "
                                                            . " (aktif='Y' and "
                                                            . " icabangid_o not in ('JKT_MT', 'JKT_RETAIL', 'MD', 'PM_ACNEMED', 'PM_CARMED', 'PM_LANORE', 'PM_MELANOX', 'PM_PARASOL') )"
                                                            . " or icabangid_o='$kodeid' order by nama"; 
                                                }else{
                                                    $query = "select iCabangId, nama from dbmaster.icabang where aktif='Y' or iCabangId='$kodeid' order by nama"; 
                                                }
                                                $result = mysqli_query($cnit, $query); 
                                                $record = mysqli_num_rows($result);
                                                for ($i=0;$i < $record;$i++) {
                                                    $row = mysqli_fetch_array($result); 
                                                    $kodeid  = $row['iCabangId'];
                                                    $nama = $row['nama'];
                                                    if ($idcabang==$kodeid)
                                                        echo "<option value=\"$kodeid\" selected>$nama</option>";
                                                    else
                                                        echo "<option value=\"$kodeid\">$nama</option>";
                                                }
                                              ?>
                                          </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>COA / Posting <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_coa' name='cb_coa' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                if ($_GET['act']=="editdata"){
                                                    $fil = "AND DIVISI = '$divisi'";
                                                    if (empty($divisi)) $fil="";
                                                    $filcoa ="";
                                                    if ($_SESSION['GROUP']<>"26" AND $_SESSION['GROUP'] <> "23") {
                                                        //if ($_SESSION['ADMINKHUSUS']=="Y") $filcoa =" and COA4 in (select distinct COA4 from dbmaster.coa_wewenang where karyawanId='$_SESSION[IDCARD]')";
                                                    }
                                                    $query = "select COA4, NAMA4 from dbmaster.v_coa_all WHERE ( ifnull(kodeid,'') = '' AND "
                                                            . "COA4 not in (select distinct COA4 from dbmaster.posting_coa))"
                                                            . " $fil $filcoa";
                                                    
                                                    $query = "select COA4, NAMA4 from dbmaster.v_coa_all WHERE COA4 in (select distinct ifnull(COA4,'') from dbmaster.posting_coa_rutin) $fil";
                                                    
                                                    $tampil = mysqli_query($cnit, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        if ($z['COA4']==$coa)
                                                            echo "<option value='$z[COA4]' selected>$z[NAMA4]</option>";
                                                        else
                                                            echo "<option value='$z[COA4]'>$z[NAMA4]</option>";
                                                    }
                                                }
                                              ?>
                                          </select>
                                    </div>
                                </div>


                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Periode <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            <div class='input-group date' id='mytgl01'>
                                                <input type='text' id='mytgl01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl1; ?>' data-inputmask="'mask': '99/99/9999'">
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <div class='input-group date' id='mytgl02'>
                                                <input type='text' id='mytgl02' name='e_periode02' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl2; ?>' data-inputmask="'mask': '99/99/9999'">
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='thnbln01'>Periode </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='thnbln01'>
                                            <input type="text" class="form-control" id='thnbln1' name='e_tglberlaku' autocomplete='off' required='required' placeholder='MM/yyyy' data-inputmask="'mask': '99/9999'" value='<?PHP echo $tglberlku; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>'>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Keterangan'><?PHP echo $keterangan; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                            <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>-->
                                        </div>
                                    </div>
                                </div>
                        
                            </div>
                            
                            
                      
                            
                        </div>
                    </div>
                    

                </div>
            </div>

        </form>
        
        
    <?PHP if ($_GET['act']=="tambahbaru") { ?>    
    
        
        <style>
            .divnone {
                display: none;
            }
            #datatable th {
                font-size: 12px;
            }
            #datatable td { 
                font-size: 11px;
            }
        </style>

        <script>
            $(document).ready(function() {
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "ordering": false,
                    "columnDefs": [
                        { "visible": false },
                        { className: "text-right", "targets": [6] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7] }//nowrap

                    ],
                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false
                } );
            } );
            
            function ProsesData(ket, noid){
                
                ok_ = 1;
                if (ok_) {
                    var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                    if (r==true) {
                        
                        var txt;
                        if (ket=="reject" || ket=="hapus" || ket=="pending") {
                            var textket = prompt("Masukan alasan "+ket+" : ", "");
                            if (textket == null || textket == "") {
                                txt = textket;
                            } else {
                                txt = textket;
                            }
                        }
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        //document.write("You pressed OK!")
                        document.getElementById("demo-form2").action = "module/mod_br_entrybrbulan/aksi_entrybrbulan.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+txt+"&ket="+ket+"&id="+noid;
                        document.getElementById("demo-form2").submit();
                        return 1;
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }
                
                

            }
        </script>
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_content'>
                <div class='x_panel'>
                    <b>Data yang terakhir diinput (max 5 data)</b>
                    <table id='datatable' class='table table-striped nowrap table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th width='80px'>Aksi</th>
                                <th width='40px'>No ID</th>
                                <th width='80px'>Cabang</th>
                                <th width='50px'>Divisi</th>
                                <th width='80px'>COA</th>
                                <th width='40px'>Periode</th>
                                <th width='50px'>Jumlah</th>
                                <th width='50px'>Keterangan</th>
                            </tr>
                        </thead>
                        <body>
                            <?PHP
                            $no=1;
                            include "config/koneksimysqli_it.php";
                            $sql = "SELECT idbr, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(periode,'%M %Y') as periode, "
                                    . "divisi, karyawanid, nama, icabangid, nama_cabang, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan, "
                                    . " COA4, NAMA4 ";
                            $sql.=" FROM dbmaster.v_t_br_bulan ";
                            $sql.=" WHERE stsnonaktif <> 'Y' ";
                            /*
                            if ($_SESSION['ADMINKHUSUS']=="Y") {
                                if (!empty($_SESSION['KHUSUSSEL'])) $sql .=" AND divisi in $_SESSION[KHUSUSSEL]";
                                $sql .=" and (COA4 in (select distinct COA4 from dbmaster.coa_wewenang where karyawanId='$_SESSION[IDCARD]')"
                                        . " OR userid='$_SESSION[IDCARD]') ";
                            }else{
                                $sql.=" AND userid='$_SESSION[IDCARD]' ";
                            }
                             * 
                             */
                            $sql.=" AND userid='$_SESSION[IDCARD]' ";
                            $sql.=" order by idbr desc limit 5 ";
                            
                            $tampil=mysqli_query($cnit, $sql);
                            while ($xc=  mysqli_fetch_array($tampil)) {
                                $faksi = ""
                                        . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$xc[idbr]'>Edit</a> "
                                        . "<button class='btn btn-danger btn-xs'"
                                        . "onClick=\"ProsesData('hapus', '$xc[idbr]')\">Hapus</button>
                                            
                                ";
                                $fnoid = $xc['idbr'];
                                $nmcabang = $xc["nama_cabang"];
                                $divisi = $xc["divisi"];
                                $nmcoa = $xc["NAMA4"]." (".$xc["COA4"].")";
                                $periode = $xc["periode"];
                                $jumlah = $xc["jumlah"];
                                $ket = $xc["keterangan"];
                                
                                echo "<tr>";
                                echo "<td>$faksi</td>";
                                echo "<td>$fnoid</td>";
                                echo "<td>$nmcabang</td>";
                                echo "<td>$divisi</td>";
                                echo "<td>$nmcoa</td>";
                                echo "<td>$periode</td>";
                                echo "<td>$jumlah</td>";
                                echo "<td>$ket</td>";
                                echo "</tr>";
                                $no++;
                            }
                            ?>
                        </body>
                    </table>

                </div>
            </div>
        </div>
        
    <?PHP } ?>
        
    </div>
    <!--end row-->
</div>

<script>
    $(document).ready(function() {
        //showCabangMR('e_idkaryawan', 'e_idcabang');
        //ShowDivisi('e_idkaryawan', 'cb_divisi');
        <?PHP if ($_GET['act']=="tambahbaru"){ ?>
        ShowCOA('e_idkaryawan', 'cb_divisi', 'cb_coa');
        <?PHP } ?>
    });
</script>

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