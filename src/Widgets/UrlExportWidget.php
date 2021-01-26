<?php

namespace Uccello\UrlExport\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Uccello\UrlExport\Models\ExportUrl;

class UrlExportWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $userLinks = $this->getLinksCreatedByUser();

        return view('url-export::widgets.url_export_widget', [
            'config' => $this->config,
            'domain' => $this->config['domain'],
            'module' => $this->config['module'],
            'user' => $this->config['user'],
            'userLinks' => $userLinks,
        ]);
    }

    protected function getLinksCreatedByUser()
    {
        return ExportUrl::where('domain_id', $this->config['domain']->id)
            ->where('module_id', $this->config['module']->id)
            ->where('user_id', $this->config['user']->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
