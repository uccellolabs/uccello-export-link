<?php

namespace Uccello\UrlExport\Http\Controllers;

use Illuminate\Http\Request;
use Uccello\Core\Http\Controllers\Core\ListController;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;
use Uccello\Core\Support\Traits\IsExportable;
use Uccello\UrlExport\Models\ExportUrl;

class ExportController extends ListController
{
    use IsExportable;

    /**
     * Default export format.
     */
    const DEFAULT_EXPORT_FORMAT = 'csv';

    /**
     * Record that defines the export option.
     *
     * @var object|null
     */
    protected $exportByUrlRecord;

    public function process(?Domain $domain, Module $module, Request $request)
    {
        // Pre-process
        $this->preProcess($domain, $module, $request);

        $this->exportByUrlRecord = $this->getExportUrlRecordByUuidOrFail($request->uuid);

        $this->initializeExportManager();

        $this->setExportOptions();

        //TODO: Vérifier que l'utilisateur a la capability url_export si nécessaire
        // Vérifier cela pour l'utilisateur qui a créé le lien, car aucun utilisateur peut être authentifié
        // // Checks if user has "url_export" capability if necessary
        // if (config('url-export.needs_url_export_capability') === true) {
        //     $this->middleware('uccello.permissions:url_export');
        // }

        //TODO: Pour les modules privés : s'authentifier en tant que l'utilisateur qui a créé le lien
        // télécharger et se déconnecter

        // File extension
        $fileExtension = $this->getFileExtension();

        // Download file
        return $this->downloadExportedFile($fileExtension);
    }

    protected function getExportUrlRecordByUuidOrFail($uuid)
    {
        $record = ExportUrl::where('uuid', $uuid)
            ->where('domain_id', $this->domain->id)
            ->where('module_id', $this->module->id)
            ->first();

        if (!$record) {
            abort(404);
        }

        return $record;
    }

    /**
     * Returns file extension defined in the URL param if defined, else returns default one.
     *
     * @return string
     */
    protected function getFileExtension()
    {
        return $this->exportByUrlRecord->extension ?? static::DEFAULT_EXPORT_FORMAT;
    }

    /**
     * Set export options according to request params
     *
     * @return void
     */
    protected function setExportOptions()
    {
        // With ID
        $this->setWithIdOption();

        // With timestamps
        $this->setWithTimestampsOption();

        // With descendants
        $this->setWithDescendantsOption();

        // With hidden columns
        $this->setWithColumnsOption();

        // With conditions
        $this->setWithConditionsOption();

        // With order
        $this->setWithOrderOption();
    }

    /**
     * Add withId option if it was asked.
     *
     * @return void
     */
    protected function setWithIdOption()
    {
        if ($this->exportByUrlRecord->withId) {
            $this->withId();
        }
    }

    /**
     * Add withTimestamp option if it was asked.
     *
     * @return void
     */
    protected function setWithTimestampsOption()
    {
        if ($this->exportByUrlRecord->withTimestamps) {
            $this->withTimestamps();
        }
    }

    /**
     * Add withDescendants option if it was asked.
     *
     * @return void
     */
    protected function setWithDescendantsOption()
    {
        if ($this->exportByUrlRecord->withDescendants) {
            $this->withDescendants();
        }
    }

    /**
     * Add withColumns option if it was asked.
     *
     * @return void
     */
    protected function setWithColumnsOption()
    {
        if ($this->exportByUrlRecord->columns) {
            $columns = $this->exportByUrlRecord->columns;
            $this->withColumns($columns);
        }
    }

    /**
     * Add withConditions option if it was asked.
     *
     * @return void
     */
    protected function setWithConditionsOption()
    {
        if ($this->exportByUrlRecord->conditions) {
            $conditions = $this->exportByUrlRecord->conditions;
            $this->withConditions($conditions);
            // dd($conditions);
        }
    }

    /**
     * Add withOrder option if it was asked.
     *
     * @return void
     */
    protected function setWithOrderOption()
    {
        if ($this->exportByUrlRecord->order) {
            $order = $this->exportByUrlRecord->order;
            $this->withOrder($order);
        }
    }
}
