<?php

namespace Uccello\ExportLink\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;
use Uccello\Core\Support\Traits\IsExportable;
use Uccello\ExportLink\Models\ExportLink;

class ExportController extends Controller
{
    use IsExportable;

    /**
     * Default export format.
     */
    const DEFAULT_EXPORT_FORMAT = 'csv';

    protected $domain;
    protected $module;
    protected $request;
    protected $wasAutomaticalyLoggedIn = false;

    /**
     * Check user permissions
     */
    protected function checkPermissions()
    {
        $this->middleware('guest');
    }

    /**
     * Record that defines the export option.
     *
     * @var object|null
     */
    protected $exportByUrlRecord;

    /**
     * Exports a file thanks to an URL
     *
     * @param \Uccello\Core\Models\Domain|null $domain
     * @param \Uccello\Core\Models\Module $module
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Exception
     */
    public function export(?Domain $domain, Module $module, Request $request)
    {
        $this->domain = $domain;
        $this->module = $module;
        $this->request = $request;

        try {
            // Prepare export
            $this->prepareExport();

            // File extension
            $fileExtension = $this->getFileExtension();

            // Download file
            $response = $this->downloadExportedFile($fileExtension);

            // Auto logout user if necessary
            $this->autoLogoutIfUserWasAutomaticalyLoggedIn();
        } catch (\Exception $exception) {
            // Auto logout user if necessary
            $this->autoLogoutIfUserWasAutomaticalyLoggedIn();

            throw $exception;
        }

        return $response;
    }

    /**
     * Prepare export
     *
     * @return void
     */
    protected function prepareExport()
    {
        $this->retrieveExportLinkRecordFromRequest() // Retrieve Export Url record
            ->retrieveUserWhoCreatedExportLink() // Retrieve user
            ->autoLoginIfModuleIsPrivateAndUserIsNotAuthenticated() // Auto login
            ->initializeExportManager() // Initialize Export Manager
            ->setExportOptions(); // Set export options
    }

    /**
     * Retrieves an export URL with its uuid present in request params.
     *
     * @return object|\Illuminate\Http\Exceptions
     */
    protected function retrieveExportLinkRecordFromRequest()
    {
        $record = ExportLink::where('uuid', $this->request->uuid)
            ->where('domain_id', $this->domain->id)
            ->where('module_id', $this->module->id)
            ->first();

        if (!$record) {
            abort(404);
        }

        $this->exportByUrlRecord = $record;

        return $this;
    }

    /**
     * Retrieve the user who created the export url.
     *
     * @return object
     */
    protected function retrieveUserWhoCreatedExportLink()
    {
        $this->user = User::find($this->exportByUrlRecord->user_id);

        return $this;
    }

    /**
     * Auto login user if the current module is private and if the user is not already authenticated.
     *
     * @return object
     */
    protected function autoLoginIfModuleIsPrivateAndUserIsNotAuthenticated()
    {
        if ($this->isModulePrivate() && !$this->isUserAuthenticated()) {
            $this->autoLogin();
        }

        return $this;
    }

    /**
     * Checks if the current module is private.
     *
     * @return boolean
     */
    protected function isModulePrivate()
    {
        return $this->module->isPrivate();
    }

    /**
     * Checks if a user is authenticated and if it is the same than the one who created the export url.
     *
     * @return boolean
     */
    protected function isUserAuthenticated()
    {
        return Auth::check() && Auth::id() === $this->user->id;
    }

    /**
     * Auto login user and memorize that it append.
     *
     * @return object
     */
    protected function autoLogin()
    {
        Auth::login($this->user, false);

        $this->wasAutomaticalyLoggedIn = true;

        return $this;
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
     * @return object
     */
    protected function setExportOptions()
    {
        $this->setWithIdOption() // With ID
            ->setWithTimestampsOption() // With timestamps
            ->setWithDescendantsOption() // With descendants
            ->setWithColumnsOption() // With hidden columns
            ->setWithConditionsOption() // With conditions
            ->setWithOrderOption(); // With order

        return $this;
    }

    /**
     * Add withId option if it was asked.
     *
     * @return object
     */
    protected function setWithIdOption()
    {
        if ($this->exportByUrlRecord->withId) {
            $this->withId();
        }

        return $this;
    }

    /**
     * Add withTimestamp option if it was asked.
     *
     * @return object
     */
    protected function setWithTimestampsOption()
    {
        if ($this->exportByUrlRecord->withTimestamps) {
            $this->withTimestamps();
        }

        return $this;
    }

    /**
     * Add withDescendants option if it was asked.
     *
     * @return object
     */
    protected function setWithDescendantsOption()
    {
        if ($this->exportByUrlRecord->withDescendants) {
            $this->withDescendants();
        }

        return $this;
    }

    /**
     * Add withColumns option if it was asked.
     *
     * @return object
     */
    protected function setWithColumnsOption()
    {
        if ($this->exportByUrlRecord->columns) {
            $columns = $this->exportByUrlRecord->columns;
            $this->withColumns($columns);
        }

        return $this;
    }

    /**
     * Add withConditions option if it was asked.
     *
     * @return object
     */
    protected function setWithConditionsOption()
    {
        if ($this->exportByUrlRecord->conditions) {
            $conditions = $this->exportByUrlRecord->conditions;
            $this->withConditions($conditions);
        }

        return $this;
    }

    /**
     * Add withOrder option if it was asked.
     *
     * @return object
     */
    protected function setWithOrderOption()
    {
        if ($this->exportByUrlRecord->order) {
            $order = $this->exportByUrlRecord->order;
            $this->withOrder($order);
        }

        return $this;
    }

    /**
     * Auto logout user if it was automaticaly logged in by this class.
     * It is important to check this, because otherwise, if the user had logged in the classic way,
     * he would be logged out after downloading the file.
     *
     * @return object
     */
    protected function autoLogoutIfUserWasAutomaticalyLoggedIn()
    {
        if ($this->wasUserAutomaticalyLoggedIn() && $this->isUserAuthenticated()) {
            Auth::logout();
        }

        return $this;
    }

    /**
     * Checks if the user was automaticaly logged in by this class.
     *
     * @return boolean
     */
    protected function wasUserAutomaticalyLoggedIn()
    {
        return $this->wasAutomaticalyLoggedIn === true;
    }
}
