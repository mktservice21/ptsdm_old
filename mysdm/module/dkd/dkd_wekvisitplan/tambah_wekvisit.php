
<?PHP

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];

        
$pcabangid="";

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


    $pcabangid=$nrs['icabangid'];
    $pareaid=$nrs['areaid'];
    $pjabatanid=$nrs['jabatanid'];


    $query = "select icabangid as icabangid, areaid as areaid, jabatanid as jabatanid from hrd.karyawan where karyawanid='$pidcard'";
    $tampil= mysqli_query($cnmy, $query);
    $rowx= mysqli_fetch_array($tampil);
    if (empty($pcabangid)) $pcabangid=$rowx['icabangid'];
    if (empty($pareaid)) $pareaid=$rowx['areaid'];
    if (empty($pjabatanid)) $pjabatanid=$rowx['jabatanid'];

    $picabidfil="";
    if ($pidjbt=="38" || (DOUBLE)$pidjbt==38) {
        $pcabangid="";
        $query = "select distinct karyawanid as karyawanid, icabangid as icabangid from hrd.rsm_auth where karyawanid='$pidcard'";
        $tampil= mysqli_query($cnmy, $query);
        while ($nro= mysqli_fetch_array($tampil)) {
            $pncab=$nro['icabangid'];
            if ($pncab=="0000000003" OR $pncab=="0000000114") {
                $pcabangid=$pncab;
            }else{
                if (empty($pcabangid)) $pcabangid=$pncab;
            }


            $picabidfil .="'".$pncab."',";
        }
        if (!empty($picabidfil)) {
            $picabidfil="(".substr($picabidfil, 0, -1).")";
        }else{
            $picabidfil="('nnzznnnn')";
        }

    }elseif ($pidjbt=="08" || (DOUBLE)$pidjbt==8) {
        $pcabangid="";
        $query = "select distinct karyawanid as karyawanid, icabangid as icabangid from MKT.idm0 where karyawanid='$pidcard'";
        $tampil= mysqli_query($cnmy, $query);
        while ($nro= mysqli_fetch_array($tampil)) {
            $pncab=$nro['icabangid'];
            if ($pncab=="0000000003" OR $pncab=="0000000114") {
                $pcabangid=$pncab;
            }else{
                if (empty($pcabangid)) $pcabangid=$pncab;
            }


            $picabidfil .="'".$pncab."',";
        }
        if (!empty($picabidfil)) {
            $picabidfil="(".substr($picabidfil, 0, -1).")";
        }else{
            $picabidfil="('nnzznnnn')";
        }

    }

    $pidcabang=$pcabangid;


    $query= "select DISTINCT a.karyawanid as karyawanid, b.nama as nama from MKT.idm0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " WHERE a.icabangid='$pcabangid' AND IFNULL(a.karyawanid,'')<>'' "
            . " AND (IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(b.tglkeluar,'')='')";
    $tampil= mysqli_query($cnmy, $query);
    $rowd= mysqli_fetch_array($tampil);
    $pnnkrydm=$rowd['karyawanid'];
    $pnnmkrydm=$rowd['nama'];
    if (!empty($pnnkrydm)) {
        $pkdspv=""; $pnamaspv="";
        $pkddm=$pnnkrydm;
        $pnamadm=$pnnmkrydm;
    }
    
    $query= "select DISTINCT a.karyawanid as karyawanid, b.nama as nama from MKT.ism0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " WHERE a.icabangid='$pcabangid' AND IFNULL(a.karyawanid,'')<>'' "
            . " AND (IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(b.tglkeluar,'')='')";
    $tampil= mysqli_query($cnmy, $query);
    $rowd2= mysqli_fetch_array($tampil);
    $pnnkrydm=$rowd2['karyawanid'];
    $pnnmkrydm=$rowd2['nama'];
    if (!empty($pnnkrydm)) {
        $pkdsm=$pnnkrydm;
        $pnamasm=$pnnmkrydm;
        $pkdgsm="";
        $pnamagsm="";
    }
    
    
    
    $query = "select a.gsm, b.nama as nama_gsm FROM dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.gsm=b.karyawanid WHERE a.karyawanid='$pkdsm'";
    $ptampil2= mysqli_query($cnmy, $query);
    $nrs2= mysqli_fetch_array($ptampil2);

    $pkdgsm=$nrs2['gsm'];
    $pnamagsm=$nrs2['nama_gsm'];

    if ($pcabangid=="0000000003X") {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
    }

    if ($pcabangid=="00000000114") {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
    }

    if ($pidjbt=="08" || (DOUBLE)$pidjbt==8) {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
    }


