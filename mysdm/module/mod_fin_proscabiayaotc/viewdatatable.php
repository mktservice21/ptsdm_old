<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $kodeinput = " AND kode=3 ";
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    $divisi = $_POST['udiv'];
    
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
    
    
?>


<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    
                </h4>
            </div><div class="clearfix">
        </div>
        <table id='datatablecabiayafinotc' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='100px'>Yg Membuat</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Tgl Input</th>
                    <th width='50px'>Periode</th>
                    <th width='30px'>Bukti</th>
                    <th width='250px'>Keterangan</th>
                    <th width='30px'>ID</th>
                    <th width='30px'>Pengajuan</th>
                    <th width='30px'>Approve</th>
                    <th width='30px'>Proses Finance</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                
                $userid=$_SESSION['USERID'];
                $now=date("mdYhis");
                $tmp01 =" dbtemp.DSETHCABOTC01_".$userid."_$now ";
                $tmp02 =" dbtemp.DSETHCABOTC02_".$userid."_$now ";
                $tmp03 =" dbtemp.DSETHCABOTC03_".$userid."_$now ";
                
                $filterpilih="";
                //FILTER
                if (strtoupper($cket)!= "REJECT") $filterpilih.=" AND stsnonaktif <> 'Y' ";
                
                if (strtoupper($cket)=="APPROVE") {
                    $filterpilih.=" AND IFNULL(tgl_fin,'')='' ";
                }elseif (strtoupper($cket)=="UNAPPROVE") {
                    $filterpilih.=" AND IFNULL(tgl_fin,'')<>'' ";
                }elseif (strtoupper($cket)=="REJECT") {
                    $filterpilih.=" AND stsnonaktif = 'Y' ";
                }elseif (strtoupper($cket)=="PENDING") {
                    
                }

                if ( (strtoupper($cket)!="SEMUA") AND strtoupper($cket)!= "REJECT" AND strtoupper($cket)!= "SUDAHFIN") {
                    
                }
                
                
                
                //END FILTER
                
                $query = "SELECT
                    br.divi AS divi,
                    br.kode AS kode,
                    br.divisi AS divisi,
                    br.idca AS idca,
                    br.karyawanid AS karyawanid,
                    k.nama AS nama,
                    br.jabatanid AS jabatanid,
                    br.tgl AS tgl,
                    br.bulan AS bulan,
                    br.periode AS periode,
                    br.jumlah AS jumlah,
                    br.keterangan AS keterangan,
                    br.stsnonaktif AS stsnonaktif,
                    br.icabangid AS icabangid,
                    br.icabangid_o AS icabangid_o,
                    br.areaid AS areaid,
                    br.areaid_o AS areaid_o,
                    br.atasan1 AS atasan1,
                    br.atasan2 AS atasan2,
                    br.atasan3 AS atasan3,
                    br.atasan4 AS atasan4,
                    br.tgl_atasan1 AS tgl_atasan1,
                    br.tgl_atasan2 AS tgl_atasan2,
                    br.tgl_atasan3 AS tgl_atasan3,
                    br.tgl_atasan4 AS tgl_atasan4,
                    br.validate AS validate,
                    br.validate_date AS validate_date,
                    br.fin AS fin,
                    br.tgl_fin AS tgl_fin,
                    br.userid AS userid
                    FROM dbmaster.t_ca0 br JOIN hrd.karyawan k ON br.karyawanid = k.karyawanId WHERE br.divisi='OTC' ";
                
                $sql = "SELECT idca, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, DATE_FORMAT(periode,'%M %Y') as bulan, DATE_FORMAT(tgl,'%d/%m/%Y') as periode, "
                        . " divisi, karyawanid, nama, areaid, '' as nama_area, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan 
                        , ifnull(tgl_atasan1,'0000-00-00') tgl_atasan1,
                        ifnull(tgl_atasan2,'0000-00-00') tgl_atasan2,
                        ifnull(tgl_atasan3,'0000-00-00') tgl_atasan3,
                        ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
                        ifnull(tgl_fin,'0000-00-00') tgl_fin, jabatanid, '3' kode, CAST('Cash Advance' AS CHAR(100)) as iketbr ";
                
                $sql.=" FROM ($query) as TBL ";
                $sql.=" WHERE 1=1 ";
                $sql.=" AND ( (Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2') OR (Date_format(periode, '%Y-%m') between '$tgl1' and '$tgl2') ) ";
                
                $sql.=" $filterpilih ";

                

                $query = "create TEMPORARY table $tmp01 ($sql)"; 
                mysqli_query($cnmy, $query);
                
                
                //rutin dan lk
                $query = "SELECT
                    br.divi AS divi,
                    br.kode AS kode,
                    br.divisi AS divisi,
                    br.idrutin AS idrutin,
                    br.karyawanid AS karyawanid,
                    k.nama AS nama,
                    br.jabatanid AS jabatanid,
                    br.tgl AS tgl,
                    br.bulan AS bulan,
                    br.periode1 AS periode1,
                    br.periode2 AS periode2,
                    br.jumlah AS jumlah,
                    br.keterangan AS keterangan,
                    br.stsnonaktif AS stsnonaktif,
                    br.icabangid AS icabangid,
                    br.icabangid_o AS icabangid_o,
                    br.areaid AS areaid,
                    br.areaid_o AS areaid_o,
                    br.atasan1 AS atasan1,
                    br.atasan2 AS atasan2,
                    br.atasan3 AS atasan3,
                    br.atasan4 AS atasan4,
                    br.tgl_atasan1 AS tgl_atasan1,
                    br.tgl_atasan2 AS tgl_atasan2,
                    br.tgl_atasan3 AS tgl_atasan3,
                    br.tgl_atasan4 AS tgl_atasan4,
                    br.validate AS validate,
                    br.validate_date AS validate_date,
                    br.fin AS fin,
                    br.tgl_fin AS tgl_fin,
                    br.userid AS userid,
                    br.nama_karyawan AS nama_karyawan
                    FROM dbmaster.t_brrutin0 br JOIN hrd.karyawan k ON br.karyawanid = k.karyawanId WHERE br.divisi='OTC' ";
                
                $sql = "SELECT idrutin, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, DATE_FORMAT(bulan,'%M %Y') as bulan, DATE_FORMAT(periode1,'%d/%m/%Y') as periode1, "
                        . " DATE_FORMAT(periode2,'%d/%m/%Y') as periode2, "
                        . " divisi, karyawanid, nama, areaid, '' as nama_area, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan 
                        , ifnull(tgl_atasan1,'0000-00-00') tgl_atasan1,
                        ifnull(tgl_atasan2,'0000-00-00') tgl_atasan2,
                        ifnull(tgl_atasan3,'0000-00-00') tgl_atasan3,
                        ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
                        ifnull(tgl_fin,'0000-00-00') tgl_fin, jabatanid, kode, CAST('Biaya Rutin' AS CHAR(100)) as iketbr ";
                $sql.=" FROM ($query) as TBL ";
                $sql.=" WHERE 1=1 ";
                
                $sql.=" AND ( (Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2') OR (Date_format(periode1, '%Y-%m') between '$tgl1' and '$tgl2') "
                        . " OR (Date_format(periode2, '%Y-%m') between '$tgl1' and '$tgl2') ) ";
                
                
                $sql.=" $filterpilih ";
                
                $query = "create TEMPORARY table $tmp02 ($sql)"; 
                mysqli_query($cnmy, $query);
                
                mysqli_query($cnmy, "UPDATE $tmp02 SET iketbr='Biaya Luar Kota' WHERE kode=2");
                
                
                
                $query = "create TEMPORARY table $tmp03 (select * from $tmp02)"; 
                mysqli_query($cnmy, $query);
                
                $query = "INSERT INTO $tmp03 (idrutin, tgl, bulan, periode1, periode2, divisi, karyawanid, nama, areaid, nama_area, jumlah, "
                        . " keterangan, tgl_atasan1, tgl_atasan2, tgl_atasan3, tgl_atasan4, tgl_fin, jabatanid, iketbr, kode) "
                        . " select idca, tgl, bulan, periode as periode1, periode as periode2, divisi, karyawanid, nama, areaid, nama_area, jumlah, "
                        . " keterangan, tgl_atasan1, tgl_atasan2, tgl_atasan3, tgl_atasan4, tgl_fin, jabatanid, iketbr, kode FROM $tmp01";
                mysqli_query($cnmy, $query);
                
                $sql ="select * from $tmp03 order by iketbr DESC, idrutin";
                //echo $sql;
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idrutin'];
                    $tglbuat = $row["tgl"];
                    $nama = $row["nama"];
                    $nmarea = $row["nama_area"];
                    $bulan = date("F Y", strtotime($row["bulan"]));
                    $periode = $row["tgl"];
                    $pbulan = $row["bulan"];
                    $jumlah = $row["jumlah"];
                    $keterangan = $row["keterangan"];
                    $ptipe = $row["iketbr"];
                    $pkode = $row["kode"];
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
                    
                    if ($pkode=="3") {
                        $pbukti = getfieldcnmy("SELECT idca as lcfields from dbimages.img_ca0 where idca='$idno'");
                        $pnamamodul="entrybrcashotc";
                    }else{
                        $pbulan = $row["periode1"]." s/d. ".$row["periode2"];

                        $pbukti = getfieldcnmy("SELECT idrutin as lcfields from dbimages.img_brrutin1 where idrutin='$idno'");
                        $pnamamodul="entrybrrutinotc";
                    }
                    
                    $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=$pnamamodul&brid=$idno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$nama</a>";
                    
                    $bukti="";
                    if (!empty($pbukti)) {
                        $bukti="<a title='lihat bukti' href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=$pnamamodul&brid=$idno&iprint=bukti',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Lihat</a>";
                    }
    
                    
                    $edit="";
                    if ($pkode=="1") {
                        $pmmoduledit="module=finprosbiayarutin&idmenu=162&brid=$idno&iprint=editrutin";
                    }elseif ($pkode=="2") {
                        $pmmoduledit="module=finprosbiayaluar&idmenu=160&brid=$idno&iprint=editrutin";
                    }else{
                        $pmmoduledit="module=finprosca&idmenu=164&brid=$idno&iprint=editrutin";
                    }
                    if (strtoupper($cket)=="APPROVE") {
                        $edit="<a title='lihat bukti' href='#' class='btn btn-success btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?$pmmoduledit',"
                            . "'Ratting','width=1000,height=600,left=200,top=50,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Edit</a>";
                    }
                    
                    
                    $apv1="";
                    $apv2="";
                    $apv3="";
                    $apv4="";
                    $apvfin="";
                    
                    $pjabat = $row["jabatanid"];
                    if ((int)$pjabat==20 OR (int)$pjabat==5) {
                    }else{
                        if ($row["tgl_atasan1"] <> "0000-00-00") $apv1=date("d F Y, h:i:s", strtotime($row["tgl_atasan1"]));
                        if ($row["tgl_atasan2"] <> "0000-00-00") $apv2=date("d F Y, h:i:s", strtotime($row["tgl_atasan2"]));
                    }
                    if ($row["tgl_atasan3"] <> "0000-00-00") $apv3=date("d F Y, h:i:s", strtotime($row["tgl_atasan3"]));
                    if ($row["tgl_atasan4"] <> "0000-00-00") $apv4=date("d F Y, h:i:s", strtotime($row["tgl_atasan4"]));
                    if ($row["tgl_fin"] <> "0000-00-00") $apvfin=date("d F Y, h:i:s", strtotime($row["tgl_fin"]));
                    
                    if (empty($apv4)) {
                        $cekbox = "";
                        $edit="";
                    }
                    if ( (strtoupper($cket)=="SEMUA") OR strtoupper($cket)== "REJECT" OR strtoupper($cket)== "SUDAHFIN") {
                        $cekbox="";
                        $edit="";
                    }
                    
                    echo "<tr>";
                    echo "<td>$cekbox</td>";
                    echo "<td>$print $edit</td>";
                    echo "<td>$jumlah</td>";
                    echo "<td>$periode</td>";
                    echo "<td>$pbulan</td>";
                    echo "<td>$bukti</td>";
                    echo "<td>$keterangan</td>";
                    echo "<td>$idno</td>";
                    echo "<td nowrap>$ptipe</td>";
                    echo "<td>$apv4</td>";
                    echo "<td>$apvfin</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    
