<?PHP
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
    
    
$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];

$pcabid_pl=$_SESSION['RLWEKPLNCAB'];


    $query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, 
        a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, 
        a.icabangid as icabangid, a.areaid as areaid, a.jabatanid as jabatanid 
        FROM dbmaster.t_karyawan_posisi a 
        LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId 
        LEFT JOIN hrd.karyawan c on a.spv=c.karyawanId 
        LEFT JOIN hrd.karyawan d on a.dm=d.karyawanId 
        LEFT JOIN hrd.karyawan e on a.sm=e.karyawanId 
        LEFT JOIN hrd.karyawan f on a.gsm=f.karyawanId WHERE a.karyawanid='$pidcard'";
    $ptampil= mysqli_query($cnmy, $query);
    $nrs= mysqli_fetch_array($ptampil);
    $pkdspv=$nrs['spv'];
    $pnamaspv=$nrs['nama_spv'];
    $pkddm=$nrs['dm'];
    $pnamadm=$nrs['nama_dm'];
    $pkdsm=$nrs['sm'];
    $pnamasm=$nrs['nama_sm'];
    $pkdgsm=$nrs['gsm'];
    $pnamagsm=$nrs['nama_gsm'];
    

$pidinput="";


$hari_ini = date('Y-m-d');
$pdate = date('Y-m-d', strtotime('+1 days', strtotime($hari_ini)));
$pnewdate = strtotime ( 'monday 0 week' , strtotime ( $pdate ) ) ;
//$tgl_pertama = date ( 'd F Y' , $pnewdate );
$tgl_pertama = date('d F Y');
$tglnow = date('Y-m-d');


$now_xhari = $hari_array[(INT)date('w', strtotime($tglnow))];
$now_xtgl= date('d', strtotime($tglnow));
$now_xbulan = $bulan_array[(INT)date('m', strtotime($tglnow))];
$now_xthn= date('Y', strtotime($tglnow));
                                                        
$ppketstatus="000";//blank
$paktivitas="";
$pcompl="";
$pjmlrecakv=1;
$pjmlrec=1;

$act="dailyinput";
if ($pidact=="editdata"){
    $act="dailyupdate";

    include "config/fungsi_ubahget_id.php";
    
    $pidinput_ec=$_GET['id'];
    $pidinput = decodeString($pidinput_ec);

    $edit = mysqli_query($cnmy, "SELECT * FROM hrd.dkd_new_real0 WHERE idinput='$pidinput'");
    $r    = mysqli_fetch_array($edit);
    $jmlrw0=mysqli_num_rows($edit);

    $pnewdate=$r['tanggal'];
    $ppketstatus=$r['ketid'];
    $paktivitas=$r['aktivitas'];
    $pcompl=$r['compl'];
    $pidjbt=$r['jabatanid'];

    $tgl_pertama = date('d F Y', strtotime($pnewdate));


    if ((INT)$jmlrw0<=0) $jmlrw0=1;
    $pjmlrecakv=$jmlrw0;

    $query = "select dokterid, jenis from hrd.dkd_new_real1 WHERE idinput='$pidinput'";
    $tampil1=mysqli_query($cnmy, $query);
    $jmlrw1=mysqli_num_rows($tampil1);
    if ((INT)$jmlrw1<=0) $jmlrw1=1;
    $pjmlrec=$jmlrw1;


}

$query = "select nama from hrd.jabatan where jabatanId='$pidjbt'";
$ntampil=mysqli_query($cnmy, $query);
$nr=mysqli_fetch_array($ntampil);
$pnamajabatan=$nr['nama'];

