<?php

namespace App\Http\Controllers;

use App\Models\RequirementReps;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
class RequirementRepsController extends Controller
{
    public function index()
    {
        $requirements = RequirementReps::get();

        return view('requirement.index', compact('requirements'));
    }
    public function getList(Request $request)
    {
        $query = DB::table('requirement_reps')
            ->select('*');
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
                });
        }
        $totalRecords = DB::table('requirement_reps')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'description',   
            2 => 'with_expiration',
            3 => 'status',
            4 => null                    
        ];
        $orderColumn = $columns[$orderColumnIndex] ?? null;

        if (!empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $fees = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($fees as $row) {
            $data[] = [
                'no' => $i++,
                    'description' => $row->description ?? '',
                    'with_expiration' => ($row->with_expiration==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Yes</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">No</span>'),
                    'status' => ($row->status=='Active'?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Inactive</span>'),
                    'action' => '<a href="#" class="btn btn-sm btn-primary edit-req-btn"
                                data-id="' . $row->id . '"
                                data-description="' . e($row->description) . '"
                                data-with_expiration="' . e($row->with_expiration) . '"
                                data-status="' . e($row->status) . '"
                                data-bs-toggle="modal" data-bs-target="#editRequirementModal">
                                <i class="fas fa-pencil "></i>
                            </a>'
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'issued_id' => 'required|string|max:255',
            'status' => 'required',
        ]);

        try {
            $req = new RequirementReps();
            $req->description = $request->issued_id;
            $req->with_expiration = $request->with_expiration;
            $req->status = $request->status;
            $req->created_by = Auth::id();
            $req->created_at = Carbon::now();

            $req->save();

            return redirect()->route('requirement.index')->with('success', 'Requirement Created Successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to create user: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
   
    public function update(Request $request, $id)
    {   
        $req = RequirementReps::findOrFail($id);

        $request->validate([
            'issued_id' => 'required|string|max:255',
            'status' => 'required',
        ]);

        try {

            $req->update([
                'description' => $request->issued_id,
                'with_expiration' => $request->with_expiration,
                'status' => $request->status,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('requirement.index')->with('success', 'Requirement Updated Successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to create user: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
