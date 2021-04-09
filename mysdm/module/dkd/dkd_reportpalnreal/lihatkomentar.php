<?PHP
session_start();
$aksi="";
$psts=$_POST['usts'];
$pidinput=$_POST['unourut'];
$pkryid=$_POST['uidkry'];
$ptgl=$_POST['utgl'];
$pudoktid=$_POST['udoktid'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];


$tgl_pertama = date('d F Y', strtotime($ptgl));
$itgl = date('Y-m-d', strtotime($ptgl));
        
include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_sql.php";

if ($psts=="plan") {
    $sql = "select c.nourut, a.idinput, a.karyawanid, e.nama as namakaryawan, a.jabatanid, a.tanggal, a.tglinput, 
        c.dokterid, d.namalengkap, d.gelar, d.spesialis, c.jenis, c.notes, c.saran 
        FROM hrd.dkd_new0 as a LEFT JOIN hrd.dkd_new1 as c on a.idinput=c.idinput 
        JOIN dr.masterdokter as d on c.dokterid=d.id JOIN hrd.karyawan as e on a.karyawanid=e.karyawanId 
        WHERE a.karyawanid='$pkryid' AND a.tanggal='$itgl' AND c.dokterid='$pudoktid'";
}else{
    $sql = "select a.karyawanid, c.nama as namakaryawan, a.tanggal, a.tglinput, 
        a.dokterid, d.namalengkap, d.gelar, d.spesialis, a.jenis, a.notes, a.saran
        FROM hrd.dkd_new_real1 as a JOIN dr.masterdokter as d on a.dokterid=d.id 
        LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
        WHERE a.karyawanid='$pkryid' AND a.tanggal='$itgl' AND a.dokterid='$pudoktid' ";
}
$pisql=$sql;
$tampil=mysqli_query($cnmy, $sql);
$row= mysqli_fetch_array($tampil);
$pnmkaryawan= $row['namakaryawan'];
$pnmdokt= $row['namalengkap'];
$pnotes= $row['notes'];
$psaran= $row['saran'];

$pkomen=""; $pusrkomen="";
$pkomen_am=""; $pusrkomen_am="";
$pkomen_dm=""; $pusrkomen_dm="";
$pkomen_sm=""; $pusrkomen_sm="";
$pkomen_gsm=""; $pusrkomen_gsm="";

$query = "select * from hrd.dkd_new_real1_komen WHERE nourut='$pidinput' AND `sts`='$psts'";
$tampil1=mysqli_query($cnmy, $query);
while ($row1= mysqli_fetch_array($tampil1)) {
    $pjbkomen=$row1['jabatanid'];
    $pikomen=$row1['komentar'];
    $pikoenuser=$row1['komen_user'];
    if ($pjbkomen=="10" OR $pjbkomen=="18") {
        $pkomen_am= $pikomen;
        $pusrkomen_am= $pikoenuser;
    }elseif ($pjbkomen=="08") {
        $pkomen_dm= $pikomen;
        $pusrkomen_dm= $pikoenuser;
    }elseif ($pjbkomen=="20") {
        $pkomen_sm= $pikomen;
        $pusrkomen_sm= $pikoenuser;
    }elseif ($pjbkomen=="05") {
        $pkomen_gsm= $pikomen;
        $pusrkomen_gsm= $pikoenuser;
    }else{
        $pkomen= $pikomen;
        $pusrkomen= $pikoenuser;
    }
}

$phiddensave="";
if (!empty($pusrkomen)) {
    if ($pusrkomen==$pidcard) {
    }else{
        //$phiddensave="hidden";
    }
}
?>

<!--
<script>

setInterval(function() {
    var ests = document.getElementById("e_idstatus").value;
    var eidinput = document.getElementById("e_idinput").value;
    ShowDataKomentar(ests, eidinput);
}, 500);

</script>
    
