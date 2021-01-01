<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable1').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [2,3,4,5,6,7] }//,//right
            //{ className: "text-nowrap", "targets": [0] }//nowrap

        ],
        dom: 'Bfrtip',
        buttons: [
            'excel', 'print'
        ],
        bFilter: false, bInfo: false, "bLengthChange": false,
		"bPaginate": false
    } );
} );

</script>

<style>
    .divnone {
        display: none;
    }
    
</style>

<?php
    
    $filtericabareaid="";
    $filtericabareaid2="";
    $filtericabareaid_p="";
    $pprodoth="";
    if ($pilihdarims==true) {
        $tgl01=$_POST["bulan"];
        $bulan= date("Y-m-d", strtotime($tgl01));
        $idspv=$_POST["cb_karyawanspv"];
        $karyawanid=$_SESSION["IDCARD"];
        $link="";
        
        require_once 'module/a_new/meekrodb.2.3.class.php';
        include "config/fungsi_combo.php";
        
        $filtericabareaid_p=('');
        if (isset($_POST['chkbox_icabarea'])) {
            if (!empty($_POST['chkbox_icabarea'])){
                $filtericabareaid_p=$_POST['chkbox_icabarea'];
                $filtericabareaid_p=PilCekBox($filtericabareaid_p);
            }
        }
        $filtericabareaid=" and CONCAT(icabangid, areaid) in $filtericabareaid_p ";
        $filtericabareaid2=" and CONCAT(icabangid, areaid) NOT in $filtericabareaid_p ";
        
        if (isset($_POST['chkboth'])) $pprodoth=$_POST['chkboth'];
        
    }else {
        $bulan=$_GET["bulan"];
        $idspv=$_GET["spv"];
        $karyawanid=$_GET["id"];
        $link =$_GET["link"]."/report/slsspv.aspx";
        
        require_once 'meekrodb.2.3.class.php';
    }
    
    $date=date_create($bulan);
    $tanggal=date_format($date,"Y-m-d");

    $namaspv = DB::queryFirstField("SELECT nama FROM ms.karyawan WHERE karyawanId=%s", $idspv);
    
    $thnbln=date_format($date,"F Y");
    $thnbln2=date('F Y', strtotime('-1 year', strtotime($tanggal)));
    $printdate= date("d/m/Y");

    if (!empty($link)) {
        echo "<h4><b><a href='$link'>back</a></b></h4>";
    }
    
    
    $filbln=date_format($date,"Ymd");

    $thnbln=date_format($date,"F Y");
    $printdate= date("d/m/Y");

    

    $thnbln=date_format($date,"F Y");
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>PT. Surya Dermato Medica</small></td></tr>";
                echo "<tr><td><b>SALES PER SUPERVISOR</b></td></tr>";
                echo "<tr><td>Nama SPV: $namaspv</td></tr>";
                echo "<tr><td>Periode: $thnbln</td></tr>";
                if ($pprodoth=="Y") {
                    echo "<tr><td>Include Produk Other Peacock</td></tr>";
                }else{
                    echo "<tr><td>Tanpa Produk Other Peacock</td></tr>";
                }
                echo "</table>";
            echo "</td>";
            echo "<td>";
                echo "<table align='left' border='0' width='100%'>";
                echo "<tr><td align='left'><h3></h3></td></tr>";
                echo "<tr><td align='center'><b></b></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "<tr><td>&nbsp;</td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table><hr>&nbsp;</hr>";
    
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp1 ="DTSALESSPVXX01_".$idspv."_$now$milliseconds";
    $tmp2 ="DTSALESSPVXX02_".$idspv."_$now$milliseconds";
    $tmp3 ="DTSALESSPVXX03_".$idspv."_$now$milliseconds";
    $tmp4 ="DTSALESSPVXX04_".$idspv."_$now$milliseconds";
    
    $tmp5 ="DTSALESSPVXX05_".$idspv."_$now$milliseconds";
    $tmp6 ="DTSALESSPVXX06_".$idspv."_$now$milliseconds";
    $tmp7 ="DTSALESSPVXX07_".$idspv."_$now$milliseconds";
    
    DB::useDB("dbtemp");
    if ($pprodoth=="Y") {
    }else{
        $query = "select iprodid from sls.othproduk WHERE divprodid='PEACO'";
        $results1 = DB::query("create TEMPORARY table $tmp7($query)");
    }
    
    
    $filbln=date_format($date,"Ymd");
    $hari_ini =date_format($date,"Y-m-d");
    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
    $tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
    
    $filblnprod=date_format($date,"Ym");
    
    
    $tgl_pertama_ytd = date('Y-01-01', strtotime($hari_ini));
    $tgl_terakhir_ytd = date('Y-m-t', strtotime($hari_ini));
    $query = "SELECT * FROM sls.sales where bulan between '$tgl_pertama_ytd' AND '$tgl_terakhir_ytd'";
    if (!empty($filtericabareaid)) $query .= $filtericabareaid." ";
    if ($pprodoth=="Y") {
    }else{
        //$query .= " AND iprodid NOT IN (select IFNULL(iprodid,'') iprodid from $tmp7)";
		$query .= " AND IFNULL(kategoriproduk,'')<>'OTHER'";
    }
    $results1 = DB::query("create TEMPORARY table $tmp5($query)");
    
	/*
	//lama
    $query = "select t0.tgl, t0.icabangid, t0.areaId, t0.divprodid, t1.iprodid, t1.target, t1.hna from ms.target1_new as t1 join ms.target0_new as t0 
            on t1.targetId=t0.targetId
            where t0.tgl between '$tgl_pertama_ytd' AND '$tgl_terakhir_ytd'";
			
	//baru
	$query = "SELECT bulan as tgl, icabangid, areaId, divprodid, iprodid, qty as target, hna  
		FROM tgt.targetarea WHERE bulan between '$tgl_pertama_ytd' AND '$tgl_terakhir_ytd'";
	
	*/
	
	//new	
	$query = "SELECT bulan as tgl, icabangid, areaId, divprodid, iprodid, qty_target as target, hna_target as hna 
		FROM $tmp5";
	
    DB::useDB("dbtemp");
    $results1 = DB::query("create TEMPORARY table $tmp6($query)");
    
    
    $query="select s.bulan, s.icabangid, s.areaid, a.nama as nama_area, s.divprodid, s.iprodid, sum(s.qty_sales) as qty, sum(s.value_sales) as tvalue from $tmp5 as s
        join sls.iarea as a on s.areaid=a.areaid and s.icabangid=a.icabangid
        where CONCAT(s.icabangid,s.areaid,s.divprodid) in (select distinct CONCAT(sp.icabangid,sp.areaid,sp.divisiid) from sls.ispv0 as sp  
        where sp.karyawanid='$idspv')
        group by s.bulan, s.icabangid, s.areaid, a.nama, s.divprodid, s.iprodid";
    DB::useDB("dbtemp");//a.aktif='Y' and 
    $results1 = DB::query("create TEMPORARY table $tmp1($query)");
    

    
    $query = "select t1.tgl, t1.icabangid, t1.areaId, t1.divprodid, t1.iprodid, sum(t1.target) as qty, sum(t1.hna*t1.target) as tvalue from $tmp6 as t1  
            where CONCAT(t1.icabangid,t1.areaId,t1.divProdId) in (select distinct CONCAT(sp.icabangid,sp.areaid,sp.divisiid) from sls.ispv0 as sp 
            join sls.iarea as a on sp.areaid=a.areaid where sp.karyawanid='$idspv'    )
            GROUP BY t1.tgl, t1.icabangid, t1.areaId, t1.divprodid, t1.iprodid";//and a.aktif='Y'
    
    $results1 = DB::query("create TEMPORARY table $tmp2($query)");
    
    if (!empty($filtericabareaid)) {
        $results1 = DB::query("DELETE FROM $tmp2 WHERE 1=1 $filtericabareaid2");
    }
        
            
    $query = "select distinct a.icabangid, a.areaid, a.nama_area, p.divprodid, p.iprodid, p.nama, CAST(0 as DECIMAL(20,2)) as SLS, CAST(0 as DECIMAL(20,2)) as TRG, 
            CAST(0 as DECIMAL(20,2)) as ACH, CAST(0 as DECIMAL(20,2)) as sqty, CAST(0 as DECIMAL(20,2)) as tqty,
            CAST(0 as DECIMAL(20,2)) as ytd_sls, CAST(0 as DECIMAL(20,2)) as ytd_trg,
            CAST(0 as DECIMAL(20,2)) as ytd_ach, CAST(0 as DECIMAL(20,2)) as ytd_sqty, CAST(0 as DECIMAL(20,2)) as ytd_tqty
            from $tmp1 as a, sls.iproduk as p
            where p.divprodid in (select distinct divisiid 
            from sls.ispv0 where karyawanid='$idspv')";
    $results1 = DB::query("create TEMPORARY table $tmp4($query)");
    
    $query = "insert into $tmp4 (icabangid, areaid, nama_area, divprodid, iprodid, nama)
            select distinct a.icabangid, a.areaid, i.nama, p.divprodid, p.iprodid, p.nama from $tmp2 as a Join sls.iproduk as p on p.iprodid=a.iprodid 
            join sls.iarea as i on a.areaid=i.areaid and a.icabangid=i.icabangid
        where p.divprodid in (select distinct divisiid from sls.ispv0 where karyawanid='$idspv')
                    And CONCAT(a.icabangid,a.areaid, p.divprodid, p.iprodid) not in (select distinct CONCAT(icabangid,areaid, divprodid, iprodid) from $tmp1)";
    $results1 = DB::query($query);
    
    
    $query = "select distinct icabangid, areaid, nama_area, divprodid, iprodid, nama, SLS, TRG, ACH, sqty, tqty, ytd_sls, ytd_trg, ytd_ach, ytd_sqty, ytd_tqty from $tmp4";
	$results1 = DB::query("create TEMPORARY table $tmp3($query)");
        
    //bulan between '$tgl_pertama' AND '$tgl_terakhir'
    //tgl between '$tgl_pertama' AND '$tgl_terakhir' 
        
    $query="UPDATE $tmp3 set SLS=ifnull((select sum(tvalue) from $tmp1 where $tmp1.bulan between '$tgl_pertama' AND '$tgl_terakhir' AND $tmp3.divprodid=$tmp1.divprodid and $tmp3.iprodid=$tmp1.iprodid and $tmp3.icabangid=$tmp1.icabangid and $tmp3.areaid=$tmp1.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set sqty=ifnull((select sum(qty) from $tmp1 where $tmp1.bulan between '$tgl_pertama' AND '$tgl_terakhir' AND $tmp3.divprodid=$tmp1.divprodid and $tmp3.iprodid=$tmp1.iprodid and $tmp3.icabangid=$tmp1.icabangid and $tmp3.areaid=$tmp1.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set TRG=ifnull((select sum(tvalue) from $tmp2 where $tmp2.tgl between '$tgl_pertama' AND '$tgl_terakhir' AND $tmp3.divprodid=$tmp2.divprodid and $tmp3.iprodid=$tmp2.iprodid  and $tmp3.icabangid=$tmp2.icabangid and $tmp3.areaid=$tmp2.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set tqty=ifnull((select sum(qty) from $tmp2 where $tmp2.tgl between '$tgl_pertama' AND '$tgl_terakhir' AND $tmp3.divprodid=$tmp2.divprodid and $tmp3.iprodid=$tmp2.iprodid  and $tmp3.icabangid=$tmp2.icabangid and $tmp3.areaid=$tmp2.areaid),0)";
    $results1 = DB::query($query);
    
    //$query="UPDATE $tmp3 set ACH=ifnull(case when ifnull(TRG,0)=0 then 0 else (SLS/TRG*100) end,0)";//berdasarkan value
	$query="UPDATE $tmp3 set ACH=ifnull(case when ifnull(tqty,0)=0 then 0 else (sqty/tqty*100) end,0)";//berdasarkan qty
    $results1 = DB::query($query);
    
    
    //YTD
    $query="UPDATE $tmp3 set ytd_sls=ifnull((select sum(tvalue) from $tmp1 where $tmp3.divprodid=$tmp1.divprodid and $tmp3.iprodid=$tmp1.iprodid and $tmp3.icabangid=$tmp1.icabangid and $tmp3.areaid=$tmp1.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set ytd_sqty=ifnull((select sum(qty) from $tmp1 where $tmp3.divprodid=$tmp1.divprodid and $tmp3.iprodid=$tmp1.iprodid and $tmp3.icabangid=$tmp1.icabangid and $tmp3.areaid=$tmp1.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set ytd_trg=ifnull((select sum(tvalue) from $tmp2 where $tmp3.divprodid=$tmp2.divprodid and $tmp3.iprodid=$tmp2.iprodid  and $tmp3.icabangid=$tmp2.icabangid and $tmp3.areaid=$tmp2.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set ytd_tqty=ifnull((select sum(qty) from $tmp2 where $tmp3.divprodid=$tmp2.divprodid and $tmp3.iprodid=$tmp2.iprodid  and $tmp3.icabangid=$tmp2.icabangid and $tmp3.areaid=$tmp2.areaid),0)";
    $results1 = DB::query($query);
    
    //$query="UPDATE $tmp3 set ytd_ach=ifnull(case when ifnull(ytd_trg,0)=0 then 0 else (ytd_sls/ytd_trg*100) end,0)";//berdasarkan value
	$query="UPDATE $tmp3 set ytd_ach=ifnull(case when ifnull(ytd_tqty,0)=0 then 0 else (ytd_sqty/ytd_tqty*100) end,0)";//berdasarkan qty
    $results1 = DB::query($query);
    
    
    $results1 = DB::query("drop TEMPORARY table if exists $tmp1");
    $results1 = DB::query("drop TEMPORARY table if exists $tmp2");
    $results1 = DB::query("drop TEMPORARY table if exists $tmp4");
