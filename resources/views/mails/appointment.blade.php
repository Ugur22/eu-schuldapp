@extends('layouts.mails')

@section('content')
  <h3>{{ $datetime }}</h3>
  <p><em>&quot;{!! nl2br($notes) !!}</em>&quot;</p>
  <h3>{{ $place }}</h3>
@endsection