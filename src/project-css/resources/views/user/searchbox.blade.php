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
                    <input id="search" type="search" class="form-control searchBar" name="search" placeholder="Search..."  aria-required="true" aria-label="Search Input" required autofocus>

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

                  <div id="search-options">
                    <label for="search-col"> Search on a Specific Column: </label>
                    <select id="search-col" name="search-col" aria-label="Search on a specific column" aria-required="false">
                      <!-- remove id from list -->
                      @array_shift($clmnNmes);
                      @foreach($clmnNmes as $clmnNme)
                        @if (strpos($clmnNme, 'index') !== false)
                          <option value="{{ $clmnNme }}" selected> All Columns </option>
                        @else
                          <option value="{{ $clmnNme }}">{{ $clmnNme }} </option>
                        @endif
                      @endforeach
                    </select>

                    <label for="search-type"> Type of Search: </label>
                    <select id="search-type" name="driver" aria-label="Type of Search" aria-required="false">
                      <option value="simple">Simple</option>
                      <option value="fuzzy" selected>Fuzzy</option>
                    </select>

                    <div class="cache-check">
                      <label for="cache"> Check Cached Search </label>
                      <input type="checkbox" name="cache" value="true" checked> Use Cache?
                    </div>
                  </div>

              </div>

          </form>
        </div>
    </div>
  </div>
</div>
