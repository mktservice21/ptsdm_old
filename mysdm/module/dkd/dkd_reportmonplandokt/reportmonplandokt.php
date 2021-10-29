<?PHP
include "config/cek_akses_modul.php";

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$pactpilih="";
if (isset($_GET['act'])) $pactpilih=$_GET['act'];
switch($pactpilih){
    default:

        $aksi="eksekusi3.php";
        $hari_ini = date("Y-m-d");
        $pbulanpilih = date('F Y', strtotime($hari_ini));

        $fkaryawan=$_SESSION['IDCARD'];
        $fjbtid=$_SESSION['JABATANID'];
        $fgroupid=$_SESSION['GROUP'];
        $fstsadmin=$_SESSION['STSADMIN'];
        $flvlposisi=$_SESSION['LVLPOSISI'];
        $fdivisi=$_SESSION['DIVISI'];
        $ppilihancabang="";
        
        $philangkan_nonkry=true;

        $pfilterkaryawan="";
        $pfilterkaryawan2="";
        $pfilterkry="";
        //$fjbtid=="38" OR $fjbtid=="33" OR 
        if ($fjbtid=="38" OR $fjbtid=="33" OR $fjbtid=="05" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {

            $pnregion="";
            if ($fkaryawan=="0000000159") $pnregion="T";
            elseif ($fkaryawan=="0000000158") $pnregion="B";
            $pfilterkry=CariDataKaryawanByCabJbt($fkaryawan, $fjbtid, $pnregion);

            if (!empty($pfilterkry)) {
                $parry_kry= explode(" | ", $pfilterkry);
                if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
                if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
            }

        }

        $pfiltercabpilih="";

        if ($fjbtid=="15" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="08" OR $fjbtid=="38" OR $fjbtid=="20" OR $fkaryawan=="0000000158" OR $fkaryawan=="0000000159") {
            if ($fkaryawan=="0000000158") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM MKT.icabang WHERE region='B'";
            }elseif ($fkaryawan=="0000000159") {
                $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM MKT.icabang WHERE region='T'";
            }else{
                if ($fjbtid=="15") {
                    $query_cab = "select distinct icabangid, areaid, divisiid FROM MKT.imr0 WHERE karyawanid='$fkaryawan'";
                }elseif ($fjbtid=="10" OR $fjbtid=="18") {
                    $query_cab = "select distinct icabangid, areaid, divisiid FROM MKT.ispv0 WHERE karyawanid='$fkaryawan'";
                }elseif ($fjbtid=="08") {
                    $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM MKT.idm0 WHERE karyawanid='$fkaryawan'";
                }elseif ($fjbtid=="20") {
                    $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM MKT.ism0 WHERE karyawanid='$fkaryawan'";
                }elseif ($fjbtid=="38") {
                    $query_cab = "select distinct icabangid, '' as areaid, '' as divisiid FROM hrd.rsm_auth WHERE karyawanid='$fkaryawan'";
                }
            }
            if (!empty($query_cab)) {
                $tampil= mysqli_query($cnmy, $query_cab);
                while ($rs= mysqli_fetch_array($tampil)) {
                    $vbicabangid=$rs['icabangid'];
                    $vbareaid=$rs['areaid'];
                    $vbdivisi=$rs['divisiid'];

                    if (!empty($vbicabangid)) $pidcabangpil=$vbicabangid;

                    if (strpos($pfiltercabpilih, $vbicabangid)==false) $pfiltercabpilih .="'".$vbicabangid."',";

                }
            }

        }

        if (!empty($pfiltercabpilih)) {
            $pfiltercabpilih="(".substr($pfiltercabpilih, 0, -1).")";
        }
?>

        <script>
            function ShowDataKaryawanCabang() {
                var eidcan =document.getElementById('cb_cabang').value;
                var eidjbt ="";

                $.ajax({
                    type:"post",
                    url:"module/dkd/viewdatadkd.php?module=viewdatakaryawancabjbt",
                    data:"uidcab="+eidcan+"&uidjbt="+eidjbt,
                    success:function(data){
                        $("#cb_karyawan").html(data);
                    }
                });
            }

            function disp_confirm(pText)  {
                var eidkry =document.getElementById('cb_karyawan').value;

                if (eidkry=="") {
                    alert("karyawan harus diisi...!!!");
                    return false;
                }

                if (pText == "excel") {
                    document.getElementById("data_form01").action = "<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu&ket=excel"; ?>";
                    document.getElementById("data_form01").submit();
                    return 1;
                }else{
                    document.getElementById("data_form01").action = "<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu&ket=bukan"; ?>";
                    document.getElementById("data_form01").submit();
                    return 1;
                }

            }
        </script>


        <div class="">

            <div class="page-title"><div class="title_left"><h3>Report Monthly Plan By User</h3></div></div><div class="clearfix"></div>

            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
                    id='data_form01' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />

                                        <div class='form-group'>
                                            <div class='col-sm-12'>
                                                <b>Cabang</b>
                                                <div class="form-group">
                                                    <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="ShowDataKaryawanCabang()">
                                                        <?PHP                                                  

                                                            $nojm=1;
                                                            $query_cb = "select icabangid as icabangid, nama as nama, "
                                                                    . " CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end as aktif "
                                                                    . " from MKT.icabang WHERE 1=1 ";
                                                            if ($fgroupid=="24" or $fgroupid=="1") {
                                                            }else{
                                                                if (!empty($pfiltercabpilih)) {
                                                                    $query_cb .=" AND iCabangId IN $pfiltercabpilih ";
                                                                }
                                                            }
                                                            $query_cb .=" AND LEFT(nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -')";
                                                            $query_cb .=" order by CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end desc, nama";
                                                            $tampil = mysqli_query($cnmy, $query_cb);

                                                            $ketemu= mysqli_num_rows($tampil);
                                                            echo "<option value='' selected>-- Pilih --</option>";
                                                            $pketaktif=false;
                                                            while ($z= mysqli_fetch_array($tampil)) {
                                                                $pcabid=$z['icabangid'];
                                                                $pcabnm=$z['nama'];
                                                                $pstsaktif=$z['aktif'];
                                                                $pcbid=(INT)$pcabid;

                                                                if ($pstsaktif=="N" AND $nojm<=1) { $pketaktif=true; $nojm++; }

                                                                if ($pketaktif==true) {
                                                                    echo "<option value=''>&nbsp;</option>";
                                                                    echo "<option value=''>-- non aktif --</option>";
                                                                    $pketaktif=false;
                                                                }
                                                                if ($fjbtid=="15" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="08") {
                                                                    echo "<option value='$pcabid' selected>$pcabnm ($pcbid)</option>";
                                                                    $pcabangselected=$pcabid;
                                                                }else {
                                                                    if ($pcabid==$ppilihancabang)
                                                                        echo "<option value='$pcabid' selected>$pcabnm ($pcbid)</option>";
                                                                    else
                                                                        echo "<option value='$pcabid'>$pcabnm ($pcbid)</option>";
                                                                }
                                                            }

                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <div class='col-sm-12'>
                                                <b>Karyawan</b>
                                                <div class="form-group">
                                                    <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                                        <?PHP
                                                            $query = "select karyawanId, nama From hrd.karyawan
                                                                WHERE 1=1 ";
                                                            if ($fgroupid=="24" or $fgroupid=="1") {
                                                                $query .= " AND nama NOT IN ('ACCOUNTING') AND karyawanId NOT IN ('0000002200', '0000002083')";
                                                            }else{
                                                                if (!empty($pfilterkaryawan)) {
                                                                    $query .= " AND karyawanId IN $pfilterkaryawan ";
                                                                }else{
                                                                    /*
                                                                    $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                                    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                            . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                            . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                            . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                            . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                                     * 
                                                                     */
                                                                    $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                                    $query .= " AND karyawanId NOT IN ('0000002200', '0000002083')";
                                                                }
                                                            }
                                                            if ($philangkan_nonkry==true) {
                                                                $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                            }
                                                            $query .= " ORDER BY nama";


                                                            $tampil = mysqli_query($cnmy, $query);

                                                            $ketemu= mysqli_num_rows($tampil);
                                                            if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

                                                            while ($z= mysqli_fetch_array($tampil)) {
                                                                $pkaryid=$z['karyawanId'];
                                                                $pkarynm=$z['nama'];
                                                                $pkryid=(INT)$pkaryid;
                                                                if ($pkaryid==$fkaryawan)
                                                                    echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                                else
                                                                    echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>



                                        <div class='form-group'>
                                            <div class='col-sm-12'>
                                                <b>Bulan</b>
                                                <div class="form-group">
                                                <div class='input-group date' id='cbln01'>
                                                    <input type="text" class="form-control" id='e_bulan' name='e_bulan' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulanpilih; ?>' Readonly>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <div class='col-sm-12'>
                                                <b>&nbsp;</b>
                                                <div class="form-group">
                                                <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                                </div>
                                            </div>
                                        </div>



                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                </form>

            </div>

        </div>


<?PHP
    break;

    case "input":
        include "tambah.php";
    break;
}
?>