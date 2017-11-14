@extends('layouts.default')

@section('content')

<!-- Search engine -->
@include('user/searchbox')

<!-- Separation -->
<hr/>

<div class="dataWrppr">
    <div class="container">

      <!-- Separation -->
      <hr/>

      @php
       <!-- if ($fileMimeType == 'application/msword') {
         $fileContents->save("php://output");
       }
       else { -->
         var_dump($fileContents);
       <!-- } -->

      @endphp
   </div>
</div>

@endsection