?>
<div class="">
    
    <div class='modal fade' id='myModalImages' role='dialog' class='no-print'></div>
    
    <!--row-->
    <div class="row">


        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>

                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                        id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <input class='btn btn-default' type=button value='Lihat Realisasi Visit'
                                onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=lihatrlvisit"; ?>';">
                        </h2>
                        <div class='clearfix'></div>
                    </div>


                        
                    <div class='clearfix'></div>
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        
                        
                        <div id='div_ttd'>

                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                    <?PHP
                                        //echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
                                            include "module/dkd/dkd_wekvisitplanreal/ttd_realisasiplan.php";
                                        //echo "</div>";
                                    ?>
                                
                                <div class='clearfix'></div>
                            </div>

                        </div>
                        
                        
                        
                        <div hidden id='div_foto'>

                        </div>

                        <div hidden id='div_foto2'>

                        </div>
                            
                        
                        
                        <div class='clearfix'></div>
                        
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-md-7 col-sm-7 col-xs-12'>
                                        <?php
                                        //echo "<label><input type='checkbox' class='js-switch' id='chk_ttdfoto' name='chk_ttdfoto' value='byttd' onclick=\"ShowFromChkTtdFoto()\" checked> <span id='lbl_ttdfoto'>Tanda Tangan</span></label>";
                                        echo "<input type='radio' class='' name='opt_ttd' id='opt_ttdfoto' value='ttd_by' checked  onclick=\"ShowFromChkTtdFoto()\" /> Tanda Tangan (User)";
                                        echo "&nbsp; &nbsp; ";
                                        echo "<input type='radio' class='' name='opt_ttd' id='opt_ttdfoto' value='foto_by'  onclick=\"ShowFromChkTtdFoto()\" /> Foto";
                                        ?>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>

                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Join Visit <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <select class='form-control' name='cb_jv' id='cb_jv' onchange="">
                                            <?php
                                            echo "<option value='' selected>N</option>";
                                            echo "<option value='JV'>Y</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <?php
                                        echo "<label><input type='checkbox' class='js-switch' id='chk_jv' name='chk_jv' value='JV' onchange=\"ShowJV(this)\"> Join Visit</label>";
                                        ?>
                                    </div>
                                </div>
                                
                                <?PHP

                                echo "<div hidden id='div_jv'>";
                                    echo "<div class='form-group'>";
                                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>";
                                        echo "<div class='col-md-7 col-sm-7 col-xs-12'>";
                                            if ($pidjbt=="15" OR $pidjbt=="10" OR $pidjbt=="18" OR $pidjbt=="20") {
                                                if (!empty($pkdspv) AND $pkdspv<>$pidcard) {
                                                    echo "<label><input type='checkbox' class='js-switch' id='chk_jv_spv' name='chk_jv_spv' value='$pkdspv'> $pnamaspv</label>";
                                                }
                                                if (!empty($pkddm) AND $pkddm<>$pidcard) {
                                                    echo "<label><input type='checkbox' class='js-switch' id='chk_jv_dm' name='chk_jv_dm' value='$pkddm'> $pnamadm</label>";
                                                }
                                                if (!empty($pkdsm) AND $pkdsm<>$pidcard) {
                                                    echo "<label><input type='checkbox' class='js-switch' id='chk_jv_sm' name='chk_jv_sm' value='$pkdsm'> $pnamasm</label>";
                                                }
                                                if (!empty($pkdgsm) AND $pkdgsm<>$pidcard) {
                                                    echo "<label><input type='checkbox' class='js-switch' id='chk_jv_gsm' name='chk_jv_gsm' value='$pkdgsm'> $pnamagsm</label>";
                                                }
                                            }
                                        echo "</div>";
                                    echo "</div>";
                                echo "</div>";
                                ?>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <select class='form-control' name='cb_cabid' id='cb_cabid' onchange="ShowDataDokter('1', '', '')">
                                            <?php
                                            if ($pidgroup=="1" OR $pidgroup=="24") {
                                                $query = "select iCabangId as icabangid, nama as nama_cabang from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
                                                $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                                $query .=" order by nama, iCabangId";
                                            }else{
                                                if ($pidjbt=="10" OR $pidjbt=="18") {
                                                    $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                        FROM mkt.ispv0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                        WHERE a.karyawanid='$pidcard'";
                                                        $query .=" order by b.nama, a.icabangid";
                                                }elseif ($pidjbt=="08") {
                                                    $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                        FROM mkt.idm0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                        WHERE a.karyawanid='$pidcard'";
                                                        $query .=" order by b.nama, a.icabangid";
                                                }elseif ($pidjbt=="20") {
                                                    $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                        FROM mkt.ism0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                        WHERE a.karyawanid='$pidcard'";
                                                        $query .=" order by b.nama, a.icabangid";
                                                }else{
                                                    $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                        FROM mkt.imr0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                        WHERE a.karyawanid='$pidcard'";
                                                        $query .=" order by b.nama, a.icabangid";
                                                }
                                            }
                                            $tampilket= mysqli_query($cnmy, $query);
                                            $ketemu=mysqli_num_rows($tampilket);
                                            if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
                                            $cno=1; $ppilihcab=""; $pbelum=false;
                                            while ($du= mysqli_fetch_array($tampilket)) {
                                                $nidcab=$du['icabangid'];
                                                $nnmcab=$du['nama_cabang'];
                                                $nidcab_=(INT)$nidcab;
                                                
                                                if ($nidcab==$pcabid_pl){
                                                    echo "<option value='$nidcab' selected>$nnmcab ($nidcab_)</option>";
                                                    $ppilihcab=$nidcab;
                                                    $pbelum=true;
                                                }else{
                                                    echo "<option value='$nidcab'>$nnmcab ($nidcab_)</option>";
                                                    if ($cno==1 AND $pbelum==false) $ppilihcab=$nidcab;
                                                }

                                                $cno++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>User <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <select class='form-control s2' name='cb_doktid' id='cb_doktid' onchange="">
                                            <?php
                                            echo "<option value='' selected>-- Pilih --</option>";
                                            //$ipcabid="0000000094";
                                            $query = "select `id` as iddokter, namalengkap, gelar, spesialis from dr.masterdokter WHERE 1=1 ";
                                            $query .=" AND icabangid='$ppilihcab' ";
                                            $query .=" order by namalengkap, `id`";
                                            //$query .=" limit 100";
                                            $tampilket= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampilket)) {
                                                $niddokt=$du['iddokter'];
                                                $nnmdokt=$du['namalengkap'];
                                                $ngelar=$du['gelar'];
                                                $nspesial=$du['spesialis'];
                                                
                                                if (!empty($pnmdokt)) $pnmdokt=rtrim($pnmdokt, ',');
                                                
                                                if (!empty($ngelar)) $nnmdokt =$nnmdokt." ($ngelar)";
                                                
                                                echo "<option value='$niddokt'>$nnmdokt, $nspesial - $niddokt</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <textarea class='form-control' id="e_ketdetail" name='e_ketdetail' maxlength='300' rows="7"></textarea>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saran <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <textarea class='form-control' id="e_saran" name='e_saran' maxlength='300' rows="7"></textarea>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
						
						

						
					
						
                    </div>
                    
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div id='loading3'></div>
                                <div id="s_div">

                                    <div class='x_content' style="overflow: auto;">

                                        <table id='dtabel' class='table table-striped table-bordered' width='100%'>
                                            <thead>
                                                <tr>
                                                    <th width='5px' align='center' colspan="2"><?PHP echo "$now_xhari, $now_xtgl $now_xbulan $now_xthn"; ?></th>
                                                    <!--
                                                    <th width='5px' align='center'>Jenis</th>
                                                    <th width='200px' align='center'>Nama User</th>
                                                    <th width='200px' align='center'>Notes</th>
                                                    <th width='200px' align='center'>Saran</th>
                                                    -->
                                                </tr>
                                            </thead>
                                            <tbody class='inputdata'>
                                            <?PHP
                                                    $nnjmlrc=0;
                                                
                                                    $puserid=$_SESSION['USERID'];
                                                    $now=date("mdYhis");
                                                    $tmp01 =" dbtemp.tmpdkdrlinptt01_".$puserid."_$now ";
        
                                                    $query = "SELECT a.*, b.namalengkap as nama_dokter, b.gelar, b.spesialis, b.icabangid FROM hrd.dkd_new_real1 as a
                                                        LEFT JOIN dr.masterdokter as b on a.dokterid=b.id 
                                                         WHERE a.tanggal='$tglnow' and a.karyawanid='$pidcard' Order by a.tglinput";
                                                    $tampild=mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $ntglinput=$nrd['tglinput'];
                                                        $ntgl=$nrd['tanggal'];
                                                        $pjenis=$nrd['jenis'];
                                                        $vcabid=$nrd['icabangid'];
                                                        $pdokterid=$nrd['dokterid'];
                                                        $pnmdokt=$nrd['nama_dokter'];
                                                        $pgelardokt=$nrd['gelar'];
                                                        $pspesdokt=$nrd['spesialis'];
                                                        $pnotes=$nrd['notes'];
                                                        $psaran=$nrd['saran'];
                                                        $pkaryawanid=$nrd['karyawanid'];
                                                        $pfttd=$nrd['user_tandatangan'];
                                                        $pffoto=$nrd['user_foto'];
                                                        
                                                        $nfolderbulan = date('Ym', strtotime($ntglinput));
                                                        
                                                        $pittd="Y";
                                                        $pimages_pl=$pfttd;
                                                        $ffolderfile="images/user_ttd/";
                                                        $ffolderfile2="images/user_ttd/$nfolderbulan/";

                                                        if (!empty($pffoto)) {
                                                            $pimages_pl=$pffoto;
                                                            $pittd="N";
                                                            $ffolderfile="images/user_foto/";
                                                            $ffolderfile2="images/user_foto/$nfolderbulan/";
                                                        }
                                                        
                                                        $pnmjenis="";
                                                        //$pnmjenis='N';
                                                        if ($pjenis=="JV") $pnmjenis="Join Visit";
                                                        elseif ($pjenis=="EC") $pnmjenis="Extra Call";
                                                        else{
                                                            if (empty($pjenis)) $pnmjenis="Visit";
                                                            else $pnmjenis="Others";
                                                        }
                                                        
                                                        if (!empty($pgelardokt))
                                                            $pnmdokt_=$pnmdokt."(".$pgelardokt.") - ".$pspesdokt;
                                                        else
                                                            $pnmdokt_=$pnmdokt." - ".$pspesdokt;
                                                        
                                                        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapusDokt('hapusdailydokt', '$pkaryawanid', '$ntgl', '$pdokterid')\">";
                                                        if (empty($pimages_pl)) $pviewuser="<b>$pnmdokt_ - $pdokterid</b>";
                                                        else {
                                                            $pviewuser="<b>$pnmdokt_ - $pdokterid</b>";
                                                            //$pviewuser="<input type='button' value='$pnmdokt_ - $pdokterid' class='btn btn-info btn-xs' data-toggle='modal' data-target='#myModalImages' onClick=\"ShowDataFotoTTD('$pittd', '$pimages_pl', '$nfolderbulan')\">";
                                                        }
                                                        
                                                        $njammenitdetik = date('H:i:s', strtotime($ntglinput));
                                                        $ntanggal = date('l d F Y', strtotime($ntglinput));

                                                        $xhari = $hari_array[(INT)date('w', strtotime($ntglinput))];
                                                        $xtgl= date('d', strtotime($ntglinput));
                                                        $xbulan = $bulan_array[(INT)date('m', strtotime($ntglinput))];
                                                        $xthn= date('Y', strtotime($ntglinput));
                
                                                        
                                                        
                                                        echo "<tr>";
                                                        if (!empty($pimages_pl)) {
                                                            if (file_exists($ffolderfile2."/".$pimages_pl)) {
                                                                $pnamafilefolder=$ffolderfile2."".$pimages_pl;
                                                            }else{
                                                                $pnamafilefolder=$ffolderfile."".$pimages_pl;
                                                            }
                                                            echo "<td nowrap><img class='img_ttdfoto' src='$pnamafilefolder' width='20px' height='30px' data-toggle='modal' data-target='#myModalImages' onClick=\"ShowDataFotoTTD('$pittd', '$pimages_pl', '$nfolderbulan')\" /></td>";
                                                        }else{
                                                            echo "<td nowrap>&nbsp;</td>";
                                                        }
                                                        echo "<td nowrap>";
                                                        echo "- $njammenitdetik";
                                                        if (!empty($pnmjenis)) echo "<br/>- $pnmjenis";
                                                        echo "<br/>- $pviewuser";
                                                        echo "</td>";
                                                        /*
                                                        echo "<td nowrap>$xhari, $xtgl $xbulan $xthn $njammenitdetik</td>";
                                                        echo "<td nowrap>$pnmjenis</td>";
                                                        echo "<td nowrap>$pviewuser</td>";
                                                        echo "<td >$pnotes</td>";
                                                        echo "<td >$psaran</td>";
                                                        */
                                                        //echo "<td >$phapus</td>";
                                                        echo "</tr>";

                                                        
                                                        $nnjmlrc++;

                                                    }
                                                

                                            ?>
                                            </tbody>
                                        </table>

                                    </div>

                                </div>

                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jabatan <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_nmjbt' name='e_nmjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamajabatan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div  id="div_atasan">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>' Readonly>
                                            <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>' Readonly>
                                            <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>' Readonly>
                                            <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>GSM <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>' Readonly>
                                            <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>


                    <div class='clearfix'></div>
                    

                </form>

            </div>

        </div>


    </div>

