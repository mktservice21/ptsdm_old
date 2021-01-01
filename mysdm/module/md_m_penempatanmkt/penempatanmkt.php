<?PHP
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    if (!empty($_SESSION['MKTTMPPERIODE'])) $tgl_pertama=$_SESSION['MKTTMPPERIODE'];
    
    $psregi="B";
    $pscab="";
    $psare="";
    
    if (!empty($_SESSION['MKTTMPREG'])) $psregi=$_SESSION['MKTTMPREG'];
    if (!empty($_SESSION['MKTTMPCAB'])) $pscab=$_SESSION['MKTTMPCAB'];
    if (!empty($_SESSION['MKTTMPARE'])) $psare=$_SESSION['MKTTMPARE'];
    
    $nact="";
    if (isset($_GET['act'])) $nact=$_GET['act'];
    
    $pjudul="Penempatan Marketing";
    if ($nact=="editdatamr") $pjudul="Edit Penempatan Marketing MR";
    if ($nact=="editdataam") $pjudul="Edit Penempatan Marketing AM";
    if ($nact=="editdatadm") $pjudul="Edit Penempatan Marketing DM";
    if ($nact=="editdatasm") $pjudul="Edit Penempatan Marketing SM";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_ms.php";
        $aksi="module/md_m_penempatanmkt/aksi_penempatanmkt.php";
        switch($_GET['act']){
            default:
                ?>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
    
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
                            Region
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_region" name="cb_region" onchange="ShowData()">
                                    <?PHP
                                    //echo "<option value=''>--Pilih--</option>";
                                    if ($psregi=="B"){
                                        echo "<option value='B' selected>Barat</option>";
                                        echo "<option value='T'>Timur</option>";    
                                    }else{
                                        echo "<option value='B'>Barat</option>";
                                        echo "<option value='T' selected>Timur</option>";
                                    }
                                    
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Cabang
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="ShowDataArea()">
                                    <?PHP
                                    $query = "select distinct iCabangId, nama from ms.icabang where region='$psregi' and ifnull(aktif,'')<>'N' order by nama";
                                    $tampil=mysqli_query($cnms, $query);
                                    echo "<option value=''>--Pilih--</option>";
                                    while ($r=  mysqli_fetch_array($tampil)) {
                                        $picabangid=$r['iCabangId'];
                                        $pnmcabang=$r['nama'];
                                        if ($pscab==$picabangid)
                                            echo "<option value='$picabangid' selected>$pnmcabang</option>";
                                        else
                                            echo "<option value='$picabangid'>$pnmcabang</option>";
                                        $no++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Area
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_area" name="cb_area" onchange="">
                                    <?PHP
                                    echo "<option value='' selected>--Pilih--</option>";
                                    if (!empty($picabangid)) {
                                        $query = "select distinct areaId, Nama nama from ms.iarea where ifnull(aktif,'')<>'N' AND iCabangId='$pscab' order by Nama";
                                        $tampil=mysqli_query($cnms, $query);
                                        while ($ra=  mysqli_fetch_array($tampil)) {
                                            $piareaid=$ra['areaId'];
                                            $pnmarea=$ra['nama'];
                                            if ($psare==$piareaid)
                                                echo "<option value='$piareaid' selected>$pnmarea</option>";
                                            else
                                                echo "<option value='$piareaid'>$pnmarea</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        
                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">
                           </div>
                       </div>
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                    </div>
                </div>
                
        
                <script>
                    $(document).ready(function() {
                        var ecab = document.getElementById("cb_cabang").value;
                        var earea = document.getElementById("cb_area").value;

                        if (ecab=="") {
                            ShowDataCabang();
                        }

                        if (earea=="") {
                            ShowDataArea();
                        }
                        if (ecab!="") {
                            RefreshDataTabel();
                        }
                        
                    } );

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
                                ShowDataCabang();
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

                    function Kosongkan(){
                        $("#c-data").html("");
                    }

                    function ShowData(){
                        ShowDataCabang();
                    }

                    function ShowDataCabang(){
                        var eregion = document.getElementById("cb_region").value;
                        var etgl = document.getElementById("tgl1").value;
                        var ecabawal = document.getElementById("cb_cabang").value;
                        $.ajax({
                            type:"post",
                            url:"module/md_m_penempatanmkt/viewdata.php?module=viewdatacabangmarketing",
                            data:"uregion="+eregion+"&utgl="+etgl+"&ucabawal="+ecabawal,
                            success:function(data){
                                $("#cb_cabang").html(data);
                                ShowDataArea();
                            }
                        });
                    }

                    function ShowDataArea(){
                        var ecabang = document.getElementById("cb_cabang").value;
                        var etgl = document.getElementById("tgl1").value;
                        var eareaawal = document.getElementById("cb_area").value;
                        $.ajax({
                            type:"post",
                            url:"module/md_m_penempatanmkt/viewdata.php?module=viewdataareamarketing",
                            data:"ucabang="+ecabang+"&utgl="+etgl+"&uareaawal="+eareaawal,
                            success:function(data){
                                $("#cb_area").html(data);
                                Kosongkan();
                            }
                        });
                    }


                    function RefreshDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");

                        var ebln = document.getElementById("tgl1").value;
                        var eregion = document.getElementById("cb_region").value;
                        var ecabang = document.getElementById("cb_cabang").value;
                        var earea = document.getElementById("cb_area").value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_penempatanmkt/viewdatatable.php?module=viewdatatabel"+"&idmenu="+idmenu+"&module="+module,
                            data:"ubln="+ebln+"&uregion="+eregion+"&ucabang="+ecabang+"&uarea="+earea,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
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

            case "editdatamr":
                include "edit.php";
            break;

            case "editdataam":
                include "edit.php";
            break;

            case "editdatadm":
                include "edit.php";
            break;

            case "editdatasm":
                include "edit.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

