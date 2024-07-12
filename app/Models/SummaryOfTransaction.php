<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryOfTransaction extends Model
{
    use HasFactory;

    protected $table = 'summary_of_transactions';

    protected $fillable = [
        'Type',
        'Name',
        'Description',
        'commission',
        'gross_commission',
        'net_commission',
        'vatable',
        'total_gross_commission_vatable',
        'total_net_commission_vatable',
        'total_gross_commission_non_vatable',
        'total_net_commission_non_vatable',
    ];
}