<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    if (!empty($_SESSION['STCUPDPERTPIL'])) $tgl_pertama=$_SESSION['STCUPDPERTPIL'];
    
    $pidcabangpil="";
    $pfilename="";
    
    if (!empty($_SESSION['STCUPDFOLDPIL'])) $pfilename=$_SESSION['STCUPDFOLDPIL'];
    
    $nact="";
    if (isset($_GET['act'])) $nact=$_GET['act'];
    
    $pjudul="Upload Data Stock";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_ms.php";
        $aksi="module/stc_uploaddatastock/aksi_uploadstock.php";
        switch($_GET['act']){
            default:
                ?>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div hidden class='col-sm-2'>
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
                        
                            <div class='col-sm-3'>
                                Load File (<b>Format File XLSX</b>)
                                <div class="form-group">
                                    <input type="file" name="fileToUpload" id="fileToUpload" accept=".xlsx"><!--.xlsx,.xls-->
                                </div>
                            </div>

                            <div class='col-sm-5'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Upload</button>
                                   <button type='button' class='btn btn-info btn-xs' onclick='LihatDataSudahUpload()'>Lihat Data</button>
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
                    
                    
                    function UploadDataKeServer() {
                        var enmfile = document.getElementById("fileToUpload").value;
                        var ebulan = document.getElementById("tgl1").value;
                        
                        
                        if (enmfile=="") {
                            alert("File belum diload..."); return false;
                        }
                        
                        pText_="Apakah akan melakukan upload data stock...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                //document.write("You pressed OK!")
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                document.getElementById("demo-form2").action = "?module=stcaksiuploadstock"+"&act=upload"+"&idmenu="+idmenu;
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
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/stc_uploaddatastock/lihatdatastock.php?module=viewdata",
                            data:"uperiode1="+etgl1,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
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

