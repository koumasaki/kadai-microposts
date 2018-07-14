<ul class="media-list">
@foreach ($microposts as $micropost)
    <!--一投稿データの中にuser情報を持ってくるため（micropostデータ自体はuser_idしか持っていない）-->
    <?php $user = $micropost->user; ?>
    <li class="media">
        <div class="media-left">
            <img class="media-object img-rounded" src="{{ Gravatar::src($user->email, 50) }}" alt="">
        </div>
        <div class="media-body">
            <div>
                {!! link_to_route('users.show', $user->name, ['id' => $user->id]) !!} <span class="text-muted">posseted at {{ $micropost->created_at }}</span>
            </div>
            <div>
                <p>{!! nl2br(e($micropost->content)) !!}</p>
            </div>
            <div class="pull-left">
                @if (Auth::user()->is_favoritting($micropost->id))
                    {!! Form::open(['route' => ['user.unfavorite', $micropost->id], 'method' => 'delete']) !!}
                        {!! Form::submit('Unfavorite', ['class' => "btn btn-warnig btn-xs"]) !!}
                    {!! Form::close() !!}
                @else
                    {!! Form::open(['route' => ['user.favorite', $micropost->id]]) !!}
                        {!! Form::submit('Favorite', ['class' => "btn btn-success btn-xs"]) !!}
                    {!! Form::close() !!}
                @endif
            </div>
            <div class="pull-left">
                @if (Auth::id() == $micropost->user_id)
                    {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) !!}
                    {!! Form::close() !!}
                @endif
            </div>
        </div>
    </li>
@endforeach
</ul>
{!! $microposts->render() !!}