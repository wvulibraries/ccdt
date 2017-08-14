<link href="{{ asset('/css/divTable.css') }}" rel="stylesheet">

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

<div class="col-xs-12 col-sm-12 col-md-12">

    <h2>Search</h2>
    <p></p>
    <p>CCDT search uses mysql advanced text searching using full-text boolean queries. </p>

    <div class="divTable helpTable">
      <div class="divTableHeading">
        <div class="divTableRow">
          <div class="divTableHead">Search String</div>
          <div class="divTableHead">Results</div>
        </div>
      </div>
      <div class="divTableBody">
        <div class="divTableRow">
          <div class="divTableCell">nice language</div>
          <div class="divTableCell">Match either nice, language, or both</div>
        </div>

        <div class="divTableRow">
          <div class="divTableCell">+nice +language</div>
          <div class="divTableCell">Match both nice and language</div>
        </div>

        <div class="divTableRow">
          <div class="divTableCell">+nice -language</div>
          <div class="divTableCell">Match nice but not language</div>
        </div>

        <div class="divTableRow">
          <div class="divTableCell">+nice ~language</div>
          <div class="divTableCell">Match nice, but mark down as less relevant rows that contain language</div>
        </div>

        <div class="divTableRow">
          <div class="divTableCell">+nice*</div>
          <div class="divTableCell">Match nice, nicely, nicety, nice language, etc</div>
        </div>

        <div class="divTableRow">
          <div class="divTableCell">"nice language"</div>
          <div class="divTableCell">Match the exact term "nice language"</div>
        </div>

        <div class="divTableRow">
          <div class="divTableCell">+nice +(language country)</div>
          <div class="divTableCell">Match either "nice language" or "nice country"</div>
        </div>

        <div class="divTableRow">
          <div class="divTableCell">+nice +(&gt;language &lt;country)</div>
          <div class="divTableCell">Match either "nice language" or "nice country", with rows matching "nice language" being considered more relevant</div>
        </div>

      </div>
    </div>
</div>

@stop
