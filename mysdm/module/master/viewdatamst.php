<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridataarea") {

    include "../../config/koneksimysqli_ms.php";

    $pcabangidpl=$_POST['ucabang'];
    
    echo "<option value=''>--All--</option>";
    if (!empty($pcabangidpl)) {
        $query_area="SELECT areaid as areaid, nama as nama from MKT.iarea where icabangid='$pcabangidpl' ";
        $query_ak =$query_area." AND IFNULL(aktif,'')='Y' ";
        $query_ak .=" order by nama";
        $tampil= mysqli_query($cnms, $query_ak);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidarea=$row['areaid'];
            $pnmarea=$row['nama'];
            $pintidarea=(INT)$pidarea;
            echo "<option value='$pidarea'>$pnmarea ($pintidarea)</option>";
        }
        


        $query_non =$query_area." AND IFNULL(aktif,'')<>'Y' ";
        $query_non .=" order by nama";
        $tampil= mysqli_query($cnms, $query_non);
        $ketemunon= mysqli_num_rows($tampil);
        if ($ketemunon>0) {
            echo "<option value='NONAKTIF'>-- Non Aktif--</option>";
            while ($row= mysqli_fetch_array($tampil)) {
                $pidarea=$row['areaid'];
                $pnmarea=$row['nama'];
                $pintidarea=(INT)$pidarea;
                echo "<option value='$pidarea'>$pnmarea ($pintidarea)</option>";
            }
        }
    }

    mysqli_close($cnms);

}

?>