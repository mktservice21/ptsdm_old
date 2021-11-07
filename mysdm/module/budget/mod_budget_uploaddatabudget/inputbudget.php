<?php
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    //ini_set("memory_limit","1G");
    ini_set('max_execution_time', 0);

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcard=$_SESSION['IDCARD'];
    $pidgroup=$_SESSION['GROUP'];
    $pidjabatan=$_SESSION['JABATANID'];
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $ptahunpilih=$_POST['utahun'];
    $pdivpilih=$_POST['udivpl'];
    $pkryawanid=$_POST['ukryid'];
    $pdeptid=$_POST['udptid'];
    $pcabid=$_POST['ucabid'];
    $pbln=$_POST['ubln'];
    $psortby=$_POST['usortby'];
    $pdivisi_pl="";
    
    $_SESSION['BGTUPDTHN']=$ptahunpilih;
    $_SESSION['BGTUPDDVL']=$pdivpilih;
    $_SESSION['BGTUPDKRY']=$pkryawanid;
    $_SESSION['BGTUPDDPT']=$pdeptid;
    $_SESSION['BGTUPDCAB']=$pcabid;
    
    
    $nnmbln="";

    if ((INT)$pbln==1) $nnmbln="Januari";
    elseif ((INT)$pbln==2) $nnmbln="Februari";
    elseif ((INT)$pbln==3) $nnmbln="Maret";
    elseif ((INT)$pbln==4) $nnmbln="April";
    elseif ((INT)$pbln==5) $nnmbln="Mei";
    elseif ((INT)$pbln==5) $nnmbln="Juni";
    elseif ((INT)$pbln==7) $nnmbln="Juli";
    elseif ((INT)$pbln==8) $nnmbln="Agustus";
    elseif ((INT)$pbln==9) $nnmbln="September";
    elseif ((INT)$pbln==10) $nnmbln="Oktober";
    elseif ((INT)$pbln==11) $nnmbln="November";
    elseif ((INT)$pbln==12) $nnmbln="Desember";
                                                    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpdtpstdep01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpdtpstdep02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpdtpstdep03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpdtpstdep04_".$puserid."_$now ";
    
    $query = "select b.deskripsi, b.igroup, a.postingid, b.posting_nama, a.kodeid, a.iddep, "
            . " a.divisi, a.all_dep, b.notes_tabel, b.field_id, b.field_nm "
            . " from dbmaster.posting_akun_dep as a "
            . " join dbmaster.posting_akun as b on a.postingid=b.postingid WHERE IFNULL(b.aktif,'')<>'N' "
            . " AND a.iddep='$pdeptid' ";
    
    if ($pdeptid=="MKT" AND !empty($pdivisi_pl) AND $pdivisi_pl<>"HO" AND $pdivisi_pl<>"OTC") {
        $query .=" AND a.divisi in ('$pdivisi_pl', 'HO') ";
    }else{
        if (!empty($pdivisi_pl)) {
            $query .=" AND a.divisi='$pdivisi_pl' ";
        }
    }
    
    if ($pdivpilih=="ETH") {
        $query .=" AND a.divisi NOT IN ('OTC') ";
    }elseif ($pdivpilih=="OTC" OR $pdivpilih=="OT") {
        $query .=" AND a.divisi IN ('$pdivpilih', 'OTHER') ";
    }
    
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD COLUMN nama_group VARCHAR(200), ADD COLUMN nama_kodeid VARCHAR(200), ADD COLUMN coa4 VARCHAR(50), ADD COLUMN nama_coa VARCHAR(300), ADD COLUMN nmgroup_dep varchar(100)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select distinct igroup, posting_nama from dbmaster.posting_akun) as b on a.igroup=b.igroup "
            . " SET a.nama_group=b.posting_nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.posting_akun_divsi_coa as b on a.postingid=b.postingid AND a.kodeid=b.kodeid AND a.divisi=b.divisi SET a.coa4=b.coa4";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.coa_level4 as b on a.coa4=b.COA4 SET a.nama_coa=b.NAMA4";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select distinct notes_tabel, field_id, field_nm FROM $tmp01 WHERE IFNULL(notes_tabel,'')<>'' AND IFNULL(field_id,'')<>'' AND IFNULL(field_nm,'')<>'' ORDER BY notes_tabel";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pnmtabel=$row['notes_tabel'];
        $pfieldid=$row['field_id'];
        $pfieldnm=$row['field_nm'];
        
        $query_upt="UPDATE $tmp01 as a JOIN $pnmtabel as b on a.kodeid=b.$pfieldid SET a.nama_kodeid=b.$pfieldnm WHERE notes_tabel='$pnmtabel'";
        mysqli_query($cnmy, $query_upt); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    $query = "UPDATE $tmp01 as a JOIN hrd.br_kode as b on a.postingid=b.postingid AND a.kodeid=b.kodeid SET a.divisi=b.divprodid WHERE LEFT(a.postingid,3)='01-'";
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.t_brid as b on a.postingid=b.postingid AND a.kodeid=b.nobrid SET "
            . " a.deskripsi=CASE WHEN b.kode='1' THEN 'BIAYA - RUTIN' 
                    ELSE CASE WHEN b.kode='2' THEN 'BIAYA - LUAR KOTA' 
                    ELSE CASE WHEN b.kode='3' THEN 'BIAYA - CASH ADVANCE' 
                    ELSE CASE WHEN b.kode='4' THEN 'BIAYA - MUTASI' 
                    ELSE CASE WHEN b.kode='5' THEN 'BIAYA - SERVICE KENDARAAN'
                    ELSE a.deskripsi 
                    END END END END END "
            . " WHERE LEFT(a.postingid,3)='04-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET nama_kodeid='Entertaint User' WHERE postingid='04-000037' AND kodeid='26'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //$query = "UPDATE $tmp01 as a JOIN dbmaster.t_department as b on a.iddep=b.iddep SET a.nmgroup_dep=b.nama_group";
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>

