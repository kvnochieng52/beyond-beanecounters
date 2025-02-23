<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <h2 class="card-title pb-2">Activities</h2>

            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#new_debt_modal">
                <i class="fa fa-fw fa-plus"></i> New Activity
            </a>
        </div>
    </div>
</div>


<a href="/lead/{{$leadDetails->id}}?page=activities&view=list"><i class="fas fa-list"></i> List</a> &nbsp; &nbsp;
<a href="/lead/{{$leadDetails->id}}?page=activities&view=timeline"><i class="fas fa-route"></i>Timeline</a>



@include('lead.show.activities._list')

{{-- @include('lead.show.activities._timeline') --}}
@include('modals.activities._new_activity_modal')