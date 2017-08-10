<div class="panel panel-default">
    <div class="panel-heading">
        <div class="level">
            <h4 class="flex"><a href="/profiles/{{$reply->owner->name}}">{{$reply->owner->name}}</a> said {{$reply->created_at->diffForHumans()}}</h4>
            <div>
                <form method="post" action="/replies/{{$reply->id}}/favorites">
                    {{csrf_field()}}
                    <button type="submit" class="btn btn-primary" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                        {{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <article>
            <div class="body">{{ $reply->body }}</div>
        </article>
    </div>
</div>