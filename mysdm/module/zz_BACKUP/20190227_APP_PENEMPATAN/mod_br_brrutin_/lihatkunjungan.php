<?PHP

    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    
    include "config/koneksimysqli.php";
    $query = "select * from dbmaster.t_brrutin0 where idrutin='$_GET[brid]' LIMIT 1";
    $tampil= mysqli_query($cnmy, $query);
    $i= mysqli_fetch_array($tampil);
    
    $date1=$i['bulan'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $tgl2= date("Y-m-t", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    $idkar=$i['karyawanid'];
    $idajukan=$i['karyawanid'];

    ?>
    <div class='x_content'>
        <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='30px'></th>
                    <th width='150px'>Hari / Tanggal</th>
                    <th>Tujuan / Note</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $patasan1="";
                    $patasan2="";
                    $patasan3="";

                    while (strtotime($tgl1) <= strtotime($tgl2)) {
                        $mytgl= date("Ymd", strtotime($tgl1));
                        $ptgl= date("d/m/Y", strtotime($tgl1));
                        $pddat= date("Y-m-d", strtotime($tgl1));
                        $mhari= date("d", strtotime($tgl1));
                        $phari=date("w", strtotime($tgl1));
                        
                        $nmhari=$seminggu[$phari];
                        $ket="";
                        $stl="";
                        if ($phari==0 OR $phari==6)
                            $stl="style='background:#FAEBD7;'";
                        
                        $sql = "SELECT * FROM dbmaster.t_planuc_mkt WHERE karyawanid='$idkar' and DATE_FORMAT(tgl, '%Y%m%d') = '$mytgl' order by tgl";
                        $tampil = mysqli_query($cnmy, $sql);
                        $ketemu = mysqli_num_rows($tampil);
                        if ($ketemu>0) {
                            $t= mysqli_fetch_array($tampil);
                            $nourut=$t['nourut'];
                            $ket=$t['keterangan'];
                            
                            if (!empty($t['atasan1'])) $patasan1=$t['atasan1'];
                            if (!empty($t['atasan2'])) $patasan2=$t['atasan2'];
                            if (!empty($t['atasan3'])) $patasan3=$t['atasan3'];
                            
                        }
                        
                        echo "<tr $stl>";
                        echo "<td nowrap>$mhari</td>";
                        echo "<td nowrap>$nmhari, $ptgl</td>";
                        echo "<td><input type='hidden' name='txtket$no' id='txtket$no' size='80px' value='$ket'>$ket</td>";
                        echo "</tr>";
                        $no++;
                        $tgl1 = date ("Y-m-d", strtotime("+1 day", strtotime($tgl1)));
                    }
                ?>
            </tbody>
        </table>

    </div>
    <style>
        .divnone {
            display: none;
        }
        #datatablercbi th {
            font-size: 14px;
        }
        #datatablercbi td { 
            font-size: 13px;
        }
        .imgzoom:hover {
            -ms-transform: scale(3.5); /* IE 9 */
            -webkit-transform: scale(3.5); /* Safari 3-8 */
            transform: scale(3.5);

        }
    </style>
    <?PHP
    exit;

?>



<!-- jQuery -->
<script src="vendors/jquery/dist/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var icar = window.opener.document.getElementById('e_idkaryawan').value;
        var itgl = window.opener.document.getElementById('e_bulan').value;
        var ptgl = itgl.split("/");
        var thnbln = ptgl[1]+"-"+ptgl[0]+"-01";
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrluarkota/kunjungan.php?module=getkunjungan",
            data:"ubulan="+thnbln+"&umr="+icar,
            success:function(data){
                $("#c-data").html(data);
            }
        });
    });
</script>

<div id='c-data'>
    
</div>