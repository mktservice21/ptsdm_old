<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_insentif = date('F Y', strtotime('-2 month', strtotime($hari_ini)));
    include "config/koneksimysqli_it.php";
    $icabang="";
    if (!empty($_SESSION['SPGMSTPRSCAB'])) $icabang=$_SESSION['SPGMSTPRSCAB'];
    if (!empty($_SESSION['SPGMSTPRSTGL'])) $tgl_pertama=$_SESSION['SPGMSTPRSTGL'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Proses Data SPG Per Bulan";
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
        //$aksi="module/md_m_spg_proses/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel(sts) {
                        KlikDataTabel(sts);
                    }

                    $(document).ready(function() {
                        var ecabang=document.getElementById('e_cabangid').value;
                        if (ecabang != "") {
                            //KlikDataTabel();
                        }
                    } );

                    function KlikDataTabel(sts) {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket="";
                        var ecabang=document.getElementById('e_cabangid').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_spg_proses/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&ucabang="+ecabang+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1+"&utglinsentif="+etgl2+"&usts="+sts,
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
                                Periode Insentif
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01x'>
                                        <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_insentif; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

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
                                    <select class='form-control input-sm' id='e_cabangid' name='e_cabangid' onchange="Kosongkan()">
                                        <?PHP
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            //$query = "select icabangid_o, nama from MKT.icabang_o WHERE aktif='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE aktif='Y' AND i_spg='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
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

                            <div class='col-sm-8'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="Belum Proses" onclick="RefreshDataTabel('1')">&nbsp;
                                   <input type='button' class='btn btn-info btn-xs' id="s-submit" value="Sudah Proses" onclick="RefreshDataTabel('2')">&nbsp;
                                   <input type='button' class='btn btn-danger btn-xs' id="s-submit" value="Pending" onclick="RefreshDataTabel('3')">&nbsp;
                                   <input type='button' class='btn btn-default btn-xs' id="s-submit" value="Sudah Proses FIN" onclick="RefreshDataTabel('4')">&nbsp;
                                   <input type='button' class='btn btn-default btn-xs' id="s-submit" value="Sudah Proses MGR" onclick="RefreshDataTabel('5')">&nbsp;
                               </div>
                           </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th width='200px' align="center">Nama SPG</th>
                                        <th width='150px' align="center">Penempatan</th>
                                        <th align="center" nowrap>Incentive</th>
                                        <th align="center" nowrap>Gaji Pokok</th>
                                        <th align="center" nowrap>Hari Kerja</th>
                                        <th align="center" nowrap>U. Makan</th>
                                        <th align="center" nowrap>T. Makan</th>
                                        <th align="center" nowrap>Sewa Kendaraan</th>
                                        <th align="center" nowrap>Pulsa</th>
                                        <th align="center" nowrap>Parkir</th>
                                        <th align="center" nowrap>Total</th>
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
        $('#tgl1, #tgl2').datepicker({
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