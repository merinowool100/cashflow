<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Record 編集') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <form method="POST" action="{{ route('ledgers.update', $ledger) }}">
            @csrf
            @method('PUT') <!-- PUTメソッドを使用 -->

            <div class="grid grid-cols-1 gap-6">
              <!-- 日付 -->
              <div>
                <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">日付</label>
                <input type="date" name="date" id="date" value="{{ old('date', $ledger->date) }}" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                @error('date')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
              </div>

              <!-- 項目 -->
              <div>
                <label for="item" class="block text-sm font-medium text-gray-700 dark:text-gray-300">項目</label>
                <input type="text" name="item" id="item" value="{{ old('item', $ledger->item) }}" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                @error('item')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
              </div>

              <!-- 金額 -->
              <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">金額</label>
                <input type="number" name="amount" id="amount" value="{{ old('amount', $ledger->amount) }}" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                @error('amount')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
              </div>

              <!-- 繰り返しオプションが選ばれている場合に表示する -->
              <div id="apply_to_later_dates_group" style="{{ ($ledger->repeat_monthly || $ledger->repeat_yearly) ? 'display:block;' : 'display:none;' }}">
                <div>
                  <label for="apply_to_later_dates" class="inline-flex items-center">
                    <input type="checkbox" name="apply_to_later_dates" id="apply_to_later_dates" {{ old('apply_to_later_dates', $ledger->group_id ? 'checked' : '') }}>
                    以降の日付のデータにも同様に適用
                  </label>
                </div>
              </div>

              <!-- 繰り返しボックス -->
              <div>
                <input type="checkbox" id="repeat_monthly" name="repeat_monthly" value="1" {{ $ledger->repeat_monthly ? 'checked' : '' }} onchange="toggleApplyToLaterDates()"> Monthly
                <input type="checkbox" id="repeat_yearly" name="repeat_yearly" value="1" {{ $ledger->repeat_yearly ? 'checked' : '' }} onchange="toggleApplyToLaterDates()"> Yearly
              </div>

              <!-- 繰り返し終了日フィールド -->
              <div class="mt-4" id="end_date_group" style="{{ ($ledger->repeat_monthly || $ledger->repeat_yearly) ? 'display:block;' : 'display:none;' }}">
                <label for="end_date">Repeat Until</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $ledger->end_date) }}">
              </div>

              <!-- 更新ボタン -->
              <div class="flex justify-end space-x-4 mt-4">
                <button type="submit" name="action" value="update" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                  更新
                </button>

                <!-- 削除ボタン -->
                <button type="submit" name="action" value="delete" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold text-sm rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-700 dark:hover:bg-red-600">
                  削除
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    function toggleApplyToLaterDates() {
      var repeatMonthly = document.getElementById('repeat_monthly').checked;
      var repeatYearly = document.getElementById('repeat_yearly').checked;
      var applyToLaterDatesGroup = document.getElementById('apply_to_later_dates_group');

      if (repeatMonthly || repeatYearly) {
        applyToLaterDatesGroup.style.display = 'block';
      } else {
        applyToLaterDatesGroup.style.display = 'none';
      }

      // 繰り返し終了日フィールドも調整
      var endDateGroup = document.getElementById('end_date_group');
      if (repeatMonthly || repeatYearly) {
        endDateGroup.style.display = 'block';
      } else {
        endDateGroup.style.display = 'none';
      }
    }

    // ページがロードされた時に繰り返しチェックボックスの状態に応じて表示
    window.onload = function() {
      toggleApplyToLaterDates();
    };
  </script>
</x-app-layout>
