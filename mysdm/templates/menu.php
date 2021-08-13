<!--<h3>&nbsp;</h3>-->
<ul class="nav side-menu">
<?PHP
$igru=$_SESSION['GROUP'];
$mobilebuka=$_SESSION['MOBILE'];
$pwebmarvis2="http://ms2.marvis.id";


$pkrymenutambah="";
if (isset($_SESSION['MENUTAMBAHID'])) {
    if (!empty($_SESSION['MENUTAMBAHID'])) {
        $pkrymenutambah=$_SESSION['MENUTAMBAHID'];
    }
}

$filteridmrnugroup=" a.ID_GROUP='$igru' ";
$filteridmrnugroup_sub=" a.ID_GROUP='$igru' ";
if (!empty($pkrymenutambah)) {
    $filteridmrnugroup=" ( a.ID_GROUP='$igru' OR (b.`ID` IN $pkrymenutambah AND b.PARENT_ID='0' AND a.ID_GROUP='1') ) ";
    $filteridmrnugroup_sub=" ( a.ID_GROUP='$igru' OR (b.`ID` IN $pkrymenutambah AND b.PARENT_ID<>'0' AND a.ID_GROUP='1') ) ";
}


$pria_menuutama="";
$pria="";
$pmarsis="";
if ($_SESSION['IDCARD']=="0000000143") {
    $pria=" AND a.ID NOT IN ('122', '115', '202', '205', '164', '160', '175', '176', '185', '186', '187', '215', '216', '253', '270', '121', '291')";
	$pria_menuutama=" AND a.ID NOT IN ('426') ";
}

//prita
if ($_SESSION['IDCARD']=="0000001043") {
    $pria=" AND a.ID NOT IN ('395')";
}

if ($_SESSION['IDCARD']=="0000000329") {
    $pmarsis=" AND a.ID NOT IN ('150', '155', '156', '203', '163', '167', '168', '172', '190', '214', '162', '241', '242', '244', '262', '263', '269', '274')";
}elseif ($_SESSION['IDCARD']<>"0000000614") {
    $pmarsis=" AND a.ID NOT IN ('285')";
}

$ptemunurma="";
if ($_SESSION['IDCARD']=="0000000175") {
	$ptemunurma=" AND a.ID NOT IN ('153', '295', '297', '295')";
}

// admin cabang not budget
if ($igru=="33" AND $_SESSION['IDCARD']<>"0000002073") {//0000002073', '0000000470
    $pria_menuutama=" AND a.ID NOT IN ('90') ";
}

//khusus jika ada perbaikan
if ($igru=="1") {
}else{
    //$pria .=" AND a.ID NOT IN ('115')";
}

//gsm
if ($_SESSION['IDCARD']=="0000000159") {//0000002073', '0000000470
    $pria_menuutama=" AND a.ID NOT IN ('521') ";
}

$pidmenu="";
$pidsubmenu="";
if (isset($_GET['idmenu'])) {
    $pidsubmenu=$_GET['idmenu'];
    $query = "select DISTINCT PARENT_ID from dbmaster.sdm_menu WHERE ID='$pidsubmenu'";
    $tmpmenu= mysqli_query($cnmy, $query);
    $tm= mysqli_fetch_array($tmpmenu);
    $pidmenu=$tm['PARENT_ID'];
}

