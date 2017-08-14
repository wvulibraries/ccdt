@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('/help') }}">Help</a></h2>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

    <!-- Quick Links -->
    <div class="container text-center">

        <!-- Row -->
        <div class="row">
          <!-- Basic Search Help -->
          <div class="col-xs-12 col-sm-6 col-md-3">
            <h2>Search</h2>
            <p></p>
            <div class="divTable">
               <div class="heading">
                  <div class="cell" align="center">Search String</div>
                  <div  class="cell">Results</div>
               </div>
               <div class="row">
                  <div class="cell">nice language</div>
                  <div class="cell">Match either nice, language, or both</div>
               </div>
               <div class="row">
                  <div class="cell">+nice +language</div>
                  <div class="cell">Match both nice and language</div>
               </div>
               <div class="row">
                  <div class="cell">+nice -language</div>
                  <div class="cell">Match nice but not language</div>
               </div>
               <div class="row">
                  <div class="cell">+nice ~language</div>
                  <div class="cell">Match nice, but mark down as less relevant rows that contain language</div>
               </div>
               <div class="row">
                  <div class="cell">+nice*</div>
                  <div class="cell">Match nice, nicely, nicety, nice language, etc</div>
               </div>
               <div class="row">
                  <div class="cell">"nice language"</div>
                  <div class="cell">Match the exact term "nice language"</div>
               </div>
               <div class="row">
                  <div class="cell">+nice +(language country)</div>
                  <div class="cell">Match either "nice language" or "nice country"</div>
               </div>
               <div class="row">
                  <div class="cell">+nice +(&gt;language &lt;country)</div>
                  <div class="cell">Match either "nice language" or "nice country", with rows matching "nice language" being considered more relevant</div>
               </div>
           </div>
          </div>

      </div>
    </div>
@stop
