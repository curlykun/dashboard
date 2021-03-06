@extends('layout.app')

@section('menu_active')
    @php($active = 'RAKORDIR')
@endsection

@section('style')
<link href="{{ url('plugins/DataTables/css/data-table.css') }}" rel="stylesheet" />
<style type="text/css">
@media (min-width: 768px) {
  .modal-xl {
    width: 90%;
   max-width:1200px;
  }
}
.fa-folder-open:hover {
    color: #428bca;
}
.pdfobject-container { height: 500px;}
.pdfobject { border: 1px solid #666; }
</style>
@endsection

@section('script')
<script type="text/javascript" src="{{ url('plugins/DataTables/js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ url('plugins/DataTables/js/dataTables.responsive.js') }}"></script>
<script type="text/javascript" src="{{ url('plugins/pdfjs/build/pdf.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function(){ 
        $('#pdf').hide();
       $('#example').DataTable( {
            "dom": 'lfr<"toolbar">tip',
               initComplete: function(){
                  $("div.toolbar").html('<button class="btn btn-primary" onclick="openForm()">Tambah</button>');           
               },
            // "order": [[ 2, "desc" ]],
            "responsive": true,
            "processing": true,
            "serverSide": true,
            "columnDefs": [ {
                "targets": 0,
                "orderable": false
            } ],
            // "sDom": 'tipr', 
            "language": {
                "search": "Cari:",
                // "lengthMenu": "Buka _MENU_ ",
                "lengthMenu": "",
                // "info": "Buka _START_ s.d _END_ dari _TOTAL_ data",
                "info": "",
                "paginate": {
                    "first":      "Pertama",
                    "last":       "Terakhir",
                    "next":       "Lanjut",
                    "previous":   "Kembali"
                },
            },
            "ajax": {
                "url" : "{{ url('rakordir/show_upload') }}",
                "type" : "POST",
                "beforeSend": function (request) {
                    request.setRequestHeader("X-CSRF-Token", "{{csrf_token()}}");
                }
            },
            "columns": [
                {   "data": "agenda_no",
                    render: function ( data, type, row, index) {
                        return index.row+1;
                    }
                },
                { "data": "no_dokument"},
                { "data": "datetime"},
                { "data": "agenda_no"},
                { "data": "judul" },
                { "data": "presenter" },
                { "data": "agenda_no" , 
                   render: function ( data, type, row ) {
                    var del = '';
                    var ex = '';
                    var fa = '';
                    var data = row.rakordir_files;
                    for (var index = 0; index < data.length; index++) {
                        
                        if(data[index]){
                            ex = data[index].file_path.substr(-3, 3);
                            if(ex === 'pdf' || ex === 'PDF'){
                                fa = 'fa-file-pdf-o';
                                del += '<a data-toggle="modal" href="#modal-id" class="text-danger m-r-5" '+
                                        'style="margin:0px 2px 2px 0px" onclick="pdf(`'+data[index].file_path+'`)">'+
                                    '<i class="fa '+fa+' fa-2x"></i>'+
                                    '</a>';
                            }else{
                                fa = 'fa-file-text-o';
                                url = "{{ url('/storage/') }}/"+data[index].file_path;
                                del += '<a href="'+url+'" target="_blank" class="text-danger m-r-5" style="margin:0px 2px 2px 0px">'+
                                    '<i class="fa '+fa+' fa-2x"></i>'+
                                    '</a>';
                            }
                            
                        }
                    }
                    return del;
                  }
                },
                { "data": "date" , 
                    render: function ( data, type, row ) {                        
                        $('[data-toggle="tooltip"]').tooltip();
                        var url  = "{{ url('/rakordir/form_edit') }}/"+row.date+"/"+row.agenda_no;
                        var tanggal = String(row.date).replace(/-/g, "");
                        var agenda_no = String(row.agenda_no);

                        var fa   = 'fa-pencil-square-o';
                        var edit = '<a href="'+url+'" class="text-success m-r-5" data-toggle="tooltip" title=" Edit " style="margin:0px 2px 2px 0px">'+
                                        '<i class="fa '+fa+' fa-2x"></i>'+
                                    '</a>'; 
                        var hapus = '<a onclick="hapus('+tanggal+','+agenda_no+')" class="text-danger m-r-10" data-toggle="tooltip" title=" Hapus " style="margin:0px 2px 2px 0px">'+
                                        '<i class="fa fa-times fa-2x"></i>'+
                                    '</a>'; 
                        return edit+hapus;
                  }
                }
            ]
        });
    });

    function hapus(tanggal,agenda_no) {
        var r = confirm("Yakin akan menghapus data ini?");
        if (r == true) {
            $.get("{{ url('rakordir/hapus') }}/"+tanggal+"/"+agenda_no ,function(data){
                if(data.msg = 'success'){
                    location.reload();
                }
            });
        }        
    }
    function pdf(file) {
        var ex = file.substr(-3, 3);
        var url;
        var html;
        if(ex === 'pdf' || ex === 'PDF'){
            url = "{{ url('plugins/pdfjs/web/viewer.html?file=') }}"+"{{ url('/storage/') }}/"+file;
            html = "<iframe src='"+url+"' style='width: 100%; height:80vh'></iframe>";
        }else{
            url = "{{ url('/storage/') }}/"+file;
            html = "<h5 class='text-center'><a href='"+url+"' target='_blank'>Kilik disini untuk unduh file.</a></h5>"
        }
        $('#example1').html(html);
    }
    function openForm() {
        window.location.href = '{{ url('rakordir/form_input') }}';
    }
</script>
@endsection
@section('content')
    <h1 class="page-header">Dashboard Operation Excellence </h1>
    <section id="content">
        @component('component.panel')
            @slot('title')
                <span class="fa fa-file"></span>
                Input File Rakordir 

                {{-- <button class="btn btn-primary btn-sm" onclick="openForm()">Tambah</button>             --}}
            @endslot
            
            <table id="example" class="table table-responsive table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width=" 20%">NO DOKUMENT</th>
                        <th width=" 10%">TANGGAL</th>
                        <th width=" 12%">AGENDA KE</th>
                        <th>JUDUL</th>
                        <th>PRESENTASI OLEH</th>
                        <th width="15%">FILE</th>
                        <th width="10%">ASKI</th>
                    </tr>
                </thead>
            </table>

        @endcomponent
    </section>
    <div class="modal fade" id="modal-id">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">-</h5>
                </div>
                <div class="modal-body">
                    <div id="example1"></div>
                </div>
            </div>
        </div>
    </div>
@endsection