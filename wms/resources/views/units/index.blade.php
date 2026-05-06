@extends('layouts.app')

@section('content')
<h2>Units</h2>

<a href="{{ route('units.create') }}">Add Unit</a>

<ul>
@foreach($units as $unit)
    <li>{{ $unit->name }}</li>
@endforeach
</ul>
@endsection