<?php
// app/Models/VF/Payment.php

namespace App\Models\VF;

use App\Models\VF\CollectionNature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'vf_payments';

    protected $fillable = [
        'or_number',
        'or_date',
        'agency',
        'fund',
        'payor',
        'franchise_id',
        'collection_items',
        'total_amount',
        'amount_in_words',
        'payment_method',
        'drawee_bank',
        'check_mo_number',
        'check_mo_date',
        'status',
        'remarks',
        'collected_by',
    ];

    protected $casts = [
        'collection_items' => 'array',
        'or_date' => 'date',
        'check_mo_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function collectedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'collected_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Generate next OR number in format: 2026-0001
     */
    public static function nextOrNumber(): string
    {
        $year = now()->year;

        $last = static::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->value('or_number');

        if ($last) {
            $parts = explode('-', $last);
            $seq = (int) end($parts) + 1;
        } else {
            $seq = 1;
        }

        return $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Convert a number to Philippine peso words.
     */
    public static function numberToWords(float $amount): string
    {
        $ones = [
            '',
            'ONE',
            'TWO',
            'THREE',
            'FOUR',
            'FIVE',
            'SIX',
            'SEVEN',
            'EIGHT',
            'NINE',
            'TEN',
            'ELEVEN',
            'TWELVE',
            'THIRTEEN',
            'FOURTEEN',
            'FIFTEEN',
            'SIXTEEN',
            'SEVENTEEN',
            'EIGHTEEN',
            'NINETEEN',
        ];

        $tens = [
            '',
            '',
            'TWENTY',
            'THIRTY',
            'FORTY',
            'FIFTY',
            'SIXTY',
            'SEVENTY',
            'EIGHTY',
            'NINETY',
        ];

        $convert = null;
        $convert = static function (int $n) use ($ones, $tens, &$convert): string {
            if ($n < 20)
                return $ones[$n];
            if ($n < 100)
                return $tens[(int) ($n / 10)] . ($n % 10 ? ' ' . $ones[$n % 10] : '');
            if ($n < 1000)
                return $ones[(int) ($n / 100)] . ' HUNDRED' . ($n % 100 ? ' ' . $convert($n % 100) : '');
            if ($n < 1000000)
                return $convert((int) ($n / 1000)) . ' THOUSAND' . ($n % 1000 ? ' ' . $convert($n % 1000) : '');
            return $convert((int) ($n / 1000000)) . ' MILLION' . ($n % 1000000 ? ' ' . $convert($n % 1000000) : '');
        };

        $pesos = (int) $amount;
        $centavos = (int) round(($amount - $pesos) * 100);

        $words = $pesos === 0 ? 'ZERO' : $convert($pesos);
        $words .= ' PESOS';
        $words .= $centavos > 0
            ? ' AND ' . $convert($centavos) . ' CENTAVOS'
            : ' ONLY';

        return $words;
    }
}