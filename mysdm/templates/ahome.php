


<?PHP
    $np_divisi_h=$_SESSION['DIVISI'];
    $np_idgroup_h=$_SESSION['GROUP'];
    $np_idcard_h=$_SESSION['IDCARD'];
    
    $h_warna_p[1] = "small-box bg-green";
    $h_warna_p[2] = "small-box bg-aqua";
    $h_warna_p[3] = "small-box bg-yellow";
    $h_warna_p[4] = "small-box bg-red";
    $h_warna=$h_warna_p[1];
?>


<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>



<div class="">
    
    <?PHP
    $pmymoduleabs="";
    if (isset($_GET['module'])) $pmymoduleabs=$_GET['module'];
    if ($pmymoduleabs=="home") {
        
    }
    
    $pbolehabsen=false;
    $pkaryawanidcekabsen="";
    if (isset($_SESSION['IDCARD'])) $pkaryawanidcekabsen=$_SESSION['IDCARD'];
    $query_habs = "select karyawanid FROM hrd.karyawan_absen WHERE karyawanid='$pkaryawanidcekabsen'";
    $tampil_habs= mysqli_query($cnmy, $query_habs);
    $ketemu_habs= mysqli_num_rows($tampil_habs);
    if ((INT)$ketemu_habs>0) {
        $pbolehabsen=true;
    }
    
    if ($pbolehabsen==true) {
        echo "<div class='row top_tiles'>";
            include "module/hrd/hrd_absen/absen_masuk.php";
            include "module/hrd/hrd_absen/absen_istirahat.php";
            include "module/hrd/hrd_absen/absen_pulang.php";
        echo "</div>";
    }
    ?>

    

</div>

