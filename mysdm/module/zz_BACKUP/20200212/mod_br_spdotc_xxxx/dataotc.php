<?php
session_start();
if ($_GET['module']=="viewdataotc"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli.php";
    $totalinput=0;
    $pket=$_GET['ket'];
    
    $pact=$_POST['uact'];
    $pidinput=$_POST['eidinput'];
    $pdivisi=$_POST['udivisi'];
    
    $tgl01=$_POST['utgl'];
    $periode1= date("Y-m-d", strtotime($tgl01));
    
    $date1=$_POST['uper1'];
    $mytgl1= date("Y-m-d", strtotime($date1));
    
    $date2=$_POST['uper2'];
    $mytgl2= date("Y-m-d", strtotime($date2));
    
    $jenis=$_POST['ujenis'];
    $pertipe=$_POST['upertipe'];
    $padvance=$_POST['uadvance'];
    
    $filterlampiran="";
    
	if ($pact=="editdata") {
	}else{
		if (!empty($jenis)) $filterlampiran = " and case when ifnull(a.lampiran,'N')='' then 'N' else a.lampiran end ='$jenis' ";
    }
    
    $ftypetgl = " AND  a.tglbr BETWEEN '$mytgl1' AND '$mytgl2' ";
    if ($pertipe=="T") $ftypetgl = " AND a.tgltrans BETWEEN '$mytgl1' AND '$mytgl2' ";
    if ($pertipe=="S") $ftypetgl = " AND a.tglrpsby BETWEEN '$mytgl1' AND '$mytgl2' ";
    
    
    $userid=$_SESSION['IDCARD'];
    
    $filsudahada="";
    if ($pact=="editdata") {
        $filsudahada=" AND a.brOtcId IN (select DISTINCT IFNULL(bridinput,'') bridinput from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput')";
    }else{
        $filsudahada=" AND a.brOtcId NOT IN (select DISTINCT IFNULL(bridinput,'') bridinput from dbmaster.t_suratdana_br1 WHERE kodeinput='D'"
                . " AND idinput IN (select distinct IFNULL(idinput,'') From dbmaster.t_suratdana_br WHERE "
                . " IFNULL(stsnonaktif,'') <>'Y' AND divisi='OTC' and IFNULL(pilih,'')<>'N' ))";
        
        if ($pertipe=="S") $filsudahada="";
    }
	
    $filsudahada="";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETH01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETH02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETH03_".$userid."_$now ";
    
    //cari yang sudah ada
    $ntglpilih = date('Y-m-d', strtotime($tgl01));
    $query = "select distinct IFNULL(bridinput,'') bridinput FROM dbmaster.t_suratdana_br1 WHERE idinput in "
            . "(select distinct IFNULL(idinput,'') idinput from dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' "
            . "AND divisi<>'$pdivisi' AND tgl='$ntglpilih')";
    
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //END 
    
        $query = "select a.brOtcId, a.tgltrans, a.COA4, b.NAMA4, a.icabangid_o, c.nama nama_cabang, a.noslip, a.kodeid, a.subpost,
           a.keterangan1, a.real1, a.jumlah, a.realisasi, 
           a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
           a.lampiran, a.ca, a.via, a.tglbr, a.tglrpsby, a.tglreal, a.jenis  
           from hrd.br_otc a 
           LEFT JOIN dbmaster.coa_level4 b on a.COA4=b.COA4 
           LEFT JOIN mkt.icabang_o c on a.icabangid_o=c.icabangid_o WHERE 1=1 $ftypetgl $filterlampiran $filsudahada ";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    if ($padvance=="A"){
    }else{
        //mysqli_query($cnmy, "UPDATE $tmp01 SET jumlah=realisasi WHERE IFNULL(realisasi,0)>0");
    }
    

    ?>
    
        <div class='form-group'>
            &nbsp;&nbsp;&nbsp;
            <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Ulang</button> 
            <button type='button' class='btn btn-success btn-xs' onclick='popUp()'>Isi Report SBY</button> 
            <span class='required'></span>
        </div>
        <div class='x_content' style="overflow-x:auto; max-height: 500px;">
            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                <thead style="">
                    <tr>
                        <th width='10px' nowrap><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" checked></th>
                        <th width='10px' nowrap>No</th>
                        <th align="center" nowrap>Jumlah</th>
                        <th align="center" nowrap>Jml. Realisasi</th>
                        <th align="center" nowrap>No. Slip</th>
                        <th align="center" nowrap>Tgl. Terima (bln/hari/thn)</th>
                        <!--<th align="center" nowrap>Tgl. Transfer (bln/hari/thn)</th>-->
                        <th align="center" nowrap>Cabang</th>
                        <th align="center" nowrap>Perkiraan</th>
                        <th align="center" nowrap>Nama Realisasi</th>
                        <th align="center" nowrap>Tgl. Rpt. Sby</th>
                        <th align="center" nowrap>Jenis</th>
                        <th align="center" nowrap>Keterangan</th>
                        <th align="center" nowrap>ID</th>
                    </tr>
                </thead>
                <tbody>

                    <?PHP
                        //harus ada, untuk cek doang
                        echo "<input type='hidden' id='chk_jml[]' name='chk_jml[]' value=''>";

                        $no=1;
                        $query = "select * from $tmp01 order by noslip, brOtcId";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['brOtcId'];
                            $pnoslip = $row['noslip'];
                            
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                                $ptgltrans =$row['tgltrans'];
                                //$ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));
                            
                            $ptglreal = "";
                            if (!empty($row['tglreal']) AND $row['tglreal']<>"0000-00-00")
                                $ptglreal =$row['tglreal'];
                            
                            $ptglsby = "";
                            if (!empty($row['tglrpsby']) AND $row['tglrpsby']<>"0000-00-00")
                                $ptglsby =date("d-M-Y", strtotime($row['tglrpsby']));

                            $pnmcabang = $row['nama_cabang'];
                            if (empty($pnmcabang)) $pnmcabang=$row['icabangid_o'];
                            $pnmcoa = $row['NAMA4'];
                            
                            $paktivitas1 = $row['keterangan1'];
                            $prealisasi1 = $row['real1'];
                            
                            $pdivisi = "OTC";

                            $pjenis = $row['jenis'];
                            $njenis="";
                            if (!empty($pjenis)) {
                                if ($pjenis=="A") {
                                    $njenis="Advance";
                                }else{
                                    $njenis="Klaim";
                                }
                            }
                            
                            $pjumlah = $row['jumlah'];
                            $pjumlah=number_format($pjumlah,0,",",",");
                            
                            $pjmlreal = $row['realisasi'];
                            
                            $ptglterima;
                            
                            $ptxtnobrid="<input type='hidden' size='10px' id='e_nobrid$no' name='e_nobrid$no' class='input-sm' autocomplete='off' value='$pbrid'>";
                            
                            $ptxtjmlreal="<input type='text' size='10px' id='e_jmlreal$no' name='e_jmlreal$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlreal'>";
                            $ptxtnoslip="<input type='text' size='10px' id='e_noslip$no' name='e_noslip$no' class='input' autocomplete='off' value='$pnoslip'>";
                            $ptxttglterima="<input type='date' size='5px' id='e_tglterima$no' name='e_tglterima$no' class='input' autocomplete='off' value='$ptglreal'>";
                            $ptxttgltrans="<input type='date' size='5px' id='e_tgltrans$no' name='e_tgltrans$no' class='input' autocomplete='off' value='$ptgltrans'>";
                            $ptxttglsby="<input type='date' size='5px' id='e_tglsby$no' name='e_tglsby$no' class='input' autocomplete='off' value='$ptglsby'>";
                            
                            $fsimpan="'e_nobrid$no', 'e_jmlreal$no', 'e_noslip$no', 'e_tglterima$no'";
                            $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save Real' onclick=\"SimpanData('input', $fsimpan)\">";
                        
                            $chkbox = "<input type='checkbox' id='chk_jml1[]' name='chk_jml1[]' value='$pbrid' onclick='HitungTotalDariCekBox()' checked> ";
                            echo "<tr>";
                            echo "<td nowrap>$chkbox $ptxtnobrid<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td nowrap>$ptxtjmlreal</td>";
                            echo "<td nowrap>$ptxtnoslip</td>";
                            echo "<td nowrap>$ptxttglterima &nbsp; $simpandata</td>";
                            //echo "<td nowrap>$ptxttgltrans</td>";
                            echo "<td nowrap>$pnmcabang</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap>$prealisasi1</td>";
                            echo "<td nowrap>$ptglsby</td>";
                            echo "<td nowrap>$njenis</td>";
                            echo "<td nowrap>$paktivitas1</td>";
                            echo "<td nowrap>$pbrid</td>";
                            
                            echo "</tr>";

                            $no++;
                        }
                    ?>
                </tbody>
            </table>

        </div>

        <style>
            .divnone {
                display: none;
            }
            #datatablespggj th {
                font-size: 12px;
            }
            #datatablespggj td { 
                font-size: 11px;
            }
            .imgzoom:hover {
                -ms-transform: scale(3.5); /* IE 9 */
                -webkit-transform: scale(3.5); /* Safari 3-8 */
                transform: scale(3.5);

            }


            .form-group, .input-group, .control-label {
                margin-bottom:2px;
            }
            .control-label {
                font-size:12px;
            }
            #datatable input[type=text], #tabelnobr input[type=text] {
                box-sizing: border-box;
                color:#000;
                font-size:10px;
                height: 25px;
            }
            select.soflow {
                font-size:11px;
                height: 30px;
            }
            .disabledDiv {
                pointer-events: none;
                opacity: 0.4;
            }

            table.datatable, table.tabelnobr {
                color: #000;
                font-family: Helvetica, Arial, sans-serif;
                width: 100%;
                border-collapse:
                collapse; border-spacing: 0;
                font-size: 11px;
                border: 0px solid #000;
            }

            table.datatable td, table.tabelnobr td {
                border: 1px solid #000; /* No more visible border */
                height: 10px;
                transition: all 0.1s;  /* Simple transition for hover effect */
            }

            table.datatable th, table.tabelnobr th {
                background: #DFDFDF;  /* Darken header a bit */
                font-weight: bold;
            }

            table.datatable td, table.tabelnobr td {
                background: #FAFAFA;
            }

            /* Cells in even rows (2,4,6...) are one color */
            tr:nth-child(even) td { background: #F1F1F1; }

            /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
            tr:nth-child(odd) td { background: #FEFEFE; }

            tr td:hover.biasa { background: #666; color: #FFF; }
            tr td:hover.left { background: #ccccff; color: #000; }

            tr td.center1, td.center2 { text-align: center; }

            tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
            tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
            /* Hover cell effect! */
            tr td {
                padding: -10px;
            }

        </style>
        

        <script>
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

            function HitungTotalDariCekBox() {
                var iperiodeby = document.getElementById('cb_pertipe').value;
                var ikode = document.getElementById('cb_kode').value;
                
                var eadvance="A";
                if (iperiodeby=="S"){
                    var eadvance="";
                }else{
                    if (ikode=="2" && iperiodeby=="T") {
                        var eadvance="";
                    }
                }
                
                var chk_arr1 =  document.getElementsByName('chk_jml1[]');
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
                }
                
                $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_spdotc/viewdata.php?module=hitungtotalcekboxotc",
                    data:"unoidbr="+allnobr+"&uadvance="+eadvance,
                    success:function(data){
                        $("#loading2").html("");
                        document.getElementById('e_jmlusulan').value=data;
                    }
                });

            }
            
            
            
            function SimpanData(eact, idbr, ajmlreal, anoslip, atglterima)  {

                var eidbr =document.getElementById(idbr).value;
                var enoslip =document.getElementById(anoslip).value;
                var ejmlreal =document.getElementById(ajmlreal).value;
                var etglterima =document.getElementById(atglterima).value;


                if (eidbr==""){
                    alert("id kosong....");
                    return 0;
                }

                //alert(eidbr+", "+ejmlreal+", "+enoslip+", "+etglterima); return 0;
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
                            url:"module/mod_br_spdotc/aksi_simpanreal.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                            data:"uidbr="+eidbr+"&ujmlreal="+ejmlreal+"&unoslip="+enoslip+"&utglterima="+etglterima,
                            success:function(data){
                                if (data.length > 1) {
                                    alert(data);
                                }
                                if (eact=="hapus" && data.length <= 1) {
                                    //document.getElementById(enoslip).value="";
                                }
                                HitungTotalDariCekBox();
                            }
                        });
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }
            }
            
            function popUp(){
                var module = "spdotc";
                var iact = "isitglrptsby";

                var chk_arr1 =  document.getElementsByName('chk_jml1[]');
                var chklength1 = chk_arr1.length; 

                var allnobr="";
                var TotalPilih=0;

                for(k=0;k< chklength1;k++)
                {
                    if (chk_arr1[k].checked == true) {
                        var kata = chk_arr1[k].value;
                        var fields = kata.split('-');
                        allnobr =allnobr + fields[0]+",";
                        TotalPilih++;
                    }
                }

                var strWindowFeatures = "location=yes,height=570,width=520,scrollbars=yes,status=yes";
                var URL = "eksekusi3.php?module="+module+"&act="+iact+"&brid="+allnobr;
                var win = window.open(URL, "_blank", strWindowFeatures);
            }
        </script>
            
    <?PHP
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    
}elseif ($_GET['module']=="xxxx"){
}elseif ($_GET['module']=="xxxx"){


}



?>
