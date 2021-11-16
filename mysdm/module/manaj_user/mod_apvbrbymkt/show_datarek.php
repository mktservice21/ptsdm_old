
<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();
    
    $pdokterid=$_POST['udoktid'];
    
    include "../../../config/koneksimysqli.php";
    
?>
    <div class='x_content'>

        <table id='datatable' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='10px'>Bank</th>
                    <th width='10px'>KCP</th>
                    <th width='10px'>No Rekening</th>
                    <th width='10px'>Atas Nama</th>
                    <th width='10px'>Relasi</th>
                    <th width='10px'>Input User</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select a.idbank, b.NAMA as nama_bank, a.kcp, a.norekening, a.atasnama, a.relasi_norek, a.tglinput, "
                        . " a.inputby, c.nama as nama_input "
                        . " from hrd.dokter_norekening as a LEFT JOIN dbmaster.bank as b on a.idbank=b.KDBANK "
                        . " LEFT JOIN hrd.karyawan as c on a.inputby=c.karyawanId "
                        . " WHERE a.dokterid='$pdokterid'";
                $tampil_u= mysqli_query($cnmy, $query);
                while ($urow= mysqli_fetch_array($tampil_u)) {
                    $uidbank=$urow['idbank'];
                    $unmbank=$urow['nama_bank'];
                    $ukcp=$urow['kcp'];
                    $unorek=$urow['norekening'];
                    $uatasnm=$urow['atasnama'];
                    $urelasi=$urow['relasi_norek'];
                    $uuserinputnm=$urow['nama_input'];

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$unmbank</td>";
                    echo "<td nowrap>$ukcp</td>";
                    echo "<td nowrap>$unorek</td>";
                    echo "<td nowrap>$uatasnm</td>";
                    echo "<td nowrap>$urelasi</td>";
                    echo "<td nowrap>$uuserinputnm</td>";
                    echo "</tr>";

                    $no++;
                }
                ?>
            </tbody>
        </table>

    </div>
<?PHP
    
    mysqli_close($cnmy);
?>