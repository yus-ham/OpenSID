<?php
/*
 * File ini:
 *
 * Model pamong untuk modul Pemerintahan Desa
 *
 * donjo-app/models/Pamong_model.php
 *
 */

/*
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

class Pamong_model extends CI_Model {

	private $urut_model;

	public function __construct()
	{
		parent::__construct();
		require_once APPPATH.'/models/Urut_model.php';
		$this->urut_model = new Urut_Model('tweb_desa_pamong', 'pamong_id');
		$this->load->model(['referensi_model']);
	}

	public function list_data($offset = 0, $limit = 500)
	{
		$this->db->select('u.*, p.nama, p.nik, p.tempatlahir, p.tanggallahir, x.nama AS sex, b.nama AS pendidikan_kk, g.nama AS agama, x2.nama AS pamong_sex, b2.nama AS pamong_pendidikan, g2.nama AS pamong_agama');

		$this->list_data_sql();
		$this->db->order_by('u.urut')
			->limit($limit, $offset);

		$data = $this->db->get()->result_array();

		$j = $offset;
		for ($i=0; $i<count($data); $i++)
		{
			if (empty($data[$i]['id_pend']))
			{
				// Dari luar desa
				$data[$i]['nama'] = $data[$i]['pamong_nama'];
				$data[$i]['nik'] = $data[$i]['pamong_nik'];
				$data[$i]['tempatlahir'] = !empty($data[$i]['pamong_tempatlahir']) ? $data[$i]['pamong_tempatlahir'] : '-';
				$data[$i]['tanggallahir'] = $data[$i]['pamong_tanggallahir'];
				$data[$i]['sex'] = $data[$i]['pamong_sex'];
				$data[$i]['pendidikan_kk'] = $data[$i]['pamong_pendidikan'];
				$data[$i]['agama'] = $data[$i]['pamong_agama'];
				if (empty($data[$i]['pamong_nosk'])) $data[$i]['pamong_nosk'] = '-';
				if (empty($data[$i]['pamong_nohenti'])) $data[$i]['pamong_nohenti'] = '-';
			}
			else
			{
				if (empty($data[$i]['tempatlahir'])) $data[$i]['tempatlahir'] = '-';
			}
			$data[$i]['no'] = $j + 1;
			$j++;
		}

		return $data;
	}

	public function paging($p)
	{
		$this->db->select('COUNT(u.pamong_id) AS jml');
		$this->list_data_sql();

		$row = $this->db->get()->row_array();
		$jml_data = $row['jml'];

		$this->load->library('paging');
		$cfg['page'] = $p;
		$cfg['per_page'] = $this->session->per_page;
		$cfg['num_rows'] = $jml_data;
		$this->paging->init($cfg);

		return $this->paging;
	}

	private function list_data_sql()
	{
		$this->db
			->from('tweb_desa_pamong u')
			->join('tweb_penduduk p', 'u.id_pend = p.id', 'LEFT')
			->join('tweb_penduduk_pendidikan_kk b', 'p.pendidikan_kk_id = b.id', 'LEFT')
			->join('tweb_penduduk_sex x', 'p.sex = x.id', 'LEFT')
			->join('tweb_penduduk_agama g', 'p.agama_id = g.id', 'LEFT')
			->join('tweb_penduduk_pendidikan_kk b2', 'u.pamong_pendidikan = b2.id', 'LEFT')
			->join('tweb_penduduk_sex x2', 'u.pamong_sex = x2.id', 'LEFT')
			->join('tweb_penduduk_agama g2', 'u.pamong_agama = g2.id', 'LEFT');
		$this->search_sql();
		$this->filter_sql();
	}

	public function autocomplete()
	{
		$sql = "SELECT * FROM
				(SELECT p.nama
					FROM tweb_desa_pamong u
					LEFT JOIN tweb_penduduk p ON u.id_pend = p.id) a
				UNION SELECT pamong_nama FROM tweb_desa_pamong
				UNION SELECT p.nik
					FROM tweb_desa_pamong u
					LEFT JOIN tweb_penduduk p ON u.id_pend = p.id
				UNION SELECT pamong_nik FROM tweb_desa_pamong
				UNION SELECT pamong_niap FROM tweb_desa_pamong
				UNION SELECT pamong_nip FROM tweb_desa_pamong";
		$query = $this->db->query($sql);
		$data  = $query->result_array();

		return autocomplete_data_ke_str($data);
	}

	private function search_sql()
	{
		if ($this->session->has_userdata('cari'))
		{
			$cari = $this->db->escape_like_str($this->session->cari);
			$this->db
				->group_start()
					->like('p.nama', $cari)
					->or_like('u.pamong_nama', $cari)
					->or_like('u.pamong_niap', $cari)
					->or_like('u.pamong_nip', $cari)
					->or_like('u.pamong_nik', $cari)
					->or_like('p.nik', $cari)
				->group_end();
		}
	}

	private function filter_sql()
	{
		if ($this->session->has_userdata('status'))
		{
			$this->db->where('u.pamong_status', $this->session->status);
		}
	}

	public function get_data($id = 0)
	{
		$sql = "SELECT u.*, p.nama as nama
			FROM tweb_desa_pamong u
			LEFT JOIN tweb_penduduk p ON u.id_pend = p.id
			WHERE pamong_id = ?";
		$query = $this->db->query($sql, $id);
		$data  = $query->row_array();
		$data['pamong_niap_nip'] = (!empty($data['pamong_nip']) and $data['pamong_nip'] != '-') ? $data['pamong_nip'] : $data['pamong_niap'];
		if (!empty($data['id_pend']))
		{
			// Dari database penduduk
			$data['pamong_nama'] = $data['nama'];
		}
		return $data;
	 }

	public function get_pamong($id = null)
	{
		$pamong = $this->db->where('pamong_id', $id)->limit(1)->get('tweb_desa_pamong')->row_array();;

		return $pamong;
	}

	public function insert()
	{
		$_SESSION['success'] = 1;
		$nama_file = '';
		$lokasi_file = $_FILES['foto']['tmp_name'];
		$tipe_file = $_FILES['foto']['type'];
		$nama_file = $_FILES['foto']['name'];

		if (!empty($nama_file))
		{
		  $nama_file = urlencode(generator(6)."_".$_FILES['foto']['name']);
			if (!empty($lokasi_file) AND in_array($tipe_file, unserialize(MIME_TYPE_GAMBAR)))
			{
				UploadFoto($nama_file, $old_foto='', $tipe_file);
			}
			else
			{
				$nama_file = '';
				$_SESSION['success'] = -1;
				$_SESSION['error_msg'] = " -> Jenis file salah: " . $tipe_file;
			}
		}

		$data = array();
		$data['foto'] = $nama_file;
		$data = $this->siapkan_data($data);
		// Beri urutan terakhir
		$data['urut'] = $this->urut_model->urut_max() + 1;
		$data['pamong_tgl_terdaftar'] = date('Y-m-d');

		$outp = $this->db->insert('tweb_desa_pamong', $data);

		if (!$outp) $_SESSION['success'] = -1;
	}

	private function siapkan_data(&$data)
	{
		$data['id_pend'] = $this->input->post('id_pend');
		$this->data_pamong_asal($data);
		$data['pamong_nip'] = strip_tags($this->input->post('pamong_nip'));
		$data['pamong_niap'] = strip_tags($this->input->post('pamong_niap'));
		$data['jabatan'] = strip_tags($this->input->post('jabatan'));
		$data['pamong_pangkat'] = strip_tags($this->input->post('pamong_pangkat'));
		$data['pamong_status'] = $this->input->post('pamong_status');
		$data['pamong_nosk'] = strip_tags($this->input->post('pamong_nosk'));
		$data['pamong_tglsk'] = !empty($this->input->post('pamong_tglsk')) ? tgl_indo_in($this->input->post('pamong_tglsk')) : NULL;
		$data['pamong_tanggallahir'] = !empty($this->input->post('pamong_tanggallahir')) ? tgl_indo_in($this->input->post('pamong_tanggallahir')) : NULL;
		$data['pamong_nohenti'] = !empty($this->input->post('pamong_nohenti')) ? strip_tags($this->input->post('pamong_nohenti')) : NULL;
		$data['pamong_tglhenti'] = !empty($this->input->post('pamong_tglhenti')) ? tgl_indo_in($this->input->post('pamong_tglhenti')) : NULL;
		$data['pamong_masajab'] = strip_tags($this->input->post('pamong_masajab')) ?: NULL;
		return $data;
	}

	private function data_pamong_asal(&$data)
	{
		if (empty($data['id_pend']))
		{
			unset($data['id_pend']);
			$data['pamong_nama'] = strip_tags($this->input->post('pamong_nama')) ?: null;
			$data['pamong_nik'] = strip_tags($this->input->post('pamong_nik')) ?: null;
			$data['pamong_tempatlahir'] = strip_tags($this->input->post('pamong_tempatlahir')) ?: null;
			$data['pamong_tanggallahir'] = tgl_indo_in($this->input->post('pamong_tanggallahir')) ?: null;
			$data['pamong_sex'] = $this->input->post('pamong_sex') ?: null;
			$data['pamong_pendidikan'] = $this->input->post('pamong_pendidikan') ?: null;
			$data['pamong_agama'] = $this->input->post('pamong_agama') ?: null;
		}
	}

	public function update($id=0)
	{
		$data = array();
		unset($_SESSION['validation_error']);
		$_SESSION['success'] = 1;;
		unset($_SESSION['error_msg']);
		$lokasi_file = $_FILES['foto']['tmp_name'];
		$tipe_file = $_FILES['foto']['type'];
		$nama_file = $_FILES['foto']['name'];
		$old_foto = $this->input->post('old_foto');
		if (!empty($nama_file))
		{
			if (!empty($lokasi_file) AND in_array($tipe_file, unserialize(MIME_TYPE_GAMBAR)))
			{
			  $data['foto'] = urlencode(generator(6)."_".$nama_file);
				UploadFoto($data['foto'], $old_foto, $tipe_file);
			}
			else
			{
				$_SESSION['success'] = -1;
				$_SESSION['error_msg'] = " -> Jenis file salah: " . $tipe_file;
			}
		}

		$data = $this->siapkan_data($data);
		$this->db->where("pamong_id", $id)->update('tweb_desa_pamong', $data);
	}

	public function delete($id='', $semua=false)
	{
		if (!$semua) $this->session->success = 1;

		$foto = $this->db->select('foto')->where('pamong_id',$id)->get('tweb_desa_pamong')->row()->foto;
		if (!empty($foto))
		{
			unlink(LOKASI_USER_PICT.$foto);
			unlink(LOKASI_USER_PICT.'kecil_'.$foto);
		}

		$outp = $this->db->where('pamong_id', $id)->delete('tweb_desa_pamong');

		status_sukses($outp, $gagal_saja=true); //Tampilkan Pesan
	}

	public function delete_all()
	{
		$this->session->success = 1;

		$id_cb = $_POST['id_cb'];
		foreach ($id_cb as $id)
		{
			$this->delete($id, $semua=true);
		}
	}

	public function ttd($jenis, $id, $val)
	{
		if ($val == 1)
		{
			// Hanya satu pamong yang boleh digunakan sebagai ttd a.n / u.b
			$this->db->where($jenis, 1)->update('tweb_desa_pamong', [$jenis => 0]);
		}
		$this->db->where('pamong_id', $id)->update('tweb_desa_pamong', [$jenis => $val]);
	}

	public function get_ttd()
	{
		$ttd = $this->db->where('pamong_ttd', 1)->get('tweb_desa_pamong')->row_array();
		return $ttd;
	}

	public function get_ub()
	{
		$ub = $this->db->where('pamong_ub', 1)->get('tweb_desa_pamong')->row_array();
		return $ub;
	}

	// $arah:
	//		1 - turun
	// 		2 - naik
	public function urut($id, $arah)
	{
		$this->urut_model->urut($id, $arah);
	}

	/*
	 * Mengambil semua data penduduk kecuali yg sdh menjadi pamong untuk pilihan drop-down form
	 */
	public function list_penduduk()
	{
		$data = $this->db->select('u.id, u.nik, u.nama, w.dusun, w.rw, w.rt, u.sex')
			->from('penduduk_hidup u')
			->join('tweb_wil_clusterdesa w', 'u.id_cluster = w.id', 'left')
			->where('u.id NOT IN (SELECT id_pend FROM tweb_desa_pamong WHERE id_pend IS NOT NULL)')
			->get()
			->result_array();

		return $data;
	}

	/*
	 * Ambil data untuk widget aparatur desa
	 */
	public function list_aparatur_desa()
	{
		$data['daftar_perangkat'] = $this->db->select('dp.jabatan, dp.pamong_niap, dp.foto,
			CASE WHEN dp.id_pend IS NULL THEN dp.pamong_nama
			ELSE p.nama END AS nama', FALSE)
			->from('tweb_desa_pamong dp')
			->join('tweb_penduduk p', 'p.id = dp.id_pend', 'left')
			->where('dp.pamong_status', '1')
			->order_by('dp.urut')
			->get()
			->result_array();

		foreach ($data['daftar_perangkat'] as $key => $perangkat)
		{
			$perangkat['foto'] = AmbilFoto($perangkat['foto'], "besar");
			if (!$data['foto_pertama'] and $perangkat['foto'] != FOTO_DEFAULT) $data['foto_pertama'] = $key;
		 	$data['daftar_perangkat'][$key] = $perangkat;
		}

		return $data;
	}

	//----------------------------------------------------------------------------------------------------

	/**
	 * @param $id id
	 * @param $val status : 1 = Unlock, 2 = Lock
	 */
	public function lock($id, $val)
	{
		$this->db
			->where('pamong_id', $id)
			->update('tweb_desa_pamong', ['pamong_status' => $val]);
	}

}
?>
