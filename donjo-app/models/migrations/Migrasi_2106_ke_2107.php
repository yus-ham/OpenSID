<?php

/**
 * File ini:
 *
 * Model untuk migrasi database
 *
 * donjo-app/models/migrations/Migrasi_2106_ke_2107.php
 *
 */

/**
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:

 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.

 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package   OpenSID
 * @author    Tim Pengembang OpenDesa
 * @copyright Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright Hak Cipta 2016 - 2021 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license   http://www.gnu.org/licenses/gpl.html GPL V3
 * @link      https://github.com/OpenSID/OpenSID
 */
class Migrasi_2106_ke_2107 extends MY_model
{
	public function up()
	{
		$hasil = true;
		// Migrasi fitur premium
		// Jalankan juga migrasi versi-versi sebelumnya, karena migrasi dari rllis umum belum menjalankan
		$daftar_migrasi_premium = ['2009', '2010', '2011', '2012'];
		foreach ($daftar_migrasi_premium as $migrasi)
		{
			$migrasi_premium = 'migrasi_fitur_premium_'.$migrasi;
			$file_migrasi = 'migrations/'.$migrasi_premium;
				$this->load->model($file_migrasi);
				$hasil = $hasil && $this->$migrasi_premium->up();
		}

		$hasil = $hasil && $this->migrasi_2021062701($hasil);
		$hasil = $hasil && $this->migrasi_2021062872($hasil);

		status_sukses($hasil);
		return $hasil;
	}

	protected function migrasi_2021062701($hasil)
	{
		// Buat folder untuk cache - 'cache\';
		mkdir(config_item('cache_path'), 0775, true);

		// Ubah type data ke text, agar bisa menampung banyak karakter
		$hasil = $hasil && $this->dbforge->modify_column('setting_aplikasi', [
			'value' => ['type' => 'text'],
		]);

		// Url production layanan opendesa
		$hasil = $hasil && $this->tambah_setting([
			'key' => 'layanan_opendesa_server',
			'value' => 'https://layanan.opendesa.id',
			'keterangan' => 'Alamat Server Layanan OpenDESA',
			'kategori' => '',
		]);

		// Url development layanan opendesa
		$hasil = $hasil && $this->tambah_setting([
			'key' => 'layanan_opendesa_dev_server',
			'value' => '',
			'keterangan' => 'Alamat Server Dev Layanan OpenDESA',
			'kategori' => '',
		]);

		// Token pelanggan layanan opendesa
		$hasil = $hasil && $this->tambah_setting([
			'key' => 'layanan_opendesa_token',
			'value' => '',
			'jenis' => 'textarea',
			'keterangan' => 'Token pelanggan Layanan OpenDESA',
			'kategori' => '',
		]);

		// Hapus API Key Pelanggan
		$hasil = $hasil && $this->db->where('key', 'api_key_opensid')->delete('setting_aplikasi');

		return $hasil;
	}

	protected function migrasi_2021062872($hasil)
	{
		// Ubah kategori layanan_opendesa_server, layanan_opendesa_dev_server, layanan_opendesa_token jadi pelanggan
		$hasil = $hasil && $this->db
			->where_in('key', ['layanan_opendesa_server', 'layanan_opendesa_dev_server', 'layanan_opendesa_token'])
			->update('setting_aplikasi', ['kategori' => 'pelanggan']);

		return $hasil;
	}
}
