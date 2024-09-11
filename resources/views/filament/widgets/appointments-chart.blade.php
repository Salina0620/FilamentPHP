@extends('filament::layouts.base')

@section('content')
<div class="chart-container" style="position: relative; height: 50vh; width: 100%;">
    {!! $this->chart !!}
</div>
@endsection