<meta http-equiv="refresh" content="30"/>
-->

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Komentar</h4>
        </div>
        <br/>
        <div class="">
            
            <?PHP //echo $pisql; ?>
            
            <div class="row">

                <div class="col-md-6 col-sm-6 col-xs-12">

                    <div class="x_panel">
                        
                        <div class="x_title">
                          <h2><?PHP echo $pnmdokt; ?> <small><?PHP echo "$pnmkaryawan ($tgl_pertama)"; ?></small></h2>
                          <div class="clearfix"></div>
                        </div>
                        
                        <div class="x_content">
                            <div class="dashboard-widget-content">

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-9'>
                                        <input type='text' id='e_idinput' name='e_idinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idstatus' name='e_idstatus' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psts; ?>' Readonly>
                                        <input type='text' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                    </div>
                                </div>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-md-9'>
                                    <div class='input-group date' id=''>
                                        <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>

                                    </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-md-9'>
                                        <input type='hidden' id='e_idkry' name='e_idkry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkryid; ?>' Readonly>
                                        <input type='text' id='e_namakry' name='e_namakry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmkaryawan; ?>' Readonly>
                                    </div>
                                </div>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                    <div class='col-md-9'>
                                        <input type='hidden' id='e_doktid' name='e_doktid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pudoktid; ?>' Readonly>
                                        <input type='text' id='e_doktnm' name='e_doktnm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmdokt; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-md-9'>
                                        <input type='hidden' id='e_komenidkry' name='e_komenidkry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_komennamakry' name='e_komennamakry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamalengkap; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Komentar <span class='required'></span></label>
                                    <div class='col-md-9'>
                                        <textarea class='form-control' id="e_komen" name='e_komen' maxlength='300'><?PHP echo ""; ?></textarea>
                                    </div>
                                </div>


                                <div <?PHP echo $phiddensave; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_notes("Simpan ?", "simapn")'>Save</button>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                        </div>
                        
                    </div>

                </div>
                
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="row">
                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                
                                <div class="x_title">
                                  <h2>List Komentar <small>&nbsp;</small></h2>
                                  <div class="clearfix"></div>
                                </div>

                                <div class="x_content" style="overflow: scroll; max-height: 300px;">
                                    <div class="dashboard-widget-content">

                                        <ul id="div_komentar" class="list-unstyled timeline widget">
                                            <?PHP
                                            $query = "select a.*, b.nama as namakoentar, b.jabatanId as jabatanid, c.nama as namajabatan "
                                                    . " from hrd.dkd_new_real1_komen as a "
                                                    . " JOIN hrd.karyawan as b on a.komen_user=b.karyawanId "
                                                    . " LEFT JOIN hrd.jabatan as c on b.jabatanId=c.jabatanId "
                                                    . " WHERE a.nourut='$pidinput' AND a.`sts`='$psts' order by komen_date DESC";
                                            $tampil1=mysqli_query($cnmy, $query);
                                            while ($row1= mysqli_fetch_array($tampil1)) {
                                                $pjbkomen=$row1['jabatanid'];
                                                $pikomen=$row1['komentar'];
                                                $pikoentgl=$row1['komen_date'];
                                                $pikoenuser=$row1['komen_user'];
                                                $pikoenusernm=$row1['namakoentar'];
                                                $pikoenuserjbt=$row1['namajabatan'];
                                                
                                                $pikoentgl = date('d F Y H:i:s', strtotime($pikoentgl));
                                                
                                                echo "<li>";
                                                    echo "<div class='block'>";
                                                        echo "<div class='block_content'>";
                                                        
                                                            echo "<h2 class='title' style='font-size:11px; font-weight:bold;'>";
                                                                echo "$pikoenusernm <span class='byline'>$pikoentgl</span> ";
                                                            echo "</h2>";
                                                            /*
                                                            echo "<div class='byline'>";
                                                                echo "<span>$pikoentgl</span> ";
                                                            echo "</div>";
                                                            */
                                                            echo "<p class=excerpt'>";
                                                                echo "$pikomen";
                                                            echo "</p>";
                                                            
                                                        echo "</div>";
                                                    echo "</div>";
                                                    
                                                echo "</li>";
                                            }
                                            ?>
                                            <!--
                                            <li>
                                                <div class="block">
                                                    <div class="block_content">
                                                        <h2 class="title">
                                                            <a>Who Needs Sundance When You’ve Got&nbsp;Crowdfunding?</a>
                                                        </h2>
                                                        <div class="byline">
                                                            <span>13 hours ago</span> by <a>Jane Smith</a>
                                                        </div>
                                                        <p class="excerpt">
                                                            Film festivals used to be do-or-die moments for movie makers. 
                                                            They were where you met the producers that could fund your project, 
                                                            and if the buyers liked your flick, they’d pay to Fast-forward and… <a>Read&nbsp;More</a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                            -->
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
        
    </div>
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script>
    function disp_confirm_notes(pText_,nid)  {
        // pText_, nid e_idkry, e_periode1, e_doktid, e_komen, e_idcarduser
        var eact="inputkomentar";
        var ests = document.getElementById("e_idstatus").value;
        var eidinput = document.getElementById("e_idinput").value;
        var eidkry = document.getElementById("e_idkry").value;
        var etgl = document.getElementById("e_periode1").value;
        var edoktid = document.getElementById("e_doktid").value;
        var eiduserinput = document.getElementById("e_idcarduser").value;
        var eidjbtinput = document.getElementById("e_idjbt").value;
        var ekomen = document.getElementById("e_komen").value;
        
        
        if (eiduserinput=="") {
            alert("Anda harus login ulang...");
            return false;
        }
        
        if (eidinput=="") {
            alert("Id kosong...");
            return false;
        }
        
        if (eidkry=="") {
            alert("karyawan kosong...");
            return false;
        }
        
        if (etgl=="") {
            alert("tanggal kosong...");
            return false;
        }
        
        if (edoktid=="") {
            alert("dokter kosong...");
            return false;
        }
        
        if (ekomen=="") {
            alert("komentar masih kosong...");
            return false;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                $.ajax({
                    type:"post",
                    url:"module/dkd/dkd_reportpalnreal/simpan_komentar.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"usts="+ests+"&uidinput="+eidinput+"&uidkry="+eidkry+"&utgl="+etgl+"&udoktid="+edoktid+"&uiduserinput="+eiduserinput+"&ukomen="+ekomen+"&uidjbtinput="+eidjbtinput,
                    success:function(data){
                        ShowDataKomentar(ests, eidinput);
                        /*
                        if (data.length > 2) {
                            alert(data);
                        }
                        nm_btn_save.style.display='none';
                        $('#myModal').modal('hide');
                         */
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function ShowDataKomentar(ests, enoid) {
        $.ajax({
            type:"post",
            url:"module/dkd/viewdatadkd.php?module=viewdatakomentar",
            data:"usts="+ests+"&unoid="+enoid,
            success:function(data){
                $("#div_komentar").html(data);
            }
        });
    }
</script>

<?PHP
mysqli_close($cnmy);
?>