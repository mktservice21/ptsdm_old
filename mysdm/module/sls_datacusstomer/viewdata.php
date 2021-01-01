<?php
session_status();

if ($_GET['module']=="caridataarea") {
    include "../../config/koneksimysqli.php";
    $pcabangidpl=$_POST['ucabang'];
    
    echo "<option value=''>--All--</option>";
    if (!empty($pcabangidpl)) {
        $query_area="SELECT * from MKT.iarea where icabangid='$pcabangidpl' ";
        $query_ak =$query_area." AND IFNULL(aktif,'')='Y' ";
        $query_ak .=" order by nama";
        $tampil= mysqli_query($cnmy, $query_ak);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidarea=$row['areaId'];
            $pnmarea=$row['Nama'];
            echo "<option value='$pidarea'>$pnmarea</option>";
        }
        


        $query_non =$query_area." AND IFNULL(aktif,'')<>'Y' ";
        $query_non .=" order by nama";
        $tampil= mysqli_query($cnmy, $query_non);
        $ketemunon= mysqli_num_rows($tampil);
        if ($ketemunon>0) {
            echo "<option value='NONAKTIF'>-- Non Aktif--</option>";
            while ($row= mysqli_fetch_array($tampil)) {
                $pidarea=$row['areaId'];
                $pnmarea=$row['Nama'];
                echo "<option value='$pidarea'>$pnmarea</option>";
            }
        }
    }
    
    mysqli_close($cnmy);
    
}


?>
