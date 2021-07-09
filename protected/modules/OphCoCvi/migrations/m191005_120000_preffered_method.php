<?php

class m191005_120000_preffered_method extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt', 'version', 'INT UNSIGNED NOT NULL DEFAULT 0');
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'version', 'INT UNSIGNED NOT NULL DEFAULT 0');
        
        $this->insert('ophcocvi_clericinfo_preferred_info_fmt', [
            'name' => 'telephone',
            'active' => 1,
            'display_order' => 1,
            'deleted' => 0,
            'version' => 1,
        ]);
        
        $this->insert('ophcocvi_clericinfo_preferred_info_fmt', [
            'name' => 'email',
            'active' => 1,
            'display_order' => 2,
            'deleted' => 0,
            'version' => 1,
        ]);
        
        $this->insert('ophcocvi_clericinfo_preferred_info_fmt', [
            'name' => 'letter',
            'active' => 1,
            'display_order' => 3,
            'deleted' => 0,
            'version' => 1,
        ]);
    }

    public function down()
    {        
        $this->execute("DELETE FROM ophcocvi_clericinfo_preferred_info_fmt WHERE version = 1");
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'version');
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt', 'version');
    }
}