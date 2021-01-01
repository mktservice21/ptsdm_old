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
    
    $pgroupid=$_SESSION['GROUP'];
    $pcardid=$_SESSION['IDCARD'];
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $_SESSION['FINKLMTIPE']=$_POST['utipeproses'];
    $_SESSION['FINKLMTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['FINKLMPERENTY1']=$_POST['uperiode1'];
    $_SESSION['FINKLMPERENTY2']=$_POST['uperiode2'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $pdivisi=$_POST['udivisi'];
    
    
    include "../../config/koneksimysqli.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPBRKLMDC01_".$puserid."_$now ";
    $tmp02 =" dbtemp.TMPBRKLMDC02_".$puserid."_$now ";
    $tmp03 =" dbtemp.TMPBRKLMDC03_".$puserid."_$now ";
    
    
    $sql = "SELECT * FROM hrd.klaim WHERE 1=1 ";
    if ($pgroupid=="1" OR $pgroupid=="24" OR $pgroupid=="3") {
    }else{
        $sql.=" AND (user1='$puserid' OR user1='$pcardid') ";
    }


    $filtipe="Date_format(tgl, '%Y-%m-%d')";
    if ($tgltipe=="2") $filtipe="Date_format(tgltrans, '%Y-%m-%d')";
    if ($tgltipe=="3") $filtipe="Date_format(tglrpsby, '%Y-%m-%d')";
    $sql.=" and $filtipe between '$tgl1' and '$tgl2' ";
    if (!empty($pdivisi)) $sql.=" and divprodid='$pdivisi' ";
    
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="SELECT
	k.klaimId AS klaimid,
	k.karyawanid AS karyawanid,
	kr.nama AS nama,
	k.distid AS distid,
	d.nama AS nama_distributor,
	k.aktivitas1 AS aktivitas1,
	k.aktivitas2 AS aktivitas2,
	k.jumlah AS jumlah,
	k.tgl AS tgl,
	k.realisasi1 AS realisasi1,
	k.noslip AS noslip,
	k.trf AS trf,
	k.tgltrans AS tgltrans,
	k.lampiran AS lampiran,
	k.user1 AS user1,
	k.tglrpsby AS tglrpsby,
	k.sby AS sby,
	k.app_owner AS app_owner,
	k.app_owner_date AS app_owner_date,
	k.app_director AS app_director,
	k.app_director_date AS app_director_date,
	k.acc AS acc,
	k.app_acc AS app_acc,
	k.COA4,
	coa.NAMA4,
	k.KODEWILAYAH,
	wil.nama nama_wilayah,
	k.pengajuan
        FROM
	$tmp01 k
	LEFT JOIN hrd.karyawan kr ON k.karyawanid = kr.karyawanId
	LEFT JOIN MKT.distrib0 d ON k.distid = d.Distid
	LEFT JOIN dbmaster.coa_level4 coa ON k.COA4 = coa.COA4
	LEFT JOIN dbmaster.t_wilayah AS wil ON wil.KODE = k.KODEWILAYAH";
    
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "Alter table $tmp02 ADD COLUMN nodivisi VARCHAR(50)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select distinct a.bridinput, b.nodivisi, b.pilih, a.amount, a.jml_adj, b.kodeid, b.subkode "
            . " from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput "
            . " WHERE b.stsnonaktif<>'Y' AND a.kodeinput IN ('E') AND a.bridinput IN (select distinct IFNULL(klaimid,'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 a JOIN (select distinct bridinput, nodivisi FROM $tmp03 WHERE IFNULL(pilih,'')='Y') b "
            . " ON a.klaimid=b.bridinput SET a.nodivisi=b.nodivisi";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct bridinput, nodivisi FROM $tmp03) b "
            . " ON a.klaimid=b.bridinput SET a.nodivisi=b.nodivisi WHERE IFNULL(a.nodivisi,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>


<form method='POST' action='<?PHP echo "?module='entrybrklaim'&act=input&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatableklaim' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th></th>
                    <th></th>
                    <th width='60px'>ID</th>
                    <th width='60px'>Tanggal</th>
                    <th width='60px'>Tgl. Transfer</th>
                    <th>Yg Membuat</th>
                    <th width='80px'>Distributor</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Realisasi</th>
                    <th width='50px'>No Slip</th>
                    <th>Lampiran</th>
                    <th width='50px'>Nodivisi</th>
                    <th width='50px'>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp02";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pklaimid=$row["klaimid"];
                    $ptlg = $row["tgl"];
                    $ptlgtrans = $row["tgltrans"];
                    $pnama = $row["nama"];
                    $pnamadist = $row["nama_distributor"];
                    $pjumlah = $row["jumlah"];
                    $pnmrealisasi = $row["realisasi1"];
                    $pnoslip = $row["noslip"];
                    $plampiran = $row["lampiran"];
                    $paktivitas1 = $row["aktivitas1"];
                    $paktivitas2 = $row["aktivitas2"];
                    $pnodivisi = $row["nodivisi"];
                    $pustinput = $row["user1"];
                    
                    $plink = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$pklaimid.">".$ptlg."</a>";
                    
                    $ptlg =date("d/m/Y", strtotime($ptlg));
                    
                    if ($ptlgtrans=="0000-00-00") $ptlgtrans="";
                    if (!empty($ptlgtrans)) $ptlgtrans =date("d/m/Y", strtotime($ptlgtrans));
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    
                    
                    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pklaimid'>Edit</a>";
                    $ppajak="<button type='button' class='btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInputPajak('$pklaimid')\">Pajak</button>";
                    $phapus="<input type='button' class='btn btn-danger btn-xs' value='Hapus' "
                                        . "onClick=\"ProsesDataHapus('hapus', '$pklaimid', '$pnodivisi')\">";
                    
                    if ((DOUBLE)$pustinput!=$puserid) {
                        $phapus="";
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>$pedit $ppajak $phapus</td>";
                    echo "<td nowrap>$pklaimid</td>";
                    echo "<td nowrap>$ptlg</td>";
                    echo "<td nowrap>$ptlgtrans</td>";
                    echo "<td nowrap>$pnama</td>";
                    echo "<td nowrap>$pnamadist</td>";
                    echo "<td nowrap>$pjumlah</td>";
                    echo "<td nowrap>$pnmrealisasi</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$plampiran</td>";
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap>$paktivitas1 $paktivitas2</td>";
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
        var dataTable = $('#datatableklaim').DataTable( {
            "stateSave": true,
            fixedHeader: true,
            //"ordering": false,
            "processing": true,
            "order": [[ 0, "asc" ], [ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": false, "targets": 2 },
                { "orderable": true, "targets": 4 },
                { className: "text-right", "targets": [8] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,12] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function ProsesDataHapus(ket, noid, snodivi){

        ok_ = 1;
        if (ok_) {
            if (snodivi=="") {
                var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            }else{
                var r = confirm('Sudah Ada Nodivisi /no BR ('+snodivi+')...!!!\n\
Apakah akan melakukan proses '+ket+' ...?\n\
Status pada SPD akan berubah menjadi BATAL (merah)...');
            }
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

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_entryklaim/aksi_entryklaim.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
    
    function TambahDataInputPajak(eidbr){
        $.ajax({
            type:"post",
            url:"module/mod_br_entryklaim/tambah_pajak.php?module=viewisipajak",
            data:"uidbr="+eidbr,
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
    #datatableklaim th {
        font-size: 12px;
    }
    #datatableklaim td { 
        font-size: 11px;
    }
</style>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>