<x-app-layout>

  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Ledger List') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

          <!-- 新しいレコードを作成するボタン -->
          <button id="openModalButton" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow-sm hover:bg-blue-700 focus:outline-none">
            新しいレコードを作成
          </button>

          <!-- 編集・削除用モーダル -->
          <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50" role="dialog" aria-labelledby="modal" aria-hidden="true">
            <div class="bg-white p-6 rounded-md shadow-lg w-96">
              <h2 id="modal-title" class="text-lg font-semibold mb-4">レコードを編集</h2>

              <form id="modalForm" method="POST" action="">
                @csrf
                @method('PUT')

                <!-- 日付 -->
                <div class="mb-4">
                  <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">日付</label>
                  <input type="date" name="date" id="modal_date" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                </div>

                <!-- 項目 -->
                <div class="mb-4">
                  <label for="item" class="block text-sm font-medium text-gray-700 dark:text-gray-300">項目</label>
                  <input type="text" name="item" id="modal_item" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                </div>

                <!-- 金額 -->
                <div class="mb-4">
                  <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">金額</label>
                  <input type="number" name="amount" id="modal_amount" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                </div>

                <!-- 繰り返し設定 -->
                <div class="mb-4">
                  <label for="repeat_monthly" class="inline-flex items-center">
                    <input type="checkbox" name="repeat_monthly" id="modal_repeat_monthly" class="mr-2">
                    Monthly
                  </label>
                  <label for="repeat_yearly" class="inline-flex items-center">
                    <input type="checkbox" name="repeat_yearly" id="modal_repeat_yearly" class="mr-2">
                    Yearly
                  </label>
                </div>

                <!-- 以降の日付に適用するチェックボックス -->
                <div class="mb-4">
                  <label for="apply_future" class="inline-flex items-center">
                    <input type="checkbox" name="apply_future" id="apply_future" class="mr-2">
                    以降の日付のレコードにも適用
                  </label>
                </div>

                <!-- アクションボタン -->
                <div id="modal-action-buttons" class="flex justify-end">
                  <button type="submit" id="modal-submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none">
                    更新
                  </button>
                  <button type="button" id="closeModalButton" class="ml-2 px-4 py-2 bg-gray-600 text-white font-semibold text-sm rounded-md shadow-sm hover:bg-gray-700 focus:outline-none">
                    キャンセル
                  </button>
                </div>
              </form>

              <!-- 削除メッセージ -->
              <div id="modal-delete-message" class="hidden mt-4">
                <p>このレコードを削除してもよろしいですか？</p>
              </div>

              <!-- 削除ボタン -->
              <div id="modal-delete-buttons" class="flex justify-end hidden mt-4">
                <form id="deleteForm" method="POST" action="">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold text-sm rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                    削除
                  </button>
                  <button type="button" id="closeDeleteModalButton" class="ml-2 px-4 py-2 bg-gray-600 text-white font-semibold text-sm rounded-md shadow-sm hover:bg-gray-700 focus:outline-none">
                    キャンセル
                  </button>
                </form>
              </div>
            </div>
          </div>

          <!-- テーブル開始 -->
          <table class="min-w-full bg-white dark:bg-gray-800">
            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">日付</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">項目</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">金額</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">残高</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">操作</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($ledgers as $ledger)
              <tr class="border-b border-gray-200 dark:border-gray-700">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ledger->date }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ledger->item }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-right 
                  @if($ledger->amount < 0) text-red-500
                  @elseif($ledger->amount === null) text-gray-500
                  @endif">
                  {{ number_format($ledger->amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ledger->balance }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button class="text-blue-500 hover:text-blue-700 openModalButton" data-id="{{ $ledger->id }}" data-action="edit" data-date="{{ $ledger->date }}" data-item="{{ $ledger->item }}" data-amount="{{ $ledger->amount }}" data-repeat_monthly="{{ $ledger->repeat_monthly }}" data-repeat_yearly="{{ $ledger->repeat_yearly }}">
                    編集
                  </button>
                  <button class="text-red-500 hover:text-red-700 openModalButton" data-id="{{ $ledger->id }}" data-action="delete">
                    削除
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <!-- テーブル終了 -->
        </div>
      </div>
    </div>
  </div>

  <!-- モーダルを開くためのJS -->
  <script>
    document.querySelectorAll('.openModalButton').forEach(button => {
      button.addEventListener('click', function() {
        const ledgerId = this.getAttribute('data-id');
        const action = this.getAttribute('data-action');

        // モーダルの内容を設定
        if (action === 'edit') {
          // 編集モード
          document.getElementById('modal-title').textContent = 'レコードを編集';
          document.getElementById('modal-submit').textContent = '更新';
          document.getElementById('modalForm').action = `/ledgers/${ledgerId}`;
          document.getElementById('modal_date').value = this.getAttribute('data-date');
          document.getElementById('modal_item').value = this.getAttribute('data-item');
          document.getElementById('modal_amount').value = this.getAttribute('data-amount');
          document.getElementById('modal_repeat_monthly').checked = this.getAttribute('data-repeat_monthly') === '1';
          document.getElementById('modal_repeat_yearly').checked = this.getAttribute('data-repeat_yearly') === '1';
          
          // 削除部分を隠す
          document.getElementById('modal-delete-message').classList.add('hidden');
          document.getElementById('modal-delete-buttons').classList.add('hidden');
        } else if (action === 'delete') {
          // 削除モード
          document.getElementById('modal-title').textContent = 'レコードを削除';
          document.getElementById('modal-submit').textContent = '削除';
          document.getElementById('modalForm').action = `/ledgers/${ledgerId}`;

          // 編集部分を隠す
          document.getElementById('modal-delete-message').classList.remove('hidden');
          document.getElementById('modal-delete-buttons').classList.remove('hidden');
        }

        // モーダルを表示
        document.getElementById('modal').classList.remove('hidden');
      });
    });

    // モーダルを閉じる
    document.getElementById('closeModalButton').addEventListener('click', function() {
      document.getElementById('modal').classList.add('hidden');
    });

    document.getElementById('closeDeleteModalButton').addEventListener('click', function() {
      document.getElementById('modal').classList.add('hidden');
    });

    // モーダル外をクリックして閉じる
    window.addEventListener('click', function(event) {
      if (event.target === document.getElementById('modal')) {
        document.getElementById('modal').classList.add('hidden');
      }
    });
  </script>

</x-app-layout>
