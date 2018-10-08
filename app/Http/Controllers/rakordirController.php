<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use Validator;

class rakordirController extends Controller
{
   public function __construct ()
	{
	   date_default_timezone_set('Asia/Jakarta');
    }
    //menu input rakordir
    public function index(Request $request)
    {
        $data_group     = $request->get('data_group');
        $data_menu      = $request->get('data_menu');
        return view('rakordir.inputFile',
            [
                'data_group'    =>$data_group,
                'data_menu'     =>$data_menu
            ]
        );
    }
    public function formInput(Request $request)
    {
    	$data_group     = $request->get('data_group');
        $data_menu      = $request->get('data_menu');
    	return view('rakordir.inputFileForm',
  			[
  				'data_group'    =>$data_group,
  				'data_menu'     =>$data_menu
  			]
        );
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file'          => ['required','mimes:pdf,PDF','max:5120'],
            'tanggal'       => 'required|unique:rakordir,date,null,id,agenda_no,'.$request->agenda_no,
            'no_dokument'   => 'required',
            'agenda_no'     => 'required|unique:rakordir,agenda_no,null,id,date,'.$request->tanggal,
            'judul'         => 'required',
        ]);

        $username      = $request->session()->get('username'); 
        $save = DB::table('rakordir')
            ->insert(
                [
                    'username'  => $username,
                    'date'      => $request->tanggal,
                    'judul'     => $request->judul,
                    'agenda_no' => $request->agenda_no,
                    'no_dokument' => $request->no_dokument,
                ]
            );
        if($save){

            $uploadedFile  = $request->file('file'); 
            $path          = $uploadedFile->store( 'public/files/rakordir/'.$username.'/'.date('Y-m-d') );
            $realName      = $request->file->getClientOriginalName();

            $update = DB::table('rakordir')
            ->where('date',$request->tanggal)
            ->where('agenda_no',$request->agenda_no)
            ->update(
                [
                    'file_name' => $realName,
                    'file_path' => str_replace("public/","",$path),
                ]
            );
            return redirect('/rakordir/input_file');
        }
        return Redirect::back()->withErrors(['msg', 'Error']);
        
    }
    public function showUpload(Request $request)
    {
        $username      = $request->session()->get('username');
        $data = DB::table('rakordir')->where('username',$username);
        return Datatables::of($data)->make(true);
    }
    
    //menu materi rakordir
    public function file(Request $request,$tanggal=null)
    {

        $data_group     = $request->get('data_group');
        $data_menu      = $request->get('data_menu');
        $tanggalx        = null;
        if($tanggal){
            $tanggalx  = $tanggal;
            $data = null;
        }else if($request->cari){
            $tanggalx  = $request->cari;
            $data = null;
        }else{
            $data = DB::table('rakordir')->groupBy('date')->paginate(12);
        }
        
        return view('rakordir.materi',
            [
                'data_group' => $data_group,
                'data_menu'  => $data_menu,
                'data'       => $data,
                'tanggal'     => $tanggalx
            ]
        );

    }
    public function fileCari(Request $request)
    {
        $data_group     = $request->get('data_group');
        $data_menu      = $request->get('data_menu');
        $isi            = null;
        if($request->cari){
            $data = DB::table('rakordir')->where('judul','like','%'.$request->cari.'%')->get();
            $isi  = $request->cari;
            return view('rakordir.materi',
                [
                    'data_group' => $data_group,
                    'data_menu'  => $data_menu,
                    'data'       => $data,
                    'isi'       => $isi
                ]
            );
        }else{
            return redirect('rakordir/file');
        }
    }

    public function showMateri(Request $request,$tanggal=null)
    {
        $data = DB::table('rakordir')->where('date',$tanggal)->orWhere('judul','like','%'.$tanggal.'%');
        return Datatables::of($data)->make(true);
    }
}
