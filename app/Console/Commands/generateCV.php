<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class generateCV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cv:generate {modal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Controller and View for a given table';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $modal = $this->argument('modal');
        $table_name = strtolower($modal) . 's';
        $columns = Schema::getColumnListing($table_name);

        // create view inside resources/views/ if not exists
        if (!file_exists(resource_path('views/' . $table_name))) {
            mkdir(resource_path('views/' . $table_name), 0777, true);
        }

        // create create.blade.php file 
        $create_blade = fopen(resource_path('views/' . $table_name . '/create.blade.php'), "w") or die("Unable to open file!");
        $create_blade_content = '@extends(\'layout.header\')';
        $create_blade_content .= "@section('content')";
        $create_blade_content .= '<div  class="right_col" role="main">';
        $create_blade_content .= '<div class="">';
        $create_blade_content .= '<div class="page-title">';
        $create_blade_content .= '<div class="title_left">';
        $create_blade_content .= '<h3>' . ucfirst($modal) . '</h3>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '<div class="clearfix"></div>';
        $create_blade_content .= '<div class="row">';
        $create_blade_content .= '<div class="col-md-12 ">';
        $create_blade_content .= '<div class="x_panel">';
        $create_blade_content .= '<div class="x_title">';
        $create_blade_content .= '<h2>Create ' . ucfirst($modal) . '</h2>';
        $create_blade_content .= '<ul class="nav navbar-right panel_toolbox">';
        $create_blade_content .= '<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>';
        $create_blade_content .= '<li><a class="close-link"><i class="fa fa-close"></i></a></li>';
        $create_blade_content .= '</ul>';
        $create_blade_content .= '<div class="clearfix"></div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '<div class="x_content">';
        $create_blade_content .= '<br />';
        $create_blade_content .= '<form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="{{ route(\'' . $table_name . '.store\') }}">';
        $create_blade_content .= '@csrf';
        // looping through all the columns and generate input fields or select fields if the column is foreign key
        foreach ($columns as $column) {
            if ($column == 'id') {
                continue;
            }
            if ($column == 'created_at' || $column == 'updated_at') {
                continue;
            }
            if ($column == 'deleted_at') {
                continue;
            }

            if (strpos($column, '_id')) {
                $create_blade_content .= '<div class="item form-group">';
                $create_blade_content .= '<label class="col-form-label col-md-3 col-sm-3 label-align" for="' . $column . '">' . ucfirst($column) . ' <span class="required">*</span>';
                $create_blade_content .= '</label>';
                $create_blade_content .= '<div class="col-sm-6">';
                $create_blade_content .= '<select class="form-control" name="' . $column . '">';
                $create_blade_content .= '<option value="">Select ' . ucfirst($column) . '</option>';
                $create_blade_content .= '@foreach($' . strtolower($column) . 's as $' . strtolower($column) . ')';
                $create_blade_content .= '<option value="{{ $' . strtolower($column) . '->id }}">{{ $' . strtolower($column) . '->name }}</option>';
                $create_blade_content .= '@endforeach';
                $create_blade_content .= '</select>';
                $create_blade_content .= '</div>';
                $create_blade_content .= '</div>';
            } else {
                $create_blade_content .= '<div class="item form-group">';
                $create_blade_content .= '<label class="col-form-label col-md-3 col-sm-3 label-align" for="' . $column . '">' . ucfirst($column) . ' <span class="required">*</span>';
                $create_blade_content .= '</label>';
                $create_blade_content .= '<div class="col-sm-6">';
                $create_blade_content .= '<input type="text" id="' . $column . '" name="' . $column . '" required="required" class="form-control ">';
                $create_blade_content .= '</div>';
                $create_blade_content .= '</div>';
            }
        }
        $create_blade_content .= '<div class="ln_solid"></div>';
        $create_blade_content .= '<div class="item form-group">';
        $create_blade_content .= '<div class="col-md-6 col-md-offset-3">';
        $create_blade_content .= '<button type="submit" class="btn btn-success">Submit</button>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</form>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '</div>';
        $create_blade_content .= '@endsection';

        // write into the create blade file
        $create_blade_file_path = base_path('resources/views/' . $table_name . '/create.blade.php');
        $create_blade_file = fopen($create_blade_file_path, 'w');
        fwrite($create_blade_file, $create_blade_content);
        fclose($create_blade_file);




        // create edit.blade.php file 
        $edit_blade = fopen(resource_path('views/' . $table_name . '/edit.blade.php'), "w") or die("Unable to open file!");
        $edit_blade_content = '';
        $edit_blade_content .= '@extends(\'layout.header\')';
        $edit_blade_content .= '@section(\'content\')';
        $edit_blade_content .= <<<STYLE
            <style>
            .top_nav {
            display: none !important;
            }

            .sidebar-menu {
            display: none !important;
            }

            footer {
            display: none !important;
            }

            .right_col {
            background: white !important;
            margin-top: 0 !important;
            height: auto;
            }
            </style>
        STYLE;

        $edit_blade_content .= '<div class="container">';
        $edit_blade_content .= '<div class="row">';
        $edit_blade_content .= '<div class="col-md-12 ">';
        $edit_blade_content .= '<div class="x_panel">';
        $edit_blade_content .= '<div class="x_title">';
        $edit_blade_content .= '<div class="clearfix"></div>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '<div class="x_content">';
        $edit_blade_content .= '<br />';
        $edit_blade_content .= '<form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="{{ route(\'' . $table_name . '.update\', $' . strtolower($modal) . '->id) }}">';
        $edit_blade_content .= '@csrf';
        // form validation alerts
        $edit_blade_content .= <<<ALERTS
            @if (Session::has('message'))
            <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
            <p id="alert"
            class="alert {{ Session::get('alert-class', 'alert-success') }}">
            {{ Session::get('message') }}</p>
            </div>
            </div>
            @endif
            {{-- display form validation errors --}}
            @if (\$errors->any())
            <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
            <div class="alert alert-danger">
            <ul>
            @foreach (\$errors->all() as \$error)
            <li>{{ \$error }}</li>
            @endforeach
            </ul>
            </div>
            </div>
            </div>
            @endif
        ALERTS;

        // looping through all the columns and generate input fields or select fields if the column is foreign key
        foreach ($columns as $column) {
            if ($column == 'id') {
                continue;
            }
            if ($column == 'created_at' || $column == 'updated_at') {
                continue;
            }
            if ($column == 'deleted_at') {
                continue;
            }

            if (strpos($column, '_id')) {
                $edit_blade_content .= '<div class="item form-group">';
                $edit_blade_content .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="' . $column . '">' . ucfirst($column) . ' <span class="required">*</span>';
                $edit_blade_content .= '</label>';
                $edit_blade_content .= '<div class="col-md-6 col-sm-6 col-xs-12">';
                $edit_blade_content .= '<select class="form-control" name="' . $column . '">';
                $edit_blade_content .= '<option value="">Select ' . ucfirst($column) . '</option>';
                $edit_blade_content .= '@foreach($' . strtolower($column) . 's as $' . strtolower($column) . ')';
                $edit_blade_content .= '<option value="{{ $' . strtolower($column) . '->id }}">{{ $' . strtolower($column) . '->name }}</option>';
                $edit_blade_content .= '@endforeach';
                $edit_blade_content .= '</select>';
                $edit_blade_content .= '</div>';
                $edit_blade_content .= '</div>';
            } else {
                $edit_blade_content .= '<div class="item form-group">';
                $edit_blade_content .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="' . $column . '">' . ucfirst($column) . ' <span class="required">*</span>';
                $edit_blade_content .= '</label>';
                $edit_blade_content .= '<div class="col-md-6 col-sm-6 col-xs-12">';
                $edit_blade_content .= '<input type="text" value="{{ $' . strtolower($modal) . '->' . $column . ' }}" id="' . $column . '" name="' . $column . '" required="required" class="form-control">';
                $edit_blade_content .= '</div>';
                $edit_blade_content .= '</div>';
            }
        }

        $edit_blade_content .= '<div class="ln_solid"></div>';
        $edit_blade_content .= '<div class="item form-group">';
        $edit_blade_content .= '<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">';
        $edit_blade_content .= '<button type="submit" class="btn btn-success">Submit</button>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '</form>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '</div>';
        $edit_blade_content .= '@endsection';

        // write into the edit blade file
        $edit_blade_file_path = base_path('resources/views/' . $table_name . '/edit.blade.php');
        $edit_blade_file = fopen($edit_blade_file_path, 'w');
        fwrite($edit_blade_file, $edit_blade_content);
        fclose($edit_blade_file);






        // create controller file
        $controller = fopen(app_path('Http/Controllers/' . $modal . 'Controller.php'), "w") or die("Unable to open file!");
        $controller_content = '';
        $controller_content .= '<?php ';
        $controller_content .= 'namespace App\Http\Controllers; ';
        $controller_content .= 'use App\Http\Controllers\Controller; ';
        $controller_content .= 'use Illuminate\Http\Request; ';
        $controller_content .= 'use App\Models\\' . $modal . '; ';
        $controller_content .= 'class ' . $modal . 'Controller extends Controller ';
        $controller_content .= '{ ';
        $controller_content .= 'public function index() ';
        $controller_content .= '{ ';
        $controller_content .= '$' . $table_name . ' = ' . $modal . '::all(); ';
        $controller_content .= '$list = true;';
        $controller_content .= 'return view(\'' . $table_name . '.index\', compact(\'' . $table_name . '\', \'list\')); ';
        $controller_content .= '} ';
        $controller_content .= 'public function create() ';
        $controller_content .= '{ ';
        $controller_content .= 'return view(\'' . $table_name . '.create\'); ';
        $controller_content .= '} ';
        $controller_content .= 'public function store(Request $request) ';
        $controller_content .= '{ ';
        // validate first
        $controller_content .= '$this->validate($request, [ ';
        foreach ($columns as $column) {
            if ($column == 'id') {
                continue;
            }
            if ($column == 'created_at' || $column == 'updated_at') {
                continue;
            }
            if ($column == 'deleted_at') {
                continue;
            }
            $controller_content .= '\'' . $column . '\' => \'required\', ';
        }
        $controller_content .= ']); ';
        $controller_content .= '$' . strtolower($modal) . ' = new ' . $modal . '(); ';
        $controller_content .= '$' . strtolower($modal) . '->fill($request->all()); ';
        $controller_content .= '$' . strtolower($modal) . '->save(); ';
        $controller_content .= 'return redirect()->route(\'' . $table_name . '.index\'); ';
        $controller_content .= '} ';
        $controller_content .= 'public function show(' . $modal . ' $' . strtolower($modal) . ') ';
        $controller_content .= '{ ';
        $controller_content .= 'return view(\'' . $table_name . '.show\', compact(\'' . $table_name . '\',)); ';
        $controller_content .= '} ';
        $controller_content .= 'public function edit(' . $modal . ' $' . strtolower($modal) . ') ';
        $controller_content .= '{ ';
        $controller_content .= 'return view(\'' . $table_name . '.edit\', compact(\'' . strtolower($modal) . '\')); ';
        $controller_content .= '} ';
        $controller_content .= 'public function update(Request $request, ' . $modal . '  $' . strtolower($modal) . ') ';
        $controller_content .= '{ ';
        // validate first
        $controller_content .= '$this->validate($request, [ ';
        foreach ($columns as $column) {
            if ($column == 'id') {
                continue;
            }
            if ($column == 'created_at' || $column == 'updated_at') {
                continue;
            }
            if ($column == 'deleted_at') {
                continue;
            }
            // if its email or username it should be unique
            if ($column == 'email' || $column == 'username') {
                $controller_content .= '\'' . $column . '\' => \'required|unique:' . $table_name . ',' . $column . '\', ';
            } else {
                $controller_content .= '\'' . $column . '\' => \'required\', ';
            }
        }
        $controller_content .= ']); ';
        //update one by one
        foreach ($columns as $column) {
            if ($column == 'id') {
                continue;
            }
            if ($column == 'created_at' || $column == 'updated_at') {
                continue;
            }
            if ($column == 'deleted_at') {
                continue;
            }
            $controller_content .= '$' . strtolower($modal) . '->' . $column . ' = $request->' . $column . '; ';
        }
        $controller_content .= '$' . strtolower($modal) . '->save(); ';
        $controller_content .= 'session()->flash(\'message\', \'Record updated successfully.\'); ';
        $controller_content .= 'return redirect()->route(\'' . $table_name . '.edit\' , $' . strtolower($modal) . '->id); ';
        $controller_content .= '} ';
        $controller_content .= 'public function destroy(' . $modal . ' $' . strtolower($modal) . ') ';
        $controller_content .= '{ ';
        $controller_content .= '$' . strtolower($modal) . '->delete(); ';
        $controller_content .= 'return redirect()->route(\'' . $table_name . '.index\'); ';
        $controller_content .= 'session()->flash(\'success\', \'Deleted Successfully\'); ';
        $controller_content .= '} ';
        $controller_content .= '} ';
        $controller_content .= '?>';
        fwrite($controller, $controller_content);
        fclose($controller);

        // create index.blade file
        $index_blade_file_path = base_path('resources/views/' . $table_name . '/index.blade.php');
        $index_blade_file = fopen($index_blade_file_path, 'w');
        $index_blade_content = '';
        $index_blade_content .= '@extends(\'layout.header\') ';
        $index_blade_content .= '@section(\'content\') ';
        $index_blade_content .= <<<HTML
            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Edit $modal</h4>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
            </button>
            </div>
            <div class="modal-body">
            <iframe id="iframe" title="description"></iframe>
            </div>
            </div>
            </div>
            </div>

            {{--  warning modal before delete --}}
            <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <form class="modal-content" method="get" id="delete">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Warning</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <p>Are you sure you want to delete this $modal?</p>
            </div>
            <div class="modal-footer">
            <a href="#" class="btn btn-secondary" data-dismiss="modal">Close</a>
            <button type="submit" class="btn btn-danger" id="delete">Delete</button>
            </div>
            </form>
            </div>
            </div>

            <!-- page content -->
            <div class="right_col" role="main">
            <div class="">
            <div class="page-title">
            </div>
            <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
            <div class="x_title">
            <h2>$table_name <a href="/$table_name/create" class="btn btn-sm btn-success">New $modal</a></h2>
            <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            </ul>
            <div class="clearfix"></div>
            </div>
            <div class="x_content">
            @if (Session::has('message'))
            <div class="row">
            <div class="col-md-12">
            <p id="alert" class="alert {{ Session::get('alert-class', 'alert-success') }}">
            {{ Session::get('message') }}</p>
            </div>
            </div>
            @endif

            <div class="row">
            <div class="col-sm-12">
            <div class="card-box table-responsive">
            <table id="datatable-buttons"
            class="table table-striped table-bordered dataTable no-footer dtr-inline"
            style="width: 100%;" role="grid" aria-describedby="datatable-buttons_info">
        HTML;

        $index_blade_content .= '<thead> ';
        $index_blade_content .= '<tr role="row"> ';
        foreach ($columns as $column) {
            $index_blade_content .= '<th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">' . $column . '</th> ';
        }
        $index_blade_content .= '<th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">Action</th> ';
        $index_blade_content .= '</tr> ';
        $index_blade_content .= '</thead> ';
        $index_blade_content .= '<tbody> ';
        $index_blade_content .= '@foreach($' . strtolower($modal) . 's as $' . strtolower($modal) . ') ';
        $index_blade_content .= '<tr role="row" class="odd"> ';
        foreach ($columns as $column) {
            $index_blade_content .= '<td>{{ $' . strtolower($modal) . '->' . $column . ' }}</td> ';
        }
        $index_blade_content .= '<td> ';
        $index_blade_content .= '<a href="#" data-toggle="modal" data-target=".bs-example-modal-lg" onclick="initializeIframe(\'{{ route(\'' . $table_name . '.edit\', $' . strtolower($modal) . '->id) }}\')" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Edit </a> ';
        $index_blade_content .= '<a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#warningModal" onclick="delete_id({{ $' . strtolower($modal) . '->id }})"><i class="fa fa-trash-o"></i> Delete </a> ';
        $index_blade_content .= '</td> ';
        $index_blade_content .= '</tr> ';
        $index_blade_content .= '@endforeach ';
        $index_blade_content .= '</tbody> ';
        $index_blade_content .= '</table> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '</div> ';
        $index_blade_content .= '<script> ';
        $index_blade_content .= 'function delete_id(id) { ';
        $index_blade_content .= 'document.getElementById("delete").action = "/' . $table_name . '/" + id + "/delete"; ';
        $index_blade_content .= '} ';

        $index_blade_content .= 'function initializeIframe(url) { ';
        $index_blade_content .= 'var iframe = document.getElementById("iframe"); ';
        $index_blade_content .= 'iframe.src = url; ';
        $index_blade_content .= '} ';

        $index_blade_content .= '</script> ';

        $index_blade_content .= '@endsection';
        fwrite($index_blade_file, $index_blade_content);
        fclose($index_blade_file);

        // create routes 
        $routes_path = base_path() . '/routes/web.php';
        $routes_file = fopen($routes_path, "a") or die("Unable to open file!");
        $routes_content = 'Route::controller(' . $modal . 'Controller::class)->prefix("' . $table_name . '")->middleware("ceelka")->name("' . $table_name . '")->group(function () { ';
        $routes_content .= 'Route::get("/", "' . 'index")->name(".index"); ';
        $routes_content .= 'Route::get("/create", "' .  'create")->name(".create"); ';
        $routes_content .= 'Route::post("/store", "' . 'store")->name(".store"); ';
        $routes_content .= 'Route::get("/{' . strtolower($modal) . '}/edit", "' .  'edit")->name(".edit"); ';
        $routes_content .= 'Route::post("/{' . strtolower($modal) . '}/update", "' .  'update")->name(".update"); ';
        $routes_content .= 'Route::get("/{' . strtolower($modal) . '}/delete", "' .  'destroy")->name(".delete"); ';
        $routes_content .= '}); ';
        fwrite($routes_file, $routes_content);
        fclose($routes_file);


        //  create record of assets table
        // asset_name = $table_name
        // route_name = $table_name
        $asset_name = $table_name;
        $route_name = $table_name;

        Asset::create([
            'asset_name' => $asset_name,
            'route_name' => $route_name,
        ]);
    }
}
