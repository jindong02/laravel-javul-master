<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserWiki extends Model
{
    protected $table = 'userwiki_page';
    protected $fillable = ['page_content','page_title','user_id'];
}
