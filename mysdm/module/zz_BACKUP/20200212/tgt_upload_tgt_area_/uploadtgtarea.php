<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $pperiode_ = date('Ym', strtotime($hari_ini));
    
    if (!empty($_SESSION['TGTUPDPERTPIL'])) $tgl_pertama=$_SESSION['TGTUPDPERTPIL'];
    
    $pidcabangpil="";
    $pidareapil="";
    $pfilename="";
    
    if (!empty($_SESSION['TGTUPDCABPIL'])) $pidcabangpil=$_SESSION['TGTUPDCABPIL'];
    if (!empty($_SESSION['TGTUPDAREAPIL'])) $pidareapil=$_SESSION['TGTUPDAREAPIL'];
    if (!empty($_SESSION['TGTUPDFOLDPIL'])) $pfilename=$_SESSION['TGTUPDFOLDPIL'];
    
    $nact="";
    if (isset($_GET['act'])) $nact=$_GET['act'];
    
    $pmyidcard=$_SESSION['IDCARD'];
    //$pmyidcard="0000002254";
    $filtercabangbyadmin="";
    $query = "select distinct icabangid from hrd.rsm_auth WHERE karyawanid='$pmyidcard'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        while ($rs= mysqli_fetch_array($tampil)) {
            $picabid_=$rs['icabangid'];
            $filtercabangbyadmin .="'".$picabid_."',";
        }
        if (!empty($filtercabangbyadmin)) {
            $filtercabangbyadmin="(".substr($filtercabangbyadmin, 0, -1).")";
        }
    }
    
    $pjudul="Upload Target Per Area";
    

    
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_ms.php";
        $aksi="module/tgt_upload_tgt_area/aksi_uploadtglarea.php";
        switch($_GET['act']){
            default:
                ?>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Periode
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01x'>
                                        <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="ShowDataArea()">
                                        <?PHP
                                        
                                        if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="24") {
                                            $filtercabangbyadmin="";
                                        }else{
                                            
                                            if (empty($filtercabangbyadmin)) $filtercabangbyadmin="('')";
                                            $filtercabangbyadmin = " AND iCabangId IN $filtercabangbyadmin ";
                                            
                                        }
                                        
                                        echo "<option value=''>-- Pilih --</option>";
                                        $query = "select iCabangId, nama from MKT.icabang where aktif='Y' $filtercabangbyadmin order by nama";
                                        $tampil = mysqli_query($cnmy, $query);
                                        while ($rx= mysqli_fetch_array($tampil)) {
                                            $nidcab=$rx['iCabangId'];
                                            $nnmcab=$rx['nama'];
                                            if ($pidcabangpil==$nidcab)
                                                echo "<option value='$nidcab' selected>$nnmcab</option>";
                                            else
                                                echo "<option value='$nidcab'>$nnmcab</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Area
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_area" name="cb_area" onchange="Kosongkan()">
                                        <?PHP
                                        if (!empty($pidcabangpil)) {
                                            
                                            $query ="select DISTINCT icabangid, areaid from tgt.targetarea WHERE icabangid='$pidcabangpil' AND DATE_FORMAT(bulan,'%Y%m')='$pperiode_'";
                                            $tampil_= mysqli_query($cnms, $query);
                                            $ketemu= mysqli_num_rows($tampil_);
                                            if ($ketemu==0) {
                                                echo "<option value=''>-- Pilih --</option>";
                                            }else{

                                                $piarean="";
                                                while ($nr= mysqli_fetch_array($tampil_)) {
                                                    $mmpidarea=$nr['areaid'];
                                                    $piarean .="'".$mmpidarea."',";
                                                }
                                                if (!empty($piarean)) {
                                                    $piarean .="'xxxcxxx'";
                                                    $piarean=" AND areaId IN (".$piarean.") ";
                                                }
                                            
                                                echo "<option value=''>-- Pilih --</option>";
                                                $query = "select iCabangId, areaId, Nama from sls.iarea where aktif='Y' AND iCabangId='$pidcabangpil' $piarean order by Nama";
                                                $tampil = mysqli_query($cnms, $query);
                                                while ($rx= mysqli_fetch_array($tampil)) {
                                                    $nidarea=$rx['areaId'];
                                                    $nnmarea=$rx['Nama'];
                                                    if ($pidareapil==$nidarea)
                                                        echo "<option value='$nidarea' selected>$nnmarea</option>";
                                                    else
                                                        echo "<option value='$nidarea'>$nnmarea</option>";
                                                }
                                                
                                            }
                                            
                                        }else{
                                            echo "<option value=''>-- Pilih --</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        
                            <div class='col-sm-2'>
                                Load File (<b>File XLSX</b>)
                                <div class="form-group">
                                    <input type="file" name="fileToUpload" id="fileToUpload" accept=".xlsx"><!--.xlsx,.xls-->
                                </div>
                            </div>

                            <div class='col-sm-4'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Upload</button>
                                   <button type='button' class='btn btn-info btn-xs' onclick='LihatDataSudahUpload()'>Lihat Data</button>
                                   <button type='button' class='btn btn-danger btn-xs' onclick='ResetUploadData()'>Reset Target Area (0)</button>
                               </div>
                           </div>
                        
                        
                            <div id='div_distributor'>


                            </div>
                        </form>
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                    </div>
                </div>
                
        
                <script>
                    function Kosongkan(){
                        $("#c-data").html("");
                    }
                    
                    function ShowDataArea() {
                        var etgl1 = document.getElementById("tgl1").value;
                        var ecabid = document.getElementById("cb_cabang").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/tgt_upload_tgt_area/viewdata.php?module=cariareacabang",
                            data:"ucabid="+ecabid+"&uperiode1="+etgl1,
                            success:function(data){
                                $("#cb_area").html(data);
                                Kosongkan();
                            }
                        });
                    }
                    
                    
                    function UploadDataKeServer() {
                        var ecabid = document.getElementById("cb_cabang").value;
                        var eareaid = document.getElementById("cb_area").value;
                        var enmfile = document.getElementById("fileToUpload").value;
                        var ebulan = document.getElementById("tgl1").value;
                        
                        if (ecabid=="") {
                            alert("Cabang Kosong..."); return false;
                        }
                        
                        if (eareaid=="") {
                            alert("Area Tidak Boleh Kosong..."); return false;
                        }
                        
                        if (enmfile=="") {
                            alert("File belum diload..."); return false;
                        }
                        
                        pText_="Jika ada data yang sudah diupload pada Cabang dan Area diBulan "+ebulan+", maka akan dihapus terlebih dahulu.\n\
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

                                document.getElementById("demo-form2").action = "?module=tgtaksiuploadtargetarea"+"&act=upload"+"&idmenu="+idmenu;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        
                    }
                    
                    
                    function LihatDataSudahUpload() {
                        
                        var etgl1=document.getElementById('tgl1').value;
                        var eidcabang=document.getElementById('cb_cabang').value;
                        var eareaid = document.getElementById("cb_area").value;
                        
                        if (eidcabang=="") {
                            alert("cabang belum diisi...");
                            return false;
                        }
                        
                        if (eareaid=="") {
                            alert("Area Tidak Boleh Kosong..."); return false;
                        }
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/tgt_upload_tgt_area/lihatdata.php?module=viewdata",
                            data:"uidcabang="+eidcabang+"&uperiode1="+etgl1+"&uareaid="+eareaid,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    
                    function ResetUploadData() {
                        
                        var etgl1=document.getElementById('tgl1').value;
                        var eidcabang=document.getElementById('cb_cabang').value;
                        var eareaid = document.getElementById("cb_area").value;
                        
                        if (eidcabang=="") {
                            alert("cabang belum diisi...");
                            return false;
                        }
                        
                        if (eareaid=="") {
                            //alert("Area Tidak Boleh Kosong..."); return false;
                        }
                        
                        if (eareaid=="") {
                            pText_="Semua data yang ada di cabang yang dipilih akan dinol (0) kan.\n\
Yakin akan Reset Data...?";
                        }else{
                            pText_="Semua data yang ada di cabang dan area yang dipilih akan dinol (0) kan.\n\
Yakin akan Reset Data...?";
                        }
                        
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                //document.write("You pressed OK!")
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/tgt_upload_tgt_area/resettargetarea.php?module=viewdata",
                                    data:"uidcabang="+eidcabang+"&uperiode1="+etgl1+"&uareaid="+eareaid,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                                
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        

                    }
                    
                </script>
        
        
                <script>
                    $(function() {
                        $('#tgl1').datepicker({
                            showButtonPanel: true,
                            changeMonth: true,
                            changeYear: true,
                            numberOfMonths: 1,
                            firstDay: 1,
                            dateFormat: 'MM yy',
                            onSelect: function(dateStr) {

                            },
                            onClose: function() {
                                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                                Kosongkan();
                                var eidcabang=document.getElementById('cb_cabang').value;
                                if (eidcabang=="") {
                                }else{
                                    ShowDataArea();
                                }
                            },

                            beforeShow: function() {
                                if ((selDate = $(this).val()).length > 0) 
                                {
                                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                                }
                            }
                        });
                    });
                </script>

                <style>
                    .divnone {
                        display: none;
                    }
                    #datatable th {
                        font-size: 13px;
                    }
                    #datatable td { 
                        font-size: 12px;
                    }
                    .ui-datepicker-calendar {
                        display: none;
                    }
                </style>
                <?PHP

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