$query = "select a.ID id, a.ID_GROUP id_group,
	b.JUDUL AS judul,
	b.URL AS url,
	b.PUBLISH AS publish,
	b.URUTAN AS urutan,
	b.GAMBAR AS gambar,
	b.PARENT_ID AS parent_id,
	b.M_KHUSUS AS m_khusus,
	b.URUTAN urutan, b.kriteria as kriteria 
        from dbmaster.sdm_groupmenu a
        LEFT JOIN dbmaster.sdm_menu b on a.ID=b.ID
        WHERE b.PUBLISH='Y' AND b.PARENT_ID='0' AND $filteridmrnugroup  $pria_menuutama 
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
                b.URUTAN urutan, b.M_KHUSUS m_khusus, b.kriteria as kriteria
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
            $filparntnot=" and b.PARENT_ID NOT IN ('132', '197', '188', '400', '417', '426') ";
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
                    b.URUTAN urutan, b.M_KHUSUS m_khusus, b.kriteria as kriteria 
                    from dbmaster.sdm_groupmenu a
                    LEFT JOIN dbmaster.sdm_menu b on a.ID=b.ID
                    WHERE b.PUBLISH='Y' AND $filteridmrnugroup_sub AND b.PARENT_ID='$row[id]' $filparntnot  $pria $pmarsis   
                    ORDER BY b.URUTAN, b.ID
                    ";
            //$query3 = "select * from dbmaster.v_groupmenu where id_group='$_SESSION[GROUP]' and publish='Y' and parent_id='$row[id]' order by urutan, id";
            $submenu=mysqli_query($cnmy, $query3);
            $ketemu2=mysqli_num_rows($submenu);
            if ($ketemu2>0){
                while ($s= mysqli_fetch_array($submenu)) {
                    
                    $pjdlmodule=$s['judul'];
                    $purlmodule=$s['url'];
                    $pidmenumodule=$s['id'];
                    $pactmodule=$s['parent_id'];
                    $pkrtmodule=$s['kriteria'];
                    
                    $purlms2="";
                    if ($pkrtmodule=="N" AND !empty($purlmodule)) {
                        $purlms2= str_replace("?module=", "", $purlmodule);
                    }
                    
                    if ($pkrtmodule=="N" AND !empty($purlms2)) 
                        $plinkmodule=$pwebmarvis2."/".$purlms2;
                    else
                        $plinkmodule=$purlmodule."&idmenu=".$pidmenumodule."&act=".$pactmodule."&kriteria=".$pkrtmodule;
                        
                    $psubmenuaktif="";
                    if ($s['id']==$pidsubmenu) $psubmenuaktif="class='active active-sm'";
                    if ($s['id']=="240" AND $_SESSION['MOBILE']=="Nx") {
                        echo "<li $psubmenuaktif><a href='eksekusi3.php?module=appdirpd&idmenu=240&act=236'>"
                            . "<span>$pjdlmodule</span></a></li>";
                    }else{
                        echo "<li><a href='$plinkmodule'> <span>$pjdlmodule</span></a></li>";
                    }
                }
            }else{
                if ($row['id']=="132" OR $row['id']=="197" OR $row['id']=="188" OR $row['id']=="400" OR $row['id']=="417" OR $row['id']=="426") {
                    
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
                                    b.URUTAN urutan, b.M_KHUSUS m_khusus, ifnull(b.S_MENU,'') S_MENU, b.kriteria as kriteria  
                                    from dbmaster.sdm_groupmenu a
                                    LEFT JOIN dbmaster.sdm_menu b on a.ID=b.ID
                                    WHERE b.PUBLISH='Y' AND a.ID_GROUP='$igru' AND b.PARENT_ID='$row[id]' AND ifnull(b.S_MENU,'')='$msmenu' $pria $pmarsis  
                                    ORDER BY IFNULL(b.S_MENU,''), b.URUTAN, b.ID
                                    ";
                            $subsmenu=mysqli_query($cnmy, $query4);
                            $ketemu3=mysqli_num_rows($subsmenu);
                            if ($ketemu3>0){
                                if (!empty($msmenu)) {
                                    echo "<ul class='nav child_menu'>";
                                    while ($sm= mysqli_fetch_array($subsmenu)) {
                                        echo "<li><a href='$sm[url]&idmenu=$sm[id]&act=$sm[parent_id]&kriteria=$sm[kriteria]'> <span>$sm[judul]</span></a></li>";
                                    }
                                    echo "</ul>";
                                }else{
                                    while ($sm= mysqli_fetch_array($subsmenu)) {
                                        echo "<li><a href='$sm[url]&idmenu=$sm[id]&act=$sm[parent_id]&kriteria=$sm[kriteria]'> <span>$sm[judul]</span></a></li>";
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
        
        if ((int)$igru==10999 OR (int)$igru==30999 OR (int)$igru==250999){
        ?>
		
		
        <?PHP
        if ($_SESSION['MOBILE']=="Y") {
        ?>
        
            <li><a><i class="fa fa-sitemap"></i> Report <span class="fa fa-chevron-down"></span></a>
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
					
					<li class="sub_menu"><a href="?module=rptlamabrlapkeuangan&idmenu=509&act=lama">Laporan Keuangan Marketing</a></li>
					
                    <li class="sub_menu"><a href="?module=apvrekapbr&idmenu=600&act=lama">Rekap Approval BR</a></li>
                    <li class="sub_menu"><a href="?module=apvrekapbrdisklaim&idmenu=601&act=lama">Rekap Approval BR-Klaim Diskon</a></li>
                    <li class="sub_menu"><a href="?module=apvrekapbracc&idmenu=602&act=lama">Rekap ACC BR</a></li>
                    <li class="sub_menu"><a href="?module=apvrekapbrviasby&idmenu=603&act=lama">Rekap Approval BR via SBY</a></li>
                    <li class="sub_menu"><a href="?module=apvrekapbraccviasby&idmenu=604&act=lama">Rekap ACC BR via SBY</a></li>
					
                    <li class="sub_menu"><a href="?module=ethrealisasibrotc&idmenu=700&act=lama">Realisasi BR OTC</a></li>
					
					
                </ul>
            </li>
            
        <?PHP
        }else{
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
					
					<li><a>Approval<span class="fa fa-chevron-down"></span></a>
						<ul class="nav child_menu">
							<li class="sub_menu"><a href="?module=apvrekapbr&idmenu=600&act=lama">Rekap Approval BR</a></li>
							<li class="sub_menu"><a href="?module=apvrekapbrdisklaim&idmenu=601&act=lama">Rekap Approval BR-Klaim Diskon</a></li>
							<li class="sub_menu"><a href="?module=apvrekapbracc&idmenu=602&act=lama">Rekap ACC BR</a></li>
							<li class="sub_menu"><a href="?module=apvrekapbrviasby&idmenu=603&act=lama">Rekap Approval BR via SBY</a></li>
							<li class="sub_menu"><a href="?module=apvrekapbraccviasby&idmenu=604&act=lama">Rekap ACC BR via SBY</a></li>
						</ul>
					</li>
					
					<li><a>OTC<span class="fa fa-chevron-down"></span></a>
						<ul class="nav child_menu">
							<li class="sub_menu"><a href="?module=ethrealisasibrotc&idmenu=700&act=lama">Realisasi BR</a></li>
						</ul>
					</li>
					
					<!--<li><a href="#level1_2">Level One</a></li>-->
				</ul>
			</li>
		
        <?PHP
        }
        ?>
		
		
        <?PHP
        }
        ?>
       
        
        <?PHP
        if ((int)$igru==1099999 OR (int)$igru==23099999 OR (int)$igru==26099999 OR (int)$igru==41099999){
        ?>
		

        <?PHP
        if ($_SESSION['MOBILE']=="Y") {
        ?>

            <li><a><i class="fa fa-sitemap"></i> OTC <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                            
                    <li class="sub_menu"><a href="?module=otcrptlamaviewbrtrans&idmenu=600&act=lama">View BR by Tgl. Tranfer</a></li>
                    <li class="sub_menu"><a href="?module=otcrptlamaviewbrtgl&idmenu=601&act=lama">View BR by Tgl. BR</a></li>
                    
                    <li class="sub_menu"><a href="?module=otclaptrans&idmenu=605&act=lama">Laporan BR Transfer (Ane)</a></li>
                    <li class="sub_menu"><a href="?module=otclaprekaptrans&idmenu=607&act=lama">Rekap Transfer</a></li>
                    
                    <li class="sub_menu"><a href="?module=otclapinputsby&idmenu=608&act=lama">Input Report SBY</a></li>
                    <li class="sub_menu"><a href="?module=otclapakhirsby&idmenu=612&act=lama">Laporan Akhir SBY</a></li>
                            
                    <?PHP
                    $igru=$_SESSION['GROUP'];
                    if ((int)$igru==23){
                    ?>
                        <li class="sub_menu"><a href="?module=otclaprekapbr3&idmenu=613&act=lama">Rekap BR</a></li>
                    <?PHP
                    }else{
                    ?>
                        <li class="sub_menu"><a href="?module=otclaprekapbr&idmenu=613&act=lama">Rekap BR</a></li>
                    <?PHP
                    }
                    ?>
                    <li class="sub_menu"><a href="?module=otclaprekapbr2&idmenu=614&act=lama">Rekap BR Cabang</a></li>
                    
                    
                </ul>
            </li>
		  
        <?PHP
        }else{
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
							<?PHP
							$igru=$_SESSION['GROUP'];
							if ((int)$igru==23){
							?>
								<li class="sub_menu"><a href="?module=otclaprekapbr&idmenu=613&act=lama">Rekap BR</a></li>
							<?PHP
							}else{
							?>
								<li class="sub_menu"><a href="?module=otclaprekapbr&idmenu=613&act=lama">Rekap BR</a></li>
							<?PHP
							}
							?>
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
        }
        ?>

        
        <?PHP
        $igru=$_SESSION['GROUP'];
        if ((int)$igru==1099999 OR (int)$igru==22099999 OR ((int)$igru==28099999 AND $_SESSION['IDCARD']=="0000000329")){
        ?>
        <li><a><i class="fa fa-sitemap"></i> Kas Kecil <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
			
                <?PHP
                if ((int) $igru != 22) {
                ?>
                <li class="sub_menu"><a href="?module=kasisikas&idmenu=700&act=lama">Isi Kas Kecil</a></li>
                <li class="sub_menu"><a href="?module=kaslihatedit&idmenu=701&act=lama">Lihat/Edit/Delete Kas Kecil</a></li>
                <?PHP
                }
                ?>
				
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
        if ((int)$igru==10999 OR (int)$igru==250999){//anne
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
        if ((int)$_SESSION['USERID']==1480999999){
        ?>
        <li><a><i class="fa fa-sitemap"></i> <?PHP echo $_SESSION['NAMALENGKAP']; ?> <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li class="sub_menu"><a href="?module=anneklaimkesehatan&idmenu=900&act=900">Lap. Klaim Kesehatan</a></li>
                <li class="sub_menu"><a href="?module=annecuti&idmenu=901&act=901">History Cuti</a></li>
            </ul>
        </li>
        <?PHP
        }
        ?>
		
		
        <?PHP
        if ((int)$igru==1 OR (int)$igru==2409999){
        ?>
        <li><a><i class="fa fa-sitemap"></i> ADMINISTRATOR 2 <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li class="sub_menu"><a href="?module=prosesdatatabel&idmenu=99999&act=none">Proses Data Tabel</a></li>
				<li class="sub_menu"><a href="eksekusi3.php?module=appdirpd&idmenu=240&act=236">TTD DIR</a></li>
				
                <?PHP if ((int)$igru==1){ ?>
                <li class="sub_menu"><a href="?module=lamacekselisihsales&idmenu=999&act=999">Cek Selisih Lama</a></li>
                <?PHP } ?>
				
            </ul>
        </li>
        <?PHP
        }
        ?>
		
		
		
		
        <!---- GL -->
        
        <?PHP
        
        if ((int)$igru==220999){
        ?>
        
        <li><a><i class="fa fa-sitemap"></i> Report <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
                <li class="sub_menu"><a href="?module=rptlamarealbr&idmenu=500&act=lama">Realisasi BR ETHICAL</a></li>
				<li class="sub_menu"><a href="?module=otclaprekapbr3&idmenu=613&act=lama">Rekap BR OTC</a></li>
            </ul>
        </li>
        
        <?PHP
        }
        ?>
        
        <!---- END GL -->
		
		
		
		
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


<?PHP

$pkaryawan_nidmenu=$_SESSION['IDCARD'];
$query = "select DISTINCT ID, JUDUL FROM dbmaster.sdm_menu 
    WHERE ID IN (select distinct IFNULL(a.PARENT_ID,'') FROM dbmaster.sdm_menu as a 
    JOIN dbmaster.t_karyawan_menu_d as b on a.ID=b.id JOIN dbmaster.t_karyawan_menu as c 
    on b.igroup=c.igroup where c.karyawanid='$pkaryawan_nidmenu' AND c.karyawanid NOT IN ('0000002073', '0000000470'))";
$tampil=mysqli_query($cnmy, $query);
$ketemu=mysqli_num_rows($tampil);
if ($ketemu>0){
    echo "<ul class='nav side-menu'>";

    while ($row= mysqli_fetch_array($tampil)) {
        $pjdlmenu=$row['JUDUL'];

        echo "<li><a><i class='fa fa fa-desktop'></i> $pjdlmenu <span class='fa fa-chevron-down'></span></a>";

        $query = "select distinct a.ID as id, a.PARENT_ID as parent_id, a.JUDUL as judul, 
            a.URL as url, a.PUBLISH as publish, a.URUTAN as urutan, a.GAMBAR as gambar, 
            a.M_KHUSUS as khusus, a.KRITERIA as kriteria FROM dbmaster.sdm_menu as a 
            JOIN dbmaster.t_karyawan_menu_d as b 
            on a.ID=b.id 
            JOIN dbmaster.t_karyawan_menu as c on b.igroup=c.igroup where 
            c.karyawanid='$pkaryawan_nidmenu' AND c.karyawanid NOT IN ('0000002073', '0000000470')";

        $tampil2=mysqli_query($cnmy, $query);
        $ketemu2=mysqli_num_rows($tampil2);
        if ($ketemu2>0){
            echo "<ul class='nav child_menu'>";

            while ($row2= mysqli_fetch_array($tampil2)) {
                $pjdlmodule=$row2['judul'];
                $purlmodule=$row2['url'];
                $pidmenumodule=$row2['id'];
                $pactmodule=$row2['parent_id'];
                $pkrtmodule=$row2['kriteria'];

                $purlms2="";
                if ($pkrtmodule=="N" AND !empty($purlmodule)) {
                    $purlms2= str_replace("?module=", "", $purlmodule);
                }
                
                if ($pkrtmodule=="N" AND !empty($purlms2)) 
                    $plinkmodule=$pwebmarvis2."/".$purlms2;
                else
                    $plinkmodule=$purlmodule."&idmenu=".$pidmenumodule."&act=".$pactmodule."&kriteria=".$pkrtmodule;
                
                
                $psubmenuaktif="";
                if ($pidmenumodule==$pidsubmenu) $psubmenuaktif="class='active active-sm'";
                
                echo "<li><a href='$plinkmodule'> <span>$pjdlmodule</span></a></li>";


            }

            echo "</ul>";
        }
        echo "</li>";
    }

    echo "</ul>";
}

$ijbt_menu=$_SESSION['JABATANID'];
$ibukamenu=false;
if ( ($ijbt_menu=="15" OR $ijbt_menu=="10" OR $ijbt_menu=="18" OR $ijbt_menu=="08" OR $ijbt_menu=="20" OR $ijbt_menu=="05") AND (int)$igru<>24 AND $ibukamenu==true ){
    
    $pcurul=$_SERVER["HTTP_HOST"];
    echo "<ul class='nav side-menu'>";

        echo "<li><a><i class='fa fa fa-desktop'></i> DKD <span class='fa fa-chevron-down'></span></a>";

            $query = "select a.ID as id, a.PARENT_ID as parent_id, a.JUDUL as judul, a.URL as url, "
                    . " a.PUBLISH as publish, a.URUTAN as urutan, a.GAMBAR as gambar, a.M_KHUSUS as khusus, "
                    . " a.KRITERIA as kriteria from dbmaster.sdm_menu as a where a.PARENT_ID='470' "
                    . " AND a.ID NOT IN ('472', '473', '475', '474') order by a.URUTAN";

            $tampil2=mysqli_query($cnmy, $query);
            $ketemu2=mysqli_num_rows($tampil2);
            if ($ketemu2>0){
                echo "<ul class='nav child_menu'>";

                while ($row2= mysqli_fetch_array($tampil2)) {
                    $pjdlmodule=$row2['judul'];
                    $purlmodule=$row2['url'];
                    $pidmenumodule=$row2['id'];
                    $pactmodule=$row2['parent_id'];
                    $pkrtmodule=$row2['kriteria'];

                    $purlms2="";
                    if ($pkrtmodule=="N" AND !empty($purlmodule)) {
                        $purlms2= str_replace("?module=", "", $purlmodule);
                    }

                    if ($pkrtmodule=="N" AND !empty($purlms2)) 
                        $plinkmodule=$pwebmarvis2."/".$purlms2;
                    else
                        $plinkmodule=$purlmodule."&idmenu=".$pidmenumodule."&act=".$pactmodule."&kriteria=".$pkrtmodule;


                    $psubmenuaktif="";
                    if ($pidmenumodule==$pidsubmenu) $psubmenuaktif="class='active active-sm'";

                    echo "<li><a href='$plinkmodule'> <span>$pjdlmodule</span></a></li>";


                }

                echo "</ul>";
            }
        
        
        
            /*
            echo "<ul class='nav child_menu'>";
            echo "<li><a href='module/data_lama/dkd_dokterpermr/doktm20.php' target='_blank'> <span>Data Dokter Per MR</span></a></li>";
            echo "<li><a href='module/data_lama/dkd_isidkd/mr0.php' target='_blank'> <span>Isi DKD</span></a></li>";
            echo "<li><a href='module/data_lama/dkd_isidkd/mr4.php' target='_blank'> <span>Rekap DKD</span></a></li>";
            echo "<li><a href='module/data_lama/dkd_isidkd/mrvdkd.php' target='_blank'> <span>Lihat DKD</span></a></li>";
            echo "<li><a href='module/data_lama/dkd_pertahun/mr400.php' target='_blank'> <span>Rekap DKD Per Tahun</span></a></li>";
            echo "<li><a href='module/data_lama/dkd_lapcallinc/mr6.php' target='_blank'> <span>Laporan Call Incentive</span></a></li>";
            echo "<li><a href='module/data_lama/dkd_prosescall/pcall00.php' target='_blank'> <span>Proses Call Incentive</span></a></li>";
            echo "</ul>";
            */

            
        echo "<li>";
        
    echo "</ul>";
    
}


?>