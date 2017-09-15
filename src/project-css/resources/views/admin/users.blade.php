@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('users') }}">User(s)</a></h2>
      <p>Create, view and manage users here.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Display controls and cards here -->
<div class="usrWrapper">
  <div class="container">

    <!-- Header Cards -->
    <a href="{{ url('/register') }}">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="colHeadCard">
          <div class="icon hidden-xs hidden-sm">
            <span class="glyphicon glyphicon-plus"></span>
          </div>
          <h4>Create User(s)</h4>
        </div>
      </div>
    </a>

    @foreach($usrs as $usr)

      @if($usr->isAdmin)
      <!-- Checks if the admin is not youz -->
      @if(!($AuthUsr->id==$usr->id))
      <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="colCard admn">

          <!-- Display the user name -->
          <div class="col-xs-6 col-sm-4 col-md-4">
            <p class="colCardName">{{$usr->name}}</p>
          </div>
          <!-- Options for the users -->
          <div class="col-xs-6 col-sm-8 col-md-8">
            <!-- Option 1 Add tables -->
            <div class="colCardOpts">
              @if($usr->hasAccess)
              <a href="#" data-toggle="modal" data-target="#rstrctAccsCllctn{{$usr->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-eye-close"></span>
                </div>
                <p>Restrict Access</p>
              </a>
              @else
              <a href="#" data-toggle="modal" data-target="#allwAccsCllctn{{$usr->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-eye-open"></span>
                </div>
                <p>Allow Access</p>
              </a>
              @endif
            </div>
            <!-- Option 2 Remove admin -->
            <div class="colCardOpts">
              <a href="#" data-toggle="modal" data-target="#removeAdmin{{$usr->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-thumbs-down"></span>
                </div>
                <p>Remove Admin</p>
              </a>
            </div>
            
          </div>

        </div>

      </div>

      <!-- Modals -->
      <!-- Restrict Access to Collection -->
      <div id="allwAccsCllctn{{$usr->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title">Allow Access to User</h3>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to Allow access to <b>{{$usr->name}}</b> user?
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('user/allow') }}">
                  {{ csrf_field() }}

                  <input id="id" name="id" type="hidden" value="{{$usr->id}}" />

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <div class="modal-footer">
                      <div class="col-md-offset-8 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Confirm
                            </button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">
                              Close
                            </button>
                        </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>

        </div>
      </div>

      <div id="rstrctAccsCllctn{{$usr->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title">Restrict Access to User</h3>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to restrict access to <b>{{$usr->name}}</b> user?
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('user/restrict') }}">
                  {{ csrf_field() }}

                  <input id="id" name="id" type="hidden" value="{{$usr->id}}" />

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <div class="modal-footer">
                      <div class="col-md-offset-8 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Confirm
                            </button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">
                              Close
                            </button>
                        </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>

        </div>
      </div>

      <!-- Remove Admin Status-->
      <div id="removeAdmin{{$usr->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title">Remove Admin</h3>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to remove <b>{{$usr->name}}</b> as admin? Please enter user name below to confirm.
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('user/demote') }}">
                  {{ csrf_field() }}

                  <input id="id" name="id" type="hidden" value="{{$usr->id}}" />

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                      <div class="col-md-6">
                          <input id="name" type="text" class="form-control" name="name" required autofocus>
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary">
                              Confirm
                          </button>
                      </div>
                  </div>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      @endif

      @endif

    @endforeach

    @foreach($usrs as $usr)

      @if(!($usr->isAdmin))

      <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="colCard usr">

          <!-- Display the user name -->
          <div class="col-xs-6 col-sm-4 col-md-4">
            <p class="colCardName">{{$usr->name}}</p>
          </div>
          <!-- Options for the users -->
          <div class="col-xs-6 col-sm-8 col-md-8">
            <!-- Option 1 Add tables -->
            <div class="colCardOpts">
              @if($usr->hasAccess)
              <a href="#" data-toggle="modal" data-target="#rstrctAccsCllctn{{$usr->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-eye-close"></span>
                </div>
                <p>Restrict Access</p>
              </a>
              @else
              <a href="#" data-toggle="modal" data-target="#allwAccsCllctn{{$usr->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-eye-open"></span>
                </div>
                <p>Allow Access</p>
              </a>
              @endif
            </div>
            <!-- Option 2 -->
            <div class="colCardOpts">
              <a href="#" data-toggle="modal" data-target="#makeAdmin{{$usr->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-thumbs-up"></span>
                </div>
                <p>Make Admin</p>
              </a>
            </div>

          </div>

        </div>

      </div>

      <!-- Modals -->
      <!-- Restrict Access to Collection -->
      <div id="allwAccsCllctn{{$usr->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title">Allow Access to User</h3>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to Allow access to <b>{{$usr->name}}</b> user?
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('user/allow') }}">
                  {{ csrf_field() }}

                  <input id="id" name="id" type="hidden" value="{{$usr->id}}" />

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <div class="modal-footer">
                      <div class="col-md-offset-8 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Confirm
                            </button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">
                              Close
                            </button>
                        </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>

        </div>
      </div>

      <div id="rstrctAccsCllctn{{$usr->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title">Restrict Access to User</h3>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to restrict access to <b>{{$usr->name}}</b> user?
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('user/restrict') }}">
                  {{ csrf_field() }}

                  <input id="id" name="id" type="hidden" value="{{$usr->id}}" />

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <div class="modal-footer">
                      <div class="col-md-offset-8 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Confirm
                            </button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">
                              Close
                            </button>
                        </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>

        </div>
      </div>

      <!-- Make Admin -->
      <div id="makeAdmin{{$usr->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title">Make Admin</h3>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to promote <b>{{$usr->name}}</b> as admin? Please enter user name below to confirm.
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('user/promote') }}">
                  {{ csrf_field() }}

                  <input id="id" name="id" type="hidden" value="{{$usr->id}}" />

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                      <div class="col-md-6">
                          <input id="name" type="text" class="form-control" name="name" required autofocus>
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary">
                              Confirm
                          </button>
                      </div>
                  </div>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      @endif

    @endforeach

  </div>

</div>

@endsection
