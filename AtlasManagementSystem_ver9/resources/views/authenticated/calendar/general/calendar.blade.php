<x-sidebar>
  <div class="vh-100 pt-5" style="background:#ECF1F6;">
    <form id="reserveParts" action="{{ route('reserveParts') }}" method="POST">
      @csrf
      <div class="border w-75 m-auto pt-5 pb-5" style="border-radius:5px; background:#FFF;">
        <div class="w-75 m-auto border" style="border-radius:5px;">
          <p class="text-center">{{ $calendar->getTitle() }}</p>
          <div>
            {!! $calendar->render() !!}
          </div>
        </div>
        <div class="text-right w-75 m-auto">
          <input type="submit" class="btn btn-primary" value="予約する">
        </div>
      </div>
    </form>

    <!-- キャンセル確認モーダル -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelModalLabel">予約キャンセル確認</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>以下の予約をキャンセルしますか？</p>
            <div class="alert alert-info">
              <p><strong>予約日:</strong> <span id="cancel-date"></span></p>
              <p><strong>予約時間:</strong> <span id="cancel-part"></span></p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">戻る</button>
            <form id="cancelForm" action="{{ route('reserve.cancel') }}" method="POST" style="display: inline;">
              @csrf
              <input type="hidden" name="reserve_id" id="cancel-reserve-id">
              <button type="submit" class="btn btn-danger">キャンセルする</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // キャンセルボタンがクリックされた時の処理
    document.addEventListener('DOMContentLoaded', function() {
      const cancelButtons = document.querySelectorAll('.cancel-btn');

      cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
          const reserveDate = this.getAttribute('data-reserve-date');
          const reservePart = this.getAttribute('data-reserve-part');
          const reserveId = this.getAttribute('data-reserve-id');

          // モーダルに値を設定
          document.getElementById('cancel-date').textContent = reserveDate;
          document.getElementById('cancel-part').textContent = reservePart;
          document.getElementById('cancel-reserve-id').value = reserveId;
        });
      });
    });
  </script>
</x-sidebar>
