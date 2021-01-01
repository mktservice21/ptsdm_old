
<?PHP
    session_start();
    include "../config/koneksimysqli.php";
    $idkaryawanproses_="0000001736";
    $idkaryawanproses_="0000001520";
    $idkaryawanproses_=$_SESSION['IDCARD'];
    $ljmldataprs=0;
    $lidrutinpros_="";
    $lidcapros_="";
    
    $query = "select kode, idrutin from dbmaster.t_brrutin0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_atasan1,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_atasan1,'')='') ORDER BY kode, idrutin";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidrutin_prosspv="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidrutinpros_=$lidrutinpros_."'".$ra_pros['idrutin']."',";
            
            $npidrutin_prosspv=$npidrutin_prosspv.$ra_pros['idrutin'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidrutinpros_)) $lidrutinpros_=substr($lidrutinpros_, 0, -1);
        if (!empty($npidrutin_prosspv)) $npidrutin_prosspv=substr($npidrutin_prosspv, 0, -2);
    }
    
    
    $filteridruti_prs="";
    if (!empty($lidrutinpros_)) $filteridruti_prs=" AND idrutin NOT IN ($lidrutinpros_)";
    $query = "select kode, idrutin from dbmaster.t_brrutin0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_atasan2,'')='') $filteridruti_prs ORDER BY kode, idrutin";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidrutin_prosdm="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidrutinpros_=$lidrutinpros_."'".$ra_pros['idrutin']."',";
            
            $npidrutin_prosdm=$npidrutin_prosdm.$ra_pros['idrutin'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidrutinpros_)) $lidrutinpros_=substr($lidrutinpros_, 0, -1);
        if (!empty($npidrutin_prosdm)) $npidrutin_prosdm=substr($npidrutin_prosdm, 0, -2);
    }
    
    $filteridruti_prs="";
    if (!empty($lidrutinpros_)) $filteridruti_prs=" AND idrutin NOT IN ($lidrutinpros_)";
    $query = "select kode, idrutin from dbmaster.t_brrutin0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_atasan3,'')='') $filteridruti_prs ORDER BY kode, idrutin";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidrutin_prossm="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidrutinpros_=$lidrutinpros_."'".$ra_pros['idrutin']."',";
            
            $npidrutin_prossm=$npidrutin_prossm.$ra_pros['idrutin'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidrutinpros_)) $lidrutinpros_=substr($lidrutinpros_, 0, -1);
        if (!empty($npidrutin_prossm)) $npidrutin_prossm=substr($npidrutin_prossm, 0, -2);
    }
    
    $filteridruti_prs="";
    if (!empty($lidrutinpros_)) $filteridruti_prs=" AND idrutin NOT IN ($lidrutinpros_)";
    $query = "select kode, idrutin from dbmaster.t_brrutin0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_fin,'')='') $filteridruti_prs ORDER BY kode, idrutin";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidrutin_prosfin="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidrutinpros_=$lidrutinpros_."'".$ra_pros['idrutin']."',";
            
            $npidrutin_prosfin=$npidrutin_prosfin.$ra_pros['idrutin'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidrutinpros_)) $lidrutinpros_=substr($lidrutinpros_, 0, -1);
        if (!empty($npidrutin_prosfin)) $npidrutin_prosfin=substr($npidrutin_prosfin, 0, -2);
    }
    
    
    
    //CA
    
    
    $query = "select idca from dbmaster.t_ca0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_atasan1,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_atasan1,'')='') ORDER BY idca";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidca_prosspv="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidcapros_=$lidcapros_."'".$ra_pros['idca']."',";
            
            $npidca_prosspv=$npidca_prosspv.$ra_pros['idca'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidcapros_)) $lidcapros_=substr($lidcapros_, 0, -1);
        if (!empty($npidca_prosspv)) $npidca_prosspv=substr($npidca_prosspv, 0, -2);
    }
    
    
    $filteridruti_prs="";
    if (!empty($lidcapros_)) $filteridruti_prs=" AND idca NOT IN ($lidcapros_)";
    $query = "select idca from dbmaster.t_ca0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_atasan2,'')='') $filteridruti_prs ORDER BY idca";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidca_prosdm="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidcapros_=$lidcapros_."'".$ra_pros['idca']."',";
            
            $npidca_prosdm=$npidca_prosdm.$ra_pros['idca'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidcapros_)) $lidcapros_=substr($lidcapros_, 0, -1);
        if (!empty($npidca_prosdm)) $npidca_prosdm=substr($npidca_prosdm, 0, -2);
    }
    
    
    $filteridruti_prs="";
    if (!empty($lidcapros_)) $filteridruti_prs=" AND idca NOT IN ($lidcapros_)";
    $query = "select idca from dbmaster.t_ca0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_atasan3,'')='') $filteridruti_prs ORDER BY idca";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidca_prossm="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidcapros_=$lidcapros_."'".$ra_pros['idca']."',";
            
            $npidca_prossm=$npidca_prossm.$ra_pros['idca'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidcapros_)) $lidcapros_=substr($lidcapros_, 0, -1);
        if (!empty($npidca_prossm)) $npidca_prossm=substr($npidca_prossm, 0, -2);
    }
    
    
    $filteridruti_prs="";
    if (!empty($lidcapros_)) $filteridruti_prs=" AND idca NOT IN ($lidcapros_)";
    $query = "select idca from dbmaster.t_ca0 WHERE karyawanid='$idkaryawanproses_' and IFNULL(stsnonaktif,'') <> 'Y' AND (IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(tgl_fin,'')='') $filteridruti_prs ORDER BY idca";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_pros= mysqli_num_rows($tampil_);
    $npidca_prosfin="";
    if ($ketemu_pros>0) {
        while ($ra_pros= mysqli_fetch_array($tampil_)) {
            $lidcapros_=$lidcapros_."'".$ra_pros['idca']."',";
            
            $npidca_prosfin=$npidca_prosfin.$ra_pros['idca'].", ";
            
            $ljmldataprs++;
        }
        if (!empty($lidcapros_)) $lidcapros_=substr($lidcapros_, 0, -1);
        if (!empty($npidca_prosfin)) $npidca_prosfin=substr($npidca_prosfin, 0, -2);
    }
    
    
