<!DOCTYPE html>
<html lang="en">
  <head>
    @include('includes.meta')
  </head>
  <body>
    <!-- Nav Bar-->
    <div class="row">
      @include('includes.navbar')
    </div>

    <!-- Create the layout for the side menu and the content -->
    <div class="row contentwrap">
        <div class="container-fluid">
            <div class="col-sm-3 col-lg-2 css-sidebar">
              @include('includes.sidebar')
            </div>
            <div class="col-sm-9 col-lg-10">
              @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="row">
      @include('includes.footer')
    </div>
  </body>
</html>
