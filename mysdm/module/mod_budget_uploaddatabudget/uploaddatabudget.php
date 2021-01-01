<?php
    include "config/cek_akses_modul.php";
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    $puserid=$_SESSION['IDCARD'];
    
    
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    
    $ptahunpilih="2021";
    $bidcabang="";
    
    $ptxturl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    
    $pkaryawaid = "";
    $icabangid = "";
    
    $ppilihdivi="";
    $pseldivpili1="selected";
    $pseldivpili2="";
    
    $pdepartemen="";
    $pseldeppili0="selected";
    $pseldeppili1="";
    $pseldeppili2="";
    $pseldeppili3="";
    $pseldeppili4="";
    $pseldeppili5="";
    $pseldeppili6="";
    $pseldeppili7="";
    $pseldeppili8="";
    
    
    if (!empty($_SESSION['BGTUPDTHN'])) $ptahunpilih=$_SESSION['BGTUPDTHN'];
    if (!empty($_SESSION['BGTUPDDVL'])) $ppilihdivi=$_SESSION['BGTUPDDVL'];
    
    if ($ppilihdivi=="OTC" OR $ppilihdivi=="OT" OR $ppilihdivi=="CHC") {
        $pseldivpili1="";
        $pseldivpili2="selected";
    }else{
        $pseldivpili1="selected";
        $pseldivpili2="";
    }
    
    
    
    if (!empty($_SESSION['BGTUPDDPT'])) $pdepartemen=$_SESSION['BGTUPDDPT'];
    if (!empty($_SESSION['BGTUPDKRY'])) $pkaryawaid=$_SESSION['BGTUPDKRY'];
    if (!empty($_SESSION['BGTUPDCAB'])) $icabangid=$_SESSION['BGTUPDCAB'];
    
    
    if ($pdepartemen=="SLS") {
        $pseldeppili0="";
        $pseldeppili1="selected";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="FIN") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="selected";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="MS") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="selected";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="IT") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="selected";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="AUDIT") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="selected";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="PCH") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="selected";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="BUSDV") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="selected";
        $pseldeppili8="";
    }elseif ($pdepartemen=="MKT") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="selected";
    }else{
    
        $pseldeppili0="selected";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    
    }
    
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
        <?php
        switch($pact){
            default:
                ?>
        
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
                                            echo "<option value='ETH' $pseldivpili1>ETHICAL</option>";
                                            echo "<option value='OT' $pseldivpili2>CHC</option>";
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
                                            
                                            $query = "select karyawanId, nama From hrd.karyawan
                                                WHERE 1=1 ";
                                            $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                            $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P', 'LOGIN') "
                                                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                            $query .= " AND nama NOT IN ('ACCOUNTING')";
                                            $query .= " AND jabatanid NOT IN ('15', '10', '18', '08', '35', '40', '41', '14', '16', '17', '19', '23', '13', '38')";
                                            $query .= " AND jabatanid NOT IN ('12', '37', '28')";
                                            $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                            $query .= " AND karyawanid NOT IN ('0000000962', '0000001675')";
                                            $query .= " ORDER BY nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pkaryid=$z['karyawanId'];
                                                $pkarynm=$z['nama'];
                                                $pkryid=(INT)$pkaryid;
                                                if ($pkaryid==$pkaryawaid)
                                                    echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                else
                                                    echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
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
                                            echo "<option value='' $pseldeppili0>--Pilihan--</option>";
                                            echo "<option value='SLS' $pseldeppili1>SALES</option>";
                                            echo "<option value='FIN' $pseldeppili2>FINANCE</option>";
                                            echo "<option value='MS' $pseldeppili3>MS</option>";
                                            echo "<option value='IT' $pseldeppili4>IT</option>";
                                            echo "<option value='AUDIT' $pseldeppili5>AUDIT</option>";
                                            echo "<option value='PCH' $pseldeppili6>PURCHASING</option>";
                                            echo "<option value='BUSDV' $pseldeppili7>BUSSINESS DEVELOPMENT</option>";
                                            echo "<option value='MKT' $pseldeppili8>MARKETING</option>";
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
                                                    echo "<option value='$pcabangid' selected>$pcabnm</option>";
                                                else
                                                    echo "<option value='$pcabangid'>$pcabnm</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>File Excel <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="file" name="fileToUpload" id="fileToUpload" accept=".xlsx"><!--.xlsx,.xls-->
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
                                        <button type='button' class='btn btn-success' onclick="UploadDataKeServer('1')">Upload</button>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type='button' class='btn btn-info' onclick="UploadDataKeServer('2')">Tampilkan Data</button>
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
                    
                    
                    function PilihDataCabang() {
                        var edivpl = document.getElementById("cb_divpilih").value;
                        $.ajax({
                            type:"post",
                            url:"module/mod_budget_uploaddatabudget/viewdatabgtup.php?module=caridatacabang",
                            data:"udivpl="+edivpl,
                            success:function(data){
                                $("#cb_cabang").html(data);
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

                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/mod_budget_uploaddatabudget/detailbudget.php?module=tmapilkandatabgt"+"&act=",
                                data:"ukryid="+ekryid+"&udptid="+edptid+"&udivpl="+edivpl+"&ucabid="+ecabid+"&utahun="+etahun,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
        
                            return 1;
                            
                        }else{
                            
                            if (ekryid=="") {
                                alert("karyawan masih kosong..."); return false;
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

                                document.getElementById("form_data01").action = "?module=bgtuploaddatabudgetdivisi"+"&act=upload"+"&idmenu="+idmenu+"&skey="+skey+"&nmodul="+module;
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
            
            
            
            
                <?PHP
                
            break;
        
        }
        ?>
        
    </div>
    
    
    
</div>

