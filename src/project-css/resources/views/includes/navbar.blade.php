<!-- Nav Bar-->
<nav class="navbar navbar-default navbar-static-top">
  <div class="container">
    <div class="navbar-header">

      <!-- Collapsed Hamburger -->
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
          <span class="sr-only">Toggle Navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>

      <!-- Branding Image -->
      <a class="navbar-brand" href="{{ url('/') }}">
          {{ config('app.name', 'CCDT') }}
      </a>
    </div>

    <div class="collapse navbar-collapse" id="app-navbar-collapse">
      <!-- Left Side Of Navbar -->
      <ul class="nav navbar-nav">
          &nbsp;
      </ul>

      <!-- Right Side Of Navbar -->
      <ul class="nav navbar-nav navbar-right">
          <!-- Authentication Links -->
          @if (Auth::guest())
              <li><a href="{{ url('/login') }}">Login</a></li>
          @else
              <li><a href="{{ url('/home') }}">Home</a></li>
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                      {{ ucfirst(Auth::user()->name) }} <span class="caret"></span>
                  </a>

                  <ul class="dropdown-menu" role="menu">
                      @if (Auth::user()->isAdmin)
                        <li>
                            <li><a href="{{ url('/admin/jobs') }}">View Pending Job(s)</a></li>
                            <li><a href="{{ url('/admin/failedjobs') }}">View Failed Job(s)</a></li>
                        </li>
                      @endif
                      <li>
                          <a href="{{ url('/logout') }}"
                              onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                              Logout
                          </a>

                          <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                          </form>
                      </li>
                  </ul>
              </li>
              <li><a href="{{ url('/help') }}">Help</a></li>
          @endif
      </ul>
    </div>
  </div>
</nav>
