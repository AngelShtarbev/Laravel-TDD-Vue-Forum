<reply :attributes="{{$reply}}" inline-template v-cloak>
    <div id="reply-{{$reply->id}}" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h4 class="flex"><a href="/profiles/{{$reply->owner->name}}">{{$reply->owner->name}}</a> said {{$reply->created_at->diffForHumans()}}</h4>
                @if(Auth::check())
                    <div>
                        <favorite :reply="{{$reply}}"></favorite>
                    </div>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <div v-if="editing" class="body">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>
                <button class="btn btn-primary" @click="update">Update</button>
                <button class="btn btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body" class="body"></div>
        </div>
        @can('update',$reply)
        <div class="panel-footer">
            <button class="btn btn-primary" @click="editing = true">Edit</button>
        </div>
        <div class="panel-footer">
            <button class="btn btn-danger" @click="destroy">Delete</button>
        </div>
        @endcan
    </div>
</reply>