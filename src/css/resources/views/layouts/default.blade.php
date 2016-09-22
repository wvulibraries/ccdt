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

    <!-- Content -->
    <div class="row contentwrap">
      @yield('content')
    </div>

    <!-- Footer -->
    <div class="row">
      @include('includes.footer')
    </div>
  </body>
</html>
