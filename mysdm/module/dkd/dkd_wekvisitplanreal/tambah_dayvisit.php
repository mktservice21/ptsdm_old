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

// END CABANG & ATASAN



$pidinput="";


$hari_ini = date('Y-m-d');
$pdate = date('Y-m-d', strtotime('+1 days', strtotime($hari_ini)));
$pnewdate = strtotime ( 'monday 0 week' , strtotime ( $pdate ) ) ;
//$tgl_pertama = date ( 'd F Y' , $pnewdate );
$tgl_pertama = date('d F Y');
$tglnow = date('Y-m-d');

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
                        
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
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
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Join Visit <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_jv' id='cb_jv' onchange="">
                                            <?php
                                            echo "<option value='' selected>N</option>";
                                            echo "<option value='JV'>Y</option>";
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_doktid' id='cb_doktid' onchange="">
                                            <?php
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saran <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id="e_saran" name='e_saran' maxlength='300'></textarea>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
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
                                                    <th width='5px' align='center'>Tanggal</th>
                                                    <th width='5px' align='center'>JV</th>
                                                    <th width='200px' align='center'>Nama Dokter</th>
                                                    <th width='200px' align='center'>Notes</th>
                                                    <th width='200px' align='center'>Saran</th>
                                                    <th align='center'>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody class='inputdata'>
                                            <?PHP
                                                $nnjmlrc=0;
                                                
                                                    
                                                    $query = "SELECT a.*, b.namalengkap as nama_dokter, b.gelar, b.spesialis, b.icabangid FROM hrd.dkd_new_real1 as a
                                                        LEFT JOIN dr.masterdokter as b on a.dokterid=b.id 
                                                         WHERE a.tanggal='$tglnow' and a.karyawanid='$pidcard'";
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
                                                        
                                                        $pnmjenis='N';
                                                        if ($pjenis=="JV") $pnmjenis='Y';

                                                        $pnmdokt_=$pnmdokt."(".$pgelardokt.") ".$pspesdokt;
                                                        
                                                        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapusDokt('hapusdailydokt', '$pkaryawanid', '$ntgl', '$pdokterid')\">";
                                                        
                                                        echo "<tr>";
                                                        echo "<td nowrap>$ntglinput</td>";
                                                        echo "<td nowrap>$pnmjenis</td>";
                                                        echo "<td nowrap>$pnmdokt_ - $pdokterid</td>";
                                                        echo "<td >$pnotes</td>";
                                                        echo "<td >$psaran</td>";
                                                        echo "<td >$phapus</td>";
                                                        echo "</tr>";

                                                        
                                                        $nnjmlrc++;

                                                    }
                                                

                                            ?>
                                            </tbody>
                                        </table>

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
    
    function disp_confirm(pText_,ket)  {
        var iid = document.getElementById('e_id').value;
        var idoktid = document.getElementById('cb_doktid').value;
        var itgl = document.getElementById('e_periode1').value;
        var ikaryawan = document.getElementById('e_idcarduser').value;

        if (idoktid=="") {
            alert("dokter kosong...");
            return false;
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

                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");
                            //document.write("You pressed OK!")
                            document.getElementById("form_data1").action = "module/dkd/dkd_wekvisitplanreal/aksi_wekvisitplanreal.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
                document.getElementById("form_data1").action = "module/dkd/dkd_wekvisitplanreal/aksi_wekvisitplanreal.php?module="+module+"&idmenu="+idmenu+"&ket=hapus&act="+ket+"&ukryid="+kryid+"&utgl="+tgl+"&udokt="+doktid;
                document.getElementById("form_data1").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
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
