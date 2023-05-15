<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeTax extends Model
{
    protected $fillable = [
		'employee_id',
		'nama',
		'nik',
		'tempat_lahir',
		'tanggal_lahir',
		'jenis_kelamin',
		'jalan',
		'rtrw',
		'kelurahan',
		'kecamatan',
		'kota',
		'status_perkawinan',
		'istri',
		'anak1',
		'anak2',
		'anak3',
		'npwp_kepemilikan',
		'npwp_status',
		'npwp_nama',
		'npwp_nomor',
		'npwp_alamat',
		'npwp_file',
		'status',
		'npwp_change_status',
		'npwp_nama_change',
		'npwp_nomor_change',
		'npwp_alamat_change',
		'created_by'
	];
}
