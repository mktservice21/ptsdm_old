<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    //$tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    $pperiodepilih= date("d/m/Y", strtotime($hari_ini));
    
    if (!empty($_SESSION['TGTUPDPERTPILCB'])) $tgl_pertama=$_SESSION['TGTUPDPERTPILCB'];
    
    $pperiode_ = date("Ym", strtotime($tgl_pertama));
    
    $pidcabangpil="";
    $pfilename="";
    
    if (!empty($_SESSION['TGTUPDCABPILCB'])) $pidcabangpil=$_SESSION['TGTUPDCABPILCB'];
    if (!empty($_SESSION['TGTUPDFOLDPILCB'])) $pfilename=$_SESSION['TGTUPDFOLDPILCB'];
    
    $nact="";
    if (isset($_GET['act'])) $nact=$_GET['act'];
    
    $idinputspd="";
    $psudahapprove=false;
    $query = "select periode, dir1_tgl, idinput FROM dbmaster.t_spd_bpjs0 WHERE periode='$pperiode_' AND IFNULL(stsnonaktif,'')<>'Y'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nrs= mysqli_fetch_array($tampil);
        $ntgldirapv=$nrs['dir1_tgl'];
        $idinputspd=$nrs['idinput'];
        if ($ntgldirapv=="0000-00-00" OR $ntgldirapv=="0000-00-00 00:00:00") $ntgldirapv="";
        
        if (!empty($ntgldirapv)) $psudahapprove=true;
        
    }else{
        $psudahapprove=true;
    }
            
    
    if (!empty($idinputspd)) {
        $query = "select tgl_dir, tgl_dir2 from dbmaster.t_suratdana_br where idinput='$idinputspd'";
        $tampild= mysqli_query($cnmy, $query);
        $ketemud= mysqli_num_rows($tampild);
        if ($ketemud>0) {
            $ndir= mysqli_fetch_array($tampild);
            $papprovdir=$ndir['tgl_dir'];
            if ($papprovdir=="0000-00-00") $papprovdir="";
            if ($papprovdir=="0000-00-00 00:00:00") $papprovdir="";
            if (!empty($papprovdir)) {
                $psudahapprove=true;
            }
        }

    }
            
    $pjudul="Upload Data SPD BPJS";
    
    $ptxturl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_spdbpjs/aksi_spdbpjs.php";
        switch($_GET['act']){
            default:
                ?>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <input type="hidden" class="form-control" id='e_txturl' name='e_txturl' autocomplete="off" value='<?PHP echo $ptxturl; ?>' readonly>
                            
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
                                Tgl. Pengajuan
                               <div class="form-group">
                                    <div class='input-group date' id='mytgl02'>
                                        <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $pperiodepilih; ?>' readonly>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>
                                    </div>
                               </div>
                           </div>
                            
                            
                            <div class='col-sm-3'>
                                Load File (<b>Format File XLSX</b>)
                                <div class="form-group">
                                    <input type="file" name="fileToUpload" id="fileToUpload" accept=".xlsx"><!--.xlsx,.xls-->
                                </div>
                            </div>

                            <div class='col-sm-5'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-dark btn-xs' onclick="UploadDataKeServer('1')">Upload</button>
                                   <button type='button' class='btn btn-info btn-xs' onclick="UploadDataKeServer('2')">Lihat Data</button>
                                   <?PHP
                                   if ($psudahapprove==false) {
                                   ?>
                                        <button type='button' class='btn btn-danger btn-xs' onclick="ProsesDataHapus('')">Hapus Data</button>
                                   <?PHP
                                   }
                                   ?>
                                   
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
                    
                    
                    function UploadDataKeServer(skey) {
                        var enmfile = document.getElementById("fileToUpload").value;
                        var ebulan = document.getElementById("tgl1").value;
                        var etgl = document.getElementById("e_tglberlaku").value;
                        
                        if (skey=="2") {
                            
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");

                            document.getElementById("demo-form2").action = "?module=tgtaksiuploadspdbpjs"+"&act=upload"+"&idmenu="+idmenu+"&skey="+skey+"&nmodul="+module;
                            document.getElementById("demo-form2").submit();
                            return 1;
                            
                        }else{
                            
                            if (enmfile=="") {
                                alert("File belum diload..."); return false;
                            }
                        
                        pText_="Jika ada data yang sudah diupload diBulan "+ebulan+",\n\
tabel data BPJS akan dihapus terlebih dahulu.\n\
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

                                document.getElementById("demo-form2").action = "?module=tgtaksiuploadspdbpjs"+"&act=upload"+"&idmenu="+idmenu+"&skey="+skey+"&nmodul="+module;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        
                        
                        }//end skey
                        
                        
                    }
                    
                    
                    function LihatDataSudahUpload() {
                        
                        var etgl1=document.getElementById('tgl1').value;
                        
                        if (eidcabang=="") {
                            alert("cabang belum diisi...");
                            return false;
                        }
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_spdbpjs/lihatdata.php?module=viewdata",
                            data:"uperiode1="+etgl1,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                </script>
        
                <script>
                    function ProsesDataHapus(){
                        var nhapus = document.getElementById("tgl1").value;
                        
                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses hapus ...?');
                            if (r==true) {

                                var txt="";


                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("demo-form2").action = "module/mod_br_spdbpjs/aksi_spdbpjs.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+txt+"&hapusnix="+nhapus+"&nmodul="+module;
                                document.getElementById("demo-form2").submit();
                                return 1;
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

