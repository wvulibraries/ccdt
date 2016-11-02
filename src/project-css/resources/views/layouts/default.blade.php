<!DOCTYPE html>
<html lang="en">
  <head>
    @include('includes.meta')
  </head>
  <body>
    <!-- Nav Bar-->
    <div class="clearfix">
      @include('includes.navbar')
    </div>

    <!-- Alerts -->
    <div class="container-fluid">
      @include('includes.alerts')
    </div>

    <!-- Content -->
    <div class="container contentwrap">
      @yield('content')
    </div>

    <!-- Footer -->
    <div class="container-fluid">
      @include('includes.footer')
    </div>

    <!-- Scripts -->
    @include('includes.scripts')
  </body>
</html>