<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        
    <?PHP
    $jn_idmenu="";
    //if ($np_divisi_h!="OTC" AND ($np_idgroup_h=="3" OR $np_idgroup_h=="25" OR $np_idgroup_h=="1")) {
    if (($np_idgroup_h=="3" OR $np_idgroup_h=="25" OR $np_idgroup_h=="1" OR $np_idgroup_h=="23" OR $np_idgroup_h=="26")) {// OR $np_idgroup_h=="28"
        
        if ($np_divisi_h=="OTC") {
            $jn_idmenu="'87', '226', '258', '224', '259', '505', '504'";
            if ($np_idgroup_h=="26") {//23=mba dsi, 26= otc spv, bang ipul
                $jn_idmenu="'122', '115', '332', '155', '256', '245', '254', '258', '224', '259', '505', '504'";
            }
        }else{
            $jn_idmenu="'88', '89', '204', '258', '259', '224'";
            if ($np_idgroup_h!="25") $jn_idmenu .=",'192'";
            if ($np_idcard_h=="0000001043") $jn_idmenu .=",'106'"; //mba anne
            
            
            if ($np_idgroup_h=="28") {
                if ($np_idcard_h=="0000000143") { //mba ria
                    $jn_idmenu="'149', '150', '156', '203', '258', '262', '264', '162', '259'";
                }else{//mba marsis
                    $jn_idmenu="'', '', '', '', ''";
                }
                
            }
            
            
        }
        if (empty($jn_idmenu)) $jn_idmenu="'00933'";
        $jn_idmenu="(".$jn_idmenu.")";
        $query = "select a.ID, b.PARENT_ID, b.JUDUL, b.URL, b.URUTAN from dbmaster.sdm_groupmenu a JOIN dbmaster.sdm_menu b on a.ID=b.ID WHERE a.ID IN $jn_idmenu AND a.ID_GROUP='$np_idgroup_h' ORDER BY b.D_URUT, a.ID, b.URUTAN";
        $tampil_h=mysqli_query($cnmy, $query);
        
        $no=1;
        $no_w=1;
        while ($trw = mysqli_fetch_array($tampil_h)) {
            $h_idmenu=$trw['ID'];
            $h_prtid=$trw['PARENT_ID'];
            $h_nmmenu=$trw['JUDUL'];
            $h_nmmenu_asli=$trw['JUDUL'];
            $h_link=$trw['URL'];
            if ($h_idmenu=="259" OR $h_idmenu=="504") $h_nmmenu="Tanda Tangan SPD";
            if ($h_idmenu=="224") $h_nmmenu="Report SPD";
			if ($h_idmenu=="505") $h_nmmenu="Input Dana Bank";
            
            if ($h_idmenu=="245") $h_nmmenu="Permintaan Dana Rutin";
            if ($h_idmenu=="254") $h_nmmenu="Outstanding LK/CA";
            if ($h_idmenu=="256") $h_nmmenu="Closing LK/CA";
            if ($h_idmenu=="332") $h_nmmenu="Isi Biaya Rutin CHC";
            
            
            if ($h_idmenu=="203") $h_nmmenu="Permintaan Dana Inc.";
            if ($h_idmenu=="264") $h_nmmenu="Permint. Dana RTN/LK.";
            
            $jml_kata=strlen($h_nmmenu_asli);
            $txt_judul=substr($h_nmmenu_asli,0,30);
            $txt_judul2=substr($h_nmmenu_asli,30,$jml_kata);
            if (!empty(trim($txt_judul2))) $txt_judul=$txt_judul."...";
            
            if (!empty($h_idmenu) AND !empty($h_nmmenu) AND !empty($h_link)) {
                echo "<div class='col-lg-3 col-xs-6'>";
                    echo "<div class='$h_warna'>";
                        echo "<div class='inner'><div id='divlink'><a href='$h_link&idmenu=$h_idmenu&act=$h_prtid' class='vlink'>$h_nmmenu</a></div></div>";
                        echo "<div class='icon'><i class='ion ion-stats-bars'></i></div>";
                        echo "<a href='$h_link&idmenu=$h_idmenu&act=$h_prtid' class='small-box-footer'>$txt_judul <i class='fa fa-arrow-circle-right'></i></a>";
                    echo "</div>";
                echo "</div>";
                
                
                if ((double)$no>=4) {
                    if ((double)$no==4) $no_w=4;
                    else $no_w=$no_w-1;
                }else{
                    $no_w++;
                }
                $no++;
                if ($no_w==0) $no_w=1;
                $h_warna = $h_warna_p[$no_w];
             
            }
        }
    }else{
        
        //bu farida dan bu ira
        if ($np_idcard_h=="0000000367" OR $np_idcard_h="0000001372") {
            $jn_idmenu="'240'";
            $jn_idmenu="(".$jn_idmenu.")";
            $query = "select a.ID, b.PARENT_ID, b.JUDUL, b.URL, b.URUTAN from dbmaster.sdm_groupmenu a JOIN dbmaster.sdm_menu b on a.ID=b.ID WHERE a.ID IN $jn_idmenu AND a.ID_GROUP='$np_idgroup_h' ORDER BY a.ID, b.URUTAN";
            $tampil_h=mysqli_query($cnmy, $query);

            $no=1;
            $no_w=1;
            while ($trw = mysqli_fetch_array($tampil_h)) {
                $h_idmenu=$trw['ID'];
                $h_prtid=$trw['PARENT_ID'];
                $h_nmmenu=$trw['JUDUL'];
                $h_link=$trw['URL'];
                
                if ($_SESSION['MOBILE']=="Nx") {
                    $h_link="eksekusi3.php?module=appdirpd";
                }
                if ($h_idmenu=="259") $h_nmmenu="Tanda Tangan SPD";
                if (!empty($h_idmenu) AND !empty($h_nmmenu) AND !empty($h_link)) {
                    echo "<div class='col-lg-3 col-xs-6'>";
                        echo "<div class='$h_warna'>";
                            echo "<div class='inner'><div id='divlink'><a href='$h_link&idmenu=$h_idmenu&act=$h_prtid' class='vlink'>$h_nmmenu</a></div></div>";
                            echo "<div class='icon'><i class='ion ion-stats-bars'></i></div>";
                            echo "<a href='$h_link&idmenu=$h_idmenu&act=$h_prtid' class='small-box-footer'>view <i class='fa fa-arrow-circle-right'></i></a>";
                        echo "</div>";
                    echo "</div>";


                    if ((double)$no>=4) {
                        if ((double)$no==4) $no_w=4;
                        else $no_w=$no_w-1;
                    }else{
                        $no_w++;
                    }
                    $no++;
                    $h_warna = $h_warna_p[$no_w];

                }
            }
        }else{//selain direktur
            
            if ($np_divisi_h=="OTC") {
                if ($np_divisi_h=="OTC" AND ($np_idgroup_h=="23")) {//mba dsi
                    
                    
                    
                }
            }else{
                //selain OTC 26=bang ipul, otc fin spv
                
                
            }
            
        }
        
    }
    ?>
        
    </div>
</section>




<style>
     #divlink { 
         font-size:20px;
     }
     #divlink2 { 
         font-size:19px;
     }
     #divlink a {
         color:white;
     }
     #divlink a:hover {
         opacity: 0.7;
     }
     #divlink2 a {
         color:white;
     }
     #divlink2 a:hover {
         opacity: 0.7;
     }
</style>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .ui-datepicker-calendar {
        display: none;
    }
    
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>