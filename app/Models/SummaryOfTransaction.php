<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryOfTransaction extends Model
{
    use HasFactory;

    protected $table = 'summary_of_transactions';

    protected $fillable = [
        'transaction_id',
        'commission',
        'gross_commission',
        'net_commission',
        'vatable',
    ];
}
