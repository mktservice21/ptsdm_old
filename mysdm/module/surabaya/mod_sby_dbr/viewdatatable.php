<?php

    session_start();
    include "../../../config/koneksimysqli.php";
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $pdivisi_pilih = $_POST['udivisi'];
    $pviasby = $_POST['uviasby'];
    $ppajak_ = $_POST['upajak'];
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.RPTREKOTCF00_".$_SESSION['USERID']."_$now ";
    $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.RPTREKOTCF02_".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.RPTREKOTCF03_".$_SESSION['USERID']."_$now ";
    $tmp04 =" dbtemp.RPTREKOTCF04_".$_SESSION['USERID']."_$now ";
    $tmp05 =" dbtemp.RPTREKOTCF05_".$_SESSION['USERID']."_$now ";
    
    $f_via="";
    if ($pviasby=="Y") $f_via=" AND IFNULL(via,'')='Y' ";
    if ($pviasby=="T") $f_via=" AND IFNULL(via,'')<>'Y' ";
    
    if ($pdivisi_pilih=="KD") {
        if ($pviasby=="Y") $f_via=" AND IFNULL(noslip,'') like '%via Sby%' ";
        if ($pviasby=="T") $f_via=" AND IFNULL(noslip,'') not like '%via Sby%' ";
    }
    
    $f_pajak="";
    if ($ppajak_=="Y") $f_pajak=" AND IFNULL(pajak,'')='Y' ";
    if ($ppajak_=="T") $f_pajak=" AND IFNULL(pajak,'')<>'Y' ";
    
    
    if ($pdivisi_pilih=="OTC") {
        $query = "select ccyId, brOtcId brid, noslip, COA4, 'OTC' as divprodid, icabangid_o, '' as areaid, '' as idcabang, tglbr tgl, tgltrans, tglrpsby, 
            '' as karyawanId, '' as karyawanI2, '' as dokterId, '' as dokter, '' as nama_dokter, real1 as realisasi1, via, jumlah, realisasi jumlah1, 
            pajak, nama_pengusaha, noseri, tgl_fp, noseri_pph, tgl_fp_pph, jenis_dpp, jasa_rp, dpp, ppn, ppn_rp, pph, pph_rp, pembulatan, materai_rp,
            keterangan1 aktivitas1, keterangan2 aktivitas2 
            from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' and DATE_FORMAT(tglbr,'%Y-%m') BETWEEN '$tgl1' AND '$tgl2' $f_via $f_pajak";
        
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        $query = "select a.*, b.NAMA4, '' as nama_karyawan, '' as nama_karyawan2, d.nama nama_cabang, '' as nama_area from $tmp01 a LEFT JOIN dbmaster.coa_level4 b on a.COA4=b.COA4 "
                . " LEFT JOIN MKT.icabang_o d on a.icabangid_o=d.icabangid_o ";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        
        $query = "select distinct a.nomor, a.nodivisi, b.bridinput from dbmaster.t_suratdana_br a 
            JOIN dbmaster.t_suratdana_br1 b on a.idinput=b.idinput
            WHERE a.stsnonaktif<>'Y' 
            AND b.bridinput IN (select distinct brid from $tmp03 WHERE divisi='OTC')
            and a.divisi IN ('OTC') AND IFNULL(a.nomor,'')<>'' AND IFNULL(a.pilih,'')='Y'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        
    }elseif ($pdivisi_pilih=="KD") {
        $query = "select DISTINCT IFNULL(a.bridinput,'') as bridinput from dbmaster.t_suratdana_br1 as a "
                . " JOIN dbmaster.t_suratdana_br as b "
                . " on a.idinput=b.idinput WHERE a.kodeinput='E' AND IFNULL(b.stsnonaktif,'')<>'Y' "
                . " and CONCAT(b.subkode,b.jenis_rpt) IN ('01C')";
        $query = "create TEMPORARY table $tmp00 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        if ($pviasby=="Y") $f_via=" AND klaimId IN (select IFNULL(bridinput,'') FROM $tmp00) ";
        elseif ($pviasby=="T") $f_via=" AND klaimId NOT IN (select IFNULL(bridinput,'') FROM $tmp00) ";
        
        $query = "select 'IDR' as ccyId, klaimId brid, noslip, COA4, pengajuan as divprodid, distid icabangid,
            CAST('' AS CHAR(10)) as areaid, CAST('' AS CHAR(10)) as idcabang, 
            tgl, tgltrans, tglrpsby, 
            CAST('' AS CHAR(10)) karyawanId, CAST('' AS CHAR(10)) karyawanI2, CAST('' AS CHAR(10)) dokterId, CAST('' AS CHAR(100)) as nama_dokter, 
            CAST('' AS CHAR(50)) dokter, realisasi1, CAST('' AS CHAR(5)) as via, jumlah, jumlah jumlah1, 
            pajak, nama_pengusaha, noseri, tgl_fp, noseri_pph, tgl_fp_pph, CAST('' AS CHAR(5)) as jenis_dpp, 
            CAST('' AS decimal(20,2)) as jasa_rp, dpp, ppn, ppn_rp, pph, pph_rp, pembulatan, CAST('' AS decimal(20,2)) as materai_rp,
            aktivitas1, aktivitas2 
            from hrd.klaim WHERE 1=1 and DATE_FORMAT(tgl,'%Y-%m') BETWEEN '$tgl1' AND '$tgl2' $f_via $f_pajak";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        $query = "select a.*, b.NAMA4, '' as nama_karyawan, '' as nama_karyawan2, d.nama nama_cabang, '' as nama_area from $tmp01 a LEFT JOIN dbmaster.coa_level4 b on a.COA4=b.COA4 "
                . " LEFT JOIN MKT.distrib0 d on a.icabangid=d.distid ";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        $query = "select distinct a.nomor, a.nodivisi, b.bridinput from dbmaster.t_suratdana_br a 
            JOIN dbmaster.t_suratdana_br1 b on a.idinput=b.idinput
            WHERE a.stsnonaktif<>'Y' 
            AND b.bridinput IN (select distinct brId from $tmp03 WHERE divisi<>'OTC')
            and a.divisi IN ('EAGLE') AND b.kodeinput='E'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
    }else{
        $query = "select ccyId, brid, noslip, COA4, divprodid, icabangid, areaid, idcabang, tgl, tgltrans, tglrpsby, 
            karyawanId, karyawanI2, dokterId, dokter, realisasi1, via, jumlah, jumlah1, 
            pajak, nama_pengusaha, noseri, tgl_fp, noseri_pph, tgl_fp_pph, jenis_dpp, jasa_rp, dpp, ppn, ppn_rp, pph, pph_rp, pembulatan, materai_rp,
            aktivitas1, aktivitas2 
            from hrd.br0 WHERE IFNULL(batal,'')<>'Y' and DATE_FORMAT(tgl,'%Y-%m') BETWEEN '$tgl1' AND '$tgl2' $f_via $f_pajak";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }


        $query = "select a.*, b.nama nama_dokter from $tmp01 a LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        $query = "select a.*, b.NAMA4, c.nama nama_karyawan, d.nama nama_karyawan2, e.nama nama_cabang, f.nama nama_area from $tmp02 a LEFT JOIN dbmaster.coa_level4 b on a.COA4=b.COA4 "
                . " LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId "
                . " LEFT JOIN hrd.karyawan d on a.karyawanI2=d.karyawanId "
                . " LEFT JOIN MKT.icabang e on a.icabangid=d.icabangid "
                . " LEFT JOIN MKT.iarea f on a.icabangid=e.icabangid AND a.areaid=f.areaid";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        $query = "select distinct a.nomor, a.nodivisi, b.bridinput from dbmaster.t_suratdana_br a 
            JOIN dbmaster.t_suratdana_br1 b on a.idinput=b.idinput
            WHERE a.stsnonaktif<>'Y' 
            AND b.bridinput IN (select distinct brId from $tmp03 WHERE divisi<>'OTC')
            and a.divisi IN ('PIGEO', 'PEACO', 'HO', 'EAGLE') AND IFNULL(a.nomor,'')<>'' AND IFNULL(a.pilih,'')='Y'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
    
    }
        
    
    if (empty($f_via) OR $pviasby=="T") {
        $query="select a.*, b.nomor, b.nodivisi from $tmp03 a JOIN $tmp04 b on a.brid=b.bridinput";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
    }else{
        //$query="select *, CAST('' as CHAR(20)) as nomor, CAST('' as CHAR(20)) as nodivisi from $tmp03";
        //$query = "create TEMPORARY table $tmp05 ($query)";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        $query="select a.*, b.nomor, b.nodivisi from $tmp03 a LEFT JOIN $tmp04 b on a.brid=b.bridinput";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        
    }
    