</div>




<script>
    
    function ShowJV(chkjv) {
        if (chkjv.checked==true) {
            div_jv.style.display = 'block';
        }else{
            div_jv.style.display = 'none';
        }
    }
    
    function ShowFromChkTtdFoto(){
        // Get the checkbox
        var checkBox = document.getElementById("opt_ttdfoto");
        $("#div_foto").html("");
        $("#div_foto2").html("");
        //$("#div_ttdfoto").html("");
        if (checkBox.checked == true){
            //document.getElementById("lbl_ttdfoto").innerHTML ="Tanda Tangan";
            div_ttd.style.display = 'block';
            div_foto.style.display = 'none';
            div_foto2.style.display = 'none';
            location.reload();
        } else {
            //document.getElementById("lbl_ttdfoto").innerHTML ="Foto";
            div_ttd.style.display = 'none';
            div_foto.style.display = 'block';
            div_foto2.style.display = 'block';
            
            $.ajax({
                type:"post",
                url:"module/dkd/viewdatadkd.php?module=viewfotocamera",
                data:"uviewcm=okecamera",
                success:function(data){
                    $("#div_foto").html(data);
                }
            });
            
            $.ajax({
                type:"post",
                url:"module/dkd/viewdatadkd.php?module=viewfotocamera2",
                data:"uviewcm=okecamera",
                success:function(data){
                    $("#div_foto2").html(data);
                }
            });
            
        }
         
    }
    

    
    function ShowDataDokter(sKey, incab, indokt){
        var eidcan =document.getElementById('cb_cabid').value;
        
        $.ajax({
            type:"post",
            url:"module/dkd/viewdatadkd.php?module=viewdatadoktercabang",
            data:"uidcab="+eidcan+"&ukdcab="+incab+"&ukddokt="+indokt+"&skode="+sKey,
            success:function(data){
                $("#cb_doktid").html(data);
            }
        });
    }
    
    
    function ProsesDataHapusDokt(ket, kryid, tgl, doktid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses hapus ...?');
            if (r==true) {


                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("form_data1").action = "module/dkd/dkd_wekvisitplanreal/aksi_wekvisitplanreal_foto.php?module="+module+"&idmenu="+idmenu+"&ket=hapus&act="+ket+"&ukryid="+kryid+"&utgl="+tgl+"&udokt="+doktid;
                document.getElementById("form_data1").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function disp_confirm(pText_, ket, data_img) {
        var iid = document.getElementById('e_id').value;
        var idoktid = document.getElementById('cb_doktid').value;
        var itgl = document.getElementById('e_periode1').value;
        var ikaryawan = document.getElementById('e_idcarduser').value;
        var iketdetail = document.getElementById('e_ketdetail').value;
        var isaran = document.getElementById('e_saran').value;
        var RBox = document.getElementById("opt_ttdfoto");
        
        var select_usr = document.getElementById('cb_doktid');
        var option_usr = select_usr.options[select_usr.selectedIndex];
        
        if (idoktid=="") {
            alert("user kosong...");
            return false;
        }
        
        if (iketdetail=="") {
            alert("notes masih kosong...");
            return false;
        }
        
        if (isaran=="") {
            alert("saran masih kosong...");
            return false;
        }
        
        if (RBox.checked == true){
        } else {
            
            var ifotoarea=document.getElementById('txt_arss').value;
            if (ifotoarea=="") {
                document.getElementById('btnScreenshot').click();
            }else{
            }
        
            var ifoto = document.getElementById(data_img).value;
            if (ifoto=="") {
                alert("Foto belum discreenshot...");
                return false;
            }
        }
        
        $.ajax({
            type:"post",
            url:"module/dkd/viewdatadkd.php?module=cekdatasudahadarealvisit",
            data:"uid="+iid+"&utgl="+itgl+"&ukaryawan="+ikaryawan+"&uidoktid="+idoktid,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;

                if (data=="boleh") {
                    
                    
                    
                    if (RBox.checked == true){
                        pText_="Pastikan Tanda Tangan Terisi...";
                    } else {
                        pText_="Pastikan Foto Sudah discreenshot...";
                    }
                    pText_=pText_+"\n\
Nama User : "+option_usr.text;
                    
                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");
                            
                            var uttd = data_img;//gambarnya
                            
                            //document.write("You pressed OK!")
                            document.getElementById("form_data1").action = "module/dkd/dkd_wekvisitplanreal/aksi_wekvisitplanreal_foto.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("form_data1").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }

                }else{
                    alert(data);
                }
            }
        });
        
        
        
    }
    
    function ShowDataFotoTTD(ittd, img_data, ifbln) {
        $.ajax({
            type:"post",
            url:"module/dkd/viewdatadkd.php?module=showimagespotottd",
            data:"uttd="+ittd+"&uimg="+img_data+"&ufbln="+ifbln,
            success:function(data){
                $("#myModalImages").html(data);
            }
        });
    }
    
    function reset_foto() {
        document.getElementById('txt_arss').value="";
        $("#screenshots").html("");
    }
    
</script>



<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .img_ttdfoto:hover {
        cursor:pointer;
    }
</style>

<style>
    .divnone {
        display: none;
    }
    #dtabel th {
        font-size: 13px;
    }
    #dtabel td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>


<link href="module/dkd/select2.min.css" rel="stylesheet" type="text/css" />
<script src="module/dkd/select2.min.js"></script>
<script>
$(document).ready(function() {
        $('.s2').select2();
    });
</script>