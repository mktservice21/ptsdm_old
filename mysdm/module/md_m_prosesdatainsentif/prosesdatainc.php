<?PHP
function caridatainsentif_query($con, $temp, $bulan, $jabatan, $pdivprod) {
    $cnmy=$con;
    $ptgl1=$bulan;
    
    $fildivisi="";
    if (!empty($pdivprod)) $fildivisi=" AND IFNULL(g.divprodid,'')='$pdivprod'";
    if ($pdivprod=="blank") $fildivisi=" AND IFNULL(g.divprodid,'')=''";
        
    $now=date("mdYhis");
    $tmp01 ="RTMPPROSINC01_".$_SESSION['USERID']."_$now";
    
    $query = "SELECT CAST(1 as DECIMAL(10,0)) as urutan,  
            '$ptgl1' AS bulan,
            g.divprodid AS divisi,
                  c.iCabangId icabangid,
            c.nama AS cabang,
            'MR' AS jabatan,
            k.karyawanid,
            k.nama,
            c.region,
            CONVERT(SUM(i.incentive), DEC (0)) AS jumlah
          FROM
            ms.incentive_mr i
            LEFT JOIN
              (SELECT
                mr,
                divprodid
              FROM
                ms.mrgp
              WHERE bulan = '$ptgl1') g
              ON i.karyawanid = g.mr
            JOIN ms.karyawan k
              ON i.karyawanid = k.karyawanId
            JOIN
              (SELECT DISTINCT
                mr,
                icabangid
              FROM
                ms.penempatan_marketing
              WHERE bulan = '$ptgl1'
                AND mr <> '000') pm
              ON i.karyawanid = pm.mr
            JOIN sls.icabang c
              ON pm.icabangid = c.iCabangId
          WHERE bulan = '$ptgl1' $fildivisi AND i.jenis2='GSM' 
          GROUP BY k.nama,
            c.region,
            c.nama";
    
    //echo "$query";exit;
    $query = "create  table dbtemp.$tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
	
    //tambahan 11 03 2020
    mysqli_query($cnmy, "ALTER TABLE dbtemp.$tmp01 ADD COLUMN ndivisiid VARCHAR(20)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "UPDATE dbtemp.$tmp01 a JOIN (select distinct bulan, karyawanid, jenis from ms.jenis_mr WHERE bulan='$ptgl1') b "
            . " ON a.bulan=b.bulan AND a.karyawanid=b.karyawanid SET a.ndivisiid=b.jenis WHERE "
            . " IFNULL(a.jabatan,'')='MR'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    mysqli_query($cnmy, "UPDATE dbtemp.$tmp01 SET divisi='CAN' WHERE IFNULL(ndivisiid,'') IN ('CAN', 'CANARY', 'CANARYPLUS')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "UPDATE dbtemp.$tmp01 SET divisi='EAGLE' WHERE IFNULL(ndivisiid,'') IN ('EAGLE')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "UPDATE dbtemp.$tmp01 SET divisi='PEACO' WHERE IFNULL(ndivisiid,'') IN ('PEACO', 'PEACOC', 'PEACOCK')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "UPDATE dbtemp.$tmp01 SET divisi='PIGEO' WHERE IFNULL(ndivisiid,'') IN ('PIGEO', 'PIGEON')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    mysqli_query($cnmy, "UPDATE dbtemp.$tmp01 SET divisi='CAN' WHERE IFNULL(jabatan,'') NOT IN ('MR')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //END tambahan 11 03 2020
	
	
if ($pdivprod=="" OR $pdivprod=="CAN") {
    
    $query = "INSERT INTO dbtemp.$tmp01 (urutan, bulan, divisi, icabangid, cabang, jabatan, karyawanid, nama, region, jumlah)
        SELECT 2 as urutan, 
        '$ptgl1' AS bulan,
        'CANARY' AS divisi,
              c.iCabangId,
        c.nama AS cabang,
        'AM' AS jabatan,
        k.karyawanid,
        k.nama,
        c.region,
        CONVERT(SUM(i.incentive), DEC (0)) AS jumlah
      FROM
        ms.incentive_am i
        LEFT JOIN
          (SELECT DISTINCT
            icabangid,
            am
          FROM
            ms.penempatan_marketing pm
          WHERE bulan = '$ptgl1') pm
          ON i.karyawanid = pm.am
        JOIN ms.karyawan k
          ON i.karyawanid = k.karyawanId
        JOIN sls.icabang c
          ON pm.icabangid = c.iCabangId
      WHERE i.bulan = '$ptgl1' AND i.jenis2='GSM' 
      GROUP BY k.nama,
        c.region,
        c.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query="INSERT INTO dbtemp.$tmp01 (urutan, bulan, divisi, icabangid, cabang, jabatan, karyawanid, nama, region, jumlah)
        SELECT 3 as urutan, 
        '$ptgl1' AS bulan,
        'CANARY' AS divisi,
              c.icabangid,
        c.nama AS cabang,
        'DM' AS jabatan,
        k.karyawanid,
        k.nama,
        c.region,
        CONVERT(SUM(i.incentive), DEC (0)) AS jumlah
      FROM
        ms.incentive_dm i
        LEFT JOIN
          (SELECT DISTINCT
            icabangid,
            dm
          FROM
            ms.penempatan_marketing pm
          WHERE bulan = '$ptgl1') pm
          ON i.karyawanid = pm.dm
        JOIN ms.karyawan k
          ON i.karyawanid = k.karyawanId
        JOIN sls.icabang c
          ON pm.icabangid = c.iCabangId
      WHERE i.bulan = '$ptgl1' AND i.jenis2='GSM' 
      GROUP BY k.nama,
        c.region,
        c.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query="UPDATE dbtemp.$tmp01 SET divisi='CAN' WHERE IFNULL(divisi,'')='CANARY'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}

    return $tmp01;
    exit;
    hapusdata:
        mysqli_query($cnmy, "DROP  TABLE dbtemp.$tmp01");
}
?>