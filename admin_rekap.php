<?php
include "koneksi.php";

$query = "SELECT 
    tanggal_input,
    kecamatan,
    desa,
    SUM(CASE WHEN jenis_sasaran = 'catin' THEN 1 ELSE 0 END) AS catin,
    SUM(CASE WHEN jenis_sasaran = 'ibu_hamil' THEN 1 ELSE 0 END) AS bumil,
    SUM(CASE WHEN jenis_sasaran = 'ibu_menyusui' THEN 1 ELSE 0 END) AS bupas,
    SUM(CASE WHEN jenis_sasaran = 'baduta' THEN 1 ELSE 0 END) AS baduta,
    SUM(CASE WHEN jenis_sasaran = 'balita' THEN 1 ELSE 0 END) AS balita,
    
    -- Penapisan KRS
    SUM(CASE WHEN status_anemia != '' AND status_anemia IS NOT NULL THEN 1 ELSE 0 END) AS anemia,
    SUM(CASE WHEN lila > 0 OR berat_badan > 0 THEN 1 ELSE 0 END) AS kek,
    SUM(CASE WHEN sanitasi_jamban LIKE '%Tidak Layak%' THEN 1 ELSE 0 END) AS jamban_tidak_layak,
    SUM(CASE WHEN sumber_air LIKE '%Tidak Layak%' THEN 1 ELSE 0 END) AS air_tidak_layak,
    
    -- Jenis Intervensi
    SUM(CASE WHEN intervensi LIKE '%KIE%' THEN 1 ELSE 0 END) AS intervensi_kie,
    SUM(CASE WHEN intervensi LIKE '%Genting%' THEN 1 ELSE 0 END) AS intervensi_genting,
    SUM(CASE WHEN intervensi LIKE '%Rujukan%' THEN 1 ELSE 0 END) AS intervensi_rujukan,
    SUM(CASE WHEN intervensi LIKE '%Bimbingan Perkawinan%' THEN 1 ELSE 0 END) AS intervensi_bimwin,
    SUM(CASE WHEN intervensi LIKE '%Bansos%' THEN 1 ELSE 0 END) AS intervensi_bansos,
    SUM(CASE WHEN intervensi LIKE '%EPPGBM%' THEN 1 ELSE 0 END) AS intervensi_eppgbm,
    SUM(CASE WHEN intervensi LIKE '%PMT%' THEN 1 ELSE 0 END) AS intervensi_pmt,
    
    -- Sumber Intervensi
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%Dana Desa%' OR sumber_intervensi_air LIKE '%Dana Desa%' THEN 1 ELSE 0 END) AS dana_desa,
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%APBD Kabupaten%' OR sumber_intervensi_air LIKE '%APBD Kabupaten%' THEN 1 ELSE 0 END) AS apbd_kab,
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%APBN%' OR sumber_intervensi_air LIKE '%APBN%' THEN 1 ELSE 0 END) AS apbn,
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%Swadaya%' OR sumber_intervensi_air LIKE '%Swadaya%' THEN 1 ELSE 0 END) AS swadaya
FROM keluarga_sasaran 
GROUP BY tanggal_input, kecamatan, desa 
ORDER BY tanggal_input DESC";

$result = mysqli_query($conn, $query);
?>