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
                $judul="Realisasi Weekly Plan";
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
        
        $pfilterkaryawan="";
        $pfilterkaryawan2="";
        $pfilterkry="";
        //$pidjabatan=="38" OR $pidjabatan=="33" OR 
        if ($pidjabatan=="38" OR $pidjabatan=="33" OR $pidjabatan=="05" OR $pidjabatan=="20" OR $pidjabatan=="08" OR $pidjabatan=="10" OR $pidjabatan=="18" OR $pidjabatan=="15") {

            $pnregion="";
            if ($pidkaryawan=="0000000159") $pnregion="T";
            elseif ($pidkaryawan=="0000000158") $pnregion="B";
            $pfilterkry=CariDataKaryawanByCabJbt($pidkaryawan, $pidjabatan, $pnregion);

            if (!empty($pfilterkry)) {
                $parry_kry= explode(" | ", $pfilterkry);
                if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
                if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
            }

        }


         
        $pcabid_pl=$_SESSION['WEKPLNRLCAB'];
        $pjbtid_pl=$_SESSION['WEKPLNRLJBT'];
        $pkryid_pl=$_SESSION['WEKPLNRLKRY'];
        $ptgl1_pl=$_SESSION['WEKPLNRLTGL'];

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
                        var etgl1=document.getElementById('e_tanggal').value;
                        var etgl2=document.getElementById('e_tanggal').value;
                        var ekryid=document.getElementById('cb_karyawan').value;
                        var ecabid=document.getElementById('cb_cabang').value;
                        var ejbtid=document.getElementById('cb_jabatan').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/dkd/dkd_realwekplan/viewdatatabelerealplan.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"utgl1="+etgl1+"&utgl2="+etgl2+"&ukryid="+ekryid+"&ucabid="+ecabid+"&ujbtid="+ejbtid,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function ShowDataKaryawan() {
                        //cb_cabang,cb_jabatan
                        var eidcan =document.getElementById('cb_cabang').value;
                        var eidjbt =document.getElementById('cb_jabatan').value;
        
                        $.ajax({
                            type:"post",
                            url:"module/dkd/viewdatadkd.php?module=viewdatakaryawancabjbt",
                            data:"uidcab="+eidcan+"&uidjbt="+eidjbt,
                            success:function(data){
                                $("#cb_karyawan").html(data);
                            }
                        });
                    }
                </script>

                <script type="text/javascript">
                    $(function() {

                        $('#e_tanggal').datepicker({
                            changeMonth: true,
                            changeYear: true,
                            numberOfMonths: 1,
                            dateFormat: 'dd MM yy',
                            onSelect: function(dateStr) {
                                
                            },
                            beforeShowDay: function (date) {
                                var day = date.getDay();
                                return [(day == 1)];
                            }
                        });

                    });
                </script>

                <?PHP
                //echo date('Y-m-d',time()+( 8 - date('w'))*24*3600);
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('t F Y', strtotime($hari_ini));

                $tgl_pertama = date('d F Y',time()+( 8 - date('w'))*24*3600);

                if (!empty($ptgl1_pl)) $tgl_pertama=$ptgl1_pl;
                ?>

                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Extra Call'
                                onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        
                        <div class='col-sm-2'>
                            Cabang
                            <div class="form-group">
                                <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="ShowDataKaryawan()">
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
                            Jabatan
                            <div class="form-group">
                                <select class='form-control' name='cb_jabatan' id='cb_jabatan' onchange="ShowDataKaryawan()">
                                    <?PHP
                                    echo "<option value='' $pseljbt0>--All--</option>";
                                    if ($pidjabatan=="15") {
                                        echo "<option value='15' $pseljbt5>MR</option>";
                                    }elseif ($pidjabatan=="10" OR $pidjabatan=="18") {
                                        echo "<option value='10' $pseljbt4>AM/SPV</option>";
                                        echo "<option value='15' $pseljbt5>MR</option>";
                                    }elseif ($pidjabatan=="08") {
                                        echo "<option value='08' $pseljbt3>DM</option>";
                                        echo "<option value='10' $pseljbt4>AM/SPV</option>";
                                        echo "<option value='15' $pseljbt5>MR</option>";
                                    }elseif ($pidjabatan=="20") {
                                        echo "<option value='20' $pseljbt2>SM</option>";
                                        echo "<option value='08' $pseljbt3>DM</option>";
                                        echo "<option value='10' $pseljbt4>AM/SPV</option>";
                                        echo "<option value='15' $pseljbt5>MR</option>";
                                    }else{
                                        echo "<option value='05' $pseljbt1>GSM</option>";
                                        echo "<option value='20' $pseljbt2>SM</option>";
                                        echo "<option value='08' $pseljbt3>DM</option>";
                                        echo "<option value='10' $pseljbt4>AM/SPV</option>";
                                        echo "<option value='15' $pseljbt5>MR</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>


                        <div class='col-sm-3'>
                            Karyawan
                            <div class="form-group">
                                <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                    <?PHP
                                        if ($pidgroup=="1" OR $pidgroup=="24") {
                                            $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                FROM hrd.karyawan WHERE 1=1 ";
                                            $query_kry .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                            $query_kry .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                            $query_kry .=" ORDER BY nama";
                                        }else{
                                            $query_kry = "select karyawanId as karyawanid, nama as nama 
                                                FROM hrd.karyawan WHERE karyawanId IN $pfilterkaryawan ";
                                                
                                            $query_kry .=" ORDER BY nama";
                                        }

                                        if (!empty($query_kry)) {
                                            $tampil = mysqli_query($cnmy, $query_kry);
                                            $ketemu= mysqli_num_rows($tampil);
                                            if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pkaryid=$z['karyawanid'];
                                                $pkarynm=$z['nama'];
                                                $pkryid=(INT)$pkaryid;

                                                if ($pkaryid==$pkryid_pl) {
                                                    echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                    $pidkaryawan=$pkryid_pl;
                                                }else{
                                                    if ($pkaryid==$pidkaryawan)
                                                        echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                    else
                                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                }
                                            }
                                        }else{
                                            echo "<option value='' selected>-- Pilih --</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        

                        <div class='col-sm-3'>
                            Tanggal 
                            <div class='input-group date' id=''>
                                <input type="text" class="form-control" id='e_tanggal' name='e_tanggal' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-calendar'></span>
                                </span>
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
                include "tambah_ec.php";
            break;

            case "editdata":
                include "tambah_ec.php";
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