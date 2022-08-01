<?php
 
namespace Farsh4d\Bank\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class PaymentTransaction extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'psp',
        'price',
        'ref_id',
        'pay_res',
        'created_at',
        'updated_at',
    ];
    
    protected $guarded = ['status'];
}