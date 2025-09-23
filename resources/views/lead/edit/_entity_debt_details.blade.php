<form action="{{ route('lead.update', $lead->id) }}" method="POST" class="user_form">
    @csrf
    @method('PUT')


    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                {!! Html::label('Entity Names', 'etity_name') !!}
                {!! Html::text('etity_name')->class('form-control')
                ->placeholder('Enter Entity Name')
                ->value($lead->title)
                ->attribute('readonly', 'readonly')
                !!}
            </div>
        </div>



        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Account/Ref No', 'account_number') !!}
                {!! Html::text('account_number')->class('form-control')
                ->placeholder('')
                ->value($lead->account_number)
                !!}
            </div>
        </div>
    </div>



    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Debt Amount*', 'amount') !!}
                {!! Html::text('amount')
                ->type('number') // Ensures only numbers are allowed
                ->class('form-control')
                ->placeholder('Enter The Debt Amount')
                ->attribute('step', '0.01') // Allows decimals (e.g., 10.50)
                ->attribute('min', '0') // Prevents negative values
                ->value($lead->amount)
                ->required()

                !!}
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Debt Balance*', 'balance') !!}
                {!! Html::text('balance')
                ->type('number') // Ensures only numbers are allowed
                ->class('form-control')
                ->placeholder('Enter The Debt balance')
                ->attribute('step', '0.01') // Allows decimals (e.g., 10.50)
                ->attribute('min', '0') // Prevents negative values
                ->value($lead->balance)
                ->required()

                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Waiver/Discount', 'waiver_discount') !!}
                {!! Html::text('waiver_discount')
                ->type('number') // Ensures only numbers are allowed
                ->class('form-control')
                ->placeholder('Enter Waiver/Discount Amount')
                ->attribute('step', '0.01') // Allows decimals (e.g., 10.50)
                ->attribute('min', '0') // Prevents negative values
                ->value($lead->waiver_discount)
                !!}
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Currency*', 'currency') !!}
                <div style="width: 100%">
                    {!! Html::select('currency', $currencies)
                    ->class('select2')
                    ->placeholder('--Specify--')
                    ->attribute('style', 'width: 100%; padding: 10px;')
                    ->value($lead->currency_id)
                    ->required()
                    !!}
                </div>
            </div>
        </div>

    </div>


    <div class="row">



        @php
        use Carbon\Carbon;
        $formattedDate = $lead->due_date ? Carbon::parse($lead->due_date)->format('d-m-Y') : '';
        @endphp

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Due Date*', 'due_date') !!}
                {!! Html::text('due_date')
                ->type('text')
                ->class('form-control date')
                ->placeholder('Enter The Due Date')
                ->required()
                ->attribute('autocomplete', 'off')
                ->value($formattedDate)
                !!}
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <hr />
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Category*', 'category') !!}
                <div style="width: 100%">
                    {!! Html::select('category', $leadCategories)
                    ->class('select2')
                    ->placeholder('--Specify--')
                    ->attribute('style', 'width: 100%; padding: 10px;')
                    ->value($lead->category_id)
                    ->required()
                    !!}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Priority*', 'priority') !!}
                <div style="width: 100%">
                    {!! Html::select('priority', $priorities)
                    ->class('select2')
                    ->placeholder('--Specify--')
                    ->attribute('style', 'width: 100%; padding: 10px;')
                    ->value($lead->priority_id)
                    ->required()
                    !!}
                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Assign to Agent*', 'agent') !!}
                <div style="width: 100%">
                    {!! Html::select('agent', $agentsList)
                    ->class('select2')
                    ->placeholder('--Specify--')
                    ->attribute('style', 'width: 100%; padding: 10px;')
                    ->value($lead->assigned_agent)
                    ->required()
                    !!}
                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Assign to Department', 'department') !!}
                <div style="width: 100%">
                    {!! Html::select('department', $departments)
                    ->class('select2')
                    ->placeholder('--Specify--')
                    ->attribute('style', 'width: 100%; padding: 10px;')
                    ->value($lead->assigned_department)
                    //->required()
                    !!}
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="step" value="2">
            <button type="submit" class="btn btn-primary"><strong>SUBMIT</strong></button>
        </div>
    </div>
</form>