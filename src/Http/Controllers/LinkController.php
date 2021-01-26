<?php

namespace Uccello\ExportLink\Http\Controllers;

use Illuminate\Http\Request;
use Uccello\Core\Http\Controllers\Core\IndexController;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;
use Uccello\ExportLink\Models\ExportLink;

class LinkController extends IndexController
{
    /**
     * Checks user permissions
     */
    protected function checkPermissions()
    {
        // Checks if user can retrieve current module
        $this->middleware('uccello.permissions:retrieve');

        // Checks if user has "export_with_link" capability if necessary
        if (config('export-link.needs_export_with_link_capability') === true) {
            $this->middleware('uccello.permissions:export_with_link');
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
    public function generateExportLink(?Domain $domain, Module $module, Request $request)
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

        $exportLinkRecord = ExportLink::create([
            'domain_id' => $domain->id,
            'module_id' => $module->id,
            'user_id' => auth()->id(),
            'data' => $data
        ]);

        $exportLinkRecord->html_content = view()->make('export-link::partials.link', [
            'link' => $exportLinkRecord
        ])->render();

        return $exportLinkRecord;
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
    public function deleteExportLink(?Domain $domain, Module $module, Request $request)
    {
        ExportLink::where('domain_id', $domain->id)
            ->where('module_id', $module->id)
            ->where('user_id', auth()->id())
            ->where('uuid', $request->uuid)
            ->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
