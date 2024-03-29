<?php

namespace App\Http\Controllers\Admin\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menu\ItemRequest;
use App\Models\Menu\MenuGroup;
use App\Models\Menu\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(int $groupId)
    {
        if (!Auth::user()->can('see_menus')) {
            return back();
        }
        $groupItems = MenuGroup::itemsByLocale($groupId, 'menu_items.id', 'item_id', 'parent_id', 'title');
        $data = [
            'navigations' => [route('menus.index') => __('menus.group.self_singular'), __('menus.item.self_plural')],
            'items' => $this->treeItems($groupItems),
            'actions' => $this->actions(),
            'groupId' => $groupId,
            'parents' => $groupItems->pluck('title', 'item_id')->toArray() // Data of selectable menu parent
        ];
        return view('admin.menus.items', $data);
    }

    /**
     * We do not have datatable on menu items, so we just return a view
     *
     * @param int $groupId
     * @return JsonResponse|Response
     */
    public function ajaxList(int $groupId)
    {
        if (!Auth::user()->can('see_menus')) {
            return resJsonUnauthorized();
        }
        $data = [
            'items' => $this->treeItems(MenuGroup::itemsByLocale($groupId, 'item_id', 'parent_id', 'title')),
            'actions' => $this->actions()
        ];
        return response()
            ->view('admin.menus.items-ajax-list', $data)
            ->header('Content-Type', 'application/html');
    }

    public function create(ItemRequest $request, int $groupId)
    {
        if (!Auth::user()->can('create_menus')) {
            return resJsonUnauthorized();
        }
        $data = $request->validated();

        $itemData = array_remove($data, 'item');

        // Set group id from route
        $itemData['group_id'] = $groupId;

        DB::beginTransaction();
        try {
            $item = MenuItem::create($itemData);

            // Create Menu Item Languages
            // Collect all data in one array to make faster sql queries
            $translationData = [];
            foreach ($data as $language => $values) {
                $translationData[] = array_merge($values, [
                    'language' => $language,
                    'item_id' => $item->id,
                    'url' => empty($values['url']) ? Str::slug($values['title'] ?? '') : $values['url'] ?? ''
                ]);
            }
            DB::table('menu_item_translations')->insert($translationData);

            // Remove the menu items cache
            cache()->forget('header-menu');
            cache()->forget('quick-links');

            DB::commit();
            return resJson(true);
        } catch (\Exception) {
            DB::rollBack();
            return resJson(false);
        }
    }

    public function find(int $groupId, int $itemId)
    {
        if (!Auth::user()->can('see_menus')) {
            return resJsonUnauthorized();
        }
        // TODO: We'll check that for better way for multi language operations (without model relations)
        $item = MenuItem::select('parent_id')->find($itemId);

        $translations = DB::table('menu_item_translations')
            ->select('title', 'url', 'icon', 'active', 'language')
            ->where('item_id', $itemId)
            ->get()
            ->keyBy('language')
            ->transform(function ($i) {
                // Remove language keys, i needed it only to make a keyBy on collection
                unset($i->language);
                return $i;
            });

        return response()->json([
            'item' => $item,
            'translations' => $translations
        ]);
    }

    public function update(ItemRequest $request, int $groupId, int $itemId)
    {
        if (!Auth::user()->can('update_menus')) {
            return resJsonUnauthorized();
        }
        $data = $request->validated();

        $itemData = array_remove($data, 'item');

        DB::beginTransaction();
        try {
            MenuItem::where('id', $itemId)->update($itemData);

            // Loop with every language
            foreach ($data as $language => $values) {
                $values['url'] = empty($values['url']) ? Str::slug($values['title'] ?? '') : $values['url'] ?? '';
                DB::table('menu_item_translations')
                    ->where('item_id', $itemId)
                    ->where('language', $language)
                    ->update($values);
            }

            // Remove the menu items cache
            cache()->forget('header-menu');
            cache()->forget('quick-links');

            DB::commit();
            return resJson(true);
        } catch (\Exception) {
            DB::rollBack();
            return resJson(false);
        }
    }

    public function destroy(int $groupId, int $itemId)
    {
        if (!Auth::user()->can('delete_menus')) {
            return resJsonUnauthorized();
        }
        // Remove the menu items cache
        cache()->forget('header-menu');
        cache()->forget('quick-links');

        return resJson(MenuItem::destroy($itemId));
    }

    public function restore(int $groupId, int $itemId)
    {
        if (!Auth::user()->can('delete_menus')) {
            return resJsonUnauthorized();
        }
        // Remove the menu items cache
        cache()->forget('header-menu');
        cache()->forget('quick-links');

        return resJson(MenuItem::withTrashed()->find($itemId)->restore());
    }

    public function saveSequence()
    {
        if (!Auth::user()->can('update_menus')) {
            return resJsonUnauthorized();
        }
        $data = request()->all();

        DB::beginTransaction();
        try {
            foreach ($data as $key => $datum) {
                // I write this with query builder for better performance, there could be a lot of data to be ordered.
                DB::update('UPDATE menu_items SET updated_at = ?, parent_id = ?, sequence = ? WHERE id = ?;', [
                    now(),
                    $datum['parent_id'],
                    $key,
                    $datum['id']
                ]);
            }
            // Remove the menu items cache
            cache()->forget('header-menu');
            cache()->forget('quick-links');

            DB::commit();
            return resJson(true);
        } catch (\Exception) {
            DB::rollBack();
            return resJson(false);
        }
    }

    /**
     * Return items as tree
     *
     * @param $items
     * @return array
     */
    private function treeItems($items): array
    {
        return buildTree($items, [
            'id' => 'item_id',
            'parentId' => 'parent_id'
        ]);
    }

    /**
     * Actions for menu items
     *
     * @return string[][]
     */
    private function actions(): array
    {
        return [
            '<button class="btn btn-info text-white btn-sm" onclick="__find({value})"><i class="material-icons-outlined md-18">edit</i></button>',
            '<button class="btn btn-danger text-white btn-sm" onclick="__delete({value})"><i class="material-icons-outlined md-18">delete</i></button>'
        ];
    }
}
