<?php
    session_start();
    include "query_data.php";
    //echo $sql;
?>




<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">

            <div class="title_left">
                <?PHP
                    $noteket = strtoupper($cket);
                    $text="";
                    if ($noteket=="APPROVE") $text="Data Yang Belum DiApprove";
                    if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiApprove";
                    if ($noteket=="REJECT") $text="Data Yang DiReject";
                    if ($noteket=="PENDING") $text="Data Yang DiPending";
                    if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Approve";
                ?>
                
                <div id="n_blk">
                    <?PHP
                    echo "$text";
                    ?>
                </div>
                
                <div class="n_info">
                    <span style='color:red;'><b>klik no divisi/nobr untuk melihat detail pengajuan</b></span>
                </div>
                
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
                    <th width='100px'>No Divisi/NOBR</th>
                    <th width='50px'>Jumlah</th>
                    <th width='30px'>Divisi</th>
                    <th width='50px'>Tgl Pengajuan</th>
                    <th width='50px'>Bulan</th>
                    <th width='30px'>Kode</th>
                    <th width='250px'>Sub</th>
                    <th width='30px'>Checker</th>
                    <th width='30px'>Approved 1</th>
                    <th width='30px'>Approved 2</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idinput'];
                    $tglbuat = $row["tgl"];
                    $pdivisi = $row["divisi"];
                    $pnodivisi = $row["nodivisi"];
                    $pkode = $row["kodeid"];
                    $psubkode = $row["subkode"];
                    $nama = $row["nama"];
                    $subnama = $row["subnama"];
                    $pkaryawanid=$row['karyawanid'];
                    $pjenisrpt=$row["jenis_rpt"];
                    $pketpilih=RTRIM($row["keterangan"]);
                    $pnomorspd=$row["nomor"];
                    
                    
                    $pmystsyginput="";
                    if ($pkaryawanid=="0000000566") {
                        $pmystsyginput=1;
                    }elseif ($pkaryawanid=="0000001043") {
                        $pmystsyginput=2;
                    }else{
                        if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {//anne
                            $pmystsyginput=5;
                        }else{
                            if ($pkode=="1" AND $psubkode=="03") {//ria
                                $pmystsyginput=3;
                            }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
                                $pmystsyginput=4;
                            }elseif ( ($pkode=="2" AND $psubkode=="22") OR ($pkode=="2" AND $psubkode=="23") ) {//marsis
                                $pmystsyginput=6;
                            }elseif ($pkode=="2" AND $psubkode=="39") {//kas kecil cabang
                                $pmystsyginput=9;
                            }
                        }
                    }
					
        
                    $periode = $row["bulan"];
                    if ($pkode=="1" AND $psubkode=="04") {
                        $periode = $row["tglf"];
                    }
                    
                    $pbulan = $row["tglf"];
                    $jumlah = $row["jumlah"];
                    $ptgldir = $row["tgl_dir"];
                    $ptgldir2 = $row["tgl_dir2"];
                    $ptglfin = $row["tgl_proses"];
                    $papv1 = $row["tgl_apv1"];
                    $papv2 = $row["tgl_apv2"];
                    
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
                    
                    $ptglinput = $row["tglinput"];
                    $ptglinput= date("Ym", strtotime($ptglinput));
					
					
                    $pmymodule="";
                    $print=$pnodivisi;
                    if ($pdivisi=="OTC") {
						
                        if ($psubkode=="02" AND (double)$ptglinput>='201910' AND $pnodivisi<>'026/BROTC-GAJI/XI/19') {
                            $pmymodule="module=laporangajispgotc&act=input&idmenu=134&ket=bukan&ispd=$idno";
                        }else{
							if ( ($pkode=="1" AND $psubkode=="03") ) {
								$pmymodule="module=rekapbiayarutinotc&act=input&idmenu=171&ket=bukan&ispd=$idno";
							}elseif ( ($pkode=="2" AND $psubkode=="21") ) {
                                if ($pketpilih=="CA") {
                                    $pmymodule="module=rekapbiayaluarotcca&act=input&idmenu=245&ket=bukan&ispd=$idno";
                                }else{
								    $pmymodule="module=rekapbiayaluarotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
                                }
							}else{
								$pmymodule="module=lapbrotcpermorpt&act=input&idmenu=134&ket=bukan&ispd=$idno";
							}
						}
						
                    }else{
                        if ($pmystsyginput==1) {
                            $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==2) {
                            if ($pjenisrpt=="D" OR $pjenisrpt=="C") {
                                $pmymodule="module=saldosuratdana&act=viewbrklaim&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                            }else{
                                $pmymodule="module=saldosuratdana&act=viewbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                            }
                        }elseif ($pmystsyginput==3) {
                            $pmymodule="module=rekapbiayarutin&act=input&idmenu=190&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==4) {
                            $pmymodule="module=rekapbiayaluar&act=input&idmenu=187&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==5) {
                            $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=204&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==6) {
                            $pmymodule="module=spdkas&act=viewbrho&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
                        }
                    }
                    
                        if ($pmystsyginput==9) {
                            $pmymodule="module=bgtpdkaskecilcabang&act=input&idmenu=350&ket=bukan&ispd=$idno&bln=$tglbuat";
                            $pmymodule2="module=bgtpdkaskecilcabang&act=input&idmenu=350&ket=excel&ispd=$idno&bln=$tglbuat";
                        }
                        
                    if ($pjenisrpt=="D" OR $pjenisrpt=="C") {
                        if ($pkaryawanid=="0000000266" OR $pkaryawanid=="0000000144") {
                            //$pmymodule="module=saldosuratdana&act=viewbrklaim&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                            $pmymodule="module=previewmintadana&act=viewrptklaimdist&idmenu=504ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }
                    }
						
                    //if ($pstsp=="BPJS") {
                    if ($psubkode=="25" AND (double)$ptglinput>='202005' ) {
                        $pmymodule="module=viewrptdatabpjs&act=viewrptdatabpjs&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
                    }
					
                    
                    if (!empty($pmymodule)) {
                        
                        $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?$pmymodule',"
                            . "'Ratting','width=800,height=500,left=400,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "$pnodivisi</a>";
                        
                    }
                    
                    $apvdir="";
                    $apvdir2="";
                    $apvfin="";
                    $napv1="";
                    $napv2="";
                    
                    if (!empty($ptgldir) AND $ptgldir <> "0000-00-00") $apvdir=date("d F Y, h:i:s", strtotime($ptgldir));
                    if (!empty($ptgldir2) AND $ptgldir2 <> "0000-00-00") $apvdir2=date("d F Y, h:i:s", strtotime($ptgldir2));
                    if (!empty($ptglfin) AND $ptglfin <> "0000-00-00") $apvfin=date("d F Y, h:i:s", strtotime($ptglfin));
                    if (!empty($papv1) AND $papv1 <> "0000-00-00") $napv1=date("d F Y, h:i:s", strtotime($papv1));
                    if (!empty($papv2) AND $papv2 <> "0000-00-00") $napv2=date("d F Y, h:i:s", strtotime($papv2));
                    
                    
                    
                    if ($noteket=="REJECT") {
                        $cekbox="";
                    }
                    
                    if ($pses_idcard=="0000001372"){// ibu ira
                        
                    }elseif ($pses_idcard=="0000000367"){// ibu farida
                        if (!empty($apvdir2)) $cekbox="";
                    }
                    
                    if ($noteket=="UNAPPROVE") {
                        if (!empty($pnomorspd)) {
                            $cekbox="";
                        }
                    }
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$cekbox</td>";
                    echo "<td>$print</td>";
                    echo "<td>$jumlah</td>";
                    echo "<td>$pdivisi</td>";
                    echo "<td>$tglbuat</td>";
                    echo "<td>$periode</td>";
                    echo "<td>$nama</td>";
                    echo "<td>$subnama</td>";
                    echo "<td>$napv2</td>";
                    echo "<td>$apvdir</td>";
                    echo "<td>$apvdir2</td>";
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
                include "ttd_appvspddir.php";
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
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6,7,8,9,10,11] }//nowrap

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
            url:"module/dir_apvspd/aksi_apvspddir.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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

<style> 
#n_blkx {
    position: relative;
    -webkit-animation: mymove 5s infinite; /* Safari 4.0 - 8.0 */
    animation: mymove 5s infinite;
    font-size:20px;
    font-weight: bold;
    color: #0000BB;
    margin-left: 20px;
}

/* Safari 4.0 - 8.0 */
@-webkit-keyframes mymove {
  from {left: 0px;}
  to {left: 200px;}
}

@keyframes mymove {
  from {left: 0px;}
  to {left: 200px;}
}

#n_blk{
    animation:blinkingText 4s infinite;
    font-size:20px;
    font-weight: bold;
    color: #0000BB;
    text-align: center;
}
@keyframes blinkingText_hapus{
    0%{		color: #009900;	}
    49%{	color: transparent;	}
    50%{	color: transparent;	}
    /*99%{	color:transparent;	}
    100%{	color: #000;	}*/
}

</style>