<?PHP
    session_start();
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    
    $_SESSION['PITIPE']="";
    $_SESSION['PITGLTIPE']=$_POST['utgltipe'];
    $_SESSION['PIPERENTY1']=$_POST['uperiode1'];
    $_SESSION['PIPERENTY2']=$_POST['uperiode2'];
    $_SESSION['PIDIVISI']=$_POST['udivisi'];
    $_SESSION['PIINCFROM']=$_POST['uincfrom'];
    
    $figroupuser=$_SESSION['GROUP'];
    
    $pincfrom=$_POST['uincfrom'];
    $pdivprod=$_POST['udivisi'];
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $ptgl1= date("Y-m-01", strtotime($date1));
    $ptahun= date("Y", strtotime($date1));
    $tgl2= date("Y-m-01", strtotime($date2));
    
    echo "<input type='hidden' name='cb_tgltipe' id='cb_tgltipe' value='$tgltipe'>";
    echo "<input type='hidden' name='xtgl1' id='xtgl1' value='$ptgl1'>";
    echo "<input type='hidden' name='xtgl2' id='xtgl2' value='$tgl2'>";
    echo "<input type='hidden' name='x_incfrom' id='x_incfrom' value='$pincfrom'>";
    
        $nfildivisi="";
        if (!empty($pdivprod)) $nfildivisi=" AND IFNULL(divisi,'')='$pdivprod'";
        if ($pdivprod=="blank") $nfildivisi=" AND IFNULL(divisi,'')=''";
    
    $pfilterincfrom=" AND IFNULL(jenis2,'')='$pincfrom' ";
    $pfilterincfrom2=" AND IFNULL(a.jenis2,'')='$pincfrom' ";
    if ($pincfrom=="PM") {
        $pfilterincfrom=" AND IFNULL(jenis2,'') NOT IN ('GSM', '') ";
        $pfilterincfrom2=" AND IFNULL(a.jenis2,'') NOT IN ('GSM', '') ";
    }

    if ((INT)$ptahun<=2020) {
        $pfilterincfrom="";
        $pfilterincfrom2="";
    }

    $nhilang1="";
    $nhilang2="hidden";
    $sudahproses=false;
    $query = "SELECT * FROM ms.incentiveperdivisi WHERE bulan='$ptgl1' $nfildivisi $pfilterincfrom";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $sudahproses=true;
        $nhilang1="hidden";
        $nhilang2="";
    }
    
    if ($figroupuser=="28") $sudahproses=true;
        
    $now=date("mdYhis");
    $tmp01 =" dbtemp.RTMPPROSINC01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.RTMPPROSINC02_".$_SESSION['USERID']."_$now ";
    
    if ($sudahproses==false) {
        
        include "prosesdatainc.php";

        $tmp01 =caridatainsentif_query($cnmy, "", $ptgl1, "", $pdivprod, $pincfrom);

        $query = "SELECT table_name FROM information_schema.tables WHERE table_name='$tmp01'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu==0) {
            goto hapusdata;
        }
        $tmp01="dbtemp.".$tmp01;
        
    }else{
        
        $fildivisi="";
        if (!empty($pdivprod)) $fildivisi=" AND IFNULL(a.divisi,'')='$pdivprod'";
        if ($pdivprod=="blank") $fildivisi=" AND IFNULL(a.divisi,'')=''";
        
        $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
                . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah FROM ms.incentiveperdivisi a "
                . " LEFT JOIN sls.icabang b on a.cabang=b.iCabangId WHERE a.bulan='$ptgl1' $fildivisi $pfilterincfrom2";
        //echo $query; goto hapusdata;
        $query = "create  table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query="UPDATE $tmp01 SET urutan=1 WHERE jabatan='MR'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp01 SET urutan=2 WHERE jabatan='AM'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp01 SET urutan=3 WHERE jabatan='DM'";
        mysqli_query($cnmy, $query);
    }
    
    //if ($figroupuser=="28") {
        $query="DELETE FROM $tmp01 WHERE IFNULL(jumlah,0)=0";
        mysqli_query($cnmy, $query);
    //}
    $ntotblank=0;
    $ntotcan=0;
    $ntoteagle=0;
    $ntotpeaco=0;
    $ntotpigeo=0;
    $query = "SELECT IFNULL(divisi,'') divisi, sum(jumlah) jumlah FROM $tmp01 group by 1";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        while ($nr= mysqli_fetch_array($tampil_)) {
            $nr_divisi=$nr['divisi'];
            if (empty($nr_divisi)) $ntotblank=$nr['jumlah'];
            if ($nr_divisi=="CAN") $ntotcan=$nr['jumlah'];
            if ($nr_divisi=="EAGLE") $ntoteagle=$nr['jumlah'];
            if ($nr_divisi=="PEACO") $ntotpeaco=$nr['jumlah'];
            if ($nr_divisi=="PIGEO") $ntotpigeo=$nr['jumlah'];
        }
    }
    $grandtotal_div=(double)$ntotblank+(double)$ntotcan+(double)$ntoteagle+(double)$ntotpeaco+(double)$ntotpigeo;
