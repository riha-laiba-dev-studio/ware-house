@extends('layouts.app')

@section('content')
<h2>Add Unit</h2>

<form method="POST" action="{{ route('units.store') }}">
    @csrf

    <input type="text" name="name" placeholder="Unit Name" required>

    <input type="text" name="symbol" placeholder="Symbol (e.g. kg, pcs, bag)" required>

    <button type="submit">Save</button>
</form>
@endsection