<?PHP
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    include "config/koneksimysqli_it.php";
    if (!empty($_SESSION['SPGMSTGJTGLCAB'])) $tgl_pertama=$_SESSION['SPGMSTGJTGLCAB'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Proses Call Incentive";
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
        //$aksi="module/mst_prosesinsentif/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel(sts) {
                        KlikDataTabel(sts);
                    }

                    $(document).ready(function() {
                        
                    } );

                    function KlikDataTabel(sts) {
                        //alert(sts); return false;
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket=""
                        var etgl1=document.getElementById('tgl1').value;
                        var eidkaryawan=document.getElementById('e_karyawanid').value;
                        var eidjabatan=document.getElementById('e_jabatanid').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mst_prosesinsentif/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&uidkaryawan="+eidkaryawan+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1+"&uidjabatan="+eidjabatan+"&usts="+sts,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function getKaryawan(){
                        var ejbt = document.getElementById("e_jabatanid").value;
                        $.ajax({
                            type:"post",
                            url:"module/mst_prosesinsentif/viewdata.php?module=viewkaryawanjbt",
                            data:"ujbt="+ejbt,
                            success:function(data){
                                $("#e_karyawanid").html(data);
                            }
                        });
                    }
                </script>
                

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                            <div class='col-sm-2'>
                                Periode
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
                                        <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Jabatan
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_jabatanid' name='e_jabatanid' onchange="getKaryawan()">
                                        <?PHP
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            $query = "select jabatanid, nama from hrd.jabatan WHERE jabatanId='15' order by 1,2";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while($s= mysqli_fetch_array($tampil)) {
                                                $pjabatanid=$s['jabatanid'];
                                                $pnmjabatan=$s['nama'];
                                                echo "<option value='$pjabatanid' selected>$pjabatanid - $pnmjabatan</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Karyawan
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_karyawanid' name='e_karyawanid'>
                                        <?PHP
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            $query = "select karyawanId, nama from hrd.karyawan WHERE jabatanId='15' AND IFNULL(aktif,'')<>'N' "
                                                    . " AND karyawanId NOT IN (select distinct IFNULL(karyawanId,'') from dbmaster.t_karyawanadmin) order by 2,1";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while($s= mysqli_fetch_array($tampil)) {
                                                $pkaryawanid=$s['karyawanId'];
                                                $pnmkaryawan=$s['nama'];
                                                echo "<option value='$pkaryawanid'>$pnmkaryawan</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-6'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="Lihat Belum Proses" onclick="RefreshDataTabel('1')">&nbsp;
                                   <input type='button' class='btn btn-info btn-xs' id="s-submit" value="Lihat Sudah Proses" onclick="RefreshDataTabel('2')">&nbsp;
                               </div>
                           </div>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th width='20px'></th>
                                        <th width='300px' align="center">Nama</th>
                                        <th align="center" width='80px'>%</th>
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