?>
    
<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledtinc').DataTable( {
            "ordering": false,
            bFilter: true, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false,
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6] }//nowrap

            ],
            "scrollY": 440,
            "scrollX": true
        } );
    });
</script>


<style>
    .divnone {
        display: none;
    }
    #datatabledtinc th {
        font-size: 13px;
    }
    #datatabledtinc td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='mstprosesinsentif'&act=input&idmenu=262"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatabledtinc' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>NO</th>
                    <th width='50px'>Divisi</th>
                    <th width='20px'>Region</th>
                    <th width='20px'>Cabang</th>
                    <th width='30px'>Jabatan</th>
                    <th width='50px'>Karyawan</th>
                    <th width='200px'>Jumlah</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $gtotalnone=0;
                $gtotalmr=0;
                $gtotalam=0;
                $gtotaldm=0;
                
                $no=1;
                $query = "select distinct urutan from $tmp01 order by urutan";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $nurutan=$row1['urutan'];
                
                    $query = "select * from $tmp01 WHERE urutan='$nurutan' order by urutan, divisi, region, cabang, nama";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {

                        $pdivisi=$row['divisi'];
                        $ndivisi=$pdivisi;
                        if ($pdivisi=="CAN") $ndivisi="CANARY";
                        
                        $pregion=$row['region'];
                        $pidcabang=$row['icabangid'];
                        $pnmcabang=$row['cabang'];
                        $pjabatan=$row['jabatan'];
                        $pidkaryawan=$row['karyawanid'];
                        $pnmkaryawan=$row['nama'];
                        $pjumlah=$row['jumlah'];
                        
                        
                        //$gtotalnone=(double)$gtotalnone+(double)$gtotalnone;
                        if ($nurutan=="1") $gtotalmr=(double)$gtotalmr+(double)$pjumlah;
                        if ($nurutan=="2") $gtotalam=(double)$gtotalam+(double)$pjumlah;
                        if ($nurutan=="3") $gtotaldm=(double)$gtotaldm+(double)$pjumlah;
                        
                        $pjumlah=number_format($pjumlah,0,",",",");
                        
                        if ($sudahproses==true AND empty($pdivisi) AND $figroupuser!="28") {
                            $simpandata= "<input type='button' class='btn btn-warning btn-xs' id='s-submit' value='Update' onclick=\"SimpanDataDivisiInc('input', 'e_blnthn$no', 'e_kryid$no', 'cb_div$no')\">";
                            $ndivisi="<table>";
                            $ndivisi .="<tr>";
                            
                                $ndivisi .="<td>";
                                    $ndivisi .="<select id='cb_div$no' name='cb_div$no' class=''>";
                                        $ndivisi .="<option value='CAN'>CANARY</option>";
                                        $ndivisi .="<option value='EAGLE'>EAGLE</option>";
                                        $ndivisi .="<option value='PEACO'>PEACOCK</option>";
                                        $ndivisi .="<option value='PIGEO'>PIGEON</option>";
                                    $ndivisi .="</select>";
                                $ndivisi .="</td>";
                                
                                $ndivisi .="<td>";
                                    $ndivisi .="<input type='hidden' size='27px' id='e_blnthn$no' name='e_blnthn$no' value='$ptgl1'>";
                                    $ndivisi .="<input type='hidden' size='27px' id='e_kryid$no' name='e_kryid$no' value='$pidkaryawan'>";
                                    $ndivisi .="&nbsp; $simpandata";
                                $ndivisi .="</td>";
                                
                            $ndivisi .="</tr>";
                            $ndivisi .="</table>";
                        }
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$ndivisi</td>";
                        echo "<td nowrap>$pregion</td>";
                        echo "<td nowrap>$pnmcabang</td>";
                        echo "<td nowrap>$pjabatan</td>";
                        echo "<td nowrap>$pnmkaryawan</td>";
                        echo "<td nowrap>$pjumlah</td>";
                        echo "</tr>";

                        $no++;
                    }
                    
                }
            ?>
            </tbody>
        </table>

    </div>
    
</form>

<?PHP
    
    $grandtotal=(double)$gtotalmr+(double)$gtotalam+(double)$gtotaldm;
    $gtotalmr=number_format($gtotalmr,0,",",",");
    $gtotalam=number_format($gtotalam,0,",",",");
    $gtotaldm=number_format($gtotaldm,0,",",",");
    $grandtotal=number_format($grandtotal,0,",",",");
    
    $ntotblank=number_format($ntotblank,0,",",",");
    $ntotcan=number_format($ntotcan,0,",",",");
    $ntoteagle=number_format($ntoteagle,0,",",",");
    $ntotpeaco=number_format($ntotpeaco,0,",",",");
    $ntotpigeo=number_format($ntotpigeo,0,",",",");
    $grandtotal_div=number_format($grandtotal_div,0,",",",");
    
    $nbulan_=date("F Y", strtotime($ptgl1));
?>

