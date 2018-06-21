@if (count($errors) > 0)
    @foreach($errors->all as $error)
        <div class="alart alart-warnig">{{ $error }}</div>
    @endforeach
@endif