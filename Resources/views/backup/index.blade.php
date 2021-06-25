@extends('core::page.tools')
@section('inner-title', "$title - ")
@section('mBackup', 'opened')

@section('css')
    @include('core::layouts.partials.datatables')
@endsection

@section('js') 
    <script src="https://cdn.enterwind.com/template/epanel/js/lib/datatables-net/datatables.min.js"></script>
    <script>
        $(function() {
            $('#datatable').DataTable({
                order: [[ 1, "desc" ]], 
                processing: true,
                serverSide: true,
                ajax : '{!! request()->fullUrl() !!}?datatable=true', 
                columns: [
                    { data: 'pilihan', name: 'pilihan', className: 'table-check' },
                    { data: 'file', name: 'file' },
                    { data: 'size', name: 'size' }, 
                    { data: 'last', name: 'last', className: 'table-date' }, 
                    { data: 'aksi', name: 'aksi', className: 'text-right' }
                ],
                "fnDrawCallback": function( oSettings ) {
                    @include('core::layouts.components.callback')
                }
            });
        });
        @include('core::layouts.components.hapus')
    </script>
@endsection

@section('content')

    @if(!count($data))

        @include('core::layouts.components.kosong', [
            'icon' => 'font-icon font-icon-download',
            'judul' => $title,
            'subjudul' => __('backup::general.empty'), 
            'tambah' => route("$prefix.create")
        ])

    @else
        
        {!! Form::open(['method' => 'delete', 'route' => ["$prefix.destroy", 'hapus-all'], 'id' => 'submit-all']) !!}

            @include('core::layouts.components.top', [
                'judul' => $title,
                'subjudul' => __('backup::general.subtitle.index'),
                'tambah' => route("$prefix.create"), 
                'hapus' => true
            ])

            <div class="card">
                <div class="card-block table-responsive">
                    <table id="datatable" class="display table table-striped" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="table-check"></th>
                                <th>{{ __('backup::general.table.file') }}</th>
                                <th width="10%">{{ __('backup::general.table.size') }}</th>
                                <th width="15%">{{ __('backup::general.table.last') }}</th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            
        {!! Form::close() !!}
    
    @endif

@endsection
