<!DOCTYPE html>
<html lang="en">
  <head>
    @include('includes.meta')
  </head>
  <body>

    <!-- Nav Bar-->
    <div class="container-fluid">
      @include('includes.navbar')
    </div>

    <!-- Alerts -->
    <div class="container-fluid">
      @include('includes.alerts')
    </div>

    <!-- Create the layout for the side menu and the content -->
    <div class="container contentwrap">
      <div class="row">
        <div class="col-sm-3 col-lg-2 css-sidebar">
          @include('includes.sidebar')
        </div>
        <div class="col-sm-9 col-lg-10">
          @yield('content')
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid">
      @include('includes.footer')
    </div>
  </body>
</html>
