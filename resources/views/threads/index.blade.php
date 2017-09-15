@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
              @include('threads.list')
              {{$threads->render()}}
            </div>
            <div class="col-md-4">
              @if(count($trending_threads))
                <div class="panel panel-default">
                   <div class="panel-heading">
                       Trending Threads
                   </div>
                   <div class="panel-body">
                     <ul class="list-group">
                       @foreach($trending_threads as $trending)
                           <li class="list-group-item"><a href="{{url($trending->path)}}">{{ $trending->title }}</a></li>
                       @endforeach
                     </ul>
                   </div>
                </div>
               @endif
            </div>
        </div>
    </div>
@endsection