<script src="js/inputmask.js"></script>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class='col-md-12 col-sm-12 col-xs-12'>
    <div class='x_panel'>
        <form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

            <div class='x_content'>
                *) isi jumlah dikolom bulan<br/>
                <table id='hrd_isidtabsen' class='table table-striped table-bordered' width='100%'>
                    <thead>
                        <tr>
                            <th class='divnone'>&nbsp;</th>
                            <th width='30px'><?PHP echo $nnmbln; ?></th>
                            <th width='40px' class='divnone'>Posting ID</th>
                            <th width='40px'>Posting Nama</th>
                            <th width='80px' class='divnone'>Kode Id</th>
                            <th width='30px'>Kode Nama (Sub Posting)</th>
                            <th width='30px'>Divisi</th>
                            <th width='30px'>COA</th>
                            <th width='30px'>Nama COA</th>
                            <th width='30px'>Transaksi</th>
                            <th width='30px'>All Dep.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        //$psortby
                        

                        $query = "SELECT * FROM $tmp01 ";
                        if ($psortby=="1"){
                            $query .=" ORDER BY deskripsi, posting_nama, nama_kodeid, divisi";
                        }else{
                            $query .=" ORDER BY posting_nama, nama_kodeid, divisi, deskripsi";
                        }

                        $tampil2=mysqli_query($cnmy, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $pdeskripsi=$row2['deskripsi'];
                            $ppostingid=$row2['postingid'];
                            $ppostingnm=$row2['posting_nama'];
                            $pkodeid=$row2['kodeid'];
                            $pkodenama=$row2['nama_kodeid'];
                            $palldep=$row2['all_dep'];
                            $pdivisi=$row2['divisi'];
                            $pidcoa=$row2['coa4'];
                            $pnmcoa=$row2['nama_coa'];

                            $pjumlah="";
                            $pkodeidbr=$ppostingid."|".$pkodeid;

                            $pfldjumlahrp="<input type='text' size='10px' id='e_txttotalrp[$pkodeidbr]' name='e_txttotalrp[$pkodeidbr]' onblur='HitungTotalJumlahRp()' class='input-sm inputmaskrp2' autocomplete='off' value='$pjumlah' >";

                            $chkbox = "<input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked>";

                            echo "<tr>";
                            echo "<td nowrap class='divnone'>$chkbox</td>";
                            echo "<td nowrap>$pfldjumlahrp</td>";
                            echo "<td nowrap class='divnone'>$ppostingid</td>";
                            echo "<td nowrap>$ppostingnm</td>";
                            echo "<td nowrap class='divnone'>$pkodeid</td>";
                            echo "<td nowrap>$pkodenama</td>";
                            echo "<td nowrap>$pdivisi</td>";
                            echo "<td nowrap>$pidcoa</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap>$pdeskripsi</td>";
                            echo "<td nowrap>$palldep</td>";
                            echo "</tr>";

                        }

                        

                        ?>
                    </tbody>
                </table>

            </div>
        </form>
    </div>
</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>


<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>

<script>
    $(document).ready(function() {
        var dataTable = $('#hrd_isidtabsen').DataTable( {
            //"stateSave": true,
            //"order": [[ 2, "desc" ]],
            "paging": false,
            "searching": false,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                //{ "orderable": false, "targets": 1 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0,1,2,3,4,5,6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 440,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function HitungTotalJumlahRp() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';
        
        var nTotal_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                var anm_jml="e_txttotalrp["+kata+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);

                nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
            }
        }
        document.getElementById('e_totalsemua').value=nTotal_;
    }
    
</script>

<style>
    .divnone {
        display: none;
    }
    #hrd_isidtabsen th {
        font-size: 13px;
    }
    #hrd_isidtabsen td { 
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
    
    mysqli_close($cnmy);
?>