<x-sidebar>
  <div class="w-100 vh-100 d-flex" style="align-items:center; justify-content:center;">
    <div class="w-100 vh-100 border p-5">
      <p class="text-center">{{ $calendar->getTitle() }}</p>
      {!! $calendar->render() !!}
      <div class="adjust-table-btn m-auto text-right">
        <input type="submit" class="btn btn-primary" value="登録" form="reserveSetting" onclick="return confirm('登録してよろしいですか？')">
      </div>
    </div>
  </div>
</x-sidebar>
