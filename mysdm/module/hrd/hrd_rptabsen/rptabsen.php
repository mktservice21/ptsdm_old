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

        $pfilterkaryawan="";
        $pfilterkaryawan2="";
        $pfilterkry="";
?>

        <script>
            function disp_confirm(pText)  {
                var eidkry =document.getElementById('cb_karyawan').value;

                if (eidkry=="") {
                    //alert("karyawan harus diisi...!!!");
                    //return false;
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

            <div class="page-title"><div class="title_left"><h3>Report Summary Absensi</h3></div></div><div class="clearfix"></div>

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
                                                <b>Karyawan</b>
                                                <div class="form-group">
                                                    <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                                        <?PHP
                                                            $query = "select karyawanId, nama From hrd.karyawan
                                                                WHERE 1=1 ";
                                                            if ($fgroupid=="24" OR $fgroupid=="1" OR $fgroupid=="57" OR $fgroupid=="47" OR $fgroupid=="29" OR $fgroupid=="46") {
                                                                echo "<option value='' selected>-- All --</option>";
                                                                $query .= " AND nama NOT IN ('ACCOUNTING') AND karyawanId NOT IN ('0000002200', '0000002083')";
                                                            }else{
                                                                $query .= " AND karyawanId='$fkaryawan'";
                                                            }
                                                            $query .= " AND karyawanId IN (select DISTINCT IFNULL(karyawanId,'') FROM dbmaster.t_karyawan_posisi WHERE IFNULL(`ho`,'')='Y')";
                                                            $query .= " ORDER BY nama";


                                                            $tampil = mysqli_query($cnmy, $query);

                                                            $ketemu= mysqli_num_rows($tampil);

                                                            while ($z= mysqli_fetch_array($tampil)) {
                                                                $pkaryid=$z['karyawanId'];
                                                                $pkarynm=$z['nama'];
                                                                $pkryid=(INT)$pkaryid;
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