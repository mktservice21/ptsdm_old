<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="carikaryawanbyatasan") {
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fgroupid=$_SESSION['GROUP'];
        
    include "../../config/koneksimysqli.php";
    
    $pidatasan=$_POST['uidatasan'];
    
    $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan
        WHERE 1=1 ";
    $query .= " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' ) ";
    if (!empty($pidatasan)) {
        $query .= " AND nama NOT IN ('ACCOUNTING') AND karyawanId NOT IN ('0000002200', '0000002083')";
        $query .= " AND ( atasanId='$pidatasan' OR atasanId2='$pidatasan' OR karyawanId='$pidatasan' ) ";
    }else{
        if ($fgroupid=="24" OR $fgroupid=="1" OR $fgroupid=="57" OR $fgroupid=="47" OR $fgroupid=="29" OR $fgroupid=="46") {
            $query .= " AND nama NOT IN ('ACCOUNTING') AND karyawanId NOT IN ('0000002200', '0000002083')";
        }else{
            $query .= " AND karyawanId='$fkaryawan'";
        }
    }
    
    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
            . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
            . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
            . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
            . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
    
    $query .= " AND karyawanId IN (select DISTINCT IFNULL(karyawanId,'') FROM dbmaster.t_karyawan_posisi WHERE IFNULL(`ho`,'')='Y')";
    
    $query .= " ORDER BY nama";


    $tampil = mysqli_query($cnmy, $query);

    $ketemu= mysqli_num_rows($tampil);
    
    if ($ketemu==0) echo "<option value='' selected>-- Pilih --</option>";
    
    while ($z= mysqli_fetch_array($tampil)) {
        $pkaryid=$z['karyawanid'];
        $pkarynm=$z['nama'];
        $pkryid=(INT)$pkaryid;
        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
    }
                                                            
    mysqli_close($cnmy);
}

?>