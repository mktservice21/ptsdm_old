<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    include "config/koneksimysqli_it.php";
    $icabang="";
    $pstatusspdpilih="";
    $ppilihproses="1";
    if (!empty($_SESSION['SPGMSTIMPCAB'])) $icabang=$_SESSION['SPGMSTIMPCAB'];
    if (!empty($_SESSION['SPGMSTIMPTGL'])) $tgl_pertama=$_SESSION['SPGMSTIMPTGL'];
    if (!empty($_SESSION['SPGMSTIMPSTS'])) $pstatusspdpilih=$_SESSION['SPGMSTIMPSTS'];
    if (!empty($_SESSION['SPGMSTIMPPILIH'])) $ppilihproses=$_SESSION['SPGMSTIMPPILIH'];
    
    
?>
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Import Data SPG IT to MS";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h2>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/md_m_spg_importdata/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel(sket) {
                        KlikDataTabel(sket);
                    }

                    $(document).ready(function() {
                        var sket=document.getElementById('e_pilihpros').value;
                        var ecabang=document.getElementById('e_cabangid').value;
                        if (ecabang != "") {
                            KlikDataTabel(sket);
                        }
                    } );

                    function KlikDataTabel(sket) {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ecabang=document.getElementById('e_cabangid').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var estspilih=document.getElementById('e_stsspg').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_spg_importdata/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act=importspg",
                            data:"ucabang="+ecabang+"&utgl="+etgl1+"&ustspilih="+estspilih+"&uketpilih="+sket,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                </script>
                

                    
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

                            <div hidden class='col-sm-2'>
                                Pilih Proses
                                <div class="form-group">
                                    <input type='text' id='e_pilihpros' name='e_pilihpros' required='required' class='form-control input-sm' value='<?PHP echo $ppilihproses; ?>' Readonly>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_cabangid' name='e_cabangid' onchange="Kosongkan()">
                                        <?PHP
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            //$query = "select icabangid_o, nama from MKT.icabang_o WHERE aktif='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE aktif='Y' AND i_spg='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ";
                                            if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="23" OR $_SESSION['GROUP']=="24" OR $_SESSION['GROUP']=="26" OR $_SESSION['GROUP']=="37" OR $_SESSION['GROUP']=="38") {
                                            }else{
                                                
                                                $icabang=$_SESSION['IDCABANG'];
                                                
                                                if ($_SESSION['ALOKASIID']=="JKT_MT" OR $_SESSION['ALOKASIID']=="JKT_RETAIL") {
                                                    $icabang=$_SESSION['ALOKASIID'];
                                                    $query .= " AND icabangid_o='$icabang' ";
                                                }else{
                                                    $query .= " AND icabangid_o IN (SELECT icabangid FROM dbmaster.otc_cabang_apv WHERE karyawanid='$_SESSION[IDCARD]')";
                                                }
                                            }
                                            $query .= " ORDER BY nama";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while($s= mysqli_fetch_array($tampil)) {
                                                $pcabangid=$s['icabangid_o'];
                                                $pnmcabang=$s['nama'];
                                                if ($pcabangid==$icabang)
                                                    echo "<option value='$pcabangid' selected>$pnmcabang</option>";
                                                else
                                                    echo "<option value='$pcabangid'>$pnmcabang</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Status <span style="color:red;"><i>(belum import)</i></span>
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_stsspg' name='e_stsspg' onchange="Kosongkan()">
                                        <?PHP
                                            if ($pstatusspdpilih=="A") {
                                                echo "<option value=''>-- All --</option>";
                                                echo "<option value='A' selected>Aktif</option>";
                                                echo "<option value='T'>Tidak Aktif</option>";
                                            }elseif ($pstatusspdpilih=="T") {
                                                echo "<option value=''>-- All --</option>";
                                                echo "<option value='A'>Aktif</option>";
                                                echo "<option value='T' selected>Tidak Aktif</option>";
                                            }else{
                                                echo "<option value='' selected>-- All --</option>";
                                                echo "<option value='A'>Aktif</option>";
                                                echo "<option value='T'>Tidak Aktif</option>";
                                            }
                                            
                                        ?>
                                    </select>
                                </div>
                            </div>

                       
                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="Belum Import" onclick="RefreshDataTabel('1')">&nbsp;
                                   <input type='button' class='btn btn-info btn-xs' id="s-submit" value="Sudah Import" onclick="RefreshDataTabel('2')">&nbsp;
                               </div>
                           </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'></th>
                                        <th width='10px'>No</th>
                                        <th width='100px' align="center">ID SPG</th>
                                        <th width='300px' align="center">Nama SPG</th>
                                        <th width='200px' align="center">Jabatan</th>
                                        <th align="center" nowrap>Area</th>
                                        <th align="center" nowrap>Alokasi</th>
                                        <th align="center" nowrap>Penempatan</th>
                                        <th align="center" nowrap>Tgl. Masuk</th>
                                        <th align="center" nowrap>Tgl. Keluar</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
                

                <?PHP

            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

<script>
    function Kosongkan(){
        $("#c-data").html("");
    }
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
    .ui-datepicker-calendar {
        display: none;
    }
    
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