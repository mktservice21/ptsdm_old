<?PHP
    $hari_ini = date("Y-m-d");
    $hari_ini = "2020-01-01";
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    if (!empty($_SESSION['MKSTRGDPERIODE'])) $tgl_pertama=$_SESSION['MKSTRGDPERIODE'];
    
    $pikaryawanpilih=$_SESSION['IDCARD'];
    $pscab="";
    
    if (!empty($_SESSION['MKSTRGDKRY'])) $pikaryawanpilih=$_SESSION['MKSTRGDKRY'];
    if (!empty($_SESSION['MKSTRGDCAB'])) $pscab=$_SESSION['MKSTRGDCAB'];
    
    $nact="";
    if (isset($_GET['act'])) $nact=$_GET['act'];
    
    $pjudul="Target Sales Per Tahun dan Daerah";
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
                            SM
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_karyawanid" name="cb_karyawanid" onchange="ShowData()">
                                    <?PHP
                                    
                                    $query = "select karyawanId, nama from hrd.karyawan WHERE jabatanId IN ('20') AND (IFNULL(tglkeluar,'')='' OR tglkeluar='0000-00-00') order by karyawanId";
                                    $tampil= mysqli_query($cnmy, $query);
                                    while ($r=  mysqli_fetch_array($tampil)) {
                                        $pkaryawanid=$r['karyawanId'];
                                        $pnmkaryawan=$r['nama'];
                                        if ($pkaryawanid==$pikaryawanpilih)
                                            echo "<option value='$pkaryawanid' selected>$pnmkaryawan</option>";
                                        else
                                            echo "<option value='$pkaryawanid'>$pnmkaryawan</option>";
                                    }
                                    
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Daerah
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_cabang" name="cb_cabang">
                                    <?PHP
                                    $query = "select distinct idcabang, nama from ms.cbgytd where id_sm='$pikaryawanpilih' order by nama";
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
                        var ekry = document.getElementById("cb_karyawanid").value;

                        if (ekry=="") {
                        }else{
                            ShowDataCabang();
                        }
                        
                    } );

                    $(function() {
                        $('#tgl1x').datepicker({
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
                        var eidkaryawan = document.getElementById("cb_karyawanid").value;
                        $.ajax({
                            type:"post",
                            url:"module/mst_trg_thn_daerah/viewdata.php?module=viewdatacabang",
                            data:"uidkaryawan="+eidkaryawan,
                            success:function(data){
                                $("#cb_cabang").html(data);
                            }
                        });
                    }


                    function RefreshDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");

                        var ebln = document.getElementById("tgl1").value;
                        var eidkaryawan = document.getElementById("cb_karyawanid").value;
                        var ecabang = document.getElementById("cb_cabang").value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mst_trg_thn_daerah/viewdatatable.php?module=viewdatatabel"+"&idmenu="+idmenu+"&module="+module,
                            data:"ubln="+ebln+"&uidkaryawan="+eidkaryawan+"&ucabang="+ecabang,
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

        }
        ?>

    </div>
    <!--end row-->
</div>

