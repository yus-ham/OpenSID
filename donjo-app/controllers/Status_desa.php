<?php
/**
 * File ini:
 *
 * Controller status desa di dashboard Admin
 *
 * donjo-app/controllers/Status_desa.php
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
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package	OpenSID
 * @author	Tim Pengembang OpenDesa
 * @copyright	Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright	Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license	http://www.gnu.org/licenses/gpl.html	GPL V3
 * @link 	https://github.com/OpenSID/OpenSID
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Status_desa extends Admin_Controller {

	protected $cache_id = 'cache_idm';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('header_model');
		$this->load->library('data_publik');
		$this->load->driver('cache');
		$this->modul_ini = 200;
		$this->sub_modul_ini = 101;
	}

	public function index()
	{
		$header = $this->header_model->get_data();
		$kode_desa = $header['desa']['kode_desa'];
		if ($this->data_publik->has_internet_connection())
		{
			$this->data_publik->set_api_url("https://idm.kemendesa.go.id/open/api/desa/rumusan/$kode_desa/2020", "idm_$kode_desa");

			$idm = $this->cache->pakai_cache(function ()
			{
				return $this->data_publik->get_url_content($no_cache = true);
			}, $this->cache_id, 604800);

			if ($idm->body->error)
			{
				$this->cache->hapus_cache_untuk_semua($this->cache_id);
				$idm->body->mapData->error_msg = $idm->body->message . " : " . $idm->header->url . "<br><br>" .
					"Periksa Kode Desa di Identitas Desa. Masukkan kode lengkap, contoh '3507012006'<br>";
			}
		}

		$this->load->view('header', $header);
		$this->load->view('nav', $nav);
		$this->load->view('home/idm', ['idm' => $idm->body->mapData]);
		$this->load->view('footer');
	}

}
