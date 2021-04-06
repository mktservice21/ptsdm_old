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
                            <input class='btn btn-default' type=button value='Lihat Realisasi Activity'
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keperluan <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_ketid' id='cb_ketid' onchange="">
                                            <?php
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
                                                    <th width='200px' align='center'>Keperluan</th>
                                                    <th width='200px' align='center'>Compl.</th>
                                                    <th width='200px' align='center'>Aktivitas</th>
                                                    <th align='center'>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody class='inputdata'>
                                            <?PHP
                                                $nnjmlrc=0;
                                                
                                                    
                                                    $query = "SELECT a.*, b.nama as nama_ket FROM hrd.dkd_new_real0 as a
                                                        LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
                                                         WHERE a.tanggal='$tglnow' and a.karyawanid='$pidcard'";
                                                    $tampild=mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $ntglinput=$nrd['tglinput'];
                                                        $ntgl=$nrd['tanggal'];
                                                        $nnmket=$nrd['nama_ket'];
                                                        $pkaryawanid=$nrd['karyawanid'];
                                                        $nketid=$nrd['ketid'];
                                                        $ncompl=$nrd['compl'];
                                                        $naktivitas=$nrd['aktivitas'];
                                                        
                                                        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapusDokt('hapusdailyact', '$pkaryawanid', '$ntgl', '$nketid')\">";
                                                        
                                                        echo "<tr>";
                                                        echo "<td nowrap>$ntglinput</td>";
                                                        echo "<td nowrap>$nnmket</td>";
                                                        echo "<td >$ncompl</td>";
                                                        echo "<td >$naktivitas</td>";
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
        var idket = document.getElementById('cb_ketid').value;
        var itgl = document.getElementById('e_periode1').value;
        var ikaryawan = document.getElementById('e_idcarduser').value;

        if (idket=="") {
            alert("keperluan kosong...");
            return false;
        }

        $.ajax({
            type:"post",
            url:"module/dkd/viewdatadkd.php?module=cekdatasudahadarealactivity",
            data:"uid="+iid+"&utgl="+itgl+"&ukaryawan="+ikaryawan+"&uidket="+idket,
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
                            document.getElementById("form_data1").action = "module/dkd/dkd_wekvisitactreal/aksi_wekvisitactreal.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
                document.getElementById("form_data1").action = "module/dkd/dkd_wekvisitactreal/aksi_wekvisitactreal.php?module="+module+"&idmenu="+idmenu+"&ket=hapus&act="+ket+"&ukryid="+kryid+"&utgl="+tgl+"&udokt="+doktid;
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
