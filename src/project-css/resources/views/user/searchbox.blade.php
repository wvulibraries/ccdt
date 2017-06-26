<!-- Search engine -->
<div class="searchBarWrapper">
  <div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
          <form class="form-horizontal" role="search" method="POST" action="{{ url('/data', $tblId) }}">
              {{ csrf_field() }}
              <div class="form-group{{ $errors->has('search') ? ' has-error' : '' }}">
                  <div class="col-md-10 col-sm-10 col-xs-8">

                    <!-- <label for="search">Search</label> -->
                    <input id="search" type="text" class="form-control searchBar" name="search" placeholder="Search..." required autofocus>

                    @if ($errors->has('search'))
                      <span class="fa fa-search">
                          <strong>{{ $errors->first('search') }}</strong>
                      </span>
                    @endif
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-4">
                      <button type="submit searchButton" class="btn btn-primary">
                          Search
                      </button>
                  </div>
              </div>
          </form>
        </div>
    </div>
  </div>
</div>
