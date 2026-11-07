<a class="city-card" href="{{ $province->url }}">
    <i class="fa-solid fa-location-dot"></i>
    <span>{{ $province->name }}@isset($province->active_districts_count)<small>{{ $province->active_districts_count }} ilçe</small>@endisset</span>
</a>
