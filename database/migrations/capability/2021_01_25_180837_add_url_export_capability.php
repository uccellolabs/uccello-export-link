<?php

use Illuminate\Database\Migrations\Migration;
use Uccello\Core\Models\Capability;

class AddUrlExportCapability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Capability::create([
            'name' => 'url_export',
            'data' => [
                'package' => 'uccello/url-export',
                'for_crud' => true,
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capability::where('name', 'url_export')->delete();
    }
}
