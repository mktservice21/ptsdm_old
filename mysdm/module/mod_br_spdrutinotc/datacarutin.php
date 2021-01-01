<?php
session_start();

    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli.php";
    $totalinput=0;
    
    $pact=$_POST['uact'];
    $pidinput=$_POST['eidinput'];
    $pdivisi=$_POST['udivisi'];
    
    $date1=$_POST['utgl1'];
    $mytgl1= date("Y-m-d", strtotime($date1));
    
    $date2=$_POST['utgl2'];
    $mytgl2= date("Y-m-d", strtotime($date2));

    $userid=$_SESSION['IDCARD'];    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.DSETHF00_".$userid."_$now ";
    $tmp01 =" dbtemp.DSETHF01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHF02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHF03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSETHF04_".$userid."_$now ";
    $tmp05 =" dbtemp.DSETHF05_".$userid."_$now ";
        

    $query = "select idservice, tglservice, divisi, karyawanid, icabangid, areaid, icabangid_o, areaid_o, nopol, km, jumlah, keterangan from dbmaster.t_service_kendaraan WHERE stsnonaktif<>'Y' and divisi='OTC' and 
        DATE_FORMAT(tglservice,'%Y-%m-%d') BETWEEN '$mytgl1' AND '$mytgl2'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama nama_karyawan, c.nama nama_cabang, CAST('' as CHAR(10)) as bridinput from $tmp01 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang_o c on a.icabangid_o=c.icabangid_o";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    if (!empty($pidinput)) {
        $query = "select distinct bridinput from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.idservice=b.bridinput SET a.bridinput=b.bridinput"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    ?>


    
    <div class='form-group'>
        &nbsp;&nbsp;&nbsp;
        <button type='button' class='btn btn-danger btn-xs' onclick='HitungJumlahTotalCexBox()'>Hitung Jumlah</button> 
        <span class='required'></span>
    </div>
    
    
    <div class='x_content'>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'><input type="checkbox" id="chkall[]" name="chkall[]" onclick="SelAllCheckBox('chkall[]', 'chk_idbr[]')" value='select'></th>
                    <th width='10px'>No</th>
                    <th width='10px'>Tanggal</th>
                    <th width='50px' nowrap>Yang Membuat</th>
                    <th width='20px'>No. Polisi</th>
                    <th align="center" nowrap>Jumlah</th>
                    <th align="center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                
                $no=1;
                $query = "select * from $tmp02";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid = $row['idservice'];
                    $ptglinput =date("d-M-Y", strtotime($row['tglservice']));

                    $pnamakaryawan = $row['nama_karyawan'];
                    $paktivitas1 = $row['keterangan'];
                    $pdivisi = $row['divisi'];
                    $pnopolisi = $row['nopol'];
                    $pbridinput = $row['bridinput'];

                    $pjumlah = $row['jumlah'];
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $ncheck_sudah="";
                    if (!empty($pbridinput)) $ncheck_sudah="checked";
                    $chkbox = "<input type='checkbox' id='chk_idbr[$pbrid]' name='chk_idbr[]' value='$pbrid' onclick=\"HitungJumlahTotalCexBox('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'chk_transke[$pbrid]', 'txt_adj_ket[$pbrid]')\" $ncheck_sudah>";
                    $ptxtjumlah = "<input type='hidden' id='txt_jml[$pbrid]' name='txt_jml[$pbrid]' value='$pjumlah' size='7px' class='input-sm inputmaskrp2' Readonly>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$chkbox<t/d>";
                    echo "<td nowrap>$no $ptxtjumlah</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td nowrap>$pnamakaryawan</td>";
                    echo "<td nowrap>$pnopolisi</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td>$paktivitas1</td>";
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
    
    <script type="text/javascript">
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
            HitungJumlahTotalCexBox();
        }

        function HitungJumlahTotalCexBox() {
            var chk_arr1 =  document.getElementsByName('chk_idbr[]');
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
                    ajml = ajml.split(',').join(newchar);
                    
                    nTotal_ =parseInt(nTotal_)+parseInt(ajml);
                }
            }
            
            document.getElementById('e_jmlusulan').value=nTotal_;
            
        }


        $(document).ready(function() {
            var table = $('#datatable2').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [5] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
    </script>
    
    <?PHP

    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table $tmp00");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
    
    




?>
