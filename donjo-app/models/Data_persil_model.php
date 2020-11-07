<?php
/*
 * File ini:
 *
 * Model untuk Modul Persil
 *
 * donjo-app/models/Data_persil_model.php
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

 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.

 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package OpenSID
 * @author  Tim Pengembang OpenDesa
 * @copyright Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license http://www.gnu.org/licenses/gpl.html  GPL V3
 * @link  https://github.com/OpenSID/OpenSID
 */

class Data_persil_model extends MY_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function autocomplete($cari='')
	{
		return $this->autocomplete_str('nomor', 'persil', $cari);
	}

	private function search_sql()
	{
		if ($this->session->cari)
		{
			$cari = $this->session->cari;
			$kw = $this->db->escape_like_str($cari);
			$kw = '%' .$kw. '%';
			$this->db->where("p.nomor like '$kw'");
		}
	}

	public function paging($p=1)
	{
		$this->main_sql();
		$jml = $this->db->select('p.id')->get()->num_rows();

		$this->load->library('paging');
		$cfg['page'] = $p;
		$cfg['per_page'] = $_SESSION['per_page'];
		$cfg['num_rows'] = $jml;
		$this->paging->init($cfg);

		return $this->paging;
	}

	private function main_sql()
	{
		$this->db->from('persil p')
			->join('ref_persil_kelas k', 'k.id = p.kelas', 'left')
			->join('tweb_wil_clusterdesa w', 'w.id = p.id_wilayah', 'left')
			->join('mutasi_cdesa m', 'p.id = m.id_persil', 'left')
			->join('cdesa c', 'c.id = p.cdesa_awal', 'left')
			->group_by('p.id, nomor_urut_bidang');
		$this->search_sql();
	}

	public function list_data($offset, $per_page)
	{
		$this->main_sql();
		$data = $this->db->select('p.*, k.kode, count(m.id_persil) as jml_bidang, c.nomor as nomor_cdesa_awal')
			->select('(CASE WHEN p.id_wilayah IS NOT NULL THEN CONCAT("RT ", w.rt, " / RW ", w.rw, " - ", w.dusun) ELSE p.lokasi END) AS alamat')
			->order_by('nomor, nomor_urut_bidang')
			->get()
			->result_array();

		$j = $offset;
		for ($i=0; $i<count($data); $i++)
		{
			$data[$i]['no'] = $j + 1;
			$j++;
		}

		return $data;
	}

	public function list_persil()
	{
		$data = $this->db
			->select('p.id, nomor, nomor_urut_bidang')
			->select('CONCAT("RT ", w.rt, " / RW ", w.rw, " - ", w.dusun) as lokasi')
			->from('persil p')
			->join('tweb_wil_clusterdesa w', 'w.id = p.id_wilayah')
			->order_by('nomor, nomor_urut_bidang')
			->get()->result_array();
		return $data;
	}

	public function get_persil($id)
	{
		$data = $this->db->select('p.*, k.kode, k.tipe, k.ndesc, c.nomor as nomor_cdesa_awal')
			->select('CONCAT("RT ", w.rt, " / RW ", w.rw, " - ", w.dusun) as alamat')
			->from('persil p')
			->join('ref_persil_kelas k', 'k.id = p.kelas', 'left')
			->join('tweb_wil_clusterdesa w', 'w.id = p.id_wilayah', 'left')
			->join('cdesa c', 'c.id = p.cdesa_awal', 'left')
			->where('p.id', $id)
			->get()->row_array();
		return $data;
	}

	public function get_list_mutasi($id)
	{
		$this->db
			->select('m.*, m.id_cdesa_masuk, c.nomor as cdesa_masuk, k.id as id_cdesa_keluar')
			->from('persil p')
			->join('mutasi_cdesa m', 'p.id = m.id_persil', 'left')
			->join('cdesa c', 'c.id = m.id_cdesa_masuk', 'left')
			->join('cdesa k', 'k.nomor = m.cdesa_keluar', 'left')
			->where('m.id_persil', $id);
		$data = $this->db->get()->result_array();
		return $data;
	}

 	private function get_persil_by_nomor($nomor, $nomor_urut_bidang)
 	{
 		$id = $this->db->select('id')
 			->where('nomor', $nomor)
 			->where('nomor_urut_bidang', $nomor_urut_bidang)
 			->get('persil')->row()->id;
 		return $id;
 	}

	public function simpan_persil($post)
	{
		$data = array();
		$data['nomor'] = bilangan($post['no_persil']);
		$data['nomor_urut_bidang'] = bilangan($post['nomor_urut_bidang']);
		$data['kelas'] = $post['kelas'];
		$data['id_wilayah'] = $post['id_wilayah'] ?: NULL;
		$data['luas_persil'] = bilangan($post['luas_persil']) ?: NULL;
		$data['lokasi'] = $post['lokasi'] ?: NULL;
		$id_persil = $post['id_persil'] ?: $this->get_persil_by_nomor($post['no_persil'], $post['nomor_urut_bidang']);
		if ($id_persil)
		{
			$this->db->where('id', $id_persil)
				->update('persil', $data);
		}
		else
		{
			$data['cdesa_awal'] = bilangan($post['cdesa_awal']);
			$data['nomor'] = $post['no_persil'];
			$this->db->insert('persil', $data);
			$id_persil = 	$this->db->insert_id();
			$this->mutasi_awal($data, $id_persil);
		}
		return $id_persil;
 	}

	public function hapus($id)
	{
		$hasil = $this->db->where('id', $id)
			->delete('persil');
		status_sukses($hasil);
	}

	public function list_dusunrwrt()
	{
		$strSQL = "SELECT `id`,`rt`,`rw`,`dusun` FROM `tweb_wil_clusterdesa` WHERE (`rt`>0) ORDER BY `dusun`";
		$query = $this->db->query($strSQL);
		return $query->result_array();
	}

	public function list_persil_kelas($table='')
	{
		if ($table)
		{
			$data =$this->db->order_by('kode')
				->get_where('ref_persil_kelas', array('tipe' => $table))
				->result_array();
			$data = array_combine(array_column($data, 'id'), $data);
		}
		else
		{
			$data = $this->db->order_by('kode')
			->get('ref_persil_kelas')
			->result_array();
			$data = array_combine(array_column($data, 'id'), $data);
		}

		return $data;
	}

	public function awal_persil($cdesa_awal, $id_persil, $hapus=false)
	{
		// Hapus mutasi awal kalau ada
		$this->db->where('id_persil', $id_persil)
			->where('jenis_mutasi', '9')
			->delete('mutasi_cdesa');
		$cdesa_awal = $hapus ? null : $cdesa_awal; // Kosongkan pemilik awal persil ini
		$this->db->where('id', $id_persil)
			->set('cdesa_awal', $cdesa_awal)
			->update('persil');
		$persil = $this->db->where('id', $id_persil)
			->get('persil')->row_array();
		$this->mutasi_awal($persil, $id_persil);
	}

	private function mutasi_awal($data, $id_persil)
 	{
 		$mutasi['id_cdesa_masuk'] = $data['cdesa_awal'];
 		$mutasi['jenis_mutasi'] = '9';
 		$mutasi['tanggal_mutasi'] = date('Y-m-d H:i:s');
 		$mutasi['id_persil'] = $id_persil;
 		$mutasi['luas'] = $data['luas_persil'];
 		$mutasi['keterangan'] = 'Pemilik awal persil ini';
 		$this->db->insert('mutasi_cdesa', $mutasi);
 	}

}
?>