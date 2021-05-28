<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $pmodule="saldosuratdana";
    $pidmenu="204";
    $hari_ini = date("Y-m-d");
    
    $cket = $_POST['uinput'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $isitipe=$_POST['uisi'];
    
    
    $_SESSION['STPDTIPE'] = $isitipe;
    $_SESSION['STPDPERENTY1'] = $mytgl1;
    $_SESSION['STPDPERENTY2'] = $mytgl2;
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
	$ptglbln_pilih2= date("Ym", strtotime($mytgl2));
	
	
    $ptanggal= date("d F Y", strtotime($hari_ini));
    
    
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBSPDPIL01_".$userid."_$now ";
    $tmp02 =" dbtemp.DBSPDPIL02_".$userid."_$now ";
    
    
    $finput = " AND IFNULL(userproses,'')='' ";
    if ($cket=="2") $finput = " AND IFNULL(userproses,'')<>'' ";
    
    $sql = "SELECT a.tgl_apv1, a.tgl_apv2, a.tgl_dir, a.tgl_dir2, a.idinput, a.tglinput, DATE_FORMAT(a.tgl,'%Y%m') as  blnpengajuan, "
            . " DATE_FORMAT(a.tgl,'%d/%m/%Y') as tgl, "
            . " a.divisi, a.kodeid, b.nama, a.subkode, b.subnama, b.igroup, b.iapprove, FORMAT(a.jumlah,0,'de_DE') as jumlah,"
            . " a.nomor, a.nodivisi, a.pilih, a.karyawanid, a.jenis_rpt, DATE_FORMAT(a.tglspd,'%d/%m/%Y') as tglspd, "
            . " CAST('' as CHAR(50)) as iinputkode, CAST('' as CHAR(1)) as ibolehapprove, CAST('' as CHAR(1)) as stsinput "
            . " FROM dbmaster.t_suratdana_br a LEFT JOIN dbmaster.t_kode_spd b ON "
            . " a.kodeid=b.kodeid AND a.subkode=b.subkode ";
    $sql.=" WHERE a.stsnonaktif <> 'Y' AND a.pilih='Y' $finput ";
    $sql.=" AND Date_format(a.tgl, '%Y-%m') between '$tgl1' and '$tgl2' ";
                
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
    $query = "UPDATE $tmp01 SET iinputkode=idinput"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($cket=="1") {// OR $cket=="2"
        
        $query ="select a.stsinput, NULL as tgl_apv1, NULL as tgl_apv2, NULL as tgl_dir, NULL as tgl_dir2, 
            a.idinputbank, a.tglinput, DATE_FORMAT(a.tanggal,'%Y%m') as blnpengajuan, DATE_FORMAT(a.tanggal,'%d/%m/%Y') as tgl,
            a.divisi, a.kodeid, b.nama, a.subkode, b.subnama, b.igroup, b.iapprove, FORMAT(a.jumlah,0,'de_DE') as jumlah,
            '' as nomor, a.nodivisi, 'Y' as pilih, a.userid karyawanid, '' as jenis_rpt, DATE_FORMAT(a.tanggal,'%d/%m/%Y') as tglspd, 
			a.jumlah jmlrp 
            from dbmaster.t_suratdana_bank a JOIN dbmaster.t_kode_spd b on a.kodeid=b.kodeid and a.subkode=b.subkode
            where IFNULL(a.stsnonaktif,'')<>'Y' and Date_format(a.tanggal, '%Y-%m') between '$tgl1' and '$tgl2' 
			and Date_format(a.tanggal, '%Y-%m')>= '2020-02'  
            AND 
            
               (  (IFNULL(a.nomor,'')='' AND IFNULL(a.nodivisi,'')='' AND IFNULL(a.idinput,'0')='0') 
                    OR (a.subkode IN ('29') AND a.stsinput NOT IN ('N') AND Date_format(a.sys_now, '%Y-%m-%d')>='2020-02-01'
                    AND a.idinputbank NOT IN (select distinct IFNULL(idinputbank,'') from dbmaster.t_suratdana_br WHERE 
                    IFNULL(idinputbank,'')<>'' AND subkode='29' AND IFNULL(stsnonaktif,'')<>'Y'))
               )
             
            AND b.igroup='3' ";//AND a.userid IN ('0000000148')
			
			//$query .=" AND CONCAT(a.idinput, a.subkode) NOT IN (select CONCAT(idinput, subkode) from dbmaster.t_suratdana_br WHERE nomor IN ('020/UM-JKT/III/20', '021/UM-JKT/III/20', '022/UM-JKT/III/20') and subkode=29 and stsnonaktif<>'Y') ";
			//$query .=" AND a.idinputbank NOT IN ('BN00001512', 'BN00001639') ";
			
			//$query .=" AND a.stsinput NOT IN ('N', 'D') ";
        //bunga dan listrik (Debit) latai 2
			$query .=" AND ( a.stsinput NOT IN ('N', 'D') OR (a.stsinput='D' AND a.subkode IN ('31', '40') AND a.userid='0000000148') )";
			$query .=" AND IFNULL(a.sudahklaim,'') <>'Y' ";
			$query .=" AND a.idinputbank NOT IN (select distinct IFNULL(idinputbank,'') from dbmaster.t_suratdana_br WHERE IFNULL(idinputbank,'')<>'') ";
			
			$query .=" AND CONCAT(a.kodeid, a.subkode) NOT IN ('234') ";
			
        //echo $query;
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //echo $query;
        $query = "INSERT INTO $tmp02 (idinputbank, tglinput, blnpengajuan, tgl, divisi, kodeid, subkode, jmlrp, jumlah)"
                . "select idinputbank, tglinput, DATE_FORMAT(tanggal,'%Y%m') as blnpengajuan, DATE_FORMAT(tanggal,'%d/%m/%Y') as tgl, "
                . " divisi, kodeid, subkode, jumlah, FORMAT(jumlah,0,'de_DE') as jumlah "
                . " from dbmaster.t_suratdana_bank WHERE idinputbank='BN00004147' "; 
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
		
		
        //bunga diganti jadi minus
        $query = "UPDATE $tmp02 SET jumlah=FORMAT(0-IFNULL(jmlrp,0),0,'de_DE') WHERE CONCAT(kodeid, subkode) IN ('231') AND IFNULL(jumlah,0)>0 AND IFNULL(stsinput,'')='D'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
		
        //setoran listrik latai 2 (Debit) diganti jadi munus
        $query = "UPDATE $tmp02 SET jumlah=FORMAT(0-IFNULL(jmlrp,0),0,'de_DE') WHERE CONCAT(kodeid, subkode) IN ('240') AND IFNULL(jumlah,0)>0 AND IFNULL(stsinput,'')='D'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
	
		
		
        $query = "INSERT INTO $tmp01 (tglinput, blnpengajuan, tgl, tglspd, divisi, kodeid, nama, subkode, subnama, igroup, iapprove, "
                . " nomor, nodivisi, karyawanid, jenis_rpt, pilih, jumlah, iinputkode, stsinput)"
                . " SELECT tglinput, blnpengajuan, tgl, tglspd, divisi, kodeid, nama, subkode, subnama, igroup, iapprove, "
                . " nomor, nodivisi, karyawanid, jenis_rpt, pilih, jumlah, idinputbank as iinputkode, stsinput FROM $tmp02"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    
        $query = "UPDATE $tmp01 SET ibolehapprove='Y' WHERE iapprove='DIR1' AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(tgl_apv1,'0000-00-00')<>'0000-00-00'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp01 SET ibolehapprove='Y' WHERE iapprove='DIR2' AND IFNULL(tgl_apv2,'')<>'' AND IFNULL(tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(tgl_apv2,'0000-00-00')<>'0000-00-00'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp01 SET ibolehapprove='Y' WHERE IFNULL(iapprove,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    $phidstsinput=" class='divnone' ";
    if ($cket=="1") $phidstsinput="";
	
?>
<script src="js/inputmask.js"></script>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <?PHP if ($cket=="1") { ?>
                    
                        <div class='col-sm-3'>
                            <button type='button' class='btn btn-default btn-xs'>Tanggal</button> <span class='required'></span>
                           <div class="form-group">
                                <div class='input-group date' id='mytgl02x'>
                                    <input type="text" class="form-control" id='e_tglberlaku' name='e_tanggal' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo "$ptanggal"; ?>'>
                                    <span class='input-group-addon'>
                                        <span class='glyphicon glyphicon-calendar'></span>
                                    </span>
                                </div>
                           </div>
                       </div>

                        <div class='col-sm-3'>
                            <button type='button' class='btn btn-default btn-xs'>No. SPD</button> <span class='required'></span>
                            
                           <div class="form-group">
                                <input type='text' id='e_nomor' name='e_nomor' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo ""; ?>'>
                           </div>
                       </div>

                        <div class='col-sm-3'>
                            <button type='button' class='btn btn-info btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Jumlah</button> <span class='required'></span>
                           <div class="form-group">
                                <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo ""; ?>' Readonly>
                           </div>
                       </div>


                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Save" onclick='disp_confirm("simpan", "chkbox_br[]")'>
                           </div>
                       </div>
                    <?PHP }elseif ($cket=="2") { ?>
                    
                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Hapus No. SPD" onclick='disp_confirm("hapus", "chkbox_br[]")'>
                           </div>
                       </div>
                    
                    <?PHP } ?>
                    
                </div>
            </div>
            
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    <?PHP
                        $noteket = strtoupper($cket);
                        $text="Data Yang Belum Proses";
                        if ($noteket=="2") $text="Data Yang Sudah Proses";
                        echo "<b>$text</b>";
                    ?>
                </h4>
            </div><div class="clearfix">
        </div>
        <table id='datatableapvcaisi' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
					<th width='40px' <?PHP echo $phidstsinput; ?>>&nbsp;</th>
                    <th width='7px'>No</th>
                    <th width='7px'></th>
                    <th width='40px'>Divisi</th>
                    <th width='40px'>Jenis</th>
                    <th width='50px'>No. BR</th>
                    <?PHP if ($cket=="1") { ?>
                        <th width='40px'>Tgl. Pengajuan</th>
                    <?PHP }else{ ?>
                        <th width='40px'>Tgl. SPD</th>
                    <?PHP } ?>
                    <th width='50px'>Jumlah</th>
                    <th width='80px'>No. SPD</th>
                    <th width='80px'>ID</th>
                    <th width='80px'>Approve 1</th>
                    <th width='80px'>Approve 2</th>
                    <!--<th width='50px'>Kode</th>
                    <th width='30px'>Sub</th>-->
                </tr>
            </thead>
            <tbody>
                <?PHP
                /*
                $no=1;
                $finput = " AND IFNULL(userproses,'')='' ";
                if ($cket=="2") $finput = " AND IFNULL(userproses,'')<>'' ";
                
                $sql = "SELECT a.tgl_apv1, a.tgl_apv2, a.tgl_dir, a.tgl_dir2, a.idinput, a.tglinput, DATE_FORMAT(a.tgl,'%Y%m') as  blnpengajuan, DATE_FORMAT(a.tgl,'%d/%m/%Y') as tgl, "
                        . " a.divisi, a.kodeid, b.nama, a.subkode, b.subnama, b.igroup, FORMAT(a.jumlah,0,'de_DE') as jumlah,"
                        . " a.nomor, a.nodivisi, a.pilih, a.karyawanid, a.jenis_rpt, DATE_FORMAT(a.tglspd,'%d/%m/%Y') as tglspd "
                        . " FROM dbmaster.t_suratdana_br a LEFT JOIN dbmaster.t_kode_spd b ON "
                        . " a.kodeid=b.kodeid AND a.subkode=b.subkode ";
                $sql.=" WHERE a.stsnonaktif <> 'Y' AND a.pilih='Y' $finput ";
                $sql.=" AND Date_format(a.tgl, '%Y-%m') between '$tgl1' and '$tgl2' ";
                
                if ($cket=="1") {
                    $sql.=" ORDER BY a.tglinput ASC, a.divisi";
                }else{
                    $sql.=" ORDER BY a.nomor DESC, a.tglinput ASC, a.divisi";
                }
                */
                
                $no=1;
                $sql = "SELECT * FROM $tmp01 ";
                if ($cket=="1") {
                    $sql.=" ORDER BY ibolehapprove DESC, tglinput ASC, divisi";
                }else{
                    $sql.=" ORDER BY nomor DESC, tglinput ASC, divisi";
                }
                
                $query = mysqli_query($cnmy, $sql);
                while( $row=mysqli_fetch_array($query) ) {
                    
                    $iinputkodeid=$row['iinputkode'];
                    $idno=$row['idinput'];
                    $pkaryawanid=$row['karyawanid'];
                    $pdivisi=$row['divisi'];
                    $pigroupspd=$row['igroup'];
                    $pnama=$row['nama'];
                    $psubnama=$row['subnama'];
                    $pnomor=$row['nomor'];
                    
                    $tglbuat = $row["tgl"];
                    $ptgl=$row['tgl'];
                    
                    $mtg_pengajuan=$row['blnpengajuan'];
                    
                    if ($cket=="2") {
                        if (!empty($row['tglspd']))
                            $ptgl=$row['tglspd'];
                    }
                    
                    $pjumlah=$row['jumlah'];
                    $ndiviotc=$row["nodivisi"];
                    $pkode=$row["kodeid"];
                    $psubkode=$row["subkode"];
                    $pjenisrpt=$row["jenis_rpt"];
                    $nourut = "";


                    $papv_fin1=$row["tgl_apv1"];
                    $papv_fin2=$row["tgl_apv2"];
                    $papv_dir1=$row["tgl_dir"];
                    $papv_dir2=$row["tgl_dir2"];
                    
                    if ($papv_fin1=="0000-00-00") $papv_fin1="";
                    if ($papv_fin2=="0000-00-00") $papv_fin2="";
                    if ($papv_dir1=="0000-00-00") $papv_dir1="";
                    if ($papv_dir2=="0000-00-00") $papv_dir2="";
                    
                    $pmystsyginput="";
                    if ($pkaryawanid=="0000000566") {
                        $pmystsyginput=1;
                    }elseif ($pkaryawanid=="0000001043") {
                        $pmystsyginput=2;
                    }else{
                        if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {//anne
                            $pmystsyginput=5;
                        }else{
                            if ($pkode=="1" AND $psubkode=="03") {//ria
                                $pmystsyginput=3;
                            }elseif ($pkode=="2" AND $psubkode=="05") {//ria CA SEWA
                                $pmystsyginput=7;
                            }elseif ($pkode=="1" AND $psubkode=="04") {//ria Insentif
                                $pmystsyginput=8;
                            }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
                                $pmystsyginput=4;
                            }elseif ( ($pkode=="2" AND $psubkode=="22") OR ($pkode=="2" AND $psubkode=="23") ) {//marsis
                                $pmystsyginput=6;
                            }
                        }
                    }
                    
                    $pmymodule="";
                    $plihat="";
                    if ($pdivisi=="OTC") {
                        if ( ($pkode=="1" AND $psubkode=="03") ) {
                            $pmymodule="module=rekapbiayarutinotc&act=input&idmenu=171&ket=bukan&ispd=$idno";
                        }elseif ( ($pkode=="2" AND $psubkode=="21") ) {
                            $pmymodule="module=rekapbiayaluarotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
                        }else{
                            $pmymodule="module=lapbrotcpermo&act=input&idmenu=134&ket=bukan&ispd=$idno";
                        }
                    }else{
                        if ($pmystsyginput==1) {
                            $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==2) {
                            if ($pjenisrpt=="D") {
                                $pmymodule="module=saldosuratdana&act=viewbrklaim&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                            }else{
                                $pmymodule="module=saldosuratdana&act=viewbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                            }
                        }elseif ($pmystsyginput==3) {
                            $pmymodule="module=rekapbiayarutin&act=input&idmenu=190&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==4) {
                            $pmymodule="module=rekapbiayaluar&act=input&idmenu=187&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==5) {
                            $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=204&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==6) {
                            $pmymodule="module=spdkas&act=viewbrho&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
                        }elseif ($pmystsyginput==7) {
                            $pmymodule="module=reportcasewa&act=rpt&idmenu=264&ket=bukan&ispd=$idno&bln=$tglbuat";
                        }elseif ($pmystsyginput==8) {
                            $pmymodule="module=mstprosesinsentif&act=input&idmenu=262&ket=bukan&ispd=$idno&bln=$tglbuat";
                        }
                    }
                    
					if ( ($pkode=="2" AND $psubkode=="39") ) {
						$pmymodule="module=bgtpdkaskecilcabang&act=input&idmenu=350&ket=bukan&ispd=$idno&bln=$tglbuat";
					}
					
					if ( ($pkode=="2" AND $psubkode=="25") ) {//BPJS
						$pmymodule="module=viewrptdatabpjs&act=viewrptdatabpjs&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
					}
					
                    $cekbox = "<input type=checkbox value='$iinputkodeid' name=chkbox_br[] onclick=\"HitungTotalDariCekBox()\">";
                    

                    if ($cket=="2"){
                        $pnomor="<a title='Print / Cetak' href='#' class='btn btn-primary btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=suratpd&brid=$pnomor&iprint=print',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "$pnomor</a>";
                    }else{
						
                        if ((double)$mtg_pengajuan<=201908) {
                            
                        }else{
							

                            //lk dan kas
                            if ( ($pkode=="2" AND $psubkode=="21") OR ($pkode=="2" AND $psubkode=="22") OR ($pkode=="2" AND $psubkode=="23") OR ($pkode=="2" AND $psubkode=="39") ) {
                                    //if (empty($papv_dir1)) $cekbox="";
									if (empty($papv_dir2)) $cekbox="";
                            }
                            //rutin
                            if ( ($pkode=="1" AND $psubkode=="03") OR ($pkode=="1" AND $psubkode=="04") OR ($pkode=="2" AND $psubkode=="05") ) {
                                    //if (empty($papv_dir1)) $cekbox="";
									if (empty($papv_dir2)) $cekbox="";
                            }
                            //BR
                            if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="1" AND $psubkode=="02") OR ($pkode=="2" AND $psubkode=="20") OR ($pkode=="6" AND $psubkode=="80") ) {
                                    if (empty($papv_dir2)) $cekbox="";
                            }
							
							//kas kecil cabang
							if ( ($pkode=="2" AND $psubkode=="39") ) {
								if (empty($papv_dir2)) $cekbox="";
							}
							
							//BPJS
							if ( ($pkode=="2" AND $psubkode=="25") ) {
								if (empty($papv_dir2)) $cekbox="";
							}
							
						
                        }
						
                    }
                    
                    
                    if (!empty($pmymodule)) {
                        $nwarna_=" btn btn-info btn-xs ";
                        if (empty($cekbox)) $nwarna_=" btn btn-warning btn-xs ";
                        
                        $plihat="<a style='font-size:11px;' title='Print / Cetak' href='#' class='$nwarna_' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?$pmymodule',"
                            . "'Ratting','width=800,height=500,left=400,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Preview</a>";    
                    }
                    
                    if ($psubkode=="29") $plihat="";
                    
                    
                    $n_trhidde="";
                    //if (empty($cekbox)) $n_trhidde=" style='display:none;' ";
                    
                    $pnmpengajuan_jenis=$pnama;
                    if ($pdivisi!="OTC" AND ($psubkode=="01" OR $psubkode=="02" OR $psubkode=="20")) {
                        $pnmpengajuan_jenis="Advance BR";
                        if ($pjenisrpt=="K") $pnmpengajuan_jenis="Klaim BR";
                        if ($pjenisrpt=="B") $pnmpengajuan_jenis="CA";
                        if ($pjenisrpt=="S") $pnmpengajuan_jenis="Kasbon SBY";
                        if ($pjenisrpt=="D") $pnmpengajuan_jenis="Klaim Disc.";
                        if ($pjenisrpt=="C") $pnmpengajuan_jenis="Klaim Disc. (Via SBY)";
                        if ($pjenisrpt=="V") $pnmpengajuan_jenis="Advance BR (Via SBY)";
                        if ($pjenisrpt=="J") $pnmpengajuan_jenis="Adjustment";
                        if ($pjenisrpt=="W") $pnmpengajuan_jenis="Transfer Ulang";
                        if ($pdivisi=="HO" AND empty($pjenisrpt)) $pnmpengajuan_jenis="Adjustment";
                        
                        if ($psubkode=="25" OR $psubkode=="26" OR $psubkode=="27" OR $psubkode=="28" OR $psubkode=="29" OR $psubkode=="30" OR $psubkode=="31" OR $psubkode=="32" OR $psubkode=="33" OR $psubkode=="34" OR $psubkode=="37") $pnmpengajuan_jenis=$psubnama;
                        
                    }
                    //if ($psubkode=="25" OR $psubkode=="26" OR $psubkode=="27" OR $psubkode=="28" OR $psubkode=="29" OR $psubkode=="30" OR $psubkode=="31" OR $psubkode=="32" OR $psubkode=="33" OR $psubkode=="34" OR $psubkode=="37") $pnmpengajuan_jenis=$psubnama;
                    
                    if ($pigroupspd=="3") $pnmpengajuan_jenis=$psubnama;
                    
                    if ($ndiviotc=="286/BR-P/XI/2019" AND $pdivisi=="PIGEO"){
                            $cekbox="";//tidak jadi minta dana info mba erny (13 Nov 2019) 9:18
                    }
                     

                    $warnapstsinp="";
                    $mstatusinpt="";
                    $pidstatusinput=$row['stsinput'];
                    if (!empty($pidstatusinput)) {
                        $mstatusinpt="KREDIT";
                        if ($pidstatusinput=="D") {
                            $mstatusinpt="DEBIT";
                            if ($psubkode=="31") {
                                $warnapstsinp=" style='font-weight: bold;' ";
                            }else{
                                $warnapstsinp=" style='color:red;' ";
                            }
                        }elseif ($pidstatusinput=="K") {
                        }else{
                            $warnapstsinp=" style='color:red;' ";
                        }
                        
                        if ($psubkode=="31" AND $pidstatusinput!="D") {
                            $warnapstsinp=" style='color:red;' ";
                            //$cekbox="";
                        }
                    }
					
					if ($psubkode=="39") {
						$pdivisi="";
					}
					
                    echo "<tr $n_trhidde>";
                    echo "<td nowrap>$cekbox</td>";
					echo "<td nowrap $phidstsinput $warnapstsinp>$mstatusinpt</td>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$plihat</td>";
                    echo "<td nowrap>$pdivisi</td>";
                    echo "<td nowrap>$pnmpengajuan_jenis</td>";
                    echo "<td nowrap>$ndiviotc</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap>$pnomor</td>";
                    echo "<td nowrap>$iinputkodeid</td>";
                    echo "<td nowrap>$papv_dir1</td>";
                    echo "<td nowrap>$papv_dir2</td>";
                    //echo "<td>$pnama</td>";
                    //echo "<td>$psubnama</td>";
                    
                    echo "</tr>";
                    
                    $no=$no+1;
                }
                
                ?>
            </tbody>
        </table>
    </div>
    
