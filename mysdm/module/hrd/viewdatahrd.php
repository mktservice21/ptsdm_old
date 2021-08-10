<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if (!isset($_SESSION['IDCARD'])) {
    echo "HARUS LOGIN ULANG...";
    exit;
}
        
if ($pmodule=="carikaryawanbyatasan") {
        
    include "../../config/koneksimysqli.php";
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fnmkaryawan=$_SESSION['NAMALENGKAP'];
    $fgroupid=$_SESSION['GROUP'];

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
    
    
    
    $pidatasan=$_POST['uidatasan'];
    
    
    $query = "select a.karyawanId as karyawanid, a.nama as nama_karyawan From hrd.karyawan as a "
            . " JOIN dbmaster.t_karyawan_posisi as b on a.karyawanId=b.karyawanId "
            . " WHERE 1=1 ";
    $query .= " AND ( IFNULL(a.tglkeluar,'')='' OR IFNULL(a.tglkeluar,'0000-00-00')='0000-00-00' ) ";
    $query .= " AND IFNULL(b.`ho`,'')='Y' ";
    if (!empty($pidatasan)) {
        $query .= " AND ( a.atasanId='$pidatasan' OR a.atasanId2='$pidatasan' OR a.karyawanId='$pidatasan' ) ";
    }else{
        if ($pbolehbukall==true) {
        }else{
            if ($pleader==true OR $patasantanpaleader==true) {
                $query .= " AND (a.karyawanId='$fkaryawan' OR a.atasanId='$fkaryawan' OR a.atasanId2='$fkaryawan' ) ";
            }else{
                $query .= " AND a.karyawanId='$fkaryawan'";
            }
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
                                
                                                            
    mysqli_close($cnmy);
}

?>