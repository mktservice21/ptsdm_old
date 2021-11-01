<?php
    include "config/cek_akses_modul.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    
?>
<?php
switch($pact){
    default:
?>
<?php
    
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    $puserid=$_SESSION['IDCARD'];
    $fpengajuan="";
    $fblnpilih="";
    $ptotalsemua="";
    
    $aksi="eksekusi3.php";
    
    $ptahunpilih="2022";
    $bidcabang="";
    
    $ptxturl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    
    $pkaryawaid = "";
    $icabangid = "";
    
    $ppilihdivi="";
    $pseldivpili1="selected";
    $pseldivpili2="";
    $pseldivpili3="";
    
    $pdepartemen="";
    
    $pketdivipl="ETHICAL";
    if ($fdivisi=="OTC") {
        $pketdivipl="CHC";
        $ppilihdivi=$fdivisi;
    }
    
    if (!empty($_SESSION['BGTUPDTHN'])) $ptahunpilih=$_SESSION['BGTUPDTHN'];
    if (!empty($_SESSION['BGTUPDDVL'])) $ppilihdivi=$_SESSION['BGTUPDDVL'];
    
    if ($ppilihdivi=="OTC" OR $ppilihdivi=="OT" OR $ppilihdivi=="CHC") {
        $pseldivpili1="";
        $pseldivpili2="selected";
        $pseldivpili3="";
    }elseif ($ppilihdivi=="HO") {
        $pseldivpili1="";
        $pseldivpili2="";
        $pseldivpili3="selected";
    }else{
        $pseldivpili1="selected";
        $pseldivpili2="";
        $pseldivpili3="";
    }
    
    
    
    if (!empty($_SESSION['BGTUPDDPT'])) $pdepartemen=$_SESSION['BGTUPDDPT'];
    if (!empty($_SESSION['BGTUPDKRY'])) $pkaryawaid=$_SESSION['BGTUPDKRY'];
    if (!empty($_SESSION['BGTUPDCAB'])) $icabangid=$_SESSION['BGTUPDCAB'];
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Upload Data Budget";
                if ($pact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h3>
            
        </div>
        
    </div>
    <div class="clearfix"></div>
    
    <div class="row">

        
                <script>
                    
                </script>
                
                
                        
<div class="">
    
    <!--row-->
    <div class="row">
    
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left' enctype='multipart/form-data'>
            <input type="hidden" class="form-control" id='e_txturl' name='e_txturl' autocomplete="off" value='<?PHP echo $ptxturl; ?>' readonly>
            <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $puserid; ?>' Readonly>
            

            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_divpilih' id='cb_divpilih' onchange="PilihDataCabang()">
                                            <?php
                                            if ($fdivisi=="OTC") {
                                                echo "<option value='OTC' selected>CHC</option>";
                                            }else{
                                                echo "<option value='ETH' $pseldivpili1>ETHICAL</option>";
                                                echo "<option value='OTC' $pseldivpili2>CHC</option>";
                                                echo "<option value='HO' $pseldivpili3>HO</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tahun <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' class='form-control' id='e_tahun' name='e_tahun' value='<?PHP echo $ptahunpilih; ?>' Readonly>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_karyawan' id='cb_karyawan' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            
                                            $query = "select karyawanId, nama, divisiId as divisi, jabatanId as jabatanid From hrd.karyawan
                                                WHERE 1=1 ";
                                            $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                            $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P', 'LOGIN') "
                                                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                            $query .= " AND nama NOT IN ('ACCOUNTING')";
                                            $query .= " AND jabatanid NOT IN ('15', '10', '18', '35', '40', '41', '14', '16', '17', '19', '23', '13', '38')";
                                            $query .= " AND jabatanid NOT IN ('12', '37', '28')";
                                            $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                            $query .= " AND karyawanid NOT IN ('0000000962', '0000001675')";
                                            if ($fdivisi=="OTC") {
                                                $query .= " AND divisiId IN ('OTC', 'CHC')";
                                            }
                                            $query .= " ORDER BY nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pkaryid=$z['karyawanId'];
                                                $pkarynm=$z['nama'];
                                                $pdivisi=$z['divisi'];
                                                $pjabatanid=$z['jabatanid'];
                                                $pkryid=(INT)$pkaryid;
                                                
                                                $pnmdivisi=$pdivisi;
                                                if ($pdivisi=="OTC") $pnmdivisi="CHC";
                                                elseif ($pdivisi=="CAN") $pnmdivisi="CANARY";
                                                elseif ($pdivisi=="PEACO") $pnmdivisi="PEACOCK";
                                                elseif ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
                                                
                                                if ($pjabatanid=="08" OR $pjabatanid=="20" OR $pjabatanid=="05") $pnmdivisi="SALES";
                                                elseif ($pjabatanid=="06") $pnmdivisi="PM";
                                                
                                                if ($pkaryid==$pkaryawaid)
                                                    echo "<option value='$pkaryid' selected>$pkarynm ($pkryid) - $pnmdivisi</option>";
                                                else
                                                    echo "<option value='$pkaryid'>$pkarynm ($pkryid) - $pnmdivisi</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Departemen <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_dept' id='cb_dept' onchange="">
                                            <?php
                                            echo "<option value=''>--Pilih--</option>";
                                            
                                            $query_dep = "select iddep, nama_dep, aktif, igroup, nama_group from dbmaster.t_department WHERE 1=1 ";
                                            $query_dep .=" AND IFNULL(aktif,'')<>'N'";
                                            $query_dep .=" ORDER BY IFNULL(nama_group,''), nama_dep";
                                            
                                            if (!empty($query_dep)) {
                                                $tampil = mysqli_query($cnmy, $query_dep);
                                                $ketemu= mysqli_num_rows($tampil);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pdepid=$z['iddep'];
                                                    $pdepnm=$z['nama_dep'];
                                                    $pdepgrpid=$z['igroup'];
                                                    $pdepgrpnm=$z['nama_group'];
                                                    
                                                    if (!empty($pdepgrpnm) AND $pdepgrpid=="1") {
                                                        $pdepnm .=" - (".$pdepgrpnm.")";
                                                    }
                                                    
                                                    if ($pdepid==$pdepartemen)
                                                        echo "<option value='$pdepid' selected>$pdepnm</option>";
                                                    else
                                                        echo "<option value='$pdepid'>$pdepnm</option>";
                                                    
                                                }
                                            }else{
                                                echo "<option value='' selected>-- Pilih --</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_cabang' id='cb_cabang' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($ppilihdivi=="OTC" OR $ppilihdivi=="OT" OR $ppilihdivi=="CHC") {
                                                $query = "select icabangid_o as icabangid, nama as nama From dbmaster.v_icabang_o 
                                                    WHERE IFNULL(aktif,'')<>'N' ";
                                                $query .= " ORDER BY nama";
                                            }else{
                                                $query = "select icabangid as icabangid, nama as nama From MKT.icabang
                                                    WHERE 1=1 ";
                                                $query .= " AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -')";
                                                $query .= " ORDER BY nama";
                                            }
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pcabangid=$z['icabangid'];
                                                $pcabnm=$z['nama'];
                                                $pcabid=(INT)$pcabangid;
                                                if ($pcabangid==$icabangid)
                                                    echo "<option value='$pcabangid' selected>$pcabnm ($pcabid) $pketdivipl</option>";
                                                else
                                                    echo "<option value='$pcabangid'>$pcabnm ($pcabid) $pketdivipl</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <?php
                                        //echo "<label><input type='checkbox' class='js-switch' id='chk_ttdfoto' name='chk_ttdfoto' value='byttd' onclick=\"ShowFromChkTtdFoto()\" checked> <span id='lbl_ttdfoto'>Tanda Tangan</span></label>";
                                        echo "<input type='radio' class='' name='opt_tipe' id='opt_tipepl' value='byupload' checked  onclick=\"ShowFromUploadInput()\" /> Upload Excel";
                                        echo "&nbsp; &nbsp; ";
                                        echo "<input type='radio' class='' name='opt_tipe' id='opt_tipepl' value='byinput'  onclick=\"ShowFromUploadInput()\" /> Input";
                                        ?>
                                    </div>
                                </div>
                                
                                <div id="div_upload">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>File Excel <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type="file" name="fileToUpload" id="fileToUpload" accept=".xlsx, .xls"><!--.xlsx,.xls-->
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            &nbsp;
                                        </div>
                                    </div>


                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <?PHP
                                            if ($fgroupidcard=="1" OR $fgroupidcard=="24") {
                                            ?>
                                                <button type='button' class='btn btn-success' onclick="UploadDataKeServer('1')">Upload</button>
                                                &nbsp;&nbsp;&nbsp;
                                            <?PHP
                                            }
                                            ?>
                                            <button type='button' class='btn btn-info' onclick="UploadDataKeServer('2')">Tampilkan Data</button>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div hidden id="div_input">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='soflow' name='cb_divisi' id='cb_divisi' onchange="">
                                                <?php
                                                    $query = "select DivProdId as divisi, nama as namadivisi from mkt.divprod where DivProdId in ('CAN', 'EAGLE', 'HO', 'PEACO', 'PIGEO', 'OTC', 'OTHER') ";
                                                    $query .=" ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    $ketemu= mysqli_num_rows($tampil);
                                                    echo "<option value='' selected>-- All --</option>";

                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pdivisiid=$z['divisi'];
                                                        $pdivisinm=$z['namadivisi'];

                                                        if ($pdivisiid==$fpengajuan)
                                                            echo "<option value='$pdivisiid' selected>$pdivisinm</option>";
                                                        else
                                                            echo "<option value='$pdivisiid'>$pdivisinm</option>";

                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='soflow' name='cb_bulan' id='cb_bulan' onchange="">
                                                <?php
                                                $urut=2;
                                                for ($x=1;$x<=12;$x++) {
                                                    $jml=  strlen($x);
                                                    $awal=$urut-$jml;
                                                    $nbulan=str_repeat("0", $awal).$x;
                                                    $nnmbln="";
                                                    
                                                    if ((INT)$x==1) $nnmbln="Januari";
                                                    elseif ((INT)$x==2) $nnmbln="Februari";
                                                    elseif ((INT)$x==3) $nnmbln="Maret";
                                                    elseif ((INT)$x==4) $nnmbln="April";
                                                    elseif ((INT)$x==5) $nnmbln="Mei";
                                                    elseif ((INT)$x==6) $nnmbln="Juni";
                                                    elseif ((INT)$x==7) $nnmbln="Juli";
                                                    elseif ((INT)$x==8) $nnmbln="Agustus";
                                                    elseif ((INT)$x==9) $nnmbln="September";
                                                    elseif ((INT)$x==10) $nnmbln="Oktober";
                                                    elseif ((INT)$x==11) $nnmbln="November";
                                                    elseif ((INT)$x==12) $nnmbln="Desember";
                                                    if (!empty($nnmbln)) {
                                                        if ($nbulan==$fblnpilih)
                                                            echo "<option value='$nbulan' selected>$nnmbln</option>";
                                                        else
                                                            echo "<option value='$nbulan'>$nnmbln</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
                                        <div class='col-md-3 col-sm-3 col-xs-5'>
                                            <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <button type='button' class='btn btn-dark' onclick="ListDataInput('1')">List Data</button>
                                        </div>
                                    </div>
                                    
                                </div>
                                

                            </div>
                        </div>
                    </div>

                </div>

            </div>


        </form>

        <div id='loading'></div>
        <div id='c-data'>

        </div>
        
        <div id='c-data2'>

        </div>
        
    </div>
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
                    
                    function ShowFromUploadInput(){
                        // Get the checkbox
                        $("#c-data").html("");
                        $("#c-data2").html("");
                        var checkBox = document.getElementById("opt_tipepl");
                        if (checkBox.checked == true){
                            div_upload.style.display = 'block';
                            div_input.style.display = 'none';
                        } else {
                            div_upload.style.display = 'none';
                            div_input.style.display = 'block';
                        }

                    }
    
                    function PilihDataCabang() {
                        var edivpl = document.getElementById("cb_divpilih").value;
                        $.ajax({
                            type:"post",
                            url:"module/budget/viewdatabgt.php?module=caridatacabangbypengajuan",
                            data:"udivpl="+edivpl,
                            success:function(data){
                                $("#cb_cabang").html(data);
                            }
                        });
                    }
                    
                    function ListDataInput(skey) {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
                            
                        var etahun = document.getElementById("e_tahun").value;
                        var edivpl = document.getElementById("cb_divpilih").value;
                        var ekryid = document.getElementById("cb_karyawan").value;
                        var edptid = document.getElementById("cb_dept").value;
                        var ecabid = document.getElementById("cb_cabang").value;
                        var ebln = document.getElementById("cb_bulan").value;
                        document.getElementById('e_totalsemua').value="";
                        $("#c-data").html("");
                        $("#c-data2").html("");
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/budget/mod_budget_uploaddatabudget/inputbudget.php?module="+module+"&act="+act+"&idmenu="+idmenu,
                            data:"ukryid="+ekryid+"&udptid="+edptid+"&udivpl="+edivpl+"&ucabid="+ecabid+"&utahun="+etahun+"&ubln="+ebln,
                            success:function(data){
                                $("#c-data2").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    function UploadDataKeServer(skey) {
                        var enmfile = document.getElementById("fileToUpload").value;
                        var etahun = document.getElementById("e_tahun").value;
                        var edivpl = document.getElementById("cb_divpilih").value;
                        var ekryid = document.getElementById("cb_karyawan").value;
                        var edptid = document.getElementById("cb_dept").value;
                        var ecabid = document.getElementById("cb_cabang").value;
                        
                        if (skey=="2") {
                            
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");
                            $("#c-data").html("");
                            $("#c-data2").html("");
                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/budget/mod_budget_uploaddatabudget/detailbudget.php?module=tmapilkandatabgt"+"&act=",
                                data:"ukryid="+ekryid+"&udptid="+edptid+"&udivpl="+edivpl+"&ucabid="+ecabid+"&utahun="+etahun,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
        
                            return 1;
                            
                        }else{
                            
                            if (ekryid=="") {
                                //alert("karyawan masih kosong..."); return false;
                            }
                            
                            if (enmfile=="") {
                                alert("File belum diload..."); return false;
                            }
                        
                        pText_="Jika ada data yang sudah diupload,\n\
maka akan dihapus terlebih dahulu sesuai Karyawan, Departemen, Cabang yang dipilih.\n\
Apakah yakin akan upload...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                //document.write("You pressed OK!")
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                document.getElementById("form_data01").action = "?module=uploaddatabudget"+"&act=eksekusiprosesuploadbgt"+"&idmenu="+idmenu+"&skey="+skey+"&nmodul="+module;
                                //document.getElementById("form_data01").action = "module/mod_budget_uploaddatabudget/aksi_uploaddatabudget.php?module="+module+"&act="+skey+"&idmenu="+idmenu+"&skey="+skey;
                                document.getElementById("form_data01").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        
                        
                        }//end skey
                        
                        
                    }
                </script>
            
            
            
            

        
    </div>
    
    
    
</div>

<?PHP

    break;
        case "eksekusiprosesuploadbgt":
        include "aksi_uploaddatabudget.php";
    break;

}
?>