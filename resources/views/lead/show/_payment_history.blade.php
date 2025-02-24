<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <h2 class="card-title pb-2">Payment History</h2>

            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#new_payment_modal">
                <i class="fa fa-fw fa-plus"></i> New Payment
            </a>
        </div>
    </div>
</div>



<div class="row">

    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Amount Paid</th>
                    <th>Balance Before</th>
                    <th>Balance after</th>
                    <th>Agent</th>
                    <th>Date</th>
                    <th>Trans ID</th>
                    <th>Status</th>
                </tr>
            </thead>


            <tbody>
                @foreach ($payments as $key=>$payment)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$leadDetails->currency_name}} {{number_format($payment->amount,0)}}</td>
                    <td>{{$leadDetails->currency_name}} {{number_format($payment->balance_before,0)}}</td>
                    <td>{{$leadDetails->currency_name}} {{number_format($payment->balance_after,0)}}</td>
                    <td>{{$payment->agent_name}}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y H:i:s') }}</td>
                    <td>{{$payment->trans_id}}</td>
                    <td><span class="badge bg-{{$payment->color_code}}">
                            {{$payment->payment_status_name}}</span></td>
                </tr>

                @endforeach
            </tbody>
        </table>



        <div class="mt-3">
            {{-- {{ $payments->appends(request()->query())->links() }} --}}

            {{ $payments->withQueryString()->links() }}
        </div>

    </div>
</div>


@include('modals.payments._new_payment_modal')