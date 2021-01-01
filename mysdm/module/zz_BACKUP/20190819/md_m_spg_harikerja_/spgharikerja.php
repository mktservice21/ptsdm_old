<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    include "config/koneksimysqli_it.php";
    $icabang="";
    if (!empty($_SESSION['SPGMSTHKCAB'])) $icabang=$_SESSION['SPGMSTHKCAB'];
    if (!empty($_SESSION['SPGMSTHKTGL'])) $tgl_pertama=$_SESSION['SPGMSTHKTGL'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Hari Kerja SPG Per Bulan";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h2>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/md_m_spg_harikerja/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        var ecabang=document.getElementById('e_cabangid').value;
                        if (ecabang != "") {
                            KlikDataTabel();
                        }
                    } );

                    function KlikDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket="";
                        var ecabang=document.getElementById('e_cabangid').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_spg_harikerja/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&ucabang="+ecabang+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function ValidasiDataInput(){
                        var ecabang=document.getElementById('e_cabangid').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var ket="";
                        
                        //alert(ecabang+", "+etgl1); return false;
                        var pText_="Data yang sudah di validasi tidak bisa diubah dan dihapus.\n\
                                cek kembali datanya...\n\
                                Jika sudah sesuai klik OK";
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                $.ajax({
                                    type:"post",
                                    url:"module/md_m_spg_harikerja/simpan_validasi.php?module="+module+"&act=input"+"&idmenu="+idmenu,
                                    data:"ucabang="+ecabang+"&utgl="+etgl1,
                                    success:function(data){
                                        if (data.length > 2) {
                                            alert(data);
                                        }
                                        KlikDataTabel();
                                    }
                                });
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        
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

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                                   <!--&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                                   <input type='button' class='btn btn-danger btn-xs' id="s-submit" value="Validate" onclick="ValidasiDataInput()">&nbsp;
                                   -->
                               </div>
                           </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th width='300px' align="center">Nama SPG</th>
                                        <th width='200px' align="center">Penempatan</th>
                                        <th align="center" nowrap>Gaji Pokok</th>
                                        <th align="center" nowrap>U. Makan</th>
                                        <th align="center" nowrap>Sewa Kendaraan</th>
                                        <th align="center" nowrap>Pulsa</th>
                                        <th align="center" nowrap>Parkir</th>
                                        <th width='80px'></th>
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