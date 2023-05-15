<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sakurentsu extends Model
{
	use SoftDeletes;

	protected $table = "sakurentsus";

	protected $fillable = [
		'sakurentsu_number', 'title_jp', 'title', 'applicant','file','upload_date','target_date','file_translate','translator','translate_date','category', 'send_status','pic','status','position','remark','additional_file','notes','delete_note','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
