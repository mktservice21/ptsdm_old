<?php
session_start();
if ($_GET['module']=="viewdatakas"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli.php";
    $totalinput=0;
    $pket=$_GET['ket'];
    $pact=$_POST['uact'];
    $psubkode=$_POST['usubkode'];
    
    $pidinput=$_POST['eidinput'];
    $pkryid=$_POST['ukaryawanid'];
    
    $date1=$_POST['uper1'];
    $mytgl1= date("Y-m-d", strtotime($date1));
    $mytahun= date("Y", strtotime($date1));
    
    $date2=$_POST['uper2'];
    $mytgl2= date("Y-m-d", strtotime($date2));
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['IDCARD'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.DSETHY00_".$userid."_$now ";
    $tmp01 =" dbtemp.DSETHY01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHY02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHY03_".$userid."_$now ";
    
    
    $nfilterid_sudah="";
    if ($pact=="editdata") $nfilterid_sudah=" AND a.idinput<> '$pidinput' ";
    
    $query = "select a.bridinput, a.amount from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b "
            . " on a.idinput=b.idinput WHERE b.stsnonaktif<>'Y' AND a.kodeinput='X' $nfilterid_sudah";
    $query = "create TEMPORARY table $tmp00 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    if ($pact=="editdata") {
        
        $query = "select bridinput, amount from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "DELETE FROM $tmp00 WHERE bridinput IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp03)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    $nfilter_sudahada=" AND a.idkascab NOT IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp00)";

    
    
    $query = "select a.idkascab, a.karyawanid, b.nama nama_karyawan, a.icabangid, c.nama nama_cabang, 
        a.coa4, d.NAMA4, 
        a.keterangan, a.jumlah, e.pc_bln_lalu, e.saldoawal, e.pcm, e.jmltambahan, e.jumlah as jmlttl, e.oustanding as otsrp, a.tanggal 
        from dbmaster.t_kaskecilcabang a LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanId 
        LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid 
        LEFT JOIN dbmaster.coa_level4 d on a.coa4=d.COA4 
        LEFT JOIN dbmaster.t_kaskecilcabang_rpdetail e on a.idkascab=e.idkascab 
        WHERE IFNULL(a.stsnonaktif,'')<>'Y' AND a.tanggal BETWEEN '$mytgl1' AND '$mytgl2' $nfilter_sudahada 
            AND (IFNULL(tgl_atasan4,'')<>'' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')
        "; //AND a.karyawanid='$pkryid' 
    $query .= " AND ( IFNULL(tgl_fin,'')<>'' AND IFNULL(tgl_fin,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' ) ";
    if ($pidgroup=="23" OR $pidgroup=="26") {
        $query .=" AND IFNULL(a.pengajuan,'') IN ('OTC', 'CHC', 'OT') ";
    }elseif ($pidgroup=="40") {
        $query .=" AND IFNULL(a.pengajuan,'') NOT IN ('OTC', 'CHC', 'OT') ";
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($pact=="editdata") {
    
        $query = "select a.*, b.bridinput, b.amount from $tmp01 a LEFT JOIN $tmp03 b on a.idkascab=b.bridinput";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }else{
        $query = "select *, CAST('' as CHAR(20)) as bridinput, jumlah as amount from $tmp01";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    ?>

        <div hidden class='form-group'>
            &nbsp;&nbsp;&nbsp;<<button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Jumlah</button> <span class='required'></span>
        </div>
        <div class='x_content'>
            
            
            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                <thead>
                    <tr>
                        <th width='10px'><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" value='select'></th>
                        <th width='10px'>No</th>
                        <th align="center" nowrap>Kas Id</th>
                        <th align="center" nowrap>Tanggal</th>
                        <!--<th align="center" nowrap>Nama</th>-->
                        <th align="center" nowrap>Cabang</th>
                        <th align="center" nowrap>Akun</th>
                        <th align="center" nowrap>Saldo Awal</th>
                        <th align="center" nowrap>Isi PC Bln</th>
                        <th align="center" nowrap>Jml. Biaya</th>
                        <th align="center" nowrap>Sisa</th>
                        <!--<th align="center" nowrap>Rp. Input</th>-->
                        <th align="center" nowrap>Keterangan</th>
                    </tr>
                </thead>
                <tbody>

                    <?PHP
                        $no=1;
                        $query = "select * from $tmp02 order by tanggal, idkascab, nama_karyawan";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['idkascab'];
                            $pidinputbr = $row['bridinput'];

                            $pnamakaryawan = $row['nama_karyawan'];
                            $pnamacoa = $row['NAMA4'];
                            $pnmcabang = $row['nama_cabang'];
                            $paktivitas = $row['keterangan'];
                            $nperiode = $row['tanggal'];
                            
                            $pjumlah = $row['jumlah'];
                            $psldawal = $row['saldoawal'];
                            $ppclalu = $row['pc_bln_lalu'];
                            $pamount = $row['amount'];
                            
                            $pjmlmintarp=(DOUBLE)$pjumlah;//-(DOUBLE)$psldawal
                            
                            
                            $psisasaldo=(DOUBLE)$psldawal+(DOUBLE)$ppclalu-(DOUBLE)$pjumlah;
                            
                            $pjumlah=number_format($pjumlah,0,",",",");
                            $psldawal=number_format($psldawal,0,",",",");
                            $ppclalu=number_format($ppclalu,0,",",",");
                            $pamount=number_format($pamount,0,",",",");
                            $pjmlmintarp=number_format($pjmlmintarp,0,",",",");
                            $psisasaldo=number_format($psisasaldo,0,",",",");
                            
                            //$ppilihjmlrp=$pjumlah;
                            $ppilihjmlrp=$pjmlmintarp;
                            
                            $pbrid_input = $row['bridinput'];
                            $ncheck_sudah="";
                            if ($pact=="editdata") {
                                if (!empty($pbrid_input)) $ncheck_sudah="checked";
                                if (!empty($pidinputbr)) $ppilihjmlrp=$pamount;
                            }
                            
                            $pinput_jumlah="<input type='hidden' id='txt_jml[$pbrid]' name='txt_jml[$pbrid]' value='$ppilihjmlrp' Readonly>";
                            $chkbox = "<input type='checkbox' id='chk_jml1[$pbrid]' name='chk_jml1[]' value='$pbrid' onclick=\"CentangCekBoxDataBR('$pbrid', 'chk_jml1[$pbrid]')\" $ncheck_sudah>";
                            
                            $pprint="<a title='Detail Barang / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                                . "onClick=\"window.open('eksekusi3.php?module=bgtkaskecilcabang&brid=$pbrid&iprint=print',"
                                . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                . "$pbrid</a>";
                    
                            echo "<tr>";
                            echo "<td nowrap>$chkbox $pinput_jumlah<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pprint</td>";
                            echo "<td nowrap>$nperiode</td>";
                            //echo "<td>$pnamakaryawan</td>";
                            echo "<td>$pnmcabang</td>";
                            echo "<td>$pnamacoa</td>";
                            echo "<td nowrap align='right'>$psldawal</td>";
                            echo "<td nowrap align='right'>$ppclalu</td>";
                            echo "<td nowrap align='right' style='color:blue;'>$pjumlah</td>";
                            echo "<td nowrap align='right'>$psisasaldo</td>";
                            //echo "<td nowrap align='right'>$pamount</td>";
                            echo "<td>$paktivitas</td>";
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
                font-size:11px;
            }
            #datatable input[type=text], #tabelnobr input[type=text] {
                box-sizing: border-box;
                color:#000;
                font-size:11px;
                height: 25px;
            }
            select.soflow {
                font-size:12px;
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


            function CentangCekBoxDataBR(nidbr, nmchk) {
                HitungTotalDariCekBox();
            }
            
            function HitungTotalDariCekBox() {
                
                var chk_arr1 =  document.getElementsByName('chk_jml1[]');
                var chklength1 = chk_arr1.length;
                var newchar = '';

                var nTotal_="0";
                for(k=0;k< chklength1;k++)
                {   
                    
                    if (chk_arr1[k].checked == true) {
                        var kata = chk_arr1[k].value;
                        var fields = kata.split('-');    
                        var anm_jml="txt_jml["+fields[0]+"]";
                        var ajml=document.getElementById(anm_jml).value;
                        if (ajml=="") ajml="0";
                        ajml = ajml.split(',').join(newchar);
                        
                        nTotal_ =parseInt(nTotal_)+parseInt(ajml);
                    }
                }

                document.getElementById('e_jmlusulan').value=nTotal_;   
            }
            
            
            
            function SelAllCheckBoxKB(nmbuton, data){
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
                HitungTotalKBDariCekBox();
            }
            
            function HitungTotalKBDariCekBox(){
                var chk_arr1 =  document.getElementsByName('chk_jml_kb[]');
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

                //$("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_spdkascab/viewdata.php?module=hitungtotalcekboxkasbon",
                    data:"unoidbr="+allnobr,
                    success:function(data){
                        //$("#loading2").html("");
                        document.getElementById('e_jmlusulan_kb').value=data;
                    }
                });
            }
        </script>
            
    <?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp00");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    
}elseif ($_GET['module']=="xxxx"){
}elseif ($_GET['module']=="xxxx"){


}



?>