<?PHP
if (strtoupper($cket)=="UNAPPROVE") {
?>
    <div class='clearfix'></div>
    <div class="well" style="margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;"><!--overflow: auto; -->
        <?PHP
        if (strtoupper($cket)=="APPROVE") {
            ?>
            <input class='btn btn-default' type='hidden' name='buttonapv' value='Pending' 
                   onClick="ProsesData('pending', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="UNAPPROVE") {
            ?>
            <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
                   onClick="ProsesData('unapprove', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="REJECT") {
        }elseif (strtoupper($cket)=="PENDING") {
        }elseif (strtoupper($cket)=="SEMUA") {
        }
        ?>
    </div>
<?PHP
}
?>
    
    <div class='clearfix'></div>


    <!-- tanda tangan -->
    <?PHP
        if (strtoupper($cket)=="APPROVE") {
            echo "<div class='col-sm-5'>";
            include "ttd_appvca.php";
            echo "</div>";
        }
    ?>
</form>

<?PHP
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>
<script>
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#datatablecabiayafinotc').DataTable( {
            //"stateSave": true,
            "order": [[ 7, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [2] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
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
    }
    
    function ProsesData(ket, cekbr){
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        var txt;
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
        var ekaryawan=document.getElementById('e_idkaryawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_proscabiayaotc/aksi_proscabiayaotc.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=approve"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData(ket);
                alert(data);
            }
        });
        
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablecabiayafinotc th {
        font-size: 13px;
    }
    #datatablecabiayafinotc td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>
