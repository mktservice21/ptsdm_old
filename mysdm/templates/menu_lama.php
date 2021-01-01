<!--<h3>&nbsp;</h3>-->
<ul class="nav side-menu">
<?PHP
$igru=$_SESSION['GROUP'];
$mobilebuka=$_SESSION['MOBILE'];

$query = "select a.ID id, a.ID_GROUP id_group,
	b.JUDUL AS judul,
	b.URL AS url,
	b.PUBLISH AS publish,
	b.URUTAN AS urutan,
	b.GAMBAR AS gambar,
	b.PARENT_ID AS parent_id,
	b.M_KHUSUS AS m_khusus,
	b.URUTAN urutan
        from dbmaster.sdm_groupmenu a
        LEFT JOIN dbmaster.sdm_menu b on a.ID=b.ID
        WHERE b.PUBLISH='Y' AND b.PARENT_ID='0' AND a.ID_GROUP='$igru'
        ORDER BY b.URUTAN, b.ID
        ";
//$query = "select * from dbmaster.v_groupmenu where id_group='$_SESSION[GROUP]' and publish='Y' and parent_id='0' order by urutan, id";
$tampil=mysqli_query($cnmy, $query);
$ketemu=mysqli_num_rows($tampil);
if ($ketemu>0){
    if ($_SESSION['UKHUSUS']=="Y") {
        echo '<li class="treeview" style="border-bottom : 1px solid #3c3c3c;"><a href="#"><i class="fa fa-star-o"></i><span>Menu Approve</span><i class="fa fa-angle-left pull-right"></i></a>';
        
        $query2 = "select a.ID id, a.ID_GROUP id_group,
                b.JUDUL AS judul,
                b.URL AS url,
                b.PUBLISH AS publish,
                b.URUTAN AS urutan,
                b.GAMBAR AS gambar,
                b.PARENT_ID AS parent_id,
                b.M_KHUSUS AS m_khusus,
                b.URUTAN urutan, b.M_KHUSUS m_khusus
                from dbmaster.sdm_groupmenu a
                LEFT JOIN dbmaster.sdm_menu b on a.ID=b.ID
                WHERE b.PUBLISH='Y' AND a.ID_GROUP='$igru' AND b.M_KHUSUS='Y'
                ORDER BY b.PARENT_ID, b.URUTAN
                ";
        //$query2 = "select * from dbmaster.v_groupmenu where id_group='$_SESSION[GROUP]' and publish='Y' and m_khusus='Y' order by parent_id, urutan";
        $submenu=mysqli_query($cnmy, $query2);
        $ketemu2=mysqli_num_rows($submenu);
        if ($ketemu2>0){
            echo '<ul class="treeview-menu">';
            while ($s= mysqli_fetch_array($submenu)) {
                ?>
                <li><a href="<?PHP echo "$s[url]&idmenu=$s[id]"; ?>"> <span><?PHP echo "$s[judul]"; ?></span></a></li>
                <?PHP
            }
            echo '</ul>';
        }
    }
    while ($row= mysqli_fetch_array($tampil)) {
        $gbr="fa fa-desktop";
        if (!empty($row['gambar'])) $gbr=$row['gambar'];
        if ( ($row['id']==11157000) AND ($_SESSION['MOBILE']!="Y") AND ($igru==4 OR $igru==5 OR $igru==611 OR $igru==8 OR $igru==11 ) ) {
        }else{
        ?>
        <li><a><i class="fa <?PHP echo $gbr; ?>"></i> <?PHP echo "$row[judul]"; ?> <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
            <?PHP
            //$filparntnot=" and b.PARENT_ID<>'132' and b.PARENT_ID<>'197' ";
			$filparntnot=" and b.PARENT_ID NOT IN ('132', '197', '188') ";
			
			if ($igru==36 OR $igru==38) $filparntnot="";
            if ($mobilebuka=="Y") $filparntnot="";
            
            $query3 = "select a.ID id, a.ID_GROUP id_group,
                    b.JUDUL AS judul,
                    b.URL AS url,
                    b.PUBLISH AS publish,
                    b.URUTAN AS urutan,
                    b.GAMBAR AS gambar,
                    b.PARENT_ID AS parent_id,
                    b.M_KHUSUS AS m_khusus,
                    b.URUTAN urutan, b.M_KHUSUS m_khusus
                    from dbmaster.sdm_groupmenu a
                    LEFT JOIN dbmaster.sdm_menu b on a.ID=b.ID
                    WHERE b.PUBLISH='Y' AND a.ID_GROUP='$igru' AND b.PARENT_ID='$row[id]' $filparntnot  
                    ORDER BY b.URUTAN, b.ID
                    ";
            //$query3 = "select * from dbmaster.v_groupmenu where id_group='$_SESSION[GROUP]' and publish='Y' and parent_id='$row[id]' order by urutan, id";
            $submenu=mysqli_query($cnmy, $query3);
            $ketemu2=mysqli_num_rows($submenu);
            if ($ketemu2>0){
                while ($s= mysqli_fetch_array($submenu)) {
                    ?>
                    <li><a href="<?PHP echo "$s[url]&idmenu=$s[id]&act=$s[parent_id]"; ?>"> <span><?PHP echo "$s[judul]"; ?></span></a></li>
                    <?PHP
                }
            }else{
                if ($row['id']=="132" OR $row['id']=="197" OR $row['id']=="188") {
                    
                    //$carismenu="select distinct IFNULL(S_MENU,'') S_MENU from dbmaster.sdm_menu WHERE PARENT_ID='$row[id]' order by IFNULL(S_MENU,'')";
					
                    $carismenu="select distinct IFNULL(S_MENU,'') S_MENU from dbmaster.sdm_menu WHERE PARENT_ID='$row[id]' "
                            . " AND ID IN (select distinct IFNULL(ID,'') from dbmaster.sdm_groupmenu WHERE ID_GROUP='$igru')"
                            . " order by IFNULL(S_MENU,'')";
							
                    $caritampil= mysqli_query($cnmy, $carismenu);
                    $cariada= mysqli_num_rows($caritampil);
                    if ($cariada>0) {
                        
                        while ($cd= mysqli_fetch_array($caritampil)) {
                            $msmenu=$cd['S_MENU'];
                            $msnmmenu=$msmenu;
                            if ($msmenu=="CA") $msnmmenu="Cash Advance";
                            if ($msmenu=="RUTIN") $msnmmenu="Biaya Rutin";
                            if ($msmenu=="LK") $msnmmenu="Biaya Luar Kota";
                            
                            if (!empty($msmenu)) {
                                echo "<li><a><i class='fa fa-sitemap'></i> $msnmmenu <span class='fa fa-chevron-down'></span></a>";
                            }
                            
                            $query4 = "select a.ID id, a.ID_GROUP id_group,
                                    b.JUDUL AS judul,
                                    b.URL AS url,
                                    b.PUBLISH AS publish,
                                    b.URUTAN AS urutan,
                                    b.GAMBAR AS gambar,
                                    b.PARENT_ID AS parent_id,
                                    b.M_KHUSUS AS m_khusus,
                                    b.URUTAN urutan, b.M_KHUSUS m_khusus, ifnull(b.S_MENU,'') S_MENU 
                                    from dbmaster.sdm_groupmenu a
                                    LEFT JOIN dbmaster.sdm_menu b on a.ID=b.ID
                                    WHERE b.PUBLISH='Y' AND a.ID_GROUP='$igru' AND b.PARENT_ID='$row[id]' AND ifnull(b.S_MENU,'')='$msmenu'  
                                    ORDER BY b.S_MENU, b.URUTAN, b.ID
                                    ";
                            $subsmenu=mysqli_query($cnmy, $query4);
                            $ketemu3=mysqli_num_rows($subsmenu);
                            if ($ketemu3>0){
                                if (!empty($msmenu)) {
                                    echo "<ul class='nav child_menu'>";
                                    while ($sm= mysqli_fetch_array($subsmenu)) {
                                        echo "<li><a href='$sm[url]&idmenu=$sm[id]&act=$sm[parent_id]'> <span>$sm[judul]</span></a></li>";
                                    }
                                    echo "</ul>";
                                }else{
                                    while ($sm= mysqli_fetch_array($subsmenu)) {
                                        echo "<li><a href='$sm[url]&idmenu=$sm[id]&act=$sm[parent_id]'> <span>$sm[judul]</span></a></li>";
                                    }
                                }
                            }
                            
                        }
                        
                    }
                    
                }
                
            }
            ?>

            </ul>
        </li>
        
        <?PHP
        }
        
    }
}
?>
        
        <?PHP
        
        if ((int)$igru==1 OR (int)$igru==3 OR (int)$igru==25){
        ?>
        <li><a><i class="fa fa-sitemap"></i> Report <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                
                <li><a>Budget Request<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li class="sub_menu"><a href="?module=rptlamarealbr&idmenu=500&act=lama">Realisasi BR</a></li>
                        <li class="sub_menu"><a href="?module=rptlamarealbrbulan&idmenu=501&act=lama">Laporan Realisasi BR Perbulan</a></li>
                        <li class="sub_menu"><a href="?module=rptlamabrdccdss&idmenu=502&act=lama">Laporan Bulanan DCC/DSS</a></li>
                        <li class="sub_menu"><a href="?module=rptlamabrnon&idmenu=503&act=lama">Laporan Bulanan Non DCC/DSS</a></li>
                        <li class="sub_menu"><a href="?module=rptlamabrytddccdss&idmenu=504&act=lama">YTD DCC/DSS</a></li>
                        <li class="sub_menu"><a href="?module=rptlamabrrekapsby&idmenu=505&act=lama">Rekap BR SBY</a></li>
                        <li class="sub_menu"><a href="?module=rptlamabrlapviasby&idmenu=506&act=lama">Laporan BR Transfer Via Surabaya</a></li>
                        <li class="sub_menu"><a href="?module=rptlamabrlapklaimdisbulan&idmenu=507&act=lama">Laporan Bulanan Klaim Discount</a></li>
                        <li class="sub_menu"><a href="?module=rptlamabrlaprekapbr&idmenu=508&act=lama">Laporan Rekap BR</a></li>
                    </ul>
                </li>
                <li><a>Transfer<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li class="sub_menu"><a href="?module=rptlamabrlapkeuangan&idmenu=509&act=lama">Laporan Keuangan Marketing</a></li>
                    </ul>
                </li>
                <!--<li><a href="#level1_2">Level One</a></li>-->
            </ul>
        </li>
        <?PHP
        }
        ?>
       
        
        <?PHP
        if ((int)$igru==1 OR (int)$igru==23 OR (int)$igru==26){
        ?>
        <li><a><i class="fa fa-sitemap"></i> OTC <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                
                <li><a>INPUT<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li class="sub_menu"><a href="?module=otcrptlamaviewbrtrans&idmenu=600&act=lama">View BR by Tgl. Tranfer</a></li>
                        <li class="sub_menu"><a href="?module=otcrptlamaviewbrtgl&idmenu=601&act=lama">View BR by Tgl. BR</a></li>
                    </ul>
                </li>
                <li><a>REPORT TRANSFER<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li class="sub_menu"><a href="?module=otclaptrans&idmenu=605&act=lama">Laporan BR Transfer (Ane)</a></li>
                        <li class="sub_menu"><a href="?module=otclaprekaptrans&idmenu=607&act=lama">Rekap Transfer</a></li>
                    </ul>
                </li>
                <li><a>REPORT SURABAYA<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li class="sub_menu"><a href="?module=otclapinputsby&idmenu=608&act=lama">Input Report SBY</a></li>
                        <li class="sub_menu"><a href="?module=otclapakhirsby&idmenu=612&act=lama">Laporan Akhir SBY</a></li>
                    </ul>
                </li>
                <li><a>REPORT REKAP<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li class="sub_menu"><a href="?module=otclaprekapbr&idmenu=613&act=lama">Rekap BR</a></li>
                        <li class="sub_menu"><a href="?module=otclaprekapbr2&idmenu=614&act=lama">Rekap BR Cabang</a></li>
                        <!--<li class="sub_menu"><a href="?module=otclaprekapdana&idmenu=618&act=lama">Rekap Permintaan Dana</a></li>-->
                    </ul>
                </li>
                
            </ul>
        </li>
        <?PHP
        }
        ?>

        
        <?PHP
        $igru=$_SESSION['GROUP'];
        if ((int)$igru==1 OR (int)$igru==28){
        ?>
        <li><a><i class="fa fa-sitemap"></i> Kas Kecil <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li class="sub_menu"><a href="?module=kasisikas&idmenu=700&act=lama">Isi Kas Kecil</a></li>
                <li class="sub_menu"><a href="?module=kaslihatedit&idmenu=701&act=lama">Lihat/Edit/Delete Kas Kecil</a></li>
                <li><a>REPORT<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li class="sub_menu"><a href="?module=kaslapkas&idmenu=702&act=lama">Laporan Kas Kecil</a></li>
                        <li class="sub_menu"><a href="?module=kasrekap&idmenu=703&act=lama">Rekap Kas Kecil</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <?PHP
        }
        ?>
        
        <?PHP
        if ((int)$igru==1 OR (int)$igru==25){//anne
        ?>
        <li><a><i class="fa fa-sitemap"></i> OTC <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li class="sub_menu"><a href="?module=otclaprekapbr&idmenu=613&act=lama">Rekap BR</a></li>
            </ul>
        </li>
        <?PHP
        }
        ?>
		
		
        <?PHP
        if ((int)$igru==1 OR (int)$igru==24){
        ?>
        <li><a><i class="fa fa-sitemap"></i> ADMINISTRATOR <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li class="sub_menu"><a href="?module=prosesdatatabel&idmenu=99999&act=none">Proses Data Tabel</a></li>
            </ul>
        </li>
        <?PHP
        }
        ?>
