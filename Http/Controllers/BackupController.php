<?php

namespace Modules\Backup\Http\Controllers;

use Modules\Core\Http\Controllers\CoreController as Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception, Storage;

class BackupController extends Controller
{
    protected $title;

    /**
     * Siapkan konstruktor controller
     * 
     * @param Backup $data
     */
    public function __construct() 
    {
        $this->title = __('backup::general.title');

        $this->middleware('auth');

        $this->toIndex = route('epanel.backup.index');
        $this->prefix = 'epanel.backup';
        $this->view = 'backup::backup';

        $this->tCreate = __('backup::general.notif.created');
        $this->tDelete = __('backup::general.notif.deleted');

        view()->share([
            'title' => $this->title, 
            'view' => $this->view, 
            'prefix' => $this->prefix
        ]);
    }

    /**
     * Tampilkan halaman utama modul yang dipilih
     * 
     * @param Request $request
     * @return Response|View
     */
    public function index(Request $request) 
    {
        $data = Storage::disk()->allFiles('backup');

        if($request->has('datatable')):
            return $this->datatable($data);
        endif;
        
        return view("$this->view.index", compact('data'));
    }

    /**
     * Tampilkan halaman untuk menambah data
     * 
     * @return Response|View
     */
    public function create(Request $request) 
    {
        try {
            \Artisan::call('backup:run --only-db');

            notify()->flash($this->tCreate, 'success');
            return redirect()->back();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Lakukan penyimpanan data ke database
     * 
     * @param Request $request
     * @return Response|View
     */
    public function store() 
    {
        return abort(404);
    }

    /**
     * Menampilkan detail lengkap
     * 
     * @param Int $id
     * @return Response|View
     */
    public function show($id)
    {
        return abort(404);
    }

    /**
     * Tampilkan halaman perubahan data
     * 
     * @param Int $id
     * @return Response|View
     */
    public function edit(Request $request, $id)
    {
        return Storage::download('backup/'.$id);
    }

    /**
     * Lakukan perubahan data sesuai dengan data yang diedit
     * 
     * @param Request $request
     * @param Int $id
     * @return Response|View
     */
    public function update($id)
    {    
        return abort(404);
    }

    /**
     * Lakukan penghapusan data yang tidak diinginkan
     * 
     * @param Request $request
     * @param Int $id
     * @return Response|String
     */
    public function destroy(Request $request, $id)
    {
        if($request->has('pilihan')):
            foreach($request->pilihan as $temp):
                Storage::disk()->delete('backup/'.$temp);
            endforeach;
            notify()->flash($this->tDelete, 'success');
            return redirect()->back();
        endif;
        Storage::disk()->delete('backup/'.$id);
        return 'success';
    }

    /**
     * Datatable API
     * 
     * @param  $data
     * @return Datatable
     */
    public function datatable($data) 
    {
        return datatables()->of($data)
            ->editColumn('pilihan', function($data) {
                $return  = '<span>';
                $return .= '    <div class="checkbox checkbox-only">';
                $return .= '        <input type="checkbox" id="pilihan['.str_replace('backup/', '', $data).']" name="pilihan[]" value="'.str_replace('backup/', '', $data).'">';
                $return .= '        <label for="pilihan['.str_replace('backup/', '', $data).']"></label>';
                $return .= '    </div>';
                $return .= '</span>';
                return $return;
            })
            ->editColumn('file', function($data) {
                $return  = str_replace('backup/', '', $data);
                return $return;
            })
            ->editColumn('size', function($data) {
                $return  = humanFilesize(Storage::disk()->size($data));
                return $return;
            })
            ->editColumn('last', function($data) {
                $return  = Storage::disk()->lastModified($data);
                return gmdate("Y-m-d\TH:i:s\Z", $return);
            })
            ->editColumn('aksi', function($data) {
                $return  = '<a href="'.route("$this->prefix.edit", str_replace('backup/', '', $data)).'" class="text-warning"><i class="fa fa-download"></i> Download</a>&nbsp;&nbsp;&nbsp;';
                $return .= '<a href="javascript:;" class="text-success" data-toggle="tooltip" title="Sorry, this access can only accessed by the Developers!" data-placement="top"><i class="fa fa-refresh"></i> Restore</a>';
                return $return;
            })
            ->rawColumns(['pilihan', 'file', 'size', 'aksi'])->toJson();
    }
}
