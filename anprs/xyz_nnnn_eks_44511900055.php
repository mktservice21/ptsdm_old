<?PHP
session_start();
$_SESSION['USERID']="";
$_SESSION['USERNAME']="";
$_SESSION['IDCARD']="";
$_SESSION['NAMALENGKAP']="";
$_SESSION['PASSWORD']="";
$_SESSION['KRYNONE']="0000002083";

include("../mysdm/config/koneksimysqli.php");
$ppilihrpt="";
$iidgrp=$_GET['xyz'];
$iidcard=$_GET['nomxyz'];


?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>pilih data</title>
    
    <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="../mysdm/images/icon.ico" />

    <?php header("Cache-Control: no-cache, must-revalidate"); ?>

	
    <link href="../mysdm/css/konten.css" rel="stylesheet">
	
    <!-- Bootstrap -->
    <link href="../mysdm/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="../mysdm/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../mysdm/vendors/nprogress/nprogress.css" rel="stylesheet">
	
	
    <!-- Datatables -->
    <link href="../mysdm/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- Datatables -->
    <link href="../mysdm/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- Datatables -->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <!-- jQuery -->
    <script src="../mysdm/vendors/jquery/dist/jquery.min.js"></script>
	
	
    <style>
        div.t_judul {
          padding: 10px;
        }

        @media screen and (min-width: 600px) {
          div.t_judul {
            font-size: 30px;
          }
        }

        @media screen and (max-width: 600px) {
          div.t_judul {
            font-size: 20px;
          }
        }
    </style>
</head>

<body>


