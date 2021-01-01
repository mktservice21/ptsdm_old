<?php
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $pprostipe="";//$_POST['uprostipe'];
    $ptgltipe="";//$_POST['utgltipe'];
    $pdate1=$_POST['uperiode1'];
    $pdate2=$_POST['uperiode2'];
    $ptgl1= date("Y-m-01", strtotime($pdate1));
    $ptgl2= date("Y-m-t", strtotime($pdate2));
    $pdivisiid=$_SESSION['DIVISI'];//$_POST['udivisi'];
    $pidcard=$_SESSION['IDCARD'];//$_POST['uidc'];
    $pcabangid="";//$_POST['uarea'];
    
    
    //$_SESSION['FINCAATIPEOTC']=$pprostipe;
    //$_SESSION['FINCAATGLTIPEOTC']=$ptgltipe;
    $_SESSION['FINRUTPERENTY1']=$pdate1;
    $_SESSION['FINRUTPERENTY2']=$pdate2;
    //$_SESSION['FINCAADIVOTC']=$pdivisiid;
    //$_SESSION['FINCAACABOTC']=$pcabangid;
    
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli.php";
    
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPRTCAOTCHO01_".$puserid."_$now ";
    $tmp02 =" dbtemp.TMPRTCAOTCHO02_".$puserid."_$now ";
    $tmp03 =" dbtemp.TMPRTCAOTCHO03_".$puserid."_$now ";
    $tmp04 =" dbtemp.TMPRTCAOTCHO04_".$puserid."_$now ";
    $tmp05 =" dbtemp.TMPRTCAOTCHO05_".$puserid."_$now ";
    
    
    $query = "SELECT userid, idrutin, tgl, bulan, periode1, periode2, "
            . " divisi, karyawanid, icabangid_o icabangid, areaid_o areaid, jumlah, keterangan, "
            . " jabatanid, tgl_atasan1, tgl_atasan2, tgl_atasan3, tgl_atasan4, validate, fin, tgl_fin, nama_karyawan ";
    $query .=" FROM dbmaster.t_brrutin0 WHERE IFNULL(stsnonaktif,'')<>'Y' AND "
            . " ( (tgl BETWEEN '$ptgl1' AND '$ptgl2') OR (bulan BETWEEN '$ptgl1' AND '$ptgl2') ) ";
    $query .=" AND divisi='OTC' ";

    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "SELECT a.*, b.nama, c.nama nama_cabang, d.nama nama_area, CAST(0 as DECIMAL(20,2)) rptotal, CAST('' as CHAR(1)) as adabukti, CAST('' as CHAR(1)) as ssudah "
            . " FROM $tmp01 a LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang_o c on a.icabangid=c.icabangid_o "
            . " LEFT JOIN MKT.iarea_o d on a.icabangid=d.icabangid_o AND a.areaid=d.areaid_o";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 SET nama=nama_karyawan where karyawanid IN ('0000002200', '0000002083')"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b on "
            . " a.idinput=b.idinput where IFNULL(b.stsnonaktif,'')<>'Y' AND b.divisi='OTC' AND CONCAT(b.kodeid, b.subkode)='221' "
            . " bridinput IN (select distinct IFNULL(idrutin,'') FROM $tmp02)";
    //$query = "create TEMPORARY table $tmp03 ($query)"; 
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct divisi, DATE_FORMAT(bulan,'%Y%m') as bulan from dbmaster.t_brrutin_ca_close_otc WHERE bulan BETWEEN '$ptgl1' AND '$ptgl2' ";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select distinct idrutin from dbimages.img_brrutin1 WHERE idrutin IN (select distinct IFNULL(idrutin,'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //rincian
    $query = "select idrutin, sum(rptotal) as rptotal from dbmaster.t_brrutin1 WHERE idrutin IN (select distinct IFNULL(idrutin,'') FROM $tmp02) GROUP BY 1";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.divisi=b.divisi AND DATE_FORMAT(a.bulan,'%Y%m')=b.bulan SET a.ssudah='C'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN $tmp04 b on a.idrutin=b.idrutin SET a.adabukti='Y'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN $tmp05 b on a.idrutin=b.idrutin SET a.rptotal=b.rptotal"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablertnotcho' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='70px'></th>
                    <th width='40px'>No ID</th>
                    <th width='80px'>Yang Membuat</th>
                    <th width='40px'>Tgl. Input</th>
                    <th width='40px'>Periode</th>
                    <th width='50px'>Jumlah</th>
                    <th width='40px'>Bukti</th>
                    <th width='50px'>Keterangan</th>
                    <th width='30px'>Approve HOS</th>
                    <th width='30px'>Proses Finance</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "SELECT * FROM $tmp02 ";
                $query .=" ORDER BY idrutin DESC";
                $no=1;
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidno=$row['idrutin'];
                    $pkaryawanid=$row['karyawanid'];
                    $pnama=$row["nama"];
                    $ptglinput=$row["tgl"];
                    
                    $pperiode = $row["bulan"];
                    $pjumlah = $row["jumlah"];
                    $prptotal = $row["rptotal"];
                    $pket = $row["keterangan"];
                    $pgbrbukti = $row["adabukti"];
                    $psudahpros = $row["ssudah"]; //C = sudah closing LK dan CA
                    
                    $pper1 = $row["periode1"];
                    $pper2 = $row["periode2"];
                    
                    
                    $ptglinput= date("d/m/Y", strtotime($ptglinput));
                    $pperiode= date("M Y", strtotime($pperiode));
                    
                    $pper1= date("d/m/Y", strtotime($pper1));
                    $pper2= date("d/m/Y", strtotime($pper2));
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $prptotal=number_format($prptotal,0,",",",");
                    
                    $t_ats1 = $row["tgl_atasan1"];
                    $t_ats2 = $row["tgl_atasan2"];
                    $t_ats3 = $row["tgl_atasan3"];
                    $t_ats4 = $row["tgl_atasan4"];
                    $pjabatanid=$row['jabatanid'];
                    
                    $apv1="";
                    $apv2="";
                    $apv3="";
                    $apvfin="";
                    
                    
                    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidno'>Edit</a>";
                    $phapus="<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesDataHapusCa('hapus', '$pidno')\">";
                    $pprint="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrcash&brid=$pidno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "Print</a>";
                    
                    $pprint="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrrutin&brid=$pidno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "Print</a>";
    
                    
                    if ($psudahpros=="C") {
                        //$pedit = "";
                        $phapus = "";
                    }
                    
                    $allbutton="$pedit $phapus $pprint";
                    
                    $warna="btn btn-success btn-xs";
                    if (!empty($pgbrbukti)) $warna="btn btn-danger btn-xs";
                    $upload="<a class='$warna' href='?module=$pmodule&act=uploaddok&idmenu=$pidmenu&nmun=$pidmenu&id=$pidno'>Upload</a>";
                    
                    $pcolorrpbeda="";
                    if ($pjumlah<>$prptotal) $pcolorrpbeda=" style='color:red;' ";
                    
                    echo "<tr $pcolorrpbeda>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$allbutton</td>";
                    echo "<td nowrap>$pidno</td>";
                    echo "<td nowrap>$pnama</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td nowrap>$pper1 s/d. $pper2</td>";
                    echo "<td nowrap>$pjumlah</td>";
                    
                    echo "<td nowrap>$upload</td>";
                    echo "<td nowrap>$pket</td>";
                    echo "<td nowrap>$apv1</td>";
                    echo "<td nowrap>$apvfin</td>";
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
        var dataTable = $('#datatablertnotcho').DataTable( {
            //"stateSave": true,
            //"order": [[ 2, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    

    function ProsesDataHapusCa(ket, noid){
        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var txt;
                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        txt = textket;
                    } else {
                        txt = textket;
                    }
                }


                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                document.getElementById("d-form2").action = "module/mod_br_brrutinotcho/aksi_entrybrcashotcho.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablertnotcho th {
        font-size: 13px;
    }
    #datatablertnotcho td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>



<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
    
    mysqli_close($cnmy);
?>