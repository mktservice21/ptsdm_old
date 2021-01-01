<?PHP
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pilih = date('d F Y', strtotime($hari_ini));
    
    $pperiode_ = date('Ym', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    
    $pilihotc=false;
    if ($fgroupidcard=="26" OR $fdivisi=="OTC") {
        $pilihotc=true;
    }

?>


<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Data Karyawan";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                elseif ($_GET['act']=="editdataatasan")
                    echo "Edit Atasan $judul";
                elseif ($_GET['act']=="editdatadivisiarea")
                    echo "Edit Divisi dan Cabang $judul";
                elseif ($_GET['act']=="otcsbeditdataatasan")
                    echo "Edit Atasan $judul OTC";
                elseif ($_GET['act']=="editdivisijabatan")
                    echo "Edit Divisi / Jabatan $judul";
                elseif ($_GET['act']=="editnonaktif")
                    echo "Aktif / NonAktif $judul";
                else
                    echo "$judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_brrutin/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script>
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ejabatan=document.getElementById('e_jabatan').value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mst_isidatakaryawan/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"ujabatan="+ejabatan,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                </script>

                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <?PHP
                        $pact=$_GET['act'];
                        if ($pact=="completeinup") {
                            if (isset($_GET['id'])) {
                                $pidbaruinput=$_GET['id'];
                                $query = "select karyawanid, nama, jabatanid, divisiid, skar, icabangid from hrd.karyawan where karyawanid='$pidbaruinput'";
                                $tampilk= mysqli_query($cnmy, $query);
                                $ketemu= mysqli_num_rows($tampilk);
                                if ($ketemu==0) {
                                    echo "<h1>DATA YANG ANDA INPUT/UPDATE GAGAL... ID : $pidbaruinput Mohon INFO MS dan Infokan ID nya</h1>";
                                }else{
                                    $nrx= mysqli_fetch_array($tampilk);
                                    $pnxnama=$nrx['nama'];
                                    $pnxjabatanid=$nrx['jabatanid'];
                                    $pnxdivisiid=$nrx['divisiid'];
                                    $pnxskarid=$nrx['skar'];
                                    $pnxcabangid=$nrx['icabangid'];
                                    
                                    if (empty($pnxnama)) {
                                        echo "<h1>ID yang telah diinput $pidbaruinput, NAMA KOSONG</h1>";
                                    }
                                    
                                    if (empty($pnxjabatanid)) {
                                        echo "<h1>Jabatan Kosong, edit ulang... !!!</h1>";
                                    }
                                    
                                    if (empty($pnxdivisiid)) {
                                        echo "<h1>Divisi Kosong, edit ulang... !!!</h1>";
                                    }
                                    
                                    if (empty($pnxskarid)) {
                                        echo "<h1>Status Karyawan kontrak/tetap Kosong, edit ulang... !!!</h1>";
                                    }
                                    
                                    if (empty($pnxcabangid)) {
                                        echo "<h1>Cabang Kosong, edit ulang... !!!</h1>";
                                    }
                                    
                                }
                                
                            }
                        }
                        ?>
                        
                        
                        <?PHP if ($pilihotc==false) { ?>
                            <?PHP if ($fgroupidcard=="1") {// OR $fgroupidcard!="24" OR $fgroupidcard!="29" ?>
                                <div class='x_title'>
                                    <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                        onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                        <small></small>
                                    </h2>
                                    <div class='clearfix'></div>
                                </div>
                            <?PHP } ?>
                        <?PHP } ?>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                            <div class='col-sm-2'>
                                Jabatan
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_jabatan' name='e_jabatan'>
                                      <option value='' selected>-- Pilihan --</option>
                                      <?PHP
                                        include "config/koneksimysqli_it.php";
                                        $query="SELECT jabatanId, nama FROM hrd.jabatan ";
                                        $query .=" order by jabatanId";
                                        $sql=mysqli_query($cnit, $query);
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            if ($Xt['jabatanId']==$_SESSION['FMSTJBT'])
                                                echo "<option value='$Xt[jabatanId]' selected>$Xt[jabatanId] - $Xt[nama]</option>";
                                            else
                                                echo "<option value='$Xt[jabatanId]'>$Xt[jabatanId] - $Xt[nama]</option>";
                                        }
                                      ?>
                                    </select>
                                </div>
                            </div>


                            <div class='col-sm-2'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-success btn-xs' onclick='KlikDataTabel()'>View Data</button>
                               </div>
                           </div>
                            
                        </form>
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                    </div>
                </div>

                <?PHP

            break;

            case "tambahbaru":
                if ($pilihotc==false) {
                    include "tambah.php";
                }
            break;
            case "editdata":
                if ($pilihotc==false) {
                    include "tambah.php";
                }
            break;
            case "editdataatasan":
                if ($pilihotc==false) {
                    include "editatasan.php";
                }
            break;
            case "editdatadivisiarea":
                include "editdivisiarea.php";
            break;
            case "otcsbeditdataatasan":
                include "editatasanotc.php";
            break;
            case "editdivisijabatan":
                include "editdivisijbt.php";
            break;
            case "editnonaktif":
                include "editnonaktif.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>