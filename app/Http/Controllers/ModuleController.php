<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ModuleMaster;
use Illuminate\Support\Str;
use App\Services\CommonService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ModuleController extends BaseController
{
	protected $commonService;

	public function __construct(CommonService $commonService)
	{
		parent::__construct();
		$this->commonService = $commonService;
	}

	public function moduleMaster()
	{
		if (session('user')->user_type_id == 1) {
			$params = ModuleMaster::where('status', 1)
				->orderBy('ordering', 'asc')
				->get();
			$title = "Service Master";
			return view('module_master', compact('title', 'params'));
		} else {
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addModuleMaster()
	{
		if (session('user')->user_type_id == 1) {
			$title = "Service Master";
			return view('add_module_master', compact('title'));
		} else {
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addModuleMasterProcess(Request $request)
	{
		$request->validate([
			'name' => 'required|unique:module_master,name',
			'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // 2MB max
			'details' => 'required',
		]);

		if ($request->hasFile('file')) {
			$file = $request->file('file');
			$filename = time() . '_' . $file->getClientOriginalName();
			$filePath = 'uploads/module_master/' . $filename;
			$file->move(public_path('uploads/module_master'), $filename);
		}

		$slug = Str::slug($request->name);

		$data = [
			'name' => $request->name,
			'details' => $request->details,
			'filepath' => $filePath ?? null,
			'slug' => $slug,
		];

		ModuleMaster::create($data);

		return redirect()->route('admin.moduleMaster')->with('success', 'Service master added successfully.');
	}

	public function editModuleMaster($id)
	{
		if (session('user')->user_type_id == 1) {
			$details = ModuleMaster::where('id', base64_decode($id))->first();
			if ($details) {
				$title = "Service Master";
				return view('add_module_master', compact('title', 'details'));
			}
			abort(404, 'Complaint master not found');
		} else {
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function editModuleMasterProcess(Request $request)
	{
		$request->validate([
			'id' => 'required|exists:module_master,id',
			'name' => 'required|unique:module_master,name,' . $request->id,
			'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
			'details' => 'required',
		]);

		$service = ModuleMaster::find($request->id);

		// If new file is uploaded
		if ($request->hasFile('file')) {
			$file = $request->file('file');
			$filename = time() . '_' . $file->getClientOriginalName();
			$filePath = 'uploads/module_master/' . $filename;

			// Delete old file if exists
			if ($service->filepath && file_exists(public_path($service->filepath))) {
				unlink(public_path($service->filepath));
			}

			// Save new file
			$file->move(public_path('uploads/module_master'), $filename);
			$service->filepath = $filePath;
		}

		$slug = Str::slug($request->name); // auto lowercase + space to dash

		// Update other fields
		$service->name = $request->name;
		$service->details = $request->details;
		$service->slug = $slug;

		$service->update();

		return redirect()->route('admin.moduleMaster')->with('success', 'Service master updated successfully.');
	}

	public function deleteModuleMaster($id)
	{
		if (session('user')->user_type_id == 1) {
			$service = ModuleMaster::where('id', base64_decode($id))->first();
			if ($service) {
				$check_count = MunicipalityModuleMaster::where('module_master_id', base64_decode($id))->count();
				if ($check_count == 0) {
					$service->delete();
					if ($service->filepath && file_exists(public_path($service->filepath))) {
						unlink(public_path($service->filepath));
					}

					MunicipalityModuleMaster::where('module_master_id', base64_decode($id))->delete();

					return redirect()->route('admin.moduleMaster')->with('success', 'Service Master deleted successfully.');
				} else {
					return redirect()->route('admin.moduleMaster')->with('error', 'This Service Master is associated with municipality(s) and it cannot be deleted.');
				}
			} else {
				return redirect()->route('admin.moduleMaster')->with('error', 'Service Master not found.');
			}
		} else {
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

}//end class  
