@if(count($errors)>0)
<div class="alert alert-danger alert-dismissible">
    {{-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> --}}
    @foreach($errors->all() as $error)
    {{$error}}<br>
    @endforeach
</div>
@endif


@if(session('success'))
<div class="alert alert-success alert-dismissible">
    {{-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> --}}
    {{session('success')}}
</div>
@endif


@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    {{-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> --}}
    {{session('error')}}
</div>
@endif