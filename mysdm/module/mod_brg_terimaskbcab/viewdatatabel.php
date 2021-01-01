<?php
session_start();


    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli.php";
    
    $mytgl1 = $_POST['ubulan'];
    $pdivisi = $_POST['udivprod'];
    $pcabangid = $_POST['ucabang'];
    
    $pbulan1= date("Ym", strtotime($mytgl1));
    
    
    $fkaryawan=$_SESSION['IDCARD'];
    $pidgroup=$_SESSION['GROUP'];
    
    $filtercabang="";
    if (!empty($pcabangid)) {
        if ($pdivisi=="OT") {
            $filtercabang=" AND a.ICABANGID_O='$pcabangid' ";
        }elseif ($pdivisi=="ET") {
            $filtercabang=" AND a.ICABANGID='$pcabangid' ";
        }
    }else{
        if ($pdivisi=="OT") {
            $filtercabang=" AND a.ICABANGID_O IN ";
        }else{
            $filtercabang=" AND a.ICABANGID IN ";
        }
        $filtercabang .=" (select ifnull(icabangid,'') from hrd.rsm_auth where karyawanid='$fkaryawan') ";
        
    }
    
    if ($pidgroup=="1" OR $pidgroup=="52" OR $pidgroup=="53" OR $pidgroup=="45" OR $pidgroup=="24") {
        if (empty($pcabangid)) {
            $filtercabang="";
        }
    }
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPGMCTSKB01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPGMCTSKB02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPGMCTSKB03_".$userid."_$now ";
    
    $query = "select 
        b.PILIHAN,a.IDKELUAR,a.TGLINPUT,a.TANGGAL, a.KARYAWANID,e.nama NAMA_KARYAWAN,a.DIVISIID,
        b.DIVISINM,a.ICABANGID,c.nama NAMA_CABANGETH,a.ICABANGID_O,d.nama NAMA_CABANGOTC,
        a.NOTES,a.USERID,a.STSNONAKTIF,a.SYS_NOW,a.PM_APV,a.PM_TGL,f.PRINT,
        f.NORESI,f.TGLKIRIM,f.TGLTERIMA, f.NAMA_KARYAWAN NAMA_KARYAWANTERIMA, a.AREAID, g.nama as NAMAAREAETH, a.AREAID_O, h.nama as NAMAAREAOTC  
        from dbmaster.t_barang_keluar a JOIN dbmaster.t_divisi_gimick b on a.DIVISIID=b.DIVISIID LEFT JOIN 
        mkt.icabang c on a.ICABANGID=c.iCabangId
        LEFT JOIN mkt.icabang_o d on a.ICABANGID_O=d.icabangid_o 
        LEFT JOIN hrd.karyawan e on a.KARYAWANID=e.karyawanId 
        LEFT JOIN dbmaster.t_barang_keluar_kirim f on a.IDKELUAR=f.IDKELUAR 
        LEFT JOIN MKT.iarea as g on a.AREAID=g.areaid AND a.ICABANGID=g.icabangid 
        LEFT JOIN MKT.iarea_o as h on a.AREAID_O=h.areaid_o AND a.ICABANGID_O=h.icabangid_o 
        WHERE IFNULL(a.STSNONAKTIF,'')<>'Y' 
        AND DATE_FORMAT(a.TANGGAL,'%Y%m')='$pbulan1' $filtercabang ";
    //$query .=" AND ( IFNULL(f.TGLKIRIM,'')<>'' AND IFNULL(f.TGLKIRIM,'0000-00-00')<>'0000-00-00' AND IFNULL(f.TGLKIRIM,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
    $query .= " AND b.PILIHAN='$pdivisi' ";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.IDKELUAR, b.IDKATEGORI, c.NAMA_KATEGORI, a.IDBARANG, b.NAMABARANG, a.STOCK, a.JUMLAH from dbmaster.t_barang_keluar_d a 
        JOIN dbmaster.t_barang b on a.IDBARANG=b.IDBARANG LEFT JOIN dbmaster.t_barang_kategori c on b.IDKATEGORI=c.IDKATEGORI WHERE 
        a.IDKELUAR IN (select IFNULL(IDKELUAR,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        
        <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='7px'></th>
                    <th width='20px'>ID</th>
                    <th width='50px'>Tanggal</th>
                    <th width='50px'>Grp. Produk</th>
                    <th width='50px'>Cabang</th>
					<th width='50px'>Area</th>
                    <th width='20px'>No. Resi</th>
                    <th width='20px'>Tgl. Terima</th>
                    <th width='20px'>Penerima</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by IDKELUAR";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidkeluar=$row1['IDKELUAR'];
                    $ptgl=$row1['TANGGAL'];
                    $ptglkirim=$row1['TGLKIRIM'];
                    $ppmtgl=$row1['PM_TGL'];
                    $pnoresi=$row1['NORESI'];
                    $ptgltrima=$row1['TGLTERIMA'];
                    $pkryterima=$row1['NAMA_KARYAWANTERIMA'];
                    $ppilihanid=$row1['PILIHAN'];
                    $pdivisinm=$row1['DIVISINM'];
                    $pnmkaryawan=$row1['NAMA_KARYAWAN'];
                    $pnmcabang=$row1['NAMA_CABANGETH'];
					$pnmarea=$row1['NAMAAREAETH'];
                    if ($ppilihanid=="OT") {
						$pnmcabang=$row1['NAMA_CABANGOTC'];
						$pnmarea=$row1['NAMAAREAOTC'];
					}
                    
                    $ptgl= date("d/m/Y", strtotime($ptgl));
                    
                    if ($ptglkirim=="0000-00-00" OR $ptglkirim=="0000-00-00 00:00:00") $ptglkirim="";
                    if ($ppmtgl=="0000-00-00" OR $ppmtgl=="0000-00-00 00:00:00") $ppmtgl="";
                    if ($ptgltrima=="0000-00-00" OR $ptgltrima=="0000-00-00 00:00:00") $ptgltrima="";
                    
                    if (!empty($ptgltrima)) $ptgltrima= date("d/m/Y", strtotime($ptgltrima));
                    
                    $print="<a title='Detail Barang / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=gimickeluarbarang&nid=$pidkeluar&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pidkeluar</a>";
                    
                    $pbtnwarnaresi="btn btn-warning btn-xs";
                    if (!empty($ptgltrima)) $pbtnwarnaresi="btn btn-info btn-xs";
                    $pbtnisiterima = "<a class='$pbtnwarnaresi' href='?module=$pmodule&act=isiterima&idmenu=$pidmenu&nmun=$pidmenu&id=$pidkeluar'>Isi Terima</a>";
                    
                    if (empty($ptglkirim)) $pbtnisiterima="belum isi tgl. kirim";
                    if (empty($ppmtgl)) $pbtnisiterima="belum approve pm";
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pbtnisiterima</td>";
                    echo "<td nowrap>$print</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap>$pdivisinm</td>";
                    echo "<td nowrap>$pnmcabang</td>";
					echo "<td nowrap>$pnmarea</td>";
                    echo "<td nowrap>$pnoresi</td>";
                    echo "<td nowrap>$ptgltrima</td>";
                    echo "<td nowrap>$pkryterima</td>";
                    echo "</tr>";
                    
                    /*
                    //detail
                    echo "<tr>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td colspan='7'>";
                    
                        echo "<table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>";
                        echo "<tr>";
                        echo "<td><b>KATEGORI</b></td>";
                        echo "<td><b>NAMA BARANG</b></td>";
                        echo "<td><b>JUMLAH</b></td>";
                        echo "</tr>";
                        
                        echo "<tbody>";
                            $query = "select * from $tmp02 WHERE IDKELUAR='$pidkeluar' order by NAMA_KATEGORI, NAMABARANG";
                            $tampil2= mysqli_query($cnmy, $query);
                            while ($row2= mysqli_fetch_array($tampil2)) {
                                
                                $pnmkategori=$row2['NAMA_KATEGORI'];
                                $pnmbarang=$row2['NAMABARANG'];
                                $pjml=$row2['JUMLAH'];
                                
                                echo "<tr>";
                                echo "<td nowrap>$pnmkategori</td>";
                                echo "<td nowrap>$pnmbarang</td>";
                                echo "<td nowrap align='right'>$pjml</td>";
                                echo "</tr>";
                            }
                            
                        echo "</tbody>";
                        echo "</table>";
                    */
                    
                    echo "</td>";
                    echo "</tr>";
                    
                    
                    $no++;
                }
                ?>
            </tbody>
                
        </table>
        
    </div>
    
    
</form>


<style>
    .divnone {
        display: none;
    }
    #datatablestockopn th {
        font-size: 13px;
    }
    #datatablestockopn td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index:1;
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>