?>


<table id="datatable1" class="display  table table-striped table-bordered" style="width:100%" border='0px'>
    <thead>
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th width="100px">Sales</th>
            <th width="100px">Target</th>
            <th>Ach %</th>
            <th width="100px">Ytd. Sales</th>
            <th width="100px">Ytd. Target</th>
            <th>Ytd. Ach %</th>
        </tr>
    </thead>
    <tbody>
        <?PHP
        $results1 = DB::query("SELECT distinct icabangid, areaid, nama_area, divprodid FROM $tmp3 order by nama_area, areaid, divprodid");
        foreach ($results1 as $r1) {
            $idcabang=$r1['icabangid'];
            $idarea=$r1['areaid'];
            $area=$r1['nama_area'];
            $divisi=$r1['divprodid'];
            echo "<tr style='background-color:#f2efef;'>";
            echo "<td></td>";
            echo "<td><b>$area ($divisi)</b></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
                
            $no=1;
            $results2 = DB::query("SELECT * FROM $tmp3 where icabangid='$idcabang' and areaid='$idarea' and divprodid='$divisi' order by nama, iprodid");
            foreach ($results2 as $r2) {
                $produk=$r2['nama'];
                
                $sales=0; $target=0; $ach=0;
                if (!empty($r2['SLS'])) $sales=number_format($r2['SLS'],0,",",",");
                if (!empty($r2['TRG'])) $target=number_format($r2['TRG'],0,",",",");
                if (!empty($r2['ACH'])) $ach=$r2['ACH'];
                
                $sqty=0; $tqty=0;
                if (!empty($r2['sqty'])) $sqty=number_format($r2['sqty'],0,",",",");
                if (!empty($r2['tqty'])) $tqty=number_format($r2['tqty'],0,",",",");
                
                //YTD
                $ytd_sales=0; $ytd_target=0; $ytd_ach=0;
                if (!empty($r2['ytd_sls'])) $ytd_sales=number_format($r2['ytd_sls'],0,",",",");
                if (!empty($r2['ytd_trg'])) $ytd_target=number_format($r2['ytd_trg'],0,",",",");
                if (!empty($r2['ytd_ach'])) $ytd_ach=$r2['ytd_ach'];

                $ytd_sqty=0; $ytd_tqty=0;
                if (!empty($r2['ytd_sqty'])) $ytd_sqty=number_format($r2['ytd_sqty'],0,",",",");
                if (!empty($r2['ytd_tqty'])) $ytd_tqty=number_format($r2['ytd_tqty'],0,",",",");

                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>$produk</td>";
                echo "<td align='right'>$sqty</td>";
                echo "<td align='right'>$tqty</td>";
                echo "<td align='right'>$ach</td>";
                //YTD
                echo "<td align='right'>$ytd_sqty</td>";
                echo "<td align='right'>$ytd_tqty</td>";
                echo "<td align='right'>$ytd_ach</td>";
                
                echo "</tr>";

                $no++;
            }
            
            //sub total
            $resultssub1 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG, sum(SLS*TRG/100) as ACH, "
                    . " sum(ytd_sls) as ytd_sls, sum(ytd_trg) as ytd_trg, sum(ytd_sls*ytd_trg/100) as ytd_ach "
                    . " FROM $tmp3 where icabangid='$idcabang' and areaid='$idarea' and divprodid='$divisi'");
            foreach ($resultssub1 as $s1) {
                $sales=0; $target=0; $ach=0;
                if (!empty($s1['SLS'])) $sales=number_format($s1['SLS'],0,",",",");
                if (!empty($s1['TRG'])) $target=number_format($s1['TRG'],0,",",",");
                //if (!empty($s1['ACH'])) $ach=number_format($s1['ACH'],0,",",",");
                if ($target>0) {
                    $ach=round((double)$s1['SLS']/(double)$s1['TRG']*100,2);
                }
                
                //YTD
                $ytd_sales=0; $ytd_target=0; $ytd_ach=0;
                if (!empty($s1['ytd_sls'])) $ytd_sales=number_format($s1['ytd_sls'],0,",",",");
                if (!empty($s1['ytd_trg'])) $ytd_target=number_format($s1['ytd_trg'],0,",",",");
                //if (!empty($s1['ytd_ach'])) $ytd_ach=number_format($s1['ytd_ach'],0,",",",");
                if ($ytd_target>0) {
                    $ytd_ach=round((double)$s1['ytd_sls']/(double)$s1['ytd_trg']*100,2);
                }
                
                

                echo "<tr style='background-color:#ccffff;'>";
                echo "<td></td>";
                echo "<td><b>Total $area ($divisi) : </b></td>";
                echo "<td align='right'>$sales</td>";
                echo "<td align='right'>$target</td>";
                echo "<td align='right'>$ach</td>";
                //YTD
                echo "<td align='right'>$ytd_sales</td>";
                echo "<td align='right'>$ytd_target</td>";
                echo "<td align='right'>$ytd_ach</td>";
                echo "</tr>";
                
            }
            
        }
        
        //grand total
        /*
        $resultssub2 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG FROM $tmp3");
        foreach ($resultssub2 as $s2) {
            $sales=0;
            $target=0;
            $ach=0;

            if (!empty($s2['SLS']))
                $sales=number_format($s2['SLS'],0,",",",");

            if (!empty($s2['TRG']))
                $target=number_format($s2['TRG'],0,",",",");

            if (!empty($s2['ACH']))
                $ach=number_format($s2['ACH'],0,",",",");

            if ($target>0) {
                $ach=round((double)$sales/(double)$target*100,2);
            }


            echo "<tr>";
            echo "<td></td>";
            echo "<td><b>Grand Total Area : </b></td>";
            echo "<td align='right'>$sales</td>";
            echo "<td align='right'>$target</td>";
            echo "<td align='right'>$ach</td>";
            echo "</tr>";

        }
        */
        
        //REKAP DIVISI
        echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
        echo "<tr style='background-color:#cccccc;'>";
        echo "<td></td>";
        echo "<td><b>REKAP DIVISI</b></td>";
        echo "<td align='right'></td>";
        echo "<td align='right'></td>";
        echo "<td align='right'></td>";
        
        echo "<td align='right'></td>";
        echo "<td align='right'></td>";
        echo "<td align='right'></td>";
        
        echo "</tr>";
        
            
        $results1 = DB::query("SELECT distinct divprodid FROM $tmp3 order by divprodid");
        foreach ($results1 as $r1) {
            $divisi=$r1['divprodid'];
            echo "<tr style='background-color:#f2efef;'>";
            echo "<td></td>";
            echo "<td><b>$divisi</b></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
            
            $no=1;
            $results2 = DB::query("SELECT nama, iprodid, sum(sqty) sqty, sum(tqty) tqty, sum(SLS) as SLS, sum(TRG) as TRG, "
                    . " sum(ytd_sqty) ytd_sqty, sum(ytd_tqty) ytd_tqty, sum(ytd_sls) as ytd_sls, sum(ytd_trg) as ytd_trg "
                    . " FROM $tmp3 where divprodid='$divisi' group by nama, iprodid order by nama, iprodid");
            foreach ($results2 as $r2) {
                $produk=$r2['nama'];
                
                $sales=0; $target=0; $ach=0;
                if (!empty($r2['SLS'])) $sales=$r2['SLS'];
                if (!empty($r2['TRG'])) $target=$r2['TRG'];
                if ($target>0) {
                    $ach=round((double)$sales/(double)$target*100,2);
                }
                if (!empty($r2['SLS'])) $sales=number_format($r2['SLS'],0,",",",");
                if (!empty($r2['TRG'])) $target=number_format($r2['TRG'],0,",",",");
				
                $sqty=0; $tqty=0;
                if (!empty($r2['sqty'])) $sqty=number_format($r2['sqty'],0,",",",");
                if (!empty($r2['tqty'])) $tqty=number_format($r2['tqty'],0,",",",");
                
                
                //YTD
                $ytd_sales=0; $ytd_target=0; $ytd_ach=0;
                if (!empty($r2['ytd_sls'])) $ytd_sales=$r2['ytd_sls'];
                if (!empty($r2['ytd_trg'])) $ytd_target=$r2['ytd_trg'];
                if ($ytd_target>0) {
                    $ytd_ach=round((double)$ytd_sales/(double)$ytd_target*100,2);
                }
                if (!empty($r2['ytd_sls'])) $ytd_sales=number_format($r2['ytd_sls'],0,",",",");
                if (!empty($r2['ytd_trg'])) $ytd_target=number_format($r2['ytd_trg'],0,",",",");
				
                $ytd_sqty=0; $ytd_tqty=0;
                if (!empty($r2['ytd_sqty'])) $ytd_sqty=number_format($r2['ytd_sqty'],0,",",",");
                if (!empty($r2['ytd_tqty'])) $ytd_tqty=number_format($r2['ytd_tqty'],0,",",",");
                

                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>$produk</td>";
                echo "<td align='right'>$sqty</td>";
                echo "<td align='right'>$tqty</td>";
                echo "<td align='right'>$ach</td>";
                //YTD
                echo "<td align='right'>$ytd_sqty</td>";
                echo "<td align='right'>$ytd_tqty</td>";
                echo "<td align='right'>$ytd_ach</td>";
                echo "</tr>";

                $no++;
            }
            
            //sub total
            $resultssub1 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG, "
                    . " sum(ytd_sls) as ytd_sls, sum(ytd_trg) as ytd_trg "
                    . " FROM $tmp3 where divprodid='$divisi'");
            foreach ($resultssub1 as $s1) {
                $sales=0; $target=0; $ach=0;
                if (!empty($s1['SLS'])) $sales=$s1['SLS'];
                if (!empty($s1['TRG'])) $target=$s1['TRG'];
                //if (!empty($s1['ACH'])) $ach=number_format($s1['ACH'],0,",",",");
                if ($target>0) {
                    $ach=round((double)$sales/(double)$target*100,2);
                }
                	
                if (!empty($s1['SLS'])) $sales=number_format($s1['SLS'],0,",",",");
                if (!empty($s1['TRG'])) $target=number_format($s1['TRG'],0,",",",");
                
                //YTD
                $ytd_sales=0; $ytd_target=0; $ytd_ach=0;
                if (!empty($s1['ytd_sls'])) $ytd_sales=$s1['ytd_sls'];
                if (!empty($s1['ytd_trg'])) $ytd_target=$s1['ytd_trg'];
                //if (!empty($s1['ytd_ach'])) $ytd_ach=number_format($s1['ytd_ach'],0,",",",");
                if ($ytd_target>0) {
                    $ytd_ach=round((double)$ytd_sales/(double)$ytd_target*100,2);
                }
                	
                if (!empty($s1['ytd_sls'])) $ytd_sales=number_format($s1['ytd_sls'],0,",",",");
                if (!empty($s1['ytd_trg'])) $ytd_target=number_format($s1['ytd_trg'],0,",",",");
				

                echo "<tr style='background-color:#ccffff;'>";
                echo "<td></td>";
                echo "<td><b>Total $divisi : </b></td>";
                echo "<td align='right'>$sales</td>";
                echo "<td align='right'>$target</td>";
                echo "<td align='right'>$ach</td>";
                //YTD
                echo "<td align='right'>$ytd_sales</td>";
                echo "<td align='right'>$ytd_target</td>";
                echo "<td align='right'>$ytd_ach</td>";
                echo "</tr>";
                
            }
            
        }
        
        //grand total
        $resultssub2 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG, "
                . " sum(ytd_sls) as ytd_sls, sum(ytd_trg) as ytd_trg "
                . " FROM $tmp3");
        foreach ($resultssub2 as $s2) {
            
            $sales=0; $target=0; $ach=0;
            if (!empty($s2['SLS'])) $sales=number_format($s2['SLS'],0,",",",");
            if (!empty($s2['TRG'])) $target=number_format($s2['TRG'],0,",",",");
            //if (!empty($s2['ACH'])) $ach=number_format($s2['ACH'],0,",",",");
            if (!empty($s2['TRG'])) {
                if ($s2['TRG']>0) {
                    if (!empty($s2['SLS'])) $ach=round((double)$s2['SLS']/(double)$s2['TRG']*100,2);
                }
            }
            
            //YTD
            $ytd_sales=0; $ytd_target=0; $ytd_ach=0;
            if (!empty($s2['ytd_sls'])) $ytd_sales=number_format($s2['ytd_sls'],0,",",",");
            if (!empty($s2['ytd_trg'])) $ytd_target=number_format($s2['ytd_trg'],0,",",",");
            //if (!empty($s2['ytd_ach'])) $ytd_ach=number_format($s2['ytd_ach'],0,",",",");
            if (!empty($s2['ytd_trg'])) {
                if ($s2['ytd_trg']>0) {
                    if (!empty($s2['ytd_sls'])) $ytd_ach=round((double)$s2['ytd_sls']/(double)$s2['ytd_trg']*100,2);
                }
            }


            echo "<tr style='background-color:#cccccc;'>";
            echo "<td></td>";
            echo "<td><b>Grand Total : </b></td>";
            echo "<td align='right'>$sales</td>";
            echo "<td align='right'>$target</td>";
            echo "<td align='right'>$ach</td>";
            //YTD
            echo "<td align='right'>$ytd_sales</td>";
            echo "<td align='right'>$ytd_target</td>";
            echo "<td align='right'>$ytd_ach</td>";
            echo "</tr>";

        }
        
        
        //REKAP DIVISI
        ?>
    </tbody>
</table>
        


<?PHP
hapusdata:
    $results1 = DB::query("drop TEMPORARY table if exists $tmp3");
    $results1 = DB::query("drop TEMPORARY table if exists $tmp5");
    $results1 = DB::query("drop TEMPORARY table if exists $tmp6");
    $results1 = DB::query("drop TEMPORARY table if exists $tmp7");
?>