?>

<form method='POST' action='' id='d-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        <table id='dtablebrsby' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='100px'></th>
                    <th width='100px'>No ID</th>
                    <th width='100px'>M. Uang</th>
                    <th width='30px'>Jumlah</th>
                    <th width='30px'>Jml. Realisasi</th>
                    <th width='30px'>Selisih</th>
                    <th width='50px'>COA</th>
                    <th width='30px'>PERKIRAAN</th>
                    <th width='20px'>Tgl Pengajuan</th>
                    <th width='20px'>Tgl Transfer</th>
                    <th width='30px'>Yang Membuat</th>
                    <th width='250px'>Dokter/Customer/Supplier</th>
                    <th width='30px'>No Slip</th>
                    <th width='30px'>Realisasi</th>
                    <th width='30px'>Keterangan</th>
                    
                    <th width='30px'>Pajak</th>
                    <th width='30px'>Nama Pengusaha</th>
                    <th width='30px'>No Seri PPN</th>
                    <th width='30px'>Tgl. FP PPN</th>
                    <th width='30px'>DPP</th>
                    <th width='30px'>PPN</th>
                    
                    <th width='30px'>No Seri PPH</th>
                    <th width='30px'>Tgl. FP PPH</th>
                    
                    <th width='30px'>PPH</th>
                    <th width='30px'>Pembulatan</th>
                    <th width='30px'>Materai</th>
                    
                    <th width='30px'>No. SPD</th>
                    <th width='30px'>No. BR/Divisi</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select * from $tmp05 order by divprodid, brid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $pidbr=$row['brid'];
                    $pccyid=$row['ccyId'];
                    $pdivisi=$row['divprodid'];
                    $pcoa=$row['COA4'];
                    $pnmcoa=$row['NAMA4'];
                    
                    $ptglbr=$row['tgl'];
                    $ptglbr=date("d/m/Y", strtotime($ptglbr));
                    $ptgltrans="";
                    if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00") $ptgltrans=date("d/m/Y", strtotime($row['tgltrans']));
                    
                    $pkaryawanid=$row['karyawanId'];
                    $pnmkaryawan=$row['nama_karyawan'];
                    $pkaryawanid2=$row['karyawanI2'];
                    $pnmkaryawan2=$row['nama_karyawan2'];
                    
                    $pnmdokter=$row['nama_dokter'];
                    if (empty($pnmdokter)) $pnmdokter=$row['dokter'];
                    
                    $pnoslip=$row['noslip'];
                    $pnmrealisasi=$row['realisasi1'];
                    $paktivitas=$row['aktivitas1'];
                    
                    $pjumlah=$row['jumlah'];
                    $pjumlah1=$row['jumlah1'];
                    $psisa=(double)$pjumlah-(double)$pjumlah1;
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $pjumlah1=number_format($pjumlah1,0,",",",");
                    $psisa=number_format($psisa,0,",",",");
                    
                    $ppajak=$row['pajak'];
                    $pnmpengusaha=$row['nama_pengusaha'];
                    
                    $pnoseri=$row['noseri'];
                    $ptglfp="";
                    if (!empty($row['tgl_fp']) AND $row['tgl_fp']<>"0000-00-00") $ptglfp=date("d/m/Y", strtotime($row['tgl_fp']));
                    
                    $pnoseri_pph=$row['noseri_pph'];
                    $ptglfp_pph="";
                    if (!empty($row['tgl_fp_pph']) AND $row['tgl_fp_pph']<>"0000-00-00") $ptglfp_pph=date("d/m/Y", strtotime($row['tgl_fp_pph']));
                    
                    
                    $pjasarp=$row['jasa_rp'];
                    $pdpp=$row['dpp'];
                    if ((double)$pjasarp<>0) $pdpp=$pjasarp;
                    $pppnrp=$row['ppn_rp'];
                    $ppphrp=$row['pph_rp'];
                    $ppembulatan=$row['pembulatan'];
                    $pmaterai=$row['materai_rp'];
                    
                    $pjasarp=number_format($pjasarp,0,",",",");
                    $pdpp=number_format($pdpp,0,",",",");
                    $pppnrp=number_format($pppnrp,0,",",",");
                    $ppphrp=number_format($ppphrp,0,",",",");
                    $ppembulatan=number_format($ppembulatan,0,",",",");
                    $pmaterai=number_format($pmaterai,0,",",",");
                    
                    
                    $pnomor=$row['nomor'];
                    $pnodivisi=$row['nodivisi'];
                    
                    
                    $btntrans="<button type='button' class='btn btn-info btn-xs' title='Isi Data Transfer' data-toggle='modal' "
                            . " data-target='#myModal' "
                            . " onClick=\"getInputDataTransferSBY('$pdivisi_pilih', '$pidbr', '$mytgl1', '$mytgl2', '$pviasby', '$ppajak_')\">Transfer</button>";
                    
                    $btnpajak="<button type='button' class='btn btn-success btn-xs' title='Isi Data Pajak' data-toggle='modal' "
                            . " data-target='#myModal' "
                            . " onClick=\"getInputDataPajakSBY('$pdivisi_pilih', '$pidbr')\">Pajak</button>";
                    
                    if ($ppajak!="Y") $btnpajak="";
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$btntrans $btnpajak</td>";
                    echo "<td>$pidbr</td>";
                    echo "<td>$pccyid</td>";
                    echo "<td>$pjumlah</td>";
                    echo "<td>$pjumlah1</td>";
                    echo "<td>$psisa</td>";
                    echo "<td>$pcoa</td>";
                    echo "<td>$pnmcoa</td>";
                    echo "<td>$ptglbr</td>";
                    echo "<td>$ptgltrans</td>";
                    echo "<td>$pnmkaryawan</td>";
                    echo "<td>$pnmdokter</td>";
                    echo "<td>$pnoslip</td>";
                    echo "<td>$pnmrealisasi</td>";
                    echo "<td>$paktivitas</td>";
                    
                    echo "<td>$ppajak</td>";
                    echo "<td>$pnmpengusaha</td>";
                    echo "<td>$pnoseri</td>";
                    echo "<td>$ptglfp</td>";
                    echo "<td>$pdpp</td>";
                    echo "<td>$pppnrp</td>";
                    
                    echo "<td>$pnoseri_pph</td>";
                    echo "<td>$ptglfp_pph</td>";
                    
                    echo "<td>$ppphrp</td>";
                    echo "<td>$ppembulatan</td>";
                    echo "<td>$pmaterai</td>";
                    
                    echo "<td>$pnomor</td>";
                    echo "<td>$pnodivisi</td>";
                    
                    echo "</tr>";
                    
                    $no++;
                }
            ?>
            </tbody>
        </table>
        
    </div>

</form>

<script>
    
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#dtablebrsby').DataTable( {
            //"stateSave": true,
            //"order": [[ 7, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [4,5,6, 17,18,19,20,21,22] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6,7,8,9,10,11,12,13, 14,15,16,17,18,19,20,21,22, 23,24, 25,26] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true
        } );
        //$('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function getInputDataTransferSBY(ddivisi, didbr, tgl1, tgl2, via, pajak){
        $.ajax({
            type:"post",
            url:"module/surabaya/mod_sby_dbr/tambahdatatransfer.php?module=tambahdatatransfer",
            data:"udivisi="+ddivisi+"&uidbr="+didbr+"&utgl1="+tgl1+"&utgl2="+tgl2+"&uvia="+via+"&upajak="+pajak,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getInputDataPajakSBY(ddivisi, didbr){
        $.ajax({
            type:"post",
            url:"module/surabaya/mod_sby_dbr/tambahdatapajak.php?module=tambahdatapajak",
            data:"udivisi="+ddivisi+"&uidbr="+didbr,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
</script>

<style>
    .divnone {
        display: none;
    }
    #dtablebrsby th {
        font-size: 13px;
    }
    #dtablebrsby td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>


<?PHP
hapudata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
?>