// END CABANG & ATASAN



$pidinput="";


$hari_ini = date('Y-m-d');
$pdate = date('Y-m-d', strtotime('+1 days', strtotime($hari_ini)));
$pnewdate = strtotime ( 'monday 0 week' , strtotime ( $pdate ) ) ;
$tgl_pertama = date ( 'd F Y' , $pnewdate );

$ptgledit=$tgl_pertama;

$ppketstatus="000";//blank
$paktivitas="";
$pcompl="";
$pjmlrecakv=1;
$pjmlrec=1;

$act="input";
if ($pidact=="editdata"){
    $act="update";

    include "config/fungsi_ubahget_id.php";
    
    $pidinput_ec=$_GET['id'];
    $pidinput = decodeString($pidinput_ec);
    $pnewdate=$_GET['nid'];
    $pidcard=$pidinput;
    
    $edit = mysqli_query($cnmy, "SELECT * FROM hrd.dkd_new0 WHERE karyawanid='$pidinput' AND tanggal='$pnewdate'");
    $jmlrw0=mysqli_num_rows($edit);
    if ((INT)$jmlrw0>0) {
        $r    = mysqli_fetch_array($edit);
        $ppketstatus=$r['ketid'];
        $paktivitas=$r['aktivitas'];
        $pcompl=$r['compl'];
        $pidjbt=$r['jabatanid'];
        
        $pjmlrecakv=$jmlrw0;
    }else{
        $jmlrw0=1;
        
        $edit = mysqli_query($cnmy, "SELECT distinct tanggal, karyawanid, jabatanid FROM hrd.dkd_new1 WHERE karyawanid='$pidinput' AND tanggal='$pnewdate'");
        $r    = mysqli_fetch_array($edit);
        $pidjbt=$r['jabatanid'];
    }

    $tgl_pertama = date('d F Y', strtotime($pnewdate));

    $ptgledit=$tgl_pertama;
    $pkaryawanedit=$pidinput;

    $query = "select karyawanid, tanggal, jabatanid, dokterid, jenis from hrd.dkd_new1 WHERE karyawanid='$pidinput' AND tanggal='$pnewdate'";
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

    
    <!--row-->
    <div class="row">


        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>

                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                        id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>



                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <b>Activity</b>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID JML <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_idjmlrecakv' name='e_idjmlrecakv' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrecakv; ?>' Readonly>
                                    </div>
                                </div>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                        <input type='text' id='e_idtgledit' name='e_idtgledit' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgledit; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_namauser' name='e_namauser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamalengkap; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jabatan <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_namajbt' name='e_namajbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamajabatan; ?>' Readonly>
                                    </div>
                                </div>

                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>

                                        </div>
                                    </div>
                                </div>

                                <hr/>

                                <div id='div_akv'>
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keperluan <span class='required'></span></label>
                                        <div class='col-xs-4'>
                                            <select class='soflow' name='cb_ketid' id='cb_ketid' onchange="">
                                                <?php
                                                echo "<option value=''>--Pilih--</option>";
                                                $query = "select ketId as ketid, nama as nama From hrd.ket WHERE IFNULL(aktif,'')<>'N' order by ketId, nama";
                                                $tampilket= mysqli_query($cnmy, $query);
                                                while ($du= mysqli_fetch_array($tampilket)) {
                                                    $nidket=$du['ketid'];
                                                    $nnmket=$du['nama'];

                                                    if ($nidket==$ppketstatus) 
                                                        echo "<option value='$nidket' selected>$nnmket</option>";
                                                    else
                                                        echo "<option value='$nidket'>$nnmket</option>";

                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Compl <span class='required'></span></label>
                                        <div class='col-xs-8'>
                                            <input type='text' id='e_compl' name='e_compl' class='form-control col-md-7 col-xs-12' maxlength="150" value='<?PHP echo $pcompl; ?>'>
                                        </div>
                                    </div>


                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Aktivitas <span class='required'></span></label>
                                        <div class='col-md-8'>
                                        <textarea class='form-control' id="e_aktivitas" name='e_aktivitas' maxlength="250"><?PHP echo $paktivitas; ?></textarea>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <button type='button' id='btnakv' class='btn btn-info btn-xs add-aktv' onclick=''>&nbsp; &nbsp; &nbsp; Tambah Activity &nbsp; &nbsp; &nbsp;</button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <b>&nbsp;</b>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                


                                <div id='loading1'></div>
                                <div id="s_div1">

                                    <div class='x_content'>

                                        <table id='dtabel' class='table table-striped table-bordered' width='100%'>
                                            <thead>
                                                <tr>
                                                    <th width='5px' nowrap></th>
                                                    <th width='10px' align='center' class='divnone'></th><!--class='divnone' -->
                                                    <th width='5px' align='center'>&nbsp;</th>
                                                    <th width='5px' align='center'>Keperluan</th>
                                                    <th width='200px' align='center'>Compl.</th>
                                                    <th width='200px' align='center'>Aktivitas</th>
                                                </tr>
                                            </thead>
                                            <tbody class='inputdatatbl'>
                                            <?PHP
                                                $nnjmlrcakv=0;
                                                //echo "<input type='hidden' id='m_idjmrecakv[$nnjmlrcakv]' name='m_idjmrecakv[]' value='$nnjmlrcakv' Readonly>";
                                                //echo "<input type='hidden' id='m_idket[$nnjmlrcakv]' name='m_idket[$nnjmlrcakv]' value=''>";

                                                
                                                if ($pact=="editdata") {
                                                    
                                                    $query = "SELECT a.*, b.nama as nama_ket FROM hrd.dkd_new0 as a
                                                        LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
                                                         WHERE a.karyawanid='$pidinput' AND a.tanggal='$pnewdate'";
                                                    $tampild=mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $pnewdate=$nrd['tanggal'];
                                                        $ppketstatus=$nrd['ketid'];
                                                        $pnmket=$nrd['nama_ket'];
                                                        $paktivitas=$nrd['aktivitas'];
                                                        $pcompl=$nrd['compl'];

                                                        echo "<tr>";
                                                        echo "<td nowrap><input type='checkbox' name='record'>";
                                                        echo "<input type='hidden' id='m_idjmrecakv[$nnjmlrcakv]' name='m_idjmrecakv[]' value='$nnjmlrcakv' Readonly>";
                                                        echo "<input type='hidden' id='m_idket[$nnjmlrcakv]' name='m_idket[$nnjmlrcakv]' value='$ppketstatus'>";
                                                        echo "</td>";
                                                        echo "<td nowrap class='divnone'><input type='checkbox' name='chkbox_akvbr[]' id='chkbox_akvbr[$nnjmlrcakv]' value='$nnjmlrcakv' checked></td>";
                                                        
                                                        echo "<td><button type='button' class='btn btn-warning btn-xs' onclick=\"EditDataAkv('chkbox_akvbr[]', '$nnjmlrcakv')\">Edit</button></td>";
                                                        
                                                        echo "<td nowrap>$pnmket<input type='hidden' id='m_nmket[$nnjmlrcakv]' name='m_nmket[$nnjmlrcakv]' value='$pnmket'></td>";
                                                        echo "<td nowrap>$pcompl<input type='hidden' id='m_compl[$nnjmlrcakv]' name='m_compl[$nnjmlrcakv]' value='$pcompl'></td>";
                                                        echo "<td >$paktivitas<span hidden><textarea class='form-control' id='txt_akv[$nnjmlrcakv]' name='txt_akv[$nnjmlrcakv]'>$paktivitas</textarea></span></td>";
                                                        echo "</tr>";
                                                        
                                                        $nnjmlrcakv++;

                                                    }
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                        
                                        <span hidden>
                                            <button type='button' class='btn btn-danger btn-xs delete-aktv' >&nbsp; &nbsp; Hapus Aktivitas &nbsp; &nbsp;</button>
                                        </span>

                                    </div>

                                </div>



                            </div>
                        </div>
                    </div>

                        
                    <div class='clearfix'></div>
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <b>Visit</b>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID JML <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_idjmlrec' name='e_idjmlrec' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrec; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>JV <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_jv' id='cb_jv' onchange="">
                                            <?php
                                            echo "<option value='N' selected>N</option>";
                                            echo "<option value='Y'>Y</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_cabid' id='cb_cabid' onchange="ShowDataDokter('1', '', '')">
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
                                            $cno=1; $ppilihcab="";
                                            while ($du= mysqli_fetch_array($tampilket)) {
                                                $nidcab=$du['icabangid'];
                                                $nnmcab=$du['nama_cabang'];
                                                $nidcab_=(INT)$nidcab;
                                                echo "<option value='$nidcab'>$nnmcab ($nidcab_)</option>";
                                                if ($cno==1) $ppilihcab=$nidcab;

                                                $cno++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>User <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow form-control s2' name='cb_doktid' id='cb_doktid' onchange="">
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
                                                
                                                echo "<option value='$niddokt'>$nnmdokt ($ngelar), $nspesial - $niddokt</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id="e_ketdetail" name='e_ketdetail' maxlength='300'></textarea>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-dark btn-xs add-row' onclick=''>&nbsp; &nbsp; &nbsp; Tambah Visit &nbsp; &nbsp; &nbsp;</button>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <b>&nbsp;</b>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div id='loading3'></div>
                                <div id="s_div">

                                    <div class='x_content'>

                                        <table id='dtabel' class='table table-striped table-bordered' width='100%'>
                                            <thead>
                                                <tr>
                                                    <th width='5px' nowrap></th>
                                                    <th width='10px' align='center' class='divnone'></th><!--class='divnone' -->
                                                    <th width='5px' align='center'>&nbsp;</th>
                                                    <th width='5px' align='center'>JV</th>
                                                    <th width='200px' align='center'>Nama User</th>
                                                    <th width='200px' align='center'>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody class='inputdata'>
                                            <?PHP
                                                $nnjmlrc=0;
                                                //echo "<input type='hidden' id='m_idjmrec[$nnjmlrc]' name='m_idjmrec[]' value='$nnjmlrc' Readonly>";
                                                //echo "<input type='hidden' id='m_iddokt[$nnjmlrc]' name='m_iddokt[$nnjmlrc]' value=''>";
                                                if ($pact=="editdata") {
                                                    
                                                    $query = "SELECT a.*, b.namalengkap as nama_dokter, b.gelar, b.spesialis, b.icabangid FROM hrd.dkd_new1 as a
                                                        LEFT JOIN dr.masterdokter as b on a.dokterid=b.id 
                                                         WHERE a.karyawanid='$pidinput' AND a.tanggal='$pnewdate' AND IFNULL(a.jenis,'') IN ('', 'JV')";
                                                    $tampild=mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $pjenis=$nrd['jenis'];
                                                        $vcabid=$nrd['icabangid'];
                                                        $pdokterid=$nrd['dokterid'];
                                                        $pnmdokt=$nrd['nama_dokter'];
                                                        $pgelardokt=$nrd['gelar'];
                                                        $pspesdokt=$nrd['spesialis'];
                                                        $pnotes=$nrd['notes'];
                                                        $pnmjenis='N';
                                                        if ($pjenis=="JV") $pnmjenis='Y';
                                                        
                                                        if (!empty($pnmdokt)) $pnmdokt=rtrim($pnmdokt, ',');
                                                        
                                                        $pnmdokt_=$pnmdokt."(".$pgelardokt.") ".$pspesdokt." - ".$pdokterid;

                                                        echo "<tr>";
                                                        echo "<td nowrap><input type='checkbox' name='record'>";
                                                        echo "<input type='hidden' id='m_idjmrec[$nnjmlrc]' name='m_idjmrec[]' value='$nnjmlrc' Readonly>";
                                                        echo "<input type='hidden' id='m_iddokt[$nnjmlrc]' name='m_iddokt[$nnjmlrc]' value='$pdokterid'>";
                                                        echo "<input type='hidden' id='m_idcab[$nnjmlrc]' name='m_idcab[$nnjmlrc]' value='$vcabid'>";
                                                        echo "</td>";
                                                        echo "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br[$nnjmlrc]' value='$nnjmlrc' checked></td>";
                                                        
                                                        echo "<td><button type='button' class='btn btn-warning btn-xs' onclick=\"EditDataDetail('chkbox_br[]', '$nnjmlrc')\">Edit</button></td>";
                                                        
                                                        echo "<td nowrap>$pnmjenis<input type='hidden' id='m_jv[$nnjmlrc]' name='m_jv[$nnjmlrc]' value='$pnmjenis'></td>";
                                                        echo "<td nowrap>$pnmdokt_<input type='hidden' id='m_nmdokt[$nnjmlrc]' name='m_nmdokt[$nnjmlrc]' value='$pnmdokt'></td>";
                                                        echo "<td >$pnotes<span hidden><textarea class='form-control' id='txt_ketdokt[$nnjmlrc]' name='txt_ketdokt[$nnjmlrc]'>$pnotes</textarea></span></td>";
                                                        echo "</tr>";

                                                        
                                                        $nnjmlrc++;

                                                    }
                                                }

                                            ?>
                                            </tbody>
                                        </table>
                                        <button type='button' class='btn btn-danger btn-xs delete-row' >&nbsp; &nbsp; Hapus Visit &nbsp; &nbsp;</button>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    
                    <div class='clearfix'></div>
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                    </div>
                                </div>

                                

                                <br/>
                                <div hidden id="div_atasan">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>'>
                                            <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>'>
                                            <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>'>
                                            <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>GSM <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>'>
                                            <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>'>
                                        </div>
                                    </div>
                                    
                                </div>

                            </div>

                        </div>
                    </div>


                </form>

            </div>

        </div>


    </div>

</div>




<script>
    function ShowAktivitas(skey) {
        var element = document.getElementById("div_akv");
        if (skey==2) {
            element.classList.remove("disabledDiv");
        }else{
            var iket=document.getElementById("cb_ketid").value;
            if (iket=="") {
                
            }else{
                element.classList.add("disabledDiv");
            }
        }
        
    }

    function EditDataAkv(xchk, xidjmlrec) {
        ShowAktivitas('2');

        var xkdket = document.getElementById('m_idket['+xidjmlrec+']').value;
        var xcompl = document.getElementById('m_compl['+xidjmlrec+']').value;
        
        var xakv = document.getElementById('txt_akv['+xidjmlrec+']').value;        
        
        document.getElementById("e_compl").value = xcompl;
        document.getElementById("cb_ketid").value = xkdket;
        document.getElementById('e_aktivitas').value=xakv;
        
        $("table tbody.inputdatatbl").find('input[id="chkbox_akvbr['+xidjmlrec+']"]').each(function(){
            $(this).parents("tr").remove();
        });

    }

    $(document).ready(function(){
        
        
        var element = document.getElementById("div_atasan");
        //element.classList.remove("disabledDiv");
        element.classList.add("disabledDiv");

        $(".add-aktv").click(function(){
            var newchar = '';
            var i_idjmlrec = $("#e_idjmlrecakv").val();
            var i_ketid = $("#cb_ketid").val();
            var i_compl = $("#e_compl").val();
            var i_akv = $("#e_aktivitas").val();

            var x = document.getElementById("cb_ketid").selectedIndex;
            var y = document.getElementById("cb_ketid").options;
            var iiket=y[x].index;
            var i_nmket=y[x].text;
            
            if (i_nmket=="--Pilih--") {
                i_nmket="";
            }
                
            if (i_ketid=="") {
                return false;
                
                var iakv = document.getElementById('e_aktivitas').value;
                if (iakv=="") {
                    return false;
                }else{
                }
            }

            var arjmlrec = document.getElementsByName('m_idjmrecakv[]');
            for (var i = 0; i < arjmlrec.length; i++) {
                var ijmlrec = arjmlrec[i].value;
                var ikdket = document.getElementById('m_idket['+ijmlrec+']').value;
                
                if (i_ketid==ikdket) {
                    return false;
                }
            }

            var markup;
            markup = "<tr>";
            markup += "<td nowrap><input type='checkbox' name='record'>";
            markup += "<input type='hidden' id='m_idjmrecakv["+i_idjmlrec+"]' name='m_idjmrecakv[]' value='"+i_idjmlrec+"' Readonly>";
            markup += "<input type='hidden' id='m_idket["+i_idjmlrec+"]' name='m_idket["+i_idjmlrec+"]' value='"+i_ketid+"'>";
            markup += "</td>";
            markup += "<td nowrap class='divnone'><input type='checkbox' name='chkbox_akvbr[]' id='chkbox_akvbr["+i_idjmlrec+"]' value='"+i_idjmlrec+"' checked></td>";
            
            markup += "<td><button type='button' class='btn btn-warning btn-xs' onclick=\"EditDataAkv('chkbox_akvbr[]', '"+i_idjmlrec+"')\">Edit</button></td>";
            
            markup += "<td nowrap>" + i_nmket + "<input type='hidden' id='m_nmket["+i_idjmlrec+"]' name='m_nmket["+i_idjmlrec+"]' value='"+i_nmket+"'></td>";
            markup += "<td nowrap>" + i_compl + "<input type='hidden' id='m_compl["+i_idjmlrec+"]' name='m_compl["+i_idjmlrec+"]' value='"+i_compl+"'></td>";
            markup += "<td >" + i_akv + "<span hidden><textarea class='form-control' id='txt_akv["+i_idjmlrec+"]' name='txt_akv["+i_idjmlrec+"]'>"+i_akv+"</textarea></span></td>";
            markup += "</tr>";
            $("table tbody.inputdatatbl").append(markup);
            
            
            if (i_idjmlrec=="") i_idjmlrec="0";
            i_idjmlrec = i_idjmlrec.split(',').join(newchar);
            i_idjmlrec=parseFloat(i_idjmlrec)+1;
            document.getElementById('e_idjmlrecakv').value=i_idjmlrec;

            ShowAktivitas('1');
            

        });


        $(".delete-aktv").click(function(){
            
            var ilewat = false;
            $("table tbody.inputdatatbl").find('input[name="record"]').each(function(){
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                    ilewat = true;
                }
            });

            if (ilewat == true) {
                
            }
            
        });




        $("#add_new").click(function(){
            $(".entry-form").fadeIn("fast");
        });

        $("#close").click(function(){
            $(".entry-form").fadeOut("fast");
        });

        $("#cancel").click(function(){
            $(".entry-form").fadeOut("fast");
        });

        $(".add-row").click(function(){
            
            var newchar = '';
            var i_idjmlrec = $("#e_idjmlrec").val();
            var i_jv = $("#cb_jv").val();
            var i_iddokt = $("#cb_doktid").val();
            var i_ket = $("#e_ketdetail").val();
            var i_cab = $("#cb_cabid").val();

            var x = document.getElementById("cb_doktid").selectedIndex;
            var y = document.getElementById("cb_doktid").options;
            var iidokt=y[x].index;
            var i_nmdokt=y[x].text;


            if (i_iddokt=="") {
                alert("dokter belum dipilih...!!!");
                return false;
            }

            var arjmlrec = document.getElementsByName('m_idjmrec[]');
            for (var i = 0; i < arjmlrec.length; i++) {
                var ijmlrec = arjmlrec[i].value;
                var ikddokt = document.getElementById('m_iddokt['+ijmlrec+']').value;
                
                if (i_iddokt==ikddokt) {
                    return false;
                }
            }
            var markup;
            markup = "<tr>";
            markup += "<td nowrap><input type='checkbox' name='record'>";
            markup += "<input type='hidden' id='m_idjmrec["+i_idjmlrec+"]' name='m_idjmrec[]' value='"+i_idjmlrec+"' Readonly>";
            markup += "<input type='hidden' id='m_iddokt["+i_idjmlrec+"]' name='m_iddokt["+i_idjmlrec+"]' value='"+i_iddokt+"'>";
            markup += "<input type='hidden' id='m_idcab["+i_idjmlrec+"]' name='m_idcab["+i_idjmlrec+"]' value='"+i_cab+"'>";
            markup += "</td>";
            markup += "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br["+i_idjmlrec+"]' value='"+i_idjmlrec+"' checked></td>";
            
            markup += "<td><button type='button' class='btn btn-warning btn-xs' onclick=\"EditDataDetail('chkbox_br[]', '"+i_idjmlrec+"')\">Edit</button></td>";
            
            markup += "<td nowrap>" + i_jv + "<input type='hidden' id='m_jv["+i_idjmlrec+"]' name='m_jv["+i_idjmlrec+"]' value='"+i_jv+"'></td>";
            markup += "<td nowrap>" + i_nmdokt + "<input type='hidden' id='m_nmdokt["+i_idjmlrec+"]' name='m_nmdokt["+i_idjmlrec+"]' value='"+i_nmdokt+"'></td>";
            markup += "<td >" + i_ket + "<span hidden><textarea class='form-control' id='txt_ketdokt["+i_idjmlrec+"]' name='txt_ketdokt["+i_idjmlrec+"]'>"+i_ket+"</textarea></span></td>";
            markup += "</tr>";
            $("table tbody.inputdata").append(markup);
            
            
            if (i_idjmlrec=="") i_idjmlrec="0";
            i_idjmlrec = i_idjmlrec.split(',').join(newchar);
            i_idjmlrec=parseFloat(i_idjmlrec)+1;
            document.getElementById('e_idjmlrec').value=i_idjmlrec;
            

        });

        $(".delete-row").click(function(){
            
            var ilewat = false;
            $("table tbody.inputdata").find('input[name="record"]').each(function(){
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                    ilewat = true;
                }
            });

            if (ilewat == true) {
                
            }
            
        });

    });


    function EditDataDetail(xchk, xidjmlrec) {
        var xkddokt = document.getElementById('m_iddokt['+xidjmlrec+']').value;
        var xkdjv = document.getElementById('m_jv['+xidjmlrec+']').value;
        var xkdcab = document.getElementById('m_idcab['+xidjmlrec+']').value;
        
        var xket = document.getElementById('txt_ketdokt['+xidjmlrec+']').value;        
        
        document.getElementById("cb_jv").value = xkdjv;
        document.getElementById("cb_doktid").value = xkddokt;
        document.getElementById('e_ketdetail').value=xket;
        document.getElementById('cb_cabid').value=xkdcab;
        
        ShowDataDokter('2', xkdcab, xkddokt);

        $("table tbody.inputdata").find('input[id="chkbox_br['+xidjmlrec+']"]').each(function(){
            $(this).parents("tr").remove();
        });
        
    }


    function disp_confirm(pText_,ket)  {
        
        var ikeperluan = document.getElementById('cb_ketid').value;
        if (ikeperluan=="") {
            disp_confirm_ext(pText_,ket);
        }else{
            document.getElementById('btnakv').click();
            
            setTimeout(function () {
                disp_confirm_ext(pText_,ket)
            }, 200);
        }
    }
    
    function disp_confirm_ext(pText_,ket)  {
        var ikeperluan = document.getElementById('cb_ketid').value;
        var iid = document.getElementById('e_id').value;
        var ijmldata = document.getElementById('e_idjmlrec').value;
        var itgl = document.getElementById('e_periode1').value;
        var itgledit = document.getElementById('e_idtgledit').value;
        var ikaryawan = document.getElementById('e_idcarduser').value;
        var ijbtid = document.getElementById('e_idjbt').value;
        
        if (ikeperluan=="000" || ikeperluan=="") {
            if (ikeperluan=="" && ijmldata<=1) {
                
                alert("keperluan harus diisi atau dokter harus dipilih (tambah visit)...");
                return false;
                
                var iakv = document.getElementById('e_aktivitas').value;
                if (iakv=="") {
                    alert("aktivitas harus diisi atau dokter harus dipilih (tambah visit)...");
                    return false;
                }
            }else{
                if (ijmldata<=1) {
                    alert("Dokter belum dipilih (tambah visit)...");
                    return false;
                }
            }
        }

        if (ijbtid=="") {
            alert("Jabatan kosong...");
            return false;
        }

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var iact = urlku.searchParams.get("act");
        //alert(iact);
        $.ajax({
            type:"post",
            url:"module/dkd/viewdatadkd.php?module=cekdatasudahadabytgl",
            data:"uact="+iact+"&uid="+iid+"&utgl="+itgl+"&ukaryawan="+ikaryawan+"&utgledit="+itgledit,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;

                if (data=="boleh" || data=="aktivitas" || data=="call") {
                    
                    if (data=="aktivitas") {
                        
                        var iakv = document.getElementById('e_aktivitas').value;
                        //if (iakv=="") {
                        if (ikeperluan=="") {
                            pText_="yang disimpan hanya AKTIVITAS, silakan isi terlebih dahulu keperluannya, \n\
untuk VISIT tanggal tersebut sudah ada inputan.";
                            alert(pText_); return false;
                        }else{
                            pText_="yang disimpan hanya AKTIVITAS, \n\
untuk VISIT tanggal tersebut sudah ada inputan.\n\
Apakah akan melanjutkan...?";
                        }
                
                    }else if (data=="call") {
                        if (ijmldata<=1) {
                            pText_="yang disimpan hanya VISIT, silakan pilih dokter (tambah visit)... \n\
untuk AKTIVITAS tanggal tersebut sudah ada inputan.";
                            alert(pText_); return false;
                        }else{
                            pText_="yang disimpan hanya VISIT, \n\
untuk AKTIVITAS tanggal tersebut sudah ada inputan.\n\
Apakah akan melanjutkan...?";
                        }
                    }
                    
                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            //document.write("You pressed OK!")
                            document.getElementById("form_data1").action = "module/dkd/dkd_wekvisitplan/aksi_wekvisitplan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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


<script type="text/javascript">
    $(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var idact = urlku.searchParams.get("act");
        if (idact=="editdata") {
            //document.getElementById('btnakv').click();
            ShowAktivitas(1);
        }//else{
            var dateToday = new Date();
            var dayToday = dateToday.getDay();
            var setMinDate=8-dayToday;

            $('#e_periode1').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                ////firstDay: 1,
                ////minDate: "1W",
                minDate: setMinDate, 
                ////maxDate: "+2W -3D",
                dateFormat: 'dd MM yy',
                onSelect: function(dateStr) {
                    
                }
            });
        //}

    });
</script>

<script src="vendors/jquery/dist/jquery.min.js"></script>
<link href="module/dkd/select2.min.css" rel="stylesheet" type="text/css" />
<script src="module/dkd/select2.min.js"></script>
<script>
$(document).ready(function() {
        $('.s2').select2();
    });
</script>