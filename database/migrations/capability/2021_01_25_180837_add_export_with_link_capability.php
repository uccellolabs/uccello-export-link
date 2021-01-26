<?php

use Illuminate\Database\Migrations\Migration;
use Uccello\Core\Models\Capability;

class AddExportWithLinkCapability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Capability::create([
            'name' => 'export_with_link',
            'data' => [
                'package' => 'uccello/export-link',
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
        Capability::where('name', 'export_with_link')->delete();
    }
}
