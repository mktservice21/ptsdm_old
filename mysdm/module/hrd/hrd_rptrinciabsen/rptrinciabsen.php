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
        $fnmkaryawan=$_SESSION['NAMALENGKAP'];
        $fjbtid=$_SESSION['JABATANID'];
        $fgroupid=$_SESSION['GROUP'];
        $fstsadmin=$_SESSION['STSADMIN'];
        $flvlposisi=$_SESSION['LVLPOSISI'];
        $fdivisi=$_SESSION['DIVISI'];
        $ppilihancabang="";

        $pfilterkaryawan="";
        $pfilterkaryawan2="";
        $pfilterkry="";
        
        $pleader=false;
        $patasantanpaleader=false;
        $query = "select karyawanId, leader from dbmaster.t_karyawan_posisi WHERE karyawanId='$fkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            $row= mysqli_fetch_array($tampil);
            $nldr_=$row['leader'];
            
            if ($nldr_=="Y") {
                $pleader=true;
                $patasantanpaleader=true;
            }else{
                $query_a = "select karyawanId FROM hrd.karyawan WHERE ( atasanId='$fkaryawan' OR atasanId2='$fkaryawan' ) "
                        . " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' ) "
                        . " AND IFNULL(aktif,'')<>'N'";
                $tampil_a= mysqli_query($cnmy, $query_a);
                $ketemu_a=mysqli_num_rows($tampil_a);
                if ((INT)$ketemu_a>0) {
                    $patasantanpaleader=true;
                }
            }
        }
        
        $pbolehbukall=false;
        if ($fgroupid=="24" OR $fgroupid=="1" OR $fgroupid=="X57" OR $fgroupid=="47" OR $fgroupid=="29" OR $fgroupid=="46") {
            $pbolehbukall=true;
        }
?>

        <script>
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
            
            function ShowDataKaryawanByAtasan() {
                var eidatasan =document.getElementById('cb_atasan').value;
                $.ajax({
                    type:"post",
                    url:"module/hrd/viewdatahrd.php?module=carikaryawanbyatasan",
                    data:"uidatasan="+eidatasan,
                    success:function(data){
                        $("#cb_karyawan").html(data);
                    }
                });
            }
        </script>


        <div class="">

            <div class="page-title"><div class="title_left"><h3>Report Detail Absensi</h3></div></div><div class="clearfix"></div>

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
                                                <b>Atasan</b>
                                                <div class="form-group">
                                                    <select class='form-control' id="cb_atasan" name="cb_atasan" onchange="ShowDataKaryawanByAtasan()">
                                                        <?PHP
                                                            $query = "select a.karyawanId as karyawanid, a.nama as nama_karyawan From hrd.karyawan as a "
                                                                    . " JOIN dbmaster.t_karyawan_posisi as b on a.karyawanId=b.karyawanId "
                                                                    . " WHERE 1=1 ";
                                                            $query .= " AND ( IFNULL(a.tglkeluar,'')='' OR IFNULL(a.tglkeluar,'0000-00-00')='0000-00-00' ) ";
                                                            $query .= " AND IFNULL(b.`leader`,'')='Y' ";
                                                            if ($pbolehbukall==true) {
                                                            }else{
                                                                $query .= " AND a.karyawanId='$fkaryawan'";
                                                            }
                                                            $query .= " ORDER BY a.nama";

                                                            $tampil = mysqli_query($cnmy, $query);
                                                            $ketemu= mysqli_num_rows($tampil);
                                                            if ((INT)$ketemu<=0) {
                                                                if ($pleader==false AND $patasantanpaleader==true)
                                                                    echo "<option value='$fkaryawan' selected>$fnmkaryawan</option>";
                                                                else
                                                                    echo "<option value='' selected></option>";
                                                            }else
                                                                echo "<option value='' selected>-- All --</option>";
                                                            while ($z= mysqli_fetch_array($tampil)) {
                                                                $pkaryid=$z['karyawanid'];
                                                                $pkarynm=$z['nama_karyawan'];
                                                                $pkryid=(INT)$pkaryid;
                                                                if ((INT)$ketemu==1)
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
                                                <b>Karyawan</b>
                                                <div class="form-group">
                                                    <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                                        <?PHP
                                                            $query = "select a.karyawanId as karyawanid, a.nama as nama_karyawan From hrd.karyawan as a "
                                                                    . " JOIN dbmaster.t_karyawan_posisi as b on a.karyawanId=b.karyawanId "
                                                                    . " WHERE 1=1 ";
                                                            $query .= " AND ( IFNULL(a.tglkeluar,'')='' OR IFNULL(a.tglkeluar,'0000-00-00')='0000-00-00' ) ";
                                                            $query .= " AND IFNULL(b.`ho`,'')='Y' ";
                                                            if ($pbolehbukall==true) {
                                                            }else{
                                                                if ($pleader==true OR $patasantanpaleader==true) {
                                                                    $query .= " AND (a.karyawanId='$fkaryawan' OR a.atasanId='$fkaryawan' OR a.atasanId2='$fkaryawan' ) ";
                                                                }else{
                                                                    $query .= " AND a.karyawanId='$fkaryawan'";
                                                                }
                                                            }
                                                            $query .= " ORDER BY a.nama";


                                                            $tampil = mysqli_query($cnmy, $query);

                                                            $ketemu= mysqli_num_rows($tampil);

                                                            while ($z= mysqli_fetch_array($tampil)) {
                                                                $pkaryid=$z['karyawanid'];
                                                                $pkarynm=$z['nama_karyawan'];
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
                                                    <?PHP
                                                    if ($_SESSION['MOBILE']!="Y") {
                                                        echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
                                                    }
                                                    ?>
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