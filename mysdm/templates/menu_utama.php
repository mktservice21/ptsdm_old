<?php

$pbukamenuloginnya=true;
$psudhpernahupdatepass="";
if (isset($_SESSION['SUDAHUPDATEPASS'])) $psudhpernahupdatepass=$_SESSION['SUDAHUPDATEPASS'];
//$psudhpernahupdatepass="Y";//hilangkan
if ($psudhpernahupdatepass=="Y") {
    //include "menu.php";
    //$pbukamenuloginnya=false;
}
    
?>

<?PHP



if ($psudhpernahupdatepass=="Y" && $pbukamenuloginnya==true) {
    
    $pidcard_menu="";
    if (isset($_SESSION['IDCARD'])) $pidcard_menu=$_SESSION['IDCARD'];


    if (!empty($pidcard_menu)) {


        //menu not in 
        $pmenu_notin_menu="";
        $pmenu_notin_sub="";
        if ($pidcard_menu=="0000000159") {
            $pmenu_notin_menu=" ('521') ";
        }

        $pgroupid_menu=$_SESSION['GROUP'];
        $pmobile_menu=$_SESSION['MOBILE'];
        $pwebmarvis2_menu="http://ms2.marvis.id";

        if (!empty($pgroupid_menu)) {
            //$pidcard_menu="0000002073";
            //cek menu tambahan
            $fidutamamenu="";
            $fidmenutambah="";
            $query = "select karyawanid as karyawanid, igroup as igroup from dbmaster.t_karyawan_menu WHERE karyawanid='$pidcard_menu'";
            $tampiltm=mysqli_query($cnmy, $query);
            $ketemutm=mysqli_num_rows($tampiltm);
            if ((INT)$ketemutm>0) {
                $rtm= mysqli_fetch_array($tampiltm);
                $pigroupmenutm=$rtm['igroup'];

                $query = "select DISTINCT b.PARENT_ID, a.igroup as igroup, a.`id` as idmenu from dbmaster.t_karyawan_menu_d as a JOIN dbmaster.sdm_menu as b on a.`id`=b.`ID` WHERE a.igroup='$pigroupmenutm'";
                $tampiltmg=mysqli_query($cnmy, $query);
                $ketemutmg=mysqli_num_rows($tampiltmg);
                if ((INT)$ketemutmg>0) {
                    $_SESSION['MENUTAMBAHGRP']=$pigroupmenutm;
                    while ($itm= mysqli_fetch_array($tampiltmg)) {
                        $nidparent=$itm['PARENT_ID'];
                        $nidmenutm=$itm['idmenu'];

                        if (strpos($fidutamamenu, $nidparent)==false AND !empty($nidmenutm)) $fidutamamenu .="'".$nidparent."',";
                        if (strpos($fidmenutambah, $nidmenutm)==false) $fidmenutambah .="'".$nidmenutm."',";
                    }

                    if (!empty($fidutamamenu)) $fidutamamenu="(".substr($fidutamamenu, 0, -1).")";
                    if (!empty($fidmenutambah)) $fidmenutambah="(".substr($fidmenutambah, 0, -1).")";
                }

            }

            //echo "$fidutamamenu dan $fidmenutambah";


            $query_utama_tanpa_tambahan = "select DISTINCT a.`ID`, b.PARENT_ID, b.JUDUL, b.URUTAN, b.GAMBAR, b.KRITERIA "
                    . " from dbmaster.sdm_groupmenu as a "
                    . " JOIN dbmaster.sdm_menu as b on a.`ID`=b.`ID` "
                    . " WHERE b.PARENT_ID='0' AND a.ID_GROUP='$pgroupid_menu' ORDER BY b.URUTAN, b.`ID`";

                            $query_sub_tanpa_tambahan = "select DISTINCT a.`ID`, b.PARENT_ID, b.JUDUL, b.URUTAN, b.GAMBAR, b.KRITERIA, b.URL "
                                    . " from dbmaster.sdm_groupmenu as a "
                                    . " JOIN dbmaster.sdm_menu as b on a.`ID`=b.`ID` "
                                    . " WHERE b.PARENT_ID<>'0' AND a.ID_GROUP='$pgroupid_menu' AND b.PARENT_ID='' ";
                            $query_sub_tanpa_tambahan .=" ORDER BY b.URUTAN, b.`ID`";






            $query = "select DISTINCT a.`ID`, a.PARENT_ID, a.JUDUL, a.URUTAN, a.GAMBAR, a.KRITERIA "
                    . " from dbmaster.sdm_menu as a "
                    . " LEFT JOIN dbmaster.sdm_groupmenu as b on a.`ID`=b.`ID` "
                    . " WHERE a.PARENT_ID='0' AND  ";
            if (!empty($fidutamamenu)) {
                $query .=" ( b.ID_GROUP='$pgroupid_menu' OR a.`ID` IN  $fidutamamenu) ";
            }else{
                $query .=" b.ID_GROUP='$pgroupid_menu' ";
            }

            if (!empty($pmenu_notin_menu)) $query .=" AND a.`ID` NOT IN $pmenu_notin_menu ";

            $query .=" ORDER BY a.URUTAN, a.`ID`";

            $tampil=mysqli_query($cnmy, $query);
            $ketemu=mysqli_num_rows($tampil);
            if ($ketemu>0){

                echo "<ul class='nav side-menu'>";

                    while ($row= mysqli_fetch_array($tampil)) {
                        $pidmenu=$row['ID'];
                        $pjudulmenu=$row['JUDUL'];
                        $pgbrmenu=$row['GAMBAR'];

                        $gbr="fa fa-desktop";
                        if (!empty($pgbrmenu)) $gbr=$pgbrmenu;
                        
                        echo "<li><a><i class='fa $gbr'></i> $pjudulmenu <span class='fa fa-chevron-down'></span></a>";

                            $query = "select DISTINCT a.`ID`, a.PARENT_ID, a.JUDUL, a.URUTAN, a.GAMBAR, a.KRITERIA, a.URL "
                                    . " from dbmaster.sdm_menu as a "
                                    . " JOIN dbmaster.sdm_groupmenu as b on a.`ID`=b.`ID` "
                                    . " WHERE a.PARENT_ID<>'0' AND ";
                            if (!empty($fidmenutambah)) {
                                $query .=" a.PARENT_ID='$pidmenu' AND ( b.ID_GROUP='$pgroupid_menu' OR a.`ID` IN $fidmenutambah) ";
                            }else{
                                $query .=" a.PARENT_ID='$pidmenu' AND b.ID_GROUP='$pgroupid_menu' ";
                            }

                            if (!empty($pmenu_notin_sub)) $query .=" AND a.`ID` NOT IN $pmenu_notin_sub ";
                            
                            if ($pmobile_menu=="N") $query .=" AND IFNULL(a.`S_MENU`,'')='' ";

                            $query .=" ORDER BY a.URUTAN, a.`ID`";

                            $tampil2=mysqli_query($cnmy, $query);
                            $ketemu2=mysqli_num_rows($tampil2);
                            
                            echo "<ul class='nav child_menu'>";
                                if ($ketemu2>0){

                                    while ($row2= mysqli_fetch_array($tampil2)) {
                                        $pid_menusub=$row2['ID'];
                                        $pjudul_menusub=$row2['JUDUL'];
                                        $purl_menusub=$row2['URL'];
                                        $pkriteria_menusub=$row2['KRITERIA'];

                                        $purlms2="";
                                        if ($pkriteria_menusub=="N" AND !empty($purl_menusub)) {
                                            $purlms2= str_replace("?module=", "", $purl_menusub);
                                        }

                                        if ($pkriteria_menusub=="N" AND !empty($purlms2)) 
                                            $plinkmodule=$pwebmarvis2_menu."/".$purlms2;
                                        else
                                            $plinkmodule=$purl_menusub."&idmenu=".$pid_menusub."&act=".$pid_menusub."&kriteria=".$pkriteria_menusub;

                                        echo "<li><a href='$plinkmodule'> <span>$pjudul_menusub</span></a></li>";

                                    }

                                }
                            
                                if ($pmobile_menu=="N") {

                                    $query = "select DISTINCT a.`S_MENU` from dbmaster.sdm_menu as a "
                                            . " JOIN dbmaster.sdm_groupmenu as b on a.`ID`=b.`ID` "
                                            . " WHERE a.PARENT_ID<>'0' AND IFNULL(a.`S_MENU`,'')<>'' AND ";
                                    if (!empty($fidmenutambah)) {
                                        $query .=" a.PARENT_ID='$pidmenu' AND ( b.ID_GROUP='$pgroupid_menu' OR a.`ID` IN $fidmenutambah) ";
                                    }else{
                                        $query .=" a.PARENT_ID='$pidmenu' AND b.ID_GROUP='$pgroupid_menu' ";
                                    }
                                    if (!empty($pmenu_notin_sub)) $query .=" AND a.`ID` NOT IN $pmenu_notin_sub ";
                                    $query .=" ORDER BY a.`S_MENU`, a.URUTAN, a.`ID`";

                                    $tampil3=mysqli_query($cnmy, $query);
                                    $ketemu3=mysqli_num_rows($tampil3);
                                    if ($ketemu3>0){
                                        while ($row3= mysqli_fetch_array($tampil3)) {
                                            $msmenu=$row3['S_MENU'];
                                            $msnmmenu=$msmenu;
                                            if ($msmenu=="CA") $msnmmenu="Cash Advance";
                                            if ($msmenu=="RUTIN") $msnmmenu="Biaya Rutin";
                                            if ($msmenu=="LK") $msnmmenu="Biaya Luar Kota";

                                            echo "<li><a><i class='fa fa-sitemap'></i> $msnmmenu <span class='fa fa-chevron-down'></span></a>";
                                            
                                                $query = "select DISTINCT a.`ID`, a.PARENT_ID, a.JUDUL, a.URUTAN, a.GAMBAR, a.KRITERIA, a.URL "
                                                        . " from dbmaster.sdm_menu as a "
                                                        . " JOIN dbmaster.sdm_groupmenu as b on a.`ID`=b.`ID` "
                                                        . " WHERE a.PARENT_ID<>'0' AND ";
                                                if (!empty($fidmenutambah)) {
                                                    $query .=" a.PARENT_ID='$pidmenu' AND ( b.ID_GROUP='$pgroupid_menu' OR a.`ID` IN $fidmenutambah) ";
                                                }else{
                                                    $query .=" a.PARENT_ID='$pidmenu' AND b.ID_GROUP='$pgroupid_menu' ";
                                                }

                                                if (!empty($pmenu_notin_sub)) $query .=" AND a.`ID` NOT IN $pmenu_notin_sub ";

                                                $query .=" AND IFNULL(a.`S_MENU`,'')='$msmenu' ";

                                                $query .=" ORDER BY a.URUTAN, a.`ID`";

                                                $tampil2=mysqli_query($cnmy, $query);
                                                $ketemu2=mysqli_num_rows($tampil2);
                                                if ($ketemu2>0){
                                                    echo "<ul class='nav child_menu'>";

                                                        while ($row2= mysqli_fetch_array($tampil2)) {
                                                            $pid_menusub=$row2['ID'];
                                                            $pjudul_menusub=$row2['JUDUL'];
                                                            $purl_menusub=$row2['URL'];
                                                            $pkriteria_menusub=$row2['KRITERIA'];

                                                            $purlms2="";
                                                            if ($pkriteria_menusub=="N" AND !empty($purl_menusub)) {
                                                                $purlms2= str_replace("?module=", "", $purl_menusub);
                                                            }

                                                            if ($pkriteria_menusub=="N" AND !empty($purlms2)) 
                                                                $plinkmodule=$pwebmarvis2_menu."/".$purlms2;
                                                            else
                                                                $plinkmodule=$purl_menusub."&idmenu=".$pid_menusub."&act=".$pid_menusub."&kriteria=".$pkriteria_menusub;

                                                            echo "<li><a href='$plinkmodule'> <span>$pjudul_menusub</span></a></li>";

                                                        }
                                    
                                                    echo "</ul>";
                                                }
                                                
                                            echo "</li>";
                                        }
                                    }

                                }
                                
                            echo "</ul>";
                            
                        echo "</li>";

                    }

                echo "</ul>";

            }





        }

    }


}


?>