<form method='POST' action='<?PHP echo "?module='mstprosesinsentif'&act=input&idmenu=262"; ?>' id='d-form3' name='form3' data-parsley-validate class='form-horizontal form-label-left'>
<div id='loading2'></div>
<div class='x_panel'>
    <div class='x_content'>
        <div class='col-md-12 col-sm-12 col-xs-12'>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='hidden' id='e_per1' name='e_per1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgl1; ?>' Readonly>
                    <input type='hidden' id='e_divp' name='e_divp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivprod; ?>' Readonly>
                    <input type='hidden' id='e_frominc' name='e_frominc' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pincfrom; ?>' Readonly>
                    
                </div>
            </div>

            <!--kiri-->
            <div class='col-md-6 col-xs-12'>
                <div class='x_panel'>
                    <div class='x_content form-horizontal form-label-left'>
                                
                                
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total MR <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_totmr' name='e_totmr' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotalmr; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total AM<span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_totam' name='e_totam' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotalam; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total DM <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_totdm' name='e_totdm' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotaldm; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Grand Total Jabatan <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_total' name='e_total' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $grandtotal; ?>' Readonly>
                            </div>
                        </div>
                        
                        <?PHP if ($figroupuser!="28") { ?>
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div class="checkbox">
                                        <div <?PHP echo $nhilang1; ?> id="nsave"><button type='button' class='btn btn-success' id="btnsave" name="btnsave" onclick='disp_confirm("Apakah akan Proses Data.. ?", "<?PHP echo "input"; ?>")'>Proses Insentif Bulan <?PHP echo $nbulan_; ?></button></div>
                                        <div <?PHP echo $nhilang2; ?> id="nhapus"><button type='button' class='btn btn-danger' id="btnhapus" name="btnhapus" onclick='disp_confirm("Apakah akan Hapus Data.. ?", "<?PHP echo "hapus"; ?>")'>Hapus Proses Insentif Bulan <?PHP echo $nbulan_; ?></button></div>
                                    </div>
                                </div>
                            </div>
                        <?PHP } ?>
                        
                    </div>
                </div>
            </div>
                  
                
            <!--kanan-->
            <div class='col-md-6 col-xs-12'>
                <div class='x_panel'>
                    <div class='x_content form-horizontal form-label-left'>
                                
                        <?PHP if ((double)$ntotblank>0) { ?>
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-6 col-xs-12' for=''><span style="color:red;">Total Belum Ada Divisi </span><span class='required'></span></label>
                                <div class='col-xs-6'>
                                    <input type='text' id='e_totnondiv' name='e_totnondiv' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ntotblank; ?>' Readonly>
                                </div>
                            </div>
                        <?PHP } ?>
                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-6 col-xs-12' for=''>Total CANARY <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_totcan' name='e_totcan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ntotcan; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-6 col-xs-12' for=''>Total EAGLE<span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_toteagle' name='e_toteagle' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ntoteagle; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total PEACOCK <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_totpeaco' name='e_totpeaco' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ntotpeaco; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total PIGEON <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_totpigeo' name='e_totpigeo' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ntotpigeo; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Grand Total Divisi <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <input type='text' id='e_totaldiv' name='e_totaldiv' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $grandtotal_div; ?>' Readonly>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </div>
            </div>
            
            
        </div>
    </div>
</div>
</form>

<script>
    function disp_confirm(pText_, eact)  {
        
        var etgl =document.getElementById('e_per1').value;
        var edivisi =document.getElementById('e_divp').value;
        var eincfm =document.getElementById('e_frominc').value;

        if (etgl=="") {
            alert("Periode kosong...");
            return false;    
        }

        if (eincfm=="") {
            alert("Incentive From nya kosong...");
            return false;    
        }
        
        //alert(etgl+", "+pText_+", "+eact); return false;
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/md_m_prosesdatainsentif/aksi_prosesdatainsentif.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"utgl="+etgl+"&uact="+eact+"&udivisi="+edivisi+"&uincfm="+eincfm,
                    success:function(data){
                        $("#loading2").html("");
                        alert(data);
                        //KlikDataTabel();
                        if (eact=="input") {
                            nsave.style.display = 'none';
                            nhapus.style.display = 'block';
                        }else{
                            nsave.style.display = 'block';
                            nhapus.style.display = 'none';
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function SimpanDataDivisiInc(eact, abulan, akry, adivisi)  {
        var etgl =document.getElementById(abulan).value;
        var ekry =document.getElementById(akry).value;
        var edivisi =document.getElementById(adivisi).value;
        
        if (etgl==""){
            alert("periode kosong....");
            return 0;
        }
        if (ekry==""){
            alert("karyawan kosong....");
            return 0;
        }
        if (edivisi==""){
            alert("divisi kosong....");
            return 0;
        }
        
        //alert(etgl+", "+ekry+", "+edivisi); return 0;
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        
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
                    url:"module/md_m_prosesdatainsentif/simpandataincdivisi.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"utgl="+etgl+"&ukry="+ekry+"&udivisi="+edivisi,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
?>