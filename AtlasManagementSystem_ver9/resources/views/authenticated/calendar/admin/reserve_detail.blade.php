<x-sidebar>
  <div class="d-flex">
    <div class="w-50 m-auto h-75">
      <p><span>{{ $date }}</span><span class="ml-3">{{ $part }}部</span></p>
      <div class="h-75">
        <table class="table table-bordered text-center reserve-table">
          <thead>
            <tr>
              <th class="w-25">ID</th>
              <th class="w-25">名前</th>
              <th class="w-25">予約部</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->over_name }} {{ $user->under_name }}</td>
              <td>{{ $part }}部</td>
            </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-sidebar>
