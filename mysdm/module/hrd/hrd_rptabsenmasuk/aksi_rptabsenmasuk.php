<?php
    session_start();

    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $pidgroup=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmprptabsmasukimg01_".$userid."_$now ";
    $tmp02 =" dbtemp.tmprptabsmasukimg02_".$userid."_$now ";
    $tmp03 =" dbtemp.tmprptabsmasukimg03_".$userid."_$now ";
    $tmp04 =" dbtemp.tmprptabsmasukimg04_".$userid."_$now ";
    $tmp05 =" dbtemp.tmprptabsmasukimg05_".$userid."_$now ";
    
    
    $ppilihsts = strtoupper($_POST['eket']);
    $mytgl1 = $_POST['uperiode1'];
    $pkaryawanid = $_POST['ukaryawan'];
    $pstsapv = $_POST['uketapv'];
    
    $pbulan1= date("Y-m-01", strtotime($mytgl1));
    $pbulan2= date("Y-m-t", strtotime($mytgl1));
    
    $pbulan = date('Y-m', strtotime($mytgl1));
    $nbln = date('m', strtotime($mytgl1));
    $nthn = date('Y', strtotime($mytgl1));
    $ptglakhir = date('t', strtotime($mytgl1));
    
    
    $query = "select a.idabsen, a.karyawanid, b.nama as nama_karyawan, a.kode_absen, a.tanggal, a.jam, a.l_latitude, a.l_longitude, a.l_status "
            . " FROM hrd.t_absen as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
            . " WHERE "
            . " a.tanggal BETWEEN '$pbulan1' AND '$pbulan2' ";
    if ($ppilihsts=="00") {
        $query .= " AND a.kode_absen IN ('1', '2') ";
    }else{
        $query .= " AND a.kode_absen='$pstsapv' ";
    }
    if (!empty($pkaryawanid)) {
        $query .=" AND a.karyawanid='$pkaryawanid' ";
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.idabsen, a.kode_absen, a.nama FROM dbimages2.img_absen as a "
            . " JOIN $tmp01 as b on a.idabsen=b.idabsen AND a.kode_absen=b.kode_absen "
            . " WHERE 1=1 ";
    if ($ppilihsts=="00") {
        $query .= " AND a.kode_absen IN ('1', '2') ";
    }else{
        $query .= " AND a.kode_absen='$pstsapv' ";
    }
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD COLUMN nama_images VARCHAR(200)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN $tmp02 as b on a.idabsen=b.idabsen AND a.kode_absen=b.kode_absen "
            . " SET a.nama_images=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select * from $tmp01 WHERE kode_absen='2'";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "DELETE FROM $tmp01 WHERE kode_absen<>'1'"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN jam_p VARCHAR(50), ADD COLUMN nama_images_p VARCHAR(100), ADD COLUMN l_latitude_p VARCHAR(200), ADD COLUMN l_longitude_p VARCHAR(200)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select DISTINCT karyawanid, tanggal, nama_images, l_latitude, l_longitude, jam FROM $tmp03 WHERE "
            . " kode_absen='2') as b on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET "
            . " a.jam_p=b.jam, a.nama_images_p=b.nama_images, a.l_latitude_p=b.l_latitude, a.l_longitude_p=b.l_longitude";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    
    //LIBUR dan JUMLAH HARI KERJA
    $query = "CREATE TEMPORARY TABLE $tmp04 (tanggal DATE, libur VARCHAR(1) DEFAULT 'N', libur_cmasal VARCHAR(1) DEFAULT 'N')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    unset($pinsert_data_detail);//kosongkan array
    $psimpandata=false;
    for($ix=1; $ix<=(INT)$ptglakhir;$ix++) {
        $pntgl=$ix;
        if (strlen($pntgl)<=1) $pntgl="0".$ix;

        $phari = strtoupper(date('l', strtotime($nthn."-".$nbln."-".$pntgl)));

        $npltanggal=$pbulan."-".$pntgl;

        $pcollibur="";
        $plibur="N";
        if ($phari=="SATURDAY") { $plibur="Y"; $pcollibur="style='background-color:#ff9999'"; }
        elseif ($phari=="SUNDAY") { $plibur="Y";$pcollibur="style='background-color:#ff3333'"; }

        $pinsert_data_detail[] = "('$npltanggal', '$plibur')";

        $psimpandata=true;
        //echo "$pntgl : $phari dan $npltanggal<br/>";

    }
    
    
    if ($psimpandata==true) {
        
        
        $query = "INSERT INTO $tmp04 (tanggal, libur) VALUES ".implode(', ', $pinsert_data_detail);
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }

        
        //CUSTI MASAL

        $query = "SELECT DISTINCT b.tanggal FROM hrd.t_cuti0 as a "
                . " JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti where a.id_jenis='00' "
                . " AND a.karyawanid IN ('ALL', 'ALLHO') AND IFNULL(a.stsnonaktif,'')<>'Y' "
                . " AND LEFT(b.tanggal,7)= '$pbulan'";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


                $query = "INSERT INTO $tmp05 (tanggal)values('2021-08-10')";
                //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                $query = "INSERT INTO $tmp05 (tanggal)values('2021-08-17')";
                //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
        $query = "UPDATE $tmp04 as a JOIN $tmp05 as b on a.tanggal=b.tanggal SET a.libur_cmasal='Y'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    }else{
        $query = "INSERT INTO $tmp04(tanggal, libur) SELECT distinct tanggal, 'N' as libur FROM  $tmp01";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    //END LIBUR dan JUMLAH HARI KERJA

        
        
    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );
    
        
    echo "<div hidden class='row'>";
    
        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
            echo "<div class='x_panel'>";
    
                echo "<div class='title_left' style='color:blue;'><h3>";
                if ($pstsapv=="1") {
                    echo "ABSEN MASUK";
                }elseif ($pstsapv=="2") {
                    echo "ABSEN PULANG";
                }
                echo "</h3></div>";
        
            echo "</div>";
        echo "</div>";
        
    echo "</div>";    
    
    echo "<div class=''>";
        
        $query = "select distinct tanggal, libur, libur_cmasal FROM $tmp04 Order by tanggal";
        $tampil0=mysqli_query($cnmy, $query);
        while ($row0= mysqli_fetch_array($tampil0)) {
            $nlibur=$row0['libur'];
            $nliburcmasal=$row0['libur_cmasal'];
            
            $sel_tgl=$row0['tanggal'];
            $ntglday= date("d", strtotime($sel_tgl));
            
            $pbtn_tgl_warna=" btn btn-info ";
            if ($nlibur=="Y" OR $nliburcmasal=="Y") $pbtn_tgl_warna=" btn btn-danger ";
            
            $xhari = $hari_array[(INT)date('w', strtotime($sel_tgl))];
            
            $query = "select DISTINCT karyawanid, nama_karyawan from $tmp01 ORDER BY nama_karyawan, karyawanid";
            $tampil1=mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $nidkaryawan=$row1['karyawanid'];
                $nnmkaryawan=$row1['nama_karyawan'];
                
                if (!empty($pkaryawanid)) $nnmkaryawan="";
                
                $query = "select * from $tmp01 WHERE tanggal='$sel_tgl' AND karyawanid='$nidkaryawan' ORDER BY tanggal, nama_karyawan, karyawanid";
                $tampil=mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);

                if ((INT)$ketemu<=0) {
                    $folderfotofileabs_n="images/foto_absen/none_foto.png";
                    $pnamafiles_img_n="<img src='$folderfotofileabs_n' width='70px' height='75px' />";
                    echo "<div class='animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12'>";
                        echo "<div class='tile-stats'>";

                            echo "<p style='color:#000; text-decoration: underline; font-weight:bold; font-size:12px; margin-bottom:5px'>$nnmkaryawan</p>";
                            echo "<div class='col-md-4'>";
                                echo "$pnamafiles_img_n";
                                echo "<h3 style='color:#fff; font-size:20px; margin-top:5px'>00:00</h3>";
                            echo "</div>";

                            echo "<div class='col-md-4'>";
                                echo "$pnamafiles_img_n";
                                echo "<h3 style='color:#fff; font-size:20px; margin-top:5px'>00:00</h3>";
                            echo "</div>";

                            echo "<div class='icon'>";
                                echo "<span  style='color:#000; font-weight:bold;'>$xhari</span>";
                                echo "<a href='#' class='$pbtn_tgl_warna' >$ntglday</a>";
                                echo "<span><i style='font-size:15px; font-wight:bold;'>&nbsp;</i></span>";
                            echo "</div>";

                            echo "<div class='clearfix'></div>";
                            
                            /*
                            echo "<h3 style='margin-top:5px;'>";
                                echo "<a href='#' class='btn btn-default btn-xs' style='color:#fff; border-color:#fff;'>&nbsp; &nbsp; </a>";
                            echo "</h3>";
                             * 
                             */
                            
                            echo "<div class='col-md-12'>";
                                echo "<a href='#' class='btn btn-default btn-xs' style='color:#fff; border-color:#fff;'>&nbsp; &nbsp; </a>";
                                echo "<a href='#' class='btn btn-default btn-xs' style='color:#fff; border-color:#fff;'>&nbsp; &nbsp; </a>";
                            echo "</div>";


                        echo "</div>";
                    echo "</div>";

                }

                while ($row= mysqli_fetch_array($tampil)) {
                    $nkodeabsen=$row['kode_absen'];
                    $nnmkaryawan=$row['nama_karyawan'];
                    $ntgl=$row['tanggal'];
                    $njam=$row['jam'];
                    $njam_p=$row['jam_p'];
                    $nnamaimg=$row['nama_images'];
                    $nnamaimg_p=$row['nama_images_p'];
                    $nlat=$row['l_latitude'];
                    $nlat_p=$row['l_latitude_p'];
                    $nlong=$row['l_longitude'];
                    $nlong_p=$row['l_longitude_p'];
                    
                    $ntempatabsen=$row['l_status'];

                    $ntglday= date("d", strtotime($ntgl));
                    $ntanggal= date("d/m/Y", strtotime($ntgl));
                    
                    $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                    
                    $folderfotofileabs="images/foto_absen/".$nnamaimg;
                    $folderfotofileabs_p="images/foto_absen/".$nnamaimg_p;

                    $pnamafiles_img="kosong";
                    if (!file_exists($folderfotofileabs) AND !empty($nnamaimg)) {
                        $pnamafiles_img="<img src='$folderfotofileabs' width='70px' height='75px' class='zoomimg' data-toggle='modal' data-target='#myModalImages' onclick=\"ShowFormImages('$folderfotofileabs')\" />";
                    }else{
                        $folderfotofileabs_n="images/foto_absen/none_foto.png";
                        $pnamafiles_img="<img src='$folderfotofileabs_n' width='70px' height='75px' class='zoomimg' data-toggle='modal' data-target='#myModalImages' onclick=\"ShowFormImages('$folderfotofileabs')\" />";
                    }

                    $pnamafiles_img_p="";
                    if (!file_exists($folderfotofileabs_p) AND !empty($nnamaimg_p)) {
                        $pnamafiles_img_p="<img src='$folderfotofileabs_p' width='70px' height='75px' class='zoomimg' data-toggle='modal' data-target='#myModalImages' onclick=\"ShowFormImages('$folderfotofileabs_p')\" />";
                    }else{
                        $folderfotofileabs_n="images/foto_absen/none_foto.png";
                        $pnamafiles_img_p="<img src='$folderfotofileabs_n' width='70px' height='75px' class='zoomimg' data-toggle='modal' data-target='#myModalImages' onclick=\"ShowFormImages('$folderfotofileabs_p')\" />";
                    }

                    /*
                    echo "<div class='animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12'>";
                        echo "<div class='tile-stats'>";
                            echo "<div class='icon'>";
                                //echo "<i class='glyphicon glyphicon-check'></i>";
                                echo "$pnamafiles_img";
                            echo "</div>";

                            echo "<div class='count'>$njam</div>";

                            echo "<h3>";
                                echo "<a href='#' class='btn btn-default' onclick=\"initMap('$nlat', '$nlong', '$nnmkaryawan');\">$ntanggal</a>";
                            echo "<h3>";
                            echo "<p>$nnmkaryawan</p>";
                        echo "</div>";
                    echo "</div>";
                    */
                    
                    if (!empty($pkaryawanid)) $nnmkaryawan="";
                    
                    echo "<div class='animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12'>";
                        echo "<div class='tile-stats'>";

                            echo "<p style='color:#000; text-decoration: underline; font-weight:bold; font-size:12px; margin-bottom:5px'>$nnmkaryawan</p>";
                            echo "<div class='col-md-4'>";
                                echo "$pnamafiles_img";
                                echo "<h3 style='color:#000; font-size:20px; margin-top:5px'>$njam</h3>";
                            echo "</div>";

                            echo "<div class='col-md-4'>";
                                echo "$pnamafiles_img_p";
                                echo "<h3 style='color:#000; font-size:20px; margin-top:5px'>$njam_p</h3>";
                            echo "</div>";

                            echo "<div class='icon'>";
                                echo "<span  style='color:#000; font-weight:bold;'>$xhari</span>";
                                echo "<a href='#' class='$pbtn_tgl_warna' >$ntglday</a>";
                                echo "<span><i style='font-size:15px; font-wight:bold;'>$ntempatabsen</i></span>";
                            echo "</div>";

                            echo "<div class='clearfix'></div>";
                            
                            /*
                            echo "<h3 style='margin-top:5px;'>";
                                //echo "<a href='#' class='btn btn-default btn-xs' onclick=\"initMap('$nlat', '$nlong', '$nnmkaryawan');\">Lihat Peta Lokasi</a>";
                                echo "<a href='#' class='btn btn-default btn-xs' onclick=\"ShowIframeMaps('$nlat', '$nlong', '$nnmkaryawan');\">Lihat Peta Lokasi</a>";
                            echo "</h3>";
                            */
                            
                            echo "<div class='col-md-12'>";
                                echo "<a href='#' class='btn btn-default btn-xs' onclick=\"ShowIframeMaps('$nlat', '$nlong', '$nnmkaryawan');\">Peta Masuk</a>";
                                if (empty($nlong_p) OR empty($nlong_p)) {
                                    echo "<a href='#' class='btn btn-default btn-xs' style='color:#fff; border-color:#fff;'>&nbsp; &nbsp; </a>";
                                }else{
                                    echo "<a href='#' class='btn btn-default btn-xs' onclick=\"ShowIframeMaps('$nlat_p', '$nlong_p', '$nnmkaryawan');\">Peta Pulang</a>";
                                }
                            echo "</div>";


                        echo "</div>";
                    echo "</div>";

                }
                
            }
            
        }
    
    echo "</div>";
?>



<style>
    .zoomimg {
      transition: transform .2s; /* Animation */
      margin: 0 auto;
    }

    .zoomimgXXX:hover {
        -webkit-transform:scale(3.5); /* Safari and Chrome */
        -moz-transform:scale(3.5); /* Firefox */
        -ms-transform:scale(3.5); /* IE 9 */
        -o-transform:scale(3.5); /* Opera */
        transform:scale(3.5);
        
        /*display:block;
        position:fixed;*/
        position:relative;
        z-index: 999;
        
        cursor: pointer;
        
    }

    .zoomimg:hover {
        cursor: pointer;
    }
</style>

<script>
    
    function ShowFormImages(sKey) {
        $.ajax({
            type:"post",
            url:"module/hrd/hrd_rptabsenmasuk/form_images.php?module=showimagespoto",
            data:"ukey="+sKey,
            success:function(data){
                $("#myModalImages").html(data);
            }
        });
    }
    
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp05");
    
    mysqli_close($cnmy);
?>