?>
<br/>
<li role="presentation" class="dropdown">
    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
        <?PHP 
            if ((double)$ljmldataprs >0 ) {
                echo "<span class='fa fa-envelope-o'>&nbsp;$ljmldataprs</span>";
            }
        ?>
        <!--
        <i class="fa fa-envelope-o"></i>
        <span class="badge bg-green"><?PHP //echo $ljmldataprs; ?></span>
        -->
    </a>

    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">

        <?PHP if (!empty($npidrutin_prosspv)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum diapprove SPV</span> <span class="time">RUTIN / LK</span> </span>
                    <span class="message"><?PHP echo $npidrutin_prosspv; ?> </span>
                </a>
            </li>
        <?PHP } ?>

        <?PHP if (!empty($npidrutin_prosdm)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum diapprove DM</span> <span class="time">RUTIN / LK</span> </span>
                    <span class="message"><?PHP echo $npidrutin_prosdm; ?> </span>
                </a>
            </li>
        <?PHP } ?>

        <?PHP if (!empty($npidrutin_prossm)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum diapprove SM </span> <span class="time">RUTIN / LK</span> </span>
                    <span class="message"><?PHP echo $npidrutin_prossm; ?></span>
                </a>
            </li>
        <?PHP } ?>

        <?PHP if (!empty($npidrutin_prosfin)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum proses FINANCE</span> <span class="time">RUTIN / LK</span> </span>
                    <span class="message"><?PHP echo $npidrutin_prosfin; ?> </span>
                </a>
            </li>
        <?PHP } ?>




        <?PHP if (!empty($npidca_prosspv)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum diapprove SPV</span> <span class="time">CA</span> </span>
                    <span class="message"><?PHP echo $npidca_prosspv; ?></span>
                </a>
            </li>
        <?PHP } ?>

        <?PHP if (!empty($npidca_prosdm)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum diapprove DM</span> <span class="time">CA</span> </span>
                    <span class="message"><?PHP echo $npidca_prosdm; ?></span>
                </a>
            </li>
        <?PHP } ?>

        <?PHP if (!empty($npidca_prossm)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum diapprove SM</span> <span class="time">CA</span> </span>
                    <span class="message"><?PHP echo $npidca_prossm; ?></span>
                </a>
            </li>
        <?PHP } ?>

        <?PHP if (!empty($npidca_prosfin)) { ?>
            <li>
                <a>
                    <span class="image">&nbsp;</span>
                    <span> <span>belum proses FINANCE</span> <span class="time">CA</span> </span>
                    <span class="message"><?PHP echo $npidca_prosfin; ?></span>
                </a>
            </li>
        <?PHP } ?>



        <!--
        <li>
            <div class="text-center">
                <a>
                    <strong>See All Alerts</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </li>
        -->

    </ul>
</li>
