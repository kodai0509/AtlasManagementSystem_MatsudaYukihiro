<x-sidebar>
  <div class="vh-100 pt-5" style="background:#ECF1F6;">
    <div class="border w-75 m-auto pt-5 pb-5" style="border-radius:5px; background:#FFF;">
      <div class="w-75 m-auto border" style="border-radius:5px;">
        <p class="text-center">{{ $calendar->getTitle() }}</p>
        <div class="">
          {!! $calendar->render() !!}
        </div>
      </div>
      <div class="text-right w-75 m-auto">
        <input type="submit" class="btn btn-primary" value="予約する" form="reserveParts">
      </div>
    </div>
  </div>

  <!-- キャンセル確認モーダル -->
  <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cancelModalLabel">予約キャンセル確認</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
        </div>
        <div class="modal-body">
          <p>予約日: <span id="modalReserveDate"></span></p>
          <p>予約時間: <span id="modalReservePart"></span></p>
          <p>本当にキャンセルしますか？</p>
        </div>
        <div class="modal-footer">
          <form method="POST" action="{{ route('reserve.cancel') }}">
            @csrf
            <input type="hidden" name="reserve_date" id="inputReserveDate" value="">
            <input type="hidden" name="reserve_part" id="inputReservePart" value="">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
            <button type="submit" class="btn btn-danger">キャンセル</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.querySelectorAll('.cancel-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const date = this.getAttribute('data-reserve-date');
        const part = this.getAttribute('data-reserve-part');
        document.getElementById('modalReserveDate').textContent = date;
        document.getElementById('modalReservePart').textContent = part;
        document.getElementById('inputReserveDate').value = date;
        document.getElementById('inputReservePart').value = part;

        // BootstrapのModalをJSで開く（もし自動で開かない場合）
        const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
        modal.show();
      });
    });
  </script>
</x-sidebar>
