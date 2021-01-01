<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
    
<?PHP
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    $pact=$_GET['act'];
    $pidklaim=$_POST['uid'];
    $pdivisi=$_POST['udivisi'];
    $pdistid=$_POST['udistid'];
    $pregion=$_POST['uregion'];
    $pjmlppn=$_POST['uppn'];
    $pjmlpph=$_POST['upph'];
    
    
    $pigroup=$_SESSION['GROUP'];
    if (empty($pregion)) {
        //$pregion="B";
        //if ($pigroup=="40") $pregion="T";
    }
    
    $ptotrpklaim=0;
    $ptotrpreal=0;
    $ptotrptolak=0;
    
    $ptotrpsusulan=0;

    $pppnrpklaim=0;
    $pppnrpreal=0;
    $pppnrptolak=0;

    $ptotppnrpklaim=0;
    $ptotppnrpreal=0;
    $ptotppnrptolak=0;

    $ppphrpklaim=0;
    $ppphrpreal=0;
    $ppphrptolak=0;

    $pgrdrpklaim=0;
    $pgrdrpreal=0;
    $pgrdrptolak=0;

    $pbulatklaim=0; $pbulatreal=0; $pbulattolak=0;

    $pjmlkuranglebih=0;

    if ($pact=="editdata") {
        $edit = mysqli_query($cnmy, "SELECT * FROM hrd.klaim WHERE klaimId='$pidklaim'");
        $r    = mysqli_fetch_array($edit);
        
        $pjmlkuranglebih=$r['jmlkuranglebih'];
        $pbulatreal=$r['pembulatan'];
    }

    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptkscbdt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptkscbdt01_".$puserid."_$now ";
    
    $query = "select * from dbmaster.t_klaim_cab_dist WHERE distid='$pdistid' ";
    if ($pdivisi=="OTC") {
        $query .= " AND IFNULL(divisi,'')<>'ETH' ";
    }else{
        $query .= " AND IFNULL(divisi,'')<>'OTC' ";
    }
    $query .= " AND IFNULL(region,'')='$pregion' ";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query = "select * from hrd.klaim_d WHERE klaimid='$pidklaim'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "ALTER TABLE $tmp00 ADD COLUMN jumlah1 DECIMAL(20,2), ADD COLUMN jumlah2 DECIMAL(20,2), "
            . " ADD COLUMN jumlah3 DECIMAL(20,2), ADD COLUMN notes VARCHAR(200), "
            . " ADD COLUMN jumlahsusulan DECIMAL(20,2), ADD COLUMN notes_susulan VARCHAR(200)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp00 a JOIN $tmp01 b on a.idcab=b.idcab SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2, a.jumlah3=b.jumlah3, a.notes=b.notes, "
            . " a.jumlahsusulan=b.jumlahsusulan, a.notes_susulan=b.notes_susulan";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
