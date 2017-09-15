@extends('layouts.default')

<!-- Content -->
@section('content')
    <!-- header -->
    @include('includes.header')

    <!-- Quick Links -->
    <div class="container text-center">

      <!-- Link cards -->
      <div class="qckLnksWrppr">
        <!-- header -->
        <h3>Made with <span style="color:red" class="glyphicon glyphicon-heart"></span> for open source.</h3>
        <!-- Row -->
        <div class="row">
          <!-- cards -->
          <!-- Presentation -->
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="http://bitly.com/wvusystemscss">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-blackboard visible-xs visible-sm smallIcon"></span><label>Presentation</label>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-blackboard"></span>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="https://github.com/wvulibraries/rockefeller-css/tree/master/src/project-css">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-console visible-xs visible-sm smallIcon"></span><label>Source</label>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-console"></span>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="https://github.com/wvulibraries/rockefeller-css/wiki">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-book visible-xs visible-sm smallIcon"></span><label>Documentation</label>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-book"></span>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="https://lib.wvu.edu/software/">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-comment visible-xs visible-sm smallIcon"></span><label>Contact</label>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-comment"></span>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
@stop