<div class="t_judul">Approve Data</div>

    <p>*)klik No BR/Divisi untuk melihat rincian pengajuan...</p>
    
    <form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
        <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $iidcard; ?>' Readonly>
        <input type='hidden' class='form-control' id='e_idgroup_lnk' name='e_idgroup_lnk' value='<?PHP echo $iidgrp; ?>' Readonly>
        <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th align="center" width="5">No</th>
                    <th align="center" width="5"></th>
                    <th align="center">No BR/Divisi</th>
                    <th align="center">Jumlah</th>
                    <th align="center">Pengajuan</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, a.idinput, a.nodivisi, FORMAT(a.jumlah,0,'de_DE') as jumlah, a.divisi, a.tgl_dir, a.tgl_dir2, "
                        . " b.stsapvdir, a.karyawanid, a.kodeid, a.subkode, a.jenis_rpt, a.tglinput, a.tgl_apv3 from dbmaster.t_suratdana_br a "
                        . " JOIN dbmaster.t_suratdana_br_link b on a.idinput=b.idinput WHERE "
                        . " b.idgroup='$iidgrp' AND b.userid='$iidcard' AND IFNULL(a.stsnonaktif,'')<>'Y' AND "
                        . " IFNULL(b.hapus,'')<>'Y'";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
						$tglbuat = $row["tgl"];
						$pjenisrpt=$row["jenis_rpt"];
						
                        $idno=$row['idinput'];
                        $pnodivisi=$row['nodivisi'];
                        $pjumlah=$row['jumlah'];
                        $pstsapvdir=$row['stsapvdir'];
                        
                        $pkaryawanid=$row['karyawanid'];
                        $pkode = $row["kodeid"];
                        $psubkode = $row["subkode"];
                        
                        $pdivisi=$row['divisi'];
                        $npdivisi=$row['divisi'];
                        if (empty($pdivisi)) $npdivisi="ETHICAL";

                        $ptgldir = $row["tgl_dir"];
                        $ptgldir2 = $row["tgl_dir2"];

                        $apvdir="";
                        $apvdir2="";

                        if (!empty($ptgldir) AND $ptgldir <> "0000-00-00") $apvdir=date("d F Y, h:i:s", strtotime($ptgldir));
                        if (!empty($ptgldir2) AND $ptgldir2 <> "0000-00-00") $apvdir2=date("d F Y, h:i:s", strtotime($ptgldir2));

						$ptglinput = $row["tglinput"];
						$ptglinput= date("Ym", strtotime($ptglinput));
					
                        $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[] checked>";

                        if ($pstsapvdir=="Y") {
                            if (!empty($apvdir) AND empty($apvdir2)) {
                                $cekbox="<a href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                                        . "onClick=\"ProsesDataUnApproveFIN('unapprove', '$idno')\"> "
                                        . "unapprove</a>";
                            }
                        }

                        
                        
                        
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
                        
                        $pmymodule="";
                        $print=$pnodivisi;
                        if ($pdivisi=="OTC") {
							if ($psubkode=="02" AND (double)$ptglinput>='201910' AND $pjenisrpt <>'G') {//$pnodivisi<>'026/BROTC-GAJI/XI/19'
								$pmymodule="module=laporangajispgotc&act=input&idmenu=134&ket=bukan&ispd=$idno";
							}else{
								if ( ($pkode=="1" AND $psubkode=="03") ) {
									$pmymodule="module=rekapbiayarutinotc&act=input&idmenu=171&ket=bukan&ispd=$idno";
								}elseif ( ($pkode=="2" AND $psubkode=="21") ) {
									$pmymodule="module=rekapbiayaluarotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
								}elseif ( ($pkode=="2" AND $psubkode=="36") ) {
									$pmymodule="module=rekapbiayarutincaotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
								}else{
									$pmymodule="module=lapbrotcpermo&act=input&idmenu=134&ket=bukan&ispd=$idno";
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
							}elseif ($pmystsyginput==9) {
								$pmymodule="module=bgtpdkaskecilcabang&act=input&idmenu=350&ket=bukan&ispd=$idno&bln=$tglbuat";
                            }
                        }

						//if ($pstsp=="BPJS") {
						if ($psubkode=="25" AND (double)$ptglinput>='202005' ) {
							$pmymodule="module=viewrptdatabpjs&act=viewrptdatabpjs&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
						}
					
                        if (!empty($pmymodule)) {

                            $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                                . "onClick=\"window.open('../mysdm/eksekusi3.php?$pmymodule',"
                                . "'Ratting','width=800,height=500,left=400,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                . "$pnodivisi</a>";

                        }
                    
                    
						if ($pdivisi=="OTC") {
							if ($psubkode=="02" AND (double)$ptglinput>='201910' AND $pjenisrpt <>'G') {
								$ptglapv3_ = $row["tgl_apv3"];
								if (empty($ptglapv3_) AND $ptglapv3_ <> "0000-00-00") $cekbox="";
							}
						}
					
					
                        echo "<tr>";
                        echo "<td >$no</td>";
                        echo "<td nowrap>$cekbox</td>";
                        echo "<td nowrap>$print</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "<td nowrap>$npdivisi</td>";
                        echo "</tr>";

                        $no++;
                    }
                }

            ?>
            </tbody>
        </table>
		<div style="margin-left:20px; margin-right:10px;">
        <?PHP
            include "xyz_pilih_ntdn.php";
        ?>
		</div>
    </form>

	
	
	
    <!-- Bootstrap -->
    <script src="../mysdm/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Datatables -->
    <script src="../mysdm/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../mysdm/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../mysdm/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../mysdm/vendors/jszip/dist/jszip.min.js"></script>
    <script src="../mysdm/vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../mysdm/vendors/pdfmake/build/vfs_fonts.js"></script>

    <script>
        function ProsesDataUnApproveFIN(ket, cekbr){
            //alert(ket+", "+cekbr);
            var cmt = confirm('Apakah akan melakukan proses unapprove ...?');
            if (cmt == false) {
                    return false;
            }
            var allnobr = "";

            if (cekbr=="") {
                    alert("Tidak ada data yang diproses...!!!");
                    return false;
            }

            allnobr="('"+cekbr+"')";

            var txt;
            var ekaryawan=document.getElementById('e_idkaryawan').value;
            var eidgrplnk=document.getElementById('e_idgroup_lnk').value;

            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");

            $.ajax({
                    type:"post",
                    url:"aksi_ansxyz.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
                    data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt+"&uidgrplnk="+eidgrplnk,
                    success:function(data){
                            location.reload();
                            alert(data);
                    }
            });


        }
    </script>
	
</body>
</html>



<?PHP
mysqli_close($cnmy);
?>