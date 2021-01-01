<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $kodeinput = " AND kode=3 ";//untuk ca
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    
    $_SESSION['DIRCAAPVKET'] = $cket;
    $_SESSION['DIRCAAPVTGL1'] = $mytgl1;
    $_SESSION['DIRCAAPVTGL2'] = $mytgl2;
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
    
    $sql = "SELECT idca, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, DATE_FORMAT(periode,'%M %Y') as bulan, DATE_FORMAT(tgl,'%d/%m/%Y') as periode, 
            divisi, karyawanid, nama, areaid, '' as nama_area, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan 
            , ifnull(tgl_dir,'0000-00-00') tgl_dir,
            ifnull(tgl_fin,'0000-00-00') tgl_fin, jabatanid ";
    $sql.=" FROM dbmaster.v_ca0_mydata ";
    $sql.=" WHERE 1=1 ";
    $sql.=" AND ( (Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2') OR (Date_format(periode, '%Y-%m') between '$tgl1' and '$tgl2') ) ";
    $sql.=" AND jabatanid IN ('04', '05', '06') ";// filter jabatan
    $sql.=" AND divisi <> 'OTC' ";// filter jabatan
    
    if (strtoupper($cket)!= "REJECT") $sql.=" AND IFNULL(stsnonaktif,'') <> 'Y' ";
    
    if (strtoupper($cket)=="APPROVE") {
        $sql.=" AND IFNULL(tgl_dir,'')='' ";
        $sql .= " AND ifnull(tgl_fin,'')='' ";
    }elseif (strtoupper($cket)=="UNAPPROVE") {
        $sql.=" AND IFNULL(tgl_dir,'')<>'' ";
    }elseif (strtoupper($cket)=="REJECT") {
        $sql.=" AND IFNULL(stsnonaktif,'') = 'Y' ";
    }elseif (strtoupper($cket)=="PENDING") {
        
    }
    
    if (strtoupper($cket)== "SUDAHFIN") $sql .= " AND ifnull(tgl_fin,'')<>'' "; //sudah fin
    
?>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    <?PHP
                        $noteket = strtoupper($cket);
                        $text="";
                        if ($noteket=="APPROVE") $text="Data Yang Belum DiApprove";
                        if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiApprove";
                        if ($noteket=="REJECT") $text="Data Yang DiReject";
                        if ($noteket=="PENDING") $text="Data Yang DiPending";
                        if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Approve";
                        
                        echo "<b>$text"
                                . "<p/>&nbsp;*) <span style='color:red;'>klik nama untuk melihat detail pengajuan</span></b>";
                    ?>
                </h4>
            </div>
        <div class="clearfix"></div>
        
        <table id='dtablecadir' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
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
                    <th width='30px'>Approved</th>
                    <th width='30px'>Proses Finance</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $sql.=" order by idca";
                //echo $sql;
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idca'];
                    $tglbuat = $row["tgl"];
                    $nama = $row["nama"];
                    $nmarea = $row["nama_area"];
                    $bulan = date("F Y", strtotime($row["bulan"]));
                    $periode = $row["periode"];
                    $pbulan = $row["bulan"];
                    $jumlah = $row["jumlah"];
                    $keterangan = $row["keterangan"];
                    $ptgldir = $row["tgl_dir"];
                    $ptglfin = $row["tgl_fin"];
                    
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
                    
                    $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrcash&brid=$idno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$nama</a>";
                    
                    $pbukti = getfieldcnmy("SELECT idca as lcfields from dbimages.img_ca0 where idca='$idno'");
                    $bukti="";
                    if (!empty($pbukti)) {
                        $bukti="<a title='lihat bukti' href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=entrybrcash&brid=$idno&iprint=bukti',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Lihat</a>";
                    }
                    
                    
                    $apvdir="";
                    $apvfin="";
                    
                    if ($ptgldir <> "0000-00-00") $apvdir=date("d F Y, h:i:s", strtotime($ptgldir));
                    if ($ptglfin <> "0000-00-00") $apvfin=date("d F Y, h:i:s", strtotime($ptglfin));
                    
                    if (strtoupper($cket)=="UNAPPROVE") {
                        if (!empty($apvfin)) {
                            $cekbox="";
                        }
                    }
                    
                    if ($noteket=="REJECT") {
                        $cekbox="";
                    }
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$cekbox</td>";
                    echo "<td>$print</td>";
                    echo "<td>$jumlah</td>";
                    echo "<td>$tglbuat</td>";
                    echo "<td>$pbulan</td>";
                    echo "<td>$bukti</td>";
                    echo "<td>$keterangan</td>";
                    echo "<td>$idno</td>";
                    echo "<td>$apvdir</td>";
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
            <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
               onClick="ProsesData('unapprove', 'chkbox_br[]')">
        </div>
    <?PHP
    }
    ?>
    <div class='clearfix'></div>
    <!-- tanda tangan -->
    <?PHP
        if (strtoupper($cket)=="APPROVE") {
            echo "<div class='col-sm-5'>";
                include "ttd_appvcadir.php";
            echo "</div>";
        }
    ?>
</form>


<script>
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#dtablecadir').DataTable( {
            //"stateSave": true,
            //"order": [[ 7, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6] }//nowrap

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
            url:"module/dir_apvca/aksi_apvcadir.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=approve"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                if (ket=="reject") ket="approve";
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
    #dtablecadir th {
        font-size: 13px;
    }
    #dtablecadir td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

