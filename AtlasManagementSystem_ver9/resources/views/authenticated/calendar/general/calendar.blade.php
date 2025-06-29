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
  </div>
</x-sidebar>
