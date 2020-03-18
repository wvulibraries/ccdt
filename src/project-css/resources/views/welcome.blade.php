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
        <h2>Made with <span style="color:red" class="glyphicon glyphicon-heart" aria-hidden="true"></span> for open source.</h2>
        <!-- Row -->
        <div class="row">
          <!-- cards -->
          <!-- Presentation -->
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="https://docs.google.com/presentation/d/1FyzvqbXJCVkH5S1Af2u6OOTHvzqgfDztlYbq4fLybr4/edit#slide=id.g35f391192_00">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-blackboard visible-xs visible-sm smallIcon" aria-hidden="true"></span>
                  <span class="navigation-cards"> Presentation </span>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-blackboard" aria-hidden="true"></span>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="https://github.com/wvulibraries/ccdt/tree/master/src/project-css">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-console visible-xs visible-sm smallIcon" aria-hidden="true"></span>
                  <span class="navigation-cards"> Source </span>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-console" aria-hidden="true"></span>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="https://github.com/wvulibraries/ccdt/wiki">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-book visible-xs visible-sm smallIcon" aria-hidden="true"></span>
                  <span class="navigation-cards"> Documentation </span>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-book" aria-hidden="true"></span>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-3">
            <a href="mailto:libdev@mail.wvu.edu?&cc=danielle.emerling@mail.wvu.edu">
              <div class="well dashCard btn-circle">
                <!-- heading -->
                <div class="dashCardHeading">
                  <span class="glyphicon glyphicon-comment visible-xs visible-sm smallIcon" aria-hidden="true"></span>
                  <span class="navigation-cards"> Contact </span>
                </div>
                <!-- Icon -->
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
@stop