?>
<div class='tbldata'>
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='2%px' class='divnone'></th>
                <th width='5%px'>No</th>
                <th width='30%' >Cabang</th>
                <th width='5%' align="right">Nilai Klaim (Rp.)</th>
                <th width='5%' align="right">Nilai Realisasi (Rp.)</th>
                <th width='5%' align="right">Tolakan (Rp.)</th>
                <th width='1%'>Notes</th>
                <th width='5%' align="right">Reklaim (Rp.)</th>
                <th width='1%'>Notes Reklaim</th>
            </tr>
        </thead>
        <tbody class='inputdatauc'>
            <?PHP
            $no=1;
            $query = "select * from $tmp00 order by nama_cabang, idcab";
            $tampil=mysqli_query($cnmy, $query);
            while ($nrow= mysqli_fetch_array($tampil)){
                $pkodeidbr=$nrow['idcab'];
                $pnmidbr=$nrow['nama_cabang'];
                $prpnilaiklaim=$nrow['jumlah1'];
                $prpnilaireal=$nrow['jumlah2'];
                $prptolakan=$nrow['jumlah3'];
                $pnotespldt=$nrow['notes'];
                
                $prpsusulan=$nrow['jumlahsusulan'];
                $pnotessusulan=$nrow['notes_susulan'];
                
                $ptotrpklaim=(DOUBLE)$ptotrpklaim+(DOUBLE)$prpnilaiklaim;
                $ptotrpreal=(DOUBLE)$ptotrpreal+(DOUBLE)$prpnilaireal;
                $ptotrptolak=(DOUBLE)$ptotrptolak+(DOUBLE)$prptolakan;
                
                $ptotrpsusulan=(DOUBLE)$ptotrpsusulan+(DOUBLE)$prpsusulan;
                
                $pfldjmlklaim="<input type='text' value='$prpnilaiklaim' size='10px' id='e_txtrpklaim[$pkodeidbr]' name='e_txtrpklaim[$pkodeidbr]' onblur=\"DataJumlahKlaim('e_txtrpklaim[$pkodeidbr]', 'e_txtrpreal[$pkodeidbr]', 'e_txtrptolak[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off'>";
                $pfldjmlreal="<input type='text' value='$prpnilaireal' size='10px' id='e_txtrpreal[$pkodeidbr]' name='e_txtrpreal[$pkodeidbr]' onblur=\"DataJumlahReal('e_txtrpklaim[$pkodeidbr]', 'e_txtrpreal[$pkodeidbr]', 'e_txtrptolak[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off'>";
                $pfldjmltolakan="<input type='text' value='$prptolakan' size='10px' id='e_txtrptolak[$pkodeidbr]' name='e_txtrptolak[$pkodeidbr]' onblur=\"DataJumlahTolakan('e_txtrpklaim[$pkodeidbr]', 'e_txtrpreal[$pkodeidbr]', 'e_txtrptolak[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off' >";
                
                $pfldnmcab="<input type='hidden' size='40px' id='e_txtnmcab[$pkodeidbr]' name='e_txtnmcab[$pkodeidbr]' class='input-sm' value='$pnmidbr'>";
                $pfldnotes="<input type='text' size='40px' id='e_txtnotes[$pkodeidbr]' name='e_txtnotes[$pkodeidbr]' class='input-sm' value='$pnotespldt'>";
                
                $pfldjmlsusulan="<input type='text' value='$prpsusulan' size='10px' id='e_txtrpsusul[$pkodeidbr]' name='e_txtrpsusul[$pkodeidbr]' onblur=\"JumlahSusulanRp()\" class='input-sm inputmaskrp2' autocomplete='off'>";
                $pfldnotessusulan="<input type='text' size='40px' id='e_txtsusul[$pkodeidbr]' name='e_txtsusul[$pkodeidbr]' class='input-sm' value='$pnotessusulan'>";
                
                $chkbox = "<input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked>";
                
                echo "<tr>";
                echo "<td nowrap class='divnone'>$chkbox</td>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pnmidbr $pfldnmcab</td>";
                echo "<td nowrap>$pfldjmlklaim</td>";
                echo "<td nowrap>$pfldjmlreal</td>";
                echo "<td nowrap>$pfldjmltolakan</td>";
                echo "<td nowrap>$pfldnotes</td>";
                
                echo "<td nowrap>$pfldjmlsusulan</td>";
                echo "<td nowrap>$pfldnotessusulan</td>";
                
                echo "</tr>";
                
                $no++;
            }
            
            $ptotrpreal=(DOUBLE)$ptotrpreal+(DOUBLE)$pjmlkuranglebih;
            
            
            $pppnrpklaim=(DOUBLE)$ptotrpklaim*(DOUBLE)$pjmlppn/100;
            $pppnrpreal=(DOUBLE)$ptotrpreal*(DOUBLE)$pjmlppn/100;
            
            $ptotppnrpklaim=(DOUBLE)$ptotrpklaim+(DOUBLE)$pppnrpklaim;
            $ptotppnrpreal=(DOUBLE)$ptotrpreal+(DOUBLE)$pppnrpreal;
            
            $ppphrpreal=(DOUBLE)$ptotrpreal*(DOUBLE)$pjmlpph/100;
            
            $pgrdrpreal=(DOUBLE)$ptotppnrpreal-(DOUBLE)$ppphrpreal+(DOUBLE)$pbulatreal;
                    
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap class='divnone'></td>";
            echo "<td nowrap></td>";         
            echo "<td nowrap></td>";         
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";         
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "</tr>";
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap class='divnone'></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>Total</td>";
            echo "<td nowrap><input type='text' value='$ptotrpklaim' size='12px' id='e_txttotklaim' name='e_txttotklaim' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap><input type='text' value='$ptotrpreal' size='12px' id='e_txttotreal' name='e_txttotreal' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap><input type='text' value='$ptotrptolak' size='12px' id='e_txttottolak' name='e_txttottolak' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap><input type='text' value='$ptotrpsusulan' size='12px' id='e_jmlkuranglebih' name='e_jmlkuranglebih' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap></td>";
            
            echo "</tr>";
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap class='divnone'></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>PPN 10 %</td>";
            echo "<td nowrap><input type='text' value='$pppnrpklaim' size='12px' id='e_txtppnklaim' name='e_txtppnklaim' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap><input type='text' value='$pppnrpreal' size='12px' id='e_txtppnreal' name='e_txtppnreal' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap><input type='hidden' value='$pppnrptolak' size='12px' id='e_txtppntolak' name='e_txtppntolak' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "</tr>";
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap class='divnone'></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>Total</td>";
            echo "<td nowrap><input type='text' value='$ptotppnrpklaim' size='12px' id='e_txttotppnklaim' name='e_txttotppnklaim' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap><input type='text' value='$ptotppnrpreal' size='12px' id='e_txttotppnreal' name='e_txttotppnreal' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap><input type='hidden' value='$ptotppnrptolak' size='12px' id='e_txttotppntolak' name='e_txttotppntolak' onblur='' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "</tr>";
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap class='divnone'></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>PPH 2 %</td>";
            echo "<td nowrap><input type='hidden' value='$ppphrpklaim' size='12px' id='e_txtpphklaim' name='e_txtpphklaim' onblur='' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap><input type='text' value='$ppphrpreal' size='12px' id='e_txtpphreal' name='e_txtpphreal' onblur='' class='input-sm inputmaskrp2' autocomplete='off'Readonly></td>";
            echo "<td nowrap><input type='hidden' value='$ppphrptolak' size='12px' id='e_txtpphtolak' name='e_txtpphtolak' onblur='' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "</tr>";
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap class='divnone'></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>Pembulatan</td>";
            echo "<td nowrap><input type='hidden' value='$pbulatklaim' size='12px' id='e_txtbulatklaim' name='e_txtbulatklaim' onblur='' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap><input type='text' value='$pbulatreal' size='12px' id='e_txtbulatreal' name='e_txtbulatreal' onblur='HitungPembualatan()' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap><input type='hidden' value='$pbulattolak' size='12px' id='e_txtbulattolak' name='e_txtbulattolak' onblur='' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "</tr>";
            
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap class='divnone'></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>Grand Total Discount</td>";
            echo "<td nowrap><input type='hidden' value='$pgrdrpklaim' size='12px' id='e_txtgrdklaim' name='e_txtgrdklaim' onblur='' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap><input type='text' value='$pgrdrpreal' size='12px' id='e_txtgrdreal' name='e_txtgrdreal' onblur='' class='input-sm inputmaskrp2' autocomplete='off' Readonly></td>";
            echo "<td nowrap><input type='hidden' value='$pgrdrptolak' size='12px' id='e_txtgrdtolak' name='e_txtgrdtolak' onblur='' class='input-sm inputmaskrp2' autocomplete='off'></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "</tr>";
            
            
            
            ?>
        </tbody>
    </table>
