<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    // このモデルで扱うカラムを指定
    protected $fillable = ['date', 'item', 'amount', 'repeat_monthly', 'repeat_yearly', 'end_date', 'group_id', 'balance'];

    // リレーションシップ（userとの関連）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 指定された日付以降のレコードのbalanceを更新
     *
     * @param string|null $startDate
     * @return void
     */
    public static function saveBalance($startDate = null)
    {
        // 指定された日付以降のレコードを取得
        $ledgers = Ledger::where('date', '>=', $startDate)->orderBy('date')->get();

        // 最初のレコードを特定
        $firstLedger = $ledgers->first();

        // 最初のレコードのbalanceからamountを引いた値を初期balanceとして設定
        if ($firstLedger) {
            $balance = ($firstLedger->balance) - ($firstLedger->amount);
        }

        // 各レコードのbalanceを計算
        foreach ($ledgers as $ledger) {
            $balance += $ledger->amount;
            $ledger->balance = $balance;

            // balanceも一緒に保存
            $ledger->save();
        }
    }
}
