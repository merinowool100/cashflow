<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ledgers = Ledger::with('user')
            ->where('user_id', Auth::id())
            ->orderBy('date', 'asc')
            ->get();
        return view('ledgers.index', compact('ledgers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ledgers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'date' => 'required|date',
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'repeat_monthly' => 'nullable|boolean',  // 追加
            'repeat_yearly' => 'nullable|boolean',   // 追加
            'end_date' => 'nullable|date', // end_dateは繰り返しの場合にのみ必須
        ]);

        // 入力された日付を取得
        $startDate = Carbon::parse($request->input('date'));
        $endDate = $request->has('end_date') ? Carbon::parse($request->input('end_date')) : null;

        // 繰り返しの単位を確認（月次/年次）
        $repeatMonthly = $request->has('repeat_monthly');
        $repeatYearly = $request->has('repeat_yearly');

        // itemとamountを別の変数に保存
        $item = $request->input('item');
        $amount = $request->input('amount');

        // 新しいgroupIDを生成（繰り返しレコード群に共通のIDを付与）
        $groupID = $repeatMonthly || $repeatYearly ? uniqid('group_') : null;

        // 毎月または毎年繰り返しレコードを作成
        if (($repeatMonthly || $repeatYearly) && $endDate) {
            if ($repeatMonthly && !$repeatYearly) {
                // 毎月繰り返し
                while ($startDate <= $endDate) {
                    $this->createLedger($startDate, $item, $amount, $groupID, Auth::id(), $repeatMonthly, $repeatYearly, $endDate);
                    $startDate->addMonth(); // 1ヶ月後に進める
                }
            } elseif ($repeatYearly && !$repeatMonthly) {
                // 毎年繰り返し
                while ($startDate <= $endDate) {
                    $this->createLedger($startDate, $item, $amount, $groupID, Auth::id(), $repeatMonthly, $repeatYearly, $endDate);
                    $startDate->addYear(); // 1年後に進める
                }
            }
        } else {
            // 繰り返しがない場合は1つのレコードを作成
            $this->createLedger($startDate, $item, $amount, $groupID, Auth::id(), $repeatMonthly, $repeatYearly, $endDate);
        }

        return redirect()->route('ledgers.index')->with('success', 'Ledger records created successfully.');
    }

    // レコード作成処理
    private function createLedger($date, $item, $amount, $groupID, $userId, $repeatMonthly, $repeatYearly, $endDate)
    {
        // 繰り返しがない場合もgroup_idを生成して保存
        $ledger = new Ledger();
        $ledger->user_id = $userId;
        $ledger->date = $date->toDateString();
        $ledger->item = $item;
        $ledger->amount = $amount;
        $ledger->group_id = $groupID;
        $ledger->repeat_monthly = $repeatMonthly; // 繰り返し設定を追加
        $ledger->repeat_yearly = $repeatYearly;   // 繰り返し設定を追加
        $ledger->end_date = $endDate ? $endDate->toDateString() : null;
        $ledger->save();
    }

    public function show(Ledger $ledger)
    {
        return view('ledgers.show', compact('ledger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ledger $ledger)
    {
        return view('ledgers.edit', compact('ledger'));
    }

    public function update(Request $request, Ledger $ledger)
    {
        // バリデーション
        $request->validate([
            'date' => 'required|date',
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'repeat_monthly' => 'nullable|boolean',
            'repeat_yearly' => 'nullable|boolean',
            'end_date' => 'nullable|date',
        ]);

        // 編集方法の選択（チェックボックス）
        $applyToLaterDates = $request->has('apply_to_later_dates'); // チェックボックスがチェックされているか

        // 更新アクション
        if ($request->input('action') == 'update') {
            // 更新処理
            if ($applyToLaterDates && $ledger->group_id !== null) {
                // 同じgroup_idを持つ、かつ当該データ日付以降のデータを更新
                Ledger::where('user_id', Auth::id())
                    ->where('group_id', $ledger->group_id)
                    ->where('date', '>=', $ledger->date)
                    ->update([
                        'date' => $request->date,
                        'item' => $request->item,
                        'amount' => $request->amount,
                        'repeat_monthly' => $request->repeat_monthly,
                        'repeat_yearly' => $request->repeat_yearly,
                        'end_date' => $request->end_date,
                    ]);
            } else {
                // 当該データのみ編集
                $ledger->date = $request->date;
                $ledger->item = $request->item;
                $ledger->amount = $request->amount;
                $ledger->repeat_monthly = $request->repeat_monthly;
                $ledger->repeat_yearly = $request->repeat_yearly;
                $ledger->end_date = $request->end_date;
                $ledger->save();
            }
        }

        // 削除アクション
        if ($request->input('action') == 'delete') {
            if ($applyToLaterDates && $ledger->group_id !== null) {
                // 同じgroup_idを持つ、かつ当該データ日付以降のデータを削除
                Ledger::where('user_id', Auth::id())
                    ->where('group_id', $ledger->group_id)
                    ->where('date', '>=', $ledger->date)
                    ->delete();
            } else {
                // 当該データのみ削除
                $ledger->delete();
            }
        }

        return redirect()->route('ledgers.index')->with('success', 'Record updated successfully.');
    }
}
