<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentRequest;
use App\Models\Admin\Content;
use App\Models\Admin\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ContentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('see_contents')) {
            return back();
        }
        $data = [
            'navigations' => [__('contents.contents')],
            'columns' => [
                ['data' => 'file', 'name' => 'file', 'title' => __('File')],
                ['data' => 'title', 'name' => 'title', 'title' => __('Title')],
                ['data' => 'status', 'name' => 'status', 'title' => __('Status')],
                ['data' => 'parent', 'name' => 'parent', 'title' => __('Parent')],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('global.created_at')],
                ['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false, 'className' => 'dt-actions'],
            ],
            'parents' => Content::findAllByLocale('contents.id', 'title', 'created_at')->pluck('title', 'id')->toArray()
        ];
        return view('admin.contents.index', $data);
    }

    public function datatable()
    {
        $data = Content::findAllByLocale('contents.id', 'title', 'parent_id', 'active', 'created_at');
        return Datatables::of($data)
            ->addColumn('file', function ($row) {
                $file = Content::select('path')
                    ->where('contents.id', $row->id)
                    // TODO: check this out, it's not working, it always returns the type 1
                    ->where(function ($query){
                        $query->where('files.type', 2)->orWhere('files.type', 1);
                    })
                    ->leftJoin('content_files', 'content_files.content_id', '=', 'contents.id')
                    ->leftJoin('files', 'files.id', 'content_files.file_id')
                    ->first();
                return isset($file->path) ? '<img src="' . asset('storage/' . $file->path) . '" class="table-img" alt="profile"/>' : '<div class="table-img"></div>';
            })
            ->addColumn('action', function ($row) {
                $actions = [
                    ['title' => '<i class="material-icons-outlined md-18">edit</i> ' . __('global.update'), 'onclick' => '__update(' . $row->id . ')'],
                    ['title' => '<i class="material-icons-outlined md-18">delete</i> ' . __('global.delete'), 'onclick' => '__delete(' . $row->id . ')'],
                ];
                return view('admin.partials.dropdown', ['actions' => $actions]);
            })
            ->addColumn('status', function ($row) {
                return $row->active ? '<span class="badge badge-success"><i class="material-icons-outlined md-18">check</i></span>' : '<span class="badge badge-danger"><i class="material-icons-outlined md-18">close</i></span></span>';
            })
            ->addColumn('parent', function ($row) {
                return Content::findOneByLocale($row->parent_id, 'title')->title ?? '';
            })
            ->rawColumns(['file', 'action', 'status'])
            ->make(true);
    }

    public function create(ContentRequest $request)
    {
        if (!Auth::user()->can('create_contents')) {
            return resJsonUnauthorized();
        }
        $data = $request->validated();
        $contentData = array_remove($data, 'content');

        // Get and unset files from content data and if it's not 0 then explode it from | character to add database each one
        $files = array_remove($contentData, 'files');
        $fileIds = $files !== '0' ? explode('|', $files) : [];

        DB::beginTransaction();
        try {
            // Create Content
            $content = Content::create($contentData);

            // Create Content Languages
            // Collect all data in one array to make faster sql queries
            $translationData = [];
            foreach ($data as $language => $values) {
                $translationData[] = array_merge($values, [
                    'language' => $language,
                    'content_id' => $content->id,
                    'url' => Str::slug($values['title'])
                ]);
            }
            DB::table('content_translations')->insert($translationData);

            // Create Content Files
            // Collect all data in one array to make faster sql queries
            $filesData = [];
            foreach ($fileIds as $fileId) {
                $filesData[] = [
                    'content_id' => $content->id,
                    'file_id' => $fileId
                ];
            }
            DB::table('content_files')->insert($filesData);

            DB::commit();
            return resJson(true);
        } catch (\Exception) {
            DB::rollBack();
            return resJson(false);
        }
    }

    public function find(int $id)
    {
        if (!Auth::user()->can('see_contents')) {
            return resJsonUnauthorized();
        }
        // TODO: We'll check that for better way for multi language operations (without model relations)
        $content = Content::select('parent_id', 'searchable')->find($id);

        $translations = DB::table('content_translations')
            ->select('title', 'description', 'full', 'active', 'language')
            ->where('content_id', $id)
            ->get()
            ->keyBy('language')
            ->transform(function ($i) {
                // Remove language keys, i needed it only to make a keyBy on collection
                unset($i->language);
                return $i;
            });

        $files = DB::table('content_files')
            ->where('content_id', $id)
            ->leftJoin('files', 'files.id', 'content_files.file_id')
            ->get()
            ->pluck('path', 'file_id');


        return response()->json([
            'content' => $content,
            'translations' => $translations,
            'files' => $files
        ]);
    }

    public function update(ContentRequest $request, int $id)
    {
        if (!Auth::user()->can('update_contents')) {
            return resJsonUnauthorized();
        }
        $data = $request->validated();
        $contentData = array_remove($data, 'content');

        // Get and unset files from content data and if it's not 0 then explode it from | character to add database each one
        $files = array_remove($contentData, 'files');
        $fileIds = $files !== '0' ? explode('|', $files) : [];

        DB::beginTransaction();
        try {
            // Update Content
            Content::where('id', $id)->update($contentData);

            // Update Content Languages
            $translationData = [];
            foreach ($data as $language => $values) {
                $values['url'] = Str::slug($values['title']);
                DB::table('content_translations')
                    ->where('content_id', $id)
                    ->where('language', $language)
                    ->update($values);
            }

            // Update Content Files
            // Drop all files first, and then collect all data in one array to make faster sql queries
            DB::table('content_files')->where('content_id', $id)->delete();
            $filesData = [];
            foreach ($fileIds as $fileId) {
                $filesData[] = [
                    'content_id' => $id,
                    'file_id' => $fileId
                ];
            }
            DB::table('content_files')->insert($filesData);

            DB::commit();
            return resJson(true);
        } catch (\Exception) {
            DB::rollBack();
            return resJson(false);
        }
    }
}
