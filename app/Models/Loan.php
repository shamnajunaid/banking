<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $hidden = ['created_at','updated_at','status'];

    protected $appends = ['paid_amount','Loan_status'];

    public function schedules()
    {
        return $this->hasMany(Loan_schedule::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //To get total amount paid in each schedule
    public function getPaidAmountAttribute()
	{
	return $this->schedules->sum('paid_amount');
	}
	public function getLoanStatusAttribute()
    {
        if($this->status==0){
        	return "Pending";
        }elseif($this->status==1){
        	return "Approved";
        }else{
        	return "Completed";
        }
    }
}
