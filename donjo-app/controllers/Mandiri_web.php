<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File ini:
 *
 * Controller untuk modul Layanan Mandiri
 *
 * donjo-app/controllers/Mandiri_web.php
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

class Mandiri_web extends Web_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(['web_dokumen_model', 'surat_model', 'penduduk_model', 'keluar_model', 'permohonan_surat_model', 'mailbox_model', 'penduduk_model', 'lapor_model', 'keluarga_model']);
		$this->load->helper('download');

		if ($this->session->mandiri != 1) redirect('first');
	}

	public function mandiri($p=1, $m=0, $kat=1)
	{
		$data = $this->includes;
		$data['p'] = $p;
		$data['menu_surat_mandiri'] = $this->surat_model->list_surat_mandiri();
		$data['m'] = $m;
		$data['kat'] = $kat;

		/* nilai $m
			1 untuk menu profilku
			2 untuk menu layanan
			3 untuk menu lapor
			4 untuk menu bantuan
			5 untuk menu surat mandiri
		*/
		switch ($m)
		{
			case 1:
				$data['list_kelompok'] = $this->penduduk_model->list_kelompok($_SESSION['id']);
				$data['list_dokumen'] = $this->penduduk_model->list_dokumen($_SESSION['id']);
				break;
			case 21:
				$data['tab'] = 2;
				$data['m'] = 2;
			case 2:
				$data['surat_keluar'] = $this->keluar_model->list_data_perorangan($_SESSION['id']);
				$data['permohonan'] = $this->permohonan_surat_model->list_permohonan_perorangan($_SESSION['id']);
				break;
			case 3:
				$inbox = $this->mailbox_model->get_inbox_user($_SESSION['nik']);
				$outbox = $this->mailbox_model->get_outbox_user($_SESSION['nik']);
				$data['main_list'] = $kat == 1 ? $inbox : $outbox;
				$data['submenu'] = $this->mailbox_model->list_menu();
				$_SESSION['mailbox'] = $kat;
				break;
			case 4:
				$data['bantuan_penduduk'] = $this->program_bantuan_model->daftar_bantuan_yang_diterima($_SESSION['nik']);
				break;
			case 5:
				$data['list_dokumen'] = $this->penduduk_model->list_dokumen($_SESSION['id']);
				break;
			default:
				break;
		}

		$data['desa'] = $this->config_model->get_data();
		$data['penduduk'] = $this->penduduk_model->get_penduduk($_SESSION['id']);
		$this->load->view('web/mandiri/layout.mandiri.php', $data);
	}

	public function mandiri_surat($id_permohonan='')
	{
		$data = $this->includes;
		$data['menu_surat_mandiri'] = $this->surat_model->list_surat_mandiri();
		$data['menu_dokumen_mandiri'] = $this->lapor_model->get_surat_ref_all();
		$data['m'] = 5;
		$data['permohonan'] = $this->permohonan_surat_model->get_permohonan($id_permohonan);
		$data['list_dokumen'] = $this->penduduk_model->list_dokumen($_SESSION['id']);
		$data['penduduk'] = $this->penduduk_model->get_penduduk($_SESSION['id']);

		// Ambil data anggota KK
		if ($data['penduduk']['kk_level'] === '1') // Jika Kepala Keluarga
		{
			$data['kk'] = $this->keluarga_model->list_anggota($data['penduduk']['id_kk']);
		}

		$data['desa'] = $this->config_model->get_data();

		$this->load->view('web/mandiri/layout.mandiri.php', $data);
	}

	public function cetak_biodata($id='')
	{
		// Hanya boleh mencetak data pengguna yang login
		$id = $_SESSION['id'];

		$data['desa'] = $this->config_model->get_data();
		$data['penduduk'] = $this->penduduk_model->get_penduduk($id);
		$this->load->view('sid/kependudukan/cetak_biodata', $data);
	}

	public function cetak_kk($id='')
	{
		// Hanya boleh mencetak data pengguna yang login
		$id = $_SESSION['id'];

		// $id adalah id penduduk. Cari id_kk dulu
		$id_kk = $this->penduduk_model->get_id_kk($id);
		$data = $this->keluarga_model->get_data_cetak_kk($id_kk);

		$this->load->view("sid/kependudukan/cetak_kk_all", $data);
	}

	public function kartu_peserta($aksi = 'tampil', $id = 0)
	{
		$data = $this->program_bantuan_model->get_program_peserta_by_id($id);
		// Hanya boleh menampilkan data pengguna yang login
		// ** Bagi program sasaran pendududk **
		// TO DO : Ganti parameter nik menjadi id
		if ($data['peserta'] == $_SESSION['nik'])
		{
			if ($aksi == 'tampil')
			{
				$this->load->view('web/mandiri/data_peserta', $data);
			}
			else
			{
				$this->load->helper('download');
				if ($data['kartu_peserta']) force_download(LOKASI_DOKUMEN . $data['kartu_peserta'], NULL);

				redirect('mandiri_web/mandiri/1/4');
			}
		}
	}

	public function cek_syarat()
	{
		$id_permohonan = $this->input->post('id_permohonan');
		$permohonan = $this->db->where('id', $id_permohonan)
			->get('permohonan_surat')
			->row_array();
		$syarat_permohonan = json_decode($permohonan['syarat'], true);
		$dokumen = $this->penduduk_model->list_dokumen($_SESSION['id']);
		$id = $this->input->post('id_surat');
		$syarat_surat = $this->surat_master_model->get_syarat_surat($id);
		$data = array();
		$no = $_POST['start'];

		foreach ($syarat_surat as $no_syarat => $baris)
		{
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $baris['ref_syarat_nama'];
			// Gunakan view sebagai string untuk mempermudah pembuatan pilihan
			$pilihan_dokumen = $this->load->view('web/mandiri/pilihan_syarat.php', array('dokumen' => $dokumen, 'syarat_permohonan' => $syarat_permohonan, 'syarat_id' => $baris['ref_syarat_id']), TRUE);
			$row[] = $pilihan_dokumen;
			$data[] = $row;
		}

		$output = array(
			"recordsTotal" => 10,
			"recordsFiltered" => 10,
			'data' => $data
		);
		echo json_encode($output);
	}

	public function ajax_table_surat_permohonan()
	{
		$data = $this->penduduk_model->list_dokumen($_SESSION['id']);
		for ($i=0; $i < count($data); $i++)
		{
			$berkas = $data[$i]['satuan'];
			$list_dokumen[$i][] = $data[$i]['no'];
			$list_dokumen[$i][] = $data[$i]['id'];
			$list_dokumen[$i][] = "<a href='".site_url("mandiri_web/unduh_berkas/".$data[$i][id])."/{$data[$i][id_pend]}"."'>".$data[$i]["nama"].'</a>';
			$list_dokumen[$i][] = tgl_indo2($data[$i]['tgl_upload']);
			$list_dokumen[$i][] = $data[$i]['nama'];
			$list_dokumen[$i][] = $data[$i]['dok_warga'];
		}
		$list['data'] = count($list_dokumen) > 0 ? $list_dokumen : array();
		echo json_encode($list);
	}

	public function ajax_upload_dokumen_pendukung()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('nama', 'Nama Dokumen', 'required');

		if ($this->form_validation->run() !== true)
		{
			$data['success'] = -1;
			$data['message'] = validation_errors();
			echo json_encode($data);
			return;
		}

		$this->session->unset_userdata('success');
		$this->session->unset_userdata('error_msg');
		$success_msg = 'Berhasil menyimpan data';

		if ($_SESSION['id'])
		{
			$_POST['id_pend'] = $this->session->id;
			$id_dokumen = $this->input->post('id');
			unset($_POST['id']);

			if ($id_dokumen)
			{
				$hasil = $this->web_dokumen_model->update($id_dokumen, $this->session->id, $mandiri = true);
				if (!$hasil)
				{
					$data['success'] = -1;
					$data['message'] = 'Gagal update';
				}
			}
			else
			{
				$_POST['dok_warga'] = 1; // Boleh diubah di layanan mandiri
				$this->web_dokumen_model->insert($mandiri = true);
			}
			$data['success'] = $this->session->success;
			$data['message'] = $data['success'] == -1 ? $this->session->error_msg : $success_msg;
		}
		else
		{
			$data['success'] = -1;
			$data['message'] = 'Anda tidak mempunyai hak akses itu';
		}

		echo json_encode($data);
	}

	public function ajax_get_dokumen_pendukung()
	{
		$id_dokumen = $this->input->post('id_dokumen');
		$data = $this->web_dokumen_model->get_dokumen($id_dokumen, $this->session->id);

		$data['anggota'] = $this->web_dokumen_model->get_dokumen_di_anggota_lain($id_dokumen);

		if (empty($data))
		{
			$data['success'] = -1;
			$data['message'] = 'Tidak ditemukan';
		}
		elseif ($this->session->id != $data['id_pend'])
		{
			$data = ['message' => 'Anda tidak mempunyai hak akses itu'];
		}
		echo json_encode($data);
	}

	public function ajax_hapus_dokumen_pendukung()
	{
		$id_dokumen = $this->input->post('id_dokumen');
		$data = $this->web_dokumen_model->get_dokumen($id_dokumen);
		if (empty($data))
		{
			$data['success'] = -1;
			$data['message'] = 'Tidak ditemukan';
		}
		elseif ($_SESSION['id'] != $data['id_pend'])
		{
			$data['success'] = -1;
			$data['message'] = 'Anda tidak mempunyai hak akses itu';
		}
		else
		{
			$this->web_dokumen_model->delete($id_dokumen);
			$data['success'] = $this->session->userdata('success') ? : '1';
		}
		echo json_encode($data);
	}

  /**
	 * Unduh berkas berdasarkan kolom dokumen.id
	 * @param   integer  $id_dokumen  Id berkas pada koloam dokumen.id
	 * @return  void
	 */
	public function unduh_berkas($id_dokumen, $id_pend)
	{
		// Ambil nama berkas dari database
		$berkas = $this->web_dokumen_model->get_nama_berkas($id_dokumen, $id_pend);
		if ($berkas)
			ambilBerkas($berkas, NULL, NULL, LOKASI_DOKUMEN);
		else
			$this->output->set_status_header('404');
	}
}
