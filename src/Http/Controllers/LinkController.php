<?php

namespace Uccello\UrlExport\Http\Controllers;

use Illuminate\Http\Request;
use Uccello\Core\Http\Controllers\Core\IndexController;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;
use Uccello\UrlExport\Models\ExportUrl;

class LinkController extends IndexController
{
    /**
     * Checks user permissions
     */
    protected function checkPermissions()
    {
        // Checks if user can retrieve current module
        $this->middleware('uccello.permissions:retrieve');

        // Checks if user has "url_export" capability if necessary
        if (config('url-export.needs_url_export_capability') === true) {
            $this->middleware('uccello.permissions:url_export');
        }
    }

    /**
     * Generates an export URL.
     *
     * @param \Uccello\Core\Models\Domain|null $domain
     * @param \Uccello\Core\Models\Module $module
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function generateExportUrl(?Domain $domain, Module $module, Request $request)
    {
        $data = [
            'extension' => $request->extension,
            'with_id' => $request->with_id === '1',
            'with_timestamps' => $request->with_timestamps === '1',
            'with_descendants' => $request->with_descendants === '1'
        ];

        if ($request->with_hidden_columns !== '1') {
            $data['columns'] = $request->columns;
        }

        if ($request->with_conditions === '1') {
            $data['conditions'] = $request->conditions;
        }

        if ($request->with_order === '1') {
            $data['order'] = $request->order;
        }

        $exportUrlRecord = ExportUrl::create([
            'domain_id' => $domain->id,
            'module_id' => $module->id,
            'user_id' => auth()->id(),
            'data' => $data
        ]);

        $exportUrlRecord->html_content = view()->make('url-export::partials.link', [
            'link' => $exportUrlRecord
        ])->render();

        return $exportUrlRecord;
    }

    /**
     * Delete an export URL. It uses uuid to retrieve it.
     *
     * @param \Uccello\Core\Models\Domain|null $domain
     * @param \Uccello\Core\Models\Module $module
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteExportUrl(?Domain $domain, Module $module, Request $request)
    {
        ExportUrl::where('domain_id', $domain->id)
            ->where('module_id', $module->id)
            ->where('user_id', auth()->id())
            ->where('uuid', $request->uuid)
            ->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
