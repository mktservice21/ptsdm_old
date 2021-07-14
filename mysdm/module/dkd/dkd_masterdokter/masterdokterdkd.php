<?PHP 
    date_default_timezone_set('Asia/Jakarta');
    include "config/cek_akses_modul.php"; 
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];

?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Master Dokter DKU";
                if ($pact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h3>
            
        </div>
        
    </div>
    <div class="clearfix"></div>

    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        $pidkaryawan=$_SESSION['IDCARD'];
        $pidjabatan=$_SESSION['JABATANID'];
        $pidgroup=$_SESSION['GROUP'];
        
         
        $pcabid_pl=$_SESSION['DKDMSTDOKTCAB'];
        $pjbtid_pl=$_SESSION['DKDMSTDOKTJBT'];
        $pkryid_pl=$_SESSION['DKDMSTDOKTKRY'];
        $ptgl1_pl=$_SESSION['DKDMSTDOKTTGL'];

        $pseljbt0="";
        $pseljbt1="";
        $pseljbt2="";
        $pseljbt3="";
        $pseljbt4="";
        $pseljbt5="";

        if (empty($pjbtid_pl)) $pseljbt0="selected";
        else{
            if ($pjbtid_pl=="05") $pseljbt1="selected";
            elseif ($pjbtid_pl=="20") $pseljbt2="selected";
            elseif ($pjbtid_pl=="08") $pseljbt3="selected";
            elseif ($pjbtid_pl=="10") $pseljbt4="selected";
            elseif ($pjbtid_pl=="15") $pseljbt5="selected";
        }
        
        $aksi="eksekusi3.php";
        switch($pact){
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
                        var eaksi = "module/dkd/dkd_masterdokter/aksi_masterdokterdkd.php";
                        var ecabid=document.getElementById('cb_cabang').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/dkd/dkd_masterdokter/viewdatatabledoktdkd.php?module=viewdata",
                            data:"ucabid="+ecabid+"&uaksi="+eaksi,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>

                <?PHP
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                ?>

                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        
                        <div class='col-sm-2'>
                            Cabang
                            <div class="form-group">
                                <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="">
                                    <?PHP
                                        if ($pidgroup=="1" OR $pidgroup=="24") {
                                            $query = "select iCabangId as icabangid, nama as nama_cabang from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
                                            $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                            $query .=" order by nama, iCabangId";
                                        }else{
                                            if ($pidjabatan=="10" OR $pidjabatan=="18") {
                                                $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                    FROM mkt.ispv0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                    WHERE a.karyawanid='$pidkaryawan'";
                                                    $query .=" order by b.nama, a.icabangid";
                                            }elseif ($pidjabatan=="08") {
                                                $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                    FROM mkt.idm0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                    WHERE a.karyawanid='$pidkaryawan'";
                                                    $query .=" order by b.nama, a.icabangid";
                                            }elseif ($pidjabatan=="20") {
                                                $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                    FROM mkt.ism0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                    WHERE a.karyawanid='$pidkaryawan'";
                                                    $query .=" order by b.nama, a.icabangid";
                                            }elseif ($pidjabatan=="05") {
                                                $pfregion="XXX";
                                                if ((INT)$pidkaryawan==158) $pfregion="B";
                                                elseif ((INT)$pidkaryawan==159) $pfregion="T";

                                                $query = "select icabangid as icabangid, nama as nama_cabang from 
                                                    MKT.icabang WHERE region='$pfregion' AND IFNULL(aktif,'')<>'N' ";
                                                $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -') ";
                                                $query .=" order by nama, icabangid";
                                            }else{
                                                $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                    FROM mkt.imr0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                    WHERE a.karyawanid='$pidkaryawan'";
                                                $query .=" order by b.nama, a.icabangid";
                                            }
                                        }
                                        $tampilket= mysqli_query($cnmy, $query);
                                        $ketemu=mysqli_num_rows($tampilket);
                                        //if ((INT)$ketemu<=0) 
                                        echo "<option value='' selected>-- Pilih --</option>";
                                        
                                        while ($du= mysqli_fetch_array($tampilket)) {
                                            $nidcab=$du['icabangid'];
                                            $nnmcab=$du['nama_cabang'];
                                            $nidcab_=(INT)$nidcab;
                                            if ($nidcab==$pcabid_pl)
                                                echo "<option value='$nidcab' selected>$nnmcab ($nidcab_)</option>";
                                            else
                                                echo "<option value='$nidcab'>$nnmcab ($nidcab_)</option>";

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



                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>



                    </div>
                </div>

                
                <?PHP
            break;

            case "tambahbaru":
                include "tambah_dr.php";
            break;

            case "editdata":
                include "tambah_dr.php";
            break;

        }
        ?>
    </div>
    <!--end row-->
</div>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>
        
<script>
    // SCROLL
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
        } else {
            document.getElementById("myBtn").style.display = "none";
        }
    }
    
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    // END SCROLL

</script>