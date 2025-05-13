<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCostRule;
use App\Models\AdditionalCostRuleType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdditionalCostRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $rules = AdditionalCostRule::leftJoin('additional_cost_rule_types', 'additional_cost_rules.cost_type', '=', 'additional_cost_rule_types.id')
        //     ->select(
        //         'additional_cost_rules.*',
        //         'additional_cost_rule_types.rule_type_name as type_name'
        //     )
        //     ->get();


        if (request()->ajax()) {
            $rules = AdditionalCostRule::leftJoin('additional_cost_rule_types', 'additional_cost_rules.cost_type', '=', 'additional_cost_rule_types.id')
                ->select(
                    'additional_cost_rules.*',
                    'additional_cost_rule_types.rule_type_name'
                );

            return DataTables::of($rules)
                ->addColumn('value', function ($rule) {
                    return number_format($rule->value, 0) . ($rule->type == 'Percentage' ? '%' : '');
                })
                ->addColumn('apply_due_date', function ($rule) {
                    return $rule->apply_due_date ? 'Yes' : 'No';
                })
                ->addColumn('active', function ($rule) { // Change from 'active' to 'is_active'
                    return $rule->is_active == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($rule) {
                    return '<a href="' . route('additional-cost-rules.edit', $rule->id) . '" class="btn btn-warning btn-sm">Edit</a>
            <form action="' . route('additional-cost-rules.destroy', $rule->id) . '" method="POST" style="display:inline;">
                ' . csrf_field() . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</button>
            </form>';
                })
                ->rawColumns(['active', 'actions']) // ✅ Ensure HTML rendering works
                ->make(true);
        }


        return view('additional_cost_rules.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $costTypes = AdditionalCostRuleType::where('is_active', 1)->get();
        return view('additional_cost_rules.create', compact('costTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:Fixed amount,Percentage',
            'cost_type' => 'required|exists:additional_cost_rule_types,id',
            'value' => 'required|numeric',
            // 'apply_due_date' => 'nullable|boolean',
            'days' => 'nullable|integer',
            'is_active' => 'required'
        ]);


        //  dd($request->is_active);


        $additionalCostRule = AdditionalCostRule::create([
            'title' => $request->title,
            'type' => $request->type,
            'cost_type' => $request->cost_type,
            'value' => $request->value,
            'apply_due_date' => $request->apply_due_date ? 1 : 0,
            'days' => $request->apply_due_date ? $request->days : null,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'created_by' => auth()->id(),
        ]);


        AdditionalCostRule::where('id', $additionalCostRule->id)->update([
            'rule_code' => 'PR' . $additionalCostRule->id,
        ]);

        return redirect()->route('additional-cost-rules.index')->with('success', 'Rule added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdditionalCostRule $additionalCostRule)
    {
        $costTypes = AdditionalCostRuleType::where('is_active', 1)->get();
        return view('additional_cost_rules.edit', compact('additionalCostRule', 'costTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdditionalCostRule $additionalCostRule)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:Fixed amount,Percentage',
            'cost_type' => 'required|exists:additional_cost_rule_types,id',
            'value' => 'required|numeric',
            // 'apply_due_date' => 'nullable|boolean',
            'days' => 'nullable|integer',
            'is_active' => 'required'
        ]);

        $additionalCostRule->update([
            'title' => $request->title,
            'type' => $request->type,
            'cost_type' => $request->cost_type,
            'value' => $request->value,
            'apply_due_date' => $request->apply_due_date ? 1 : 0,
            'days' => $request->apply_due_date ? $request->days : null,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('additional-cost-rules.index')->with('success', 'Rule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdditionalCostRule $additionalCostRule)
    {
        $additionalCostRule->delete();
        return redirect()->route('additional-cost-rules.index')->with('success', 'Rule deleted successfully.');
    }



    public function scheduledRules(Request $request) {}
}
