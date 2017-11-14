<!-- Alerts to show the messages and notifications -->
@if(session()->has('messages'))
  @if(count(session()->get('messages')) > 0)

    @if(count(session()->get('messages')) == 1))
      @foreach (session()->get('messages') as $message)
        <div class="alert alert-{{ $message['level'] }} alrtArea">
          <h4>{{ $message['content'] }}</h4>
        </div>
      @endforeach

    @else
      <div class="alert alert-danger alrtArea" data-toggle="collapse" data-target="#alrtsWrappr">
        <h4>One or more messages exists. Please click to reveal.</h4>
      </div>

      <div id="alrtsWrappr" class="collapse">
        @foreach (session()->get('messages') as $message)
          <div class="alert alert-{{ $message['level'] }} alrtArea">
            <h4>{{ $message['content'] }}</h4>
          </div>
        @endforeach
      </div>

    @endif

  @endif
@endif
