<script>
	$(function() {
		var keyword = <?= $keyword != '' ? $keyword : '""' ?> ;
		$("#cari").autocomplete( {
			source: keyword,
			maxShowItems: 10,
		});
	});
</script>
<?php $detail = $program[0]; ?>
<div class="content-wrapper">
	<section class="content-header">
		<h1>Program Bantuan <?= $detail['nama']; ?></h1>
		<ol class="breadcrumb">
			<li><a href="<?= site_url('hom_sid')?>"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="<?= site_url('program_bantuan')?>"> Daftar Program Bantuan</a></li>
			<li class="active">Program Bantuan <?= $detail['nama']; ?></li>
		</ol>
	</section>
	<section class="content" id="maincontent">
		<div class="box box-info">
			<div class="box-header with-border">
				<?php if ($detail["status"] == 1): ?>
					<div class="btn-group btn-group-vertical">
						<a class="btn btn-social btn-flat btn-success btn-sm" data-toggle="dropdown"><i class='fa fa-plus'></i> Tambah Peserta Baru</a>
						<ul class="dropdown-menu" role="menu">
							<li>
								<a href="<?= site_url("program_bantuan/aksi/1/".$detail['id'])?>" class="btn btn-social btn-flat btn-block btn-sm" title="Tambah Satu Peserta Baru "><i class="fa fa-plus"></i> Tambah Satu Peserta Baru</a>
							</li>
							<li>
								<a href="<?= site_url("program_bantuan/aksi/2/".$detail['id'])?>" class="btn btn-social btn-flat btn-block btn-sm" title="Tambah Beberapa Peserta Baru"><i class="fa fa-plus"></i> Tambah Beberapa Peserta Baru</a>
							</li>
						</ul>
					</div>
				<?php endif; ?>
				<a href="#confirm-delete" title="Hapus Data Terpilih" onclick="deleteAllBox('mainform', '<?=site_url("program_bantuan/delete_all/$detail[id]")?>')" class="btn btn-social btn-flat btn-danger btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block hapus-terpilih"><i class='fa fa-trash-o'></i> Hapus Data Terpilih</a>
				<a href="<?= site_url("program_bantuan/daftar/$detail[id]/cetak")?>" class="btn btn-social btn-flat bg-purple btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Cetak" target="_blank"><i class="fa fa-print"></i> Cetak
				</a>
				<a href="<?= site_url("program_bantuan/daftar/$detail[id]/unduh")?>" class="btn btn-social btn-flat bg-navy btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Unduh" target="_blank"><i class="fa fa-download"></i> Unduh
				</a>
				<a href="<?= site_url('program_bantuan')?>" class="btn btn-social btn-flat btn-info btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Kembali Ke Daftar Program Bantuan"><i class="fa fa-arrow-circle-o-left"></i> Kembali Ke Daftar Program Bantuan
				</a>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-sm-12">
						<input type="hidden" id="program_id" name="program_id" value="<?= $detail['id']?>">
						<?php include('donjo-app/views/program_bantuan/rincian.php'); ?>
						<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
							<div class="row">
								<div class="col-sm-9">
									<h5><b>Daftar Peserta</b></h5>
								</div>
								<div class="col-sm-3">
									<form id="mainform" name="mainform" action="" method="post">
										<div class="input-group input-group-sm pull-right with-border">
											<input name="cari" id="cari" class="form-control" placeholder="Cari..." type="text" value="<?=html_escape($cari)?>" onkeypress="if (event.keyCode == 13){$('#'+'mainform').attr('action', '<?=site_url("program_bantuan/search/$detail[id]")?>');$('#'+'mainform').submit();}">
											<div class="input-group-btn">
												<button type="submit" class="btn btn-default" onclick="$('#'+'mainform').attr('action', '<?=site_url("program_bantuan/search/$detail[id]")?>');$('#'+'mainform').submit();"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</div>
								<div class="col-sm-12">
									<?php $peserta = $program[1];?>
									<div class="table-responsive">
										<table class="table table-bordered table-striped dataTable table-hover tabel-daftar">
											<thead class="bg-gray disabled color-palette">
												<tr>
													<th rowspan="2" class="padat"><input type="checkbox" id="checkall"/></th>
													<th rowspan="2" class="padat">No</th>
													<th rowspan="2" class="padat">Aksi</th>
													<th rowspan="2" nowrap><?= $detail["judul_peserta"]?></th>
													<?php if (!empty($detail['judul_peserta_plus'])): ?>
														<th rowspan="2" nowrap class="text-center"><?= $detail["judul_peserta_plus"]?></th>
													<?php endif ;?>
													<th rowspan="2" nowrap><?= $detail["judul_peserta_info"]?></th>
													<th colspan="7">Identitas di Kartu Peserta</th>
												</tr>
												<tr>
													<th rowspan="2" class="padat">No. Kartu Peserta</th>
													<th>NIK</th>
													<th>Nama</th>
													<th>Tempat Lahir</th>
													<th>Tanggal Lahir</th>
													<th>Jenis Kelamin</th>
													<th>Alamat</th>
												</tr>
											</thead>
											<tbody>
												<?php if (is_array($peserta)): ?>
													<?php foreach ($peserta as $key => $item): ?>
														<tr>
															<td class="padat"><input type="checkbox" name="id_cb[]" value="<?= $item['id']?>" /></td>
															<td class="padat"><?= ($key + $paging->offset + 1); ?></td>
															<td class="padat">
																<a href="<?= site_url("program_bantuan/edit_peserta_form/$item[id]")?>" class="btn bg-orange btn-flat btn-sm" title="Ubah" data-remote="false" data-toggle="modal" data-target="#modalBox" data-title="Ubah Data Peserta"><i class="fa fa-edit"></i></a>
																<a href="#" data-href="<?= site_url("program_bantuan/hapus_peserta/$detail[id]/$item[id]")?>" class="btn bg-maroon btn-flat btn-sm" title="Hapus" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i></a>
															</td>
															<?php $id_peserta = ($detail['sasaran'] == 4) ? $item['peserta'] : $item['nik'] ?>
															<td nowrap><a href="<?= site_url("program_bantuan/peserta/$detail[sasaran]/$id_peserta")?>" title="Daftar program untuk peserta"><?= $item["peserta_nama"] ?></a></td>
															<?php if (!empty($item['peserta_plus'])): ?>
																<td nowrap><?= $item["peserta_plus"]?></td>
															<?php endif; ?>
															<td nowrap><?= $item["peserta_info"]?></td>
															<td nowrap class="padat"><a href="<?= site_url("program_bantuan/data_peserta/$item[id]")?>" title="Data peserta"><?= $item['no_id_kartu'];?></a></td>
															<td nowrap><?= $item["kartu_nik"];?></td>
															<td nowrap><?= $item["kartu_nama"];?></td>
															<td nowrap><?= $item["kartu_tempat_lahir"];?></td>
															<td nowrap><?= tgl_indo_out($item["kartu_tanggal_lahir"]);?></td>
															<td nowrap><?= $item["sex"];?></td>
															<td nowrap><?= $item["kartu_alamat"];?></td>
														</tr>
													<?php endforeach; ?>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
									<?php $this->load->view('global/paging');?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php $this->load->view('global/confirm_delete');?>
