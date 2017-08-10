@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="/profiles/{{$thread->creator->name}}">{{$thread->creator->name}}</a>
                        posted: {{$thread->title}}
                        @can ('update', $thread)
                        <form action="{{ $thread->path() }}" method="post">
                            {{csrf_field()}}
                            {{method_field('DELETE')}}
                            <button type="submit" class="btn btn-primary">Delete Thread</button>
                        </form>
                        @endcan
                    </div>
                    <div class="panel-body">
                         <div class="body">{{ $thread->body }}</div>
                    </div>
                </div>
                @foreach($replies as $reply)
                    @include('threads.reply')
                @endforeach
                {{$replies->links()}}
                @if (auth()->check())
                    <form method="post" action="{{$thread->path().'/replies'}}">
                        {{csrf_field()}}
                        <div class="form-group">
                            <textarea name="body" id="body" class="form-control" placeholder="Share your thoughts here." rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                @else
                    <p style="text-align:center">Please <a href="{{route('login')}}">sign in</a> to participate in this discussion.</p>
                @endif
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>This thread was published {{$thread->created_at->diffForHumans()}}
                           by <a href="#">{{$thread->creator->name}}</a>, and currently has {{$thread->replies_count}} {{str_plural('comment', $thread->replies_count)}}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