</div>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>
<script>
    
    function DataJumlahKlaim(iklaim, ireal, itolak) {
        var nklaim=document.getElementById(iklaim).value;
        document.getElementById(ireal).value=nklaim;
        document.getElementById(itolak).value="0";        
        HitungTotalJumlahRp();
    }
    
    function DataJumlahTolakan(iklaim, ireal, itolak) {
        var nklaim=document.getElementById(iklaim).value;
        var ntolak=document.getElementById(itolak).value;
        var newchar = '';
        
        if (nklaim=="") nklaim="0";
        nklaim = nklaim.split(',').join(newchar);
        
        if (ntolak=="") ntolak="0";
        ntolak = ntolak.split(',').join(newchar);
        
        var nTotal_="0";
        nTotal_ =parseFloat(nklaim)-parseFloat(ntolak);
        
        document.getElementById(ireal).value=nTotal_;      
        HitungTotalJumlahRp();
    }
    
    function DataJumlahReal(iklaim, ireal, itolak) {
        var nklaim=document.getElementById(iklaim).value;
        var nreal=document.getElementById(ireal).value;
        var newchar = '';
        
        if (nklaim=="") nklaim="0";
        nklaim = nklaim.split(',').join(newchar);
        if (nreal=="") nreal="0";
        nreal = nreal.split(',').join(newchar);
        
        var nTotal_="0";
        nTotal_ =parseFloat(nklaim)-parseFloat(nreal);
        
        document.getElementById(itolak).value=nTotal_;
        HitungTotalJumlahRp();
    }
    

    function JumlahSusulanRp() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';
        
        var nTotalSusul_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                //susulan
                var anm_jmlssl="e_txtrpsusul["+fields[0]+"]";
                var ajmls=document.getElementById(anm_jmlssl).value;
                if (ajmls=="") ajmls="0";
                ajmls = ajmls.split(',').join(newchar);

                nTotalSusul_ =parseFloat(nTotalSusul_)+parseFloat(ajmls);
                
                
            }
        }
        
        
        document.getElementById('e_jmlkuranglebih').value=nTotalSusul_;
        HitungTotalJumlahRp();
    }
    
    
    
    function HitungTotalJumlahRp() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';

        var nTotal_="0";
        var nTotal2_="0";
        var nTotal3_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_jml="e_txtrpklaim["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);

                nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
                
                var anm_jml2="e_txtrpreal["+fields[0]+"]";
                var ajml2=document.getElementById(anm_jml2).value;
                if (ajml2=="") ajml2="0";
                ajml2 = ajml2.split(',').join(newchar);

                nTotal2_ =parseFloat(nTotal2_)+parseFloat(ajml2);
                
                var anm_jml3="e_txtrptolak["+fields[0]+"]";
                var ajml3=document.getElementById(anm_jml3).value;
                if (ajml3=="") ajml3="0";
                ajml3 = ajml3.split(',').join(newchar);

                nTotal3_ =parseFloat(nTotal3_)+parseFloat(ajml3);
                
                
            }
        }
        
        var itotkurleb=document.getElementById('e_jmlkuranglebih').value;
        if (itotkurleb=="") itotkurleb="0";
        itotkurleb = itotkurleb.split(',').join(newchar);
        
        nTotal2_=parseFloat(nTotal2_) + parseFloat(itotkurleb);
        
        
        document.getElementById('e_txttotklaim').value=nTotal_;
        document.getElementById('e_txttotreal').value=nTotal2_;
        document.getElementById('e_txttottolak').value=nTotal3_;
        
        HitungPPN();
        HitungPPH();
        HitungTotalRpPPN();
        HitungGrandTotalRp();
    }
    
    function HitungPPN() {
        var newchar = '';
        var nTotal_="0";
        var nTotal2_="0";
        
        var ippn=document.getElementById('e_jmlppn').value;
        var itotklaim=document.getElementById('e_txttotklaim').value;
        var itotreal=document.getElementById('e_txttotreal').value;
        
        if (ippn=="") ippn="0";
        ippn = ippn.split(',').join(newchar);
        
        if (itotklaim=="") itotklaim="0";
        itotklaim = itotklaim.split(',').join(newchar);
        
        if (itotreal=="") itotreal="0";
        itotreal = itotreal.split(',').join(newchar);
        
        
        if (ippn!="0") {
            nTotal_=parseFloat(itotklaim) * parseFloat(ippn) / 100;
            nTotal2_=parseFloat(itotreal) * parseFloat(ippn) / 100;
        }
        
        document.getElementById('e_txtppnklaim').value=nTotal_;
        document.getElementById('e_txtppnreal').value=nTotal2_;
        
        
    }
    
    function HitungTotalRpPPN() {
        var newchar = '';
        var nTotal_="0";
        var nTotal2_="0";
        
        var itotklaim=document.getElementById('e_txttotklaim').value;
        var itotreal=document.getElementById('e_txttotreal').value;
        var ippnklaim=document.getElementById('e_txtppnklaim').value;
        var ippnreal=document.getElementById('e_txtppnreal').value;
        
        if (itotklaim=="") itotklaim="0";
        itotklaim = itotklaim.split(',').join(newchar);
        if (itotreal=="") itotreal="0";
        itotreal = itotreal.split(',').join(newchar);
        
        if (ippnklaim=="") ippnklaim="0";
        ippnklaim = ippnklaim.split(',').join(newchar);
        if (ippnreal=="") ippnreal="0";
        ippnreal = ippnreal.split(',').join(newchar);
        
        nTotal_=parseFloat(itotklaim) + parseFloat(ippnklaim);
        nTotal2_=parseFloat(itotreal) + parseFloat(ippnreal);
        
        document.getElementById('e_txttotppnklaim').value=nTotal_;
        document.getElementById('e_txttotppnreal').value=nTotal2_;
        
    }
    
    
    function HitungPPH() {
        var newchar = '';
        var nTotal_="0";
        var nTotal2_="0";
        
        var ipph=document.getElementById('e_jmlpph').value;
        var itotklaim=document.getElementById('e_txttotklaim').value;
        var itotreal=document.getElementById('e_txttotreal').value;
        
        if (ipph=="") ipph="0";
        ipph = ipph.split(',').join(newchar);
        
        if (itotklaim=="") itotklaim="0";
        itotklaim = itotklaim.split(',').join(newchar);
        
        if (itotreal=="") itotreal="0";
        itotreal = itotreal.split(',').join(newchar);
        
        
        if (ipph!="0") {
            nTotal_=parseFloat(itotklaim) * parseFloat(ipph) / 100;
            nTotal2_=parseFloat(itotreal) * parseFloat(ipph) / 100;
        }
        
        //document.getElementById('e_txtpphklaim').value=nTotal_;
        document.getElementById('e_txtpphreal').value=nTotal2_;
        
    }
    
    function HitungGrandTotalRp(){
        var newchar = '';
        var nTotal_="0";
        
        var itotreal=document.getElementById('e_txttotppnreal').value;
        var itotrealpph=document.getElementById('e_txtpphreal').value;
        
        if (itotreal=="") itotreal="0";
        itotreal = itotreal.split(',').join(newchar);
        
        if (itotrealpph=="") itotrealpph="0";
        itotrealpph = itotrealpph.split(',').join(newchar);
        
        
        nTotal_=parseFloat(itotreal) - parseFloat(itotrealpph);
        
        var itotal="0";
        itotal=nTotal_.toFixed(0);
        
        
        var ipembulatan="0";
        var ibulat1="0";
        var ibulat2="0";
        ibulat1=parseFloat(itotal) - parseFloat(nTotal_);
        ibulat2=parseFloat(nTotal_) - parseFloat(itotal);
        
        if (ibulat1=="0") ibulat1="0";
        if (ibulat2=="0") ibulat2="0";
        
        var ntotasli1="0";
        var ntotasli2="0";
        
        ntotasli1=parseFloat(nTotal_) + parseFloat(ibulat1);
        ntotasli2=parseFloat(nTotal_) + parseFloat(ibulat2);
        
        if (ntotasli1=="0") ntotasli1="0";
        if (ntotasli2=="0") ntotasli2="0";
        
        if (parseFloat(ntotasli1)==parseFloat(itotal)) ipembulatan=ibulat1;
        else ipembulatan=ibulat2;
        
        
        //diulang kembali untuk memastikan
        itotal=parseFloat(nTotal_) + parseFloat(ipembulatan);
        document.getElementById('e_txtgrdreal').value=itotal;
        document.getElementById('e_txtbulatreal').value=ipembulatan;
        
    }
    
    function HitungPembualatan() {
        var newchar = '';
        var itotal="0";
        
        var itotreal=document.getElementById('e_txttotppnreal').value;
        var itotrealpph=document.getElementById('e_txtpphreal').value;
        var itotbulat=document.getElementById('e_txtbulatreal').value;
        
        if (itotreal=="") itotreal="0";
        itotreal = itotreal.split(',').join(newchar);
        
        if (itotrealpph=="") itotrealpph="0";
        itotrealpph = itotrealpph.split(',').join(newchar);
        
        if (itotbulat=="") itotbulat="0";
        itotbulat = itotbulat.split(',').join(newchar);
        
        itotal=parseFloat(itotreal) - parseFloat(itotrealpph) + parseFloat(itotbulat);
        
        document.getElementById('e_txtgrdreal').value=itotal;
        
    }
</script>