</form>

<script>
    $(document).ready(function() {
        <?PHP if ($cket=="1") { ?>
            ShowNoSPD();
        <?PHP } ?>
        var dataTable = $('#datatableapvcaisi').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [8] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }/*,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
        
        HitungTotalDariCekBox();
        
    }
    
    function disp_confirm(ket, cekbr){
        if (ket=="simpan") {
			
            var inospdpl =document.getElementById('e_nomor').value;
            var itglbrl =document.getElementById('e_tglberlaku').value;
            
            if (itglbrl=="") {
                //document.getElementById('e_tglberlaku').value=moment().format('DD MMMM YYYY');
                //itglbrl =document.getElementById('e_tglberlaku').value;
            }
            
            if (itglbrl=="") {
                alert("TANGGAL MASIH KOSONG...");
                return false;
            }
            
            if (inospdpl=="") {
                alert("NOMOR SPD KOSONG...\n\
PILIH TANGGAL YANG LAIN ATAU REFRESH PAGE...!!!");
                return false;
            }
			
			
			
            var ijml =document.getElementById('e_jmlusulan').value;
            if(ijml==""){
                ijml="0";
            }
            if (ijml=="0") {
                alert("jumlah masih kosong...");
                return false;
            }
        }
        
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        if (ket=="simpan") {
            var itgl = document.getElementById('e_tglberlaku').value;
            var inospd = document.getElementById('e_nomor').value;
        }else if (ket=="hapus") {
            var itgl = "";
            var inospd = "";
        }
        
        var txt="";
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/aksi_suratpd.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket="+ket+"&unobr="+allnobr+"&utgl="+itgl+"&unospd="+inospd+"&ketrejpen="+txt,
            success:function(data){
                if (ket=="simpan") {
                    TampilData('1');
                }else{
                    TampilData('2');
                }
                alert(data);
            }
        });
        
    }
    

    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                ShowNoSPD();
            } 
        });
    });
    
    function ShowNoSPD() {
        var itgl = document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=viewnomorspd",
            data:"utgl="+itgl,
            success:function(data){
                document.getElementById('e_nomor').value=data;
            }
        });
    }
    
    
    function HitungTotalDariCekBox() {
        document.getElementById('e_jmlusulan').value="0";
        
        var chk_arr1 =  document.getElementsByName('chkbox_br[]');
        var chklength1 = chk_arr1.length; 

        var allnobr="";
        var TotalPilih=0;

        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                allnobr =allnobr + "'"+fields[0]+"',";
                TotalPilih++;
            }
        }
        
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            //alert("tidak ada data yang dipilih...");
            //return false;
        }
        //$("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=hitungtotalcekboxspd",
            data:"unoidbr="+allnobr,
            success:function(data){
                //$("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });

    }
            
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableapvcaisi th {
        font-size: 13px;
    }
    #datatableapvcaisi td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
?>