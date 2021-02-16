<?PHP
    include "config/cek_akses_modul.php";
	//server 2020 06 15
    $hari_ini = date("Y-m-d");
    $hari_ini2 = date("Y-01-d");
    //$tgl_pertama = date('F Y', strtotime('-2 month', strtotime($hari_ini)));
    $pidkaryawan="";
    if (!empty($_SESSION['KSDTAPTKRY'])) $pidkaryawan = $_SESSION['KSDTAPTKRY'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    $fgroupid=$_SESSION['GROUP'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    
    
    //$fkaryawan="0000001900"; $fjbtid="38";//hapussaja
    
    $pfilterkaryawan="";
    $pfilterkaryawan2="";
    $pfilterkry="";
    //$fjbtid=="38" OR $fjbtid=="33" OR 
    if ($fjbtid=="38" OR $fjbtid=="33" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
        
        $pnregion="";
        if ($fkaryawan=="0000000159") $pnregion="T";
        elseif ($fkaryawan=="0000000158") $pnregion="B";
        $pfilterkry=CariDataKaryawanByCabJbt($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
        
    }elseif ($fjbtid=="38x" OR $fjbtid=="33x") {
        $pnregion="";
        $pfilterkry=CariDataKaryawanByRsmAuthCNIT($fkaryawan, $fjbtid, $pnregion);
        
        if (!empty($pfilterkry)) {
            $parry_kry= explode(" | ", $pfilterkry);
            if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
            if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
        }
    }
    
    if ($fgroupid=="24") {
        $pfilterkaryawan="";
        $pfilterkaryawan2="";
    }
    
    //echo "karyawan : $pfilterkaryawan<br/>karyawan2 : $pfilterkaryawan2<br/>";exit;

    
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Daftar Apotik";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/ks_dataapotik/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var eaksi = "module/ks_dataapotik/aksi_dataapotik.php";
                        var ekryid=document.getElementById('cb_karyawan').value;
                        var eidpilihkry=document.getElementById('e_idkaryawanpilih').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/ks_dataapotik/viewdatatabel_ksapt.php?module=viewdata",
                            data:"ukryid="+ekryid+"&uidpilihkry="+eidpilihkry+"&uaksi="+eaksi,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div class='col-sm-2'>
                                Karyawan (MR) 
                                <div class="form-group">
                                    
                                  <input type='hidden' id='e_idkaryawanpilih' name='e_idkaryawanpilih' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pfilterkaryawan2; ?>' >
                                  <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="" data-live-search="true">
                                      <?PHP 
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            
                                            
                                            $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan
                                                WHERE 1=1 ";
                                            if (!empty($pfilterkaryawan)) {
                                                $query .= " AND karyawanid IN $pfilterkaryawan ";
                                            }else{
                                                $query .=" AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                            }
                                            $query .= " ORDER BY nama";
                                                
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pkaryid=$z['karyawanid'];
                                                $pkarynm=$z['nama'];
                                                $pkryid=(INT)$pkaryid;
                                                if ($pkaryid==$pidkaryawan)
                                                    echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                else
                                                    echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                            }

                                      ?>
                                  </select>
                                    
                                </div>
                            </div>

                            <div class='col-sm-5'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
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
                include "tambahdtapt.php";
            break;

            case "editdata":
                include "tambahdtapt.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