</ul>


<!---------------------------------------------- LAMA 
<h3>General</h3>
<ul class="nav side-menu">
<?PHP
/*
$tampil=mysqli_query($cnmy, "select DISTINCT IDMENU, NAMA_MENU from v_menu_akses where IDMENU<>1 AND USERGRP='$_SESSION[GROUP]' and IFNULL(TAMPILKAN,0)<>1 ORDER BY URUTANMENU, URUTAN");
$ketemu=mysqli_num_rows($tampil);
if ($ketemu>0){
    while ($row= mysqli_fetch_array($tampil)) {
        ?>
        <li><a><i class="fa fa-desktop"></i> <?PHP echo "$row[NAMA_MENU]"; ?> <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
            <?PHP
            $submenu=mysqli_query($cnmy, "select * from v_menu_akses where USERGRP='$_SESSION[GROUP]' and IDMENU='$row[IDMENU]' and IFNULL(TAMPILKAN,0)<>1 order by URUTANMENU, URUTAN");
            $ketemu2=mysqli_num_rows($submenu);
            if ($ketemu2>0){
                while ($s= mysqli_fetch_array($submenu)) {
                    ?>
                    <li><a href="<?PHP echo "?module=".$s['LINK']."&xmodp=".$s['IDSUB']."&nmun=".$s['NAMA_SUB']."&act=".$s['NAMA_SUB']; ?>"><?PHP echo "$s[NAMA_SUB]"; ?></a></li>
                    <?PHP
                }
            }
            ?>

            </ul>
        </li>
        <?PHP
    }
}
 * 
 */
?>
</ul>
---------------------------------------------- LAMA -->