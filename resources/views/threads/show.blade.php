@extends('layouts.app')

@section('reply-suggestion')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
    <thread :initial-replies-count="{{$thread->replies_count}}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="25" height="25" class="mr-1">
                        <span class="flex">
                            <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted:{{ $thread->title }}
                        </span>
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
                <replies @added="repliesCount++" @removed="repliesCount--"></replies>
                {{--{{$replies->links()}}--}}

            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>This thread was published {{$thread->created_at->diffForHumans()}}
                           by <a href="#">{{$thread->creator->name}}</a>, and currently has <span v-text="repliesCount"></span> {{str_plural('comment', $thread->replies_count)}}.
                        </p>
                        <p><subscribe :active="{{json_encode($thread->isSubscribed)}}"></subscribe></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </thread>
@endsection
